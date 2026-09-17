<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\Tenant;
use App\Models\TenantRegistration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantOnboardingFlowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test alur pendaftaran prospek -> Superadmin approval -> Kelola Penyewa -> Auto Suspend.
     */
    public function test_full_lead_registration_approval_and_tenant_management_flow(): void
    {
        // 1. Setup Plan & Superadmin
        $plan = Plan::create([
            'name'        => 'Paket Pro',
            'price'       => 1200000,
            'period'      => 'bulan',
            'max_members' => 500,
            'features'    => ['members', 'pos', 'classes', 'lockers'],
            'status'      => 'active',
        ]);

        $superadmin = User::factory()->create([
            'email'    => 'superadmin@test.com',
            'password' => 'password123',
            'role'     => 'superadmin',
        ]);

        // 2. Calon penyewa mendaftar dari landing page
        $prospectEmail = 'calon.gym' . uniqid() . '@gmail.com';
        $response = $this->post(route('register.saas'), [
            'name'      => 'Budi Pratama',
            'email'     => $prospectEmail,
            'phone'     => '081234567890',
            'plan_name' => 'Paket Pro',
            'notes'     => 'Butuh 10 loker dan integrasi kasir.',
        ]);

        $response->assertRedirect(route('register'));
        $this->assertDatabaseHas('tenant_registrations', [
            'email'     => $prospectEmail,
            'status'    => 'pending',
            'plan_name' => 'Paket Pro',
            'notes'     => 'Butuh 10 loker dan integrasi kasir.',
        ]);

        $registration = TenantRegistration::where('email', $prospectEmail)->first();
        $this->assertNotNull($registration);
        $this->assertStringContainsString('6281234567890', $registration->formatted_phone);
        $this->assertStringContainsString('wa.me', $registration->whatsapp_url);

        // 3. Superadmin melihat daftar pendaftaran
        $this->actingAs($superadmin)
            ->get(route('superadmin.registrations'))
            ->assertOk()
            ->assertSee('Budi Pratama')
            ->assertSee('Paket Pro')
            ->assertSee('Butuh 10 loker dan integrasi kasir.')
            ->assertSee('Hubungi')
            ->assertSee('Buat Akun');

        // 4. Superadmin menyetujui & membuat akun login untuk penyewa
        $approveResponse = $this->actingAs($superadmin)
            ->post(route('superadmin.registrations.approve', $registration->id), [
                'email'    => $prospectEmail,
                'password' => 'gym1234',
                'plan_id'  => $plan->id,
                'features' => ['members', 'pos', 'classes', 'lockers'],
                'notes'    => 'Sudah bayar via Transfer BCA.',
            ]);

        $approveResponse->assertRedirect(route('superadmin.registrations'));

        // Pastikan akun user admin terbuat dan terhubung ke tenant
        $adminUser = User::where('email', $prospectEmail)->first();
        $this->assertNotNull($adminUser);
        $this->assertSame('admin', $adminUser->role);
        $this->assertNotNull($adminUser->tenant_id);

        $tenant = Tenant::find($adminUser->tenant_id);
        $this->assertNotNull($tenant);
        $this->assertSame('active', $tenant->status);
        $this->assertSame('Budi Pratama', $tenant->owner_name);
        $this->assertSame('Paket Pro', $tenant->plan_name);
        $this->assertGreaterThan(0, $tenant->expires_in_days);

        // 5. Data langsung masuk ke Kelola Penyewa Superadmin
        $this->actingAs($superadmin)
            ->get(route('superadmin.tenants'))
            ->assertOk()
            ->assertSee($tenant->name)
            ->assertSee($tenant->subdomain)
            ->assertSee('Paket Pro');

        // 6. Admin Gym login dan bisa mengakses dashboard
        $this->actingAs($adminUser)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee($tenant->name);

        // 7. Verifikasi Auto Suspend jika sisa hari <= 0
        $tenant->update(['expires_at' => now()->subDay()]);
        $tenant->refresh();
        $this->assertSame('suspended', $tenant->status);
        $this->assertSame(0, $tenant->expires_in_days);
    }
}
