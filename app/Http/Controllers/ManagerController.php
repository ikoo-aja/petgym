<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
     * 1. Perencanaan Master Kelas
     * 2. Program Promo & Voucher Diskon
     * 3. Pantauan Evaluasi Kinerja Karyawan
     * 4. Rekapitulasi Kas Keuangan Harian
     * 5. Database Mitra / Vendor Eksternal
     */
    public function features(Request $request)
    {
        $user = Auth::user();
        $tenant = $user->tenant;
        $activeTab = $request->query('tab', 'classes');

        // 1. Perencanaan Master Kelas
        $masterClasses = GymClass::where('tenant_id', $tenant->id)->with('trainer')->latest()->get();
        $trainers = Trainer::where('tenant_id', $tenant->id)->get();

        // 2. Manajemen Promo & Voucher Diskon
        $promoCodes = PromoCode::where('tenant_id', $tenant->id)->latest()->get();

        // 3. Pantauan Kinerja Karyawan & Target Omset
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

        // 4. Laporan Rekapitulasi Kas Harian
        $dailyCashRecap = PosTransaction::where('tenant_id', $tenant->id)
            ->whereDate('created_at', Carbon::today())
            ->get();

        // 5. Database Vendor & Mitra Eksternal
        $vendors = Vendor::where('tenant_id', $tenant->id)->latest()->get();

        return view('manager.features', compact(
            'user',
            'tenant',
            'activeTab',
            'masterClasses',
            'trainers',
            'promoCodes',
            'receptionistPerformance',
            'trainerPerformance',
            'dailyCashRecap',
            'vendors'
        ));
    }

    // ==========================================
    // 1. MASTER KELAS GYM
    // ==========================================
    public function storeMasterClass(Request $request)
    {
        $tenant = Auth::user()->tenant;
        $request->validate([
            'name' => 'required|string|max:255',
            'day' => 'required|string',
            'start_time' => 'required',
            'duration_minutes' => 'required|integer|min:1',
        ]);

        $startTime = Carbon::createFromFormat('H:i', $request->start_time);
        $endTime = (clone $startTime)->addMinutes((int) $request->duration_minutes);

        GymClass::create([
            'tenant_id' => $tenant->id,
            'name' => $request->name,
            'day' => $request->day,
            'start_time' => $startTime->format('H:i'),
            'end_time' => $endTime->format('H:i'),
            'duration_minutes' => $request->duration_minutes,
        ]);

        return redirect()->route('manager.features', ['tab' => 'classes'])->with('success', 'Rencana Master Kelas baru berhasil dibuat.');
    }

    public function updateMasterClass(Request $request, $id)
    {
        $tenant = Auth::user()->tenant;
        $class = GymClass::where('tenant_id', $tenant->id)->findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'day' => 'required|string',
            'start_time' => 'required',
            'duration_minutes' => 'required|integer|min:1',
        ]);

        $startTime = Carbon::createFromFormat('H:i:s', strlen($request->start_time) == 5 ? $request->start_time . ':00' : $request->start_time);
        $endTime = (clone $startTime)->addMinutes((int) $request->duration_minutes);

        $class->update([
            'name' => $request->name,
            'day' => $request->day,
            'start_time' => $startTime->format('H:i:s'),
            'end_time' => $endTime->format('H:i:s'),
            'duration_minutes' => $request->duration_minutes,
        ]);

        return redirect()->route('manager.features', ['tab' => 'classes'])->with('success', 'Rencana Master Kelas berhasil diperbarui.');
    }

    public function destroyMasterClass($id)
    {
        $tenant = Auth::user()->tenant;
        $class = GymClass::where('tenant_id', $tenant->id)->findOrFail($id);
        $class->delete();

        return redirect()->route('manager.features', ['tab' => 'classes'])->with('success', 'Master Kelas berhasil dihapus.');
    }

    // ==========================================
    // 2. PROMO & VOUCHER DISKON
    // ==========================================
    public function storePromo(Request $request)
    {
        $tenant = Auth::user()->tenant;
        $request->validate([
            'code' => 'required|string|unique:promo_codes,code',
            'description' => 'nullable|string',
            'discount_type' => 'required|string',
            'discount_value' => 'required|numeric|min:0',
            'min_purchase' => 'required|numeric|min:0',
            'max_uses' => 'required|integer|min:1',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date',
        ]);

        PromoCode::create(array_merge($request->all(), ['tenant_id' => $tenant->id, 'is_active' => true]));

        return redirect()->route('manager.features', ['tab' => 'promo'])->with('success', 'Kode voucher promo berhasil dibuat.');
    }

    public function updatePromo(Request $request, $id)
    {
        $tenant = Auth::user()->tenant;
        $promo = PromoCode::where('tenant_id', $tenant->id)->findOrFail($id);
        $request->validate([
            'code' => 'required|string|unique:promo_codes,code,' . $promo->id,
            'description' => 'nullable|string',
            'discount_type' => 'required|string',
            'discount_value' => 'required|numeric|min:0',
            'min_purchase' => 'required|numeric|min:0',
            'max_uses' => 'required|integer|min:1',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date',
        ]);

        $promo->update(array_merge($request->all(), ['is_active' => $request->has('is_active')]));

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

        $vendor->update($request->all());

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
