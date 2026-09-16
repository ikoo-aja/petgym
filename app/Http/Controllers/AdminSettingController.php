<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\StaffLog;

class AdminSettingController extends Controller
{
    public function index()
    {
        return redirect()->route('admin.landing.edit');
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        if ($user->isOwner()) {
            return redirect()->back()->with('error', 'Mode Pemantauan Owner: Anda hanya memiliki hak akses untuk melihat data.');
        }

        $tenant = $user->tenant;

        $request->validate([
            'name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'owner_email' => 'required|email|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        $logoUrl = $tenant->logo_url;
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = 'logo_' . $tenant->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/logos'), $filename);
            $logoUrl = 'uploads/logos/' . $filename;
        }

        $tenant->update([
            'name' => $request->name,
            'owner_name' => $request->owner_name,
            'owner_email' => $request->owner_email,
            'logo_url' => $logoUrl,
        ]);

        StaffLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'Update Pengaturan Gym',
            'description' => "Memperbarui profil dan logo brand gym: {$tenant->name}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.settings.index')->with('success', 'Pengaturan profil & logo gym berhasil diperbarui.');
    }
}
