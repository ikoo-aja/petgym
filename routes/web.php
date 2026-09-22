<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\TenantLandingController;
use App\Http\Controllers\SupervisorController;

// Landing Page Publik per-Tenant lewat subdomain (demo: fitlife.localhost, powerhouse.localhost, dll)
// WAJIB didaftarkan sebelum route '/' utama supaya request subdomain tidak jatuh ke welcome page.
Route::domain('{slug}.' . config('app.tenant_apex'))->group(function () {
    Route::get('/', [TenantLandingController::class, 'show'])->name('tenant.landing');
    Route::get('/login', [TenantLandingController::class, 'showMemberLogin'])->name('tenant.login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [TenantLandingController::class, 'showMemberRegister'])->name('tenant.register');
    Route::post('/register', [LoginController::class, 'registerMember'])->name('tenant.register.submit');
});

Route::get('/', function () {
    return view('welcome');
});

Route::post('/checkout', [\App\Http\Controllers\SuperadminController::class, 'publicCheckout'])->name('public.checkout');

Route::get('/home', function () {
    // Arahkan ke dashboard sesuai role user (bukan hardcode /admin)
    $user = auth()->user();
    return redirect()->route($user ? $user->dashboardRoute() : 'admin.dashboard');
});



// Group Guest (Hanya bisa diakses jika belum login)
Route::middleware('guest')->group(function () {
    // 1. Alur Pembelian & Pengelolaan Web Gym SaaS (SaaS Platform)
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [LoginController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [LoginController::class, 'registerSaas'])->name('register.saas');

    // 2. Alur Portal Member Keanggotaan Gym (Member Gym)
    Route::get('/member/login', [LoginController::class, 'showMemberLoginForm'])->name('member.login');
    Route::post('/member/login', [LoginController::class, 'login']);
    Route::get('/member/register', [LoginController::class, 'showMemberRegisterForm'])->name('member.register');
    Route::post('/member/register', [LoginController::class, 'registerMember'])->name('member.register.submit');

    // Lupa Password (self-service reset, tanpa campur tangan admin)
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

// Route Logout (Bisa diakses via GET & POST, tanpa error 419)
Route::match(['get', 'post'], '/logout', [LoginController::class, 'logout'])->name('logout');

// Verifikasi Email (user yang baru login tapi emailnya belum diverifikasi diarahkan ke sini)
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [EmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend'])->middleware('throttle:6,1')->name('verification.send');
    
    // Ubah Password Wajib (untuk staf baru)
    Route::get('/change-password', [\App\Http\Controllers\ChangePasswordController::class, 'show'])->name('password.change');
    Route::post('/change-password', [\App\Http\Controllers\ChangePasswordController::class, 'update'])->name('password.change.update');

    // Pengaturan Akun (Profil & Ganti Password)
    Route::get('/account/settings', [\App\Http\Controllers\AccountSettingController::class, 'index'])->name('account.settings');
    Route::post('/account/profile', [\App\Http\Controllers\AccountSettingController::class, 'updateProfile'])->name('account.profile.update');
    Route::post('/account/password', [\App\Http\Controllers\AccountSettingController::class, 'updatePassword'])->name('account.password.update');
});

// Link verifikasi bertanda tangan (signed URL) — bisa dibuka langsung dari email tanpa login
Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])->middleware('signed')->name('verification.verify');

use App\Http\Controllers\SuperadminController;
use App\Http\Controllers\TenantOnboardingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminStaffController;
use App\Http\Controllers\AdminSettingController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\ManagerMemberController;
use App\Http\Controllers\ManagerClassController;
use App\Http\Controllers\ManagerStaffController;
use App\Http\Controllers\ReceptionistController;
use App\Http\Controllers\ReceptionistCheckInController;
use App\Http\Controllers\ReceptionistPosController;
use App\Http\Controllers\ReceptionistLockerController;
use App\Http\Controllers\TrainerController;

