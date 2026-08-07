<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', function () {
    return redirect('/admin/dashboard');
});



// Group Guest (Hanya bisa diakses jika belum login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Route Logout (Hanya bisa diakses jika sudah login)
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

use App\Http\Controllers\SuperadminController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminMemberController;
use App\Http\Controllers\AdminPosController;
use App\Http\Controllers\AdminClassController;
use App\Http\Controllers\AdminLockerController;
use App\Http\Controllers\AdminCheckInController;
use App\Http\Controllers\AdminStaffController;
use App\Http\Controllers\AdminSettingController;
use App\Http\Controllers\AdminLogController;
use App\Http\Controllers\AdminReportController;

// Group Superadmin (Hanya bisa diakses jika sudah login)
Route::middleware('auth')->prefix('superadmin')->group(function () {
    Route::get('/dashboard', [SuperadminController::class, 'dashboard'])->name('superadmin.dashboard');
    Route::get('/tenants', [SuperadminController::class, 'tenants'])->name('superadmin.tenants');
    Route::get('/plans', [SuperadminController::class, 'plans'])->name('superadmin.plans');
    Route::get('/billing', [SuperadminController::class, 'billing'])->name('superadmin.billing');
    Route::get('/announcements', [SuperadminController::class, 'announcements'])->name('superadmin.announcements');
    Route::get('/logs', [SuperadminController::class, 'logs'])->name('superadmin.logs');
    Route::get('/settings', [SuperadminController::class, 'settings'])->name('superadmin.settings');
    Route::get('/profile', [SuperadminController::class, 'profile'])->name('superadmin.profile');
    Route::post('/clear-cache', [SuperadminController::class, 'clearCache'])->name('superadmin.clear-cache');
});

