<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\GymClass;
use App\Models\Trainer;
use App\Models\Member;

class TrainerController extends Controller
{
    /**
     * Dashboard Utama Personal Trainer
     */
    public function dashboard()
    {
        $user = Auth::user();
        $tenant = $user->tenant;
        $trainerProfile = $user->trainerProfile ?? Trainer::where('tenant_id', $tenant?->id)->where('email', $user->email)->first();

        if (!$tenant) {
            return view('trainer.dashboard', [
                'user' => $user,
                'tenant' => null,
                'trainerProfile' => null,
                'myClasses' => collect(),
                'activeMembersCount' => 0,
            ]);
        }

        $trainerId = $trainerProfile ? $trainerProfile->id : null;

        $myClasses = GymClass::where('tenant_id', $tenant->id)
            ->where(function($q) use ($trainerId) {
                if ($trainerId) {
                    $q->where('trainer_id', $trainerId);
                }
            })
            ->with('trainer')
            ->get();

        $activeMembersCount = Member::where('tenant_id', $tenant->id)->where('status', 'active')->count();

        return view('trainer.dashboard', compact(
            'user',
            'tenant',
            'trainerProfile',
            'myClasses',
            'activeMembersCount'
        ));
    }
}
