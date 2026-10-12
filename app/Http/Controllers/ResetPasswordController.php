<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    /**
     * Menampilkan form ganti password (dari link email).
     */
    public function showResetForm(Request $request, string $token)
    {
        return view('reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    /**
     * Memproses password baru + verifikasi token.
     */
    public function reset(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'token'    => 'required',
            'email'    => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ], [
            'email.required'            => 'Email wajib diisi.',
            'email.email'               => 'Format email tidak valid.',
            'password.required'         => 'Password baru wajib diisi.',
            'password.confirmed'        => 'Konfirmasi password tidak cocok.',
            'password.min'              => 'Password minimal 8 karakter.',
        ]);

        // 2. Reset password — Laravel verifikasi token (hash, expiry 60 menit, sekali pakai),
        //    lalu hapus token dari DB setelah berhasil.
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => $password, // cast 'hashed' di model User otomatis hash
                    'must_change_password' => false,
                ])->setRememberToken(Str::random(60)); // matikan cookie "remember me" lama

                $user->save();

                // Jika ada permohonan reset password admin yang terkait, tandai selesai
                \App\Models\AdminPasswordResetRequest::where('user_id', $user->id)
                    ->where('status', 'approved')
                    ->update(['status' => 'completed']);
            }
        );

        // Pastikan tidak ada sesi aktif (tidak auto-login)
        if (\Illuminate\Support\Facades\Auth::check()) {
            \Illuminate\Support\Facades\Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        // 3. Arahkan ke halaman login yang tepat
        if ($status === Password::PASSWORD_RESET) {
            $user = \App\Models\User::where('email', $request->email)->first();
            if ($user && $user->role === 'member') {
                $tenant = $user->tenant;
                $memberLoginUrl = $tenant ? $tenant->publicLandingUrl() . '/login' : route('member.login');
                return redirect($memberLoginUrl)->with('success', 'Kata sandi Anda berhasil diperbarui. Silakan masuk dengan kata sandi baru Anda.');
            }

            return redirect()->route('login')->with('success', 'Kata sandi Anda berhasil diperbarui. Silakan masuk dengan email dan kata sandi baru Anda.');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'Link reset password tidak valid atau sudah kedaluwarsa. Silakan minta tautan reset baru.']);
    }
}