<?php

namespace Tests\Feature;

use App\Models\ReceptionistShift;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReceptionistLogoutTest extends TestCase
{
    use RefreshDatabase;

    private function makeTenant(): Tenant
    {
        return Tenant::create([
            'name'        => 'Test Gym ' . uniqid(),
            'subdomain'   => 'gym-' . uniqid() . '.workout.id',
            'owner_name'  => 'Test Owner',
            'owner_email' => 'owner@test-gym.com',
            'status'      => 'active',
        ]);
    }

    private function makeReceptionist(Tenant $tenant): User
    {
        return User::factory()->create([
            'email'     => 'resepsionis-' . uniqid() . '@test-gym.com',
            'password'  => 'password123',
            'role'      => 'receptionist',
            'tenant_id' => $tenant->id,
        ]);
    }

    /**
     * Resepsionis dengan shift masih terbuka TIDAK boleh logout.
     */
    public function test_receptionist_with_open_shift_cannot_logout(): void
    {
        $tenant = $this->makeTenant();
        $user   = $this->makeReceptionist($tenant);

        ReceptionistShift::create([
            'tenant_id'  => $tenant->id,
            'user_id'    => $user->id,
            'start_cash' => 100000,
            'opened_at'  => now(),
            'status'     => 'open',
        ]);

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('receptionist.shifts'))
            ->assertSessionHas('error');

        // Tetap login — logout diblokir
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Resepsionis tanpa shift terbuka boleh logout normal.
     */
    public function test_receptionist_without_open_shift_can_logout(): void
    {
        $tenant = $this->makeTenant();
        $user   = $this->makeReceptionist($tenant);

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect('/login')
            ->assertSessionHas('success');

        $this->assertGuest();
    }

    /**
     * Resepsionis yang sudah menutup shift-nya boleh logout.
     */
    public function test_receptionist_can_logout_after_closing_shift(): void
    {
        $tenant = $this->makeTenant();
        $user   = $this->makeReceptionist($tenant);

        $shift = ReceptionistShift::create([
            'tenant_id'  => $tenant->id,
            'user_id'    => $user->id,
            'start_cash' => 100000,
            'opened_at'  => now(),
            'status'     => 'open',
        ]);

        $shift->update([
            'end_cash'  => 150000,
            'closed_at' => now(),
            'status'    => 'closed',
        ]);

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect('/login');

        $this->assertGuest();
    }

    /**
     * Role lain (misal admin) tetap bisa logout tanpa terpengaruh guard shift.
     */
    public function test_admin_can_logout_without_shift_guard(): void
    {
        $tenant = $this->makeTenant();

        $admin = User::factory()->create([
            'email'     => 'admin-' . uniqid() . '@test-gym.com',
            'password'  => 'password123',
            'role'      => 'admin',
            'tenant_id' => $tenant->id,
        ]);

        $this->actingAs($admin)
            ->post(route('logout'))
            ->assertRedirect('/login');

        $this->assertGuest();
    }
}