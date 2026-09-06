<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Member;
use App\Models\Locker;
use App\Models\LockerRental;
use App\Models\GymClass;
use App\Models\Trainer;
use App\Models\MemberPtQuota;
use App\Models\PtBooking;
use App\Models\ClassRsvp;
use App\Models\PosTransaction;
use App\Models\StaffLog;
use Carbon\Carbon;

class MemberPortalController extends Controller
{
    /**
     * Helper untuk mendapatkan atau membuat data profil Member dari User yang login.
     */
    private function getMemberProfile()
    {
        $user = Auth::user();
        if (!$user) return null;

        $tenantId = $user->tenant_id ?? 1;

        $member = Member::where('user_id', $user->id)->first();
        if (!$member) {
            $member = Member::where('email', $user->email)->where('tenant_id', $tenantId)->first();
            if ($member) {
                $member->update(['user_id' => $user->id]);
            } else {
                $member = Member::create([
                    'tenant_id' => $tenantId,
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => '081234567890',
                    'access_code' => 'MBR-' . strtoupper(substr(md5(uniqid()), 0, 6)),
                    'membership_tier' => 'basic',
                    'status' => 'active',
                    'expired_at' => now()->addDays(30),
                ]);
            }
        }

        return $member;
    }

    /**
     * Dashboard Utam Member Portal.
     */
    public function dashboard()
    {
        $user = Auth::user();
        $member = $this->getMemberProfile();
        $tenantId = $member->tenant_id;

        // Auto-check expired lockers
        $this->checkExpiredLockers($tenantId);

        // Active Locker Rental
        $activeRental = LockerRental::where('member_id', $member->id)
            ->where('status', 'active')
            ->with('locker')
            ->first();

        // Active PT Quotas
        $ptQuotas = MemberPtQuota::where('member_id', $member->id)
            ->where('remaining_sessions', '>', 0)
            ->with('trainer')
            ->get();

        // Upcoming PT Bookings
        $upcomingPtBookings = PtBooking::where('member_id', $member->id)
            ->where('status', 'scheduled')
            ->whereDate('booking_date', '>=', now())
            ->with('trainer')
            ->orderBy('booking_date')
            ->take(3)
            ->get();

        // Upcoming Class RSVPs
        $upcomingClassRsvps = ClassRsvp::where('member_id', $member->id)
            ->whereIn('status', ['confirmed', 'waitlist'])
            ->whereDate('class_date', '>=', now())
            ->with('gymClass.trainer')
            ->orderBy('class_date')
            ->take(3)
            ->get();

        // Total spending this month
        $totalSpending = PosTransaction::where('tenant_id', $tenantId)
            ->where('user_id', $user->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');

        // Classes attended total
        $classesAttendedCount = ClassRsvp::where('member_id', $member->id)
            ->where('status', 'attended')
            ->count();

        return view('member.dashboard', compact(
            'user',
            'member',
            'activeRental',
            'ptQuotas',
            'upcomingPtBookings',
            'upcomingClassRsvps',
            'totalSpending',
            'classesAttendedCount'
        ));
    }

    /**
     * 1. MANAJEMEN LOKER & PEMBAYARAN (Visual Grid & Anti Double-Booking)
     */
    public function lockers()
    {
        $member = $this->getMemberProfile();
        $tenantId = $member->tenant_id;

        $this->checkExpiredLockers($tenantId);

        $lockers = Locker::where('tenant_id', $tenantId)->orderBy('locker_number')->get();
        $activeRental = LockerRental::where('member_id', $member->id)
            ->where('status', 'active')
            ->with('locker')
            ->first();

        return view('member.lockers', compact('member', 'lockers', 'activeRental'));
    }

    /**
     * Proses Sewa Loker dengan Validasi State Management Anti Double-Booking.
     */
    public function rentLocker(Request $request)
    {
        $member = $this->getMemberProfile();
        $tenantId = $member->tenant_id;

        $request->validate([
            'locker_id' => 'required|exists:lockers,id',
            'rental_type' => 'required|in:daily,monthly',
        ]);

        $locker = Locker::where('tenant_id', $tenantId)->findOrFail($request->locker_id);

        // Anti Double-Booking Validation
        if ($locker->status !== 'tersedia') {
            return back()->with('error', "Loker #{$locker->locker_number} sudah disewa orang lain atau tidak tersedia!");
        }

        // Cek apakah user sudah punya sewa aktif
        $existingRental = LockerRental::where('member_id', $member->id)->where('status', 'active')->first();
        if ($existingRental) {
            return back()->with('error', "Anda masih memiliki sewa Loker #{$existingRental->locker->locker_number} yang sedang aktif!");
        }

        // Tentukan Durasi & Biaya
        $isMonthly = $request->rental_type === 'monthly';
        $startDate = now();
        $endDate = $isMonthly ? now()->addDays(30) : now()->addDays(1);

        // Premium Tier Free Locker Bundling
        $amount = 0;
        if ($member->membership_tier === 'premium' && $isMonthly) {
            $amount = 0; // Bundling Gratis!
        } else {
            $amount = $isMonthly ? 150000 : 15000;
        }

        // Generate 6-digit Access PIN
        $pinCode = str_pad(rand(1000, 999999), 6, '0', STR_PAD_LEFT);

        // Lock Locker State in DB (Anti Double-Booking)
        $locker->update(['status' => 'terpakai']);

        // Record Rental
        $rental = LockerRental::create([
            'tenant_id' => $tenantId,
            'locker_id' => $locker->id,
            'member_id' => $member->id,
            'rental_type' => $request->rental_type,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'pin_code' => $pinCode,
            'amount' => $amount,
            'payment_status' => 'paid',
            'status' => 'active',
            'rented_at' => now(),
        ]);

        // Invoice Record
        $paymentMethod = $request->input('payment_method', 'qris');
        PosTransaction::create([
            'tenant_id' => $tenantId,
            'user_id' => Auth::id(),
            'member_id' => $member->id,
            'invoice_number' => 'INV-LKR-' . strtoupper(substr(md5(uniqid()), 0, 6)),
            'total_amount' => $amount,
            'payment_method' => $paymentMethod,
            'type' => 'inventory',
        ]);

        return redirect()->route('member.lockers')->with('success', "Pembayaran berhasil! Loker #{$locker->locker_number} berhasil disewa. Access PIN Anda: {$pinCode}");
    }

    /**
     * Kembalikan / Selesaikan Sewa Loker.
     */
    public function returnLocker($id)
    {
        $member = $this->getMemberProfile();
        $rental = LockerRental::where('member_id', $member->id)->findOrFail($id);

        $rental->update([
            'status' => 'returned',
            'returned_at' => now(),
        ]);

        if ($rental->locker) {
            $rental->locker->update(['status' => 'tersedia']);
        }

        return redirect()->route('member.lockers')->with('success', "Sewa Loker #{$rental->locker->locker_number} berhasil diakhiri. Loker kembali tersedia.");
    }

    /**
     * 2. MULTI-TIER MEMBERSHIP & BILLING
     */
    public function membership()
    {
        $member = $this->getMemberProfile();
        return view('member.membership', compact('member'));
    }

    public function upgradeMembership(Request $request)
    {
        $member = $this->getMemberProfile();
        $request->validate([
            'tier' => 'required|in:basic,standard,premium',
        ]);

        $tierPrices = [
            'basic' => 500000,
            'standard' => 1200000,
            'premium' => 2500000,
        ];

        $tierPrice = $tierPrices[$request->tier];

        $member->update([
            'membership_tier' => $request->tier,
            'status' => 'active',
            'expired_at' => now()->addDays(30),
        ]);

        $paymentMethod = $request->input('payment_method', 'qris');
        PosTransaction::create([
            'tenant_id' => $member->tenant_id,
            'user_id' => Auth::id(),
            'member_id' => $member->id,
            'invoice_number' => 'INV-MBR-' . strtoupper(substr(md5(uniqid()), 0, 6)),
            'total_amount' => $tierPrice,
            'payment_method' => $paymentMethod,
            'type' => 'membership',
        ]);

        return redirect()->route('member.membership')->with('success', "Pembayaran Lunas! Keanggotaan Anda berhasil diperbarui ke Tier " . strtoupper($request->tier) . "!");
    }

    /**
     * 3. MODUL PERSONAL TRAINER (PT) BERBASIS KUOTA & CONFLICT-FREE BOOKING
     */
    public function pt()
    {
        $member = $this->getMemberProfile();
        $tenantId = $member->tenant_id;

        $trainers = Trainer::where('tenant_id', $tenantId)->where('status', 'active')->get();
        $quotas = MemberPtQuota::where('member_id', $member->id)->with('trainer')->get();
        $bookings = PtBooking::where('member_id', $member->id)->with('trainer')->orderBy('booking_date', 'desc')->get();

        return view('member.pt', compact('member', 'trainers', 'quotas', 'bookings'));
    }

    public function buyPtQuota(Request $request)
    {
        $member = $this->getMemberProfile();
        $tenantId = $member->tenant_id;

        $request->validate([
            'trainer_id' => 'required|exists:trainers,id',
            'sessions' => 'required|integer|in:5,10,20',
        ]);

        $trainer = Trainer::where('tenant_id', $tenantId)->findOrFail($request->trainer_id);
        $sessions = (int) $request->sessions;

        $quota = MemberPtQuota::where('member_id', $member->id)
            ->where('trainer_id', $trainer->id)
            ->first();

        if ($quota) {
            $quota->update([
                'total_sessions' => $quota->total_sessions + $sessions,
                'remaining_sessions' => $quota->remaining_sessions + $sessions,
                'status' => 'active',
            ]);
        } else {
            $quota = MemberPtQuota::create([
                'tenant_id' => $tenantId,
                'member_id' => $member->id,
                'trainer_id' => $trainer->id,
                'total_sessions' => $sessions,
                'remaining_sessions' => $sessions,
                'status' => 'active',
            ]);
        }

        $sessionPrice = 150000 * $sessions;
        $paymentMethod = $request->input('payment_method', 'qris');
        PosTransaction::create([
            'tenant_id' => $tenantId,
            'user_id' => Auth::id(),
            'member_id' => $member->id,
            'invoice_number' => 'INV-PT-' . strtoupper(substr(md5(uniqid()), 0, 6)),
            'total_amount' => $sessionPrice,
            'payment_method' => $paymentMethod,
            'type' => 'membership',
        ]);

        return redirect()->route('member.pt')->with('success', "Pembayaran Lunas! Berhasil membeli paket {$sessions} sesi PT dengan Trainer {$trainer->name}!");
    }

    public function bookPt(Request $request)
    {
        $member = $this->getMemberProfile();
        $tenantId = $member->tenant_id;

        $request->validate([
            'trainer_id' => 'required|exists:trainers,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required',
        ]);

        $bookingDateObj = Carbon::parse($request->booking_date);
        if ($bookingDateObj->isBefore(Carbon::today())) {
            return back()->with('error', 'Tanggal booking PT tidak boleh di masa lalu.');
        }

        $maxBookingDate = Carbon::today()->addDays(14);
        if ($bookingDateObj->greaterThan($maxBookingDate)) {
            return back()->with('error', "Booking PT hanya dapat dilakukan maksimal 14 hari ke depan.");
        }

        $trainer = Trainer::where('tenant_id', $tenantId)->findOrFail($request->trainer_id);

        // Check Quota
        $quota = MemberPtQuota::where('member_id', $member->id)
            ->where('trainer_id', $trainer->id)
            ->where('remaining_sessions', '>', 0)
            ->first();

        if (!$quota) {
            return back()->with('error', "Sisa kuota sesi PT Anda dengan Trainer {$trainer->name} adalah 0. Silakan beli paket kuota sesi lebih dulu!");
        }

        // Conflict Resolution Engine: Cek apakah Trainer sudah di-booking di tanggal & jam tersebut
        $conflict = PtBooking::where('trainer_id', $trainer->id)
            ->whereDate('booking_date', $request->booking_date)
            ->where('booking_time', $request->booking_time)
            ->where('status', 'scheduled')
            ->exists();

        if ($conflict) {
            return back()->with('error', "Trainer {$trainer->name} sudah memiliki jadwal booking pada jam tersebut. Silakan pilih jam atau tanggal lain!");
        }

        // Deduct Quota
        $quota->decrement('remaining_sessions');
        if ($quota->remaining_sessions <= 0) {
            $quota->update(['status' => 'exhausted']);
        }

        // Record Booking
        PtBooking::create([
            'tenant_id' => $tenantId,
            'member_id' => $member->id,
            'trainer_id' => $trainer->id,
            'booking_date' => $request->booking_date,
            'booking_time' => $request->booking_time,
            'status' => 'scheduled',
        ]);

        return redirect()->route('member.pt')->with('success', "Berhasil! Jadwal PT dengan {$trainer->name} pada " . Carbon::parse($request->booking_date)->format('d M Y') . " jam {$request->booking_time} telah terdaftar.");
    }

    /**
     * 4. CLASS RSVP & PENALTY SYSTEM
     */
    public function classes()
    {
        $member = $this->getMemberProfile();
        $tenantId = $member->tenant_id;

        $classes = GymClass::where('tenant_id', $tenantId)->with('trainer')->get();
        $myRsvps = ClassRsvp::where('member_id', $member->id)->with('gymClass')->orderBy('class_date', 'desc')->get();

        return view('member.classes', compact('member', 'classes', 'myRsvps'));
    }

    public function rsvpClass(Request $request)
    {
        $member = $this->getMemberProfile();
        $tenantId = $member->tenant_id;

        // Cek Penalti Blocked Status
        if ($member->is_penalty_blocked) {
            $blockedUntil = $member->penalty_blocked_until->format('d M Y H:i');
            return back()->with('error', "Akses booking kelas Anda ditangguhkan hingga {$blockedUntil} akibat pelanggaran penalti No-Show!");
        }

        $request->validate([
            'gym_class_id' => 'required|exists:gym_classes,id',
            'class_date' => 'required|date',
        ]);

        $gymClass = GymClass::where('tenant_id', $tenantId)->findOrFail($request->gym_class_id);

        $classDateObj = Carbon::parse($request->class_date);

        if ($classDateObj->isBefore(Carbon::today())) {
            return back()->with('error', 'Tanggal kelas tidak boleh di masa lalu.');
        }

        $maxAllowedDate = Carbon::today()->addDays(7);
        if ($classDateObj->greaterThan($maxAllowedDate)) {
            return back()->with('error', "Pendaftaran kelas hanya dibuka hingga 7 hari ke depan (maksimal tanggal " . $maxAllowedDate->format('d M Y') . ").");
        }

        // Cek RSVP ganda
        $alreadyRsvpd = ClassRsvp::where('gym_class_id', $gymClass->id)
            ->where('member_id', $member->id)
            ->whereDate('class_date', $request->class_date)
            ->whereIn('status', ['confirmed', 'waitlist'])
            ->exists();

        if ($alreadyRsvpd) {
            return back()->with('error', "Anda sudah terdaftar untuk kelas {$gymClass->name} pada tanggal tersebut!");
        }

        // Cek Kuota Kelas & Waitlist Queue
        $activeRsvpsCount = ClassRsvp::where('gym_class_id', $gymClass->id)
            ->whereDate('class_date', $request->class_date)
            ->where('status', 'confirmed')
            ->count();

        $isFull = $activeRsvpsCount >= $gymClass->max_capacity;

        if ($isFull) {
            $waitlistCount = ClassRsvp::where('gym_class_id', $gymClass->id)
                ->whereDate('class_date', $request->class_date)
                ->where('status', 'waitlist')
                ->count();

            ClassRsvp::create([
                'tenant_id' => $tenantId,
                'gym_class_id' => $gymClass->id,
                'member_id' => $member->id,
                'class_date' => $request->class_date,
                'status' => 'waitlist',
                'queue_position' => $waitlistCount + 1,
            ]);

            return redirect()->route('member.classes')->with('warning', "Kelas {$gymClass->name} sudah penuh! Anda masuk dalam Antrean Waitlist Posisi #" . ($waitlistCount + 1));
        }

        ClassRsvp::create([
            'tenant_id' => $tenantId,
            'gym_class_id' => $gymClass->id,
            'member_id' => $member->id,
            'class_date' => $request->class_date,
            'status' => 'confirmed',
        ]);

        return redirect()->route('member.classes')->with('success', "RSVP Kelas {$gymClass->name} pada tanggal " . $classDateObj->format('d M Y') . " Berhasil Dikonfirmasi!");
    }

    public function cancelRsvp($id)
    {
        $member = $this->getMemberProfile();
        $rsvp = ClassRsvp::where('member_id', $member->id)->findOrFail($id);

        $gymClassId = $rsvp->gym_class_id;
        $classDate = $rsvp->class_date;

        // Hanya pembatalan dari status CONFIRMED yang membebaskan slot kelas.
        // Pembatalan dari posisi WAITLIST tidak boleh mempromosikan siapa pun,
        // karena jumlah confirmed tidak berkurang — kalau tetap dipromosikan,
        // jumlah peserta confirmed bisa melebihi kapasitas kelas.
        $freesClassSlot = $rsvp->status === 'confirmed';

        $rsvp->update(['status' => 'cancelled']);

        if ($freesClassSlot) {
            // Auto-promote urutan pertama waitlist karena ada slot kosong
            $nextWaitlist = ClassRsvp::where('gym_class_id', $gymClassId)
                ->whereDate('class_date', $classDate)
                ->where('status', 'waitlist')
                ->orderBy('queue_position')
                ->first();

            if ($nextWaitlist) {
                $nextWaitlist->update([
                    'status' => 'confirmed',
                    'queue_position' => null,
                ]);
            }
        }

        return redirect()->route('member.classes')->with('success', "RSVP Kelas berhasil dibatalkan.");
    }

    /**
     * 5. RIWAYAT TAGIHAN & INVOICING
     */
    public function billing(Request $request)
    {
        $member = $this->getMemberProfile();
        
        $query = PosTransaction::where('tenant_id', $member->tenant_id)
            ->where('user_id', Auth::id());

        if ($request->has('type') && in_array($request->type, ['membership', 'inventory'])) {
            $query->where('type', $request->type);
        }

        $transactions = $query->latest()->get();

        return view('member.billing', compact('member', 'transactions'));
    }

    /**
     * Cron helper untuk memeriksa loker expired.
     */
    private function checkExpiredLockers($tenantId)
    {
        $expiredRentals = LockerRental::where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->whereDate('end_date', '<', now())
            ->get();

        foreach ($expiredRentals as $rental) {
            $rental->update(['status' => 'expired']);
            if ($rental->locker) {
                $rental->locker->update(['status' => 'tersedia']);
            }
        }
    }

    /**
     * 6. PANDUAN PENGGUNAAN PORTAL MEMBER (Step-by-Step Guide)
     */
    public function guide()
    {
        $member = $this->getMemberProfile();
        return view('member.guide', compact('member'));
    }

    /**
     * 7. PENGATURAN PROFIL & KEAMANAN AKUN MEMBER
     */
    public function settings()
    {
        $user = Auth::user();
        $member = $this->getMemberProfile();

        $activeRental = LockerRental::where('member_id', $member->id)
            ->where('status', 'active')
            ->with('locker')
            ->first();

        $recentTransactionsCount = PosTransaction::where('member_id', $member->id)->count();
        $checkInsCount = \App\Models\CheckIn::where('member_id', $member->id)->count();

        return view('member.settings', compact('user', 'member', 'activeRental', 'recentTransactionsCount', 'checkInsCount'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $member = $this->getMemberProfile();

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'gender' => 'required|in:Laki-laki,Perempuan',
            'address' => 'nullable|string',
        ]);

        $user->update(['name' => $request->name]);

        $member->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'address' => $request->address,
        ]);

        return redirect()->route('member.settings')->with('success', 'Informasi profil akun Anda berhasil diperbarui!');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        if (!\Illuminate\Support\Facades\Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Kata sandi saat ini tidak cocok!');
        }

        $user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->new_password)
        ]);

        return redirect()->route('member.settings')->with('success', 'Kata sandi akun Anda berhasil diubah!');
    }
}