// Group Onboarding Setup Website (Khusus Admin yang belum punya website)
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('onboarding')->group(function () {
    Route::get('/setup', [TenantOnboardingController::class, 'show'])->name('tenant.onboarding');
    Route::post('/setup', [TenantOnboardingController::class, 'provision'])->name('tenant.onboarding.provision');
});

// Group Superadmin (Hanya bisa diakses jika sudah login)
Route::middleware(['auth', 'verified', 'role:superadmin'])->prefix('superadmin')->group(function () {
    Route::get('/dashboard', [SuperadminController::class, 'dashboard'])->name('superadmin.dashboard');
    Route::get('/registrations', [SuperadminController::class, 'registrations'])->name('superadmin.registrations');
    Route::post('/registrations/{id}/approve', [SuperadminController::class, 'approveRegistration'])->name('superadmin.registrations.approve');
    Route::post('/registrations/{id}/status', [SuperadminController::class, 'updateRegistrationStatus'])->name('superadmin.registrations.status');
    Route::delete('/registrations/{id}', [SuperadminController::class, 'destroyRegistration'])->name('superadmin.registrations.destroy');
    Route::get('/tenants', [SuperadminController::class, 'tenants'])->name('superadmin.tenants');
    Route::post('/tenants', [SuperadminController::class, 'storeTenant'])->name('superadmin.tenants.store');
    Route::post('/tenants/{id}/features', [SuperadminController::class, 'updateTenantFeatures'])->name('superadmin.tenants.features');
    Route::post('/tenants/{id}/toggle-status', [SuperadminController::class, 'toggleTenantStatus'])->name('superadmin.tenants.toggle-status');
    Route::delete('/tenants/{id}', [SuperadminController::class, 'destroyTenant'])->name('superadmin.tenants.destroy');
    Route::get('/plans', [SuperadminController::class, 'plans'])->name('superadmin.plans');
    Route::post('/plans', [SuperadminController::class, 'storePlan'])->name('superadmin.plans.store');
    Route::put('/plans/{id}', [SuperadminController::class, 'updatePlan'])->name('superadmin.plans.update');
    Route::post('/plans/{id}/toggle-status', [SuperadminController::class, 'togglePlanStatus'])->name('superadmin.plans.toggle-status');
    Route::delete('/plans/{id}', [SuperadminController::class, 'destroyPlan'])->name('superadmin.plans.destroy');
    Route::get('/billing', [SuperadminController::class, 'billing'])->name('superadmin.billing');
    Route::post('/billing/{id}/approve', [SuperadminController::class, 'approveRenewal'])->name('superadmin.billing.approve');
    Route::post('/billing/{id}/reject', [SuperadminController::class, 'rejectRenewal'])->name('superadmin.billing.reject');
    Route::post('/billing/{id}/verify-dp', [SuperadminController::class, 'verifyDpPayment'])->name('superadmin.billing.verify-dp');
    Route::post('/billing/{id}/verify', [SuperadminController::class, 'verifyInvoice'])->name('superadmin.billing.verify');
    Route::get('/announcements', [SuperadminController::class, 'announcements'])->name('superadmin.announcements');
    Route::get('/logs', [SuperadminController::class, 'logs'])->name('superadmin.logs');
    Route::get('/settings', [SuperadminController::class, 'settings'])->name('superadmin.settings');
    Route::get('/profile', [SuperadminController::class, 'profile'])->name('superadmin.profile');
    Route::post('/clear-cache', [SuperadminController::class, 'clearCache'])->name('superadmin.clear-cache');
});

