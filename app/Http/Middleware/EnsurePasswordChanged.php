<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordChanged
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Hanya enforce untuk user yang sudah login dan wajib ganti password
        if (!$user || !$user->must_change_password) {
            return $next($request);
        }

        $allowedRoutes = [
            'password.change',
            'password.change.update',
            'logout',
            'verification.notice',
            'verification.verify',
            'verification.send',
            'login',
            'password.request',
            'password.email',
            'password.reset',
            'password.update',
        ];

        if ($request->routeIs($allowedRoutes)) {
            return $next($request);
        }

        return redirect()->route('password.change')
            ->with('warning', 'Anda harus mengubah password default sebelum melanjutkan.');
    }
}