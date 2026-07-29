<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Member;
use App\Models\CheckIn;
use App\Models\PosTransaction;
use App\Models\Announcement;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Dashboard Utama Admin (Gym-Level Metrics, Alerts, SaaS Limit)
     */
    public function dashboard()
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        if (!$tenant) {
            return view('admin.dashboard', [
                'user' => $user,
                'tenant' => null,
                'checkinsToday' => 0,
                'revenueToday' => 0,
                'activeMembersCount' => 0,
                'totalMembersCount' => 0,
                'expiringMembers' => collect(),
                'announcements' => collect(),
                'maxMembersLimit' => 0,
                'usagePercent' => 0,
            ]);
        }

        // 1. Daily Metriks
        $today = Carbon::today();
        $checkinsToday = CheckIn::where('tenant_id', $tenant->id)
            ->whereDate('checked_in_at', $today)
            ->count();

        $revenueToday = PosTransaction::where('tenant_id', $tenant->id)
            ->whereDate('created_at', $today)
            ->sum('total_amount');

        $activeMembersCount = Member::where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->where(function($q) {
                $q->whereNull('expired_at')->orWhere('expired_at', '>=', Carbon::today());
            })
            ->count();

        $totalMembersCount = Member::where('tenant_id', $tenant->id)->count();

        // 2. Alert System: Expiring members (3-7 days)
        $expiringMembers = Member::where('tenant_id', $tenant->id)
            ->whereNotNull('expired_at')
            ->whereBetween('expired_at', [Carbon::today(), Carbon::today()->addDays(7)])
            ->orderBy('expired_at', 'asc')
            ->get();

        // Pengumuman dari Superadmin
        $announcements = Announcement::where('status', 'Active')
            ->latest()
            ->take(5)
            ->get();

        // 3. Status Limit SaaS
        $plan = $tenant->plan;
        $maxMembersLimit = $plan ? $plan->max_members : null;
        $usagePercent = 0;
        if ($maxMembersLimit && $maxMembersLimit > 0) {
            $usagePercent = min(100, round(($totalMembersCount / $maxMembersLimit) * 100));
        }

        return view('admin.dashboard', compact(
            'user',
            'tenant',
            'checkinsToday',
            'revenueToday',
            'activeMembersCount',
            'totalMembersCount',
            'expiringMembers',
            'announcements',
            'maxMembersLimit',
            'usagePercent'
        ));
    }
}
