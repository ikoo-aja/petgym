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

        // Logout jika ada sesi login aktif agar user wajib login ulang dengan kredensialnya
        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        // Arahkan ke URL login yang tepat sesuai role
        if ($user->role === 'member') {
            $memberLoginUrl = $tenant ? $tenant->publicLandingUrl() . '/login' : route('member.login');
            return redirect($memberLoginUrl)
                ->with('success', 'Email berhasil diverifikasi! Silakan masuk dengan email dan kata sandi Anda.');
        }

        return redirect()->route('login')
            ->with('success', 'Email berhasil diverifikasi! Silakan masuk dengan email dan kata sandi Anda.');
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