<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Member;
use App\Models\PosTransaction;
use App\Models\CheckIn;
use App\Models\Locker;
use App\Models\GymClass;
use App\Models\Trainer;
use App\Models\StaffLog;
use App\Models\Product;
use App\Models\User;
use App\Models\GymEquipment;
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

    /**
     * Pemantauan Seluruh Transaksi Kasir POS (Read-Only).
     */
    public function transactions(Request $request)
    {
        $tenant = Auth::user()->tenant;
        $tenantId = $tenant->id;

        $query = PosTransaction::where('tenant_id', $tenantId)->with(['items', 'user']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('invoice_number', 'like', "%{$search}%");
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        $transactions = $query->latest()->paginate(10)->withQueryString();

        $totalRevenue = PosTransaction::where('tenant_id', $tenantId)->sum('total_amount');
        $todayRevenue = PosTransaction::where('tenant_id', $tenantId)->whereDate('created_at', Carbon::today())->sum('total_amount');
        $transactionCount = PosTransaction::where('tenant_id', $tenantId)->count();

        return view('owner.transactions', compact('tenant', 'transactions', 'totalRevenue', 'todayRevenue', 'transactionCount'));
    }

    /**
     * Pemantauan Data & Status Member (Read-Only).
     */
    public function members(Request $request)
    {
        $tenant = Auth::user()->tenant;
        $tenantId = $tenant->id;

        $query = Member::where('tenant_id', $tenantId);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('access_code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $members = $query->latest()->paginate(10)->withQueryString();
        $totalMembers = Member::where('tenant_id', $tenantId)->count();
        $activeMembers = Member::where('tenant_id', $tenantId)->where('status', 'active')->count();

        return view('owner.members', compact('tenant', 'members', 'totalMembers', 'activeMembers'));
    }

    /**
     * Pemantauan Jadwal Kelas & Trainer (Read-Only).
     */
    public function classes(Request $request)
    {
        $tenant = Auth::user()->tenant;
        $tenantId = $tenant->id;

        $classes = GymClass::where('tenant_id', $tenantId)->with('trainer')->get();
        $trainers = Trainer::where('tenant_id', $tenantId)->get();

        return view('owner.classes', compact('tenant', 'classes', 'trainers'));
    }

    /**
     * Pemantauan Inventaris Produk & Loker Gym (Read-Only).
     */
    public function inventory(Request $request)
    {
        $tenant = Auth::user()->tenant;
        $tenantId = $tenant->id;

        $products = Product::where('tenant_id', $tenantId)->latest()->get();
        $lockers = Locker::where('tenant_id', $tenantId)->get();
        $equipments = GymEquipment::where('tenant_id', $tenantId)->get();

        $occupiedLockers = $lockers->where('status', 'terpakai')->count();
        $availableLockers = $lockers->where('status', 'tersedia')->count();
        $brokenLockers = $lockers->where('status', 'rusak')->count();

        return view('owner.inventory', compact('tenant', 'products', 'lockers', 'equipments', 'occupiedLockers', 'availableLockers', 'brokenLockers'));
    }

    /**
     * Pemantauan Akun Staf & Role (Read-Only).
     */
    public function staff(Request $request)
    {
        $tenant = Auth::user()->tenant;
        $tenantId = $tenant->id;

        $staffList = User::where('tenant_id', $tenantId)->latest()->get();

        return view('owner.staff', compact('tenant', 'staffList'));
    }

    /**
     * Pemantauan Audit Trail Log Aktivitas Staf (Read-Only).
     */
    public function logs(Request $request)
    {
        $tenant = Auth::user()->tenant;
        $tenantId = $tenant->id;

        $query = StaffLog::where('tenant_id', $tenantId)->with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $logs = $query->latest()->paginate(15)->withQueryString();

        return view('owner.logs', compact('tenant', 'logs'));
    }

    /**
     * Pusat Laporan Eksekutif & Omset (Read-Only).
     */
    public function reports(Request $request)
    {
        $tenant = Auth::user()->tenant;
        $tenantId = $tenant->id;

        $totalRevenue = PosTransaction::where('tenant_id', $tenantId)->sum('total_amount');
        $monthRevenue = PosTransaction::where('tenant_id', $tenantId)->whereMonth('created_at', Carbon::now()->month)->sum('total_amount');
        $totalMembers = Member::where('tenant_id', $tenantId)->count();
        $activeMembers = Member::where('tenant_id', $tenantId)->where('status', 'active')->count();

        $recentTransactions = PosTransaction::where('tenant_id', $tenantId)->with(['items', 'user'])->latest()->take(8)->get();

        return view('owner.reports', compact('tenant', 'totalRevenue', 'monthRevenue', 'totalMembers', 'activeMembers', 'recentTransactions'));
    }

    /**
     * Informasi Profil Tenant Gym (Read-Only).
     */
    public function settings()
    {
        $tenant = Auth::user()->tenant;
        return view('owner.settings', compact('tenant'));
    }
}
