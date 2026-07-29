<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;

Route::get('/', function () {
    return view('welcome');
});



// Group Guest (Hanya bisa diakses jika belum login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Route Logout (Hanya bisa diakses jika sudah login)
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

use App\Http\Controllers\SuperadminController;

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
