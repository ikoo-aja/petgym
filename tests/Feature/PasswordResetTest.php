<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\ResetPassword;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Halaman form "Lupa Password" bisa diakses tanpa login.
     */
    public function test_forgot_password_page_is_accessible(): void
    {
        $this->get(route('password.request'))
            ->assertOk()
            ->assertSee('Lupa Password');
    }

    /**
     * Request link reset mengirim notifikasi email berisi token.
     */
    public function test_sending_reset_link_sends_notification(): void
    {
        Notification::fake();

        $user = User::factory()->create(['email' => 'member@fitlife.com']);

        $this->post(route('password.email'), ['email' => 'member@fitlife.com'])
            ->assertRedirect();

        Notification::assertSentTo($user, ResetPassword::class);
    }

    /**
     * Email yang tidak terdaftar tetap mendapat pesan yang sama (anti user enumeration).
     */
    public function test_unknown_email_gets_same_response(): void
    {
        Notification::fake();

        $this->post(route('password.email'), ['email' => 'tidakada@fitlife.com'])
            ->assertRedirect()
            ->assertSessionHas('status');

        Notification::assertNothingSent();
    }

    /**
     * Halaman reset dengan token menampilkan form ganti password.
     */
    public function test_reset_password_page_requires_token(): void
    {
        $this->get('/reset-password/token-abc')
            ->assertOk()
            ->assertSee('Buat Password Baru');
    }

    /**
     * Alur lengkap: request link -> buka link -> ganti password -> login dengan password baru.
     */
    public function test_user_can_reset_password_and_login_with_new_password(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email'    => 'member@fitlife.com',
            'password' => 'passwordlama',
            'role'     => 'member',
        ]);

        // 1. Minta link reset
        $this->post(route('password.email'), ['email' => 'member@fitlife.com'])
            ->assertRedirect();

        // 2. Ambil token dari notifikasi yang dikirim
        $notification = Notification::sent($user, ResetPassword::class)->first();
        $token = $notification->token;

        // 3. Buka link + submit password baru
        $this->get(route('password.reset', ['token' => $token, 'email' => 'member@fitlife.com']))
            ->assertOk();

        $this->post(route('password.update'), [
            'token'                 => $token,
            'email'                 => 'member@fitlife.com',
            'password'              => 'passwordbaru123',
            'password_confirmation' => 'passwordbaru123',
        ])->assertRedirect(route('login'));

        // 4. Password lama gagal, password baru berhasil
        $this->assertFalse(Hash::check('passwordlama', $user->fresh()->password));
        $this->assertTrue(Hash::check('passwordbaru123', $user->fresh()->password));

        $this->post(route('login'), [
            'email'    => 'member@fitlife.com',
            'password' => 'passwordbaru123',
        ])->assertRedirect('/member/dashboard');
    }

    /**
     * Token yang tidak valid ditolak.
     */
    public function test_invalid_token_is_rejected(): void
    {
        $user = User::factory()->create([
            'email'    => 'member@fitlife.com',
            'password' => 'passwordlama',
        ]);

        $this->post(route('password.update'), [
            'token'                 => 'token-salah',
            'email'                 => 'member@fitlife.com',
            'password'              => 'passwordbaru123',
            'password_confirmation' => 'passwordbaru123',
        ])->assertSessionHasErrors('email');

        $this->assertTrue(Hash::check('passwordlama', $user->fresh()->password));
    }
}