<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class LoginController extends Controller
{
    /**
     * Menampilkan halaman/view login.
     */
    public function showLoginForm()
    {
        return view('login'); // sesuaikan dengan nama file blade kamu (misal: resources/views/auth/login.blade.php)
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

        // Cek input "Remember Me" dari form
        $remember = $request->has('remember');

        // 2. Percobaan Autentikasi
        if (Auth::attempt($credentials, $remember)) {
            // Regenerasi session untuk mencegah session fixation attack
            $request->session()->regenerate();

            /** @var \App\Models\User $user */
            $user = Auth::user();

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

        // 3. Jika Autentikasi Gagal
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

    /**
     * Menampilkan halaman/view pendaftaran (Register) Member Baru.
     */
    public function showRegisterForm()
    {
        $tenants = \App\Models\Tenant::where('status', 'active')->get();
        return view('register', compact('tenants'));
    }

    /**
     * Memproses pendaftaran (Register) Member Baru.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'phone'    => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:4', 'confirmed'],
            'tenant_id'=> ['nullable', 'exists:tenants,id'],
        ], [
            'email.unique'       => 'Email sudah terdaftar di sistem. Silakan gunakan menu Masuk/Login.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $tenantId = $request->tenant_id ?? 1;

        $user = User::create([
            'tenant_id' => $tenantId,
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role'      => 'member',
        ]);

        \App\Models\Member::create([
            'tenant_id'       => $tenantId,
            'user_id'         => $user->id,
            'name'            => $user->name,
            'email'           => $user->email,
            'phone'           => $request->phone,
            'access_code'     => 'MBR-' . strtoupper(substr(md5(uniqid()), 0, 6)),
            'membership_tier' => 'basic',
            'status'          => 'active',
            'expired_at'      => now()->addDays(30),
        ]);

        Auth::login($user);

        return redirect()->intended('/member/dashboard')->with('success', 'Pendaftaran akun member berhasil! Selamat datang di Portal Keanggotaan Gym.');
    }
}
