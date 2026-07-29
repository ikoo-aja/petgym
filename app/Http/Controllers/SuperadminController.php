<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\Plan;
use App\Models\Invoice;
use App\Models\Announcement;
use App\Models\SystemLog;
use Illuminate\Support\Facades\Artisan;

class SuperadminController extends Controller
{
    /**
     * Tampilkan Halaman Dashboard Ringkasan.
     */
    public function dashboard()
    {
        $activeTenantsCount = Tenant::where('status', 'active')->count();
        $totalTenantsCount = Tenant::count();
        $suspendedTenantsCount = Tenant::where('status', 'suspended')->count();
        $monthlyIncome = Invoice::where('status', 'paid')->sum('amount');
        $newTenantsCount = Tenant::whereMonth('joined_at', now()->month)
                                 ->whereYear('joined_at', now()->year)
                                 ->count();
        $expiringTenantsCount = Tenant::where('status', 'active')
                                      ->whereBetween('expires_at', [now(), now()->addDays(7)])
                                      ->count();

        $recentLogs = SystemLog::latest()->take(5)->get();

        return view('superadmin.dashboard', compact(
            'activeTenantsCount',
            'totalTenantsCount',
            'suspendedTenantsCount',
            'monthlyIncome',
            'newTenantsCount',
            'expiringTenantsCount',
            'recentLogs'
        ));
    }

    /**
     * Tampilkan Halaman Kelola Penyewa.
     */
    public function tenants(Request $request)
    {
        $query = Tenant::query();

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('subdomain', 'like', "%{$search}%")
                  ->orWhere('owner_name', 'like', "%{$search}%")
                  ->orWhere('owner_email', 'like', "%{$search}%");
            });
        }

        $tenants = $query->latest('id')->paginate(4)->withQueryString();

        return view('superadmin.tenants', compact('tenants'));
    }

    /**
     * Tampilkan Halaman Paket Sewa.
     */
    public function plans()
    {
        $plans = Plan::all();
        return view('superadmin.plans', compact('plans'));
    }

    /**
     * Tampilkan Halaman Keuangan & Tagihan.
     */
    public function billing(Request $request)
    {
        $query = Invoice::with('tenant');
        $status = $request->query('status', 'all');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $invoices = $query->latest('id')->get();

        return view('superadmin.billing', compact('invoices', 'status'));
    }

    /**
     * Tampilkan Halaman Pengumuman.
     */
    public function announcements()
    {
        $announcements = Announcement::latest('id')->get();
        return view('superadmin.announcements', compact('announcements'));
    }

    /**
     * Tampilkan Halaman System Logs.
     */
    public function logs()
    {
        $logs = SystemLog::latest('id')->get();
        return view('superadmin.logs', compact('logs'));
    }

    /**
     * Tampilkan Halaman Pengaturan.
     */
    public function settings()
    {
        return view('superadmin.settings');
    }

    /**
     * Tampilkan Halaman Profil.
     */
    public function profile()
    {
        return view('superadmin.profile');
    }

    /**
     * Proses Pembersihan Cache Aplikasi.
     */
    public function clearCache()
    {
        try {
            Artisan::call('optimize:clear');
            $message = 'System cache & optimization cleared successfully!';
        } catch (\Exception $e) {
            $message = 'Cache clear simulated/completed (with notes: ' . $e->getMessage() . ')';
        }

        return redirect()->back()->with('success', $message);
    }
}