// Group Admin Tenant (Hanya bisa diakses jika sudah login)
Route::middleware('auth')->prefix('admin')->group(function () {
    // 1. Dashboard Admin
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // 2. Member Management
    Route::get('/members', [AdminMemberController::class, 'index'])->name('admin.members.index');
    Route::post('/members', [AdminMemberController::class, 'store'])->name('admin.members.store');
    Route::put('/members/{id}', [AdminMemberController::class, 'update'])->name('admin.members.update');
    Route::delete('/members/{id}', [AdminMemberController::class, 'destroy'])->name('admin.members.destroy');
    Route::get('/members/{id}/history', [AdminMemberController::class, 'history'])->name('admin.members.history');

    // 3. POS Kasir, Struk & Katalog Produk
    Route::get('/pos', [AdminPosController::class, 'index'])->name('admin.pos.index');
    Route::post('/pos/checkout', [AdminPosController::class, 'checkout'])->name('admin.pos.checkout');
    Route::get('/pos/invoice/{id}', [AdminPosController::class, 'invoiceData'])->name('admin.pos.invoice');
    Route::post('/pos/void/{id}', [AdminPosController::class, 'requestVoid'])->name('admin.pos.void');
    Route::post('/products', [AdminPosController::class, 'storeProduct'])->name('admin.products.store');
    Route::put('/products/{id}', [AdminPosController::class, 'updateProduct'])->name('admin.products.update');
    Route::delete('/products/{id}', [AdminPosController::class, 'destroyProduct'])->name('admin.products.destroy');

    // Master Loker (Khusus Admin)
    Route::get('/lockers', [AdminLockerController::class, 'index'])->name('admin.lockers.index');
    Route::post('/lockers', [AdminLockerController::class, 'store'])->name('admin.lockers.store');
    Route::put('/lockers/{id}', [AdminLockerController::class, 'update'])->name('admin.lockers.update');
    Route::delete('/lockers/{id}', [AdminLockerController::class, 'destroy'])->name('admin.lockers.destroy');

    // 4. Kelas & Trainer
    Route::get('/classes', [AdminClassController::class, 'index'])->name('admin.classes.index');
    Route::post('/classes', [AdminClassController::class, 'storeClass'])->name('admin.classes.store');
    Route::put('/classes/{id}', [AdminClassController::class, 'updateClass'])->name('admin.classes.update');
    Route::delete('/classes/{id}', [AdminClassController::class, 'destroyClass'])->name('admin.classes.destroy');
    Route::post('/trainers', [AdminClassController::class, 'storeTrainer'])->name('admin.classes.store-trainer');
    Route::put('/trainers/{id}', [AdminClassController::class, 'updateTrainer'])->name('admin.classes.update-trainer');
    Route::delete('/trainers/{id}', [AdminClassController::class, 'destroyTrainer'])->name('admin.classes.destroy-trainer');

    // 5. Check-In Absensi
    Route::get('/checkin', [AdminCheckInController::class, 'index'])->name('admin.checkin.index');
    Route::post('/checkin/process', [AdminCheckInController::class, 'processCheckIn'])->name('admin.checkin.process');
    Route::post('/checkin/manual', [AdminCheckInController::class, 'manualCheckIn'])->name('admin.checkin.manual');
    Route::delete('/checkin/{id}', [AdminCheckInController::class, 'destroyCheckIn'])->name('admin.checkin.destroy');

    // 6. Akun Staf (RBAC)
    Route::get('/staff', [AdminStaffController::class, 'index'])->name('admin.staff.index');
    Route::post('/staff', [AdminStaffController::class, 'store'])->name('admin.staff.store');
    Route::put('/staff/{id}', [AdminStaffController::class, 'update'])->name('admin.staff.update');
    Route::post('/staff/{id}/reset-password', [AdminStaffController::class, 'resetPassword'])->name('admin.staff.reset-password');
    Route::delete('/staff/{id}', [AdminStaffController::class, 'destroy'])->name('admin.staff.destroy');

    // 7. Pengaturan Gym
    Route::get('/settings', [AdminSettingController::class, 'index'])->name('admin.settings.index');
    Route::post('/settings', [AdminSettingController::class, 'update'])->name('admin.settings.update');

    // 8. Audit Trail Log
    Route::get('/logs', [AdminLogController::class, 'index'])->name('admin.logs.index');

    // 9. Ekspor Laporan
    Route::get('/reports', [AdminReportController::class, 'index'])->name('admin.reports.index');
    Route::get('/reports/export-members', [AdminReportController::class, 'exportMembers'])->name('admin.reports.export-members');
    Route::get('/reports/export-checkins', [AdminReportController::class, 'exportCheckIns'])->name('admin.reports.export-checkins');
    Route::get('/reports/export-transactions', [AdminReportController::class, 'exportTransactions'])->name('admin.reports.export-transactions');
});

use App\Http\Controllers\ManagerController;
use App\Http\Controllers\ReceptionistController;
use App\Http\Controllers\TrainerController;

