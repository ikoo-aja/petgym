<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\Plan;
use App\Models\Invoice;
use App\Models\Announcement;
use App\Models\SystemLog;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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

        // Otomatis Buat Akun Admin Pengelola Gym
        $adminUser = $this->ensureTenantAdmin($tenant, $ownerName, $request->owner_email);

        SystemLog::create([
            'user_id' => Auth::id(),
            'action' => 'Tenant Registration',
            'description' => "Superadmin mendaftarkan tenant baru: {$tenant->name} ({$tenant->subdomain}). Akun Admin dibuat: ({$adminUser->email}).",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('superadmin.tenants')->with('success', "Tenant '{$tenant->name}' berhasil terdaftar! Akun Admin ({$adminUser->email}) dibuat otomatis. Password default: 1234");
    }

    /**
     * Memastikan Akun Admin Otomatis Terbuat & Terverifikasi untuk Tenant.
     */
    private function ensureTenantAdmin(Tenant $tenant, string $adminName, string $adminEmail): User
    {
        // Akun Admin (Pengelola Operasional Gym)
        $adminUser = User::updateOrCreate(
            ['email' => $adminEmail],
            [
                'name'      => $adminName,
                'password'  => Hash::make('1234'),
                'role'      => 'admin',
                'tenant_id' => $tenant->id,
            ]
        );
        if (!$adminUser->hasVerifiedEmail()) {
            $adminUser->markEmailAsVerified();
        }

        return $adminUser;
    }

    /**
     * Update Fitur & Add-on Tenant di Database.
     */
    public function updateTenantFeatures(Request $request, $id)
    {
        $tenant = Tenant::findOrFail($id);

        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'features' => 'nullable|array',
        ]);

        $plan = Plan::findOrFail($request->plan_id);

        $tenant->update([
            'plan_id' => $plan->id,
            'plan_name' => $plan->name,
            'features' => $request->features ?? $plan->features ?? [],
        ]);

        SystemLog::create([
            'user_id' => Auth::id(),
            'action' => 'Update Tenant Features',
            'description' => "Superadmin memperbarui paket & fitur tenant {$tenant->name} ke {$plan->name}.",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('superadmin.tenants')->with('success', "Paket & Fitur tenant '{$tenant->name}' berhasil diperbarui!");
    }

    /**
     * Toggle Nonaktifkan / Aktifkan Akses Tenant.
     */
    public function toggleTenantStatus(Request $request, $id)
    {
        $tenant = Tenant::findOrFail($id);
        $newStatus = $tenant->status === 'active' ? 'suspended' : 'active';

        $tenant->update(['status' => $newStatus]);

        SystemLog::create([
            'user_id' => Auth::id(),
            'action' => 'Toggle Status Tenant',
            'description' => "Superadmin mengubah status tenant {$tenant->name} menjadi {$newStatus}.",
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
     * Simpan Paket Sewa Baru.
     */
    public function storePlan(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'period' => 'required|string',
            'max_members' => 'required|integer',
            'features' => 'nullable|array',
        ]);

        Plan::create([
            'name' => $request->name,
            'price' => $request->price,
            'period' => $request->period,
            'max_members' => $request->max_members,
            'features' => $request->features ?? [],
            'status' => 'active',
        ]);

        return redirect()->route('superadmin.plans')->with('success', 'Paket sewa baru berhasil ditambahkan!');
    }

    /**
     * Update Paket Sewa.
     */
    public function updatePlan(Request $request, $id)
    {
        $plan = Plan::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'period' => 'required|string',
            'max_members' => 'required|integer',
            'features' => 'nullable|array',
        ]);

        $plan->update([
            'name' => $request->name,
            'price' => $request->price,
            'period' => $request->period,
            'max_members' => $request->max_members,
            'features' => $request->features ?? [],
        ]);

        return redirect()->route('superadmin.plans')->with('success', "Paket sewa '{$plan->name}' berhasil diperbarui!");
    }

    /**
     * Toggle Status Aktif Paket Sewa.
     */
    public function togglePlanStatus($id)
    {
        $plan = Plan::findOrFail($id);
        $newStatus = $plan->status === 'active' ? 'inactive' : 'active';
        $plan->update(['status' => $newStatus]);

        return redirect()->route('superadmin.plans')->with('success', "Status paket '{$plan->name}' diubah menjadi {$newStatus}!");
    }

    /**
     * Hapus Paket Sewa.
     */
    public function destroyPlan($id)
    {
        $plan = Plan::findOrFail($id);
        $plan->delete();

        return redirect()->route('superadmin.plans')->with('success', 'Paket sewa berhasil dihapus!');
    }

    /**
     * Tampilkan Halaman Riwayat Billing.
     */
    public function billing(Request $request)
    {
        $status = $request->query('status', 'all');
        $query = Invoice::with('tenant');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $invoices = $query->latest('id')->paginate(10)->withQueryString();

        return view('superadmin.billing', compact('invoices', 'status'));
    }

    /**
     * Tampilkan Halaman Pengumuman.
     */
    public function announcements()
    {
        $announcements = Announcement::latest()->get();
        return view('superadmin.announcements', compact('announcements'));
    }

    /**
     * Tampilkan Halaman Audit Trail Log.
     */
    public function logs()
    {
        $logs = SystemLog::latest()->paginate(15);
        return view('superadmin.logs', compact('logs'));
    }

    /**
     * Tampilkan Halaman Pengaturan Sistem.
     */
    public function settings()
    {
        return view('superadmin.settings');
    }

    /**
     * Tampilkan Profil Superadmin.
     */
    public function profile()
    {
        return view('superadmin.profile');
    }

    /**
     * Hapus Cache Aplikasi / Artisan Clear.
     */
    public function clearCache()
    {
        try {
            Artisan::call('optimize:clear');
            $message = 'Berhasil membersihkan seluruh cache aplikasi!';
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

        // Create Tenant in Database (Pending status until verified by Superadmin)
        $tenant = Tenant::create([
            'name' => $request->gym_name,
            'subdomain' => $subdomainFormatted,
            'owner_name' => $request->owner_name,
            'owner_email' => $request->owner_email,
            'plan_id' => $plan ? $plan->id : null,
            'plan_name' => $request->plan_name,
            'status' => 'pending',
            'joined_at' => now(),
            'expires_at' => now()->addDays(30),
            'features' => $features,
        ]);

        // Otomatis Buat Akun Owner & Admin
        $this->ensureTenantUsers($tenant, $request->owner_name, $request->owner_email);

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
            'description' => "Pendaftaran Gym '{$tenant->name}' dengan DP 50% Rp " . number_format($request->dp_price, 0, ',', '.') . " (#{$invoice->invoice_number}). Akun Owner & Admin otomatis terbuat.",
            'ip_address' => $request->ip(),
        ]);

        return redirect('/#pricing-section')->with('checkout_success', "Pendaftaran & Bukti Transfer DP 50% untuk Gym '{$tenant->name}' berhasil terkirim! Superadmin akan mengonfirmasi via WhatsApp & memverifikasi transaksi Anda.");
    }

    /**
     * Verifikasi Stage 1: Verifikasi Pembayaran DP 50% oleh Superadmin.
     */
    public function verifyDpPayment(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->update([
            'status' => 'dp_paid',
        ]);

        if ($invoice->tenant) {
            $invoice->tenant->update(['status' => 'active']);
            $this->ensureTenantAdmin(
                $invoice->tenant,
                $invoice->tenant->owner_name ?: $invoice->tenant->name,
                $invoice->tenant->owner_email
            );
        }

        SystemLog::create([
            'user_id' => Auth::id(),
            'action' => 'Verifikasi DP 50%',
            'description' => "Superadmin menyetujui verifikasi transfer DP 50% invoice {$invoice->invoice_number} untuk tenant " . ($invoice->tenant ? $invoice->tenant->name : 'N/A') . ". Web Gym & Akun diaktifkan.",
            'ip_address' => $request->ip(),
        ]);

        if ($request->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Verifikasi DP 50% disetujui!']);
        }

        return redirect()->back()->with('success', "Pembayaran DP 50% (#{$invoice->invoice_number}) berhasil diverifikasi! Akses web gym '{$invoice->tenant->name}' serta akun Owner & Admin telah DIAKTIFKAN.");
    }

    /**
     * Verifikasi Stage 2: Verifikasi Pelunasan 100% oleh Superadmin.
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
            $this->ensureTenantAdmin(
                $invoice->tenant,
                $invoice->tenant->owner_name ?: $invoice->tenant->name,
                $invoice->tenant->owner_email
            );
        }

        SystemLog::create([
            'user_id' => Auth::id(),
            'action' => 'Verifikasi Pelunasan 100%',
            'description' => "Superadmin menyetujui verifikasi pelunasan 100% invoice {$invoice->invoice_number} untuk tenant " . ($invoice->tenant ? $invoice->tenant->name : 'N/A') . ".",
            'ip_address' => $request->ip(),
        ]);

        if ($request->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Invoice diverifikasi LUNAS 100%!']);
        }

        return redirect()->back()->with('success', "Invoice {$invoice->invoice_number} berhasil diverifikasi LUNAS 100%!");
    }
}
