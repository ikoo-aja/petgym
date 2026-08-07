<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Locker;
use App\Models\LockerRental;
use App\Models\Guest;
use App\Models\LostFound;
use App\Models\ReceptionistShift;
use App\Models\TrainerSession;
use App\Models\Member;
use App\Models\Trainer;
use App\Models\GymClass;
use App\Models\Complaint;
use App\Models\CheckIn;
use App\Models\PosTransaction;
use App\Models\StaffLog;
use Carbon\Carbon;

class ReceptionistController extends Controller
{
    /**
     * Dashboard Utama Resepsionis / Frontdesk (Daily Info Hub)
     */
    public function dashboard()
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        if (!$tenant) {
            return view('receptionist.dashboard', [
                'user' => $user,
                'tenant' => null,
                'todayCheckIns' => collect(),
                'recentTransactions' => collect(),
                'activeMembers' => collect(),
                'activeShift' => null,
                'dailyClasses' => collect(),
                'standbyTrainers' => collect(),
            ]);
        }

        $today = Carbon::today();

        $todayCheckIns = CheckIn::where('tenant_id', $tenant->id)
            ->whereDate('checked_in_at', $today)
            ->with('member')
            ->latest()
            ->get();

        $recentTransactions = PosTransaction::where('tenant_id', $tenant->id)
            ->where('user_id', $user->id)
            ->with('member')
            ->latest()
            ->take(10)
            ->get();

        $activeMembers = Member::where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        // Cek shift kasir aktif
        $activeShift = ReceptionistShift::where('tenant_id', $tenant->id)
            ->where('user_id', $user->id)
            ->where('status', 'open')
            ->first();

        // 10. Pusat Informasi Cepat (Daily Info Hub - Read Only)
        $dailyClasses = GymClass::where('tenant_id', $tenant->id)
            ->with('trainer')
            ->get();

        $standbyTrainers = Trainer::where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->get();

