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
                'email_verified_at' => now(),
            ]
        );

        // 2. Plans (Paket Sewa)
        $basicPlan = Plan::updateOrCreate(
            ['name' => 'Paket Basic'],
            [
                'price' => 500000,
                'max_members' => 150,
                'features' => ['Akses Manajemen Kelas', 'Kasir / POS Sederhana'],
                'status' => 'active',
            ]
        );

        $proPlan = Plan::updateOrCreate(
            ['name' => 'Paket Pro'],
            [
                'price' => 1200000,
                'max_members' => 500,
                'features' => ['Akses Manajemen Kelas', 'Kasir / POS Sederhana', 'Akses Manajemen Trainer', 'Manajemen Inventaris', 'Mobile App Member Access'],
                'status' => 'active',
            ]
        );

        $enterprisePlan = Plan::updateOrCreate(
            ['name' => 'Paket Enterprise'],
            [
                'price' => 2500000,
                'max_members' => null,
                'features' => ['Akses Manajemen Kelas', 'Kasir / POS Sederhana', 'Akses Manajemen Trainer', 'Manajemen Inventaris', 'Mobile App Member Access', 'Analytics Lanjutan', 'Kustom Domain Sendiri', 'Dedicated Database', 'Support Prioritas 24/7'],
                'status' => 'active',
            ]
        );

        // 3. Tenants (Penyewa Gym)
        $tenantsData = [
            [
                'name' => 'FitLife Studio',
                'subdomain' => 'fitlife.workout.id',
                'slug' => 'fitlife',
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
                'slug' => 'powerhouse',
                'owner_name' => 'Siti Rahma',
                'owner_email' => 'siti@powerhouse.com',
                'plan_id' => $enterprisePlan->id,
                'plan_name' => 'Paket Enterprise',
                'status' => 'active',
                'joined_at' => Carbon::now()->subDays(120),
                'expires_at' => Carbon::now()->addDays(112),
                'features' => ['POS', 'Class', 'Trainer', 'Inventory', 'Mobile', 'Analytics'],            ],
        ];

        $tenantModels = [];
        foreach ($tenantsData as $tData) {
            $tenantModels[$tData['name']] = Tenant::updateOrCreate(
                ['subdomain' => $tData['subdomain']],
                $tData
            );
        }

        // ==== Konten Landing Page Publik (tingkat kustomisasi mengikuti paket) ====
        $landingData = [
            // Paket Pro — warna, bagian, dan fitur unggulan bisa dikustomisasi
            'FitLife Studio' => [
                'hero_title'       => 'Bakar Kalori, Bangun Otot, Raih Versi Terbaik Diri Anda',
                'hero_tagline'     => 'Studio kebugaran modern dengan kelas terjadwal, personal trainer bersertifikat, dan fasilitas loker premium di jantung kota.',
                'about_text'       => 'FitLife Studio hadir untuk menemani perjalanan kebugaran Anda. Dengan peralatan lengkap, kelas yang bervariasi, dan personal trainer profesional, kami membantu setiap member mencapai targetnya — dari pemula hingga atlet.',
                'address'          => 'Jl. Kebugaran Raya No. 12, Bandung',
                'phone'            => '0812-0000-1212',
                'email'            => 'halo@fitlife.com',
                'instagram'        => 'https://instagram.com/fitlife.studio',
                'opening_hours'    => 'Senin - Jumat: 06.00 - 22.00 | Sabtu - Minggu: 07.00 - 20.00',
                'primary_color'    => '#f43f5e',
                'secondary_color'  => '#111827',
                'sections_enabled' => ['hero', 'about', 'features', 'contact', 'footer'],
                'features'         => [
                    ['label' => 'Kelas Terjadwal', 'description' => 'Zumba, yoga, HIIT, dan spinning tiap minggu bersama instruktur bersertifikat.'],
                    ['label' => 'Personal Trainer', 'description' => 'Sesi latihan 1-on-1 dengan coach berpengalaman dan program yang disusun khusus.'],
                    ['label' => 'Loker Ber-PIN', 'description' => 'Loker pribadi dengan PIN akses untuk menyimpan barang Anda dengan aman.'],
                    ['label' => 'Kafe Nutrisi', 'description' => 'Whey protein, suplemen, dan kebutuhan nutrisi tersedia di dalam gym.'],
                ],
            ],
            // Paket Enterprise — statistik angka ikut bisa dikustomisasi
            'Powerhouse Gym' => [
                'hero_title'       => 'Powerhouse Gym — Kekuatan Sejati Dimulai di Sini',
                'hero_tagline'     => 'Rumah bagi atlet dan pecinta fitness sejati. Peralatan kelas kompetisi, coaching profesional, dan komunitas yang saling mendukung.',
                'about_text'       => 'Sejak 2015, Powerhouse Gym telah melahirkan ribuan atlet dan member yang konsisten menjaga kebugarannya. Kami percaya kekuatan sejati bukan hanya soal otot — tapi soal disiplin, komunitas, dan mental juara.',
                'address'          => 'Jl. Juara No. 88, Jakarta Selatan',
                'phone'            => '021-8888-1234',
                'email'            => 'info@powerhouse.com',
                'instagram'        => 'https://instagram.com/powerhouse.gym',
                'facebook'         => 'https://facebook.com/powerhousegym',
                'opening_hours'    => 'Buka 24 Jam (akses member aktif)',
                'primary_color'    => '#2563eb',
                'secondary_color'  => '#0f172a',
                'sections_enabled' => ['hero', 'about', 'features', 'stats', 'contact', 'footer'],
                'features'         => [
                    ['label' => 'Zona Angkat Berat', 'description' => 'Platform angkat beban Olimpiade dengan bumper plate lengkap.'],
                    ['label' => 'Cross & Conditioning', 'description' => 'Area fungsional untuk latihan ketahanan dan kondisi fisik.'],
                    ['label' => 'Kelas Kompetisi', 'description' => 'Program khusus persiapan kompetisi binaraga dan angkat berat.'],
                    ['label' => 'Nutrition Coaching', 'description' => 'Pendampingan pola makan oleh ahli gizi olahraga.'],
                ],
                'stats'            => [
                    ['label' => '500+', 'description' => 'Member Aktif'],
                    ['label' => '25', 'description' => 'Instruktur Sertifikasi'],
                    ['label' => '60+', 'description' => 'Kelas per Minggu'],
                    ['label' => '10', 'description' => 'Tahun Pengalaman'],
                ],
            ],
        ];

        foreach ($landingData as $tenantName => $content) {
            if (!isset($tenantModels[$tenantName])) {
                continue;
            }

            \App\Models\TenantLandingSetting::updateOrCreate(
                ['tenant_id' => $tenantModels[$tenantName]->id],
                $content
            );
        }

        // Seed Tenant Admin User & Staff
        if (isset($tenantModels['FitLife Studio'])) {
            $fitlife = $tenantModels['FitLife Studio'];

            // Akun 1: Owner (Pemilik Gym - Mode Pemantauan Eksekutif)
            $ownerUser = User::updateOrCreate(
                ['email' => 'owner@fitlife.com'],
                [
                    'name' => 'Budi Pratama',
                    'password' => Hash::make('1234'),
                    'role' => 'owner',
                    'tenant_id' => $fitlife->id,
                    'email_verified_at' => now(),
                ]
            );

            // Akun 2: Admin (Pengelola Operasional Harian)
            $adminUser = User::updateOrCreate(
                ['email' => 'admin@fitlife.com'],
                [
                    'name' => 'FitLife Admin',
                    'password' => Hash::make('1234'),
                    'role' => 'admin',
                    'tenant_id' => $fitlife->id,
                    'email_verified_at' => now(),
                ]
            );

            $mgrUser = User::updateOrCreate(
                ['email' => 'manager@fitlife.com'],
                [
                    'name' => 'Joko Manager',
                    'password' => Hash::make('1234'),
                    'role' => 'manager',
                    'tenant_id' => $fitlife->id,
                    'email_verified_at' => now(),
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
                    'email_verified_at' => now(),
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
                    'email_verified_at' => now(),
                ]
            );

            // Akun 6: Member (Portal Keanggotaan Member Gym)
            $mbrUser = User::updateOrCreate(
                ['email' => 'member@fitlife.com'],
                [
                    'name' => 'Budi Member',
                    'password' => Hash::make('1234'),
                    'role' => 'member',
                    'tenant_id' => $fitlife->id,
                    'email_verified_at' => now(),
                ]
            );

            // Master Lockers FitLife Studio (#01 s/d #12)
            for ($i = 1; $i <= 12; $i++) {
                $lockerNum = str_pad($i, 2, '0', STR_PAD_LEFT);
                $status = ($i == 3) ? 'terpakai' : (($i == 10) ? 'rusak' : 'tersedia');
                \App\Models\Locker::updateOrCreate(
                    ['tenant_id' => $fitlife->id, 'locker_number' => $lockerNum],
                    ['status' => $status]
                );
            }

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

        // ==== Data Operasional Lengkap Powerhouse Gym (tenant kedua) ====
        if (isset($tenantModels['Powerhouse Gym'])) {
            $ph = $tenantModels['Powerhouse Gym'];

            // 1. Enam akun lengkap (Owner, Admin, Manager, Resepsionis, Trainer, Member)
            $phOwner = User::updateOrCreate(
                ['email' => 'owner@powerhouse.com'],
                ['name' => 'Siti Rahma', 'password' => Hash::make('1234'), 'role' => 'owner', 'tenant_id' => $ph->id, 'email_verified_at' => now()]
            );

            $phAdmin = User::updateOrCreate(
                ['email' => 'admin@powerhouse.com'],
                ['name' => 'Dian Admin', 'password' => Hash::make('1234'), 'role' => 'admin', 'tenant_id' => $ph->id, 'email_verified_at' => now()]
            );

            $phMgr = User::updateOrCreate(
                ['email' => 'manager@powerhouse.com'],
                ['name' => 'Bayu Manager', 'password' => Hash::make('1234'), 'role' => 'manager', 'tenant_id' => $ph->id, 'email_verified_at' => now()]
            );
            \App\Models\Manager::updateOrCreate(
                ['tenant_id' => $ph->id, 'user_id' => $phMgr->id],
                ['name' => 'Bayu Manager', 'email' => 'manager@powerhouse.com', 'phone' => '081377665544', 'department' => 'Operasional & Logistik', 'status' => 'active']
            );

            $phRec = User::updateOrCreate(
                ['email' => 'resepsionis@powerhouse.com'],
                ['name' => 'Dewi Resepsionis', 'password' => Hash::make('1234'), 'role' => 'receptionist', 'tenant_id' => $ph->id, 'email_verified_at' => now()]
            );
            \App\Models\Receptionist::updateOrCreate(
                ['tenant_id' => $ph->id, 'user_id' => $phRec->id],
                ['name' => 'Dewi Resepsionis', 'email' => 'resepsionis@powerhouse.com', 'phone' => '081255667788', 'shift' => 'Sore', 'status' => 'active']
            );

            $phTrn = User::updateOrCreate(
                ['email' => 'raka@powerhouse.com'],
                ['name' => 'Coach Raka', 'password' => Hash::make('1234'), 'role' => 'trainer', 'tenant_id' => $ph->id, 'email_verified_at' => now()]
            );

            $phMbr = User::updateOrCreate(
                ['email' => 'member@powerhouse.com'],
                ['name' => 'Dodi Member', 'password' => Hash::make('1234'), 'role' => 'member', 'tenant_id' => $ph->id, 'email_verified_at' => now()]
            );

            // 2. Master Loker #01 s/d #08
            for ($i = 1; $i <= 8; $i++) {
                $lockerNum = str_pad($i, 2, '0', STR_PAD_LEFT);
                $status = ($i == 2) ? 'terpakai' : (($i == 7) ? 'rusak' : 'tersedia');
                \App\Models\Locker::updateOrCreate(
                    ['tenant_id' => $ph->id, 'locker_number' => $lockerNum],
                    ['status' => $status]
                );
            }

            // 3. Members (Dodi terhubung ke akun login member portal)
            $pm1 = \App\Models\Member::updateOrCreate(
                ['tenant_id' => $ph->id, 'access_code' => '991234'],
                ['name' => 'Dodi Saputra', 'email' => 'dodi@gmail.com', 'phone' => '081398877665', 'gender' => 'Laki-laki', 'user_id' => $phMbr->id, 'status' => 'active', 'expired_at' => Carbon::now()->addDays(30)]
            );
            $pm2 = \App\Models\Member::updateOrCreate(
                ['tenant_id' => $ph->id, 'access_code' => '995678'],
                ['name' => 'Maya Anggraini', 'email' => 'maya@gmail.com', 'phone' => '081288776655', 'gender' => 'Perempuan', 'status' => 'active', 'expired_at' => Carbon::now()->addDays(3)]
            );
            $pm3 = \App\Models\Member::updateOrCreate(
                ['tenant_id' => $ph->id, 'access_code' => '997890'],
                ['name' => 'Rizky Ramadhan', 'email' => 'rizky@gmail.com', 'phone' => '085711223344', 'gender' => 'Laki-laki', 'status' => 'active', 'expired_at' => Carbon::now()->addDays(60)]
            );

            // 4. Products Kasir
            \App\Models\Product::updateOrCreate(
                ['tenant_id' => $ph->id, 'name' => 'Whey Isolate 24g (Single)'],
                ['category' => 'supplement', 'price' => 75000, 'stock' => 40]
            );
            \App\Models\Product::updateOrCreate(
                ['tenant_id' => $ph->id, 'name' => 'BCAA 1000mg (Single)'],
                ['category' => 'supplement', 'price' => 60000, 'stock' => 30]
            );
            \App\Models\Product::updateOrCreate(
                ['tenant_id' => $ph->id, 'name' => 'Energy Drink Zero Sugar'],
                ['category' => 'drink', 'price' => 12000, 'stock' => 90]
            );

            // 5. Trainer & Kelas
            $pt1 = \App\Models\Trainer::updateOrCreate(
                ['tenant_id' => $ph->id, 'name' => 'Coach Raka'],
                ['user_id' => $phTrn->id, 'email' => 'raka@powerhouse.com', 'phone' => '081166778899', 'specialization' => 'Strength & Powerlifting', 'certification' => 'ISSA Certified Strength Coach', 'bio' => 'Pelatih strength & powerlifting dengan pengalaman 6+ tahun melatih atlet angkat besi.', 'status' => 'active']
            );
            \App\Models\GymClass::updateOrCreate(
                ['tenant_id' => $ph->id, 'name' => 'Strength & Conditioning Circuit'],
                ['trainer_id' => $pt1->id, 'day' => 'Senin', 'start_time' => '07:00', 'end_time' => '08:00', 'room' => 'Zona Fungsional', 'duration_minutes' => 60, 'max_capacity' => 18]
            );
            \App\Models\GymClass::updateOrCreate(
                ['tenant_id' => $ph->id, 'name' => 'Powerlifting Technique'],
                ['trainer_id' => $pt1->id, 'day' => 'Jumat', 'start_time' => '19:00', 'end_time' => '20:30', 'room' => 'Ruang Beban', 'duration_minutes' => 90, 'max_capacity' => 12]
            );

            // 6. Check-in hari ini
            \App\Models\CheckIn::updateOrCreate(
                ['tenant_id' => $ph->id, 'member_id' => $pm1->id, 'access_code' => '991234'],
                ['checked_in_at' => Carbon::now()->subMinutes(50), 'check_in_method' => 'code']
            );

            // 7. Staff Log
            \App\Models\StaffLog::updateOrCreate(
                ['tenant_id' => $ph->id, 'action' => 'Registrasi Member Baru'],
                ['user_id' => $phAdmin->id, 'description' => 'Admin Powerhouse mendaftarkan member Dodi Saputra dengan PIN 991234.', 'ip_address' => '127.0.0.1']
            );

            // 8. Equipment & Maintenance
            \App\Models\GymEquipment::updateOrCreate(
                ['tenant_id' => $ph->id, 'name' => 'Barbell Olimpiade Eleiko 20kg'],
                ['category' => 'Alat Berat', 'brand' => 'Eleiko', 'status' => 'berfungsi', 'purchase_date' => Carbon::now()->subMonths(8), 'next_service_date' => Carbon::now()->addMonths(3), 'notes' => 'Barbell utama area angkat beban']
            );
            $pe2 = \App\Models\GymEquipment::updateOrCreate(
                ['tenant_id' => $ph->id, 'name' => 'Squat Rack Pro Series'],
                ['category' => 'Alat Berat', 'brand' => 'Powertec', 'status' => 'perlu_servis', 'purchase_date' => Carbon::now()->subYear(), 'next_service_date' => Carbon::now()->subWeek(), 'notes' => 'Baut penyangga mulai longgar, perlu dikencangkan & diberi pelumas']
            );
            \App\Models\EquipmentMaintenanceLog::updateOrCreate(
                ['tenant_id' => $ph->id, 'gym_equipment_id' => $pe2->id, 'action' => 'Pengencangan Baut & Pelumasan'],
                ['description' => 'Servis rutin squat rack: kencangkan seluruh baut penyangga dan beri pelumas rel beban', 'cost' => 450000, 'serviced_at' => Carbon::now()->subMonths(2), 'next_service_date' => Carbon::now()->addMonths(1)]
            );

            // 9. Shift & Cuti
            \App\Models\StaffShift::updateOrCreate(
                ['tenant_id' => $ph->id, 'user_id' => $phRec->id, 'shift_date' => Carbon::today()],
                ['shift_name' => 'Sore', 'start_time' => '14:00:00', 'end_time' => '22:00:00', 'notes' => 'Shift front desk utama sore']
            );
            \App\Models\LeaveRequest::updateOrCreate(
                ['tenant_id' => $ph->id, 'user_id' => $phRec->id, 'reason' => 'Acara keluarga di luar kota'],
                ['start_date' => Carbon::tomorrow(), 'end_date' => Carbon::tomorrow(), 'status' => 'pending']
            );

            // 10. Promo
            \App\Models\PromoCode::updateOrCreate(
                ['tenant_id' => $ph->id, 'code' => 'POWERUP'],
                ['description' => 'Diskon 10% untuk pembelian paket membership & suplemen', 'discount_type' => 'percentage', 'discount_value' => 10, 'min_purchase' => 200000, 'max_uses' => 100, 'used_count' => 8, 'valid_from' => Carbon::now()->subDays(3), 'valid_until' => Carbon::now()->addDays(27), 'is_active' => true]
            );

            // 11. Complaint
            \App\Models\Complaint::updateOrCreate(
                ['tenant_id' => $ph->id, 'member_id' => $pm2->id, 'title' => 'Pendingin Ruangan Area Kardio Gangguan'],
                ['reported_by' => $phRec->id, 'description' => 'AC di area kardio tidak dingin sejak pagi, member mengeluh kepanasan saat latihan.', 'status' => 'open']
            );

            // 12. Vendor
            \App\Models\Vendor::updateOrCreate(
                ['tenant_id' => $ph->id, 'name' => 'PT Gaya Sportindo (Supplier Alat)'],
                ['phone' => '021-555-7788', 'email' => 'sales@gayasportindo.com', 'category' => 'Supplier Alat Gym', 'address' => 'Jl. Gatot Subroto No. 15, Jakarta', 'notes' => 'Kontak person: Pak Andre. Supplier resmi barbel & rack.' ]
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
            ['action' => 'Update Fitur Tenant'],
            [
                'user_id' => $admin->id,
                'description' => 'Superadmin memperbarui fitur & add-on tenant Powerhouse Gym (Paket Enterprise).',
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
