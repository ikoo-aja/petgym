<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Tenant;
use App\Models\Plan;
use App\Models\TenantRegistration;
use App\Models\TenantLandingSetting;
use App\Models\StaffLog;
use App\Models\SystemLog;

class TenantOnboardingController extends Controller
{
    /**
     * Tampilkan Halaman Onboarding Setup Website untuk Admin baru.
     */
    public function show()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->isAdmin()) {
            return redirect()->route($user->dashboardRoute());
        }

        if ($user->hasSetupWebsite() && $user->tenant) {
            return redirect()->route('admin.dashboard');
        }

        // Cari data pendaftaran prospek yang diasosiasikan dengan user ini jika ada
        $registration = TenantRegistration::where('created_user_id', $user->id)
            ->orWhere('email', $user->email)
            ->latest()
            ->first();

        $planName = $registration ? $registration->plan_name : 'Paket Pro';
        $plan = $registration ? $registration->plan : Plan::where('name', 'like', "%{$planName}%")->first();
        $plans = Plan::where('status', 'active')->get();

        return view('onboarding.setup', compact('user', 'registration', 'plan', 'plans'));
    }

    /**
     * Memproses Setup & Provisioning Otomatis Website Gym.
     */
    public function provision(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->isAdmin()) {
            return redirect()->route($user->dashboardRoute());
        }

        if ($user->hasSetupWebsite() && $user->tenant) {
            return redirect()->route('admin.dashboard');
        }

        $request->validate([
            'gym_name'    => ['required', 'string', 'max:255'],
            'tagline'     => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'subdomain'   => ['required', 'string', 'max:60', 'regex:/^[a-zA-Z0-9\-]+$/'],
            'address'     => ['nullable', 'string', 'max:255'],
            'phone'       => ['nullable', 'string', 'max:30'],
            'logo'        => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
        ], [
            'gym_name.required'  => 'Nama toko / bisnis gym wajib diisi.',
            'subdomain.required' => 'Subdomain website wajib diisi.',
            'subdomain.regex'    => 'Subdomain hanya boleh berisi huruf, angka, dan tanda hubung (-), tanpa spasi atau simbol.',
        ]);

        $cleanSlug = Str::lower(trim($request->subdomain));
        $subdomainFormatted = $cleanSlug . '.workout.id';

        // Validasi keunikan subdomain di database tenants
        $subdomainExists = Tenant::where('subdomain', $subdomainFormatted)
            ->orWhere('slug', $cleanSlug)
            ->exists();

        if ($subdomainExists) {
            return back()->withInput()->withErrors([
                'subdomain' => "Subdomain '{$cleanSlug}.workout.id' sudah digunakan oleh gym lain. Silakan pilih subdomain lain.",
            ]);
        }

        // Cari data pendaftaran prospek jika ada
        $registration = TenantRegistration::where('created_user_id', $user->id)
            ->orWhere('email', $user->email)
            ->latest()
            ->first();

        $plan = null;
        if ($registration && $registration->plan_id) {
            $plan = Plan::find($registration->plan_id);
        }
        if (!$plan && $registration && $registration->plan_name) {
            $plan = Plan::where('name', 'like', "%{$registration->plan_name}%")->first();
        }
        if (!$plan) {
            $plan = Plan::first();
        }

        $planName = $plan ? $plan->name : ($registration->plan_name ?? 'Paket Pro');
        $features = ($registration && !empty($registration->selected_features))
            ? $registration->selected_features
            : ($plan ? ($plan->features ?? []) : ['members', 'pos', 'classes', 'lockers']);

        // Upload Logo jika ada
        $logoUrl = null;
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = 'logo_' . $cleanSlug . '_' . time() . '.' . $file->getClientOriginalExtension();
            $targetDir = public_path('uploads/logos');
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            $file->move($targetDir, $filename);
            $logoUrl = 'uploads/logos/' . $filename;
        }

        // 1. Buat Record Tenant
        $tenant = Tenant::create([
            'name'        => $request->gym_name,
            'subdomain'   => $subdomainFormatted,
            'slug'        => $cleanSlug,
            'logo_url'    => $logoUrl,
            'owner_name'  => $user->name,
            'owner_email' => $user->email,
            'plan_id'     => $plan ? $plan->id : null,
            'plan_name'   => $planName,
            'status'      => 'active',
            'joined_at'   => now(),
            'expires_at'  => now()->addDays(30),
            'features'    => $features,
        ]);

        // 2. Inisialisasi Pengaturan Landing Page Default
        TenantLandingSetting::create([
            'tenant_id'          => $tenant->id,
            'template'           => 'default',
            'brand_display_mode' => $logoUrl ? 'both' : 'text',
            'hero_title'         => $request->gym_name,
            'hero_tagline'       => $request->tagline ?: "Selamat datang di {$tenant->name} — Pusat Kebugaran Terbaik Anda",
            'about_text'         => $request->description ?: "{$tenant->name} adalah pusat kebugaran modern yang dilengkapi dengan instruktur profesional dan fasilitas lengkap.",
            'address'            => $request->address,
            'phone'              => $request->phone,
            'email'              => $user->email,
            'opening_hours'      => 'Senin - Minggu: 06.00 - 22.00',
            'cta_text'           => 'Mulai Latihan Sekarang',
            'cta_url'            => config('app.url'),
            'primary_color'      => '#f43f5e',
            'secondary_color'    => '#111827',
            'sections_enabled'   => TenantLandingSetting::defaultSections(),
        ]);

        // 3. Asosiasikan tenant_id ke Akun User Admin
        $user->update([
            'tenant_id' => $tenant->id,
        ]);

        // Update registration status jika ada
        if ($registration) {
            $registration->update([
                'created_user_id' => $user->id,
                'status' => 'approved',
            ]);
        }

        // 4. Audit Log
        StaffLog::create([
            'tenant_id'   => $tenant->id,
            'user_id'     => $user->id,
            'action'      => 'Setup Website & Onboarding',
            'description' => "Admin {$user->name} menyelesaikan onboarding mandiri. Website gym '{$tenant->name}' ({$tenant->subdomain}) aktif.",
            'ip_address'  => $request->ip(),
        ]);

        SystemLog::create([
            'user_id'     => $user->id,
            'action'      => 'Tenant Onboarded',
            'description' => "Tenant baru berhasil di-provision oleh Admin {$user->email}: {$tenant->name} ({$tenant->subdomain}).",
            'ip_address'  => $request->ip(),
        ]);

        return redirect()->route('admin.dashboard')
            ->with('success', "🎉 Selamat! Website & Dasbor Gym '{$tenant->name}' ({$tenant->subdomain}) berhasil diaktifkan dan siap digunakan!");
    }
}
