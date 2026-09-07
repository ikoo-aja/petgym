<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private function makeTenant(): Tenant
    {
        return Tenant::create([
            'name'        => 'Role Test Gym ' . uniqid(),
            'subdomain'   => 'role-' . uniqid() . '.workout.id',
            'owner_name'  => 'Test Owner',
            'owner_email' => 'owner@role-test.com',
            'status'      => 'active',
        ]);
    }

    private function makeUser(string $role, Tenant $tenant): User
    {
        return User::factory()->create([
            'email'     => $role . '-' . uniqid() . '@role-test.com',
            'password'  => 'password123',
            'role'      => $role,
            'tenant_id' => $tenant->id,
        ]);
    }

    /**
     * Member TIDAK boleh membuka halaman admin (URL langsung ditolak 403).
     */
    public function test_member_cannot_access_admin_route(): void
    {
        $tenant = $this->makeTenant();
        $member = $this->makeUser('member', $tenant);

        $this->actingAs($member)
            ->get('/admin/members')
            ->assertForbidden();
    }

    /**
     * Member tetap bisa membuka halamannya sendiri.
     */
    public function test_member_can_access_own_route(): void
    {
        $tenant = $this->makeTenant();
        $member = $this->makeUser('member', $tenant);

        $this->actingAs($member)
            ->get(route('member.dashboard'))
            ->assertOk();
    }

    /**
     * Resepsionis sah memakai halaman kasir (POS) yang ada di grup admin.
     */
    public function test_receptionist_can_access_shared_admin_route(): void
    {
        $tenant = $this->makeTenant();
        $receptionist = $this->makeUser('receptionist', $tenant);

        $this->actingAs($receptionist)
            ->get(route('admin.pos.index'))
            ->assertOk();
    }

    /**
     * Trainer sah melihat Data Member (menu bersama staf).
     */
    public function test_trainer_can_access_shared_member_data(): void
    {
        $tenant = $this->makeTenant();
        $trainer = $this->makeUser('trainer', $tenant);

        $this->actingAs($trainer)
            ->get(route('admin.members.index'))
            ->assertOk();
    }

    /**
     * Admin TIDAK boleh membuka halaman manager.
     */
    public function test_admin_cannot_access_manager_route(): void
    {
        $tenant = $this->makeTenant();
        $admin = $this->makeUser('admin', $tenant);

        $this->actingAs($admin)
            ->get(route('manager.dashboard'))
            ->assertForbidden();
    }

    /**
     * Member TIDAK boleh membuka halaman owner.
     */
    public function test_member_cannot_access_owner_route(): void
    {
        $tenant = $this->makeTenant();
        $member = $this->makeUser('member', $tenant);

        $this->actingAs($member)
            ->get(route('owner.dashboard'))
            ->assertForbidden();
    }

    /**
     * Owner (read-only) TIDAK boleh membuka halaman admin — penegakan di URL,
     * bukan cuma tombol yang disembunyikan.
     */
    public function test_owner_cannot_access_admin_route(): void
    {
        $tenant = $this->makeTenant();
        $owner = $this->makeUser('owner', $tenant);

        $this->actingAs($owner)
            ->get(route('admin.members.index'))
            ->assertForbidden();
    }

    /**
     * Admin TIDAK boleh membuka halaman superadmin.
     */
    public function test_admin_cannot_access_superadmin_route(): void
    {
        $tenant = $this->makeTenant();
        $admin = $this->makeUser('admin', $tenant);

        $this->actingAs($admin)
            ->get(route('superadmin.dashboard'))
            ->assertForbidden();
    }

    /**
     * Superadmin boleh membuka halamannya sendiri.
     */
    public function test_superadmin_can_access_own_route(): void
    {
        $tenant = $this->makeTenant();
        $superadmin = $this->makeUser('superadmin', $tenant);

        $this->actingAs($superadmin)
            ->get(route('superadmin.dashboard'))
            ->assertOk();
    }

    /**
     * Guest yang belum login diarahkan ke halaman login, bukan 403.
     */
    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin/members')
            ->assertRedirect(route('login'));
    }
}