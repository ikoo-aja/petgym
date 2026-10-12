<?php

namespace Tests\Audit;

use App\Models\AdminPasswordResetRequest;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetFlowTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_reset_password_admin_membuat_permohonan_ke_superadmin(): void
    {
        $admin = User::where('email', 'admin@fitlife.com')->firstOrFail();

        // 1. Admin mengajukan lupa kata sandi
        $response = $this->post(route('password.email'), [
            'email' => $admin->email,
        ]);

        $response->assertStatus(302);

        // 2. Harus tercatat record pending di admin_password_reset_requests dengan target_type = superadmin
        $req = AdminPasswordResetRequest::where('user_id', $admin->id)->first();
        $this->assertNotNull($req);
        $this->assertSame('pending', $req->status);
        $this->assertSame('superadmin', $req->target_type);
        $this->assertSame($admin->email, $req->email);
    }

    public function test_reset_password_role_non_admin_membuat_permohonan_ke_admin_gym(): void
    {
        $manager = User::where('email', 'manager@fitlife.com')->firstOrFail();

        // Manager mengajukan lupa kata sandi
        $response = $this->post(route('password.email'), [
            'email' => $manager->email,
        ]);

        $response->assertStatus(302);

        // Harus tercatat permohonan ke Admin gym (target_type = admin)
        $req = AdminPasswordResetRequest::where('user_id', $manager->id)->first();
        $this->assertNotNull($req);
        $this->assertSame('pending', $req->status);
        $this->assertSame('admin', $req->target_type);
        $this->assertSame($manager->tenant_id, $req->tenant_id);
    }

    public function test_reset_password_member_membuat_permohonan_ke_admin_gym(): void
    {
        $member = User::where('role', 'member')->firstOrFail();

        $response = $this->post(route('password.email'), [
            'email' => $member->email,
        ]);

        $response->assertStatus(302);

        $req = AdminPasswordResetRequest::where('user_id', $member->id)->first();
        $this->assertNotNull($req);
        $this->assertSame('pending', $req->status);
        $this->assertSame('admin', $req->target_type);
    }

    public function test_admin_gym_dapat_menyetujui_permohonan_reset_password_staf_dan_member(): void
    {
        $admin = User::where('email', 'admin@fitlife.com')->firstOrFail();
        $trainer = User::where('email', 'alex@fitlife.com')->firstOrFail();

        $req = AdminPasswordResetRequest::create([
            'user_id'     => $trainer->id,
            'tenant_id'   => $admin->tenant_id,
            'target_type' => 'admin',
            'email'       => $trainer->email,
            'admin_name'  => $trainer->name,
            'user_role'   => 'trainer',
            'status'      => 'pending',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.password-resets.approve', $req->id));

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $this->assertSame('approved', $req->fresh()->status);
        $this->assertSame($admin->id, $req->fresh()->approved_by);
    }

    public function test_superadmin_dapat_menyetujui_permohonan_reset_password_admin(): void
    {
        $superadmin = User::where('role', 'superadmin')->firstOrFail();
        $admin = User::where('email', 'admin@fitlife.com')->firstOrFail();

        $req = AdminPasswordResetRequest::create([
            'user_id'     => $admin->id,
            'tenant_id'   => $admin->tenant_id,
            'target_type' => 'superadmin',
            'email'       => $admin->email,
            'admin_name'  => $admin->name,
            'user_role'   => 'admin',
            'status'      => 'pending',
        ]);

        $response = $this->actingAs($superadmin)->post(route('superadmin.password-resets.approve', $req->id));

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $this->assertSame('approved', $req->fresh()->status);
        $this->assertSame($superadmin->id, $req->fresh()->approved_by);
    }

    public function test_setelah_berhasil_reset_password_redirect_ke_login_dan_tidak_auto_login(): void
    {
        $admin = User::where('email', 'admin@fitlife.com')->firstOrFail();

        // Generate token reset valid
        $token = Password::createToken($admin);

        $response = $this->post(route('password.update'), [
            'token'                 => $token,
            'email'                 => $admin->email,
            'password'              => 'PasswordBaru1234!',
            'password_confirmation' => 'PasswordBaru1234!',
        ]);

        // Harus redirect ke login
        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success');

        // Tidak boleh auto-login
        $this->assertFalse(Auth::check());

        // Password di database harus sudah diperbarui dan bisa dipakai login
        $this->assertTrue(Hash::check('PasswordBaru1234!', $admin->fresh()->password));

        // Coba login dengan password baru
        $loginResponse = $this->post(route('login'), [
            'email'    => $admin->email,
            'password' => 'PasswordBaru1234!',
        ]);

        $loginResponse->assertStatus(302);
        $this->assertTrue(Auth::check());
        $this->assertSame($admin->id, Auth::id());
    }
}
