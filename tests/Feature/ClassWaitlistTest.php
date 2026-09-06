<?php

namespace Tests\Feature;

use App\Models\ClassRsvp;
use App\Models\GymClass;
use App\Models\Member;
use App\Models\Tenant;
use App\Models\Trainer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClassWaitlistTest extends TestCase
{
    use RefreshDatabase;

    private int $memberCounter = 0;

    private Tenant $tenant;
    private GymClass $gymClass;
    private string $classDate;

    protected function setUp(): void
    {
        parent::setUp();

        $n = uniqid();
        $this->tenant = Tenant::create([
            'name'        => 'Gym Kelas Test',
            'subdomain'   => 'kelas-test-' . $n . '.workout.id',
            'slug'        => 'kelas-test-' . $n,
            'owner_name'  => 'Test Owner',
            'owner_email' => 'owner-kelas@test.com',
            'plan_name'   => 'Paket Pro',
            'status'      => 'active',
        ]);

        $trainer = Trainer::create([
            'tenant_id' => $this->tenant->id,
            'name'      => 'Coach Test',
            'email'     => 'coach@test.com',
            'status'    => 'active',
        ]);

        $this->gymClass = GymClass::create([
            'tenant_id'        => $this->tenant->id,
            'trainer_id'       => $trainer->id,
            'name'             => 'HIIT Test Class',
            'day'              => 'Senin',
            'start_time'       => '08:00',
            'end_time'         => '09:00',
            'room'             => 'Studio Test',
            'duration_minutes' => 60,
            'max_capacity'     => 2,
        ]);

        $this->classDate = now()->addDay()->format('Y-m-d');
    }

    private function createMemberUser(string $email): User
    {
        $user = User::factory()->create([
            'email'     => $email,
            'password'  => 'password123',
            'role'      => 'member',
            'tenant_id' => $this->tenant->id,
        ]);

        Member::create([
            'tenant_id'       => $this->tenant->id,
            'user_id'         => $user->id,
            'name'            => 'Member ' . $email,
            'email'           => $email,
            'phone'           => '081300000000',
            'access_code'     => '7777' . str_pad((string) ++$this->memberCounter, 2, '0', STR_PAD_LEFT),
            'membership_tier' => 'basic',
            'status'          => 'active',
            'expired_at'      => now()->addDays(30),
        ]);

        return $user;
    }

    private function rsvp(User $user): void
    {
        $this->actingAs($user)
            ->post(route('member.classes.rsvp'), [
                'gym_class_id' => $this->gymClass->id,
                'class_date'   => $this->classDate,
            ]);
    }

    /**
     * Kelas penuh -> RSVP berikutnya masuk waitlist dengan posisi urut.
     */
    public function test_rsvp_goes_to_waitlist_when_class_is_full(): void
    {
        $m1 = $this->createMemberUser('m1@test.com');
        $m2 = $this->createMemberUser('m2@test.com');
        $m3 = $this->createMemberUser('m3@test.com');
        $m4 = $this->createMemberUser('m4@test.com');

        $this->rsvp($m1);
        $this->rsvp($m2);
        $this->rsvp($m3);
        $this->rsvp($m4);

        $this->assertSame(2, ClassRsvp::where('status', 'confirmed')->count());
        $this->assertSame(2, ClassRsvp::where('status', 'waitlist')->count());

        $pos1 = ClassRsvp::where('member_id', Member::where('user_id', $m3->id)->first()->id)->first();
        $pos2 = ClassRsvp::where('member_id', Member::where('user_id', $m4->id)->first()->id)->first();

        $this->assertSame('waitlist', $pos1->status);
        $this->assertSame(1, $pos1->queue_position);
        $this->assertSame(2, $pos2->queue_position);
    }

    /**
     * Saat member CONFIRMED membatalkan, urutan pertama waitlist otomatis naik.
     */
    public function test_cancelling_confirmed_auto_promotes_first_waitlist(): void
    {
        $m1 = $this->createMemberUser('m1@test.com');
        $m2 = $this->createMemberUser('m2@test.com');
        $m3 = $this->createMemberUser('m3@test.com');

        $this->rsvp($m1);
        $this->rsvp($m2);
        $this->rsvp($m3); // waitlist posisi 1

        $m2Member = Member::where('user_id', $m2->id)->first();
        $m2Rsvp = ClassRsvp::where('member_id', $m2Member->id)->where('status', 'confirmed')->first();

        $this->actingAs($m2)
            ->post(route('member.classes.cancel', $m2Rsvp->id))
            ->assertRedirect(route('member.classes'));

        $m3Rsvp = ClassRsvp::where('member_id', Member::where('user_id', $m3->id)->first()->id)->first();

        $this->assertSame('confirmed', $m3Rsvp->fresh()->status);
        $this->assertNull($m3Rsvp->fresh()->queue_position);
        $this->assertSame(2, ClassRsvp::where('status', 'confirmed')->count());
    }

    /**
     * Member yang masih di WAITLIST membatalkan -> TIDAK boleh mempromosikan
     * siapa pun, karena slot confirmed tidak berkurang (kelas tetap penuh).
     */
    public function test_cancelling_waitlist_does_not_over_promote(): void
    {
        $m1 = $this->createMemberUser('m1@test.com');
        $m2 = $this->createMemberUser('m2@test.com');
        $m3 = $this->createMemberUser('m3@test.com');
        $m4 = $this->createMemberUser('m4@test.com');
        $m5 = $this->createMemberUser('m5@test.com');

        $this->rsvp($m1);
        $this->rsvp($m2); // confirmed x2 (penuh)
        $this->rsvp($m3); // waitlist 1
        $this->rsvp($m4); // waitlist 2
        $this->rsvp($m5); // waitlist 3

        // Member waitlist (m4) membatalkan
        $m4Member = Member::where('user_id', $m4->id)->first();
        $m4Rsvp = ClassRsvp::where('member_id', $m4Member->id)->where('status', 'waitlist')->first();

        $this->actingAs($m4)
            ->post(route('member.classes.cancel', $m4Rsvp->id))
            ->assertRedirect(route('member.classes'));

        // Tidak ada promosi: confirmed tetap 2 (sesuai kapasitas), m5 tetap waitlist
        $this->assertSame(2, ClassRsvp::where('status', 'confirmed')->count());
        $this->assertSame(2, ClassRsvp::where('status', 'waitlist')->count());

        $m5Rsvp = ClassRsvp::where('member_id', Member::where('user_id', $m5->id)->first()->id)->first();
        $this->assertSame('waitlist', $m5Rsvp->fresh()->status);
    }

    /**
     * Member yang sudah RSVP (confirmed/waitlist) tidak bisa daftar dua kali.
     */
    public function test_duplicate_rsvp_is_rejected(): void
    {
        $m1 = $this->createMemberUser('m1@test.com');

        $this->rsvp($m1);

        $this->actingAs($m1)
            ->post(route('member.classes.rsvp'), [
                'gym_class_id' => $this->gymClass->id,
                'class_date'   => $this->classDate,
            ])
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertSame(1, ClassRsvp::where('member_id', Member::where('user_id', $m1->id)->first()->id)->count());
    }
}
