<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ReceptionistShift;
use Symfony\Component\HttpFoundation\Response;

class EnsureReceptionistShiftOpen
{
    /**
     * Memastikan staf Resepsionis telah membuka shift kasir dengan kas awal
     * sebelum dapat mengakses fitur operasional (POS, Check-in, Loker, Buku Tamu, dll).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $user->isReceptionist() && $user->tenant_id) {
            $exemptRoutes = [
                'receptionist.shifts',
                'receptionist.shifts.start',
                'receptionist.shifts.close-logout',
                'logout',
                'account.settings',
            ];

            foreach ($exemptRoutes as $route) {
                if ($request->routeIs($route)) {
                    return $next($request);
                }
            }

            $hasOpenShift = ReceptionistShift::where('tenant_id', $user->tenant_id)
                ->where('user_id', $user->id)
                ->where('status', 'open')
                ->exists();

            if (!$hasOpenShift) {
                return redirect()->route('receptionist.shifts')
                    ->with('warning', 'Akses operasional dibatasi. Anda wajib membuka shift kasir dengan memasukkan nominal uang kas awal terlebih dahulu.');
            }
        }

        return $next($request);
    }
}
