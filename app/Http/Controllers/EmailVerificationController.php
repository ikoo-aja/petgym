<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
     */
    public function verify(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        if (!hash_equals(sha1($user->getEmailForVerification()), (string) $hash)) {
            abort(403, 'Link verifikasi tidak valid.');
        }

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        // Jika user sudah login → redirect ke dashboard sendiri
        if (Auth::check() && Auth::id() === $user->id) {
            if ($user->must_change_password) {
                return redirect()->route('password.change')
                    ->with('success', 'Email berhasil diverifikasi. Silakan ubah password default Anda.');
            }
            return redirect()->route($user->dashboardRoute())
                ->with('success', 'Email berhasil diverifikasi!');
        }

        // Jika admin login klik link verifikasi staf → kembali ke dashboard admin
        if (Auth::check()) {
            return redirect()->route(Auth::user()->dashboardRoute())
                ->with('success', "Email akun {$user->name} berhasil diverifikasi.");
        }

        // Belum login → ke halaman login
        return redirect()->route('login')
            ->with('success', 'Email berhasil diverifikasi. Silakan login.');
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