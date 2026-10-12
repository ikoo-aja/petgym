<?php

namespace Tests\Audit;

use App\Models\AdminPasswordResetRequest;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Tests\TestCase;

class AdminStaffResetButtonTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_tombol_setujui_reset_pw_hanya_muncul_saat_ada_permohonan_pending(): void
    {
        $admin = User::where('email', 'admin@fitlife.com')->firstOrFail();
        $manager = User::where('email', 'manager@fitlife.com')->firstOrFail();

        // 1. Awalnya tidak ada permohonan reset password pending
        $response = $this->actingAs($admin)->get(route('admin.staff.index'));
        $response->assertStatus(200);
        $response->assertDontSee('Setujui Reset PW');

        // 2. Buat permohonan reset password pending untuk manager
        $req = AdminPasswordResetRequest::create([
            'user_id'     => $manager->id,
            'tenant_id'   => $admin->tenant_id,
            'target_type' => 'admin',
            'email'       => $manager->email,
            'admin_name'  => $manager->name,
            'user_role'   => 'manager',
            'status'      => 'pending',
        ]);

        // 3. Akses kembali halaman admin/staff -> tombol harus muncul di samping tombol Hapus
        $responseWithPending = $this->actingAs($admin)->get(route('admin.staff.index'));
        $responseWithPending->assertStatus(200);
        $responseWithPending->assertSee('Setujui Reset PW');
        $responseWithPending->assertSee(route('admin.password-resets.approve', $req->id));

        // 4. Setelah disetujui -> tombol harus otomatis hilang
        $approveResponse = $this->actingAs($admin)->post(route('admin.password-resets.approve', $req->id));
        $approveResponse->assertStatus(302);
        $approveResponse->assertSessionHas('success');

        $responseAfterApprove = $this->actingAs($admin)->get(route('admin.staff.index'));
        $responseAfterApprove->assertStatus(200);
        $responseAfterApprove->assertDontSee('Setujui Reset PW');
    }
}
