<?php

namespace Tests\Feature;

use App\Models\Locker;
use App\Models\LockerRental;
use App\Models\Member;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LockerBookingTest extends TestCase
{
    use RefreshDatabase;

    private int $lockerCounter = 0;

    private function createTenant(): Tenant
    {
        $n = uniqid();

        return Tenant::create([
            'name'        => 'Gym Loker Test',
            'subdomain'   => 'loker-test-' . $n . '.workout.id',
            'slug'        => 'loker-test-' . $n,
            'owner_name'  => 'Test Owner',
            'owner_email' => 'owner-loker@test.com',
            'plan_name'   => 'Paket Pro',
            'status'      => 'active',
        ]);
    }

    private function createMemberUser(Tenant $tenant, string $email): User
    {
        $user = User::factory()->create([
            'email'     => $email,
            'password'  => 'password123',
            'role'      => 'member',
            'tenant_id' => $tenant->id,
        ]);

        Member::create([
            'tenant_id'       => $tenant->id,
            'user_id'         => $user->id,
            'name'            => 'Member ' . $email,
            'email'           => $email,
            'phone'           => '081200000000',
            'access_code'     => '9999' . str_pad((string) ++$this->lockerCounter, 2, '0', STR_PAD_LEFT),
            'membership_tier' => 'basic',
            'status'          => 'active',
            'expired_at'      => now()->addDays(30),
        ]);

        return $user;
    }

    private function createLocker(Tenant $tenant, string $number, string $status = 'tersedia'): Locker
    {
        return Locker::create([
            'tenant_id'     => $tenant->id,
            'locker_number' => $number,
            'status'        => $status,
        ]);
    }

    /**
     * Member bisa menyewa loker yang tersedia; status berubah jadi terpakai.
     */
    public function test_member_can_rent_available_locker(): void
    {
        $tenant = $this->createTenant();
        $locker = $this->createLocker($tenant, '01');
        $user = $this->createMemberUser($tenant, 'member1@test.com');

        $this->actingAs($user)
            ->post(route('member.lockers.rent'), [
                'locker_id'   => $locker->id,
                'rental_type' => 'daily',
            ])
            ->assertRedirect(route('member.lockers'))
            ->assertSessionHas('success');

        $this->assertSame('terpakai', $locker->fresh()->status);

        $rental = LockerRental::where('member_id', Member::where('user_id', $user->id)->first()->id)
            ->where('locker_id', $locker->id)
            ->first();

        $this->assertNotNull($rental);
        $this->assertSame('active', $rental->status);
        $this->assertMatchesRegularExpression('/^\d{6}$/', $rental->pin_code);
    }

    /**
     * Loker yang sudah terpakai tidak bisa disewa member lain (anti double-booking).
     */
    public function test_member_cannot_rent_locker_already_taken(): void
    {
        $tenant = $this->createTenant();
        $locker = $this->createLocker($tenant, '02');
        $userA = $this->createMemberUser($tenant, 'membera@test.com');
        $userB = $this->createMemberUser($tenant, 'memberb@test.com');

        // Member A sewa dulu
        $this->actingAs($userA)
            ->post(route('member.lockers.rent'), [
                'locker_id'   => $locker->id,
                'rental_type' => 'daily',
            ])
            ->assertRedirect(route('member.lockers'));

        // Member B coba sewa loker yang sama -> ditolak
        $this->actingAs($userB)
            ->post(route('member.lockers.rent'), [
                'locker_id'   => $locker->id,
                'rental_type' => 'daily',
            ])
            ->assertRedirect()
            ->assertSessionHas('error');

        // Status tetap terpakai & hanya ada 1 sewa aktif
        $this->assertSame('terpakai', $locker->fresh()->status);
        $this->assertSame(
            1,
            LockerRental::where('locker_id', $locker->id)->where('status', 'active')->count()
        );
    }

    /**
     * Satu member tidak boleh punya 2 sewa aktif sekaligus.
     */
    public function test_member_cannot_rent_second_locker_while_active(): void
    {
        $tenant = $this->createTenant();
        $locker1 = $this->createLocker($tenant, '03');
        $locker2 = $this->createLocker($tenant, '04');
        $user = $this->createMemberUser($tenant, 'memberc@test.com');

        // Sewa loker pertama
        $this->actingAs($user)
            ->post(route('member.lockers.rent'), [
                'locker_id'   => $locker1->id,
                'rental_type' => 'daily',
            ])
            ->assertRedirect(route('member.lockers'));

        // Coba sewa loker kedua -> ditolak karena masih punya sewa aktif
        $this->actingAs($user)
            ->post(route('member.lockers.rent'), [
                'locker_id'   => $locker2->id,
                'rental_type' => 'daily',
            ])
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertSame('tersedia', $locker2->fresh()->status);
        $this->assertSame(
            1,
            LockerRental::where('member_id', Member::where('user_id', $user->id)->first()->id)
                ->where('status', 'active')
                ->count()
        );
    }

    /**
     * Setelah dikembalikan, loker kembali tersedia dan bisa disewa lagi.
     */
    public function test_returned_locker_becomes_available_again(): void
    {
        $tenant = $this->createTenant();
        $locker = $this->createLocker($tenant, '05');
        $user = $this->createMemberUser($tenant, 'memberd@test.com');
        $member = Member::where('user_id', $user->id)->first();

        $this->actingAs($user)
            ->post(route('member.lockers.rent'), [
                'locker_id'   => $locker->id,
                'rental_type' => 'daily',
            ]);

        $rental = LockerRental::where('member_id', $member->id)->where('status', 'active')->first();

        $this->actingAs($user)
            ->post(route('member.lockers.return', $rental->id))
            ->assertRedirect(route('member.lockers'));

        $this->assertSame('returned', $rental->fresh()->status);
        $this->assertSame('tersedia', $locker->fresh()->status);

        // Bisa disewa lagi oleh member lain
        $userB = $this->createMemberUser($tenant, 'membere@test.com');
        $this->actingAs($userB)
            ->post(route('member.lockers.rent'), [
                'locker_id'   => $locker->id,
                'rental_type' => 'daily',
            ])
            ->assertRedirect(route('member.lockers'))
            ->assertSessionHas('success');
    }
}
