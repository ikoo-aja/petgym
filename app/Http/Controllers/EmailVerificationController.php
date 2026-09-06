<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class EmailVerificationController extends Controller
{
    /**
     * Menampilkan halaman pengingat bahwa email harus diverifikasi dulu
     * (diakses user yang baru login tapi emailnya belum terverifikasi).
     */
    public function notice()
    {
        return view('verify-email');
    }

    /**
     * Memverifikasi email lewat link bertanda tangan (signed URL) yang dikirim via email.
     * Route memakai middleware 'signed' -> URL tidak bisa dipalsukan & punya masa berlaku.
     */
    public function verify(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        // Lapisan kedua: pastikan hash cocok dengan email user
        if (!hash_equals(sha1($user->getEmailForVerification()), (string) $hash)) {
            abort(403, 'Link verifikasi tidak valid.');
        }

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return redirect()->route('login')->with('success', 'Email Anda berhasil diverifikasi. Silakan login.');
    }

    /**
     * Mengirim ulang link verifikasi (khusus user yang sudah login, dengan throttle).
     */
    public function resend(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('login')->with('success', 'Email Anda sudah terverifikasi.');
        }

        $user->sendEmailVerificationNotification();

        return back()->with('success', 'Link verifikasi baru telah dikirim ke email Anda.');
    }
}
