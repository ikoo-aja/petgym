<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminStaffAndMemberRbacTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected User $admin;
    protected User $manager;
    protected User $receptionist;
    protected User $trainer;
    protected User $owner;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::create([
            'name'        => 'Fitlife Gym',
            'subdomain'   => 'fitlife.workout.id',
            'slug'        => 'fitlife',
            'owner_name'  => 'Owner Fitlife',
            'owner_email' => 'owner@fitlife.test',
            'status'      => 'active',
            'joined_at'   => now(),
            'expires_at'  => now()->addDays(30),
        ]);

        $this->admin = User::factory()->create([
            'tenant_id'        => $this->tenant->id,
            'role'             => 'admin',
            'email_verified_at' => now(),
        ]);

        $this->manager = User::factory()->create([
            'tenant_id'        => $this->tenant->id,
            'role'             => 'manager',
            'email_verified_at' => now(),
        ]);

        $this->receptionist = User::factory()->create([
            'tenant_id'        => $this->tenant->id,
            'role'             => 'receptionist',
            'email_verified_at' => now(),
        ]);

        $this->trainer = User::factory()->create([
            'tenant_id'        => $this->tenant->id,
            'role'             => 'trainer',
            'email_verified_at' => now(),
        ]);

        $this->owner = User::factory()->create([
            'tenant_id'        => $this->tenant->id,
            'role'             => 'owner',
            'email_verified_at' => now(),
        ]);

        Member::create([
            'tenant_id'   => $this->tenant->id,
            'name'        => 'Member Test',
            'access_code' => '123456',
            'gender'      => 'Laki-laki',
            'status'      => 'active',
            'expired_at'  => now()->addMonth(),
        ]);
    }


    public function test_admin_cannot_view_members_but_manager_can(): void
    {
        // Admin diblokir dari halaman data member
        $this->actingAs($this->admin)
            ->get(route('admin.members.index'))
            ->assertRedirect(route('admin.dashboard'))
            ->assertSessionHas('error');

        // Manager dapat akses halaman data member
        $this->actingAs($this->manager)
            ->get(route('admin.members.index'))
            ->assertStatus(200)
            ->assertSee('Member Test');
    }

    public function test_admin_only_sees_manager_and_owner_staff(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.staff.index'))
            ->assertStatus(200)
            ->assertSee($this->manager->name)
            ->assertSee($this->owner->name)
            ->assertDontSee($this->receptionist->name)
            ->assertDontSee($this->trainer->name);
    }

    public function test_manager_only_sees_receptionist_and_trainer_staff(): void
    {
        $this->actingAs($this->manager)
            ->get(route('admin.staff.index'))
            ->assertStatus(200)
            ->assertSee($this->receptionist->name)
            ->assertSee($this->trainer->name)
            ->assertDontSee($this->owner->name);
    }

    public function test_admin_can_only_store_manager_or_owner(): void
    {
        // Admin bisa buat Manager
        $this->actingAs($this->admin)
            ->post(route('admin.staff.store'), [
                'name'     => 'Manager Baru',
                'email'    => 'manager.baru@fitlife.test',
                'password' => '1234',
                'role'     => 'manager',
            ])
            ->assertRedirect(route('admin.staff.index'));

        $this->assertDatabaseHas('users', ['email' => 'manager.baru@fitlife.test', 'role' => 'manager']);

        // Admin gagal buat Receptionist (ditolak)
        $this->actingAs($this->admin)
            ->post(route('admin.staff.store'), [
                'name'     => 'Receptionist Illegal',
                'email'    => 'illegal.receptionist@fitlife.test',
                'password' => '1234',
                'role'     => 'receptionist',
            ])
            ->assertSessionHas('error');

        $this->assertDatabaseMissing('users', ['email' => 'illegal.receptionist@fitlife.test']);
    }

    public function test_manager_can_only_store_operational_staff(): void
    {
        // Manager bisa buat Trainer
        $this->actingAs($this->manager)
            ->post(route('admin.staff.store'), [
                'name'     => 'Trainer Baru',
                'email'    => 'trainer.baru@fitlife.test',
                'password' => '1234',
                'role'     => 'trainer',
            ])
            ->assertRedirect(route('admin.staff.index'));

        $this->assertDatabaseHas('users', ['email' => 'trainer.baru@fitlife.test', 'role' => 'trainer']);

        // Manager gagal buat Owner (ditolak)
        $this->actingAs($this->manager)
            ->post(route('admin.staff.store'), [
                'name'     => 'Owner Illegal',
                'email'    => 'illegal.owner@fitlife.test',
                'password' => '1234',
                'role'     => 'owner',
            ])
            ->assertSessionHas('error');

        $this->assertDatabaseMissing('users', ['email' => 'illegal.owner@fitlife.test']);
    }
}