        return view('receptionist.dashboard', compact(
            'user',
            'tenant',
            'todayCheckIns',
            'recentTransactions',
            'activeMembers',
            'activeShift',
            'dailyClasses',
            'standbyTrainers'
        ));
    }

    /**
     * 7. Manajemen Loker & Peminjaman Barang
     */
    public function lockers()
    {
        $tenant = Auth::user()->tenant;

        $lockers = Locker::where('tenant_id', $tenant->id)
            ->orderByRaw('CAST(locker_number AS UNSIGNED) ASC')
            ->orderBy('locker_number')
            ->get();

        $activeRentals = LockerRental::where('tenant_id', $tenant->id)
            ->whereNull('returned_at')
            ->with(['locker', 'member'])
            ->latest()
            ->get();

        $members = Member::where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('receptionist.lockers', compact('lockers', 'activeRentals', 'members'));
    }

    public function assignLocker(Request $request)
    {
        $tenant = Auth::user()->tenant;
        $request->validate([
            'locker_id' => 'required|exists:lockers,id',
            'member_id' => 'required|exists:members,id',
        ]);

        $locker = Locker::where('tenant_id', $tenant->id)->findOrFail($request->locker_id);

        if ($locker->status !== 'tersedia') {
            return redirect()->back()->with('error', 'Loker sedang terpakai atau dalam kondisi rusak.');
        }

        // Create rental log
        LockerRental::create([
            'tenant_id' => $tenant->id,
            'locker_id' => $locker->id,
            'member_id' => $request->member_id,
            'rented_at' => Carbon::now(),
        ]);

        // Update locker status
        $locker->update(['status' => 'terpakai']);

        return redirect()->route('receptionist.lockers')->with('success', "Kunci Loker {$locker->locker_number} berhasil diberikan.");
    }

    public function returnLocker($id)
    {
        $tenant = Auth::user()->tenant;
        $locker = Locker::where('tenant_id', $tenant->id)->findOrFail($id);

        $rental = LockerRental::where('tenant_id', $tenant->id)
            ->where('locker_id', $locker->id)
            ->whereNull('returned_at')
            ->first();

        if ($rental) {
            $rental->update(['returned_at' => Carbon::now()]);
        }

        $locker->update(['status' => 'tersedia']);

        return redirect()->route('receptionist.lockers')->with('success', "Kunci Loker {$locker->locker_number} berhasil dikembalikan.");
    }

    /**
     * Buku Tamu & Lost Found Page
     */
    public function guests()
    {
        $tenant = Auth::user()->tenant;

        $guests = Guest::where('tenant_id', $tenant->id)->latest()->get();
        $lostFounds = LostFound::where('tenant_id', $tenant->id)->latest()->get();

        return view('receptionist.guests', compact('guests', 'lostFounds'));
    }

    /**
     * 8. Buku Tamu & Walk-in Leads
     */
    public function storeGuest(Request $request)
    {
        $tenant = Auth::user()->tenant;
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email',
            'notes' => 'nullable|string',
        ]);

        Guest::create(array_merge($request->all(), ['tenant_id' => $tenant->id]));

        return redirect()->route('receptionist.guests')->with('success', 'Buku tamu berhasil dicatat.');
    }

    public function convertGuestToMember(Request $request, $id)
    {
        $tenant = Auth::user()->tenant;
        $guest = Guest::where('tenant_id', $tenant->id)->findOrFail($id);

        // Auto-generate static PIN code
        $accessCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        $member = Member::create([
            'tenant_id' => $tenant->id,
            'name' => $guest->name,
            'email' => $guest->email ?? strtolower(str_replace(' ', '', $guest->name)) . '@petgym-lead.com',
            'phone' => $guest->phone,
            'gender' => 'Laki-laki',
            'access_code' => $accessCode,
            'status' => 'active',
            'expired_at' => Carbon::now()->addMonth(), // Default 1 bulan trial/aktif
        ]);

        $guest->update(['converted_to_member_id' => $member->id]);

        StaffLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => Auth::id(),
            'action' => 'Konversi Tamu ke Member',
            'description' => "Konversi walk-in guest {$guest->name} menjadi member aktif. PIN: {$accessCode}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.members.index')->with('success', "Tamu {$guest->name} berhasil dikonversi menjadi member aktif dengan PIN akses: {$accessCode}.");
    }

    /**
     * 9. Lost & Found
     */
    public function storeLostFound(Request $request)
    {
        $tenant = Auth::user()->tenant;
        $request->validate([
            'item_name' => 'required|string|max:255',
            'location_found' => 'required|string|max:255',
            'found_at' => 'required|date',
        ]);

        LostFound::create(array_merge($request->all(), ['tenant_id' => $tenant->id, 'status' => 'tercatat']));

        return redirect()->route('receptionist.guests')->with('success', 'Barang temuan Lost & Found berhasil dicatat.');
    }

    public function claimLostFound(Request $request, $id)
    {
        $tenant = Auth::user()->tenant;
        $item = LostFound::where('tenant_id', $tenant->id)->findOrFail($id);

        $request->validate([
            'claimed_by_name' => 'required|string|max:255',
        ]);

        $item->update([
            'status' => 'diklaim',
            'claimed_by_name' => $request->claimed_by_name,
            'claimed_at' => Carbon::now(),
        ]);

        return redirect()->route('receptionist.guests')->with('success', "Barang {$item->item_name} berhasil diklaim oleh {$request->claimed_by_name}.");
    }

    /**
     * Shift & Keluhan Page
     */
    public function shifts()
    {
        $tenant = Auth::user()->tenant;
        $user = Auth::user();

        $shifts = ReceptionistShift::where('tenant_id', $tenant->id)
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $complaints = Complaint::where('tenant_id', $tenant->id)->latest()->get();
        $members = Member::where('tenant_id', $tenant->id)->get();

        return view('receptionist.shifts', compact('shifts', 'complaints', 'members'));
    }

    /**
     * 5. Manajemen Shift Kasir
     */
    public function startShift(Request $request)
    {
        $tenant = Auth::user()->tenant;
        $user = Auth::user();

        $request->validate([
            'start_cash' => 'required|numeric|min:0',
        ]);

        // Cek jika ada shift terbuka
        $existing = ReceptionistShift::where('tenant_id', $tenant->id)
            ->where('user_id', $user->id)
            ->where('status', 'open')
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'Anda masih memiliki shift kasir yang terbuka.');
        }

        ReceptionistShift::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'start_cash' => $request->start_cash,
            'opened_at' => Carbon::now(),
            'status' => 'open'
        ]);

        return redirect()->route('receptionist.dashboard')->with('success', 'Shift kasir meja depan berhasil dibuka.');
    }

    public function endShift(Request $request, $id)
    {
        $tenant = Auth::user()->tenant;
        $shift = ReceptionistShift::where('tenant_id', $tenant->id)->findOrFail($id);

        $request->validate([
            'end_cash' => 'required|numeric|min:0',
        ]);

        $shift->update([
            'end_cash' => $request->end_cash,
            'closed_at' => Carbon::now(),
            'status' => 'closed'
        ]);

        return redirect()->route('receptionist.shifts')->with('success', 'Shift kasir berhasil ditutup. Setoran fisik sebesar Rp ' . number_format($request->end_cash, 0, ',', '.') . ' telah dicatat.');
    }

    /**
     * 6. Pencatatan Komplain (Ticketing)
     */
    public function storeComplaint(Request $request)
    {
        $tenant = Auth::user()->tenant;
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        Complaint::create([
            'tenant_id' => $tenant->id,
            'member_id' => $request->member_id,
            'reported_by' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'status' => 'open',
        ]);

        return redirect()->route('receptionist.shifts')->with('success', 'Keluhan member berhasil dicatat dan dikirim ke Manager.');
    }

    /**
     * 4. Booking Kelas & PT Check-In
     */
    public function checkInTrainerSession(Request $request)
    {
        $tenant = Auth::user()->tenant;
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'trainer_id' => 'required|exists:trainers,id',
            'session_date' => 'required|date',
        ]);

        TrainerSession::create([
            'tenant_id' => $tenant->id,
            'member_id' => $request->member_id,
            'trainer_id' => $request->trainer_id,
            'session_date' => $request->session_date,
            'status' => 'completed',
        ]);

        // Catat di Staff Log
        StaffLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => Auth::id(),
            'action' => 'Check-In Sesi PT',
            'description' => "Resepsionis memverifikasi sesi PT member ID: {$request->member_id} dengan Trainer ID: {$request->trainer_id}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('receptionist.dashboard')->with('success', 'Sesi PT berhasil di-checkin dan kuota sesi member terpotong.');
    }
}
