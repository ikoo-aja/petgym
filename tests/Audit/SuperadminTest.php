<?php

namespace Tests\Audit;

use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Tests\TestCase;

/**
 * AUDIT AKSI SUPERADMIN TERHADAP TENANT
 *
 * 1. Subdomain divalidasi pada nilai mentah, padahal yang disimpan sudah diformat
 *    (ditambah `.workout.id`) dan kolomnya UNIQUE.
 * 2. Menghapus tenant membuat stafnya kehilangan tenant (FK nullOnDelete),
 *    tetapi mereka masih bisa login.
 */
class SuperadminTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    private function superadmin(): User
    {
        return User::where('email', 'superadmin@petgym.com')->firstOrFail();
    }

    public function test_subdomain_duplikat_menghasilkan_validasi_bukan_500(): void
    {
        // Tenant bawaan: subdomain "fitlife.workout.id", slug "fitlife"
        $response = $this->actingAs($this->superadmin())
            ->from('/')
            ->post(route('superadmin.tenants.store'), [
                'name' => 'Gym Duplikat',
                'subdomain' => 'fitlife', // akan diformat jadi fitlife.workout.id
                'owner_email' => 'duplikat@uji.test',
            ]);

        $this->assertNotSame(
            500,
            $response->getStatusCode(),
            "Subdomain duplikat lolos validasi lalu memicu error server (HTTP {$response->getStatusCode()})."
        );
    }

    public function test_staf_tenant_terhapus_tidak_menyebabkan_error_500(): void
    {
        $tenant = Tenant::where('slug', 'fitlife')->firstOrFail();
        $manager = User::where('email', 'manager@fitlife.com')->firstOrFail();

        $this->actingAs($this->superadmin())->delete(route('superadmin.tenants.destroy', $tenant->id));

        $manager->refresh();

        $response = $this->actingAs($manager)->get(route('manager.members.index'));

        $this->assertNotSame(
            500,
            $response->getStatusCode(),
            "Staf dari tenant yang sudah dihapus mengalami error server (HTTP {$response->getStatusCode()})."
        );
    }

    public function test_halaman_dan_fitur_lama_reset_password_dan_ubah_prospek_sudah_dihapus(): void
    {
        // 1. Route GET /superadmin/password-resets sudah tidak ada (404)
        $resReset = $this->actingAs($this->superadmin())->get('/superadmin/password-resets');
        $this->assertSame(404, $resReset->getStatusCode());

        // 2. Route PUT /superadmin/registrations/{id} (ubah data prospek) sudah tidak ada (405/404)
        $resUpdate = $this->actingAs($this->superadmin())->put('/superadmin/registrations/1', [
            'name' => 'Test',
        ]);
        $this->assertTrue(in_array($resUpdate->getStatusCode(), [404, 405]));
    }

    public function test_daftar_penyewa_tidak_ada_tombol_akun_siap_dan_reset_password_hanya_aktif_saat_ada_request(): void
    {
        $admin = User::where('email', 'admin@fitlife.com')->firstOrFail();

        // Pastikan ada registration yang approved
        $reg = \App\Models\TenantRegistration::create([
            'name'              => 'FitLife Studio',
            'email'             => 'admin@fitlife.com',
            'phone'             => '08123456789',
            'status'            => 'approved',
            'created_user_id'   => $admin->id,
            'selected_features' => ['Fitur 1'],
        ]);

        // 1. Kondisi awal: Tidak ada request reset password -> 'Akun Siap' tidak muncul, Reset Password disabled
        $response = $this->actingAs($this->superadmin())->get(route('superadmin.registrations'));
        $response->assertStatus(200);
        $response->assertDontSee('Akun Siap');
        $response->assertSee('Hubungi');
        $response->assertDontSee('Ubah Data Prospek');
        // Reset password disabled
        $response->assertSee('Tidak ada permohonan');

        // 2. Buat request reset password dari Admin
        $req = \App\Models\AdminPasswordResetRequest::create([
            'user_id'     => $admin->id,
            'tenant_id'   => $admin->tenant_id,
            'target_type' => 'superadmin',
            'email'       => $admin->email,
            'admin_name'  => $admin->name,
            'user_role'   => 'admin',
            'status'      => 'pending',
        ]);

        // 3. Muat ulang halaman daftar penyewa -> Reset Password aktif & modal approval tersedia
        $responseWithReq = $this->actingAs($this->superadmin())->get(route('superadmin.registrations'));
        $responseWithReq->assertStatus(200);
        $responseWithReq->assertSee('Approval');
        $responseWithReq->assertSee("resetPasswordModal{$reg->id}");
        $responseWithReq->assertSee('Setujui &amp; Kirim Link', false);

        // 4. Superadmin menyetujui request reset password
        $approveRes = $this->actingAs($this->superadmin())->post(route('superadmin.password-resets.approve', $req->id));
        $approveRes->assertStatus(302);
        $approveRes->assertSessionHas('success');
        $this->assertSame('approved', $req->fresh()->status);
    }
}
