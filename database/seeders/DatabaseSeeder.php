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

        // Seed Tenant Admin User & Staff
        if (isset($tenantModels['FitLife Studio'])) {
            $fitlife = $tenantModels['FitLife Studio'];

            $adminUser = User::updateOrCreate(
                ['email' => 'admin@fitlife.com'],
                [
                    'name' => 'FitLife Admin',
                    'password' => Hash::make('1234'),
                    'role' => 'admin',
                    'tenant_id' => $fitlife->id,
                ]
            );

            $mgrUser = User::updateOrCreate(
                ['email' => 'manager@fitlife.com'],
                [
                    'name' => 'Joko Manager',
                    'password' => Hash::make('1234'),
                    'role' => 'manager',
                    'tenant_id' => $fitlife->id,
                ]
            );

            \App\Models\Manager::updateOrCreate(
                ['tenant_id' => $fitlife->id, 'user_id' => $mgrUser->id],
                [
                    'name' => 'Joko Manager',
                    'email' => 'manager@fitlife.com',
                    'phone' => '081299887766',
                    'department' => 'Operasional & Keuangan',
                    'status' => 'active'
                ]
            );

            $recUser = User::updateOrCreate(
                ['email' => 'resepsionis@fitlife.com'],
                [
                    'name' => 'Rina Resepsionis',
                    'password' => Hash::make('1234'),
                    'role' => 'receptionist',
                    'tenant_id' => $fitlife->id,
                ]
            );

            \App\Models\Receptionist::updateOrCreate(
                ['tenant_id' => $fitlife->id, 'user_id' => $recUser->id],
                [
                    'name' => 'Rina Resepsionis',
                    'email' => 'resepsionis@fitlife.com',
                    'phone' => '081255443322',
                    'shift' => 'Pagi',
                    'status' => 'active'
                ]
            );

            $trnUser = User::updateOrCreate(
                ['email' => 'alex@fitlife.com'],
                [
                    'name' => 'Coach Alex',
                    'password' => Hash::make('1234'),
                    'role' => 'trainer',
                    'tenant_id' => $fitlife->id,
                ]
            );

            // Members
            $m1 = \App\Models\Member::updateOrCreate(
                ['tenant_id' => $fitlife->id, 'access_code' => '881234'],
                [
                    'name' => 'Budi Santoso',
                    'email' => 'budi@gmail.com',
                    'phone' => '081234567890',
                    'gender' => 'Laki-laki',
                    'status' => 'active',
                    'expired_at' => Carbon::now()->addDays(20),
                ]
            );

            $m2 = \App\Models\Member::updateOrCreate(
                ['tenant_id' => $fitlife->id, 'access_code' => '885678'],
                [
                    'name' => 'Siti Rahayu',
                    'email' => 'siti@gmail.com',
                    'phone' => '089876543210',
                    'gender' => 'Perempuan',
                    'status' => 'active',
                    'expired_at' => Carbon::now()->addDays(4), // Expiring soon alert!
                ]
            );

            $m3 = \App\Models\Member::updateOrCreate(
                ['tenant_id' => $fitlife->id, 'access_code' => '889900'],
                [
                    'name' => 'Agus Pratama',
                    'email' => 'agus@gmail.com',
                    'phone' => '085512344321',
                    'gender' => 'Laki-laki',
                    'status' => 'active',
                    'expired_at' => Carbon::now()->addDays(45),
                ]
            );

            // Products
            \App\Models\Product::updateOrCreate(
                ['tenant_id' => $fitlife->id, 'name' => 'Whey Protein Shake (Single)'],
                ['category' => 'supplement', 'price' => 35000, 'stock' => 50]
            );
            \App\Models\Product::updateOrCreate(
                ['tenant_id' => $fitlife->id, 'name' => 'Air Mineral 600ml'],
                ['category' => 'drink', 'price' => 5000, 'stock' => 120]
            );
            \App\Models\Product::updateOrCreate(
                ['tenant_id' => $fitlife->id, 'name' => 'Handuk Gym FitLife'],
                ['category' => 'merchandise', 'price' => 75000, 'stock' => 25]
            );

            // Trainers
            $tr1 = \App\Models\Trainer::updateOrCreate(
                ['tenant_id' => $fitlife->id, 'name' => 'Coach Alex'],
                [
                    'user_id' => $trnUser->id,
                    'email' => 'alex@fitlife.com',
                    'phone' => '081122334455',
                    'specialization' => 'Pilates & Bodybuilding',
                    'certification' => 'Certified Fitness Trainer (NSCA)',
                    'bio' => 'Pelatih profesional berpengalaman 8+ tahun dalam kebugaran fisik dan pilates.',
                    'status' => 'active'
                ]
            );

            // Classes
            \App\Models\GymClass::updateOrCreate(
                ['tenant_id' => $fitlife->id, 'name' => 'Zumba Morning Energizer'],
                ['trainer_id' => $tr1->id, 'day' => 'Senin', 'start_time' => '08:00', 'end_time' => '09:00', 'room' => 'Studio A', 'duration_minutes' => 60, 'max_capacity' => 25]
            );
            \App\Models\GymClass::updateOrCreate(
                ['tenant_id' => $fitlife->id, 'name' => 'Yoga Flow & Mindfulness'],
                ['trainer_id' => $tr1->id, 'day' => 'Rabu', 'start_time' => '17:00', 'end_time' => '18:00', 'room' => 'Studio B', 'duration_minutes' => 60, 'max_capacity' => 20]
            );

            // Check-Ins today
            \App\Models\CheckIn::updateOrCreate(
                ['tenant_id' => $fitlife->id, 'member_id' => $m1->id, 'access_code' => '881234'],
                ['checked_in_at' => Carbon::now()->subHours(2), 'check_in_method' => 'code']
            );
            \App\Models\CheckIn::updateOrCreate(
                ['tenant_id' => $fitlife->id, 'member_id' => $m3->id, 'access_code' => '889900'],
                ['checked_in_at' => Carbon::now()->subMinutes(35), 'check_in_method' => 'manual']
            );

            // Staff Log
            \App\Models\StaffLog::updateOrCreate(
                ['tenant_id' => $fitlife->id, 'action' => 'Registrasi Member Baru'],
                [
                    'user_id' => $adminUser->id,
                    'description' => 'Admin mendaftarkan member Budi Santoso dengan PIN 881234.',
                    'ip_address' => '127.0.0.1'
                ]
            );

            // Seeding 10 Fitur Manajerial
            // 1. Gym Equipment & Maintenance Logs
            $eq1 = \App\Models\GymEquipment::updateOrCreate(
                ['tenant_id' => $fitlife->id, 'name' => 'Treadmill Premium Lifesport A'],
                [
                    'category' => 'Cardio',
                    'brand' => 'Lifesport',
                    'status' => 'berfungsi',
                    'purchase_date' => Carbon::now()->subMonths(10),
                    'next_service_date' => Carbon::now()->addMonths(2),
                    'notes' => 'Treadmill utama dekat jendela kaca depan'
                ]
            );

            $eq2 = \App\Models\GymEquipment::updateOrCreate(
                ['tenant_id' => $fitlife->id, 'name' => 'Dumbbell Set 10kg - 25kg'],
                [
                    'category' => 'Alat Berat',
                    'brand' => 'Kettler',
                    'status' => 'perlu_servis',
                    'purchase_date' => Carbon::now()->subYear(),
                    'next_service_date' => Carbon::now()->subWeek(),
                    'notes' => 'Rak dumbbell nomor 2, beberapa karet pelindung mulai longgar'
                ]
            );

            \App\Models\EquipmentMaintenanceLog::updateOrCreate(
                ['tenant_id' => $fitlife->id, 'gym_equipment_id' => $eq1->id, 'action' => 'Kalibrasi & Pelumasan Motor'],
                [
                    'description' => 'Servis berkala bulanan dan pemberian pelumas pada belt treadmill',
                    'cost' => 350000,
                    'serviced_at' => Carbon::now()->subMonths(1),
                    'next_service_date' => Carbon::now()->addMonths(2),
                ]
            );

            // 2. Staff Shifts & Leaves
            \App\Models\StaffShift::updateOrCreate(
                ['tenant_id' => $fitlife->id, 'user_id' => $recUser->id, 'shift_date' => Carbon::today()],
                [
                    'shift_name' => 'Pagi',
                    'start_time' => '08:00:00',
                    'end_time' => '16:00:00',
                    'notes' => 'Shift kasir utama pagi'
                ]
            );

            \App\Models\LeaveRequest::updateOrCreate(
                ['tenant_id' => $fitlife->id, 'user_id' => $recUser->id, 'reason' => 'Keperluan keluarga mendesak ke luar kota'],
                [
                    'start_date' => Carbon::tomorrow(),
                    'end_date' => Carbon::tomorrow()->addDay(),
                    'status' => 'pending'
                ]
            );

            // 4. Promo Codes
            \App\Models\PromoCode::updateOrCreate(
                ['tenant_id' => $fitlife->id, 'code' => 'FITAGUSTUS'],
                [
                    'description' => 'Diskon promo kemerdekaan 15% untuk perpanjangan membership',
                    'discount_type' => 'percentage',
                    'discount_value' => 15,
                    'min_purchase' => 150000,
                    'max_uses' => 200,
                    'used_count' => 12,
                    'valid_from' => Carbon::now()->subDays(5),
                    'valid_until' => Carbon::now()->addDays(25),
                    'is_active' => true
                ]
            );

            // 8. Complaints
            \App\Models\Complaint::updateOrCreate(
                ['tenant_id' => $fitlife->id, 'member_id' => $m1->id, 'title' => 'Loker Kamar Mandi Pria Rusak'],
                [
                    'reported_by' => $recUser->id,
                    'description' => 'Loker nomor 12 di kamar mandi pria tidak bisa dikunci dengan kunci magnetik.',
                    'status' => 'open'
                ]
            );

            // 10. Database Vendor & Pihak Ketiga
            \App\Models\Vendor::updateOrCreate(
                ['tenant_id' => $fitlife->id, 'name' => 'CV Sejahtera Abadi (Teknisi Alat)'],
                [
                    'phone' => '081234567890',
                    'email' => 'sejahtera.abadi@gmail.com',
                    'category' => 'Teknisi Alat Gym',
                    'address' => 'Jl. Industri No. 45, Bandung',
                    'notes' => 'Kontak person: Pak Roni. Jasa servis treadmill dan beban berat.'
                ]
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
