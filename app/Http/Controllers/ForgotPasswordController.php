<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
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
        $status = Password::sendResetLink(
            $request->only('email')
        );

        // 3. Respons identik mau email terdaftar atau tidak (anti user enumeration)
        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', 'Jika email tersebut terdaftar di sistem kami, link reset password telah dikirim ke inbox Anda.')
            : back()->with('status', 'Jika email tersebut terdaftar di sistem kami, link reset password telah dikirim ke inbox Anda.');
    }
}