<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ChangePasswordController extends Controller
{
    /**
     * Menampilkan halaman ubah password (wajib untuk akun staf baru).
     */
    public function show()
    {
        return view('change-password');
    }

    /**
     * Memproses perubahan password dan menghapus flag must_change_password.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:4', 'confirmed'],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.min' => 'Password baru minimal 4 karakter.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        // Cek password lama benar
        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Password saat ini salah.',
            ]);
        }

        // Jangan simpan password yang sama dengan yang lama
        if (Hash::check($request->new_password, $user->password)) {
            throw ValidationException::withMessages([
                'new_password' => 'Password baru tidak boleh sama dengan password saat ini.',
            ]);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
            'must_change_password' => false,
        ]);

        return redirect()->route($user->dashboardRoute())
            ->with('success', 'Password berhasil diubah. Selamat datang, ' . $user->name . '!');
    }
}