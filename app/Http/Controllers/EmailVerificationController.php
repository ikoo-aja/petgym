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
    /**
     * Menampilkan halaman pengingat bahwa email harus diverifikasi dulu
     * (diakses user yang baru login tapi emailnya belum terverifikasi).
     */
    public function notice()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $tenant = $user ? $user->tenant : null;

        if ($user && ($user->role === 'member' || $tenant)) {
            $settings = $tenant ? $tenant->landingSettings() : null;
            return view('landing.member-verify', compact('user', 'tenant', 'settings'));
        }

        return view('verify-email');
    }

    /**
     * Memverifikasi email lewat link bertanda tangan (signed URL) yang dikirim via email.
     */
    public function verify(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        if (!hash_equals(sha1($user->getEmailForVerification()), (string) $hash)) {
            abort(403, 'Link verifikasi tidak valid atau telah kedaluwarsa.');
        }

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        $tenant = $user->tenant;
        $tenantLoginUrl = $tenant ? $tenant->publicLandingUrl() . '/login' : route('member.login');

        // Jika user adalah member gym
        if ($user->role === 'member') {
            if (Auth::check() && Auth::id() === $user->id) {
                return redirect()->route('member.dashboard')
                    ->with('success', "Email berhasil diverifikasi! Selamat datang di Portal Keanggotaan " . ($tenant ? $tenant->name : 'Gym') . ".");
            }

            return redirect($tenantLoginUrl)
                ->with('success', "Email berhasil diverifikasi! Silakan masuk ke akun member Anda.");
        }

        // Jika user sudah login → redirect ke dashboard sendiri
        if (Auth::check() && Auth::id() === $user->id) {
            if ($user->must_change_password) {
                return redirect()->route('password.change')
                    ->with('success', 'Email berhasil diverifikasi. Silakan ubah kata sandi bawaan Anda.');
            }
            return redirect()->route($user->dashboardRoute())
                ->with('success', 'Email berhasil diverifikasi!');
        }

        // Jika admin login klik tautan verifikasi staf → kembali ke dashboard admin
        if (Auth::check()) {
            return redirect()->route(Auth::user()->dashboardRoute())
                ->with('success', "Email akun {$user->name} berhasil diverifikasi.");
        }

        // Belum login → ke halaman login
        return redirect()->route('login')
            ->with('success', 'Email berhasil diverifikasi. Silakan masuk.');
    }

    /**
     * Mengirim ulang tautan verifikasi (khusus user yang sudah login, dengan throttle).
     */
    public function resend(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            if ($user->role === 'member') {
                return redirect()->route('member.dashboard')->with('success', 'Email Anda sudah terverifikasi.');
            }
            return redirect()->route('login')->with('success', 'Email Anda sudah terverifikasi.');
        }

        $user->sendEmailVerificationNotification();

        return back()->with('success', 'Tautan verifikasi baru telah dikirim ke email Anda.');
    }
}