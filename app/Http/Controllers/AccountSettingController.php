<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\StaffLog;

class AccountSettingController extends Controller
{
    /**
     * Menampilkan halaman Pengaturan Akun (Profil & Ganti Password).
     */
    public function index()
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        return view('account.settings', compact('user', 'tenant'));
    }

    /**
     * Memperbarui informasi profil (Nama / Username dan Email).
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
        ], [
            'name.required' => 'Nama / Username wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
        ]);

        $oldName = $user->name;
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Catat di Staff Log jika memiliki tenant
        if ($tenant) {
            StaffLog::create([
                'tenant_id' => $tenant->id,
                'user_id' => $user->id,
                'action' => 'Update Profil Akun',
                'description' => "Pengguna {$oldName} memperbarui informasi akun menjadi: {$user->name} ({$user->email})",
                'ip_address' => $request->ip(),
            ]);
        }

        return redirect()->back()->with('success', 'Informasi profil akun Anda berhasil diperbarui.');
    }

    /**
     * Memperbarui password akun.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:4|confirmed',
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.min' => 'Password baru minimal 4 karakter.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak sesuai.',
        ]);

        // Cek kecocokan password saat ini
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.'])->withInput();
        }

        // Pastikan password baru tidak sama dengan yang lama
        if (Hash::check($request->new_password, $user->password)) {
            return redirect()->back()->withErrors(['new_password' => 'Password baru tidak boleh sama dengan password saat ini.'])->withInput();
        }

        $user->update([
            'password' => Hash::make($request->new_password),
            'must_change_password' => false,
        ]);

        if ($tenant) {
            StaffLog::create([
                'tenant_id' => $tenant->id,
                'user_id' => $user->id,
                'action' => 'Ganti Password Akun',
                'description' => "Pengguna {$user->name} berhasil mengubah password login akunnya.",
                'ip_address' => $request->ip(),
            ]);
        }

        return redirect()->back()->with('success', 'Kata sandi akun Anda berhasil diperbarui.');
    }
}