// Group Admin Tenant (Hanya fokus pada Pengelolaan Web SaaS & Hubungan Superadmin)
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->group(function () {
    // 1. Dashboard Admin
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // 2. Akun Staf (RBAC - Manager & Owner)
    Route::get('/staff', [AdminStaffController::class, 'index'])->name('admin.staff.index');
    Route::post('/staff', [AdminStaffController::class, 'store'])->name('admin.staff.store');
    Route::post('/staff/{id}/send-verification', [AdminStaffController::class, 'sendVerification'])->name('admin.staff.send-verification');
    Route::delete('/staff/{id}', [AdminStaffController::class, 'destroy'])->name('admin.staff.destroy');

    // 3. Pengaturan Gym
    Route::get('/settings', [AdminSettingController::class, 'index'])->name('admin.settings.index');
    Route::post('/settings', [AdminSettingController::class, 'update'])->name('admin.settings.update');

    // 4. Landing Page Publik Tenant (kustomisasi sesuai paket langganan)
    Route::get('/landing', [TenantLandingController::class, 'edit'])->name('admin.landing.edit');
    Route::post('/landing', [TenantLandingController::class, 'update'])->name('admin.landing.update');

    // 5. Pembayaran & Melanjutkan Penyewaan Web ke Superadmin
    Route::get('/subscription', [\App\Http\Controllers\AdminSubscriptionController::class, 'index'])->name('admin.subscription.index');
    Route::post('/subscription/pay', [\App\Http\Controllers\AdminSubscriptionController::class, 'pay'])->name('admin.subscription.pay');
});

// Group Manager Gym & Akses Bersama Staf Operasional
Route::middleware(['auth', 'verified', 'role:manager,receptionist,trainer'])->prefix('manager')->group(function () {
    // 1. Member Management (Manager approval, Receptionist registration, Trainer view)
    Route::get('/members', [ManagerMemberController::class, 'index'])->name('manager.members.index');
    Route::post('/members', [ManagerMemberController::class, 'store'])->name('manager.members.store');
    Route::put('/members/{id}', [ManagerMemberController::class, 'update'])->name('manager.members.update');
    Route::post('/members/{id}/approve', [ManagerMemberController::class, 'approve'])->name('manager.members.approve');
    Route::delete('/members/{id}', [ManagerMemberController::class, 'destroy'])->name('manager.members.destroy');
    Route::get('/members/{id}/history', [ManagerMemberController::class, 'history'])->name('manager.members.history');

    // 2. Kelas & Trainer (Manager edit, Trainer view)
    Route::get('/classes', [ManagerClassController::class, 'index'])->name('manager.classes.index');
    Route::post('/classes', [ManagerClassController::class, 'storeClass'])->name('manager.classes.store');
    Route::put('/classes/{id}', [ManagerClassController::class, 'updateClass'])->name('manager.classes.update');
    Route::delete('/classes/{id}', [ManagerClassController::class, 'destroyClass'])->name('manager.classes.destroy');
    Route::post('/trainers', [ManagerClassController::class, 'storeTrainer'])->name('manager.classes.store-trainer');
    Route::put('/trainers/{id}', [ManagerClassController::class, 'updateTrainer'])->name('manager.classes.update-trainer');
    Route::delete('/trainers/{id}', [ManagerClassController::class, 'destroyTrainer'])->name('manager.classes.destroy-trainer');
});

// Group Manager Khusus (Fitur Manajerial Strategis & Dashboard Manager)
Route::middleware(['auth', 'verified', 'role:manager'])->prefix('manager')->group(function () {
    Route::get('/dashboard', [ManagerController::class, 'dashboard'])->name('manager.dashboard');
    Route::get('/features', [ManagerController::class, 'features'])->name('manager.features');

    // Promo & Voucher Diskon
    Route::post('/promo', [ManagerController::class, 'storePromo'])->name('manager.promo.store');
    Route::put('/promo/{id}', [ManagerController::class, 'updatePromo'])->name('manager.promo.update');
    Route::delete('/promo/{id}', [ManagerController::class, 'destroyPromo'])->name('manager.promo.destroy');

    // Perencanaan Master Kelas
    Route::post('/master-classes', [ManagerController::class, 'storeMasterClass'])->name('manager.master-classes.store');
    Route::put('/master-classes/{id}', [ManagerController::class, 'updateMasterClass'])->name('manager.master-classes.update');
    Route::delete('/master-classes/{id}', [ManagerController::class, 'destroyMasterClass'])->name('manager.master-classes.destroy');

    // Vendors Database
    Route::post('/vendors', [ManagerController::class, 'storeVendor'])->name('manager.vendors.store');
    Route::put('/vendors/{id}', [ManagerController::class, 'updateVendor'])->name('manager.vendors.update');
    Route::delete('/vendors/{id}', [ManagerController::class, 'destroyVendor'])->name('manager.vendors.destroy');

    // Akun Staf Operasional (Supervisor, Resepsionis/Kasir, dan Personal Trainer)
    Route::get('/staff', [ManagerStaffController::class, 'index'])->name('manager.staff.index');
    Route::post('/staff', [ManagerStaffController::class, 'store'])->name('manager.staff.store');
    Route::post('/staff/{id}/send-verification', [ManagerStaffController::class, 'sendVerification'])->name('manager.staff.send-verification');
    Route::delete('/staff/{id}', [ManagerStaffController::class, 'destroy'])->name('manager.staff.destroy');
});

