<?php

namespace Tests\Audit;

use App\Models\ClassRsvp;
use App\Models\Complaint;
use App\Models\Member;
use App\Models\PtBooking;
use App\Models\ReceptionistShift;
use App\Models\Tenant;
use App\Models\Trainer;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Tests\TestCase;

/**
 * AUDIT VALIDASI INPUT
 *
 * 1. Setiap endpoint wajib mengembalikan error validasi (bukan 500 / bukan sukses)
 *    ketika dikirim payload kosong.
 * 2. Beberapa endpoint yang divalidasi secara longgar (mis. enum) diuji dengan
 *    nilai tak dikenal untuk melihat apakah berujung error server.
 */
class ValidationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    private function user(string $email): User
    {
        return User::where('email', $email)->firstOrFail();
    }

    private function tenant(): Tenant
    {
        return Tenant::where('slug', 'fitlife')->firstOrFail();
    }

    private function openShiftFor(User $user): void
    {
        ReceptionistShift::create([
            'tenant_id' => $user->tenant_id,
            'user_id' => $user->id,
            'start_cash' => 100000,
            'opened_at' => now(),
            'status' => 'open',
        ]);
    }

    public function test_payload_kosong_menghasilkan_error_validasi(): void
    {
        $tenant = $this->tenant();
        $member = Member::where('tenant_id', $tenant->id)->firstOrFail();
        $complaint = Complaint::where('tenant_id', $tenant->id)->firstOrFail();
        $trainer = Trainer::where('tenant_id', $tenant->id)->firstOrFail();

        $booking = PtBooking::create([
            'tenant_id' => $tenant->id,
            'member_id' => $member->id,
            'trainer_id' => $trainer->id,
            'booking_date' => now()->addDay()->toDateString(),
            'booking_time' => '10:00',
            'status' => 'scheduled',
        ]);

        $rsvp = ClassRsvp::create([
            'tenant_id' => $tenant->id,
            'gym_class_id' => \App\Models\GymClass::where('tenant_id', $tenant->id)->firstOrFail()->id,
            'member_id' => $member->id,
            'class_date' => now()->addDay()->toDateString(),
            'status' => 'confirmed',
        ]);

        $receptionist = $this->user('resepsionis@fitlife.com');
        $this->openShiftFor($receptionist);

        // Hapus invoice pending milik tenant agar endpoint pembayaran langganan
        // tidak berhenti di guard "masih ada tagihan pending" (bukan uji validasi).
        \App\Models\Invoice::where('tenant_id', $tenant->id)
            ->whereIn('status', ['pending', 'dp_pending'])
            ->delete();

        // [label, email aktor, method, nama route, parameter]
        $cases = [
            ['daftar SaaS', null, 'post', 'register.saas', []],
            ['daftar member publik', null, 'post', 'member.register.submit', []],
            ['lupa password', null, 'post', 'password.email', []],
            ['simpan staf admin', 'admin@fitlife.com', 'post', 'admin.staff.store', []],
            ['bayar langganan', 'admin@fitlife.com', 'post', 'admin.subscription.pay', []],
            ['daftar member', 'resepsionis@fitlife.com', 'post', 'manager.members.store', []],
            ['update member', 'manager@fitlife.com', 'put', 'manager.members.update', [$member->id]],
            ['simpan kelas', 'manager@fitlife.com', 'post', 'manager.classes.store', []],
            ['simpan trainer', 'manager@fitlife.com', 'post', 'manager.classes.store-trainer', []],
            ['simpan promo', 'manager@fitlife.com', 'post', 'manager.promo.store', []],
            ['simpan master kelas', 'manager@fitlife.com', 'post', 'manager.master-classes.store', []],
            ['simpan vendor', 'manager@fitlife.com', 'post', 'manager.vendors.store', []],
            ['simpan staf operasional', 'manager@fitlife.com', 'post', 'manager.staff.store', []],
            ['simpan shift', 'supervisor@fitlife.com', 'post', 'supervisor.shifts.store', []],
            ['simpan cuti', 'supervisor@fitlife.com', 'post', 'supervisor.leave.store', []],
            ['simpan alat', 'supervisor@fitlife.com', 'post', 'supervisor.equipment.store', []],
            ['simpan log maintenance', 'supervisor@fitlife.com', 'post', 'supervisor.maintenance.store', []],
            ['update komplain', 'supervisor@fitlife.com', 'put', 'supervisor.complaints.update', [$complaint->id]],
            ['checkout POS', 'resepsionis@fitlife.com', 'post', 'receptionist.pos.checkout', []],
            ['simpan produk', 'resepsionis@fitlife.com', 'post', 'receptionist.products.store', []],
            ['check-in kode', 'resepsionis@fitlife.com', 'post', 'receptionist.checkin.process', []],
            ['check-in manual', 'resepsionis@fitlife.com', 'post', 'receptionist.checkin.manual', []],
            ['simpan loker', 'resepsionis@fitlife.com', 'post', 'receptionist.lockers.store', []],
            ['pinjam loker', 'resepsionis@fitlife.com', 'post', 'receptionist.lockers.assign', []],
            ['simpan lost found', 'resepsionis@fitlife.com', 'post', 'receptionist.lost-found.store', []],
            ['simpan komplain', 'resepsionis@fitlife.com', 'post', 'receptionist.complaints.store', []],
            ['check-in sesi PT', 'resepsionis@fitlife.com', 'post', 'receptionist.pt.checkin', []],
            ['update status PT', 'alex@fitlife.com', 'post', 'trainer.pt-sessions.status', [$booking->id]],
            ['update status RSVP', 'alex@fitlife.com', 'post', 'trainer.rsvps.status', [$rsvp->id]],
            ['upgrade membership', 'member@fitlife.com', 'post', 'member.membership.upgrade', []],
            ['beli kuota PT', 'member@fitlife.com', 'post', 'member.pt.buy_quota', []],
            ['booking PT', 'member@fitlife.com', 'post', 'member.pt.book', []],
            ['RSVP kelas', 'member@fitlife.com', 'post', 'member.classes.rsvp', []],
            ['update profil member', 'member@fitlife.com', 'post', 'member.settings.update_profile', []],
            ['update password member', 'member@fitlife.com', 'post', 'member.settings.update_password', []],
            ['ganti password wajib', 'admin@fitlife.com', 'post', 'password.change.update', []],
            ['update profil akun', 'admin@fitlife.com', 'post', 'account.profile.update', []],
            ['update password akun', 'admin@fitlife.com', 'post', 'account.password.update', []],
            ['simpan paket', 'superadmin@petgym.com', 'post', 'superadmin.plans.store', []],
            ['simpan pengumuman', 'superadmin@petgym.com', 'post', 'superadmin.announcements.store', []],
            ['simpan prospek', 'superadmin@petgym.com', 'post', 'superadmin.registrations.store', []],
            [
                'approve pendaftaran', 'superadmin@petgym.com', 'post', 'superadmin.registrations.approve',
                [\App\Models\TenantRegistration::create([
                    'name' => 'Prospek Uji', 'email' => 'prospek@uji.test', 'phone' => '0800',
                    'plan_name' => 'Paket Basic', 'status' => 'pending',
                ])->id],
            ],
        ];

        $failures = [];

        foreach ($cases as [$label, $email, $method, $route, $params]) {
            if ($email !== null) {
                $this->actingAs($this->user($email));
            }

            $this->flushSession();
            $response = $this->from('/')->{$method}(route($route, $params), []);
            $status = $response->getStatusCode();
            $errors = session()->get('errors');
            $hasErrors = $errors && $errors->isNotEmpty();

            if ($status >= 500) {
                $failures[] = "{$label} ({$route}) => HTTP {$status} (SERVER ERROR saat input kosong)";
            } elseif ($status === 403) {
                $failures[] = "{$label} ({$route}) => HTTP 403 (role seharusnya diizinkan)";
            } elseif (!$hasErrors) {
                $failures[] = "{$label} ({$route}) => HTTP {$status} TANPA error validasi (input kosong diterima)";
            }
        }

        $this->assertSame([], $failures, "Validasi input bermasalah:\n" . implode("\n", $failures));
    }

    public function test_discount_type_promo_dibatasi_nilai_enum(): void
    {
        $manager = $this->user('manager@fitlife.com');

        $response = $this->actingAs($manager)->from('/')->post(route('manager.promo.store'), [
            'code' => 'PROMO-UJI-' . uniqid(),
            'description' => 'Uji tipe diskon tak dikenal',
            'discount_type' => 'tipe-tidak-dikenal',
            'discount_value' => 10,
            'min_purchase' => 0,
            'max_uses' => 10,
            'valid_from' => now()->toDateString(),
            'valid_until' => now()->addMonth()->toDateString(),
        ]);

        $this->assertNotSame(
            500,
            $response->getStatusCode(),
            "discount_type tidak divalidasi enum dan memicu error server (HTTP 500)."
        );
    }

    public function test_status_alat_dibatasi_nilai_enum(): void
    {
        $supervisor = $this->user('supervisor@fitlife.com');

        $response = $this->actingAs($supervisor)->from('/')->post(route('supervisor.equipment.store'), [
            'name' => 'Alat Uji',
            'category' => 'Cardio',
            'status' => 'status-tidak-dikenal',
        ]);

        $this->assertNotSame(
            500,
            $response->getStatusCode(),
            "status alat tidak divalidasi enum dan memicu error server (HTTP 500)."
        );
    }
}
