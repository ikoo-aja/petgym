<?php

namespace Tests\Audit;

use App\Models\Announcement;
use App\Models\CheckIn;
use App\Models\ClassRsvp;
use App\Models\Complaint;
use App\Models\GymClass;
use App\Models\GymEquipment;
use App\Models\Invoice;
use App\Models\LeaveRequest;
use App\Models\Locker;
use App\Models\LockerRental;
use App\Models\LostFound;
use App\Models\Member;
use App\Models\MemberPtQuota;
use App\Models\Plan;
use App\Models\PosTransaction;
use App\Models\PosTransactionItem;
use App\Models\Product;
use App\Models\PromoCode;
use App\Models\PtBooking;
use App\Models\ReceptionistShift;
use App\Models\StaffShift;
use App\Models\Tenant;
use App\Models\TenantRegistration;
use App\Models\Trainer;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Tests\TestCase;

class InteractiveElementsE2ETest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    private function user(string $email): User
    {
        return User::where('email', $email)->first() ?? User::where('email', str_replace('@petgym.website', '@petgym.com', $email))->firstOrFail();
    }

    private function fitlife(): Tenant
    {
        return Tenant::where('slug', 'fitlife')->firstOrFail();
    }

    private function ensureOpenShift(User $rec): void
    {
        ReceptionistShift::firstOrCreate(
            ['tenant_id' => $rec->tenant_id, 'user_id' => $rec->id, 'status' => 'open'],
            ['start_cash' => 100000, 'opened_at' => now()]
        );
    }

    // ==========================================
    // 1. SUPERADMIN ROLE TESTS
    // ==========================================

    public function test_superadmin_clear_cache_button()
    {
        $sa = $this->user('superadmin@petgym.website');
        $res = $this->actingAs($sa)->post(route('superadmin.clear-cache'));
        $res->assertSessionHas('success');
    }

    public function test_superadmin_store_plan_and_toggle()
    {
        $sa = $this->user('superadmin@petgym.website');
        $res = $this->actingAs($sa)->post(route('superadmin.plans.store'), [
            'name' => 'Paket QA Test',
            'price' => 750000,
            'duration_days' => 30,
            'features' => ['members', 'pos'],
        ]);
        $res->assertRedirect(route('superadmin.plans'));
        $plan = Plan::where('name', 'Paket QA Test')->first();
        $this->assertNotNull($plan);

        // Toggle status
        $resToggle = $this->actingAs($sa)->post(route('superadmin.plans.toggle-status', $plan->id));
        $resToggle->assertRedirect(route('superadmin.plans'));
        $plan->refresh();
        $this->assertSame('inactive', $plan->status);
    }

    public function test_superadmin_settings_update_does_not_persist()
    {
        $sa = $this->user('superadmin@petgym.website');
        $res = $this->actingAs($sa)->post(route('superadmin.settings.update'), [
            'section' => 'Umum',
            'app_name' => 'Nama Baru Platform QA',
        ]);
        $res->assertRedirect(route('superadmin.settings'));
        // Verifikasi apakah nama platform berubah di config atau database
        $this->assertNotSame('Nama Baru Platform QA', config('app.name'));
    }

    public function test_superadmin_settings_billing_persists()
    {
        $sa = $this->user('superadmin@petgym.website');
        $res = $this->actingAs($sa)->post(route('superadmin.settings.update'), [
            'section' => 'billing',
            'billing_bank_name' => 'BCA Digital',
            'billing_bank_account' => '888899990000',
            'billing_bank_owner' => 'PT PetGym Global Solusi',
            'billing_bank_notes' => 'Catatan transfer uji coba QA',
            'billing_mandiri_account' => '1370000000000',
            'billing_qris_name' => 'QRIS PetGym Official',
            'billing_qris_code' => 'QRIS-OFFICIAL-QA',
        ]);
        $res->assertRedirect(route('superadmin.settings'));
        $res->assertSessionHas('success');

        $this->assertSame('BCA Digital', \App\Models\SystemSetting::get('billing_bank_name'));
        $this->assertSame('888899990000', \App\Models\SystemSetting::get('billing_bank_account'));
        $this->assertSame('PT PetGym Global Solusi', \App\Models\SystemSetting::get('billing_bank_owner'));
        $this->assertSame('QRIS-OFFICIAL-QA', \App\Models\SystemSetting::get('billing_qris_code'));
    }

    public function test_superadmin_settings_support_and_mail_persist()
    {
        $sa = $this->user('superadmin@petgym.website');
        $resSupport = $this->actingAs($sa)->post(route('superadmin.settings.update'), [
            'section' => 'support',
            'support_whatsapp' => '089911223344',
            'support_email' => 'cs-official@petgym.website',
            'support_hours' => '24 Jam Setiap Hari',
        ]);
        $resSupport->assertRedirect(route('superadmin.settings'));
        $resSupport->assertSessionHas('success');
        $this->assertSame('089911223344', \App\Models\SystemSetting::get('support_whatsapp'));

        $resMail = $this->actingAs($sa)->post(route('superadmin.settings.update'), [
            'section' => 'mail',
            'mail_host' => 'smtp.postmarkapp.com',
            'mail_port' => 587,
            'mail_username' => 'postmark-user',
            'mail_password' => 'secret123',
            'mail_encryption' => 'tls',
            'mail_from_name' => 'PetGym Official Gateway',
            'mail_from_address' => 'noreply@petgym.website',
        ]);
        $resMail->assertRedirect(route('superadmin.settings'));
        $resMail->assertSessionHas('success');
        $this->assertSame('smtp.postmarkapp.com', \App\Models\SystemSetting::get('mail_host'));
    }

    public function test_global_maintenance_mode_and_superadmin_bypass()
    {
        // 1. Aktifkan maintenance mode global
        $sa = $this->user('superadmin@petgym.website');
        $this->actingAs($sa)->post(route('superadmin.settings.update'), [
            'section' => 'maintenance',
            'maintenance_active' => '1',
            'maintenance_title' => 'Pemeliharaan Darurat QA',
            'maintenance_message' => 'Sistem sedang diuji coba.',
            'maintenance_estimated_end' => 'Estimasi 10 menit',
        ]);
        $this->assertSame('1', \App\Models\SystemSetting::get('maintenance_active'));

        // 2. Superadmin tetap bisa akses (bypass)
        $resSa = $this->actingAs($sa)->get(route('superadmin.dashboard'));
        $resSa->assertStatus(200);

        // 3. User gym biasa (admin/member) diblokir dengan 503 maintenance
        $admin = $this->user('admin@fitlife.com');
        $resAdmin = $this->actingAs($admin)->get(route('admin.dashboard'));
        $resAdmin->assertStatus(503);
        $resAdmin->assertSee('Pemeliharaan Darurat QA');

        // 4. Nonaktifkan kembali maintenance mode
        $this->actingAs($sa)->post(route('superadmin.settings.update'), [
            'section' => 'maintenance',
            'maintenance_title' => 'Normal',
            'maintenance_message' => 'Selesai',
        ]);
        $this->assertSame('0', \App\Models\SystemSetting::get('maintenance_active'));

        // 5. User gym kembali bisa akses normal
        $resAdminNormal = $this->actingAs($admin)->get(route('admin.dashboard'));
        $resAdminNormal->assertStatus(200);
    }

    // ==========================================
    // 2. ADMIN ROLE TESTS (FitLife)
    // ==========================================

    public function test_admin_staff_store_and_toggle()
    {
        $admin = $this->user('admin@fitlife.com');
        $res = $this->actingAs($admin)->post(route('admin.staff.store'), [
            'name' => 'Manager Baru QA',
            'email' => 'mgr.qa@fitlife.com',
            'role' => 'manager',
            'password' => 'secret123',
        ]);
        $res->assertRedirect(route('admin.staff.index'));
        $newStaff = User::where('email', 'mgr.qa@fitlife.com')->first();
        $this->assertNotNull($newStaff);
        $this->assertSame('manager', $newStaff->role);

        // Toggle status
        $resToggle = $this->actingAs($admin)->post(route('admin.staff.toggle-status', $newStaff->id));
        $resToggle->assertRedirect(route('admin.staff.index'));
        $newStaff->refresh();
        $this->assertFalse($newStaff->isActive());
    }

    public function test_admin_settings_update_profile()
    {
        $admin = $this->user('admin@fitlife.com');
        $res = $this->actingAs($admin)->post(route('admin.settings.update'), [
            'name' => 'FitLife Studio Updated',
            'owner_name' => 'Budi Pratama Updated',
            'owner_email' => 'budi.updated@fitlife.com',
        ]);
        $res->assertRedirect(route('admin.settings.index'));
        $this->fitlife()->refresh();
        $this->assertSame('FitLife Studio Updated', $this->fitlife()->name);
    }

    // ==========================================
    // 3. MANAGER ROLE TESTS (FitLife)
    // ==========================================

    public function test_manager_cannot_register_member_directly()
    {
        $mgr = $this->user('manager@fitlife.com');
        $res = $this->actingAs($mgr)->post(route('manager.members.store'), [
            'name' => 'Member Ilegal',
            'phone' => '08123456789',
            'gender' => 'Laki-laki',
            'package_duration' => '1',
            'payment_method' => 'cash',
        ]);
        $res->assertStatus(403);
    }

    public function test_manager_classes_crud()
    {
        $mgr = $this->user('manager@fitlife.com');
        $trainer = Trainer::where('tenant_id', $mgr->tenant_id)->firstOrFail();

        // 1. Create Class
        $res = $this->actingAs($mgr)->post(route('manager.classes.store'), [
            'name' => 'Yoga Pagi QA',
            'day' => 'Senin',
            'start_time' => '07:00',
            'end_time' => '08:00',
            'max_capacity' => 15,
            'trainer_id' => $trainer->id,
            'room' => 'Studio 1',
        ]);
        $res->assertRedirect(route('manager.classes.index'));
        $class = GymClass::where('name', 'Yoga Pagi QA')->first();
        $this->assertNotNull($class);

        // 2. Update Class
        $resUpdate = $this->actingAs($mgr)->put(route('manager.classes.update', $class->id), [
            'name' => 'Yoga Pagi QA Update',
            'day' => 'Selasa',
            'start_time' => '07:30',
            'end_time' => '08:30',
            'max_capacity' => 20,
            'trainer_id' => $trainer->id,
            'room' => 'Studio 2',
        ]);
        $resUpdate->assertRedirect(route('manager.classes.index'));
        $class->refresh();
        $this->assertSame('Yoga Pagi QA Update', $class->name);

        // 3. Delete Class
        $resDel = $this->actingAs($mgr)->delete(route('manager.classes.destroy', $class->id));
        $resDel->assertRedirect(route('manager.classes.index'));
        $this->assertNull(GymClass::find($class->id));
    }

    public function test_manager_promo_and_vendor_crud()
    {
        $mgr = $this->user('manager@fitlife.com');

        // Promo
        $resPromo = $this->actingAs($mgr)->post(route('manager.promo.store'), [
            'code' => 'TESTQA50',
            'description' => 'Diskon QA',
            'discount_type' => 'percentage',
            'discount_value' => 50,
            'min_purchase' => 100000,
            'max_uses' => 10,
            'valid_from' => now()->toDateString(),
            'valid_until' => now()->addDays(7)->toDateString(),
        ]);
        $resPromo->assertRedirect(route('manager.features', ['tab' => 'promo']));
        $promo = PromoCode::where('code', 'TESTQA50')->first();
        $this->assertNotNull($promo);

        // Vendor
        $resVendor = $this->actingAs($mgr)->post(route('manager.vendors.store'), [
            'name' => 'PT Suplemen QA Sejahtera',
            'category' => 'Suplemen',
            'phone' => '081299998888',
        ]);
        $resVendor->assertRedirect(route('manager.features', ['tab' => 'vendor']));
    }

    // ==========================================
    // 4. SUPERVISOR ROLE TESTS (FitLife)
    // ==========================================

    public function test_supervisor_equipment_and_maintenance()
    {
        $spv = $this->user('supervisor@fitlife.com');
        $res = $this->actingAs($spv)->post(route('supervisor.equipment.store'), [
            'name' => 'Treadmill Commercial QA',
            'category' => 'Cardio',
            'status' => 'berfungsi',
        ]);
        $res->assertRedirect(route('supervisor.features', ['tab' => 'equipment']));
        $eq = GymEquipment::where('name', 'Treadmill Commercial QA')->first();
        $this->assertNotNull($eq);

        // Maintenance Log
        $resLog = $this->actingAs($spv)->post(route('supervisor.maintenance.store'), [
            'gym_equipment_id' => $eq->id,
            'action' => 'Ganti oli belt',
            'cost' => 150000,
            'serviced_at' => now()->toDateString(),
            'description' => 'Teknisi Luar',
        ]);
        $resLog->assertRedirect(route('supervisor.features', ['tab' => 'equipment']));
    }

    // ==========================================
    // 5. RECEPTIONIST ROLE TESTS (FitLife)
    // ==========================================

    public function test_receptionist_shift_and_pos_checkout()
    {
        $rec = $this->user('resepsionis@fitlife.com');
        $this->ensureOpenShift($rec);
        $prod = Product::where('tenant_id', $rec->tenant_id)->where('category', '!=', 'membership')->firstOrFail();
        $oldStock = $prod->stock;

        $res = $this->actingAs($rec)->post(route('receptionist.pos.checkout'), [
            'payment_method' => 'cash',
            'type' => 'inventory',
            'items' => [
                [
                    'product_id' => $prod->id,
                    'item_name' => $prod->name,
                    'qty' => 1,
                    'price' => $prod->price,
                ]
            ],
        ]);

        $res->assertRedirect(route('receptionist.pos.index'));
        $res->assertSessionHas('success');
        $prod->refresh();
        $this->assertSame($oldStock - 1, $prod->stock);
    }

    public function test_receptionist_checkin_flow()
    {
        $rec = $this->user('resepsionis@fitlife.com');
        $this->ensureOpenShift($rec);
        $member = Member::where('tenant_id', $rec->tenant_id)->where('status', 'active')->firstOrFail();

        $res = $this->actingAs($rec)->post(route('receptionist.checkin.process'), [
            'access_code' => $member->access_code,
        ]);
        $res->assertRedirect(route('receptionist.checkin.index'));
        $res->assertSessionHas('success');

        // Checkin gagal kode salah
        $resFail = $this->actingAs($rec)->post(route('receptionist.checkin.process'), [
            'access_code' => 'WRONG-CODE-999',
        ]);
        $resFail->assertSessionHas('error');
    }

    public function test_receptionist_lost_found_store_and_claim()
    {
        $rec = $this->user('resepsionis@fitlife.com');
        $this->ensureOpenShift($rec);

        $res = $this->actingAs($rec)->post(route('receptionist.lost-found.store'), [
            'item_name' => 'Botol Minum HydroFlask QA',
            'location_found' => 'Dekat Bench Press',
            'found_at' => now()->toDateString(),
        ]);
        $res->assertRedirect(route('receptionist.lost-found'));
        $lf = LostFound::where('item_name', 'Botol Minum HydroFlask QA')->first();
        $this->assertNotNull($lf);
        $this->assertSame('tercatat', $lf->status);

        // Claim
        $resClaim = $this->actingAs($rec)->post(route('receptionist.lost-found.claim', $lf->id), [
            'claimed_by_name' => 'Pemilik Budi',
        ]);
        $resClaim->assertRedirect(route('receptionist.lost-found'));
        $lf->refresh();
        $this->assertSame('diklaim', $lf->status);
    }

    // ==========================================
    // 6. PERSONAL TRAINER ROLE TESTS (FitLife)
    // ==========================================

    public function test_trainer_views_sessions_and_updates_status()
    {
        $trainerUser = $this->user('alex@fitlife.com');
        $trainerProfile = Trainer::where('user_id', $trainerUser->id)->firstOrFail();
        $member = Member::where('tenant_id', $trainerUser->tenant_id)->firstOrFail();

        $booking = PtBooking::create([
            'tenant_id' => $trainerUser->tenant_id,
            'member_id' => $member->id,
            'trainer_id' => $trainerProfile->id,
            'booking_date' => now()->toDateString(),
            'booking_time' => '10:00',
            'status' => 'scheduled',
        ]);

        $res = $this->actingAs($trainerUser)->post(route('trainer.pt-sessions.status', $booking->id), [
            'status' => 'completed',
        ]);
        $res->assertSessionHas('success');
        $booking->refresh();
        $this->assertSame('completed', $booking->status);
    }

    // ==========================================
    // 7. MEMBER PORTAL ROLE TESTS (FitLife)
    // ==========================================

    public function test_member_rent_locker_flow()
    {
        $memberUser = $this->user('member@fitlife.com');
        $locker = Locker::where('tenant_id', $memberUser->tenant_id)->where('status', 'tersedia')->firstOrFail();

        $res = $this->actingAs($memberUser)->post(route('member.lockers.rent'), [
            'locker_id' => $locker->id,
            'rental_type' => 'daily',
            'payment_method' => 'qris',
        ]);

        $res->assertRedirect(route('member.lockers'));
        $res->assertSessionHas('success');
        $locker->refresh();
        $this->assertSame('terpakai', $locker->status);
    }

    public function test_member_buy_pt_quota_and_book_session()
    {
        $memberUser = $this->user('member@fitlife.com');
        $trainer = Trainer::where('tenant_id', $memberUser->tenant_id)->firstOrFail();

        // 1. Beli Kuota PT
        $resBuy = $this->actingAs($memberUser)->post(route('member.pt.buy_quota'), [
            'trainer_id' => $trainer->id,
            'sessions' => 5,
        ]);
        $resBuy->assertRedirect(route('member.pt'));
        $resBuy->assertSessionHas('success');

        // 2. Booking Sesi
        $bookDate = now()->addDays(2)->toDateString();
        $resBook = $this->actingAs($memberUser)->post(route('member.pt.book'), [
            'trainer_id' => $trainer->id,
            'booking_date' => $bookDate,
            'booking_time' => '14:00',
        ]);
        $resBook->assertRedirect(route('member.pt'));
        $resBook->assertSessionHas('success');

        // 3. Double Booking Conflict
        $resConflict = $this->actingAs($memberUser)->post(route('member.pt.book'), [
            'trainer_id' => $trainer->id,
            'booking_date' => $bookDate,
            'booking_time' => '14:00',
        ]);
        $resConflict->assertSessionHas('error');
    }

    public function test_member_update_profile_and_send_feedback()
    {
        $memberUser = $this->user('member@fitlife.com');

        // Update profil
        $resProf = $this->actingAs($memberUser)->post(route('member.settings.update_profile'), [
            'name' => 'Budi QA Member',
            'phone' => '081299990000',
            'gender' => 'Laki-laki',
            'address' => 'Jl. Kebon Jeruk No. 12',
        ]);
        $resProf->assertRedirect(route('member.settings'));
        $resProf->assertSessionHas('success');

        // Kirim feedback / komplain
        $resFb = $this->actingAs($memberUser)->post(route('member.settings.feedback'), [
            'rating' => 5,
            'feedback' => 'Aplikasi gym sangat cepat dan responsif!',
        ]);
        $resFb->assertRedirect(route('member.settings'));
        $resFb->assertSessionHas('success');
        $cmp = Complaint::where('reported_by', $memberUser->id)->latest()->first();
        $this->assertNotNull($cmp);
        $this->assertStringContainsString('Aplikasi gym sangat cepat', $cmp->description);
    }

    // ==========================================
    // 8. OWNER ROLE TESTS (FitLife)
    // ==========================================

    public function test_owner_update_payment_settings()
    {
        $owner = $this->user('owner@fitlife.com');
        $res = $this->actingAs($owner)->post(route('owner.settings.payment'), [
            'bank_name' => 'BCA Digital',
            'bank_account_number' => '8880192837',
            'bank_account_holder' => 'FitLife Studio Official',
            'qris_merchant_name' => 'FitLife Studio QRIS',
        ]);
        $res->assertRedirect(route('owner.settings'));
        $res->assertSessionHas('success');
        $this->fitlife()->refresh();
        $this->assertSame('BCA Digital', $this->fitlife()->bank_name);
        $this->assertSame('8880192837', $this->fitlife()->bank_account_number);
    }

    public function test_member_class_rsvp_and_cancel_flow()
    {
        $memberUser = $this->user('member@fitlife.com');
        $member = Member::where('user_id', $memberUser->id)->firstOrFail();
        $class = GymClass::where('tenant_id', $memberUser->tenant_id)->firstOrFail();
        $classDate = now()->addDays(3)->toDateString();

        // 1. RSVP Class
        $resRsvp = $this->actingAs($memberUser)->post(route('member.classes.rsvp'), [
            'gym_class_id' => $class->id,
            'class_date' => $classDate,
        ]);
        $resRsvp->assertRedirect(route('member.classes'));
        $resRsvp->assertSessionHas('success');

        $rsvp = ClassRsvp::where('member_id', $member->id)->where('gym_class_id', $class->id)->latest()->first();
        $this->assertNotNull($rsvp);
        $this->assertSame('confirmed', $rsvp->status);

        // 2. Cancel RSVP
        $resCancel = $this->actingAs($memberUser)->post(route('member.classes.cancel', $rsvp->id));
        $resCancel->assertRedirect(route('member.classes'));
        $resCancel->assertSessionHas('success');
        $rsvp->refresh();
        $this->assertSame('cancelled', $rsvp->status);
    }

    public function test_supervisor_equipment_dropdown_value_mismatch_fails_validation()
    {
        $spv = $this->user('supervisor@fitlife.com');
        // Nilai dari opsi dropdown di view features.blade.php: 'perlu_perbaikan' dan 'rusak_berat'
        $res = $this->actingAs($spv)->from(route('supervisor.features', ['tab' => 'equipment']))->post(route('supervisor.equipment.store'), [
            'name' => 'Alat Dropdown Test',
            'category' => 'Strength',
            'status' => 'perlu_perbaikan', // Opsi di blade yang bertentangan dengan validator controller ('in:berfungsi,perlu_servis,rusak')
        ]);
        $res->assertSessionHasErrors('status');
    }
}
