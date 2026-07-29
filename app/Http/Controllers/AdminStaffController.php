<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Manager;
use App\Models\Receptionist;
use App\Models\Trainer;
use App\Models\StaffLog;

class AdminStaffController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        $staffs = User::where('tenant_id', $tenant->id)->latest()->get();

        return view('admin.staff.index', compact('staffs', 'tenant'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:4',
            'role' => 'required|string',
        ]);

        $staff = User::create([
            'tenant_id' => $tenant->id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        // Auto Sync ke tabel profil dedicated berdasarkan role
        if ($staff->role === 'manager') {
            Manager::updateOrCreate(
                ['tenant_id' => $tenant->id, 'user_id' => $staff->id],
                ['name' => $staff->name, 'email' => $staff->email, 'department' => 'Operasional', 'status' => 'active']
            );
        } elseif ($staff->role === 'receptionist') {
            Receptionist::updateOrCreate(
                ['tenant_id' => $tenant->id, 'user_id' => $staff->id],
                ['name' => $staff->name, 'email' => $staff->email, 'shift' => 'Pagi', 'status' => 'active']
            );
        } elseif ($staff->role === 'trainer') {
            Trainer::updateOrCreate(
                ['tenant_id' => $tenant->id, 'user_id' => $staff->id],
                ['name' => $staff->name, 'email' => $staff->email, 'specialization' => 'Fitness & Conditioning', 'status' => 'active']
            );
        }

        StaffLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'Tambah Akun Staf',
            'description' => "Mendaftarkan akun staf baru: {$staff->name} ({$staff->email}) sebagai {$staff->role}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.staff.index')->with('success', "Akun staf {$staff->name} berhasil dibuat.");
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        $staff = User::where('tenant_id', $tenant->id)->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $staff->id,
            'role' => 'required|string',
        ]);

        $oldRole = $staff->role;

        $staff->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ]);

        if ($request->filled('password')) {
            $staff->update(['password' => Hash::make($request->password)]);
        }

        // Hapus profil lama jika role berubah
        if ($oldRole !== $staff->role) {
            if ($oldRole === 'manager') Manager::where('user_id', $staff->id)->delete();
            if ($oldRole === 'receptionist') Receptionist::where('user_id', $staff->id)->delete();
            if ($oldRole === 'trainer') Trainer::where('user_id', $staff->id)->delete();
        }

        // Auto Sync ke tabel profil dedicated baru
        if ($staff->role === 'manager') {
            Manager::updateOrCreate(
                ['tenant_id' => $tenant->id, 'user_id' => $staff->id],
                ['name' => $staff->name, 'email' => $staff->email, 'department' => 'Operasional', 'status' => 'active']
            );
        } elseif ($staff->role === 'receptionist') {
            Receptionist::updateOrCreate(
                ['tenant_id' => $tenant->id, 'user_id' => $staff->id],
                ['name' => $staff->name, 'email' => $staff->email, 'shift' => 'Pagi', 'status' => 'active']
            );
        } elseif ($staff->role === 'trainer') {
            Trainer::updateOrCreate(
                ['tenant_id' => $tenant->id, 'user_id' => $staff->id],
                ['name' => $staff->name, 'email' => $staff->email, 'specialization' => 'Fitness & Conditioning', 'status' => 'active']
            );
        }

        StaffLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'Update Akun Staf',
            'description' => "Memperbarui akun staf {$staff->name} ({$staff->role})",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.staff.index')->with('success', "Data akun staf {$staff->name} berhasil diperbarui.");
    }

    public function resetPassword(Request $request, $id)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        $request->validate([
            'new_password' => 'required|string|min:4',
        ]);

        $staff = User::where('tenant_id', $tenant->id)->findOrFail($id);
        $staff->update([
            'password' => Hash::make($request->new_password),
        ]);

        StaffLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'Reset Password Staf',
            'description' => "Mereset password staf {$staff->name}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.staff.index')->with('success', "Password staf {$staff->name} berhasil direset.");
    }

    public function destroy(Request $request, $id)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        if ($user->id == $id) {
            return redirect()->route('admin.staff.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $staff = User::where('tenant_id', $tenant->id)->findOrFail($id);
        $staffName = $staff->name;

        // Clean up role profile records
        Manager::where('user_id', $staff->id)->delete();
        Receptionist::where('user_id', $staff->id)->delete();
        Trainer::where('user_id', $staff->id)->delete();

        $staff->delete();

        StaffLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'Hapus Akun Staf',
            'description' => "Menghapus akun staf {$staffName}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.staff.index')->with('success', "Akun staf {$staffName} berhasil dihapus.");
    }
}
