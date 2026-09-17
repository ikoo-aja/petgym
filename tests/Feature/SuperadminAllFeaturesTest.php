<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\Tenant;
use App\Models\User;
 use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperadminAllFeaturesTest extends TestCase
{
    use RefreshDatabase;

    protected User $superadmin;
    protected Plan $plan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superadmin = User::factory()->create([
            'email'             => 'superadmin@petgym.test',
            'password'          => 'password123',
            'role'              => 'superadmin',
            'email_verified_at' => now(),
        ]);

        $this->plan = Plan::create([
            'name'        => 'Paket Pro',
            'price'       => 1200000,
            'period'      => 'monthly',
            'max_members' => 500,
            'features'    => ['members', 'pos', 'classes', 'lockers'],
            'status'      => 'active',
        ]);
    }

    public function test_superadmin_can_access_dashboard(): void
    {
        $this->actingAs($this->superadmin)
            ->get(route('superadmin.dashboard'))
            ->assertStatus(200)
            ->assertViewIs('superadmin.dashboard');
    }

    public function test_superadmin_can_access_all_pages(): void
    {
        $this->actingAs($this->superadmin)
            ->get(route('superadmin.registrations'))
            ->assertStatus(200)
            ->assertViewIs('superadmin.registrations');

        $this->actingAs($this->superadmin)
            ->get(route('superadmin.tenants'))
            ->assertStatus(200)
            ->assertViewIs('superadmin.tenants');

        $this->actingAs($this->superadmin)
            ->get(route('superadmin.plans'))
            ->assertStatus(200)
            ->assertViewIs('superadmin.plans');

        $this->actingAs($this->superadmin)
            ->get(route('superadmin.billing'))
            ->assertStatus(200)
            ->assertViewIs('superadmin.billing');

        $this->actingAs($this->superadmin)
            ->get(route('superadmin.announcements'))
            ->assertStatus(200)
            ->assertViewIs('superadmin.announcements');

        $this->actingAs($this->superadmin)
            ->get(route('superadmin.logs'))
            ->assertStatus(200)
            ->assertViewIs('superadmin.logs');

        $this->actingAs($this->superadmin)
            ->get(route('superadmin.settings'))
            ->assertStatus(200)
            ->assertViewIs('superadmin.settings');

        $this->actingAs($this->superadmin)
            ->get(route('superadmin.profile'))
            ->assertStatus(200)
            ->assertViewIs('superadmin.profile');
    }

    public function test_superadmin_can_manage_plans(): void
    {
        $this->actingAs($this->superadmin)
            ->post(route('superadmin.plans.store'), [
                'name'        => 'Paket Custom',
                'price'       => 2000000,
                'period'      => 'monthly',
                'max_members' => 1000,
                'features'    => ['members', 'pos'],
            ])
            ->assertRedirect(route('superadmin.plans'))
            ->assertSessionHas('success');

        $createdPlan = Plan::where('name', 'Paket Custom')->firstOrFail();

        $this->actingAs($this->superadmin)
            ->put(route('superadmin.plans.update', $createdPlan->id), [
                'name'        => 'Paket Custom Updated',
                'price'       => 2500000,
                'period'      => 'monthly',
                'max_members' => 1200,
                'features'    => ['members', 'pos', 'classes'],
            ])
            ->assertRedirect(route('superadmin.plans'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('plans', ['name' => 'Paket Custom Updated', 'price' => 2500000]);

        $this->actingAs($this->superadmin)
            ->post(route('superadmin.plans.toggle-status', $createdPlan->id))
            ->assertRedirect(route('superadmin.plans'));

        $this->assertDatabaseHas('plans', ['id' => $createdPlan->id, 'status' => 'inactive']);

        $this->actingAs($this->superadmin)
            ->delete(route('superadmin.plans.destroy', $createdPlan->id))
            ->assertRedirect(route('superadmin.plans'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('plans', ['id' => $createdPlan->id]);
    }

    public function test_superadmin_can_manage_tenant_actions(): void
    {
        $tenant = Tenant::create([
            'name'        => 'Mega Gym Test',
            'subdomain'   => 'megagym.workout.id',
            'slug'        => 'megagym',
            'owner_name'  => 'Mega Owner',
            'owner_email' => 'owner@megagym.test',
            'plan_id'     => $this->plan->id,
            'plan_name'   => $this->plan->name,
            'status'      => 'active',
            'joined_at'   => now(),
            'expires_at'  => now()->addDays(30),
            'features'    => ['members', 'pos'],
        ]);

        $this->actingAs($this->superadmin)
            ->post(route('superadmin.tenants.features', $tenant->id), [
                'plan_id'  => $this->plan->id,
                'features' => ['members', 'pos', 'classes'],
            ])
            ->assertRedirect(route('superadmin.tenants'))
            ->assertSessionHas('success');

        $this->actingAs($this->superadmin)
            ->post(route('superadmin.tenants.toggle-status', $tenant->id))
            ->assertRedirect(route('superadmin.tenants'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('tenants', ['id' => $tenant->id, 'status' => 'suspended']);

        $this->actingAs($this->superadmin)
            ->delete(route('superadmin.tenants.destroy', $tenant->id))
            ->assertRedirect(route('superadmin.tenants'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('tenants', ['id' => $tenant->id]);
    }

    public function test_superadmin_can_clear_cache(): void
    {
        $this->actingAs($this->superadmin)
            ->post(route('superadmin.clear-cache'))
            ->assertRedirect()
            ->assertSessionHas('success');
    }
}
