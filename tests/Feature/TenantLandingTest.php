<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantLandingTest extends TestCase
{
    use RefreshDatabase;

    private function createTenant(string $planName = 'Paket Pro'): Tenant
    {
        return Tenant::create([
            'name'        => 'FitLife Test Studio',
            'subdomain'   => 'fitlife-test-' . uniqid() . '.workout.id',
            'slug'        => 'fitlife-test-' . uniqid(),
            'owner_name'  => 'Test Owner',
            'owner_email' => 'owner-test@fitlife.com',
            'plan_name'   => $planName,
            'status'      => 'active',
        ]);
    }

    /**
     * Halaman landing tenant bisa diakses lewat subdomain slug.
     */
    public function test_tenant_landing_page_accessible_via_subdomain(): void
    {
        $tenant = $this->createTenant();
        $settings = $tenant->landingSettings();
        $settings->update(['hero_title' => 'Judul Khas FitLife']);

        $this->get('http://' . $tenant->slug . '.localhost/')
            ->assertOk()
            ->assertSee('Judul Khas FitLife')
            ->assertSee($tenant->name);
    }

    /**
     * Subdomain yang tidak dikenal -> 404.
     */
    public function test_unknown_subdomain_returns_404(): void
    {
        $this->get('http://tidakada.localhost/')->assertNotFound();
    }

    /**
     * Admin bisa menyimpan kustomisasi landing page (hero, warna, fitur).
     */
    public function test_admin_can_update_landing_settings(): void
    {
        $tenant = $this->createTenant('Paket Pro');
        $admin = User::factory()->create([
            'email'     => 'admin@fitlife.com',
            'password'  => 'password123',
            'role'      => 'admin',
            'tenant_id' => $tenant->id,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.landing.update'), [
                'hero_title'       => 'Judul Baru',
                'primary_color'    => '#0ea5e9',
                'secondary_color'  => '#1e293b',
                'features_title'   => ['Kelas Zumba', 'Personal Trainer', ''],
                'features_desc'    => ['Kelas pagi setiap Senin', 'Sesi 1-on-1', ''],
                'sections'         => ['hero', 'about', 'features', 'contact', 'footer'],
            ])
            ->assertRedirect(route('admin.landing.edit'));

        $settings = $tenant->landingSettings()->fresh();

        $this->assertEquals('Judul Baru', $settings->hero_title);
        $this->assertEquals('#0ea5e9', $settings->primary_color);
        $this->assertCount(2, $settings->features);
    }

    /**
     * Tenant paket Basic: field warna/fitur/statistik DIABAIKAN di server.
     */
    public function test_basic_plan_cannot_use_pro_features(): void
    {
        $tenant = $this->createTenant('Paket Basic');
        $admin = User::factory()->create([
            'email'     => 'admin@fitlife.com',
            'password'  => 'password123',
            'role'      => 'admin',
            'tenant_id' => $tenant->id,
        ]);

        // Coba kirim field Pro lewat request (seolah-oleh diakali dari form)
        $this->actingAs($admin)
            ->post(route('admin.landing.update'), [
                'hero_title'      => 'Teks Basic Boleh',
                'primary_color'   => '#ff0000',
                'features_title'  => ['Fitur Curang'],
                'features_desc'   => ['Tidak boleh tersimpan'],
                'stats_value'     => ['999'],
                'stats_label'     => ['Hack'],
                'sections'        => ['hero'],
            ])
            ->assertRedirect(route('admin.landing.edit'));

        $settings = $tenant->landingSettings()->fresh();

        // Teks tersimpan (semua paket), tapi field Pro diabaikan:
        // warna tetap default (#f43f5e dari awal, BUKAN #ff0000 yang dicoba dikirim)
        $this->assertEquals('Teks Basic Boleh', $settings->hero_title);
        $this->assertEquals('#f43f5e', $settings->primary_color);
        $this->assertNull($settings->features);
        $this->assertNull($settings->stats);
    }

    /**
     * Halaman edit landing hanya untuk role admin.
     */
    public function test_only_admin_can_access_landing_editor(): void
    {
        $tenant = $this->createTenant();
        $receptionist = User::factory()->create([
            'email'     => 'resepsionis@fitlife.com',
            'password'  => 'password123',
            'role'      => 'receptionist',
            'tenant_id' => $tenant->id,
        ]);

        $this->actingAs($receptionist)
            ->get(route('admin.landing.edit'))
            ->assertRedirect(route('admin.dashboard'));
    }
}
