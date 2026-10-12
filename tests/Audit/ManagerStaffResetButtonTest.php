<?php

namespace Tests\Audit;

use App\Models\AdminPasswordResetRequest;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Tests\TestCase;

class ManagerStaffResetButtonTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_tombol_setujui_reset_pw_di_manager_staff_hanya_muncul_saat_ada_permohonan(): void
    {
        $manager = User::where('email', 'manager@fitlife.com')->firstOrFail();
        $receptionist = User::where('email', 'resepsionis@fitlife.com')->firstOrFail();

        // 1. Kondisi awal tanpa permohonan reset pw: tombol TIDAK muncul
        $response = $this->actingAs($manager)->get(route('manager.staff.index'));
        $response->assertStatus(200);
        $response->assertDontSee('Setujui Reset PW');

        // 2. Buat permohonan reset password pending untuk resepsionis
        $req = AdminPasswordResetRequest::create([
            'user_id'     => $receptionist->id,
            'tenant_id'   => $manager->tenant_id,
            'target_type' => 'admin',
            'email'       => $receptionist->email,
            'admin_name'  => $receptionist->name,
            'user_role'   => 'receptionist',
            'status'      => 'pending',
        ]);

        // 3. Akses kembali halaman manager/staff -> tombol harus muncul di samping tombol Hapus
        $responseWithPending = $this->actingAs($manager)->get(route('manager.staff.index'));
        $responseWithPending->assertStatus(200);
        $responseWithPending->assertSee('Setujui Reset PW');
        $responseWithPending->assertSee(route('manager.staff.password-resets.approve', $req->id));

        // 4. Setelah disetujui oleh manager -> tombol otomatis hilang
        $approveResponse = $this->actingAs($manager)->post(route('manager.staff.password-resets.approve', $req->id));
        $approveResponse->assertStatus(302);
        $approveResponse->assertSessionHas('success');

        $responseAfterApprove = $this->actingAs($manager)->get(route('manager.staff.index'));
        $responseAfterApprove->assertStatus(200);
        $responseAfterApprove->assertDontSee('Setujui Reset PW');
    }
}
