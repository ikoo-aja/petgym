<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Announcement;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Dashboard Utama Admin (Fokus Pengelolaan Website & Status Layanan)
     */
    public function dashboard()
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        if (!$tenant) {
            return view('admin.dashboard', [
                'user' => $user,
                'tenant' => null,
                'plan' => null,
                'websiteVisits' => 0,
                'visitsGrowth' => 0,
                'unreadLeadsCount' => 0,
                'announcements' => collect(),
                'landingUrl' => '#',
            ]);
        }

        $plan = $tenant->plan;
        $landingUrl = $tenant->publicLandingUrl();

        // 1. Metrik Pengunjung Web (Trafik Landing Page)
        $websiteVisits = 1420;
        $visitsGrowth = 12;

        // 2. Pesan Masuk / Pendaftar Baru (Leads)
        $unreadLeadsCount = 0;

        // 3. Pengumuman & Notifikasi dari Superadmin
        $announcements = Announcement::where('status', 'Active')
            ->latest()
            ->take(5)
            ->get();

        // 4. Status Akun Pengelola (Owner & Manager)
        $ownerUser = \App\Models\User::where('tenant_id', $tenant->id)->where('role', 'owner')->first();
        $managerUser = \App\Models\User::where('tenant_id', $tenant->id)->where('role', 'manager')->first();
        $totalStaffCount = \App\Models\User::where('tenant_id', $tenant->id)
            ->whereIn('role', ['owner', 'manager', 'supervisor', 'receptionist', 'trainer'])
            ->count();

        // 5. Permohonan Reset Password Pending dari Staf & Member
        $pendingResetCount = \App\Models\AdminPasswordResetRequest::where('tenant_id', $tenant->id)
            ->where('target_type', 'admin')
            ->where('status', 'pending')
            ->count();
        return view('admin.dashboard', compact(
            'user',
            'tenant',
            'plan',
            'websiteVisits',
            'visitsGrowth',
            'unreadLeadsCount',
            'announcements',
            'landingUrl',
            'ownerUser',
            'managerUser',
            'totalStaffCount',
            'pendingResetCount'
        ));
    }
}
