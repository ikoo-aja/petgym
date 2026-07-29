<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Locker;
use App\Models\StaffLog;

class AdminLockerController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        $lockers = Locker::where('tenant_id', $tenant->id)
            ->orderByRaw('CAST(locker_number AS INTEGER) ASC')
            ->orderBy('locker_number')
            ->get();

        return view('admin.lockers.index', compact('lockers', 'tenant'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        $request->validate([
            'locker_number' => 'required|string|max:50',
            'status' => 'required|in:tersedia,terpakai,rusak',
        ]);

        // Cek duplikasi nomor loker di tenant
        $exists = Locker::where('tenant_id', $tenant->id)
            ->where('locker_number', $request->locker_number)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', "Nomor loker {$request->locker_number} sudah terdaftar.");
        }

        $locker = Locker::create([
            'tenant_id' => $tenant->id,
            'locker_number' => $request->locker_number,
            'status' => $request->status,
        ]);

        StaffLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'Tambah Master Loker',
            'description' => "Admin menambahkan locker baru nomor: {$locker->locker_number}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.lockers.index')->with('success', "Loker nomor {$locker->locker_number} berhasil didaftarkan.");
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        $locker = Locker::where('tenant_id', $tenant->id)->findOrFail($id);

        $request->validate([
            'locker_number' => 'required|string|max:50',
            'status' => 'required|in:tersedia,terpakai,rusak',
        ]);

        $exists = Locker::where('tenant_id', $tenant->id)
            ->where('locker_number', $request->locker_number)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', "Nomor loker {$request->locker_number} sudah terdaftar.");
        }

        $locker->update([
            'locker_number' => $request->locker_number,
            'status' => $request->status,
        ]);

        StaffLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'Update Master Loker',
            'description' => "Admin memperbarui locker {$locker->locker_number} status menjadi {$locker->status}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.lockers.index')->with('success', "Loker nomor {$locker->locker_number} berhasil diperbarui.");
    }

    public function destroy(Request $request, $id)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        $locker = Locker::where('tenant_id', $tenant->id)->findOrFail($id);
        $num = $locker->locker_number;
        $locker->delete();

        StaffLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'Hapus Master Loker',
            'description' => "Admin menghapus locker nomor: {$num}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.lockers.index')->with('success', "Loker nomor {$num} berhasil dihapus.");
    }
}
