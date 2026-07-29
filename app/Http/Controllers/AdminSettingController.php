<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\StaffLog;

class AdminSettingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        return view('admin.settings.index', compact('user', 'tenant'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        $request->validate([
            'name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'owner_email' => 'required|email|max:255',
        ]);

        $tenant->update([
            'name' => $request->name,
            'owner_name' => $request->owner_name,
            'owner_email' => $request->owner_email,
        ]);

        StaffLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'Update Pengaturan Gym',
            'description' => "Memperbarui profil dan preferensi gym: {$tenant->name}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.settings.index')->with('success', 'Pengaturan profil gym berhasil diperbarui.');
    }
}
