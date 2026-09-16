<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminStaffTrainerPhoneTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): User
    {
        $tenant = Tenant::create([
            'name'        => 'Staff Phone Gym ' . uniqid(),
            'subdomain'   => 'phone-' . uniqid() . '.workout.id',
            'owner_name'  => 'Test Owner',
            'owner_email' => 'owner@phone-test.com',
            'status'      => 'active',
        ]);

        return User::factory()->create([
            'email'     => 'admin-' . uniqid() . '@phone-test.com',
            'password'  => 'password123',
            'role'      => 'admin',
            'tenant_id' => $tenant->id,
        ]);
    }

    /**
     * Staff baru ber-role Personal Trainer: nomor telepon tersimpan ke profil
     * Trainer sehingga tampil di halaman Kelas & Trainer (semua role dengan akses).
     */
    public function test_adding_trainer_staff_saves_phone_to_trainer_profile(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)
            ->post(route('admin.staff.store'), [
                'name'     => 'Coach Baru',
                'email'    => 'coach-' . uniqid() . '@fitlife.com',
                'password' => '1234',
                'role'     => 'trainer',
                'phone'    => '081234567890',
            ])
            ->assertRedirect(route('admin.staff.index'));

        $this->assertDatabaseHas('users', ['role' => 'trainer']);
        $this->assertDatabaseHas('trainers', ['phone' => '081234567890']);
    }

    /**
     * Add staf gagal validasi (misal email dobel): dikembalikan ke halaman
     * daftar staf dengan error — dan halamannya MENAMPILKAN error di modal,
     * bukan gagal diam-diam tanpa konfirmasi.
     */
    public function test_add_staff_with_duplicate_email_shows_visible_error(): void
    {
        $admin = $this->makeAdmin();

        // Email yang sudah dipakai akun lain
        User::factory()->create([
            'email'     => 'dobel@fitlife.com',
            'password'  => 'password123',
            'role'      => 'manager',
            'tenant_id' => $admin->tenant_id,
        ]);

        $this->actingAs($admin)
            ->from(route('admin.staff.index'))
            ->post(route('admin.staff.store'), [
                'name'     => 'Staf Dobel',
                'email'    => 'dobel@fitlife.com',
                'password' => '1234',
                'role'     => 'receptionist',
            ])
            ->assertRedirect(route('admin.staff.index'))
            ->assertSessionHasErrors('email');

        // Halaman daftar staf menampilkan pesan error di modal (bukan senyap)
        $this->get(route('admin.staff.index'))
            ->assertOk()
            ->assertSee('Data tidak tersimpan')
            ->assertSee('sudah terdaftar');
    }

    /**
     * Nomor telepon tidak disimpan untuk role selain Personal Trainer
     * (field hanya relevan untuk trainer).
     */
    public function test_phone_is_ignored_for_non_trainer_staff(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)
            ->post(route('admin.staff.store'), [
                'name'     => 'Resepsionis Baru',
                'email'    => 'resepsionis-' . uniqid() . '@fitlife.com',
                'password' => '1234',
                'role'     => 'receptionist',
                'phone'    => '089999999999',
            ])
            ->assertRedirect(route('admin.staff.index'));

        $this->assertDatabaseHas('users', ['role' => 'receptionist']);
        $this->assertDatabaseMissing('trainers', ['phone' => '089999999999']);
    }
}