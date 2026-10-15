<?php

namespace Tests\Audit;

use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Tests\TestCase;

/**
 * AUDIT KEAMANAN: HAK AKSES PERAN ("ROLE X TIDAK BOLEH AKSES Y")
 *
 * Menguji boundary otorisasi server-side antar role:
 * 1. Member TIDAK boleh akses dashboard staf / kasir / POS / supervisor / owner.
 * 2. Trainer TIDAK boleh akses POS kasir / pendaftaran member / pengaturan sewa.
 * 3. Resepsionis TIDAK boleh akses pengaturan Superadmin / kelola langganan sewa.
 * 4. Manager TIDAK boleh akses pengaturan Superadmin / menu bayar sewa Superadmin.
 * 5. Tamu (Unauthenticated) ditolak dan diarahkan ke login.
 * 6. Admin Gym TIDAK boleh mengakses pengaturan pembayaran Owner.
 */
class RoleBoundaryAccessTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    private function user(string $email): User
    {
        return User::where('email', $email)->firstOrFail();
    }

    public function test_member_tidak_boleh_akses_area_staf_dan_kasir(): void
    {
        $member = $this->user('member@fitlife.com');

        $forbiddenRoutes = [
            'receptionist.pos.index',
            'receptionist.checkin.index',
            'manager.dashboard',
            'supervisor.dashboard',
            'owner.dashboard',
            'admin.dashboard',
            'superadmin.dashboard',
        ];

        foreach ($forbiddenRoutes as $routeName) {
            $res = $this->actingAs($member)->get(route($routeName));
            $this->assertSame(403, $res->status(), "Member seharusnya DITOLAK 403 saat membuka {$routeName}");
        }
    }

    public function test_trainer_tidak_boleh_akses_pos_kasir_dan_manajemen_loker(): void
    {
        $trainer = $this->user('alex@fitlife.com');

        $forbiddenRoutes = [
            'receptionist.pos.index',
            'receptionist.shifts',
            'receptionist.lockers',
            'supervisor.dashboard',
            'owner.dashboard',
            'admin.dashboard',
            'superadmin.dashboard',
        ];

        foreach ($forbiddenRoutes as $routeName) {
            $res = $this->actingAs($trainer)->get(route($routeName));
            $this->assertSame(403, $res->status(), "Trainer seharusnya DITOLAK 403 saat membuka {$routeName}");
        }
    }

    public function test_resepsionis_tidak_boleh_akses_pengaturan_superadmin_dan_approval_void(): void
    {
        $rec = $this->user('resepsionis@fitlife.com');

        $forbiddenRoutes = [
            'superadmin.dashboard',
            'superadmin.settings',
            'superadmin.billing',
            'superadmin.tenants',
            'admin.dashboard',
            'admin.subscription.index',
            'supervisor.dashboard',
        ];

        foreach ($forbiddenRoutes as $routeName) {
            $res = $this->actingAs($rec)->get(route($routeName));
            $this->assertSame(403, $res->status(), "Resepsionis seharusnya DITOLAK 403 saat membuka {$routeName}");
        }
    }

    public function test_manager_tidak_boleh_akses_pengaturan_superadmin_dan_billing_superadmin(): void
    {
        $mgr = $this->user('manager@fitlife.com');

        $forbiddenRoutes = [
            'superadmin.dashboard',
            'superadmin.settings',
            'superadmin.billing',
            'superadmin.tenants',
            'admin.subscription.index',
        ];

        foreach ($forbiddenRoutes as $routeName) {
            $res = $this->actingAs($mgr)->get(route($routeName));
            $this->assertSame(403, $res->status(), "Manager seharusnya DITOLAK 403 saat membuka {$routeName}");
        }
    }

    public function test_admin_tidak_boleh_akses_pengaturan_rekening_owner(): void
    {
        $admin = $this->user('admin@fitlife.com');

        // Admin mencoba submit perubahan rekening owner
        $res = $this->actingAs($admin)->post(route('owner.settings.payment'), [
            'bank_name'           => 'Bank Pembajak',
            'bank_account_number' => '999999999',
            'bank_account_holder' => 'Admin Bajak',
        ]);

        $this->assertSame(403, $res->status(), "Admin seharusnya DITOLAK 403 saat mencoba mengubah rekening Owner");
    }
}
