<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\StaffLog;
use App\Models\Tenant;
use App\Models\TenantLandingSetting;

class TenantLandingController extends Controller
{
    /**
     * Halaman landing publik milik tenant, diakses lewat subdomain
     * (contoh: http://fitlife.localhost:8000).
     */
    public function show($slug)
    {
        $tenant = Tenant::where('slug', $slug)->first();

        if (!$tenant) {
            abort(404);
        }

        $settings = $tenant->landingSettings();

        return view('landing.default', compact('tenant', 'settings'));
    }

    /**
     * Form kustomisasi landing page (khusus Admin tenant).
     */
    public function edit(Request $request)
    {
        $user = Auth::user();

        if (!$user->isAdmin()) {
            return redirect()->route('admin.dashboard')->with('error', 'Menu Landing Page hanya dapat dikelola oleh Admin.');
        }

        $tenant = $user->tenant;
        $settings = $tenant->landingSettings();
        $landingUrl = $this->buildLandingUrl($request, $tenant);

        return view('admin.landing.index', compact('tenant', 'settings', 'landingUrl'));
    }

    /**
     * Menyimpan kustomisasi landing page.
     * Field di luar kemampuan paket langganan DIABAIKAN (enforcement di server).
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        if (!$user->isAdmin()) {
            return redirect()->route('admin.dashboard')->with('error', 'Menu Landing Page hanya dapat dikelola oleh Admin.');
        }

        $tenant = $user->tenant;
        $settings = $tenant->landingSettings();

        // 1. Aturan validasi dasar — teks bisa diedit semua paket
        $rules = [
            'hero_title'    => 'nullable|string|max:255',
            'hero_tagline'  => 'nullable|string|max:500',
            'about_text'    => 'nullable|string|max:3000',
            'address'       => 'nullable|string|max:255',
            'phone'         => 'nullable|string|max:50',
            'email'         => 'nullable|email|max:255',
            'instagram'     => 'nullable|string|max:255',
            'facebook'      => 'nullable|string|max:255',
            'opening_hours' => 'nullable|string|max:255',
            'cta_text'      => 'nullable|string|max:100',
            'cta_url'       => 'nullable|string|max:500',
        ];

        // 2. Warna hanya untuk paket Pro ke atas
        if ($tenant->canLanding('colors')) {
            $rules['primary_color']   = 'nullable|regex:/^#[0-9A-Fa-f]{6}$/';
            $rules['secondary_color'] = 'nullable|regex:/^#[0-9A-Fa-f]{6}$/';
        }

        $validated = $request->validate($rules);

        // 3. Susun data sesuai capability paket (field terlarang tidak ikut tersimpan)
        $data = [
            'hero_title'     => $validated['hero_title'] ?? null,
            'hero_tagline'   => $validated['hero_tagline'] ?? null,
            'about_text'     => $validated['about_text'] ?? null,
            'address'        => $validated['address'] ?? null,
            'phone'          => $validated['phone'] ?? null,
            'email'          => $validated['email'] ?? null,
            'instagram'      => $validated['instagram'] ?? null,
            'facebook'       => $validated['facebook'] ?? null,
            'opening_hours'  => $validated['opening_hours'] ?? null,
            'cta_text'       => $validated['cta_text'] ?? null,
            'cta_url'        => $validated['cta_url'] ?? null,
        ];

        if ($tenant->canLanding('colors')) {
            $data['primary_color']   = $validated['primary_color'] ?? null;
            $data['secondary_color'] = $validated['secondary_color'] ?? null;
        }

        if ($tenant->canLanding('features')) {
            $data['features'] = $this->collectRows($request->input('features_title', []), $request->input('features_desc', []), 6);
        }

        if ($tenant->canLanding('stats')) {
            $data['stats'] = $this->collectRows($request->input('stats_value', []), $request->input('stats_label', []), 4);
        }

        if ($tenant->canLanding('sections')) {
            $data['sections_enabled'] = $request->input('sections', TenantLandingSetting::defaultSections());
        }

        $settings->update($data);

        StaffLog::create([
            'tenant_id'    => $tenant->id,
            'user_id'      => $user->id,
            'action'       => 'Update Landing Page',
            'description'  => "Admin memperbarui konten landing page publik {$tenant->name}.",
            'ip_address'   => $request->ip(),
        ]);

        return redirect()->route('admin.landing.edit')->with('success', 'Landing page berhasil diperbarui.');
    }

    /**
     * Mengumpulkan baris berulang (fitur/statistik) menjadi array bersih.
     */
    private function collectRows(array $labels, array $descriptions, int $max): array
    {
        $rows = [];
        foreach ($labels as $i => $label) {
            $label = trim((string) $label);
            $desc  = trim((string) ($descriptions[$i] ?? ''));

            if ($label === '' && $desc === '') {
                continue;
            }

            $rows[] = ['label' => $label, 'description' => $desc];

            if (count($rows) >= $max) {
                break;
            }
        }

        return $rows;
    }

    /**
     * Membuat URL landing page berdasarkan host request saat ini
     * (contoh dari http://localhost:8000 -> http://fitlife.localhost:8000).
     */
    private function buildLandingUrl(Request $request, Tenant $tenant): string
    {
        return $request->getScheme() . '://' . $tenant->slug . '.' . $request->getHttpHost();
    }
}
