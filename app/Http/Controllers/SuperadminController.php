<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\Plan;
use App\Models\Invoice;
use App\Models\Announcement;
use App\Models\SystemLog;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\TenantRegistration;
use App\Models\AdminPasswordResetRequest;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
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
        $pendingPasswordResetsCount = AdminPasswordResetRequest::where('status', 'pending')->count();

        $recentLogs = SystemLog::latest()->take(5)->get();

        // Distribusi Paket Sewa Riil dari Database
        $planDistribution = Tenant::selectRaw('plan_name, count(*) as count')
            ->groupBy('plan_name')
            ->pluck('count', 'plan_name')
            ->toArray();

        // Statistik 6 Bulan Terakhir untuk Grafik
        $monthlyStats = [];
        for ($i = 5; $i >= 0; $i--) {
            $d = now()->subMonths($i);
            $monthlyStats[] = [
                'label'   => $d->translatedFormat('M Y'),
                'short'   => $d->translatedFormat('M'),
                'tenants' => Tenant::whereMonth('joined_at', $d->month)->whereYear('joined_at', $d->year)->count(),
                'income'  => (float) Invoice::where('status', 'paid')->whereMonth('paid_at', $d->month)->whereYear('paid_at', $d->year)->sum('amount'),
            ];
        }

        return view('superadmin.dashboard', compact(
            'activeTenantsCount',
            'totalTenantsCount',
            'suspendedTenantsCount',
            'monthlyIncome',
            'newTenantsCount',
            'expiringTenantsCount',
            'pendingRegistrationsCount',
            'pendingPasswordResetsCount',
            'recentLogs',
            'planDistribution',
            'monthlyStats'
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

        $tenants = $query->with(['users', 'plan'])->latest('id')->paginate(10)->withQueryString();
        $plans = Plan::where('status', 'active')->get();

        return view('superadmin.tenants', compact('tenants', 'plans'));
    }

    /**
     * Simpan Tenant / Gym Baru ke Database.
     */
    public function storeTenant(Request $request)
    {
        $apex = config('app.tenant_apex', 'localhost');
        $rawSub = strtolower(trim((string) $request->subdomain));
        $slug = str_contains($rawSub, '.') ? explode('.', $rawSub)[0] : $rawSub;
        $subdomainFormatted = (str_ends_with($rawSub, '.' . $apex) || str_ends_with($rawSub, '.workout.id'))
            ? $rawSub
            : $rawSub . '.' . $apex;
        $request->merge([
            'subdomain' => $subdomainFormatted,
            'slug'      => $slug,
        ]);

        $request->validate([
            'name'        => 'required|string|max:255',
            'subdomain'   => 'required|string|max:255|unique:tenants,subdomain',
            'slug'        => 'required|string|max:255|unique:tenants,slug',
            'owner_email' => 'required|email|max:255',
            'plan_id'     => 'nullable|exists:plans,id',
        ], [
            'subdomain.unique' => 'Subdomain gym ini sudah digunakan. Silakan gunakan nama subdomain lain.',
            'slug.unique'      => 'Slug subdomain gym ini sudah digunakan.',
        ]);

        $ownerName = explode('@', $request->owner_email)[0];
        $ownerName = ucwords(str_replace(['.', '_', '-'], ' ', $ownerName));

        $plan = Plan::find($request->plan_id);
        $planName = $plan ? $plan->name : 'Paket Basic';
        $features = $plan ? ($plan->features ?? []) : ['Class', 'POS'];

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

        // Buat Akun Admin Pengelola Gym (HANYA Admin yang dibuat saat pendaftaran tenant)
        $adminUser = User::updateOrCreate(
            ['email' => $request->owner_email],
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
            'description' => "Superadmin mendaftarkan tenant baru: {$tenant->name} dan membuat akun Admin ({$adminUser->email}).",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('superadmin.tenants')->with('success', "Tenant '{$tenant->name}' berhasil terdaftar dan akun Admin ({$adminUser->email}) telah dibuat.");
    }

    /**
     * Update Fitur & Add-on Tenant di Database.
     */
    public function updateTenantFeatures(Request $request, $id)
    {
        $tenant = Tenant::findOrFail($id);

        $request->validate([
            'plan_id' => 'nullable|exists:plans,id',
            'features' => 'nullable|array',
        ]);

        $planId = $request->plan_id ?: $tenant->plan_id;
        $plan = $planId ? Plan::find($planId) : null;
        $planName = $plan ? $plan->name : $tenant->plan_name;

        $tenant->update([
            'plan_id' => $plan ? $plan->id : $tenant->plan_id,
            'plan_name' => $planName,
            'features' => $request->features ?? ($plan ? $plan->features : $tenant->features) ?? [],
        ]);

        SystemLog::create([
            'user_id' => Auth::id(),
            'action' => 'Update Tenant Features',
            'description' => "Superadmin memperbarui paket & fitur tenant {$tenant->name} (Paket: {$planName}).",
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

        return redirect()->route('superadmin.tenants')->with('success', "Penyewa '{$tenantName}' berhasil dihapus.");
    }

    /**
     * Reset Password Akun Admin / Pengelola Tenant (Pemulihan Darurat).
     */
    public function resetTenantAdminPassword(Request $request, $id)
    {
        $tenant = Tenant::findOrFail($id);
        
        $request->validate([
            'password' => ['nullable', 'string', \App\Rules\StaffPassword::rule()],
        ]);

        // Cari akun admin milik tenant, atau fallback ke user pengelola di tenant tersebut
        $adminUser = User::where('tenant_id', $tenant->id)
            ->where('role', 'admin')
            ->first();

        if (!$adminUser) {
            $adminUser = User::where('tenant_id', $tenant->id)
                ->whereIn('role', ['admin', 'owner', 'manager'])
                ->first();
        }

        if (!$adminUser) {
            $slug = $tenant->slug ?: (str_contains($tenant->subdomain, '.') ? explode('.', $tenant->subdomain)[0] : $tenant->subdomain);
            $apex = config('app.tenant_apex', 'localhost');
            $adminUser = User::create([
                'name' => "Admin {$tenant->name}",
                'email' => "admin.{$slug}@" . ($apex === 'localhost' ? 'petgym.local' : $apex),
                'role' => 'admin',
                'tenant_id' => $tenant->id,
            ]);
            $adminUser->markEmailAsVerified();
        }

        // Tentukan password baru (custom atau generate otomatis)
        $newPassword = $request->filled('password') 
            ? $request->password 
            : ('PetGym-' . strtoupper(Str::random(6)));

        $adminUser->update([
            'password' => Hash::make($newPassword),
            'must_change_password' => true,
            'is_active' => true,
        ]);

        SystemLog::create([
            'user_id' => Auth::id(),
            'action' => 'Reset Password Admin Tenant',
            'description' => "Superadmin me-reset password akun admin ({$adminUser->email}) untuk tenant {$tenant->name}.",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('superadmin.tenants')->with([
            'success' => "Password akun Admin untuk gym '{$tenant->name}' berhasil di-reset!",
            'reset_credentials' => [
                'tenant_name' => $tenant->name,
                'subdomain' => $tenant->subdomain,
                'login_url' => $tenant->publicLandingUrl() . '/login',
                'owner_name' => $tenant->owner_name ?? '-',
                'owner_email' => $tenant->owner_email ?? '-',
                'admin_email' => $adminUser->email,
                'admin_name' => $adminUser->name,
                'new_password' => $newPassword,
            ]
        ]);
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
            'price' => 'required|numeric|min:0',
            'max_members' => 'nullable|integer|min:0',
            'features' => 'nullable|array',
        ]);

        $plan = Plan::create([
            'name' => $request->name,
            'price' => $request->price,
            'max_members' => $request->max_members,
            'features' => $request->features ?? [],
            'status' => 'active',
        ]);

        SystemLog::create([
            'user_id' => Auth::id(),
            'action' => 'Tambah Paket Sewa',
            'description' => "Superadmin menambahkan paket sewa baru: {$plan->name} (Rp " . number_format($plan->price, 0, ',', '.') . ").",
            'ip_address' => $request->ip(),
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
            'price' => 'required|numeric|min:0',
            'max_members' => 'nullable|integer|min:0',
            'features' => 'nullable|array',
        ]);

        $plan->update([
            'name' => $request->name,
            'price' => $request->price,
            'max_members' => $request->max_members,
            'features' => $request->features ?? [],
        ]);

        SystemLog::create([
            'user_id' => Auth::id(),
            'action' => 'Update Paket Sewa',
            'description' => "Superadmin memperbarui batasan paket sewa: {$plan->name}.",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('superadmin.plans')->with('success', "Paket sewa '{$plan->name}' berhasil diperbarui!");
    }

    /**
     * Toggle Status Aktif Paket Sewa.
     */
    public function togglePlanStatus(Request $request, $id)
    {
        $plan = Plan::findOrFail($id);
        $newStatus = $plan->status === 'active' ? 'inactive' : 'active';
        $plan->update(['status' => $newStatus]);

        SystemLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'Toggle Status Paket',
            'description' => "Superadmin mengubah status paket {$plan->name} menjadi {$newStatus}.",
            'ip_address'  => $request->ip(),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'status'     => 'success',
                'new_status' => $newStatus,
                'message'    => "Status paket '{$plan->name}' diubah menjadi {$newStatus}!"
            ]);
        }

        return redirect()->route('superadmin.plans')->with('success', "Status paket '{$plan->name}' diubah menjadi {$newStatus}!");
    }

    /**
     * Hapus Paket Sewa.
     */
    public function destroyPlan(Request $request, $id)
    {
        $plan = Plan::findOrFail($id);
        $planName = $plan->name;
        $plan->delete();

        SystemLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'Hapus Paket Sewa',
            'description' => "Superadmin menghapus paket sewa: {$planName}.",
            'ip_address'  => $request->ip(),
        ]);

        return redirect()->route('superadmin.plans')->with('success', "Paket sewa '{$planName}' berhasil dihapus!");
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
     * SEC-03: Unduh bukti transfer secara aman (hanya Superadmin, dari storage privat).
     */
    public function downloadProof(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        if (empty($invoice->proof_url)) {
            abort(404, 'Bukti pembayaran tidak tersedia.');
        }

        // Jika berupa URL eksternal (mis. mock di seeder/staging)
        if (filter_var($invoice->proof_url, FILTER_VALIDATE_URL)) {
            return redirect()->away($invoice->proof_url);
        }

        // Normalisasi path: cegah path traversal
        $relative = ltrim(str_replace('\\', '/', $invoice->proof_url), '/');
        $candidates = [
            'proofs/' . basename($relative),
            $relative,
            str_replace('uploads/', '', $relative),
        ];

        foreach ($candidates as $path) {
            if (str_contains($path, '..')) {
                continue;
            }
            if (\Illuminate\Support\Facades\Storage::disk('local')->exists($path)) {
                SystemLog::create([
                    'user_id'     => Auth::id(),
                    'action'      => 'Unduh Bukti Pembayaran',
                    'description' => "Superadmin mengunduh bukti pembayaran invoice #{$invoice->invoice_number}.",
                    'ip_address'  => $request->ip(),
                ]);

                return \Illuminate\Support\Facades\Storage::disk('local')->download($path);
            }
        }

        abort(404, 'Berkas bukti pembayaran tidak ditemukan.');
    }

    /**
     * Setujui / Approve Perpanjangan Masa Sewa Web oleh Superadmin.
     */
    public function approveRenewal(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);
        $tenant = $invoice->tenant;
        if ($invoice->status === 'paid') {
            return redirect()->back()->with('error', "Tagihan invoice #{$invoice->invoice_number} sudah berstatus lunas dan telah diproses sebelumnya.");
        }

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
     * Setujui Permohonan Reset Password Admin Tenant dan Kirim Tautan Reset ke Email.
     */
    public function approvePasswordReset(Request $request, $id)
    {
        $resetReq = AdminPasswordResetRequest::findOrFail($id);

        if ($resetReq->status !== 'pending') {
            return redirect()->back()->with('error', 'Permohonan reset password ini sudah pernah diproses sebelumnya.');
        }

        $user = $resetReq->user;
        if (!$user) {
            return redirect()->back()->with('error', 'Akun admin tidak ditemukan. Silakan muat ulang halaman dan coba lagi.');
        }

        // Kirim link reset password bawaan Laravel
        try {
            $status = Password::sendResetLink(['email' => $user->email]);
        } catch (\Throwable $e) {
            report($e);
            return redirect()->back()->with('error', 'Tautan reset gagal dikirim karena koneksi ke server email terputus. Hubungi Administrator Sistem untuk memeriksa konfigurasi email.');
        }

        $resetReq->update([
            'status'           => 'approved',
            'approved_by'      => Auth::id(),
            'approved_at'      => now(),
            'token_expires_at' => now()->addMinutes(60),
            'notes'            => $request->notes,
        ]);

        SystemLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'Approve Reset Password Admin',
            'description' => "Superadmin menyetujui permohonan reset password Admin {$user->name} ({$user->email}) dan mengirimkan tautan reset ke email.",
            'ip_address'  => $request->ip(),
        ]);

        return redirect()->back()->with('success', "Permohonan reset password untuk Admin '{$user->name}' ({$user->email}) berhasil disetujui! Tautan pembuatan kata sandi baru telah dikirimkan ke email yang bersangkutan.");
    }

    /**
     * Tolak Permohonan Reset Password Admin Tenant.
     */
    public function rejectPasswordReset(Request $request, $id)
    {
        $resetReq = AdminPasswordResetRequest::findOrFail($id);

        if ($resetReq->status !== 'pending') {
            return redirect()->back()->with('error', 'Permohonan reset password ini sudah pernah diproses sebelumnya.');
        }

        $resetReq->update([
            'status'      => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'notes'       => $request->notes ?: 'Ditolak oleh Superadmin.',
        ]);

        SystemLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'Tolak Reset Password Admin',
            'description' => "Superadmin menolak permohonan reset password Admin {$resetReq->admin_name} ({$resetReq->email}).",
            'ip_address'  => $request->ip(),
        ]);

        return redirect()->back()->with('success', "Permohonan reset password Admin '{$resetReq->admin_name}' berhasil ditolak.");
    }

    /**
     * Tampilkan Halaman Daftar Pendaftaran Penyewa Baru (Leads / Prospects).
     */
    public function registrations(Request $request)
    {
        $query = TenantRegistration::with(['plan', 'createdUser']);

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

        // Ambil seluruh permohonan reset password admin yang berstatus pending
        $pendingResets = AdminPasswordResetRequest::where('status', 'pending')
            ->where(function ($q) {
                $q->where('target_type', 'superadmin')
                  ->orWhereNull('target_type');
            })
            ->get();

        // Petakan ke setiap baris data penyewa
        $registrations->getCollection()->transform(function ($reg) use ($pendingResets) {
            $reg->pending_reset = $pendingResets->first(function ($reset) use ($reg) {
                if ($reg->created_user_id && $reset->user_id == $reg->created_user_id) {
                    return true;
                }
                return strtolower(trim($reset->email)) === strtolower(trim($reg->email));
            });
            return $reg;
        });

        return view('superadmin.registrations', compact('registrations', 'plans'));
    }

    /**
     * Simpan Data Calon Penyewa Baru Secara Manual oleh Superadmin.
     */
    public function storeRegistration(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255',
            'phone'    => 'required|string|max:30',
            'plan_id'  => 'nullable|exists:plans,id',
            'notes'    => 'nullable|string',
        ], [
            'name.required'  => 'Nama calon penyewa wajib diisi.',
            'email.required' => 'Email calon penyewa wajib diisi.',
            'phone.required' => 'Nomor WhatsApp calon penyewa wajib diisi.',
        ]);

        $plan = $request->plan_id ? Plan::find($request->plan_id) : null;
        $planName = $plan ? $plan->name : 'Paket Basic';

        $registration = TenantRegistration::create([
            'name'              => $request->name,
            'email'             => $request->email,
            'phone'             => $request->phone,
            'plan_id'           => $plan ? $plan->id : null,
            'plan_name'         => $planName,
            'status'            => 'pending',
            'notes'             => $request->notes,
            'selected_features' => $plan ? ($plan->features ?? []) : ['Akses Manajemen Kelas', 'Kasir / POS Sederhana'],
        ]);

        SystemLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'Tambah Calon Penyewa',
            'description' => "Superadmin mendaftarkan calon penyewa baru secara manual: {$registration->name} ({$registration->email}).",
            'ip_address'  => $request->ip(),
        ]);

        return redirect()->route('superadmin.registrations')->with('success', "Data calon penyewa '{$registration->name}' berhasil ditambahkan ke daftar prospek!");
    }


    /**
     * Setujui Pembayaran & Buat Akun Login untuk Penyewa Baru.
     */
    public function approveRegistration(Request $request, $id)
    {
        $registration = TenantRegistration::findOrFail($id);

        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string', \App\Rules\StaffPassword::rule()],
            'plan_id'  => ['nullable', 'exists:plans,id'],
            'features' => ['nullable', 'array'],
            'notes'    => ['nullable', 'string'],
        ], [
            'email.required'    => 'Email login penyewa wajib diisi.',
            'password.required' => 'Password login awal penyewa wajib diisi.',
            'password.min'      => 'Password minimal 8 karakter.',
        ]);

        $plan = $request->plan_id ? Plan::find($request->plan_id) : $registration->plan;
        $planName = $plan ? $plan->name : ($registration->plan_name ?: 'Paket Basic');
        $features = ($plan && !empty($plan->features)) ? $plan->features : ['Akses Manajemen Kelas', 'Kasir / POS Sederhana'];

        // Generate Subdomain & Slug unik untuk tenant baru
        $baseSlug = Str::slug($registration->name) ?: 'gym';
        $slug = $baseSlug;
        $counter = 1;
        $apex = config('app.tenant_apex', 'localhost');
        while (Tenant::where('subdomain', $slug . '.' . $apex)->orWhere('subdomain', $slug . '.workout.id')->orWhere('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        $subdomainFormatted = $slug . '.' . $apex;

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

        // 3. Buat / Update Akun User Admin (HANYA Admin yang dibuat saat pendaftaran)
        $adminUser = User::updateOrCreate(
            ['email' => $request->email],
            [
                'name'      => "Admin {$registration->name}",
                'password'  => Hash::make($request->password),
                'role'      => 'admin',
                'tenant_id' => $tenant->id,
            ]
        );
        if (!$adminUser->hasVerifiedEmail()) {
            $adminUser->markEmailAsVerified();
        }

        // 4. Update status pendaftaran
        $registration->update([
            'status'            => 'approved',
            'plan_id'           => $plan ? $plan->id : null,
            'plan_name'         => $planName,
            'selected_features' => $features,
            'notes'             => $request->notes,
            'created_user_id'   => $adminUser->id,
        ]);

        SystemLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'Approve Pendaftaran Penyewa',
            'description' => "Superadmin menyetujui pendaftaran sewa #REG-{$registration->id} ({$planName}) dan membuat akun Admin ({$adminUser->email}).",
            'ip_address'  => $request->ip(),
        ]);

        return redirect()->route('superadmin.registrations')->with('success', "Pendaftaran {$registration->name} berhasil disetujui. Akun Admin ({$adminUser->email}) telah dibuat dan website aktif di domain {$tenant->subdomain}.");
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
     * Simpan & Siarkan Pengumuman Baru.
     */
    public function storeAnnouncement(Request $request)
    {
        $request->validate([
            'title'   => 'required|string|max:255',
            'message' => 'required|string',
        ], [
            'title.required'   => 'Judul pengumuman wajib diisi.',
            'message.required' => 'Isi pesan broadcast wajib diisi.',
        ]);

        $announcement = Announcement::create([
            'title'   => $request->title,
            'message' => $request->message,
            'status'  => 'Active',
            'user_id' => Auth::id(),
        ]);

        SystemLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'Kirim Pengumuman',
            'description' => "Superadmin menyiarkan pengumuman baru: {$announcement->title}.",
            'ip_address'  => $request->ip(),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'status'  => 'success',
                'message' => "Pengumuman '{$announcement->title}' berhasil disiarkan!",
                'data'    => $announcement,
            ]);
        }

        return redirect()->route('superadmin.announcements')->with('success', "Pengumuman '{$announcement->title}' berhasil disiarkan!");
    }

    /**
     * Perbarui Pengumuman.
     */
    public function updateAnnouncement(Request $request, $id)
    {
        $announcement = Announcement::findOrFail($id);

        $request->validate([
            'title'   => 'required|string|max:255',
            'message' => 'required|string',
        ], [
            'title.required'   => 'Judul pengumuman wajib diisi.',
            'message.required' => 'Isi pesan broadcast wajib diisi.',
        ]);

        $announcement->update([
            'title'   => $request->title,
            'message' => $request->message,
        ]);

        SystemLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'Update Pengumuman',
            'description' => "Superadmin memperbarui konten pengumuman: {$announcement->title}.",
            'ip_address'  => $request->ip(),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'status'  => 'success',
                'message' => "Pengumuman '{$announcement->title}' berhasil diperbarui!",
            ]);
        }

        return redirect()->route('superadmin.announcements')->with('success', "Pengumuman '{$announcement->title}' berhasil diperbarui!");
    }

    /**
     * Toggle Status Aktif / Tarik Pengumuman.
     */
    public function toggleAnnouncementStatus(Request $request, $id)
    {
        $announcement = Announcement::findOrFail($id);
        $newStatus = (strcasecmp($announcement->status, 'active') === 0) ? 'Inactive' : 'Active';

        $announcement->update(['status' => $newStatus]);

        SystemLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'Toggle Status Pengumuman',
            'description' => "Superadmin mengubah status pengumuman '{$announcement->title}' menjadi {$newStatus}.",
            'ip_address'  => $request->ip(),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'status'     => 'success',
                'new_status' => $newStatus,
                'message'    => "Status pengumuman diubah menjadi {$newStatus}.",
            ]);
        }

        return redirect()->route('superadmin.announcements')->with('success', "Status pengumuman diubah menjadi {$newStatus}.");
    }

    /**
     * Hapus Pengumuman.
     */
    public function destroyAnnouncement(Request $request, $id)
    {
        $announcement = Announcement::findOrFail($id);
        $title = $announcement->title;
        $announcement->delete();

        SystemLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'Hapus Pengumuman',
            'description' => "Superadmin menghapus pengumuman: {$title}.",
            'ip_address'  => $request->ip(),
        ]);

        if ($request->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => "Pengumuman '{$title}' berhasil dihapus."]);
        }

        return redirect()->route('superadmin.announcements')->with('success', "Pengumuman '{$title}' berhasil dihapus.");
    }

    /**
     * Tampilkan Halaman System Logs.
     */
    public function logs(Request $request)
    {
        $query = SystemLog::with('user');

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('action')) {
            $query->where('action', 'like', "%{$request->query('action')}%");
        }

        $logs = $query->latest('id')->paginate(20)->withQueryString();
        return view('superadmin.logs', compact('logs'));
    }

    /**
     * Tampilkan Halaman Pengaturan Sistem.
     */
    public function settings()
    {
        $billing = SystemSetting::getGroup('billing');
        $support = SystemSetting::getGroup('support');
        $maintenance = SystemSetting::getGroup('maintenance');
        $mail = SystemSetting::getGroup('mail');
        $settings = SystemSetting::pluck('value', 'key')->toArray();

        return view('superadmin.settings', compact('billing', 'support', 'maintenance', 'mail', 'settings'));
    }

    /**
     * Simpan Pengaturan Konfigurasi Sistem.
     */
    public function updateSettings(Request $request)
    {
        $section = $request->input('section', 'billing');

        if ($section === 'billing') {
            $data = $request->validate([
                'billing_bank_name'       => 'required|string|max:50',
                'billing_bank_account'    => 'required|string|max:50',
                'billing_bank_owner'      => 'required|string|max:100',
                'billing_bank_notes'      => 'nullable|string|max:500',
                'billing_mandiri_account' => 'nullable|string|max:50',
                'billing_qris_name'       => 'nullable|string|max:100',
                'billing_qris_code'       => 'nullable|string|max:100',
            ]);

            foreach ($data as $key => $val) {
                SystemSetting::set($key, $val, 'billing');
            }

            // Format nomor rekening BCA
            $cleanBca = preg_replace('/[^0-9]/', '', $data['billing_bank_account']);
            $fmtBca = strlen($cleanBca) >= 10 ? substr($cleanBca, 0, 4) . '-' . substr($cleanBca, 4, 4) . '-' . substr($cleanBca, 8) : $data['billing_bank_account'];
            SystemSetting::set('billing_bank_account_fmt', $fmtBca, 'billing');

            if (!empty($data['billing_mandiri_account'])) {
                $cleanMan = preg_replace('/[^0-9]/', '', $data['billing_mandiri_account']);
                $fmtMan = strlen($cleanMan) >= 13 ? substr($cleanMan, 0, 3) . '-' . substr($cleanMan, 3, 2) . '-' . substr($cleanMan, 5, 7) . '-' . substr($cleanMan, 12) : $data['billing_mandiri_account'];
                SystemSetting::set('billing_mandiri_account_fmt', $fmtMan, 'billing');
            }

            SystemLog::create([
                'user_id'     => Auth::id(),
                'action'      => 'Update Pengaturan Rekening',
                'description' => "Superadmin memperbarui rekening pembayaran sewa platform ({$data['billing_bank_name']} {$data['billing_bank_account']}).",
                'ip_address'  => $request->ip(),
            ]);

            return redirect()->route('superadmin.settings')->with('success', 'Pengaturan rekening pembayaran sewa platform berhasil disimpan!');
        }

        if ($section === 'support') {
            $data = $request->validate([
                'support_whatsapp' => 'required|string|max:30',
                'support_email'    => 'required|email|max:100',
                'support_hours'    => 'nullable|string|max:100',
            ]);

            foreach ($data as $key => $val) {
                SystemSetting::set($key, $val, 'support');
            }

            SystemLog::create([
                'user_id'     => Auth::id(),
                'action'      => 'Update Pengaturan Bantuan',
                'description' => 'Superadmin memperbarui kontak pusat bantuan resmi platform.',
                'ip_address'  => $request->ip(),
            ]);

            return redirect()->route('superadmin.settings')->with('success', 'Kontak bantuan resmi platform berhasil disimpan!');
        }

        if ($section === 'maintenance') {
            $data = $request->validate([
                'maintenance_active'        => 'nullable|in:0,1',
                'maintenance_title'         => 'required|string|max:150',
                'maintenance_message'       => 'required|string|max:1000',
                'maintenance_estimated_end' => 'nullable|string|max:100',
            ]);

            $isActive = $request->has('maintenance_active') && ($request->input('maintenance_active') === '1' || $request->input('maintenance_active') === 1) ? '1' : '0';
            SystemSetting::set('maintenance_active', $isActive, 'maintenance');
            SystemSetting::set('maintenance_title', $data['maintenance_title'], 'maintenance');
            SystemSetting::set('maintenance_message', $data['maintenance_message'], 'maintenance');
            SystemSetting::set('maintenance_estimated_end', $data['maintenance_estimated_end'] ?? '', 'maintenance');

            $statusText = $isActive === '1' ? 'AKTIF' : 'NONAKTIF';
            SystemLog::create([
                'user_id'     => Auth::id(),
                'action'      => 'Update Mode Maintenance',
                'description' => "Superadmin mengubah status mode pemeliharaan global menjadi {$statusText}.",
                'ip_address'  => $request->ip(),
            ]);

            return redirect()->route('superadmin.settings')->with('success', "Pengaturan mode pemeliharaan platform berhasil disimpan (Status: {$statusText})!");
        }

        if ($section === 'mail') {
            $data = $request->validate([
                'mail_host'         => 'required|string|max:150',
                'mail_port'         => 'required|numeric',
                'mail_username'     => 'nullable|string|max:100',
                'mail_password'     => 'nullable|string|max:100',
                'mail_encryption'   => 'nullable|string|max:20',
                'mail_from_name'    => 'required|string|max:100',
                'mail_from_address' => 'required|email|max:100',
            ]);

            foreach ($data as $key => $val) {
                SystemSetting::set($key, $val, 'mail');
            }

            SystemLog::create([
                'user_id'     => Auth::id(),
                'action'      => 'Update Pengaturan Email',
                'description' => 'Superadmin memperbarui konfigurasi email (SMTP) platform.',
                'ip_address'  => $request->ip(),
            ]);

            return redirect()->route('superadmin.settings')->with('success', 'Konfigurasi email platform berhasil disimpan!');
        }

        if ($section === 'test_mail') {
            $targetEmail = $request->input('test_email', Auth::user()->email ?? 'admin@petgym.website');
            try {
                Mail::raw(
                    "Halo,\n\nIni adalah email uji coba dari PetGym Platform untuk memverifikasi bahwa konfigurasi gateway SMTP Anda berfungsi dengan baik.\n\nWaktu pengiriman: " . now()->format('d M Y H:i:s') . " WIB\n\nSalam,\nTim PetGym",
                    function ($msg) use ($targetEmail) {
                        $msg->to($targetEmail)
                            ->subject('Uji Coba Koneksi SMTP: ' . config('app.name', 'PetGym'));
                    }
                );

                return redirect()->route('superadmin.settings')->with('success', "Email uji coba berhasil dikirim ke {$targetEmail}!");
            } catch (\Throwable $e) {
                report($e);
                return redirect()->route('superadmin.settings')->with('error', 'Gagal mengirim email uji coba. Periksa koneksi dan kredensial SMTP di berkas log server.');
            }
        }

        // Fallback untuk section lain
        foreach ($request->except(['_token', 'section']) as $k => $v) {
            SystemSetting::set($k, $v, $section);
        }

        SystemLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'Update Pengaturan',
            'description' => "Superadmin memperbarui konfigurasi sistem platform (Bagian: {$section}).",
            'ip_address'  => $request->ip(),
        ]);

        return redirect()->route('superadmin.settings')->with('success', "Konfigurasi {$section} platform berhasil disimpan!");
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
