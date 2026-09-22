<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Receptionist;
use App\Models\Trainer;
use App\Models\StaffLog;

class ManagerStaffController extends Controller
{
    /**
     * Kirim email verifikasi secara aman tanpa merusak alur pembuatan akun.
     */
    private function sendVerificationEmailSafely(User $staff): bool
    {
        try {
            $staff->sendEmailVerificationNotification();
            return true;
        } catch (\Throwable $e) {
            report($e);
            return false;
        }
    }

    /**
     * Menampilkan daftar staf operasional (Resepsionis/Kasir & Trainer).
     * Mengecualikan akun Admin, Manager, dan Owner.
     */
    public function index()
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        $staffs = User::where('tenant_id', $tenant->id)
            ->whereNotIn('role', ['admin', 'manager', 'owner', 'superadmin'])
            ->latest()
            ->get();

        return view('manager.staff.index', compact('staffs', 'tenant'));
    }

    /**
     * Mendaftarkan akun staf operasional baru (hanya role selain admin, manager, owner).
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'nullable|string|min:4',
            'role' => 'required|string|in:supervisor,receptionist,trainer',
            'phone' => 'nullable|string|max:20',
        ], [
            'name.required' => 'Nama staf wajib diisi.',
            'email.required' => 'Email login wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar, gunakan email lain.',
            'password.min' => 'Kata sandi minimal 4 karakter.',
            'role.required' => 'Role / jabatan wajib dipilih.',
            'role.in' => 'Manager dapat mendaftarkan akun staf operasional (Supervisor, Resepsionis & Personal Trainer).',
        ]);

        // Generate password default jika dikosongkan
        if (empty($request->password)) {
            $rawPassword = 'PetGym-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
        } else {
            $rawPassword = $request->password;
        }

        $staff = User::create([
            'tenant_id' => $tenant->id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($rawPassword),
            'role' => $request->role,
            'must_change_password' => true,
        ]);

        if ($staff->role === 'receptionist') {
            Receptionist::updateOrCreate(
                ['tenant_id' => $tenant->id, 'user_id' => $staff->id],
                ['name' => $staff->name, 'email' => $staff->email, 'shift' => 'Pagi', 'status' => 'active']
            );
        } elseif ($staff->role === 'trainer') {
            Trainer::updateOrCreate(
                ['tenant_id' => $tenant->id, 'user_id' => $staff->id],
                ['name' => $staff->name, 'email' => $staff->email, 'phone' => $request->phone, 'specialization' => 'Fitness & Conditioning', 'status' => 'active']
            );
        }

        StaffLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'Tambah Akun Staf Operasional',
            'description' => "Manager {$user->name} mendaftarkan staf operasional: {$staff->name} ({$staff->email}) sebagai {$staff->role}",
            'ip_address' => $request->ip(),
        ]);

        $emailSent = $this->sendVerificationEmailSafely($staff);

        if (!$emailSent) {
            return redirect()->route('manager.staff.index')->with('warning', "Akun staf {$staff->name} berhasil dibuat, TETAPI email verifikasi gagal terkirim (server email tidak terhubung). Gunakan tombol 'Kirim Verifikasi' di daftar staf setelah server email aktif.");
        }

        return redirect()->route('manager.staff.index')->with('success', "Akun staf {$staff->name} berhasil dibuat. Link verifikasi email telah dikirim ke {$staff->email}.");
    }

    /**
     * Kirim ulang email verifikasi akun staf operasional.
     */
    public function sendVerification(Request $request, $id)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        $staff = User::where('tenant_id', $tenant->id)
            ->whereNotIn('role', ['admin', 'manager', 'owner', 'superadmin'])
            ->findOrFail($id);

        if ($staff->hasVerifiedEmail()) {
            return redirect()->route('manager.staff.index')->with('success', "Email akun {$staff->name} sudah terverifikasi.");
        }

        $emailSent = $this->sendVerificationEmailSafely($staff);

        StaffLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'Kirim Ulang Verifikasi Email',
            'description' => "Manager mengirim ulang link verifikasi email untuk staf {$staff->name}",
            'ip_address' => $request->ip(),
        ]);

        if (!$emailSent) {
            return redirect()->route('manager.staff.index')->with('error', "Link verifikasi email GAGAL dikirim ke {$staff->email}.");
        }

        return redirect()->route('manager.staff.index')->with('success', "Link verifikasi email telah dikirim ulang ke {$staff->email}.");
    }

    /**
     * Hapus akun staf operasional.
     */
    public function destroy(Request $request, $id)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        if ($user->id == $id) {
            return redirect()->route('manager.staff.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $staff = User::where('tenant_id', $tenant->id)
            ->whereNotIn('role', ['admin', 'manager', 'owner', 'superadmin'])
            ->findOrFail($id);

        $staffName = $staff->name;

        Receptionist::where('user_id', $staff->id)->delete();
        Trainer::where('user_id', $staff->id)->delete();

        $staff->delete();

        StaffLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'Hapus Akun Staf Operasional',
            'description' => "Manager menghapus akun staf operasional {$staffName}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('manager.staff.index')->with('success', "Akun staf {$staffName} berhasil dihapus.");
    }
}
