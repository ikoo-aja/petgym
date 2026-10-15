<?php

namespace Tests\Audit;

use App\Models\Announcement;
use App\Models\Tenant;
use App\Models\TenantLandingSetting;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

/**
 * AUDIT KEAMANAN: XSS & UPLOAD
 *
 * SEC-01: Isi pengumuman disimpan/ditampilkan sebagai plain-text (di-escape),
 *         sehingga tag HTML/script tidak dieksekusi.
 * SEC-02: Upload logo menolak berkas SVG (vektor XSS berbasis SVG).
 */
class SecurityXssTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    private function user(string $email): User
    {
        return User::where('email', $email)->firstOrFail();
    }

    private function superadmin(): User
    {
        return User::where('role', 'superadmin')->firstOrFail();
    }

    private function tenant(string $slug = 'fitlife'): Tenant
    {
        return Tenant::where('slug', $slug)->firstOrFail();
    }

    // ======================================================
    // SEC-01: Stored XSS pada Pengumuman Superadmin
    // ======================================================

    public function test_pengumuman_ditampilkan_tereksekusi_sebagai_teks_bukan_html(): void
    {
        $sa = $this->superadmin();
        $payload = '<script>alert("xss")</script><b>tebal</b>';

        $this->actingAs($sa)->post(route('superadmin.announcements.store'), [
            'title'   => 'Pengumuman Uji XSS',
            'message' => $payload,
        ]);

        // Dashboard Admin Gym merender pengumuman
        $admin = $this->user('admin@fitlife.com');
        $res = $this->actingAs($admin)->get(route('admin.dashboard'));

        $res->assertStatus(200);
        // Tag script mentah TIDAK boleh muncul (harus ter-escape)
        $res->assertDontSee('<script>alert("xss")</script>', false);
        // Versi ter-escape harus muncul (aman)
        $res->assertSee('&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;', false);
    }

    // ======================================================
    // SEC-02: Upload Logo SVG ditolak (vektor XSS)
    // ======================================================

    public function test_upload_logo_svg_ditolak(): void
    {
        $admin = $this->user('admin@fitlife.com');

        $svg = UploadedFile::fake()->createWithContent(
            'logo.svg',
            '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>'
        );

        $res = $this->actingAs($admin)->post(route('admin.landing.update'), [
            'logo' => $svg,
            'name' => 'FitLife Uji',
        ]);

        $res->assertSessionHasErrors('logo');
    }

    public function test_upload_logo_png_diterima(): void
    {
        $admin = $this->user('admin@fitlife.com');

        $png = UploadedFile::fake()->image('logo.png', 200, 200);

        $res = $this->actingAs($admin)->post(route('admin.landing.update'), [
            'logo' => $png,
            'name' => 'FitLife Uji PNG',
        ]);

        $res->assertSessionHasNoErrors();
    }
}
