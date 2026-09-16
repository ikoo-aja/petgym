<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginThrottleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Login dikunci sementara setelah 5x percobaan gagal (anti brute-force).
     */
    public function test_login_is_locked_after_too_many_failed_attempts(): void
    {
        $user = User::factory()->create([
            'email'    => 'member@fitlife.com',
            'password' => 'password123',
        ]);

        // 5 percobaan gagal
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('login'), [
                'email'    => 'member@fitlife.com',
                'password' => 'password-salah',
            ])->assertSessionHasErrors('email');
        }

        // Percobaan ke-6 harus ditolak karena kunci sementara
        $this->post(route('login'), [
            'email'    => 'member@fitlife.com',
            'password' => 'password123', // password benar pun tetap ditolak
        ])->assertSessionHasErrors('email');

        $this->assertStringContainsString(
            'Terlalu banyak percobaan login',
            session('errors')->first('email')
        );
    }

    /**
     * Login pakai password benar tetap berhasil (hitungan gagal dibersihkan).
     */
    public function test_successful_login_clears_failed_attempts(): void
    {
        $user = User::factory()->create([
            'email'    => 'member@fitlife.com',
            'password' => 'password123',
            'role'     => 'member',
        ]);

        $this->post(route('login'), [
            'email'    => 'member@fitlife.com',
            'password' => 'password-salah',
        ])->assertSessionHasErrors('email');

        $this->post(route('login'), [
            'email'    => 'member@fitlife.com',
            'password' => 'password123',
        ])->assertRedirect('/member/dashboard');
    }

    /**
     * User yang emailnya belum diverifikasi diarahkan ke halaman verifikasi,
     * dan tidak bisa membuka dashboard (middleware 'verified').
     */
    public function test_unverified_user_is_sent_to_verification_notice(): void
    {
        $user = User::factory()->unverified()->create([
            'email'    => 'staff-baru@fitlife.com',
            'password' => 'password123',
            'role'     => 'receptionist',
        ]);

        $this->post(route('login'), [
            'email'    => 'staff-baru@fitlife.com',
            'password' => 'password123',
        ])->assertRedirect(route('verification.notice'));

        // Akses langsung ke dashboard role juga ditolak middleware 'verified'
        $this->actingAs($user)
            ->get('/receptionist/dashboard')
            ->assertRedirect(route('verification.notice'));
    }
}
