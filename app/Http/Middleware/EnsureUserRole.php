<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    /**
     * Hanya role yang terdaftar (mis. `role:admin,manager`) yang boleh lewat.
     * Role lain yang memaksa membuka URL ini ditolak 403 — bukan sekadar
     * menyembunyikan menu di tampilan.
     *
     * @param  array<int, string>  $roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = Auth::user();

        if (!$user || !in_array($user->role, $roles, true)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        // Otomatis Cek Masa Aktif & Status Suspend Tenant (Khusus Non-Superadmin)
        if (!$user->isSuperadmin() && $user->tenant) {
            $tenant = $user->tenant;

            // Jika tanggal expires_at sudah lewat -> otomatis ubah status ke suspended
            if ($tenant->expires_at && $tenant->expires_at->isPast() && $tenant->status !== 'suspended') {
                $tenant->update(['status' => 'suspended']);
            }

            // Jika status tenant suspended -> blokir akses ke fitur operasional
            if ($tenant->status === 'suspended') {
                // Kecuali halaman perpanjangan subscription & logout
                if (!$request->routeIs('admin.subscription.*') && !$request->routeIs('logout')) {
                    if ($user->isAdmin() || $user->isOwner()) {
                        return redirect()->route('admin.subscription.index')
                            ->with('error', "Masa aktif langganan website gym '{$tenant->name}' telah BERAKHIR / DITANGGUHKAN. Silakan lakukan pembayaran perpanjangan untuk mengaktifkan kembali akses dasbor.");
                    }

                    abort(403, "Akses website gym '{$tenant->name}' sedang DITANGGUHKAN (Suspended / Expired). Silakan hubungi pengelola gym Anda.");
                }
            }
        }

        return $next($request);
    }
}