<?php

namespace Tests\Audit;

use App\Models\Member;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Tests\TestCase;

/**
 * DEMO BUKTI - Hirarki Role Bocor
 *
 * Login sebagai TRAINER, lalu tembak endpoint pendaftaran member milik Manager.
 * Test ini mencetak bukti ke layar (member yang benar-benar tercipta).
 *
 * Jalankan:
 *   vendor/bin/phpunit -c phpunit.audit.xml --filter=DemoBuktiTest --testdox
 */
class DemoBuktiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_demo_trainer_bisa_mendaftarkan_member(): void
    {
        $tenant = Tenant::where('slug', 'fitlife')->firstOrFail();
        $trainer = User::where('email', 'alex@fitlife.com')->firstOrFail();

        $before = Member::where('tenant_id', $tenant->id)->count();

        $response = $this->actingAs($trainer)->post(route('manager.members.store'), [
            'name' => 'Member Dibuat Trainer',
            'phone' => '081200000001',
            'email' => 'member.trainer@uji.test',
            'gender' => 'Laki-laki',
            'package_duration' => '1',
            'payment_method' => 'cash',
            'cash_paid' => 500000,
        ]);

        $after = Member::where('tenant_id', $tenant->id)->count();
        $created = Member::where('tenant_id', $tenant->id)
            ->where('name', 'Member Dibuat Trainer')
            ->first();

        fwrite(STDERR, "\n================= BUKTI =================\n");
        fwrite(STDERR, "Login sebagai : {$trainer->name} (role: {$trainer->role})\n");
        fwrite(STDERR, "Request       : POST " . route('manager.members.store') . "\n");
        fwrite(STDERR, "HTTP status   : " . $response->getStatusCode()
            . "  -> redirect: " . ($response->headers->get('Location') ?? '-') . "\n");
        fwrite(STDERR, "Jumlah member : {$before} -> {$after}\n");
        fwrite(STDERR, "Member dibuat : " . ($created
            ? "#{$created->id} {$created->name} (PIN {$created->access_code})"
            : 'TIDAK ADA') . "\n");
        fwrite(STDERR, "=========================================\n");

        $this->assertNull(
            $created,
            "BOCOR: Trainer berhasil membuat member baru (seharusnya ditolak 403)."
        );
    }
}
