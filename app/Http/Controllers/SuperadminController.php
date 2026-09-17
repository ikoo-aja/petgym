<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\Plan;
use App\Models\Invoice;
use App\Models\Announcement;
use App\Models\SystemLog;
use App\Models\User;
use App\Models\TenantRegistration;
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
        $pendingRegistrationsCount = TenantRegistration::where('status', 'pending')->count();

        $recentLogs = SystemLog::latest()->take(5)->get();

        return view('superadmin.dashboard', compact(
            'activeTenantsCount',
            'totalTenantsCount',
            'suspendedTenantsCount',
            'monthlyIncome',
            'newTenantsCount',
            'expiringTenantsCount',
            'pendingRegistrationsCount',
            'recentLogs'
        ));
    }

    /**
     * Tampilkan Halaman Kelola Penyewa.
     */
    public function tenants(Request $request)
    {
        // Otomatis ubah status menjadi suspended jika sisa masa aktif sudah habis
        Tenant::where('status', '!=', 'suspended')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->update(['status' => 'suspended']);

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

        $tenants = $query->latest('id')->paginate(10)->withQueryString();
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
        $slug = str_contains($rawSub, '.') ? explode('.', $rawSub)[0] : $rawSub;
        if (strpos($rawSub, '.workout.id') === false) {
            $subdomainFormatted = $rawSub . '.workout.id';
        } else {
            $subdomainFormatted = $rawSub;
        }

        $tenant = Tenant::create([
            'name' => $request->name,
            'subdomain' => $subdomainFormatted,
            'slug' => $slug,
            'owner_name' => $ownerName,
            'owner_email' => $request->owner_email,
            'plan_id' => $plan ? $plan->id : null,
            'plan_name' => $planName,
            'status' => 'active',
            'joined_at' => now(),
            'expires_at' => now()->addDays(14),
            'features' => $features,
        ]);

        // Otomatis Buat Akun Owner & Admin Pengelola Gym
        $ownerUser = User::updateOrCreate(
            ['email' => $request->owner_email],
            [
                'name'      => $ownerName,
                'password'  => Hash::make('1234'),
                'role'      => 'owner',
                'tenant_id' => $tenant->id,
            ]
        );
        if (!$ownerUser->hasVerifiedEmail()) {
            $ownerUser->markEmailAsVerified();
        }

        $adminEmail = "admin.{$slug}@workout.id";
        $adminUser = User::updateOrCreate(
            ['email' => $adminEmail],
            [
                'name'      => "Admin {$request->name}",
                'password'  => Hash::make('1234'),
                'role'      => 'admin',
                'tenant_id' => $tenant->id,
            ]
        );
        if (!$adminUser->hasVerifiedEmail()) {
            $adminUser->markEmailAsVerified();
        }

        SystemLog::create([
            'user_id' => Auth::id(),
            'action' => 'Tenant Registration',
            'description' => "Superadmin mendaftarkan tenant baru: {$tenant->name}.",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('superadmin.tenants')->with('success', "Tenant '{$tenant->name}' berhasil terdaftar! Akun Admin ({$adminUser->email}) dan Owner ({$ownerUser->email}) dibuat otomatis. Password default: 1234");
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
     * Tampilkan Halaman Keuangan & Tagihan Perpanjangan Sewa Web.
     */
    public function billing(Request $request)
    {
        $status = $request->query('status', 'all');
        $query = Invoice::with('tenant');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('tenant', function ($t) use ($search) {
                      $t->where('name', 'like', "%{$search}%")
                        ->orWhere('subdomain', 'like', "%{$search}%")
                        ->orWhere('owner_name', 'like', "%{$search}%");
                  });
            });
        }

        $invoices = $query->latest('id')->paginate(10)->withQueryString();

        return view('superadmin.billing', compact('invoices', 'status'));
    }

    /**
     * Setujui / Approve Perpanjangan Masa Sewa Web oleh Superadmin.
     */
    public function approveRenewal(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);
        $tenant = $invoice->tenant;

        $durationMonths = max(1, (int) ($invoice->duration_months ?: 1));
        $daysToAdd = $durationMonths * 30;

        if ($tenant) {
            // Perpanjang masa aktif
            if ($tenant->expires_at && $tenant->expires_at->isFuture()) {
                $tenant->expires_at = $tenant->expires_at->addDays($daysToAdd);
            } else {
                $tenant->expires_at = now()->addDays($daysToAdd);
            }

            $tenant->status = 'active';

            if ($invoice->plan_name) {
                $tenant->plan_name = $invoice->plan_name;
                $plan = Plan::where('name', 'like', "%{$invoice->plan_name}%")->first();
                if ($plan) {
                    $tenant->plan_id = $plan->id;
                }
            }

            $tenant->save();
        }

        $invoice->update([
            'status'  => 'paid',
            'paid_at' => now(),
        ]);

        SystemLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'Approve Perpanjangan Sewa',
            'description' => "Superadmin menyetujui perpanjangan sewa website gym " . ($tenant ? $tenant->name : 'N/A') . " ({$durationMonths} bulan) sebesar Rp " . number_format($invoice->amount, 0, ',', '.') . " (#{$invoice->invoice_number}).",
            'ip_address'  => $request->ip(),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'status'  => 'success',
                'message' => "Perpanjangan sewa untuk " . ($tenant ? $tenant->name : 'tenant') . " berhasil disetujui!",
            ]);
        }

        return redirect()->back()->with(
            'success',
            "Perpanjangan sewa untuk " . ($tenant ? $tenant->name : 'tenant') . " BERHASIL DISETUJUI! Masa aktif bertambah +{$durationMonths} bulan ({$daysToAdd} hari)."
        );
    }

    /**
     * Tolak Pengajuan Perpanjangan Sewa.
     */
    public function rejectRenewal(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->update([
            'status' => 'rejected',
            'notes'  => $request->notes ?? $invoice->notes,
        ]);

        SystemLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'Reject Perpanjangan Sewa',
            'description' => "Superadmin menolak pengajuan tagihan #{$invoice->invoice_number} untuk tenant " . ($invoice->tenant ? $invoice->tenant->name : 'N/A') . ".",
            'ip_address'  => $request->ip(),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'status'  => 'success',
                'message' => "Tagihan #{$invoice->invoice_number} ditolak.",
            ]);
        }

        return redirect()->back()->with('success', "Pengajuan tagihan perpanjangan #{$invoice->invoice_number} berhasil ditolak.");
    }

    /**
     * Verifikasi Legacy / Kompatibilitas Invoice.
     */
    public function verifyInvoice(Request $request, $id)
    {
        return $this->approveRenewal($request, $id);
    }

    public function verifyDpPayment(Request $request, $id)
    {
        return $this->approveRenewal($request, $id);
    }

    /**
     * Tampilkan Halaman Daftar Pendaftaran Penyewa Baru (Leads / Prospects).
     */
    public function registrations(Request $request)
    {
        $query = TenantRegistration::with('plan');

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('plan_name', 'like', "%{$search}%");
            });
        }

        $registrations = $query->latest('id')->paginate(10)->withQueryString();
        $plans = Plan::where('status', 'active')->get();

        return view('superadmin.registrations', compact('registrations', 'plans'));
    }

    /**
     * Setujui Pembayaran & Buat Akun Login untuk Penyewa Baru.
     */
    public function approveRegistration(Request $request, $id)
    {
        $registration = TenantRegistration::findOrFail($id);

        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string', 'min:4'],
            'plan_id'  => ['nullable', 'exists:plans,id'],
            'features' => ['nullable', 'array'],
            'notes'    => ['nullable', 'string'],
        ], [
            'email.required'    => 'Email login penyewa wajib diisi.',
            'password.required' => 'Password login awal penyewa wajib diisi.',
            'password.min'      => 'Password minimal 4 karakter.',
        ]);

        $plan = $request->plan_id ? Plan::find($request->plan_id) : $registration->plan;
        $planName = $plan ? $plan->name : $registration->plan_name;
        $features = $request->features ?? ($plan ? ($plan->features ?? []) : ['members', 'pos', 'classes', 'lockers']);

        // Generate Subdomain & Slug unik untuk tenant baru
        $baseSlug = Str::slug($registration->name) ?: 'gym';
        $slug = $baseSlug;
        $counter = 1;
        while (Tenant::where('subdomain', $slug . '.workout.id')->orWhere('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        $subdomainFormatted = $slug . '.workout.id';

        // 1. Buat Record Tenant di database agar langsung tampil di Kelola Penyewa
        $tenant = Tenant::create([
            'name'        => $registration->name . ' Gym',
            'subdomain'   => $subdomainFormatted,
            'slug'        => $slug,
            'owner_name'  => $registration->name,
            'owner_email' => $request->email,
            'plan_id'     => $plan ? $plan->id : null,
            'plan_name'   => $planName,
            'status'      => 'active',
            'joined_at'   => now(),
            'expires_at'  => now()->addDays(30), // Masa aktif 30 hari
            'features'    => $features,
        ]);

        // 2. Inisialisasi Pengaturan Landing Page bawaan
        $tenant->landingSettings();

        // 3. Buat / Update Akun User Pengelola (Admin Gym)
        $user = User::updateOrCreate(
            ['email' => $request->email],
            [
                'name'      => $registration->name,
                'password'  => Hash::make($request->password),
                'role'      => 'admin',
                'tenant_id' => $tenant->id,
            ]
        );

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        // 4. Update status pendaftaran
        $registration->update([
            'status'            => 'approved',
            'plan_id'           => $plan ? $plan->id : null,
            'plan_name'         => $planName,
            'selected_features' => $features,
            'notes'             => $request->notes,
            'created_user_id'   => $user->id,
        ]);

        SystemLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'Approve Pendaftaran Penyewa',
            'description' => "Superadmin menyetujui pendaftaran sewa #REG-{$registration->id} ({$planName}) dan membuat akun pengelola.",
            'ip_address'  => $request->ip(),
        ]);

        return redirect()->route('superadmin.registrations')->with('success', "Pendaftaran {$registration->name} BERHASIL DISETUJUI! Akun login telah dibuat ({$user->email} / Password: {$request->password}) dan website gym telah aktif di domain {$tenant->subdomain}.");
    }

    /**
     * Update Status Prospek (misal: contacted / rejected / pending).
     */
    public function updateRegistrationStatus(Request $request, $id)
    {
        $registration = TenantRegistration::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,contacted,approved,rejected',
            'notes'  => 'nullable|string',
        ]);

        $registration->update([
            'status' => $request->status,
            'notes'  => $request->notes ?? $registration->notes,
        ]);

        SystemLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'Update Status Pendaftaran',
            'description' => "Superadmin memperbarui status pendaftaran sewa #REG-{$registration->id} menjadi " . strtoupper($request->status) . ".",
            'ip_address'  => $request->ip(),
        ]);

        return redirect()->route('superadmin.registrations')->with('success', "Status pendaftaran {$registration->name} diperbarui menjadi " . strtoupper($request->status) . ".");
    }

    /**
     * Hapus Pendaftaran Prospek.
     */
    public function destroyRegistration($id)
    {
        $registration = TenantRegistration::findOrFail($id);
        $name = $registration->name;
        $registration->delete();

        SystemLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'Hapus Pendaftaran Penyewa',
            'description' => "Superadmin menghapus arsip data pendaftaran #REG-{$id}.",
            'ip_address'  => request()->ip(),
        ]);

        return redirect()->route('superadmin.registrations')->with('success', "Data pendaftaran {$name} berhasil dihapus.");
    }

    /**
     * Tampilkan Halaman Pengumuman Sistem.
     */
    public function announcements()
    {
        $announcements = Announcement::latest()->paginate(10);
        return view('superadmin.announcements', compact('announcements'));
    }

    /**
     * Tampilkan Halaman System Logs.
     */
    public function logs()
    {
        $logs = SystemLog::with('user')->latest()->paginate(20);
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
     * Tampilkan Halaman Profil Superadmin.
     */
    public function profile()
    {
        return view('superadmin.profile');
    }

    /**
     * Bersihkan Cache Sistem.
     */
    public function clearCache()
    {
        Artisan::call('optimize:clear');
        return redirect()->back()->with('success', 'Cache sistem dan view berhasil dibersihkan!');
    }
}
