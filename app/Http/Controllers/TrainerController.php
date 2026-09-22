<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\GymClass;
use App\Models\Trainer;
use App\Models\Member;
use App\Models\PtBooking;
use App\Models\ClassRsvp;
use Carbon\Carbon;

class TrainerController extends Controller
{
    /**
     * Helper untuk mendapatkan profil Trainer berdasarkan user yang login
     */
    protected function getTrainerProfile()
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        if (!$tenant) {
            return null;
        }

        return Trainer::where('tenant_id', $tenant->id)
            ->where(function($q) use ($user) {
                $q->where('name', $user->name)
                  ->orWhere('name', 'like', "%{$user->name}%");
            })
            ->first() ?? Trainer::where('tenant_id', $tenant->id)->first();
    }

    /**
     * 1. Beranda / Dashboard Utama Personal Trainer
     */
    public function dashboard()
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        if (!$tenant) {
            return view('trainer.dashboard', [
                'user' => $user,
                'tenant' => null,
                'trainerProfile' => null,
                'myClasses' => collect(),
                'myPtBookings' => collect(),
                'myClassRsvps' => collect(),
                'todayPtBookings' => collect(),
                'todayClasses' => collect(),
                'activePtCount' => 0,
                'completedPtCount' => 0,
            ]);
        }

        $trainerProfile = $this->getTrainerProfile();
        $trainerId = $trainerProfile ? $trainerProfile->id : null;

        // Kelas yang ditugaskan ke trainer ini
        $myClasses = GymClass::where('tenant_id', $tenant->id)
            ->when($trainerId, function($q) use ($trainerId) {
                $q->where('trainer_id', $trainerId);
            })
            ->with(['trainer', 'classRsvps'])
            ->get();

        // Booking Sesi PT
        $myPtBookings = PtBooking::where('tenant_id', $tenant->id)
            ->when($trainerId, function($q) use ($trainerId) {
                $q->where('trainer_id', $trainerId);
            })
            ->with('member')
            ->orderBy('booking_date', 'asc')
            ->get();

        // RSVP Kelas
        $myClassRsvps = ClassRsvp::where('tenant_id', $tenant->id)
            ->whereIn('gym_class_id', $myClasses->pluck('id'))
            ->with(['member', 'gymClass'])
            ->latest()
            ->get();

        // Sesi Hari Ini
        $today = Carbon::today()->format('Y-m-d');
        $todayPtBookings = $myPtBookings->filter(function($b) use ($today) {
            return $b->booking_date && Carbon::parse($b->booking_date)->format('Y-m-d') === $today;
        });

        // Kelas Hari Ini (berdasarkan hari dalam seminggu bahasa Indonesia)
        $dayNames = [
            0 => 'Minggu',
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
        ];
        $currentDayName = $dayNames[Carbon::now()->dayOfWeek] ?? '';
        $todayClasses = $myClasses->filter(function($c) use ($currentDayName) {
            return strtolower($c->day) === strtolower($currentDayName);
        });

        $activePtCount = $myPtBookings->where('status', 'scheduled')->count();
        $completedPtCount = $myPtBookings->where('status', 'completed')->count();

        return view('trainer.dashboard', compact(
            'user',
            'tenant',
            'trainerProfile',
            'myClasses',
            'myPtBookings',
            'myClassRsvps',
            'todayPtBookings',
            'todayClasses',
            'activePtCount',
            'completedPtCount'
        ));
    }

    /**
     * 2. Booking Sesi Personal Trainer (PT)
     */
    public function ptSessions(Request $request)
    {
        $user = Auth::user();
        $tenant = $user->tenant;
        $trainerProfile = $this->getTrainerProfile();
        $trainerId = $trainerProfile ? $trainerProfile->id : null;

        $query = PtBooking::where('tenant_id', $tenant->id)
            ->when($trainerId, function($q) use ($trainerId) {
                $q->where('trainer_id', $trainerId);
            })
            ->with('member');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('booking_date', $request->date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('member', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $ptBookings = $query->orderBy('booking_date', 'asc')->paginate(10);

        return view('trainer.pt-sessions', compact('ptBookings', 'trainerProfile'));
    }

    /**
     * Update Status Sesi Booking PT
     */
    public function updatePtStatus(Request $request, $id)
    {
        $tenant = Auth::user()->tenant;
        $booking = PtBooking::where('tenant_id', $tenant->id)->findOrFail($id);

        $request->validate([
            'status' => 'required|in:scheduled,completed,cancelled',
        ]);

        $booking->update([
            'status' => $request->status,
        ]);

        $statusLabel = [
            'completed' => 'diselesaikan',
            'cancelled' => 'dibatalkan',
            'scheduled' => 'dijadwalkan kembali',
        ][$request->status] ?? 'diperbarui';

        return redirect()->back()->with('success', "Status sesi latihan PT bersama member {$booking->member?->name} berhasil {$statusLabel}.");
    }

    /**
     * 3. Penugasan Kelas Mengajar
     */
    public function classes(Request $request)
    {
        $user = Auth::user();
        $tenant = $user->tenant;
        $trainerProfile = $this->getTrainerProfile();
        $trainerId = $trainerProfile ? $trainerProfile->id : null;

        $myClasses = GymClass::where('tenant_id', $tenant->id)
            ->when($trainerId, function($q) use ($trainerId) {
                $q->where('trainer_id', $trainerId);
            })
            ->with(['trainer', 'classRsvps'])
            ->orderByRaw("FIELD(day, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")
            ->get();

        return view('trainer.classes', compact('myClasses', 'trainerProfile'));
    }

    /**
     * 4. Daftar Peserta RSVP Kelas
     */
    public function rsvps(Request $request)
    {
        $user = Auth::user();
        $tenant = $user->tenant;
        $trainerProfile = $this->getTrainerProfile();
        $trainerId = $trainerProfile ? $trainerProfile->id : null;

        // Kelas yang diajar trainer ini
        $myClasses = GymClass::where('tenant_id', $tenant->id)
            ->when($trainerId, function($q) use ($trainerId) {
                $q->where('trainer_id', $trainerId);
            })
            ->get();

        $query = ClassRsvp::where('tenant_id', $tenant->id)
            ->whereIn('gym_class_id', $myClasses->pluck('id'))
            ->with(['member', 'gymClass']);

        if ($request->filled('gym_class_id')) {
            $query->where('gym_class_id', $request->gym_class_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('class_date', $request->date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('member', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $rsvps = $query->latest()->paginate(10);

        return view('trainer.rsvps', compact('rsvps', 'myClasses', 'trainerProfile'));
    }

    /**
     * Update Status Kehadiran RSVP Peserta Kelas
     */
    public function updateRsvpStatus(Request $request, $id)
    {
        $tenant = Auth::user()->tenant;
        $rsvp = ClassRsvp::where('tenant_id', $tenant->id)->findOrFail($id);

        $request->validate([
            'status' => 'required|in:confirmed,attended,cancelled',
        ]);

        $rsvp->update([
            'status' => $request->status,
        ]);

        $statusLabel = [
            'attended' => 'ditandai Hadir',
            'cancelled' => 'dibatalkan',
            'confirmed' => 'dikonfirmasi Terdaftar',
        ][$request->status] ?? 'diperbarui';

        return redirect()->back()->with('success', "Kehadiran peserta {$rsvp->member?->name} pada kelas {$rsvp->gymClass?->name} berhasil {$statusLabel}.");
    }
}
