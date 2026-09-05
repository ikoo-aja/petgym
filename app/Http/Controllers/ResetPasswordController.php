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
                ])->setRememberToken(Str::random(60)); // matikan cookie "remember me" lama

                $user->save();
            }
        );

        // 3. Arahkan sesuai hasil
        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', 'Password Anda berhasil diubah. Silakan login dengan password baru.')
            : back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Link reset password tidak valid atau sudah kadaluarsa. Silakan minta link baru.']);
    }
}