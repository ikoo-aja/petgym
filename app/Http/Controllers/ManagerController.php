<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\GymEquipment;
use App\Models\EquipmentMaintenanceLog;
use App\Models\StaffShift;
use App\Models\LeaveRequest;
use App\Models\PromoCode;
use App\Models\Complaint;
use App\Models\Vendor;
use App\Models\User;
use App\Models\Member;
use App\Models\PosTransaction;
use App\Models\CheckIn;
use App\Models\StaffLog;
use App\Models\GymClass;
use App\Models\Trainer;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ManagerController extends Controller
{
    /**
     * Dashboard Utama Manager Gym
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
     * 10 Fitur Manajerial Terintegrasi
     */
    public function features()
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        // 1. Manajemen Aset & Pemeliharaan Alat Gym
        $equipments = GymEquipment::where('tenant_id', $tenant->id)->latest()->get();
        $maintenanceLogs = EquipmentMaintenanceLog::where('tenant_id', $tenant->id)->with('equipment')->latest()->get();

        // 2. Pengaturan Jadwal & Shift Karyawan
        $staffShifts = StaffShift::where('tenant_id', $tenant->id)->with('user')->orderBy('shift_date', 'desc')->get();
        $leaveRequests = LeaveRequest::where('tenant_id', $tenant->id)->with(['user', 'approver'])->latest()->get();

        // 3. Sistem Otorisasi & Otoritas Transaksi (Approval Void / Custom Discount)
        $voidTransactions = PosTransaction::where('tenant_id', $tenant->id)
            ->where('void_status', '!=', 'none')
            ->with(['member', 'user'])
            ->latest()
            ->get();

        // 4. Manajemen Promo & Harga
        $promoCodes = PromoCode::where('tenant_id', $tenant->id)->latest()->get();

        // 5. Pantauan Kinerja Staf & Target
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

        // 6. Laporan Operasional Taktis (Rekap Kas Harian / Tren Kehadiran)
        $dailyCashRecap = PosTransaction::where('tenant_id', $tenant->id)
            ->whereDate('created_at', Carbon::today())
            ->get();

        // 7. Manajemen Stok & Inventaris Ritel (Stock Opname)
        $lowStockProducts = Product::where('tenant_id', $tenant->id)
            ->where('stock', '<=', 10)
            ->get();

        // 8. Manajemen Retensi Member (Complaints Ticketing)
        $complaints = Complaint::where('tenant_id', $tenant->id)->with(['member', 'reporter'])->latest()->get();

        // 9. Perencanaan Master Kelas
        $masterClasses = GymClass::where('tenant_id', $tenant->id)->with('trainer')->latest()->get();

        // 10. Database Vendor & Pihak Ketiga
        $vendors = Vendor::where('tenant_id', $tenant->id)->latest()->get();

        // Extra data for forms
        $staffUsers = User::where('tenant_id', $tenant->id)->get();
        $members = Member::where('tenant_id', $tenant->id)->get();
        $trainers = Trainer::where('tenant_id', $tenant->id)->get();

        return view('manager.features', compact(
            'equipments', 'maintenanceLogs', 'staffShifts', 'leaveRequests',
            'voidTransactions', 'promoCodes', 'receptionistPerformance', 'trainerPerformance',
            'dailyCashRecap', 'lowStockProducts', 'complaints', 'masterClasses', 'vendors',
            'staffUsers', 'members', 'trainers', 'tenant'
        ));
    }

    // --- 1. EQUIPMENT CRUD ---
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

        return redirect()->route('manager.features')->with('success', 'Alat gym berhasil didaftarkan.');
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

        return redirect()->route('manager.features')->with('success', 'Data alat gym berhasil diperbarui.');
    }

    public function destroyEquipment($id)
    {
        $tenant = Auth::user()->tenant;
        $equipment = GymEquipment::where('tenant_id', $tenant->id)->findOrFail($id);
        $equipment->delete();

        return redirect()->route('manager.features')->with('success', 'Alat gym berhasil dihapus.');
    }

    // --- MAINTENANCE LOG ---
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

        // Update status and next service date on the equipment
        $equipment = GymEquipment::where('tenant_id', $tenant->id)->findOrFail($request->gym_equipment_id);
        $equipment->update([
            'status' => 'berfungsi',
            'next_service_date' => $request->next_service_date,
        ]);

        return redirect()->route('manager.features')->with('success', 'Log pemeliharaan berhasil dicatat dan status alat di-reset.');
    }

    // --- 2. STAFF SHIFTS CRUD ---
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

        StaffShift::create(array_merge($request->all(), ['tenant_id' => $tenant->id]));

        return redirect()->route('manager.features')->with('success', 'Jadwal shift staf berhasil ditambahkan.');
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

        return redirect()->route('manager.features')->with('success', 'Jadwal shift staf berhasil diperbarui.');
    }

    public function destroyShift($id)
    {
        $tenant = Auth::user()->tenant;
        $shift = StaffShift::where('tenant_id', $tenant->id)->findOrFail($id);
        $shift->delete();

        return redirect()->route('manager.features')->with('success', 'Jadwal shift staf berhasil dihapus.');
    }

    // --- LEAVE REQUESTS ---
    public function approveLeave($id)
    {
        $tenant = Auth::user()->tenant;
        $leave = LeaveRequest::where('tenant_id', $tenant->id)->findOrFail($id);
        $leave->update([
            'status' => 'approved',
            'approved_by' => Auth::id()
        ]);

        return redirect()->route('manager.features')->with('success', 'Pengajuan cuti disetujui.');
    }

    public function rejectLeave($id)
    {
        $tenant = Auth::user()->tenant;
        $leave = LeaveRequest::where('tenant_id', $tenant->id)->findOrFail($id);
        $leave->update([
            'status' => 'rejected',
            'approved_by' => Auth::id()
        ]);

        return redirect()->route('manager.features', ['tab' => 'shift'])->with('success', 'Pengajuan cuti ditolak.');
    }

    // --- VOID OTORISASI ---
    public function approveVoid($id)
    {
        $tenant = Auth::user()->tenant;
        $transaction = PosTransaction::where('tenant_id', $tenant->id)->findOrFail($id);

        $transaction->update(['void_status' => 'approved']);

        // Refund product stocks if inventory type
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
            'description' => "Manager menyetujui void (pembatalan) transaksi invoice {$transaction->invoice_number}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('manager.features', ['tab' => 'approval'])->with('success', 'Transaksi void berhasil disetujui.');
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
            'description' => "Manager menolak void (pembatalan) transaksi invoice {$transaction->invoice_number}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('manager.features', ['tab' => 'approval'])->with('success', 'Transaksi void berhasil ditolak.');
    }

    // --- 4. PROMO CODES CRUD ---
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

        return redirect()->route('manager.features')->with('success', 'Kode promo berhasil dibuat.');
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

        return redirect()->route('manager.features')->with('success', 'Kode promo berhasil diperbarui.');
    }

    public function destroyPromo($id)
    {
        $tenant = Auth::user()->tenant;
        $promo = PromoCode::where('tenant_id', $tenant->id)->findOrFail($id);
        $promo->delete();

        return redirect()->route('manager.features')->with('success', 'Kode promo berhasil dihapus.');
    }

    // --- 8. COMPLAINTS TICKETING ---
    public function updateComplaint(Request $request, $id)
    {
        $tenant = Auth::user()->tenant;
        $complaint = Complaint::where('tenant_id', $tenant->id)->findOrFail($id);
        $request->validate([
            'status' => 'required|string',
            'resolution' => 'nullable|string',
        ]);

        $complaint->update($request->all());

        return redirect()->route('manager.features')->with('success', 'Tiket komplain member berhasil diperbarui.');
    }

    // --- 10. VENDORS CRUD ---
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

        return redirect()->route('manager.features')->with('success', 'Buku kontak vendor eksternal berhasil disimpan.');
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

        return redirect()->route('manager.features')->with('success', 'Kontak vendor berhasil diperbarui.');
    }

    public function destroyVendor($id)
    {
        $tenant = Auth::user()->tenant;
        $vendor = Vendor::where('tenant_id', $tenant->id)->findOrFail($id);
        $vendor->delete();

        return redirect()->route('manager.features')->with('success', 'Kontak vendor berhasil dihapus.');
    }

    // --- 9. PERENCANAAN MASTER KELAS ---
    public function storeMasterClass(Request $request)
    {
        $tenant = Auth::user()->tenant;
        $request->validate([
            'name' => 'required|string|max:255',
            'day' => 'required|string',
            'start_time' => 'required',
            'duration_minutes' => 'required|integer|min:1',
        ]);

        // Calculate end_time based on start_time and duration_minutes
        $startTime = Carbon::createFromFormat('H:i', $request->start_time);
        $endTime = (clone $startTime)->addMinutes($request->duration_minutes);

        GymClass::create([
            'tenant_id' => $tenant->id,
            'name' => $request->name,
            'day' => $request->day,
            'start_time' => $startTime->format('H:i'),
            'end_time' => $endTime->format('H:i'),
            'duration_minutes' => $request->duration_minutes,
            // trainer_id, room, max_capacity are left null/empty for Admin to allocate
        ]);

        return redirect()->route('manager.features')->with('success', 'Master Kelas dasar berhasil direncanakan. Harap hubungi Admin untuk alokasi instruktur, ruangan, dan kuota.');
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
        $endTime = (clone $startTime)->addMinutes($request->duration_minutes);

        $class->update([
            'name' => $request->name,
            'day' => $request->day,
            'start_time' => $startTime->format('H:i:s'),
            'end_time' => $endTime->format('H:i:s'),
            'duration_minutes' => $request->duration_minutes,
        ]);

        return redirect()->route('manager.features')->with('success', 'Master Kelas dasar berhasil diperbarui.');
    }

    public function destroyMasterClass($id)
    {
        $tenant = Auth::user()->tenant;
        $class = GymClass::where('tenant_id', $tenant->id)->findOrFail($id);
        $class->delete();

        return redirect()->route('manager.features')->with('success', 'Master Kelas berhasil dihapus.');
    }
}
