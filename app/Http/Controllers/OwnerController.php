<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Member;
use App\Models\PosTransaction;
use App\Models\CheckIn;
use App\Models\Locker;
use App\Models\GymClass;
use App\Models\StaffLog;
use App\Models\Product;
use Carbon\Carbon;

class OwnerController extends Controller
{
    /**
     * Tampilkan Halaman Dasbor Pemantauan Eksekutif Khusus Owner.
     */
    public function dashboard()
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        if (!$tenant) {
            return redirect('/login')->with('error', 'Akun Owner belum terhubung ke tenant gym.');
        }

        $tenantId = $tenant->id;

        // 1. Ringkasan Keuangan & Omset
        $revenueToday = PosTransaction::where('tenant_id', $tenantId)
            ->whereDate('created_at', Carbon::today())
            ->sum('total_amount');

        $revenueMonth = PosTransaction::where('tenant_id', $tenantId)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total_amount');

        $totalTransactionsMonth = PosTransaction::where('tenant_id', $tenantId)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // 2. Statistik Member
        $totalMembers = Member::where('tenant_id', $tenantId)->count();
        $activeMembers = Member::where('tenant_id', $tenantId)->where('status', 'active')->count();
        $expiringSoonMembers = Member::where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->whereBetween('expired_at', [Carbon::now(), Carbon::now()->addDays(7)])
            ->count();

        // 3. Kunjungan / Presensi Presensi Hari Ini
        $checkinsToday = CheckIn::where('tenant_id', $tenantId)
            ->whereDate('checked_in_at', Carbon::today())
            ->count();

        $recentCheckins = CheckIn::where('tenant_id', $tenantId)
            ->with('member')
            ->latest('checked_in_at')
            ->take(5)
            ->get();

        // 4. Okupansi Loker Gym
        $totalLockers = Locker::where('tenant_id', $tenantId)->count();
        $occupiedLockers = Locker::where('tenant_id', $tenantId)->where('status', 'terpakai')->count();
        $brokenLockers = Locker::where('tenant_id', $tenantId)->where('status', 'rusak')->count();
        $availableLockers = Locker::where('tenant_id', $tenantId)->where('status', 'tersedia')->count();

        // 5. Kelas & Trainer Popularity
        $gymClasses = GymClass::where('tenant_id', $tenantId)
            ->with('trainer')
            ->get();

        // 6. Log Aktivitas Staf (Audit Trail Eksekutif)
        $recentStaffLogs = StaffLog::where('tenant_id', $tenantId)
            ->with('user')
            ->latest()
            ->take(6)
            ->get();

        // 7. Produk Terlaris / Stok Kritis
        $lowStockProducts = Product::where('tenant_id', $tenantId)
            ->where('stock', '<=', 10)
            ->get();

        return view('owner.dashboard', compact(
            'tenant',
            'revenueToday',
            'revenueMonth',
            'totalTransactionsMonth',
            'totalMembers',
            'activeMembers',
            'expiringSoonMembers',
            'checkinsToday',
            'recentCheckins',
            'totalLockers',
            'occupiedLockers',
            'brokenLockers',
            'availableLockers',
            'gymClasses',
            'recentStaffLogs',
            'lowStockProducts'
        ));
    }
}
