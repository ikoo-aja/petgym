<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /**
     * Pesan anti user-enumeration: identik apakah email terdaftar atau tidak.
     */
    private const SAFE_RESPONSE = 'Jika email tersebut terdaftar di sistem kami, link reset password telah dikirim ke inbox Anda.';

    /**
     * Menampilkan form "Lupa Password" (input email).
     */
    public function showLinkRequestForm()
    {
        return view('forgot-password');
    }

    /**
     * Mengirim link reset password ke email pengguna.
     */
    public function sendResetLinkEmail(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
        ]);

        // 2. Kirim link reset (Laravel yang handle token + tabel password_reset_tokens)
        //    sendResetLink otomatis: buat token acak ter-hash, simpan ke DB,
        //    kirim email lewat notifikasi ResetPassword, dan throttle 60 detik per email.
        try {
            $status = Password::sendResetLink(
                $request->only('email')
            );
        } catch (\Throwable $e) {
            report($e);

            if ($request->wantsJson()) {
                return response()->json([
                    'status'  => 'warning',
                    'message' => 'Sistem email sedang tidak terhubung, sehingga tautan belum dapat dikirimkan. Silakan coba beberapa saat lagi atau hubungi administrator.',
                ], 500);
            }

            return back()
                ->with('status', self::SAFE_RESPONSE)
                ->with('warning', 'Sistem email sedang tidak terhubung, sehingga tautan belum terkirim. Silakan hubungi administrator.');
        }

        if ($request->wantsJson()) {
            return response()->json([
                'status'  => 'success',
                'message' => "Tautan reset kata sandi telah dikirim ke {$request->email}! Silakan periksa kotak masuk atau spam email Anda.",
            ]);
        }

        // 3. Respons identik mau email terdaftar atau tidak (anti user enumeration)
        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', self::SAFE_RESPONSE)
            : back()->with('status', self::SAFE_RESPONSE);
    }
}