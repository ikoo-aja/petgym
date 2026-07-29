<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Plan;
use App\Models\Tenant;
use App\Models\Invoice;
use App\Models\Announcement;
use App\Models\SystemLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. User Superadmin
        $admin = User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('1234'),
                'role' => 'superadmin',
            ]
        );

        // 2. Plans (Paket Sewa)
        $basicPlan = Plan::updateOrCreate(
            ['name' => 'Paket Basic'],
            [
                'price' => 500000,
                'max_members' => 150,
                'features' => ['POS', 'Class'],
                'status' => 'active',
            ]
        );

        $proPlan = Plan::updateOrCreate(
            ['name' => 'Paket Pro'],
            [
                'price' => 1200000,
                'max_members' => 500,
                'features' => ['POS', 'Class', 'Trainer', 'Inventory'],
                'status' => 'active',
            ]
        );

        $enterprisePlan = Plan::updateOrCreate(
            ['name' => 'Paket Enterprise'],
            [
                'price' => 2500000,
                'max_members' => null,
                'features' => ['POS', 'Class', 'Trainer', 'Inventory', 'Mobile', 'Analytics'],
                'status' => 'active',
            ]
        );

        // 3. Tenants (Penyewa Gym)
        $tenantsData = [
            [
                'name' => 'FitLife Studio',
                'subdomain' => 'fitlife.workout.id',
                'owner_name' => 'Budi Pratama',
                'owner_email' => 'budi@fitlife.com',
                'plan_id' => $proPlan->id,
                'plan_name' => 'Paket Pro',
                'status' => 'active',
                'joined_at' => Carbon::now()->subDays(60),
                'expires_at' => Carbon::now()->addDays(24),
                'features' => ['POS', 'Class', 'Trainer', 'Inventory'],
            ],
            [
                'name' => 'Powerhouse Gym',
                'subdomain' => 'powerhouse.workout.id',
                'owner_name' => 'Siti Rahma',
                'owner_email' => 'siti@powerhouse.com',
                'plan_id' => $enterprisePlan->id,
                'plan_name' => 'Paket Enterprise',
                'status' => 'active',
                'joined_at' => Carbon::now()->subDays(120),
                'expires_at' => Carbon::now()->addDays(112),
                'features' => ['POS', 'Class', 'Trainer', 'Inventory', 'Mobile', 'Analytics'],
            ],
            [
                'name' => 'Barbell Club',
                'subdomain' => 'barbell.workout.id',
                'owner_name' => 'Andi Wijaya',
                'owner_email' => 'andi@barbell.com',
                'plan_id' => $basicPlan->id,
                'plan_name' => 'Paket Basic',
                'status' => 'suspended',
                'joined_at' => Carbon::now()->subDays(90),
                'expires_at' => Carbon::now()->subDays(5),
                'features' => ['POS', 'Class'],
            ],
            [
                'name' => 'Iron Palace',
                'subdomain' => 'ironpalace.workout.id',
                'owner_name' => 'Hendra Setiawan',
                'owner_email' => 'hendra@ironpalace.com',
                'plan_id' => $proPlan->id,
                'plan_name' => 'Paket Pro',
                'status' => 'active',
                'joined_at' => Carbon::now()->subDays(45),
                'expires_at' => Carbon::now()->addDays(140),
                'features' => ['POS', 'Class', 'Trainer', 'Inventory'],
            ],
            [
                'name' => 'Gold Standard Gym',
                'subdomain' => 'goldstandard.workout.id',
                'owner_name' => 'Dewi Lestari',
                'owner_email' => 'dewi@goldstandard.com',
                'plan_id' => $enterprisePlan->id,
                'plan_name' => 'Paket Enterprise',
                'status' => 'active',
                'joined_at' => Carbon::now()->subDays(30),
                'expires_at' => Carbon::now()->addDays(5),
                'features' => ['POS', 'Class', 'Trainer', 'Inventory', 'Mobile', 'Analytics'],
            ],
            [
                'name' => 'Apex Fitness',
                'subdomain' => 'apexfit.workout.id',
                'owner_name' => 'Rian Jamil',
                'owner_email' => 'rian@apexfit.com',
                'plan_id' => $basicPlan->id,
                'plan_name' => 'Paket Basic',
                'status' => 'active',
                'joined_at' => Carbon::now()->subDays(15),
                'expires_at' => Carbon::now()->addDays(8),
                'features' => ['POS', 'Class'],
            ],
            [
                'name' => 'Champion Gym',
                'subdomain' => 'champion.workout.id',
                'owner_name' => 'Eko Prasetyo',
                'owner_email' => 'eko@champion.com',
                'plan_id' => $proPlan->id,
                'plan_name' => 'Paket Pro',
                'status' => 'active',
                'joined_at' => Carbon::now()->subDays(75),
                'expires_at' => Carbon::now()->addDays(54),
                'features' => ['POS', 'Class', 'Trainer', 'Inventory'],
            ],
            [
                'name' => 'Olympus Fitness',
                'subdomain' => 'olympus.workout.id',
                'owner_name' => 'Maria Utami',
                'owner_email' => 'maria@olympus.com',
                'plan_id' => $basicPlan->id,
                'plan_name' => 'Paket Basic',
                'status' => 'suspended',
                'joined_at' => '2026-04-10',
                'expires_at' => Carbon::now()->subDays(10),
                'features' => ['POS', 'Class'],
            ],
            [
                'name' => 'Titan Gym',
                'subdomain' => 'titan.workout.id',
                'owner_name' => 'Faisal Rahman',
                'owner_email' => 'faisal@titan.com',
                'plan_id' => $enterprisePlan->id,
                'plan_name' => 'Paket Enterprise',
                'status' => 'active',
                'joined_at' => Carbon::now()->subDays(40),
                'expires_at' => Carbon::now()->addDays(98),
                'features' => ['POS', 'Class', 'Trainer', 'Inventory', 'Mobile', 'Analytics'],
            ],
            [
                'name' => 'Muscle Factory',
                'subdomain' => 'musclefac.workout.id',
                'owner_name' => 'Kevin Wijaya',
                'owner_email' => 'kevin@musclefac.com',
                'plan_id' => $proPlan->id,
                'plan_name' => 'Paket Pro',
                'status' => 'active',
                'joined_at' => Carbon::now()->subDays(10),
                'expires_at' => Carbon::now()->addDays(320),
                'features' => ['POS', 'Class', 'Trainer', 'Inventory'],
            ]
        ];

        $tenantModels = [];
        foreach ($tenantsData as $tData) {
            $tenantModels[$tData['name']] = Tenant::updateOrCreate(
                ['subdomain' => $tData['subdomain']],
                $tData
            );
        }

        // 4. Invoices (Keuangan & Tagihan)
        if (isset($tenantModels['FitLife Studio'])) {
            Invoice::updateOrCreate(
                ['invoice_number' => '#INV-2026-001'],
                [
                    'tenant_id' => $tenantModels['FitLife Studio']->id,
                    'amount' => 1200000,
                    'due_date' => Carbon::now()->addDays(2),
                    'status' => 'pending',
                    'proof_url' => 'https://raw.githubusercontent.com/Antigravity-AI/mock-assets/main/receipt-mockup.png',
                ]
            );
        }

        if (isset($tenantModels['Powerhouse Gym'])) {
            Invoice::updateOrCreate(
                ['invoice_number' => '#INV-2026-002'],
                [
                    'tenant_id' => $tenantModels['Powerhouse Gym']->id,
                    'amount' => 2500000,
                    'due_date' => Carbon::now()->subDays(12),
                    'status' => 'paid',
                    'proof_url' => 'https://raw.githubusercontent.com/Antigravity-AI/mock-assets/main/receipt-mockup.png',
                    'paid_at' => Carbon::now()->subDays(11),
                ]
            );
        }

        if (isset($tenantModels['Apex Fitness'])) {
            Invoice::updateOrCreate(
                ['invoice_number' => '#INV-2026-003'],
                [
                    'tenant_id' => $tenantModels['Apex Fitness']->id,
                    'amount' => 500000,
                    'due_date' => Carbon::now()->addDays(9),
                    'status' => 'pending',
                    'proof_url' => 'https://raw.githubusercontent.com/Antigravity-AI/mock-assets/main/receipt-mockup.png',
                ]
            );
        }

        if (isset($tenantModels['Titan Gym'])) {
            Invoice::updateOrCreate(
                ['invoice_number' => '#INV-2026-004'],
                [
                    'tenant_id' => $tenantModels['Titan Gym']->id,
                    'amount' => 2500000,
                    'due_date' => Carbon::now()->addDays(14),
                    'status' => 'paid',
                    'proof_url' => 'https://raw.githubusercontent.com/Antigravity-AI/mock-assets/main/receipt-mockup.png',
                    'paid_at' => Carbon::now()->subDays(1),
                ]
            );
        }

        // 5. Announcements (Pengumuman Sistem)
        Announcement::updateOrCreate(
            ['title' => 'Maintenance Sistem Bulanan'],
            [
                'message' => '<p>Halo para tenant, kami akan melakukan maintenance sistem rutin pada hari <strong>Sabtu, 25 Juli 2026</strong> pukul 00:00 - 02:00 WIB. Selama waktu tersebut, akses ke dashboard mungkin akan terganggu sejenak. Terima kasih.</p>',
                'status' => 'Active',
                'user_id' => $admin->id,
            ]
        );

        Announcement::updateOrCreate(
            ['title' => 'Update Fitur Laporan POS Keuangan'],
            [
                'message' => '<p>Kami telah memperbarui fitur Kasir/POS untuk semua tenant di paket Pro & Enterprise. Silakan cek menu Laporan POS di dashboard Anda untuk melihat visualisasi grafik baru.</p>',
                'status' => 'Active',
                'user_id' => $admin->id,
            ]
        );

        Announcement::updateOrCreate(
            ['title' => 'Promo Diskon Upgrade Paket Pro'],
            [
                'message' => '<p>Dapatkan potongan harga sebesar 20% untuk upgrade ke Paket Pro khusus bulan ini dengan kode kupon <strong>UPGRADEMAX</strong>. Hubungi tim support kami segera!</p>',
                'status' => 'Recalled',
                'user_id' => $admin->id,
            ]
        );

        // 6. System Audit Logs
        SystemLog::updateOrCreate(
            ['action' => 'Pembayaran Konfirmasi'],
            [
                'user_id' => $admin->id,
                'description' => 'Superadmin mengkonfirmasi pembayaran #INV-2026-002 (Powerhouse Gym).',
                'ip_address' => '127.0.0.1',
            ]
        );

        SystemLog::updateOrCreate(
            ['action' => 'Suspend Account'],
            [
                'user_id' => $admin->id,
                'description' => 'Superadmin melakukan suspend pada akun Barbell Club.',
                'ip_address' => '127.0.0.1',
            ]
        );

        SystemLog::updateOrCreate(
            ['action' => 'Tenant Registration'],
            [
                'user_id' => null,
                'description' => 'System menambahkan tenant baru: FitLife Studio.',
                'ip_address' => '127.0.0.1',
            ]
        );
    }
}
