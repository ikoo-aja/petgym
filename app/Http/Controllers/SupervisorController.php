<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\GymEquipment;
use App\Models\EquipmentMaintenanceLog;
use App\Models\StaffShift;
use App\Models\LeaveRequest;
use App\Models\Complaint;
use App\Models\User;
use App\Models\Member;
use App\Models\PosTransaction;
use App\Models\StaffLog;
use App\Models\Locker;
use App\Models\Product;
use App\Models\Vendor;
use App\Models\StockMutation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SupervisorController extends Controller
{
    /**
     * Dashboard Khusus Supervisor (Pengawas Operasional Lapangan)
     */
    public function dashboard()
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        if (!$tenant) {
            return view('supervisor.dashboard', [
                'user' => $user,
                'tenant' => null,
                'pendingVoidCount' => 0,
                'lowStockCount' => 0,
                'activeShiftsCount' => 0,
                'openComplaintsCount' => 0,
                'pendingVoids' => collect(),
                'todayShifts' => collect(),
                'lowStockProducts' => collect(),
                'activeComplaints' => collect(),
                'lockers' => collect(),
            ]);
        }

        $today = Carbon::today();

        // 1. Antrean Otorisasi Void Kasir
        $pendingVoids = PosTransaction::where('tenant_id', $tenant->id)
            ->where('void_status', 'pending')
            ->with(['member', 'user'])
            ->latest()
            ->get();
        $pendingVoidCount = $pendingVoids->count();

        // 2. Peringatan Stok Ritel Menipis (<= 10)
        $lowStockProducts = Product::where('tenant_id', $tenant->id)
            ->where('stock', '<=', 10)
            ->get();
        $lowStockCount = $lowStockProducts->count();

        // 3. Shift Staf Hari Ini
        $todayShifts = StaffShift::where('tenant_id', $tenant->id)
            ->whereDate('shift_date', $today)
            ->with('user')
            ->get();
        $activeShiftsCount = $todayShifts->count();

        // 4. Tiket Komplain Terbuka
        $activeComplaints = Complaint::where('tenant_id', $tenant->id)
            ->whereIn('status', ['open', 'in_progress'])
            ->with(['member', 'reporter'])
            ->latest()
            ->get();
        $openComplaintsCount = $activeComplaints->count();

        // 5. Loker Gym
        $lockers = Locker::where('tenant_id', $tenant->id)->get();

        return view('supervisor.dashboard', compact(
            'user',
            'tenant',
            'pendingVoidCount',
            'lowStockCount',
            'activeShiftsCount',
            'openComplaintsCount',
            'pendingVoids',
            'todayShifts',
            'lowStockProducts',
            'activeComplaints',
            'lockers'
        ));
    }

    /**
     * Halaman Fitur Operasional Supervisor
     */
    public function features(Request $request)
    {
        $user = Auth::user();
        $tenant = $user->tenant;
        $activeTab = $request->query('tab', 'void');

        // 1. Otorisasi Kasir & Void Transaksi
        $voidTransactions = PosTransaction::where('tenant_id', $tenant->id)
            ->where('void_status', '!=', 'none')
            ->with(['member', 'user'])
            ->latest()
            ->get();

        // 2. Shift & Cuti Staf Operasional (Resepsionis & PT)
        $staffShifts = StaffShift::where('tenant_id', $tenant->id)
            ->with('user')
            ->orderBy('shift_date', 'desc')
            ->get();
        $leaveRequests = LeaveRequest::where('tenant_id', $tenant->id)
            ->with(['user', 'approver'])
            ->latest()
            ->get();
        $shiftStaffUsers = User::where('tenant_id', $tenant->id)
            ->whereIn('role', ['receptionist', 'trainer'])
            ->get();

        // 3. Aset & Pemeliharaan Alat Gym
        $equipments = GymEquipment::where('tenant_id', $tenant->id)->latest()->get();
        $maintenanceLogs = EquipmentMaintenanceLog::where('tenant_id', $tenant->id)
            ->with('equipment')
            ->latest()
            ->get();

        // 4. Data Produk & Inventaris Gudang vs Etalase
        $allProducts = Product::where('tenant_id', $tenant->id)->latest()->get();
        $lowStockProducts = $allProducts->filter(function($prod) {
            return $prod->stock <= 10;
        });
        $lowWarehouseProducts = $allProducts->filter(function($prod) {
            return $prod->warehouse_stock <= 5;
        });

        // Rekanan Vendor & Mutasi Stok
        $vendors = Vendor::where('tenant_id', $tenant->id)->orderBy('name')->get();
        $stockMutations = StockMutation::where('tenant_id', $tenant->id)
            ->with(['product', 'user'])
            ->latest()
            ->take(25)
            ->get();

        // Valuasi Aset Inventaris
        $totalWarehouseValuation = $allProducts->sum(function($p) {
            return $p->warehouse_stock * ($p->cost_price ?: $p->price * 0.7);
        });
        $totalDisplayValuation = $allProducts->sum(function($p) {
            return $p->stock * ($p->cost_price ?: $p->price * 0.7);
        });
        $totalInventoryValuation = $totalWarehouseValuation + $totalDisplayValuation;

        // 5. Penanganan Komplain Member
        $complaints = Complaint::where('tenant_id', $tenant->id)
            ->with(['member', 'reporter'])
            ->latest()
            ->get();
        $members = Member::where('tenant_id', $tenant->id)->get();

        // 6. Master Loker Gym
        $lockers = Locker::where('tenant_id', $tenant->id)
            ->orderByRaw('CAST(locker_number AS UNSIGNED) ASC')
            ->orderBy('locker_number')
            ->get();
        $availableLockers = $lockers->where('status', 'tersedia')->count();
        $occupiedLockers = $lockers->where('status', 'terpakai')->count();
        $brokenLockers = $lockers->where('status', 'rusak')->count();

        // Rekap Kas Transaksi POS Hari Ini
        $dailyCashRecap = PosTransaction::where('tenant_id', $tenant->id)
            ->whereDate('created_at', Carbon::today())
            ->get();

        return view('supervisor.features', compact(
            'user',
            'tenant',
            'activeTab',
            'voidTransactions',
            'staffShifts',
            'leaveRequests',
            'shiftStaffUsers',
            'equipments',
            'maintenanceLogs',
            'allProducts',
            'lowStockProducts',
            'lowWarehouseProducts',
            'vendors',
            'stockMutations',
            'totalWarehouseValuation',
            'totalDisplayValuation',
            'totalInventoryValuation',
            'complaints',
            'members',
            'lockers',
            'availableLockers',
            'occupiedLockers',
            'brokenLockers',
            'dailyCashRecap'
        ));
    }

    // ==========================================
    // 1. OTORISASI & VOID TRANSAKSI KASIR
    // ==========================================
    public function approveVoid($id)
    {
        $tenant = Auth::user()->tenant;
        $transaction = PosTransaction::where('tenant_id', $tenant->id)->findOrFail($id);

        if ($transaction->void_status !== 'pending') {
            return redirect()->route('supervisor.features', ['tab' => 'void'])->with('error', "Permintaan pembatalan transaksi invoice {$transaction->invoice_number} sudah diproses sebelumnya.");
        }

        DB::transaction(function () use ($tenant, $transaction) {
            $transaction->update(['void_status' => 'approved']);

            // Kembalikan stok barang jika tipe transaksi inventaris ritel
            if ($transaction->type === 'inventory') {
                foreach ($transaction->items as $item) {
                    if ($item->product_id) {
                        $prod = Product::where('tenant_id', $tenant->id)->find($item->product_id);
                        if ($prod) {
                            $prod->increment('stock', $item->qty);
                        }
                    }
                }
            }

            StaffLog::create([
                'tenant_id' => $tenant->id,
                'user_id' => Auth::id(),
                'action' => 'Persetujuan Pembatalan Transaksi',
                'description' => "Supervisor menyetujui pembatalan transaksi invoice {$transaction->invoice_number}",
                'ip_address' => request()->ip(),
            ]);
        });

        return redirect()->route('supervisor.features', ['tab' => 'void'])->with('success', "Permintaan pembatalan transaksi untuk invoice {$transaction->invoice_number} berhasil disetujui.");
    }

    public function rejectVoid($id)
    {
        $tenant = Auth::user()->tenant;
        $transaction = PosTransaction::where('tenant_id', $tenant->id)->findOrFail($id);

        if ($transaction->void_status !== 'pending') {
            return redirect()->route('supervisor.features', ['tab' => 'void'])->with('error', "Permintaan pembatalan transaksi invoice {$transaction->invoice_number} sudah diproses sebelumnya.");
        }

        DB::transaction(function () use ($tenant, $transaction) {
            $transaction->update(['void_status' => 'rejected']);

            StaffLog::create([
                'tenant_id' => $tenant->id,
                'user_id' => Auth::id(),
                'action' => 'Penolakan Pembatalan Transaksi',
                'description' => "Supervisor menolak pembatalan transaksi invoice {$transaction->invoice_number}",
                'ip_address' => request()->ip(),
            ]);
        });

        return redirect()->route('supervisor.features', ['tab' => 'void'])->with('success', "Permintaan pembatalan transaksi untuk invoice {$transaction->invoice_number} ditolak.");
    }

    // ==========================================
    // 2. PENJADWALAN SHIFT & CUTI STAF
    // ==========================================
    public function storeShift(Request $request)
    {
        $tenant = Auth::user()->tenant;
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'shift_date' => 'required|date',
            'shift_name' => 'required|string',
            'start_time' => 'required',
            'end_time' => 'required',
            'notes' => 'nullable|string',
        ]);

        $targetUser = User::where('tenant_id', $tenant->id)->findOrFail($request->user_id);
        if (!in_array($targetUser->role, ['receptionist', 'trainer'])) {
            return redirect()->back()->with('error', 'Penjadwalan shift hanya berlaku untuk Resepsionis dan Personal Trainer (PT).');
        }

        StaffShift::create(array_merge($request->all(), ['tenant_id' => $tenant->id]));

        return redirect()->route('supervisor.features', ['tab' => 'shift'])->with('success', 'Jadwal shift staf operasional berhasil ditambahkan.');
    }

    public function updateShift(Request $request, $id)
    {
        $tenant = Auth::user()->tenant;
        $shift = StaffShift::where('tenant_id', $tenant->id)->findOrFail($id);
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'shift_date' => 'required|date',
            'shift_name' => 'required|string',
            'start_time' => 'required',
            'end_time' => 'required',
            'notes' => 'nullable|string',
        ]);

        // Scope user_id ke tenant sendiri (cegah jadwal shift lintas gym)
        $targetUser = User::where('tenant_id', $tenant->id)->findOrFail($request->user_id);

        $shift->update([
            'user_id' => $targetUser->id,
            'shift_date' => $request->shift_date,
            'shift_name' => $request->shift_name,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'notes' => $request->notes,
        ]);

        return redirect()->route('supervisor.features', ['tab' => 'shift'])->with('success', 'Jadwal shift staf berhasil diperbarui.');
    }

    public function destroyShift($id)
    {
        $tenant = Auth::user()->tenant;
        $shift = StaffShift::where('tenant_id', $tenant->id)->findOrFail($id);
        $shift->delete();

        return redirect()->route('supervisor.features', ['tab' => 'shift'])->with('success', 'Jadwal shift staf berhasil dihapus.');
    }

    public function storeLeave(Request $request)
    {
        $tenant = Auth::user()->tenant;
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
        ]);

        // Scope user_id ke tenant sendiri (cegah cuti lintas gym)
        $targetUser = User::where('tenant_id', $tenant->id)->findOrFail($request->user_id);

        LeaveRequest::create([
            'tenant_id' => $tenant->id,
            'user_id' => $targetUser->id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'status' => 'approved',
            'approved_by' => Auth::id(),
        ]);

        return redirect()->route('supervisor.features', ['tab' => 'shift'])->with('success', 'Pengajuan cuti staf berhasil dicatat dan disetujui.');
    }

    public function approveLeave($id)
    {
        $tenant = Auth::user()->tenant;
        $leave = LeaveRequest::where('tenant_id', $tenant->id)->findOrFail($id);
        $leave->update([
            'status' => 'approved',
            'approved_by' => Auth::id()
        ]);

        return redirect()->route('supervisor.features', ['tab' => 'shift'])->with('success', 'Pengajuan cuti staf disetujui.');
    }

    public function rejectLeave($id)
    {
        $tenant = Auth::user()->tenant;
        $leave = LeaveRequest::where('tenant_id', $tenant->id)->findOrFail($id);
        $leave->update([
            'status' => 'rejected',
            'approved_by' => Auth::id()
        ]);

        return redirect()->route('supervisor.features', ['tab' => 'shift'])->with('success', 'Pengajuan cuti staf ditolak.');
    }

    // ==========================================
    // 3. ASET & PEMELIHARAAN ALAT GYM
    // ==========================================
    public function storeEquipment(Request $request)
    {
        $tenant = Auth::user()->tenant;
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'brand' => 'nullable|string',
            'status' => 'required|in:berfungsi,perlu_servis,rusak',
            'purchase_date' => 'nullable|date',
            'next_service_date' => 'nullable|date',
        ]);

        GymEquipment::create(array_merge($request->all(), ['tenant_id' => $tenant->id]));

        return redirect()->route('supervisor.features', ['tab' => 'equipment'])->with('success', 'Data alat gym berhasil didaftarkan ke inventaris aset.');
    }

    public function updateEquipment(Request $request, $id)
    {
        $tenant = Auth::user()->tenant;
        $equipment = GymEquipment::where('tenant_id', $tenant->id)->findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'brand' => 'nullable|string',
            'status' => 'required|in:berfungsi,perlu_servis,rusak',
            'purchase_date' => 'nullable|date',
            'next_service_date' => 'nullable|date',
        ]);

        $equipment->update($request->except(['tenant_id']));

        return redirect()->route('supervisor.features', ['tab' => 'equipment'])->with('success', 'Data alat gym berhasil diperbarui.');
    }

    public function destroyEquipment($id)
    {
        $tenant = Auth::user()->tenant;
        $equipment = GymEquipment::where('tenant_id', $tenant->id)->findOrFail($id);
        $equipment->delete();

        return redirect()->route('supervisor.features', ['tab' => 'equipment'])->with('success', 'Alat gym berhasil dihapus dari inventaris.');
    }

    public function storeMaintenanceLog(Request $request)
    {
        $tenant = Auth::user()->tenant;

        if ($request->filled('cost')) {
            $request->merge(['cost' => preg_replace('/[^0-9]/', '', (string)$request->cost)]);
        }

        $request->validate([
            'gym_equipment_id' => 'required|exists:gym_equipments,id',
            'action' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cost' => 'required|numeric|min:0',
            'serviced_at' => 'required|date',
            'next_service_date' => 'nullable|date',
        ]);

        // Validasi kepemilikan alat gym terhadap tenant
        $equipment = GymEquipment::where('tenant_id', $tenant->id)->findOrFail($request->gym_equipment_id);

        DB::transaction(function () use ($request, $tenant, $equipment) {
            EquipmentMaintenanceLog::create(array_merge($request->all(), ['tenant_id' => $tenant->id]));

            // Reset status alat menjadi berfungsi
            $equipment->update([
                'status' => 'berfungsi',
                'next_service_date' => $request->next_service_date,
            ]);
        });
        return redirect()->route('supervisor.features', ['tab' => 'equipment'])->with('success', 'Log perbaikan alat berhasil dicatat dan status alat telah di-reset.');
    }

    // ==========================================
    // 4. PENANGANAN TIKET KOMPLAIN MEMBER
    // ==========================================
    public function updateComplaint(Request $request, $id)
    {
        $tenant = Auth::user()->tenant;
        $complaint = Complaint::where('tenant_id', $tenant->id)->findOrFail($id);
        $request->validate([
            'status' => 'required|string|in:open,in_progress,resolved,closed',
            'resolution' => 'nullable|string',
        ]);

        $complaint->update($request->except(['tenant_id']));

        StaffLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => Auth::id(),
            'action' => 'Tindak Lanjut Keluhan Member',
            'description' => "Supervisor menindaklanjuti tiket keluhan #{$complaint->id} ({$complaint->title}) dengan status: {$complaint->status}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('supervisor.features', ['tab' => 'complaint'])->with('success', 'Status tiket keluhan member berhasil diperbarui.');
    }

    // ==========================================
    // 5. MANAJEMEN GUDANG & PRODUK DARI VENDOR
    // ==========================================
    public function storeProduct(Request $request)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        if ($request->filled('price')) {
            $request->merge(['price' => preg_replace('/[^0-9]/', '', (string)$request->price)]);
        }
        if ($request->filled('cost_price')) {
            $request->merge(['cost_price' => preg_replace('/[^0-9]/', '', (string)$request->cost_price)]);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:supplement,drink,merchandise',
            'price' => 'required|integer|min:0',
            'cost_price' => 'required|integer|min:0',
            'warehouse_stock' => 'required|integer|min:0',
            'vendor_name' => 'nullable|string|max:255',
        ]);

        $product = DB::transaction(function () use ($request, $tenant, $user) {
            $product = Product::create([
                'tenant_id' => $tenant->id,
                'name' => $request->name,
                'category' => $request->category,
                'price' => $request->price,
                'cost_price' => $request->cost_price,
                'stock' => $request->warehouse_stock, // Stok yang langsung siap ditransaksikan kasir
                'warehouse_stock' => $request->warehouse_stock,
                'vendor_name' => $request->vendor_name,
            ]);

            // Catat Mutasi Masuk ke Gudang
            if ($request->warehouse_stock > 0) {
                StockMutation::create([
                    'tenant_id' => $tenant->id,
                    'product_id' => $product->id,
                    'user_id' => $user->id,
                    'type' => 'in_vendor',
                    'qty' => $request->warehouse_stock,
                    'from_location' => 'vendor',
                    'to_location' => 'gudang',
                    'unit_cost' => $request->cost_price,
                    'unit_price' => $request->price,
                    'notes' => 'Penerimaan stok awal dari vendor: ' . ($request->vendor_name ?: 'Vendor Mitra'),
                ]);
            }

            StaffLog::create([
                'tenant_id' => $tenant->id,
                'user_id' => $user->id,
                'action' => 'Input Produk Gudang (Vendor)',
                'description' => "Supervisor {$user->name} mendaftarkan produk baru ke inventaris: {$product->name} (Stok: {$product->stock} pcs, Modal: Rp " . number_format($product->cost_price, 0, ',', '.') . ") dari Vendor " . ($product->vendor_name ?: '-'),
                'ip_address' => $request->ip(),
            ]);

            return $product;
        });

        return redirect()->route('supervisor.features', ['tab' => 'stock'])->with('success', "Produk '{$product->name}' berhasil didaftarkan ke inventaris (Stok: {$product->stock} pcs).");
    }

    public function updateProduct(Request $request, $id)
    {
        $user = Auth::user();
        $tenant = $user->tenant;
        $product = Product::where('tenant_id', $tenant->id)->findOrFail($id);

        if ($request->filled('price')) {
            $request->merge(['price' => preg_replace('/[^0-9]/', '', (string)$request->price)]);
        }
        if ($request->filled('cost_price')) {
            $request->merge(['cost_price' => preg_replace('/[^0-9]/', '', (string)$request->cost_price)]);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:supplement,drink,merchandise',
            'price' => 'required|integer|min:0',
            'cost_price' => 'required|integer|min:0',
            'vendor_name' => 'nullable|string|max:255',
        ]);

        $product->update([
            'name' => $request->name,
            'category' => $request->category,
            'price' => $request->price,
            'cost_price' => $request->cost_price,
            'vendor_name' => $request->vendor_name,
        ]);

        StaffLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'Update Produk Gudang',
            'description' => "Supervisor {$user->name} memperbarui data produk: {$product->name}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('supervisor.features', ['tab' => 'stock'])->with('success', "Data produk '{$product->name}' berhasil diperbarui.");
    }

    public function restockProduct(Request $request, $id)
    {
        $user = Auth::user();
        $tenant = $user->tenant;
        $product = Product::where('tenant_id', $tenant->id)->findOrFail($id);

        if ($request->filled('cost_price')) {
            $request->merge(['cost_price' => preg_replace('/[^0-9]/', '', (string)$request->cost_price)]);
        }

        $request->validate([
            'qty_add' => 'required|integer|min:1',
            'cost_price' => 'nullable|integer|min:0',
            'vendor_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:255',
        ], [
            'qty_add.required' => 'Jumlah barang masuk wajib diisi.',
            'qty_add.min' => 'Jumlah barang masuk minimal 1 pcs.',
        ]);

        $oldStock = (int) $product->stock;
        $oldWarehouseStock = (int) $product->warehouse_stock;

        $vendorText = $request->vendor_name ?: ($product->vendor_name ?: 'Vendor Mitra');
        $noteText = $request->filled('notes') ? " ({$request->notes})" : "";

        DB::transaction(function () use ($request, $product, $tenant, $user, $oldWarehouseStock, $oldStock, $vendorText, $noteText) {
            $product->increment('stock', $request->qty_add);
            $product->increment('warehouse_stock', $request->qty_add);

            if ($request->filled('cost_price') && $request->cost_price > 0) {
                $product->update(['cost_price' => $request->cost_price]);
            }
            if ($request->filled('vendor_name')) {
                $product->update(['vendor_name' => $request->vendor_name]);
            }

            $product->refresh();

            StockMutation::create([
                'tenant_id' => $tenant->id,
                'product_id' => $product->id,
                'user_id' => $user->id,
                'type' => 'in_vendor',
                'qty' => $request->qty_add,
                'from_location' => 'vendor',
                'to_location' => 'gudang',
                'unit_cost' => $product->cost_price,
                'unit_price' => $product->price,
                'notes' => "Restock kiriman dari vendor: {$vendorText}{$noteText}",
            ]);

            StaffLog::create([
                'tenant_id' => $tenant->id,
                'user_id' => $user->id,
                'action' => 'Penerimaan Stok Vendor ke Gudang',
                'description' => "Supervisor {$user->name} menerima kiriman vendor +{$request->qty_add} pcs untuk '{$product->name}' ke Gudang (Stok Gudang: {$oldWarehouseStock} -> {$product->warehouse_stock} pcs) dari {$vendorText}",
                'ip_address' => $request->ip(),
            ]);
        });

        return redirect()->route('supervisor.features', ['tab' => 'stock'])->with('success', "Stok gudang '{$product->name}' berhasil ditambah +{$request->qty_add} pcs dari Vendor {$vendorText}.");
    }

    public function destroyProduct(Request $request, $id)
    {
        $user = Auth::user();
        $tenant = $user->tenant;
        $product = Product::where('tenant_id', $tenant->id)->findOrFail($id);
        $prodName = $product->name;
        $product->delete();

        StaffLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'Hapus Produk Gudang',
            'description' => "Supervisor {$user->name} menghapus produk '{$prodName}' dari sistem",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('supervisor.features', ['tab' => 'stock'])->with('success', "Produk '{$prodName}' berhasil dihapus.");
    }

    /**
     * Perbaikan & Pemeliharaan Unit Loker Gym
     */
    public function repairLocker(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->isOwner()) {
            return redirect()->back()->with('error', 'Aksi tidak diizinkan: Akun ini dalam mode pantau (hanya baca).');
        }

        $tenant = $user->tenant;
        $locker = Locker::where('tenant_id', $tenant->id)->findOrFail($id);

        if ($locker->status !== 'rusak') {
            return redirect()->back()->with('error', "Loker nomor #{$locker->locker_number} saat ini tidak dalam status rusak.");
        }

        $oldNotes = $locker->notes;
        $repairNotes = $request->input('repair_notes');

        $locker->update([
            'status' => 'tersedia',
            'notes' => null,
        ]);

        StaffLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'Perbaikan Loker Selesai',
            'description' => "Supervisor {$user->name} menyelesaikan perbaikan loker nomor #{$locker->locker_number}. Status dikembalikan menjadi Tersedia." . ($repairNotes ? " Catatan perbaikan: {$repairNotes}." : "") . ($oldNotes ? " (Keluhan sebelumnya: {$oldNotes})" : ""),
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('supervisor.features', ['tab' => 'locker'])->with('success', "Loker nomor #{$locker->locker_number} berhasil diperbaiki dan status dikembalikan menjadi Tersedia.");
    }
}
