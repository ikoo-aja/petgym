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
        $apex = config('app.tenant_apex');
        $cases = [
            "http://fitlife.{$apex}/" => 200,
            "http://fitlife.{$apex}/login" => 200,
            "http://fitlife.{$apex}/register" => 200,
            "http://tidakada.{$apex}/" => 404,
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

        $apex = config('app.tenant_apex');
        $response = $this->post("http://fitlife.{$apex}/login", [
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

    public function test_admin_dapat_menutup_dan_membuka_landing_page_serta_mode_maintenance(): void
    {
        $admin = \App\Models\User::where('email', 'admin@fitlife.com')->first();
        $this->actingAs($admin);

        $apex = config('app.tenant_apex');
        $publicUrl = "http://fitlife.{$apex}/";

        // 1. Admin menutup landing page (Mode Maintenance)
        $response = $this->post(route('admin.landing.update'), [
            'is_active'           => '0',
            'maintenance_title'   => 'Perbaikan Fasilitas Gym',
            'maintenance_message' => 'Kami sedang melakukan pemeliharaan server dan fasilitas gym.',
            'hero_title'          => 'FitLife Studio',
        ]);

        $response->assertRedirect(route('admin.landing.edit'));
        $this->assertDatabaseHas('tenant_landing_settings', [
            'tenant_id'         => $admin->tenant_id,
            'is_active'         => false,
            'maintenance_title' => 'Perbaikan Fasilitas Gym',
        ]);

        // 2. Publik umum melihat halaman maintenance
        Auth::logout();
        $this->flushSession();

        $guestResponse = $this->get($publicUrl);
        $guestResponse->assertStatus(200);
        $guestResponse->assertSee('Mode Pemeliharaan');
        $guestResponse->assertSee('Perbaikan Fasilitas Gym');
        $guestResponse->assertSee('Kami sedang melakukan pemeliharaan server dan fasilitas gym.');
        $guestResponse->assertSee('Portal Login Member');

        // 3. Admin dapat melakukan pratinjau halaman penuh dengan ?preview=1
        $this->actingAs($admin);
        $previewResponse = $this->get("{$publicUrl}?preview=1");
        $previewResponse->assertStatus(200);
        $previewResponse->assertSee('Mode Pratinjau Admin');

        // 4. Admin mengaktifkan kembali landing page
        $reopenResponse = $this->post(route('admin.landing.update'), [
            'is_active'  => '1',
            'hero_title' => 'FitLife Studio Kembali Online',
        ]);
        $reopenResponse->assertRedirect(route('admin.landing.edit'));

        $this->assertDatabaseHas('tenant_landing_settings', [
            'tenant_id' => $admin->tenant_id,
            'is_active' => true,
        ]);

        // 5. Publik dapat melihat kembali landing page normal
        Auth::logout();
        $this->flushSession();

        $normalResponse = $this->get($publicUrl);
        $normalResponse->assertStatus(200);
        $normalResponse->assertDontSee('Mode Pemeliharaan');
        $normalResponse->assertSee('FitLife Studio Kembali Online');
    }
}