// Group Supervisor (Pengawas Operasional Lapangan Gym)
Route::middleware(['auth', 'verified', 'role:supervisor'])->prefix('supervisor')->group(function () {
    Route::get('/dashboard', [SupervisorController::class, 'dashboard'])->name('supervisor.dashboard');
    Route::get('/features', [SupervisorController::class, 'features'])->name('supervisor.features');

    // Otorisasi & Void Kasir
    Route::post('/void/{id}/approve', [SupervisorController::class, 'approveVoid'])->name('supervisor.void.approve');
    Route::post('/void/{id}/reject', [SupervisorController::class, 'rejectVoid'])->name('supervisor.void.reject');

    // Penjadwalan Shift & Cuti Staf
    Route::post('/shifts', [SupervisorController::class, 'storeShift'])->name('supervisor.shifts.store');
    Route::put('/shifts/{id}', [SupervisorController::class, 'updateShift'])->name('supervisor.shifts.update');
    Route::delete('/shifts/{id}', [SupervisorController::class, 'destroyShift'])->name('supervisor.shifts.destroy');
    Route::post('/leave', [SupervisorController::class, 'storeLeave'])->name('supervisor.leave.store');
    Route::post('/leave/{id}/approve', [SupervisorController::class, 'approveLeave'])->name('supervisor.leave.approve');
    Route::post('/leave/{id}/reject', [SupervisorController::class, 'rejectLeave'])->name('supervisor.leave.reject');

    // Aset & Pemeliharaan Alat Gym
    Route::post('/equipment', [SupervisorController::class, 'storeEquipment'])->name('supervisor.equipment.store');
    Route::put('/equipment/{id}', [SupervisorController::class, 'updateEquipment'])->name('supervisor.equipment.update');
    Route::delete('/equipment/{id}', [SupervisorController::class, 'destroyEquipment'])->name('supervisor.equipment.destroy');
    Route::post('/maintenance-log', [SupervisorController::class, 'storeMaintenanceLog'])->name('supervisor.maintenance.store');

    // Penanganan Komplain Member
    Route::put('/complaints/{id}', [SupervisorController::class, 'updateComplaint'])->name('supervisor.complaints.update');
});

