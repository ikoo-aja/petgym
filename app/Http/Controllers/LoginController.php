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

            // Redirect ke halaman yang tuju sebelumnya (atau ke dashboard/home)
            return redirect()->intended('/superadmin/dashboard')->with('success', 'Selamat datang kembali!');
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
}
