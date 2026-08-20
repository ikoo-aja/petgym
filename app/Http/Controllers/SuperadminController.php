<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\Plan;
use App\Models\Invoice;
use App\Models\Announcement;
use App\Models\SystemLog;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

class SuperadminController extends Controller
{
    /**
     * Tampilkan Halaman Dashboard Ringkasan.
     */
    public function dashboard()
    {
        $activeTenantsCount = Tenant::where('status', 'active')->count();
        $totalTenantsCount = Tenant::count();
        $suspendedTenantsCount = Tenant::where('status', 'suspended')->count();
        $monthlyIncome = Invoice::where('status', 'paid')->sum('amount');
        $newTenantsCount = Tenant::whereMonth('joined_at', now()->month)
                                 ->whereYear('joined_at', now()->year)
                                 ->count();
        $expiringTenantsCount = Tenant::where('status', 'active')
                                      ->whereBetween('expires_at', [now(), now()->addDays(7)])
                                      ->count();

        $recentLogs = SystemLog::latest()->take(5)->get();

        return view('superadmin.dashboard', compact(
            'activeTenantsCount',
            'totalTenantsCount',
            'suspendedTenantsCount',
            'monthlyIncome',
            'newTenantsCount',
            'expiringTenantsCount',
            'recentLogs'
        ));
    }