// Group Manager Gym
Route::middleware('auth')->prefix('manager')->group(function () {
    Route::get('/dashboard', [ManagerController::class, 'dashboard'])->name('manager.dashboard');
    Route::get('/features', [ManagerController::class, 'features'])->name('manager.features');

    // 1. Equipment & Maintenance CRUD
    Route::post('/equipment', [ManagerController::class, 'storeEquipment'])->name('manager.equipment.store');
    Route::put('/equipment/{id}', [ManagerController::class, 'updateEquipment'])->name('manager.equipment.update');
    Route::delete('/equipment/{id}', [ManagerController::class, 'destroyEquipment'])->name('manager.equipment.destroy');
    Route::post('/maintenance-log', [ManagerController::class, 'storeMaintenanceLog'])->name('manager.maintenance.store');

    // 2. Staff Shifts & Leaves
    Route::post('/shifts', [ManagerController::class, 'storeShift'])->name('manager.shifts.store');
    Route::put('/shifts/{id}', [ManagerController::class, 'updateShift'])->name('manager.shifts.update');
    Route::delete('/shifts/{id}', [ManagerController::class, 'destroyShift'])->name('manager.shifts.destroy');
    Route::post('/leave/{id}/approve', [ManagerController::class, 'approveLeave'])->name('manager.leave.approve');
    Route::post('/leave/{id}/reject', [ManagerController::class, 'rejectLeave'])->name('manager.leave.reject');

    // 3. Void Otorisasi
    Route::post('/void/{id}/approve', [ManagerController::class, 'approveVoid'])->name('manager.void.approve');
    Route::post('/void/{id}/reject', [ManagerController::class, 'rejectVoid'])->name('manager.void.reject');

    // 4. Promo Codes
    Route::post('/promo', [ManagerController::class, 'storePromo'])->name('manager.promo.store');
    Route::put('/promo/{id}', [ManagerController::class, 'updatePromo'])->name('manager.promo.update');
    Route::delete('/promo/{id}', [ManagerController::class, 'destroyPromo'])->name('manager.promo.destroy');

    // 8. Complaints Ticketing
    Route::put('/complaints/{id}', [ManagerController::class, 'updateComplaint'])->name('manager.complaints.update');

    // 9. Perencanaan Master Kelas
    Route::post('/classes', [ManagerController::class, 'storeMasterClass'])->name('manager.classes.store');
    Route::put('/classes/{id}', [ManagerController::class, 'updateMasterClass'])->name('manager.classes.update');
    Route::delete('/classes/{id}', [ManagerController::class, 'destroyMasterClass'])->name('manager.classes.destroy');

    // 10. Vendors Database
    Route::post('/vendors', [ManagerController::class, 'storeVendor'])->name('manager.vendors.store');
    Route::put('/vendors/{id}', [ManagerController::class, 'updateVendor'])->name('manager.vendors.update');
    Route::delete('/vendors/{id}', [ManagerController::class, 'destroyVendor'])->name('manager.vendors.destroy');
});

// Group Resepsionis / Frontdesk
Route::middleware('auth')->prefix('receptionist')->group(function () {
    Route::get('/dashboard', [ReceptionistController::class, 'dashboard'])->name('receptionist.dashboard');

    // 7. Loker & Peminjaman
    Route::get('/lockers', [ReceptionistController::class, 'lockers'])->name('receptionist.lockers');
    Route::post('/lockers/assign', [ReceptionistController::class, 'assignLocker'])->name('receptionist.lockers.assign');
    Route::post('/lockers/{id}/return', [ReceptionistController::class, 'returnLocker'])->name('receptionist.lockers.return');

    // 8 & 9. Buku Tamu & Lost and Found
    Route::get('/guests', [ReceptionistController::class, 'guests'])->name('receptionist.guests');
    Route::post('/guests/store', [ReceptionistController::class, 'storeGuest'])->name('receptionist.guests.store');
    Route::post('/guests/{id}/convert', [ReceptionistController::class, 'convertGuestToMember'])->name('receptionist.guests.convert');
    Route::post('/lost-found/store', [ReceptionistController::class, 'storeLostFound'])->name('receptionist.lost-found.store');
    Route::post('/lost-found/{id}/claim', [ReceptionistController::class, 'claimLostFound'])->name('receptionist.lost-found.claim');

    // 5. Shift & 6. Keluhan
    Route::get('/shifts', [ReceptionistController::class, 'shifts'])->name('receptionist.shifts');
    Route::post('/shifts/start', [ReceptionistController::class, 'startShift'])->name('receptionist.shifts.start');
    Route::post('/shifts/{id}/end', [ReceptionistController::class, 'endShift'])->name('receptionist.shifts.end');
    Route::post('/complaints/store', [ReceptionistController::class, 'storeComplaint'])->name('receptionist.complaints.store');

    // 4. Booking Kelas & PT Check-In
    Route::post('/pt/checkin', [ReceptionistController::class, 'checkInTrainerSession'])->name('receptionist.pt.checkin');
});

// Group Personal Trainer
Route::middleware('auth')->prefix('trainer')->group(function () {
    Route::get('/dashboard', [TrainerController::class, 'dashboard'])->name('trainer.dashboard');
});
