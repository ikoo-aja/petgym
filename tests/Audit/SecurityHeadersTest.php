<?php

namespace Tests\Audit;

use Tests\TestCase;

/**
 * AUDIT KEAMANAN: SECURITY HEADERS & COOKIE
 *
 * SEC-13: Security headers (X-Frame-Options, X-Content-Type-Options, Referrer-Policy, Permissions-Policy)
 * SEC-12: HTTP Only Cookie & SameSite
 */
class SecurityHeadersTest extends TestCase
{
    public function test_aplikasi_mengirimkan_security_headers_standar(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
    }

    public function test_halaman_login_dilindungi_security_headers(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
    }
}
