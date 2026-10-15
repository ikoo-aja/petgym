<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * SEC-13: Security Headers Middleware
 *
 * Menerapkan pertahanan defensif standar OWASP:
 * - X-Frame-Options: SAMEORIGIN (cegah Clickjacking)
 * - X-Content-Type-Options: nosniff (cegah MIME-type sniffing)
 * - Referrer-Policy: strict-origin-when-cross-origin (lindungi kebocoran token/URL)
 * - Permissions-Policy: batasi akses fitur hardware berisiko
 */
class AddSecurityHeaders
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
