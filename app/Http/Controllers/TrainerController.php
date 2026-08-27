<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\GymClass;
use App\Models\Trainer;
use App\Models\Member;
use App\Models\PtBooking;
use App\Models\ClassRsvp;

class TrainerController extends Controller
{
    /**
     * Dashboard Utama Personal Trainer
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
                'activeMembersCount' => 0,
            ]);
        }

        // Resolusi Trainer Profile berbasis nama user atau fallback ke trainer tenant
        $trainerProfile = Trainer::where('tenant_id', $tenant?->id)
            ->where(function($q) use ($user) {
                $q->where('name', $user->name)
                  ->orWhere('name', 'like', "%{$user->name}%");
            })
            ->first() ?? Trainer::where('tenant_id', $tenant?->id)->first();

        $trainerId = $trainerProfile ? $trainerProfile->id : null;

        // Kelas yang diajar trainer ini
        $myClassesQuery = GymClass::where('tenant_id', $tenant->id);
        if ($trainerId) {
            $myClassesQuery->where('trainer_id', $trainerId);
        }
        $myClasses = $myClassesQuery->with('trainer')->get();

        // Booking Sesi PT dari Member untuk Trainer ini
        $ptBookingsQuery = PtBooking::where('tenant_id', $tenant->id);
        if ($trainerId) {
            $ptBookingsQuery->where('trainer_id', $trainerId);
        }
        $myPtBookings = $ptBookingsQuery->with('member')->orderBy('booking_date', 'asc')->get();

        // RSVP Member untuk kelas-kelas yang diajar trainer ini
        $myClassRsvps = ClassRsvp::where('tenant_id', $tenant->id)
            ->whereIn('gym_class_id', $myClasses->pluck('id'))
            ->with(['member', 'gymClass'])
            ->latest()
            ->get();

        $activeMembersCount = Member::where('tenant_id', $tenant->id)->where('status', 'active')->count();

        return view('trainer.dashboard', compact(
            'user',
            'tenant',
            'trainerProfile',
            'myClasses',
            'myPtBookings',
            'myClassRsvps',
            'activeMembersCount'
        ));
    }
}