    /**
     * Tampilkan Halaman Kelola Penyewa.
     */
    public function tenants(Request $request)
    {
        $query = Tenant::query();

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('subdomain', 'like', "%{$search}%")
                  ->orWhere('owner_name', 'like', "%{$search}%")
                  ->orWhere('owner_email', 'like', "%{$search}%");
            });
        }

        $tenants = $query->latest('id')->paginate(4)->withQueryString();
        $plans = Plan::where('status', 'active')->get();

        return view('superadmin.tenants', compact('tenants', 'plans'));
    }

    /**
     * Simpan Tenant / Gym Baru ke Database.
     */
    public function storeTenant(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'subdomain' => 'required|string|max:255|unique:tenants,subdomain',
            'owner_email' => 'required|email|max:255',
            'plan_id' => 'nullable|exists:plans,id',
        ]);

        $ownerName = explode('@', $request->owner_email)[0];
        $ownerName = ucwords(str_replace(['.', '_', '-'], ' ', $ownerName));

        $plan = Plan::find($request->plan_id);
        $planName = $plan ? $plan->name : 'Paket Basic';
        $features = $plan ? ($plan->features ?? []) : ['Class', 'POS'];

        $rawSub = strtolower(trim($request->subdomain));
        if (strpos($rawSub, '.workout.id') === false) {
            $subdomainFormatted = $rawSub . '.workout.id';
        } else {
            $subdomainFormatted = $rawSub;
        }

        $tenant = Tenant::create([
            'name' => $request->name,
            'subdomain' => $subdomainFormatted,
            'owner_name' => $ownerName,
            'owner_email' => $request->owner_email,
            'plan_id' => $plan ? $plan->id : null,
            'plan_name' => $planName,
            'status' => 'active',
            'joined_at' => now(),
            'expires_at' => now()->addDays(14),
            'features' => $features,
        ]);

        SystemLog::create([
            'user_id' => Auth::id(),
            'action' => 'Tenant Registration',
            'description' => "Superadmin mendaftarkan tenant baru: {$tenant->name} ({$tenant->subdomain}).",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('superadmin.tenants')->with('success', "Tenant '{$tenant->name}' berhasil terdaftar di database!");
    }

    /**
     * Update Fitur & Add-on Tenant di Database.
     */
    public function updateTenantFeatures(Request $request, $id)
    {
        $tenant = Tenant::findOrFail($id);
        $features = $request->input('features', []);

        $tenant->update([
            'features' => $features,
        ]);

        SystemLog::create([
            'user_id' => Auth::id(),
            'action' => 'Update Fitur Tenant',
            'description' => "Superadmin memperbarui modul fitur aktif untuk tenant {$tenant->name}.",
            'ip_address' => $request->ip(),
        ]);

        if ($request->wantsJson()) {
            return response()->json(['status' => 'success', 'features' => $features]);
        }

        return redirect()->route('superadmin.tenants')->with('success', "Modul fitur untuk tenant '{$tenant->name}' berhasil diperbarui di database!");
    }

    /**
     * Toggle Status Tenant (Active / Suspended) di Database.
     */
    public function toggleTenantStatus(Request $request, $id)
    {
        $tenant = Tenant::findOrFail($id);
        $newStatus = $tenant->status === 'active' ? 'suspended' : 'active';

        $tenant->update(['status' => $newStatus]);

        SystemLog::create([
            'user_id' => Auth::id(),
            'action' => $newStatus === 'suspended' ? 'Suspend Account' : 'Activate Account',
            'description' => "Superadmin meng-{$newStatus} akun tenant {$tenant->name}.",
            'ip_address' => $request->ip(),
        ]);

        if ($request->wantsJson()) {
            return response()->json(['status' => 'success', 'new_status' => $newStatus]);
        }

        return redirect()->route('superadmin.tenants')->with('success', "Status tenant '{$tenant->name}' berhasil diubah menjadi {$newStatus}!");
    }

    /**
     * Hapus Tenant dari Database.
     */
    public function destroyTenant($id)
    {
        $tenant = Tenant::findOrFail($id);
        $tenantName = $tenant->name;
        $tenant->delete();

        SystemLog::create([
            'user_id' => Auth::id(),
            'action' => 'Hapus Tenant',
            'description' => "Superadmin menghapus tenant {$tenantName} dari database.",
            'ip_address' => request()->ip(),
        ]);

        if (request()->wantsJson()) {
            return response()->json(['status' => 'success']);
        }

        return redirect()->route('superadmin.tenants')->with('success', "Tenant '{$tenantName}' berhasil dihapus dari database!");
    }

    /**
     * Tampilkan Halaman Paket Sewa.
     */
    public function plans()
    {
        $plans = Plan::all();
        return view('superadmin.plans', compact('plans'));
    }

    /**
     * Simpan Paket Sewa Baru ke Database.
     */
    public function storePlan(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'max_members' => 'nullable|integer|min:1',
            'features' => 'nullable|array',
        ]);

        $features = $request->input('features', []);

        Plan::create([
            'name' => $request->name,
            'price' => $request->price,
            'max_members' => $request->max_members ?: null,
            'features' => $features,
            'status' => 'active',
        ]);

        return redirect()->route('superadmin.plans')->with('success', "Paket '{$request->name}' berhasil ditambahkan ke database!");
    }

    /**
     * Update Batasan & Fitur Paket Sewa di Database.
     */
    public function updatePlan(Request $request, $id)
    {
        $plan = Plan::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'max_members' => 'nullable|integer|min:1',
            'features' => 'nullable|array',
        ]);

        $features = $request->input('features', []);

        $plan->update([
            'name' => $request->name,
            'price' => $request->price,
            'max_members' => $request->max_members ?: null,
            'features' => $features,
        ]);

        return redirect()->route('superadmin.plans')->with('success', "Batasan & fitur paket '{$plan->name}' berhasil diperbarui di database!");
    }

    /**
     * Toggle Status Paket (Active / Archived).
     */
    public function togglePlanStatus(Request $request, $id)
    {
        $plan = Plan::findOrFail($id);
        $newStatus = $plan->status === 'active' ? 'archived' : 'active';
        $plan->update(['status' => $newStatus]);

        if ($request->wantsJson()) {
            return response()->json(['status' => 'success', 'new_status' => $newStatus]);
        }

        return redirect()->route('superadmin.plans')->with('success', "Status paket '{$plan->name}' berhasil diubah menjadi {$newStatus}!");
    }

    /**
     * Hapus Paket Sewa dari Database.
     */
    public function destroyPlan($id)
    {
        $plan = Plan::findOrFail($id);
        $planName = $plan->name;
        $plan->delete();

        return redirect()->route('superadmin.plans')->with('success', "Paket '{$planName}' telah dihapus dari database!");
    }

    /**
     * Tampilkan Halaman Keuangan & Tagihan.
     */
    public function billing(Request $request)
    {
        $query = Invoice::with('tenant');
        $status = $request->query('status', 'all');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $invoices = $query->latest('id')->get();

        return view('superadmin.billing', compact('invoices', 'status'));
    }

    /**
     * Tampilkan Halaman Pengumuman.
     */
    public function announcements()
    {
        $announcements = Announcement::latest('id')->get();
        return view('superadmin.announcements', compact('announcements'));
    }

    /**
     * Tampilkan Halaman System Logs.
     */
    public function logs()
    {
        $logs = SystemLog::latest('id')->get();
        return view('superadmin.logs', compact('logs'));
    }

    /**
     * Tampilkan Halaman Pengaturan.
     */
    public function settings()
    {
        return view('superadmin.settings');
    }

    /**
     * Tampilkan Halaman Profil.
     */
    public function profile()
    {
        return view('superadmin.profile');
    }

    /**
     * Proses Pembersihan Cache Aplikasi.
     */
    public function clearCache()
    {
        try {
            Artisan::call('optimize:clear');
            $message = 'System cache & optimization cleared successfully!';
        } catch (\Exception $e) {
            $message = 'Cache clear simulated/completed (with notes: ' . $e->getMessage() . ')';
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Handle Public Checkout & DP 50% Proof Submission from Landing Page.
     */
    public function publicCheckout(Request $request)
    {
        $request->validate([
            'gym_name' => 'required|string|max:255',
            'subdomain' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'owner_email' => 'required|email|max:255',
            'owner_phone' => 'required|string|max:255',
            'plan_name' => 'required|string',
            'dp_price' => 'required|numeric',
            'proof_file' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
        ]);

        // Upload Proof Image File
        $proofUrl = null;
        if ($request->hasFile('proof_file')) {
            $file = $request->file('proof_file');
            $filename = 'dp_proof_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            $targetDir = public_path('uploads/proofs');
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            $file->move($targetDir, $filename);
            $proofUrl = asset('uploads/proofs/' . $filename);
        }

        // Subdomain Formatting
        $rawSub = strtolower(trim($request->subdomain));
        $subdomainFormatted = (strpos($rawSub, '.workout.id') === false) ? $rawSub . '.workout.id' : $rawSub;

        // Find Plan
        $plan = Plan::where('name', 'like', "%{$request->plan_name}%")->first();
        $features = $plan ? ($plan->features ?? []) : ['Akses Manajemen Kelas', 'Kasir / POS Sederhana'];

        // Create Tenant in Database
        $tenant = Tenant::create([
            'name' => $request->gym_name,
            'subdomain' => $subdomainFormatted,
            'owner_name' => $request->owner_name,
            'owner_email' => $request->owner_email,
            'plan_id' => $plan ? $plan->id : null,
            'plan_name' => $request->plan_name,
            'status' => 'active',
            'joined_at' => now(),
            'expires_at' => now()->addDays(30),
            'features' => $features,
        ]);

        // Create Invoice Record in Database
        $invoiceCount = Invoice::count() + 1;
        $invNumber = '#INV-' . date('Y') . '-' . str_pad($invoiceCount, 3, '0', STR_PAD_LEFT);

        $invoice = Invoice::create([
            'invoice_number' => $invNumber,
            'tenant_id' => $tenant->id,
            'amount' => $request->dp_price,
            'due_date' => now()->addDays(3),
            'status' => 'pending',
            'proof_url' => $proofUrl,
        ]);

        // Audit Log
        SystemLog::create([
            'user_id' => null,
            'action' => 'Pembayaran DP Tenant',
            'description' => "Pendaftaran Gym '{$tenant->name}' dengan DP 50% Rp " . number_format($request->dp_price, 0, ',', '.') . " (#{$invoice->invoice_number}).",
            'ip_address' => $request->ip(),
        ]);

        return redirect('/#pricing-section')->with('checkout_success', "Pendaftaran & Bukti Transfer DP 50% untuk Gym '{$tenant->name}' berhasil terkirim! Tim Superadmin kami akan memverifikasi dalam 1x24 jam.");
    }

    /**
     * Verifikasi Lunas Invoice / DP Tenant oleh Superadmin.
     */
    public function verifyInvoice(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        if ($invoice->tenant) {
            $invoice->tenant->update(['status' => 'active']);
        }

        SystemLog::create([
            'user_id' => Auth::id(),
            'action' => 'Verifikasi Pembayaran',
            'description' => "Superadmin menyetujui verifikasi lunas invoice {$invoice->invoice_number} untuk tenant " . ($invoice->tenant ? $invoice->tenant->name : 'N/A') . ".",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->back()->with('success', "Invoice {$invoice->invoice_number} berhasil diverifikasi LUNAS!");
    }
}
