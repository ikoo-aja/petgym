<?php

namespace Tests\Audit;

use App\Models\Complaint;
use App\Models\LeaveRequest;
use App\Models\Locker;
use App\Models\Member;
use App\Models\ReceptionistShift;
use App\Models\StaffShift;
use App\Models\Tenant;
use App\Models\Trainer;
use App\Models\User;
use App\Models\Vendor;
use Database\Seeders\DatabaseSeeder;
use Tests\TestCase;

/**
 * AUDIT ISOLASI DATA ANTAR TENANT (Gym)
 *
 * Memastikan user tenant A tidak dapat membaca/mengubah data tenant B,
 * baik lewat ID langsung (IDOR) maupun lewat mass assignment.
 */
class CrossTenantTest extends TestCase
{
    private Tenant $tenantA;
    private Tenant $tenantB;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->tenantA = Tenant::where('slug', 'fitlife')->firstOrFail();

        $this->tenantB = Tenant::create([
            'name' => 'Rival Gym',
            'subdomain' => 'rival.workout.id',
            'slug' => 'rival',
            'owner_name' => 'Rival Owner',
            'owner_email' => 'owner@rival.test',
            'plan_name' => 'Paket Pro',
            'status' => 'active',
            'joined_at' => now(),
            'expires_at' => now()->addMonths(3),
            'features' => ['POS', 'Class'],
        ]);
    }

    private function user(string $email): User
    {
        return User::where('email', $email)->firstOrFail();
    }

    private function makeTenantBManager(): User
    {
        return User::create([
            'name' => 'Rival Manager',
            'email' => 'manager@rival.test',
            'password' => bcrypt('1234'),
            'role' => 'manager',
            'tenant_id' => $this->tenantB->id,
            'email_verified_at' => now(),
        ]);
    }

    private function makeTenantBMember(): Member
    {
        return Member::create([
            'tenant_id' => $this->tenantB->id,
            'name' => 'Member Rival',
            'email' => 'member@rival.test',
            'phone' => '081199998888',
            'gender' => 'Laki-laki',
            'access_code' => '777777',
            'membership_tier' => 'basic',
            'status' => 'active',
            'expired_at' => now()->addMonth(),
        ]);
    }

    public function test_manager_tidak_bisa_mengubah_member_tenant_lain(): void
    {
        $manager = $this->user('manager@fitlife.com');
        $foreignMember = $this->makeTenantBMember();

        $response = $this->actingAs($manager)->put(route('manager.members.update', $foreignMember->id), [
            'name' => 'Dibajak',
            'gender' => 'Laki-laki',
            'status' => 'active',
        ]);

        $this->assertSame(
            404,
            $response->getStatusCode(),
            "Manager tenant A bisa mengubah member tenant B (IDOR). Status: {$response->getStatusCode()}"
        );
    }

    public function test_manager_tidak_bisa_menghapus_member_tenant_lain(): void
    {
        $manager = $this->user('manager@fitlife.com');
        $foreignMember = $this->makeTenantBMember();

        $response = $this->actingAs($manager)->delete(route('manager.members.destroy', $foreignMember->id));

        $this->assertSame(
            404,
            $response->getStatusCode(),
            "Manager tenant A bisa menghapus member tenant B (IDOR). Status: {$response->getStatusCode()}"
        );
        $this->assertDatabaseHas('members', ['id' => $foreignMember->id]);
    }

    public function test_manager_tidak_bisa_melihat_riwayat_member_tenant_lain(): void
    {
        $manager = $this->user('manager@fitlife.com');
        $foreignMember = $this->makeTenantBMember();

        $response = $this->actingAs($manager)->get(route('manager.members.history', $foreignMember->id));

        $this->assertSame(404, $response->getStatusCode(), 'Riwayat member tenant lain dapat diakses (IDOR).');
    }

    public function test_supervisor_tidak_bisa_membuat_cuti_untuk_user_tenant_lain(): void
    {
        $supervisor = $this->user('supervisor@fitlife.com');
        $foreignUser = $this->makeTenantBManager();

        $this->actingAs($supervisor)->from('/')->post(route('supervisor.leave.store'), [
            'user_id' => $foreignUser->id,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'reason' => 'Cuti lintas tenant',
        ]);

        $this->assertSame(
            0,
            LeaveRequest::where('user_id', $foreignUser->id)->count(),
            'Supervisor tenant A berhasil membuat pengajuan cuti untuk user tenant B (kebocoran lintas tenant).'
        );
    }

    public function test_member_tanpa_tenant_tidak_ditempelkan_ke_tenant_lain(): void
    {
        // Simulasi member yang tenant-nya sudah dihapus / belum diset
        $orphan = User::create([
            'name' => 'Orphan Member',
            'email' => 'orphan.member@uji.test',
            'password' => bcrypt('1234'),
            'role' => 'member',
            'tenant_id' => null,
        ]);
        $orphan->markEmailAsVerified();

        $response = $this->actingAs($orphan)->get(route('member.dashboard'));

        $this->assertNotSame(
            500,
            $response->getStatusCode(),
            'Member tanpa tenant memicu error server (HTTP 500) karena fallback tenant_id ?? 1.'
        );

        $attached = Member::where('user_id', $orphan->id)->first();

        $this->assertNull(
            $attached,
            'Member tanpa tenant otomatis ditempelkan ke tenant ' . ($attached->tenant_id ?? '-') . ' (fallback tenant_id ?? 1).'
        );
    }

    public function test_endpoint_lintas_tenant_menolak_id_relasi_tenant_lain(): void
    {
        $foreignMember = $this->makeTenantBMember();
        $foreignTrainer = Trainer::create([
            'tenant_id' => $this->tenantB->id,
            'name' => 'Trainer Rival',
            'email' => 'trainer@rival.test',
            'status' => 'active',
        ]);
        $ownLocker = Locker::where('tenant_id', $this->tenantA->id)->firstOrFail();

        $receptionist = $this->user('resepsionis@fitlife.com');
        ReceptionistShift::create([
            'tenant_id' => $receptionist->tenant_id,
            'user_id' => $receptionist->id,
            'start_cash' => 100000,
            'opened_at' => now(),
            'status' => 'open',
        ]);
        $manager = $this->user('manager@fitlife.com');

        // [label, aktor, method, route, payload, field yang harus error]
        $cases = [
            'pos checkout' => [$receptionist, 'post', 'receptionist.pos.checkout', [
                'member_id' => $foreignMember->id,
                'payment_method' => 'cash',
                'type' => 'membership',
                'items' => [['item_name' => 'Uji', 'qty' => 1, 'price' => 1000]],
            ], 'member_id'],
            'lockers assign' => [$receptionist, 'post', 'receptionist.lockers.assign', [
                'locker_id' => $ownLocker->id,
                'member_id' => $foreignMember->id,
            ], 'member_id'],
            'complaints store' => [$receptionist, 'post', 'receptionist.complaints.store', [
                'member_id' => $foreignMember->id,
                'title' => 'Komplain Uji',
                'description' => 'Uji lintas tenant',
            ], 'member_id'],
            'pt checkin' => [$receptionist, 'post', 'receptionist.pt.checkin', [
                'member_id' => $foreignMember->id,
                'trainer_id' => $foreignTrainer->id,
                'session_date' => now()->toDateString(),
            ], 'member_id'],
            'classes store' => [$manager, 'post', 'manager.classes.store', [
                'name' => 'Kelas Uji',
                'day' => 'Senin',
                'start_time' => '08:00',
                'end_time' => '09:00',
                'max_capacity' => 10,
                'trainer_id' => $foreignTrainer->id,
            ], 'trainer_id'],
        ];

        $failures = [];

        foreach ($cases as $label => [$user, $method, $route, $payload, $errorKey]) {
            $this->flushSession();
            $this->actingAs($user)->from('/')->{$method}(route($route), $payload);
            $errors = session()->get('errors');

            if (!$errors || !$errors->has($errorKey)) {
                $failures[] = "{$label}: relasi lintas tenant DITERIMA (tidak ada error pada '{$errorKey}')";
            }
        }

        $this->assertSame([], $failures, "Endpoint masih menerima ID lintas tenant:\n" . implode("\n", $failures));
    }

    public function test_mass_assignment_tidak_bisa_memindahkan_vendor_ke_tenant_lain(): void
    {
        $manager = $this->user('manager@fitlife.com');
        $vendor = Vendor::where('tenant_id', $this->tenantA->id)->firstOrFail();

        $this->actingAs($manager)->from('/')->put(route('manager.vendors.update', $vendor->id), [
            'name' => 'Vendor Uji',
            'category' => 'Teknisi',
            'tenant_id' => $this->tenantB->id,
        ]);

        $vendor->refresh();
        $this->assertSame(
            $this->tenantA->id,
            (int) $vendor->tenant_id,
            'Manager tenant A bisa memindahkan data vendor ke tenant B lewat tenant_id (mass assignment).'
        );
    }

    public function test_mass_assignment_tidak_bisa_memindahkan_komplain_ke_tenant_lain(): void
    {
        $supervisor = $this->user('supervisor@fitlife.com');
        $complaint = Complaint::where('tenant_id', $this->tenantA->id)->firstOrFail();

        $this->actingAs($supervisor)->from('/')->put(route('supervisor.complaints.update', $complaint->id), [
            'status' => 'in_progress',
            'tenant_id' => $this->tenantB->id,
        ]);

        $complaint->refresh();
        $this->assertSame(
            $this->tenantA->id,
            (int) $complaint->tenant_id,
            'Supervisor tenant A bisa memindahkan komplain ke tenant B lewat tenant_id (mass assignment).'
        );
    }

    public function test_mass_assignment_tidak_bisa_memindahkan_shift_ke_tenant_lain(): void
    {
        $supervisor = $this->user('supervisor@fitlife.com');
        $shift = StaffShift::where('tenant_id', $this->tenantA->id)->firstOrFail();
        $receptionist = $this->user('resepsionis@fitlife.com');

        $this->actingAs($supervisor)->from('/')->put(route('supervisor.shifts.update', $shift->id), [
            'user_id' => $receptionist->id,
            'shift_date' => now()->toDateString(),
            'shift_name' => 'Pagi',
            'start_time' => '08:00',
            'end_time' => '16:00',
            'tenant_id' => $this->tenantB->id,
        ]);

        $shift->refresh();
        $this->assertSame(
            $this->tenantA->id,
            (int) $shift->tenant_id,
            'Supervisor tenant A bisa memindahkan jadwal shift ke tenant B lewat tenant_id (mass assignment).'
        );
    }
}
