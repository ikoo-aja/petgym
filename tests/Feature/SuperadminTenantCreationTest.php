<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperadminTenantCreationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Tenant baru dari panel superadmin: Owner & Admin otomatis dibuat dan
     * LANGSUNG terverifikasi — supaya admin tenant baru tidak terkunci dari
     * semua dashboard (email verifikasi tidak pernah dikirim oleh alur ini).
     */
    public function test_new_tenant_owner_and_admin_are_verified(): void
    {
        $superadmin = User::factory()->create([
            'email'    => 'superadmin-' . uniqid() . '@test.com',
            'password' => 'password123',
            'role'     => 'superadmin',
        ]);

        $name  = 'Gym Baru ' . uniqid();
        $slug  = 'gymbaru' . substr(uniqid(), -5);
        $ownerEmail = 'owner-' . $slug . '@gmail.com';

        $this->actingAs($superadmin)
            ->post(route('superadmin.tenants.store'), [
                'name'         => $name,
                'subdomain'    => $slug,
                'owner_email'  => $ownerEmail,
            ])
            ->assertRedirect(route('superadmin.tenants'));

        // Tenant tersimpan
        $tenant = Tenant::where('subdomain', $slug . '.workout.id')->first();
        $this->assertNotNull($tenant);

        // Owner terverifikasi
        $owner = User::where('email', $ownerEmail)->first();
        $this->assertNotNull($owner);
        $this->assertTrue($owner->hasVerifiedEmail());
        $this->assertSame($tenant->id, $owner->tenant_id);

        // Admin otomatis terverifikasi — bukan terkunci dari dashboard
        $admin = User::where('email', "admin.{$slug}@workout.id")->first();
        $this->assertNotNull($admin);
        $this->assertTrue($admin->hasVerifiedEmail());
        $this->assertSame($tenant->id, $admin->tenant_id);
    }
}