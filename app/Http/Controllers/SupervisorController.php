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

        // 4. Peringatan Stok Ritel Menipis & Seluruh Produk
        $allProducts = Product::where('tenant_id', $tenant->id)->latest()->get();
        $lowStockProducts = $allProducts->filter(function($prod) {
            return $prod->stock <= 10;
        });

        // 5. Penanganan Komplain Member
        $complaints = Complaint::where('tenant_id', $tenant->id)
            ->with(['member', 'reporter'])
            ->latest()
            ->get();
        $members = Member::where('tenant_id', $tenant->id)->get();

        // 6. Master Loker Gym
        $lockers = Locker::where('tenant_id', $tenant->id)->get();
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

        $transaction->update(['void_status' => 'approved']);

        // Kembalikan stok barang jika tipe transaksi inventaris ritel
        if ($transaction->type === 'inventory') {
            foreach ($transaction->items as $item) {
                if ($item->product_id) {
                    $prod = Product::find($item->product_id);
                    if ($prod) {
                        $prod->increment('stock', $item->qty);
                    }
                }
            }
        }

        StaffLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => Auth::id(),
            'action' => 'Approve Void Transaksi',
            'description' => "Supervisor menyetujui void (pembatalan) transaksi invoice {$transaction->invoice_number}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('supervisor.features', ['tab' => 'void'])->with('success', "Permintaan void untuk invoice {$transaction->invoice_number} berhasil disetujui.");
    }

    public function rejectVoid($id)
    {
        $tenant = Auth::user()->tenant;
        $transaction = PosTransaction::where('tenant_id', $tenant->id)->findOrFail($id);

        $transaction->update(['void_status' => 'rejected']);

        StaffLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => Auth::id(),
            'action' => 'Reject Void Transaksi',
            'description' => "Supervisor menolak void (pembatalan) transaksi invoice {$transaction->invoice_number}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('supervisor.features', ['tab' => 'void'])->with('success', "Permintaan void untuk invoice {$transaction->invoice_number} ditolak.");
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

        $shift->update($request->all());

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

        LeaveRequest::create([
            'tenant_id' => $tenant->id,
            'user_id' => $request->user_id,
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
            'status' => 'required|string',
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
            'status' => 'required|string',
            'purchase_date' => 'nullable|date',
            'next_service_date' => 'nullable|date',
        ]);

        $equipment->update($request->all());

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
        $request->validate([
            'gym_equipment_id' => 'required|exists:gym_equipments,id',
            'action' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cost' => 'required|numeric|min:0',
            'serviced_at' => 'required|date',
            'next_service_date' => 'nullable|date',
        ]);

        EquipmentMaintenanceLog::create(array_merge($request->all(), ['tenant_id' => $tenant->id]));

        // Reset status alat menjadi berfungsi
        $equipment = GymEquipment::where('tenant_id', $tenant->id)->findOrFail($request->gym_equipment_id);
        $equipment->update([
            'status' => 'berfungsi',
            'next_service_date' => $request->next_service_date,
        ]);

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

        $complaint->update($request->all());

        StaffLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => Auth::id(),
            'action' => 'Update Tiket Komplain',
            'description' => "Supervisor memperbarui tiket komplain #{$complaint->id} ({$complaint->title}) menjadi {$complaint->status}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('supervisor.features', ['tab' => 'complaint'])->with('success', 'Status tiket keluhan member berhasil diperbarui.');
    }
}
