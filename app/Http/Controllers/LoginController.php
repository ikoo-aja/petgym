<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use App\Models\User;

class LoginController extends Controller
{
    /**
     * Maksimal percobaan login yang diperbolehkan sebelum dikunci sementara.
     */
    protected int $maxAttempts = 5;

    /**
     * Durasi penguncian (detik) setelah terlalu banyak percobaan gagal.
     */
    protected int $decaySeconds = 60;

    /**
     * Menampilkan halaman/view login.
     */
    public function showLoginForm()
    {
        return view('login');
    }

    /**
     * Kunci throttle unik per email + IP (anti brute-force terdistribusi).
     */
    protected function throttleKey(Request $request): string
    {
        return 'login:' . Str::lower($request->input('email')) . '|' . $request->ip();
    }

    /**
     * Memproses autentikasi pengguna.
     */
    public function login(Request $request)
    {
        // 1. Validasi Input
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // 2. Proteksi Brute-Force: kunci sementara jika terlalu banyak percobaan gagal
        $throttleKey = $this->throttleKey($request);
        if (RateLimiter::tooManyAttempts($throttleKey, $this->maxAttempts)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $menit   = (int) ceil($seconds / 60);

            return back()
                ->withErrors(['email' => "Terlalu banyak percobaan login. Akun Anda dikunci sementara, coba lagi dalam {$menit} menit."])
                ->onlyInput('email');
        }

        // Cek input "Remember Me" dari form
        $remember = $request->has('remember');

        // 3. Percobaan Autentikasi
        if (Auth::attempt($credentials, $remember)) {
            // Regenerasi session untuk mencegah session fixation attack
            $request->session()->regenerate();

            /** @var \App\Models\User $user */
            $user = Auth::user();

            // 3a. Wajib verifikasi email sebelum bisa masuk ke dashboard
            // (user tetap login tapi diarahkan ke halaman verifikasi dulu)
            if (!$user->hasVerifiedEmail()) {
                RateLimiter::clear($throttleKey);

                return redirect()->route('verification.notice');
            }

            // 3b. Login sukses -> bersihkan hitungan percobaan gagal
            RateLimiter::clear($throttleKey);

            if ($user->isSuperadmin()) {
                return redirect()->intended('/superadmin/dashboard')->with('success', 'Selamat datang Superadmin!');
            }

            if ($user->isOwner()) {
                return redirect()->intended('/owner/dashboard')->with('success', 'Selamat datang Pemilik Gym! Anda dalam mode pemantauan bisnis (Read-Only).');
            }

            if ($user->isAdmin()) {
                return redirect()->intended('/admin/dashboard')->with('success', 'Selamat datang di Dashboard Admin!');
            }

            if ($user->isManager()) {
                return redirect()->intended('/manager/dashboard')->with('success', 'Selamat datang di Dashboard Manager!');
            }

            if ($user->isReceptionist()) {
                return redirect()->intended('/receptionist/dashboard')->with('success', 'Selamat datang di Dashboard Resepsionis!');
            }

            if ($user->role === 'trainer') {
                return redirect()->intended('/trainer/dashboard')->with('success', 'Selamat datang di Dashboard Personal Trainer!');
            }

            if ($user->role === 'member') {
                return redirect()->intended('/member/dashboard')->with('success', 'Selamat datang di Portal Keanggotaan Member Gym!');
            }

            return redirect()->intended('/member/dashboard')->with('success', 'Selamat datang kembali!');
        }

        // 4. Jika Autentikasi Gagal -> catat percobaan gagal untuk throttle
        RateLimiter::hit($throttleKey, $this->decaySeconds);

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    /**
     * Memproses Logout pengguna.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Anda telah berhasil keluar.');
    }
}