// Group Resepsionis / Frontdesk (Operasional Kasir, Checkin, Loker)
Route::middleware(['auth', 'verified', 'role:receptionist,manager', 'receptionist.shift'])->prefix('receptionist')->group(function () {
    Route::get('/dashboard', [ReceptionistController::class, 'dashboard'])->name('receptionist.dashboard');

    // 1. POS Kasir, Struk & Katalog Produk
    Route::get('/pos', [ReceptionistPosController::class, 'index'])->name('receptionist.pos.index');
    Route::post('/pos/checkout', [ReceptionistPosController::class, 'checkout'])->name('receptionist.pos.checkout');
    Route::get('/pos/invoice/{id}', [ReceptionistPosController::class, 'invoiceData'])->name('receptionist.pos.invoice');
    Route::post('/pos/void/{id}', [ReceptionistPosController::class, 'requestVoid'])->name('receptionist.pos.void');
    Route::post('/products', [ReceptionistPosController::class, 'storeProduct'])->name('receptionist.products.store');
    Route::put('/products/{id}', [ReceptionistPosController::class, 'updateProduct'])->name('receptionist.products.update');
    Route::delete('/products/{id}', [ReceptionistPosController::class, 'destroyProduct'])->name('receptionist.products.destroy');

    // 2. Check-In Absensi
    Route::get('/checkin', [ReceptionistCheckInController::class, 'index'])->name('receptionist.checkin.index');
    Route::post('/checkin/process', [ReceptionistCheckInController::class, 'processCheckIn'])->name('receptionist.checkin.process');
    Route::post('/checkin/manual', [ReceptionistCheckInController::class, 'manualCheckIn'])->name('receptionist.checkin.manual');
    Route::delete('/checkin/{id}', [ReceptionistCheckInController::class, 'destroyCheckIn'])->name('receptionist.checkin.destroy');

    // 3. Master Loker (Data Master Loker Gym)
    Route::get('/master-lockers', [ReceptionistLockerController::class, 'index'])->name('receptionist.lockers.index');
    Route::post('/master-lockers', [ReceptionistLockerController::class, 'store'])->name('receptionist.lockers.store');
    Route::put('/master-lockers/{id}', [ReceptionistLockerController::class, 'update'])->name('receptionist.lockers.update');
    Route::delete('/master-lockers/{id}', [ReceptionistLockerController::class, 'destroy'])->name('receptionist.lockers.destroy');

    // 4. Loker Peminjaman & Pengembalian
    Route::get('/lockers', [ReceptionistController::class, 'lockers'])->name('receptionist.lockers');
    Route::post('/lockers/assign', [ReceptionistController::class, 'assignLocker'])->name('receptionist.lockers.assign');
    Route::post('/lockers/{id}/return', [ReceptionistController::class, 'returnLocker'])->name('receptionist.lockers.return');

    // 5. Buku Tamu (Walk-in Leads)
    Route::get('/guests', [ReceptionistController::class, 'guests'])->name('receptionist.guests');
    Route::post('/guests/store', [ReceptionistController::class, 'storeGuest'])->name('receptionist.guests.store');
    Route::post('/guests/{id}/convert', [ReceptionistController::class, 'convertGuestToMember'])->name('receptionist.guests.convert');

    // 6. Log Barang Tertinggal (Lost & Found)
    Route::get('/lost-found', [ReceptionistController::class, 'lostFound'])->name('receptionist.lost-found');
    Route::post('/lost-found/store', [ReceptionistController::class, 'storeLostFound'])->name('receptionist.lost-found.store');
    Route::post('/lost-found/{id}/claim', [ReceptionistController::class, 'claimLostFound'])->name('receptionist.lost-found.claim');

    // 7. Shift Kasir
    Route::get('/shifts', [ReceptionistController::class, 'shifts'])->name('receptionist.shifts');
    Route::post('/shifts/start', [ReceptionistController::class, 'startShift'])->name('receptionist.shifts.start');
    Route::post('/shifts/{id}/end', [ReceptionistController::class, 'endShift'])->name('receptionist.shifts.end');
    Route::post('/shifts/close-logout', [ReceptionistController::class, 'closeShiftAndLogout'])->name('receptionist.shifts.close-logout');

    // 8. Keluhan & Komplain Member
    Route::get('/complaints', [ReceptionistController::class, 'complaints'])->name('receptionist.complaints');
    Route::post('/complaints/store', [ReceptionistController::class, 'storeComplaint'])->name('receptionist.complaints.store');

    // 9. Booking Kelas & PT Check-In
    Route::post('/pt/checkin', [ReceptionistController::class, 'checkInTrainerSession'])->name('receptionist.pt.checkin');
});

