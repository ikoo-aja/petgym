<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        $tenant = Tenant::create([
            'name'        => 'FitLife Verif Test',
            'subdomain'   => 'fitlife-verif-' . uniqid() . '.workout.id',
            'slug'        => 'fitlife-verif-' . uniqid(),
            'owner_name'  => 'Test Owner',
            'owner_email' => 'owner@fitlife.com',
            'plan_name'   => 'Paket Pro',
            'status'      => 'active',
        ]);

        return User::factory()->create([
            'email'     => 'admin@fitlife.com',
            'password'  => 'password123',
            'role'      => 'admin',
            'tenant_id' => $tenant->id,
        ]);
    }

    /**
     * Saat admin membuat akun staf, email verifikasi otomatis terkirim.
     */
    public function test_admin_creating_staff_sends_verification_email(): void
    {
        Notification::fake();

        $admin = $this->createAdmin();

        $this->actingAs($admin)
            ->post(route('admin.staff.store'), [
                'name'     => 'Rina Baru',
                'email'    => 'rina-baru@fitlife.com',
                'password' => 'password123',
                'role'     => 'receptionist',
            ])
            ->assertRedirect(route('admin.staff.index'));

        $staff = User::where('email', 'rina-baru@fitlife.com')->firstOrFail();

        Notification::assertSentTo($staff, VerifyEmail::class);
    }

    /**
     * Alur lengkap: akun baru -> belum bisa login -> klik link verifikasi -> bisa login.
     */
    public function test_staff_can_verify_email_then_login(): void
    {
        $admin = $this->createAdmin();
        $tenant = $admin->tenant;

        $staff = User::create([
            'tenant_id' => $tenant->id,
            'name'      => 'Staf Baru',
            'email'     => 'staf-baru@fitlife.com',
            'password'  => 'password123',
            'role'      => 'manager',
        ]);

        $this->assertFalse($staff->hasVerifiedEmail());

        // Sebelum verifikasi -> login diarahkan ke halaman verifikasi
        $this->post(route('login'), [
            'email'    => 'staf-baru@fitlife.com',
            'password' => 'password123',
        ])->assertRedirect(route('verification.notice'));

        // Link tanpa tanda tangan (dipalsukan) ditolak
        $this->get(route('verification.verify', [
            'id'   => $staff->id,
            'hash' => sha1($staff->email),
        ]))->assertForbidden();

        // Buka link verifikasi asli (signed URL)
        $url = URL::temporarySignedRoute('verification.verify', now()->addMinutes(60), [
            'id'   => $staff->id,
            'hash' => sha1($staff->email),
        ]);

        $this->get($url);

        $this->assertTrue($staff->fresh()->hasVerifiedEmail());

        // Setelah verifikasi -> login sukses ke dashboard role-nya
        $this->post(route('login'), [
            'email'    => 'staf-baru@fitlife.com',
            'password' => 'password123',
        ])->assertRedirect('/manager/dashboard');
    }
}
