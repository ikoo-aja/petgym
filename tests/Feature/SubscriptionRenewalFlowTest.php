<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\Tenant;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SubscriptionRenewalFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_admin_can_submit_renewal_and_superadmin_can_approve(): void
    {
        Storage::fake('public');

        // 1. Setup Plan, Superadmin & Tenant Admin
        $plan = Plan::create([
            'name'        => 'Paket Pro',
            'price'       => 1200000,
            'period'      => 'bulan',
            'max_members' => 500,
            'features'    => ['members', 'pos', 'classes'],
            'status'      => 'active',
        ]);

        $superadmin = User::factory()->create([
            'email'    => 'superadmin@test.com',
            'password' => 'password123',
            'role'     => 'superadmin',
        ]);

        $initialExpiresAt = now()->addDays(10);
        $tenant = Tenant::create([
            'name'        => 'FitLife Studio',
            'subdomain'   => 'fitlife.workout.id',
            'slug'        => 'fitlife',
            'owner_name'  => 'Budi Pratama',
            'owner_email' => 'budi@fitlife.com',
            'plan_id'     => $plan->id,
            'plan_name'   => 'Paket Pro',
            'status'      => 'active',
            'joined_at'   => now()->subDays(20),
            'expires_at'  => $initialExpiresAt,
            'features'    => ['members', 'pos', 'classes'],
        ]);

        $adminUser = User::factory()->create([
            'email'     => 'admin@fitlife.com',
            'password'  => 'password123',
            'role'      => 'admin',
            'tenant_id' => $tenant->id,
        ]);

        // 2. Admin membuka halaman subscription
        $this->actingAs($adminUser)
            ->get(route('admin.subscription.index'))
            ->assertOk()
            ->assertSee('FitLife Studio')
            ->assertSee('Paket Pro');

        // 3. Admin mengajukan perpanjangan 3 bulan (3 x 1.200.000 = 3.600.000)
        $fakeFile = UploadedFile::fake()->image('transfer_receipt.jpg');
        $payResponse = $this->actingAs($adminUser)
            ->post(route('admin.subscription.pay'), [
                'plan_id'         => $plan->id,
                'duration_months' => 3,
                'payment_method'  => 'Bank Transfer BCA',
                'proof_file'      => $fakeFile,
                'notes'           => 'Pembayaran perpanjangan Q4 dari BCA Budi',
            ]);

        $payResponse->assertRedirect(route('admin.subscription.index'));
        $payResponse->assertSessionHas('success');

        // 4. Verifikasi Invoice tercatat di database dengan status pending
        $this->assertDatabaseHas('invoices', [
            'tenant_id'       => $tenant->id,
            'amount'          => 3600000,
            'duration_months' => 3,
            'plan_name'       => 'Paket Pro',
            'payment_method'  => 'Bank Transfer BCA',
            'status'          => 'pending',
        ]);

        $invoice = Invoice::where('tenant_id', $tenant->id)->latest('id')->first();
        $this->assertNotNull($invoice);
        $this->assertNotNull($invoice->proof_url);

        // 5. Superadmin membuka halaman billing dan melihat pengajuan perpanjangan
        $this->actingAs($superadmin)
            ->get(route('superadmin.billing'))
            ->assertOk()
            ->assertSee($invoice->invoice_number)
            ->assertSee('FitLife Studio')
            ->assertSee('Rp 3.600.000')
            ->assertSee('3 Bulan');

        // 6. Superadmin menyetujui (Approve) perpanjangan sewa
        $approveResponse = $this->actingAs($superadmin)
            ->post(route('superadmin.billing.approve', $invoice->id));

        $approveResponse->assertRedirect();
        $approveResponse->assertSessionHas('success');

        // 7. Verifikasi invoice berubah jadi 'paid' dan masa aktif tenant bertambah +90 hari (3 bulan)
        $invoice->refresh();
        $this->assertSame('paid', $invoice->status);
        $this->assertNotNull($invoice->paid_at);

        $tenant->refresh();
        $this->assertSame('active', $tenant->status);
        // Tanggal expires_at harus bertambah ~90 hari dari initial (10 hari + 90 hari = ~100 hari dari now)
        $this->assertGreaterThan(85, $tenant->expires_in_days);

        // 8. Superadmin Dashboard menghitung pendapatan dari pembayaran perpanjangan ini
        $this->actingAs($superadmin)
            ->get(route('superadmin.dashboard'))
            ->assertOk()
            ->assertSee('Rp 3.600.000');
    }
}