// Group Personal Trainer (Pelatih Olahraga & Kebugaran)
Route::middleware(['auth', 'verified', 'role:trainer'])->prefix('trainer')->group(function () {
    Route::get('/dashboard', [TrainerController::class, 'dashboard'])->name('trainer.dashboard');
    Route::get('/pt-sessions', [TrainerController::class, 'ptSessions'])->name('trainer.pt-sessions');
    Route::post('/pt-sessions/{id}/status', [TrainerController::class, 'updatePtStatus'])->name('trainer.pt-sessions.status');
    Route::get('/classes', [TrainerController::class, 'classes'])->name('trainer.classes');
    Route::get('/rsvps', [TrainerController::class, 'rsvps'])->name('trainer.rsvps');
    Route::post('/rsvps/{id}/status', [TrainerController::class, 'updateRsvpStatus'])->name('trainer.rsvps.status');
});

use App\Http\Controllers\OwnerController;
use App\Http\Controllers\MemberPortalController;

// Group Pemilik Gym (Owner) - Mode Pemantauan Eksekutif (Read-Only)
Route::middleware(['auth', 'verified', 'role:owner'])->prefix('owner')->group(function () {
    Route::get('/dashboard', [OwnerController::class, 'dashboard'])->name('owner.dashboard');
    Route::get('/transactions', [OwnerController::class, 'transactions'])->name('owner.transactions');
    Route::get('/performance', [OwnerController::class, 'performance'])->name('owner.performance');
    Route::get('/classes', [OwnerController::class, 'classes'])->name('owner.classes');
    Route::get('/inventory', [OwnerController::class, 'inventory'])->name('owner.inventory');
    Route::get('/staff', [OwnerController::class, 'staff'])->name('owner.staff');
    Route::get('/logs', [OwnerController::class, 'logs'])->name('owner.logs');
    Route::get('/reports', [OwnerController::class, 'reports'])->name('owner.reports');
    Route::get('/settings', [OwnerController::class, 'settings'])->name('owner.settings');
});

// Group User / Member Gym Portal
Route::middleware(['auth', 'verified', 'role:member'])->prefix('member')->group(function () {
    Route::get('/dashboard', [MemberPortalController::class, 'dashboard'])->name('member.dashboard');

    // Loker & Sewa Visual Grid
    Route::get('/lockers', [MemberPortalController::class, 'lockers'])->name('member.lockers');
    Route::post('/lockers/rent', [MemberPortalController::class, 'rentLocker'])->name('member.lockers.rent');
    Route::post('/lockers/return/{id}', [MemberPortalController::class, 'returnLocker'])->name('member.lockers.return');

    // Multi-Tier Membership
    Route::get('/membership', [MemberPortalController::class, 'membership'])->name('member.membership');
    Route::post('/membership/upgrade', [MemberPortalController::class, 'upgradeMembership'])->name('member.membership.upgrade');

    // Personal Trainer (PT) Quotas & Booking
    Route::get('/pt', [MemberPortalController::class, 'pt'])->name('member.pt');
    Route::post('/pt/buy-quota', [MemberPortalController::class, 'buyPtQuota'])->name('member.pt.buy_quota');
    Route::post('/pt/book', [MemberPortalController::class, 'bookPt'])->name('member.pt.book');

    // Class RSVP & Waitlist
    Route::get('/classes', [MemberPortalController::class, 'classes'])->name('member.classes');
    Route::post('/classes/rsvp', [MemberPortalController::class, 'rsvpClass'])->name('member.classes.rsvp');
    Route::post('/classes/cancel/{id}', [MemberPortalController::class, 'cancelRsvp'])->name('member.classes.cancel');

    // Billing & Tagihan
    Route::get('/billing', [MemberPortalController::class, 'billing'])->name('member.billing');

    // Panduan Penggunaan & Pengaturan Akun Member
    Route::get('/guide', [MemberPortalController::class, 'guide'])->name('member.guide');
    Route::get('/settings', [MemberPortalController::class, 'settings'])->name('member.settings');
    Route::post('/settings/profile', [MemberPortalController::class, 'updateProfile'])->name('member.settings.update_profile');
    Route::post('/settings/password', [MemberPortalController::class, 'updatePassword'])->name('member.settings.update_password');
});
