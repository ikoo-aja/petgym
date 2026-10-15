<?php

namespace App\Http\Middleware;

use App\Models\SystemSetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureGlobalMaintenanceMode
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Cek apakah mode pemeliharaan global aktif
        try {
            $isMaintenance = SystemSetting::get('maintenance_active', '0');
        } catch (\Throwable $e) {
            return $next($request);
        }

        if ($isMaintenance !== '1' && $isMaintenance !== 'true' && $isMaintenance !== true) {
            return $next($request);
        }

        // 2. Superadmin bypass: Pengguna dengan role superadmin dapat mengakses seluruh sistem
        $user = Auth::user();
        if ($user && method_exists($user, 'isSuperadmin') && $user->isSuperadmin()) {
            return $next($request);
        }

        // 3. Rute yang dikecualikan (Bypass Routes)
        if (
            $request->is('superadmin*') ||
            $request->is('login') ||
            $request->is('logout') ||
            $request->is('up')
        ) {
            return $next($request);
        }

        // 4. Respon JSON jika request meminta format JSON
        $title = SystemSetting::get('maintenance_title', 'Platform Sedang Dalam Pemeliharaan Terjadwal');
        $message = SystemSetting::get('maintenance_message', 'Kami sedang melakukan peningkatan sistem dan infrastruktur server PetGym untuk performa yang lebih optimal.');
        $estimatedEnd = SystemSetting::get('maintenance_estimated_end', 'Estimasi selesai: 02:00 WIB');
        $supportWhatsapp = SystemSetting::get('support_whatsapp', '081234567890');
        $supportEmail = SystemSetting::get('support_email', 'support@petgym.website');

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'maintenance',
                'title' => $title,
                'message' => $message,
                'estimated_end' => $estimatedEnd,
            ], 503);
        }

        // 5. Tampilkan halaman pemeliharaan global
        return response()->view('maintenance', compact(
            'title',
            'message',
            'estimatedEnd',
            'supportWhatsapp',
            'supportEmail'
        ), 503);
    }
}
