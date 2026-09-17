<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantSetup
{
    /**
     * Handle an incoming request.
     *
     * Jika akun bertipe Admin tetapi belum memiliki website/tenant,
     * paksa redirect ke halaman onboarding setup website.
     * Sebaliknya jika sudah punya website, cegah masuk kembali ke setup website.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $user->isAdmin()) {
            $hasWebsite = !empty($user->tenant_id) && $user->tenant;

            // Jika belum punya website dan mencoba mengakses selain rute onboarding / logout / change-password
            if (!$hasWebsite && !$request->routeIs('tenant.onboarding*') && !$request->routeIs('logout') && !$request->routeIs('password.*')) {
                return redirect()->route('tenant.onboarding')
                    ->with('info', 'Selamat datang! Silakan daftarkan nama dan subdomain website gym Anda terlebih dahulu untuk mulai menggunakan sistem.');
            }

            // Jika sudah punya website tetapi mencoba membuka halaman onboarding lagi
            if ($hasWebsite && $request->routeIs('tenant.onboarding*')) {
                return redirect()->route('admin.dashboard');
            }
        }

        return $next($request);
    }
}
