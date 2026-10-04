<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\GymClass;
use App\Models\Trainer;
use App\Models\PromoCode;
use App\Models\Vendor;
use App\Models\User;
use App\Models\Member;
use App\Models\PosTransaction;
use App\Models\CheckIn;
use App\Models\StaffLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ManagerController extends Controller
{
    /**
     * Dashboard Eksekutif Manager Gym (Fokus Strategis & Manajerial)
     */
    public function dashboard()
    {
        $user = Auth::user();
        $tenant = $user->tenant;
        $managerProfile = $user->manager;

        if (!$tenant) {
            return view('manager.dashboard', [
                'user' => $user,
                'tenant' => null,
                'managerProfile' => null,
                'checkinsToday' => 0,
                'revenueToday' => 0,
                'revenueMonth' => 0,
                'totalMembers' => 0,
                'recentStaffLogs' => collect(),
            ]);
        }

        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();

        $checkinsToday = CheckIn::where('tenant_id', $tenant->id)
            ->whereDate('checked_in_at', $today)
            ->count();

        $revenueToday = PosTransaction::where('tenant_id', $tenant->id)
            ->whereDate('created_at', $today)
            ->sum('total_amount');

        $revenueMonth = PosTransaction::where('tenant_id', $tenant->id)
            ->where('created_at', '>=', $startOfMonth)
            ->sum('total_amount');

        $totalMembers = Member::where('tenant_id', $tenant->id)->count();

        $recentStaffLogs = StaffLog::where('tenant_id', $tenant->id)
            ->with('user')
            ->latest()
            ->take(10)
            ->get();

        return view('manager.dashboard', compact(
            'user',
            'tenant',
            'managerProfile',
            'checkinsToday',
            'revenueToday',
            'revenueMonth',
            'totalMembers',
            'recentStaffLogs'
        ));
    }

    /**
     * Fitur Strategis & Manajerial Manager Gym:
     * 1. Program Promo & Voucher Diskon
     * 2. Pantauan Evaluasi Kinerja Karyawan
     * 3. Rekapitulasi Kas Keuangan Harian
     * 4. Database Mitra / Vendor Eksternal
     */
    public function features(Request $request)
    {
        $user = Auth::user();
        $tenant = $user->tenant;
        $activeTab = $request->query('tab', 'promo');

        // 1. Manajemen Promo & Voucher Diskon
        $promoCodes = PromoCode::where('tenant_id', $tenant->id)->latest()->get();

        // 2. Pantauan Kinerja Karyawan & Target Omset
        $receptionistPerformance = PosTransaction::where('tenant_id', $tenant->id)
            ->where('created_at', '>=', Carbon::now()->startOfMonth())
            ->select('user_id', DB::raw('SUM(total_amount) as total_sales'), DB::raw('COUNT(*) as total_transactions'))
            ->groupBy('user_id')
            ->with('user')
            ->orderBy('total_sales', 'desc')
            ->get();

        $trainerPerformance = GymClass::where('tenant_id', $tenant->id)
            ->select('trainer_id', DB::raw('COUNT(*) as total_classes'))
            ->whereNotNull('trainer_id')
            ->groupBy('trainer_id')
            ->with('trainer')
            ->orderBy('total_classes', 'desc')
            ->get();

        // 3. Laporan Rekapitulasi Kas Harian
        $dailyCashRecap = PosTransaction::where('tenant_id', $tenant->id)
            ->whereDate('created_at', Carbon::today())
            ->get();

        // 4. Database Vendor & Mitra Eksternal
        $vendors = Vendor::where('tenant_id', $tenant->id)->latest()->get();

        return view('manager.features', compact(
            'user',
            'tenant',
            'activeTab',
            'promoCodes',
            'receptionistPerformance',
            'trainerPerformance',
            'dailyCashRecap',
            'vendors'
        ));
    }

    // ==========================================
    // 2. PROMO & VOUCHER DISKON
    // ==========================================
    public function storePromo(Request $request)
    {
        $tenant = Auth::user()->tenant;
        $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('promo_codes', 'code')->where('tenant_id', $tenant->id)],
            'description' => 'nullable|string|max:500',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'min_purchase' => 'required|numeric|min:0',
            'max_uses' => 'required|integer|min:1',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after_or_equal:valid_from',
        ]);

        PromoCode::create(array_merge($request->all(), ['tenant_id' => $tenant->id, 'is_active' => true]));

        return redirect()->route('manager.features', ['tab' => 'promo'])->with('success', 'Kode voucher promo berhasil dibuat.');
    }

    public function updatePromo(Request $request, $id)
    {
        $tenant = Auth::user()->tenant;
        $promo = PromoCode::where('tenant_id', $tenant->id)->findOrFail($id);
        $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('promo_codes', 'code')->where('tenant_id', $tenant->id)->ignore($promo->id)],
            'description' => 'nullable|string|max:500',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'min_purchase' => 'required|numeric|min:0',
            'max_uses' => 'required|integer|min:1',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after_or_equal:valid_from',
        ]);

        $promo->update(array_merge($request->except(['tenant_id']), ['is_active' => $request->has('is_active')]));

        return redirect()->route('manager.features', ['tab' => 'promo'])->with('success', 'Voucher promo berhasil diperbarui.');
    }

    public function destroyPromo($id)
    {
        $tenant = Auth::user()->tenant;
        $promo = PromoCode::where('tenant_id', $tenant->id)->findOrFail($id);
        $promo->delete();

        return redirect()->route('manager.features', ['tab' => 'promo'])->with('success', 'Voucher promo berhasil dihapus.');
    }

    // ==========================================
    // 3. DATABASE VENDOR & MITRA
    // ==========================================
    public function storeVendor(Request $request)
    {
        $tenant = Auth::user()->tenant;
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'category' => 'required|string',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        Vendor::create(array_merge($request->all(), ['tenant_id' => $tenant->id]));

        return redirect()->route('manager.features', ['tab' => 'vendor'])->with('success', 'Kontak vendor mitra berhasil disimpan.');
    }

    public function updateVendor(Request $request, $id)
    {
        $tenant = Auth::user()->tenant;
        $vendor = Vendor::where('tenant_id', $tenant->id)->findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'category' => 'required|string',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $vendor->update($request->except(['tenant_id']));

        return redirect()->route('manager.features', ['tab' => 'vendor'])->with('success', 'Kontak vendor berhasil diperbarui.');
    }

    public function destroyVendor($id)
    {
        $tenant = Auth::user()->tenant;
        $vendor = Vendor::where('tenant_id', $tenant->id)->findOrFail($id);
        $vendor->delete();

        return redirect()->route('manager.features', ['tab' => 'vendor'])->with('success', 'Kontak vendor berhasil dihapus.');
    }
}
