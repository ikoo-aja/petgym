<?php

namespace Tests\Audit;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

/**
 * AUDIT HALAMAN TENANT & LOGIN MEMBER
 *
 * Menelusuri celah routing pada halaman publik custom-domain (subdomain gym)
 * dan konsistensi alur login member.
 */
class TenantLandingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_halaman_publik_tenant_dapat_dibuka(): void
    {
        $cases = [
            'http://fitlife.localhost/' => 200,
            'http://fitlife.localhost/login' => 200,
            'http://fitlife.localhost/register' => 200,
            'http://tidakada.localhost/' => 404,
        ];

        $failures = [];

        foreach ($cases as $url => $expected) {
            $status = $this->get($url)->getStatusCode();
            if ($status !== $expected) {
                $failures[] = "GET {$url} => HTTP {$status} (harusnya {$expected})";
            }
        }

        $this->assertSame([], $failures, "Halaman publik tenant bermasalah:\n" . implode("\n", $failures));
    }

    public function test_member_bisa_login_lewat_subdomain_gym(): void
    {
        Auth::logout();
        $this->flushSession();

        $response = $this->post('http://fitlife.localhost/login', [
            'email' => 'member@fitlife.com',
            'password' => '1234',
        ]);

        $location = (string) $response->headers->get('Location');

        $this->assertSame(302, $response->getStatusCode(), 'Login member via subdomain tidak redirect.');
        $this->assertStringContainsString(
            '/member/dashboard',
            $location,
            "Member gagal login lewat subdomain gym (redirect: {$location})."
        );
    }

    /**
     * Membuktikan kontradiksi: halaman /member/login ada dan formnya menembak
     * endpoint itu, tetapi LoginController menolak semua member di portal utama.
     * Escape hatch `app()->environment('testing')` dimatikan agar menyerupai produksi.
     */
    public function test_member_login_dari_portal_utama_masuk_jalan_buntu(): void
    {
        $this->app['env'] = 'local';

        Auth::logout();
        $this->flushSession();

        $this->post('/member/login', [
            'email' => 'member@fitlife.com',
            'password' => '1234',
        ]);

        $this->assertFalse(
            Auth::check(),
            'Member berhasil login dari portal utama, padahal LoginController seharusnya menolaknya.'
        );
    }
}
