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
use App\Models\Product;
use App\Models\StockMutation;
use App\Models\Locker;
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

        // 3. Laporan Rekapitulasi Kas Keuangan (Filter: Harian, Mingguan, Bulanan)
        $cashPeriod = $request->query('period', 'daily');
        if (!in_array($cashPeriod, ['daily', 'weekly', 'monthly'])) {
            $cashPeriod = 'daily';
        }

        $cashQuery = PosTransaction::where('tenant_id', $tenant->id)->with(['user', 'member']);

        if ($cashPeriod === 'daily') {
            $cashQuery->whereDate('created_at', Carbon::today());
        } elseif ($cashPeriod === 'weekly') {
            $cashQuery->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($cashPeriod === 'monthly') {
            $cashQuery->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
        }

        $dailyCashRecap = $cashQuery->latest()->get();

        // 4. Database Vendor & Mitra Eksternal
        $vendors = Vendor::where('tenant_id', $tenant->id)->latest()->get();

        // 5. Data Inventaris & Valuasi Stok (Gudang & Etalase)
        $products = Product::where('tenant_id', $tenant->id)->latest()->get();
        $totalWarehouseValuation = $products->sum(function($p) {
            return $p->warehouse_stock * ($p->cost_price ?: $p->price * 0.7);
        });
        $totalDisplayValuation = $products->sum(function($p) {
            return $p->stock * ($p->cost_price ?: $p->price * 0.7);
        });
        $totalInventoryValuation = $totalWarehouseValuation + $totalDisplayValuation;
        // 6. Master Loker Gym
        $lockers = Locker::where('tenant_id', $tenant->id)
            ->orderByRaw('CAST(locker_number AS UNSIGNED) ASC')
            ->orderBy('locker_number')
            ->get();
        $totalLockers = $lockers->count();
        $availableLockers = $lockers->where('status', 'tersedia')->count();
        $occupiedLockers = $lockers->where('status', 'terpakai')->count();
        $brokenLockers = $lockers->where('status', 'rusak')->count();

        return view('manager.features', compact(
            'user',
            'tenant',
            'activeTab',
            'cashPeriod',
            'promoCodes',
            'receptionistPerformance',
            'trainerPerformance',
            'dailyCashRecap',
            'vendors',
            'products',
            'totalWarehouseValuation',
            'totalDisplayValuation',
            'totalInventoryValuation',
            'lockers',
            'totalLockers',
            'availableLockers',
            'occupiedLockers',
            'brokenLockers'
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

    // ==========================================
    // 5. MANAJEMEN MASTER LOKER GYM
    // ==========================================
    public function storeLocker(Request $request)
    {
        $user = Auth::user();
        if ($user->isOwner()) {
            return redirect()->back()->with('error', 'Anda tidak dapat melakukan perubahan ini karena akun Anda hanya memiliki akses pantau (baca saja). Hubungi pemilik akun utama untuk melakukan perubahan.');
        }

        $tenant = $user->tenant;
        $request->validate([
            'locker_number' => 'required|string|max:50',
            'status' => 'nullable|in:tersedia,rusak',
        ]);

        $exists = Locker::where('tenant_id', $tenant->id)
            ->where('locker_number', $request->locker_number)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', "Nomor loker {$request->locker_number} sudah terdaftar.");
        }

        $locker = Locker::create([
            'tenant_id' => $tenant->id,
            'locker_number' => $request->locker_number,
            'status' => $request->status ?: 'tersedia',
        ]);

        StaffLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'Tambah Master Loker',
            'description' => "Manager {$user->name} mendaftarkan loker baru nomor: {$locker->locker_number}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('manager.features', ['tab' => 'locker'])->with('success', "Loker nomor {$locker->locker_number} berhasil didaftarkan.");
    }

    public function storeBulkLockers(Request $request)
    {
        $user = Auth::user();
        if ($user->isOwner()) {
            return redirect()->back()->with('error', 'Anda tidak dapat melakukan perubahan ini karena akun Anda hanya memiliki akses pantau (baca saja). Hubungi pemilik akun utama untuk melakukan perubahan.');
        }

        $tenant = $user->tenant;
        $mode = $request->input('mode', 'quantity');

        $createdCount = 0;
        $skippedCount = 0;

        if ($mode === 'quantity') {
            $request->validate([
                'quantity' => 'required|integer|min:1|max:100',
                'prefix' => 'nullable|string|max:10',
            ]);

            $quantity = (int) $request->quantity;
            $prefix = trim((string) $request->prefix);

            $existingLockers = Locker::where('tenant_id', $tenant->id)->pluck('locker_number')->toArray();

            $startNum = 1;
            while (count($existingLockers) > 0 && in_array(($prefix ? $prefix . '-' : '') . str_pad($startNum, 2, '0', STR_PAD_LEFT), $existingLockers)) {
                $startNum++;
            }

            $currentNum = $startNum;
            while ($createdCount < $quantity && $currentNum <= ($startNum + $quantity + 200)) {
                $numStr = ($prefix ? $prefix . '-' : '') . str_pad($currentNum, 2, '0', STR_PAD_LEFT);
                if (!in_array($numStr, $existingLockers)) {
                    Locker::create([
                        'tenant_id' => $tenant->id,
                        'locker_number' => $numStr,
                        'status' => 'tersedia',
                    ]);
                    $existingLockers[] = $numStr;
                    $createdCount++;
                } else {
                    $skippedCount++;
                }
                $currentNum++;
            }
        } else {
            $request->validate([
                'start_number' => 'required|integer|min:1',
                'end_number' => 'required|integer|min:1|gte:start_number',
                'prefix' => 'nullable|string|max:10',
            ]);

            $start = (int) $request->start_number;
            $end = (int) $request->end_number;
            $prefix = trim((string) $request->prefix);

            if (($end - $start + 1) > 100) {
                return redirect()->back()->with('error', 'Maksimal 100 loker dapat dibuat dalam satu batch.');
            }

            $existingLockers = Locker::where('tenant_id', $tenant->id)
                ->pluck('locker_number')
                ->toArray();

            for ($i = $start; $i <= $end; $i++) {
                $numStr = ($prefix ? $prefix . '-' : '') . str_pad($i, 2, '0', STR_PAD_LEFT);
                if (!in_array($numStr, $existingLockers)) {
                    Locker::create([
                        'tenant_id' => $tenant->id,
                        'locker_number' => $numStr,
                        'status' => 'tersedia',
                    ]);
                    $existingLockers[] = $numStr;
                    $createdCount++;
                } else {
                    $skippedCount++;
                }
            }
        }

        StaffLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'Tambah Loker Massal',
            'description' => "Manager {$user->name} membuat {$createdCount} loker secara massal" . ($skippedCount > 0 ? " ({$skippedCount} dilewati karena sudah ada)" : ""),
            'ip_address' => $request->ip(),
        ]);

        $msg = "Berhasil menambahkan {$createdCount} unit loker baru.";
        if ($skippedCount > 0) {
            $msg .= " ({$skippedCount} nomor loker dilewati karena sudah terdaftar sebelumnya).";
        }

        return redirect()->route('manager.features', ['tab' => 'locker'])->with('success', $msg);
    }

    public function destroyLocker(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->isOwner()) {
            return redirect()->back()->with('error', 'Anda tidak dapat melakukan perubahan ini karena akun Anda hanya memiliki akses pantau (baca saja). Hubungi pemilik akun utama untuk melakukan perubahan.');
        }

        $tenant = $user->tenant;
        $locker = Locker::where('tenant_id', $tenant->id)->findOrFail($id);

        if ($locker->status === 'terpakai') {
            return redirect()->back()->with('error', "Loker nomor {$locker->locker_number} sedang terpakai oleh member dan tidak dapat dihapus.");
        }

        $num = $locker->locker_number;
        $locker->delete();

        StaffLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'Hapus Master Loker',
            'description' => "Manager {$user->name} menghapus loker nomor: {$num}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('manager.features', ['tab' => 'locker'])->with('success', "Loker nomor {$num} berhasil dihapus.");
    }
}
