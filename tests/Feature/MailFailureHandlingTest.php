<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MailFailureHandlingTest extends TestCase
{
    use RefreshDatabase;

    private function simulateDeadSmtp(): void
    {
        // Arahkan mailer ke port yang pasti ditolak (127.0.0.1:1) supaya
        // pengiriman email melempar TransportException seperti SMTP mati.
        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp' => array_merge(config('mail.mailers.smtp'), [
                'host'    => '127.0.0.1',
                'port'    => 1,
                'timeout' => 1,
            ]),
        ]);
    }

    private function makeTenant(): Tenant
    {
        return Tenant::create([
            'name'        => 'Mail Test Gym ' . uniqid(),
            'subdomain'   => 'mail-' . uniqid() . '.workout.id',
            'owner_name'  => 'Test Owner',
            'owner_email' => 'owner@mail-test.com',
            'status'      => 'active',
        ]);
    }

    /**
     * Add staf tetap tersimpan + redirect sukses (bukan 500) walau SMTP mati.
     */
    public function test_add_staff_still_saves_when_mail_server_down(): void
    {
        $this->simulateDeadSmtp();

        $tenant = $this->makeTenant();
        $admin  = User::factory()->create([
            'email'     => 'admin-' . uniqid() . '@mail-test.com',
            'password'  => 'password123',
            'role'      => 'admin',
            'tenant_id' => $tenant->id,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.staff.store'), [
                'name'     => 'Staf Baru Repro',
                'email'    => 'staff-' . uniqid() . '@fitlife.com',
                'password' => '1234',
                'role'     => 'receptionist',
            ])
            ->assertRedirect(route('admin.staff.index'))
            ->assertSessionHas('warning');

        // Akun tetap tersimpan walaupun email gagal terkirim
        $this->assertDatabaseHas('users', [
            'role' => 'receptionist',
        ]);
    }

    /**
     * Kirim ulang verifikasi tidak 500 walau SMTP mati — kasih pesan error.
     */
    public function test_resend_verification_handles_mail_failure(): void
    {
        $this->simulateDeadSmtp();

        $tenant = $this->makeTenant();
        $admin  = User::factory()->create([
            'email'     => 'admin-' . uniqid() . '@mail-test.com',
            'password'  => 'password123',
            'role'      => 'admin',
            'tenant_id' => $tenant->id,
        ]);

        // Staf baru yang BELUM verifikasi email
        $staff = User::factory()->create([
            'email'     => 'staff-' . uniqid() . '@fitlife.com',
            'password'  => 'password123',
            'role'      => 'trainer',
            'tenant_id' => $tenant->id,
            'email_verified_at' => null,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.staff.send-verification', $staff->id))
            ->assertRedirect(route('admin.staff.index'))
            ->assertSessionHas('error');
    }

    /**
     * Lupa password tetap menampilkan respons aman (bukan 500) saat SMTP mati.
     */
    public function test_forgot_password_handles_mail_failure(): void
    {
        $this->simulateDeadSmtp();

        $tenant = $this->makeTenant();
        $user   = User::factory()->create([
            'email'     => 'user-' . uniqid() . '@mail-test.com',
            'password'  => 'password123',
            'role'      => 'member',
            'tenant_id' => $tenant->id,
        ]);

        $this->from(route('password.request'))
            ->post(route('password.email'), [
                'email' => $user->email,
            ])
            ->assertRedirect(route('password.request'))
            ->assertSessionHas('status')      // respons anti-enumeration tetap muncul
            ->assertSessionHas('warning');    // + info bahwa email gagal terkirim
    }
}