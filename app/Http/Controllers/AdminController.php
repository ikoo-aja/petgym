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
