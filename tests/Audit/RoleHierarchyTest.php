<?php

namespace Tests\Audit;

use App\Models\Member;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Tests\TestCase;

/**
 * AUDIT HIRARKI ROLE PADA AKSI TULIS (POST/PUT/DELETE)
 *
 * Fokus: memastikan role dengan jabatan lebih rendah tidak bisa
 * memodifikasi data yang bukan wewenangnya, walau route middleware
 * mengizinkannya.
 */
class RoleHierarchyTest extends TestCase
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

    private function firstMember(): Member
    {
        $tenant = Tenant::where('slug', 'fitlife')->firstOrFail();
        return Member::where('tenant_id', $tenant->id)->firstOrFail();
    }

    /**
     * Trainer hanya boleh MELIHAT data member, bukan menulis.
     * Route group /manager/* memang mengizinkan trainer (untuk view),
     * sehingga pengamanan HARUS ada di controller.
     */
    public function test_trainer_tidak_boleh_mendaftarkan_member(): void
    {
        $trainer = $this->user('alex@fitlife.com');

        $response = $this->actingAs($trainer)->post(route('manager.members.store'), [
            'name' => 'Member Buatan Trainer',
            'phone' => '081200000000',
            'gender' => 'Laki-laki',
            'package_duration' => '1',
            'payment_method' => 'cash',
        ]);

        $this->assertSame(
            403,
            $response->getStatusCode(),
            "Trainer berhasil memanggil pendaftaran member (harusnya 403). Status: {$response->getStatusCode()}"
        );
    }

    public function test_trainer_tidak_boleh_mengubah_data_member(): void
    {
        $trainer = $this->user('alex@fitlife.com');
        $member = $this->firstMember();

        $response = $this->actingAs($trainer)->put(route('manager.members.update', $member->id), [
            'name' => 'Diubah Trainer',
            'gender' => 'Laki-laki',
            'status' => 'active',
        ]);

        $this->assertSame(
            403,
            $response->getStatusCode(),
            "Trainer berhasil mengubah data member (harusnya 403). Status: {$response->getStatusCode()}"
        );
    }

    public function test_trainer_tidak_boleh_menyetujui_member(): void
    {
        $trainer = $this->user('alex@fitlife.com');
        $member = $this->firstMember();

        $response = $this->actingAs($trainer)->post(route('manager.members.approve', $member->id));

        $this->assertSame(
            403,
            $response->getStatusCode(),
            "Trainer berhasil menyetujui (approve) member (harusnya 403). Status: {$response->getStatusCode()}"
        );
    }

    public function test_trainer_tidak_boleh_menghapus_member(): void
    {
        $trainer = $this->user('alex@fitlife.com');
        $member = $this->firstMember();

        $response = $this->actingAs($trainer)->delete(route('manager.members.destroy', $member->id));

        $this->assertSame(
            403,
            $response->getStatusCode(),
            "Trainer berhasil menghapus data member (harusnya 403). Status: {$response->getStatusCode()}"
        );
    }

    /**
     * Aksi lintas area wajib ditolak 403 oleh middleware role.
     */
    public function test_aksi_tulis_lintas_area_ditolak_403(): void
    {
        $admin = $this->user('admin@fitlife.com');
        $manager = $this->user('manager@fitlife.com');
        $supervisor = $this->user('supervisor@fitlife.com');
        $receptionist = $this->user('resepsionis@fitlife.com');
        $trainer = $this->user('alex@fitlife.com');
        $owner = $this->user('owner@fitlife.com');
        $member = $this->user('member@fitlife.com');
        $superadmin = $this->user('superadmin@petgym.com');

        $checks = [
            'admin -> superadmin.plans.store' => [$admin, 'post', 'superadmin.plans.store'],
            'admin -> superadmin.tenants.store' => [$admin, 'post', 'superadmin.tenants.store'],
            'manager -> admin.staff.store' => [$manager, 'post', 'admin.staff.store'],
            'manager -> superadmin.tenants.store' => [$manager, 'post', 'superadmin.tenants.store'],
            'supervisor -> manager.promo.store' => [$supervisor, 'post', 'manager.promo.store'],
            'supervisor -> manager.members.store' => [$supervisor, 'post', 'manager.members.store'],
            'receptionist -> admin.staff.store' => [$receptionist, 'post', 'admin.staff.store'],
            'trainer -> supervisor.shifts.store' => [$trainer, 'post', 'supervisor.shifts.store'],
            'owner -> manager.members.store' => [$owner, 'post', 'manager.members.store'],
            'owner -> supervisor.equipment.store' => [$owner, 'post', 'supervisor.equipment.store'],
            'member -> manager.members.store' => [$member, 'post', 'manager.members.store'],
            'member -> receptionist.pos.checkout' => [$member, 'post', 'receptionist.pos.checkout'],
            'superadmin -> admin.staff.store' => [$superadmin, 'post', 'admin.staff.store'],
        ];

        $failures = [];

        foreach ($checks as $label => [$user, $method, $route]) {
            $response = $this->actingAs($user)->{$method}(route($route));
            $status = $response->getStatusCode();

            if ($status !== 403) {
                $failures[] = "{$label} => HTTP {$status} (harusnya 403)";
            }
        }

        $this->assertSame([], $failures, "Aksi lintas area tidak terlindungi:\n" . implode("\n", $failures));
    }
}
