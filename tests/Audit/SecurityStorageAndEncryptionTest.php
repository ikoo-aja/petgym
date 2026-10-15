<?php

namespace Tests\Audit;

use App\Models\Invoice;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * AUDIT KEAMANAN: STORAGE PRIVAT & ENKRIPSI REKENING
 *
 * SEC-03: Bukti transfer disimpan di disk privat dan hanya bisa diunduh oleh Superadmin.
 *         Role lain (Admin, Member, Tamu) ditolak 403 / dialihkan.
 * SEC-21: Nomor rekening disimpan terenkripsi di database (encryption at rest).
 * SEC-09: Perubahan rekening oleh Owner tercatat di StaffLog.
 */
class SecurityStorageAndEncryptionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        Storage::fake('local');
    }

    private function user(string $email): User
    {
        return User::where('email', $email)->firstOrFail();
    }

    private function superadmin(): User
    {
        return User::where('role', 'superadmin')->firstOrFail();
    }

    private function tenant(): Tenant
    {
        return Tenant::where('slug', 'fitlife')->firstOrFail();
    }

    public function test_bukti_transfer_disimpan_di_disk_privat(): void
    {
        $admin = $this->user('admin@fitlife.com');
        $plan = \App\Models\Plan::firstOrFail();

        // Bersihkan invoice pending bawaan seeder agar tidak kena guard hasPending
        Invoice::where('tenant_id', $admin->tenant_id)->delete();

        $proof = UploadedFile::fake()->image('bukti_transfer.jpg', 600, 800);
        $res = $this->actingAs($admin)->post(route('admin.subscription.pay'), [
            'plan_id'         => $plan->id,
            'duration_months' => 3,
            'payment_method'  => 'Bank Transfer BCA',
            'proof_file'      => $proof,
        ]);

        $res->assertSessionHasNoErrors();
        $res->assertRedirect(route('admin.subscription.index'));

        $invoice = Invoice::where('tenant_id', $admin->tenant_id)->latest('id')->firstOrFail();
        $this->assertNotNull($invoice->proof_url);
        // Pastikan path berawalan 'proofs/' bukan 'uploads/proofs/'
        $this->assertStringStartsWith('proofs/', $invoice->proof_url);
        // Bukti tersimpan di disk privat
        Storage::disk('local')->assertExists($invoice->proof_url);
    }

    public function test_hanya_superadmin_yang_boleh_mengunduh_bukti_transfer(): void
    {
        $sa = $this->superadmin();
        $admin = $this->user('admin@fitlife.com');
        $member = $this->user('member@fitlife.com');

        // Buat file bukti di storage privat
        Storage::disk('local')->put('proofs/test_proof.jpg', 'fake-image-content');

        $invoice = Invoice::create([
            'invoice_number'  => '#INV-TEST-001',
            'tenant_id'       => $admin->tenant_id,
            'amount'          => 500000,
            'plan_name'       => 'Paket Basic',
            'duration_months' => 1,
            'payment_method'  => 'Bank Transfer BCA',
            'due_date'        => now()->addDays(3),
            'status'          => 'pending',
            'proof_url'       => 'proofs/test_proof.jpg',
        ]);

        // 1. Superadmin boleh unduh
        $resSa = $this->actingAs($sa)->get(route('superadmin.billing.proof', $invoice->id));
        $resSa->assertStatus(200);

        // 2. Admin gym ditolak (403 via middleware role:superadmin)
        $resAdmin = $this->actingAs($admin)->get(route('superadmin.billing.proof', $invoice->id));
        $resAdmin->assertStatus(403);

        // 3. Member gym ditolak (403)
        $resMember = $this->actingAs($member)->get(route('superadmin.billing.proof', $invoice->id));
        $resMember->assertStatus(403);

        // 4. Tamu diarahkan ke login
        $this->app['auth']->logout();
        $resGuest = $this->get(route('superadmin.billing.proof', $invoice->id));
        $resGuest->assertRedirect('/login');
    }

    // ======================================================
    // SEC-21: Nomor rekening di-encrypt at rest
    // ======================================================

    public function test_nomor_rekening_tenant_disimpan_terenkripsi_di_database(): void
    {
        $tenant = $this->tenant();
        $plainNumber = '883099887766';

        $tenant->bank_account_number = $plainNumber;
        $tenant->save();

        // 1. Raw di database harus berupa ciphertext (bukan plain)
        $raw = DB::table('tenants')->where('id', $tenant->id)->value('bank_account_number');
        $this->assertNotSame($plainNumber, $raw);
        $this->assertNotEmpty($raw);

        // 2. Model Eloquent otomatis mendekripsi saat dibaca
        $this->assertSame($plainNumber, $tenant->fresh()->bank_account_number);
    }

    // ======================================================
    // SEC-09: Perubahan rekening oleh Owner tercatat di audit log
    // ======================================================

    public function test_perubahan_rekening_oleh_owner_tercatat_di_audit_log(): void
    {
        $owner = $this->user('owner@fitlife.com');

        $res = $this->actingAs($owner)->post(route('owner.settings.payment'), [
            'bank_name'           => 'Bank BCA Syariah',
            'bank_account_number' => '5544332211',
            'bank_account_holder' => 'FitLife Official Owner',
        ]);

        $res->assertSessionHasNoErrors();
        $res->assertRedirect(route('owner.settings'));

        // Cek log tercatat
        $this->assertDatabaseHas('staff_logs', [
            'tenant_id' => $owner->tenant_id,
            'user_id'   => $owner->id,
            'action'    => 'Update Rekening Pembayaran',
        ]);
    }
}
