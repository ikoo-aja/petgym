<?php

namespace Tests\Audit;

use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Tests\TestCase;

/**
 * AUDIT AKSI SUPERADMIN TERHADAP TENANT
 *
 * 1. Subdomain divalidasi pada nilai mentah, padahal yang disimpan sudah diformat
 *    (ditambah `.workout.id`) dan kolomnya UNIQUE.
 * 2. Menghapus tenant membuat stafnya kehilangan tenant (FK nullOnDelete),
 *    tetapi mereka masih bisa login.
 */
class SuperadminTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    private function superadmin(): User
    {
        return User::where('email', 'superadmin@petgym.com')->firstOrFail();
    }

    public function test_subdomain_duplikat_menghasilkan_validasi_bukan_500(): void
    {
        // Tenant bawaan: subdomain "fitlife.workout.id", slug "fitlife"
        $response = $this->actingAs($this->superadmin())
            ->from('/')
            ->post(route('superadmin.tenants.store'), [
                'name' => 'Gym Duplikat',
                'subdomain' => 'fitlife', // akan diformat jadi fitlife.workout.id
                'owner_email' => 'duplikat@uji.test',
            ]);

        $this->assertNotSame(
            500,
            $response->getStatusCode(),
            "Subdomain duplikat lolos validasi lalu memicu error server (HTTP {$response->getStatusCode()})."
        );
    }

    public function test_staf_tenant_terhapus_tidak_menyebabkan_error_500(): void
    {
        $tenant = Tenant::where('slug', 'fitlife')->firstOrFail();
        $manager = User::where('email', 'manager@fitlife.com')->firstOrFail();

        $this->actingAs($this->superadmin())->delete(route('superadmin.tenants.destroy', $tenant->id));

        $manager->refresh();

        $response = $this->actingAs($manager)->get(route('manager.members.index'));

        $this->assertNotSame(
            500,
            $response->getStatusCode(),
            "Staf dari tenant yang sudah dihapus mengalami error server (HTTP {$response->getStatusCode()})."
        );
    }
}
