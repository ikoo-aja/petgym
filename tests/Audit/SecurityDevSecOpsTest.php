<?php

namespace Tests\Audit;

use Illuminate\Http\Request;
use Tests\TestCase;

/**
 * AUDIT DEVSECOPS: TRUSTED PROXIES, REAL IP & RATE LIMITING
 *
 * DEP-03: Laravel mempercayai proxy Cloudflare (trustProxies: *),
 *         sehingga IP asli pengunjung dan protokol HTTPS terdeteksi akurat.
 * DEP-06: Endpoint POST /forgot-password memiliki rate limiting (throttle:5,1)
 *         untuk mencegah spamming email & flooding permohonan reset password.
 */
class SecurityDevSecOpsTest extends TestCase
{
    public function test_trusted_proxy_mendeteksi_real_client_ip_dari_cloudflare(): void
    {
        $app = app();
        $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

        $fakeClientIp = '103.21.244.195';

        $req = Request::create('http://petgym.website/login', 'GET', [], [], [], [
            'REMOTE_ADDR'          => '127.0.0.1',
            'HTTP_X_FORWARDED_FOR' => $fakeClientIp,
            'HTTP_X_FORWARDED_PROTO' => 'https',
        ]);

        $kernel->handle($req);

        // IP yang terdeteksi wajib IP asli client, BUKAN 127.0.0.1
        $this->assertSame($fakeClientIp, $req->ip(), 'IP asli pengunjung dari Cloudflare harus terdeteksi');
        $this->assertTrue($req->isSecure(), 'Koneksi HTTPS dari Cloudflare harus terdeteksi sebagai isSecure=true');
    }

    public function test_hsts_header_aktif_saat_koneksi_https_terdeteksi(): void
    {
        $res = $this->withHeaders([
            'X-Forwarded-Proto' => 'https',
            'X-Forwarded-For'   => '103.21.244.2',
        ])->get('/');

        $res->assertStatus(200);
        $res->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
    }

    public function test_forgot_password_dibatasi_rate_limit_throttle(): void
    {
        $email = 'spam-test@example.com';

        // 5 request pertama diizinkan (redirect kembali)
        for ($i = 1; $i <= 5; $i++) {
            $res = $this->post(route('password.email'), ['email' => $email]);
            $this->assertSame(302, $res->status(), "Request ke-{$i} harus diizinkan");
        }

        // Request ke-6 harus ditolak oleh Rate Limiter (HTTP 429 Too Many Requests)
        $res6 = $this->post(route('password.email'), ['email' => $email]);
        $this->assertSame(429, $res6->status(), 'Request ke-6 harus ditolak HTTP 429 Too Many Requests');
    }
}
