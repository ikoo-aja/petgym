<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Guest;
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
        $unreadLeadsCount = Guest::where('tenant_id', $tenant->id)
            ->whereNull('converted_to_member_id')
            ->count();

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

    /**
     * Halaman Pembayaran & Melanjutkan Penyewaan Web ke Superadmin
     */
    public function subscription()
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        if (!$tenant) {
            return redirect()->route('admin.dashboard')->with('error', 'Tenant tidak ditemukan.');
        }

        $plan = $tenant->plan;
        $invoices = \App\Models\Invoice::where('tenant_id', $tenant->id)
            ->latest()
            ->get();
        
        $pendingInvoice = $invoices->whereIn('status', ['unpaid', 'pending'])->first();
        $plans = \App\Models\Plan::all();

        return view('admin.subscription.index', compact('user', 'tenant', 'plan', 'invoices', 'pendingInvoice', 'plans'));
    }

    /**
     * Memproses Pengajuan Perpanjangan Sewa / Pembayaran ke Superadmin
     */
    public function processSubscriptionPayment(Request $request)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        if (!$tenant) {
            return back()->with('error', 'Tenant tidak ditemukan.');
        }

        $request->validate([
            'duration_months' => 'required|integer|in:1,3,6,12',
            'plan_id' => 'nullable|exists:plans,id',
            'payment_method' => 'required|string',
            'proof_file' => 'nullable|image|max:2048',
        ]);

        $selectedPlan = $request->plan_id ? \App\Models\Plan::find($request->plan_id) : $tenant->plan;
        $monthlyPrice = $selectedPlan ? $selectedPlan->price : 500000;
        $totalAmount = $monthlyPrice * (int)$request->duration_months;

        $proofUrl = null;
        if ($request->hasFile('proof_file')) {
            $path = $request->file('proof_file')->store('proofs', 'public');
            $proofUrl = asset('storage/' . $path);
        }

        $invoiceNumber = 'INV-GYM-' . strtoupper(\Illuminate\Support\Str::random(6));

        \App\Models\Invoice::create([
            'invoice_number' => $invoiceNumber,
            'tenant_id' => $tenant->id,
            'amount' => $totalAmount,
            'due_date' => Carbon::now()->addDays(3),
            'status' => $proofUrl ? 'pending' : 'unpaid',
            'proof_url' => $proofUrl,
        ]);

        return back()->with('success', 'Konfirmasi perpanjangan sewa web sebesar Rp ' . number_format($totalAmount, 0, ',', '.') . ' berhasil dikirim ke Superadmin. Status invoice Anda: ' . ($proofUrl ? 'PENDING (Verifikasi)' : 'UNPAID') . '.');
    }
}
