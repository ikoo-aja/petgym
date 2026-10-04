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

        // 2. Pesan Masuk / Leads Calon Klien (Form Kontak Website)
        $unreadLeadsCount = 0;

        // 3. Pengumuman & Notifikasi dari Superadmin
        $announcements = Announcement::where('status', 'Active')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'user',
            'tenant',
            'plan',
            'websiteVisits',
            'visitsGrowth',
            'unreadLeadsCount',
            'announcements',
            'landingUrl'
        ));
    }
}
