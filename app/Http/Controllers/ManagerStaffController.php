<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Receptionist;
use App\Models\Trainer;
use App\Models\StaffLog;
use App\Models\AdminPasswordResetRequest;
use Illuminate\Support\Facades\Password;
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
     * Menampilkan daftar staf operasional (Supervisor, Resepsionis/Kasir & Trainer).
     * Mengecualikan akun Admin, Manager, Owner, Superadmin, dan Member.
     */
    public function index()
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        $staffs = User::with('latestPendingPasswordReset')
            ->where('tenant_id', $tenant->id)
            ->whereIn('role', ['supervisor', 'receptionist', 'trainer'])
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
            'specialization' => 'nullable|string|max:255',
        ], [
            'name.required' => 'Nama staf wajib diisi.',
            'email.required' => 'Email login wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar, gunakan email lain.',
            'role.required' => 'Role / jabatan wajib dipilih.',
            'role.in' => 'Manager dapat mendaftarkan akun staf operasional (Supervisor, Resepsionis & Personal Trainer).',
        ]);
        if ($tenant && !$tenant->canAddStaff()) {
            $maxStaff = $tenant->maxStaff();
            return redirect()->back()->with('error', "Gagal menambahkan akun staf: Kuota maksimal karyawan untuk {$tenant->plan_name} ({$maxStaff} Akun Karyawan) telah tercapai. Silakan hubungi Admin untuk upgrade paket.");
        }

        if ($tenant && !$tenant->isStaffRoleAllowed($request->role)) {
            return redirect()->back()->with('error', "Paket {$tenant->plan_name} belum mendukung pengelolaan akun Personal Trainer (PT). Silakan lakukan upgrade ke Paket Pro atau Enterprise.");
        }

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
                [
                    'name' => $staff->name,
                    'email' => $staff->email,
                    'phone' => $request->phone,
                    'specialization' => $request->specialization ?: 'Fitness & Conditioning',
                    'status' => 'active',
                ]
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
            ->whereIn('role', ['supervisor', 'receptionist', 'trainer'])
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
            ->whereIn('role', ['supervisor', 'receptionist', 'trainer'])
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

    /**
     * Mengaktifkan / Menonaktifkan akun staf operasional (Supervisor, Kasir, Trainer).
     */
    public function toggleStatus(Request $request, $id)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        if ($user->id == $id) {
            return redirect()->route('manager.staff.index')->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $staff = User::where('tenant_id', $tenant->id)
            ->whereIn('role', ['supervisor', 'receptionist', 'trainer'])
            ->findOrFail($id);

        $newStatus = !$staff->isActive();
        $staff->update(['is_active' => $newStatus]);

        $statusText = $newStatus ? 'diaktifkan kembali' : 'dinonaktifkan';

        StaffLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => $newStatus ? 'Aktivasi Staf Operasional' : 'Nonaktifkan Staf Operasional',
            'description' => "Manager {$user->name} mengubah status akun {$staff->name} ({$staff->role}) menjadi {$statusText}.",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('manager.staff.index')->with('success', "Akun staf {$staff->name} berhasil {$statusText}.");
    }

    /**
     * Setujui Permohonan Reset Password Staf Operasional dan Kirim Tautan Reset ke Email.
     */
    public function approvePasswordReset(Request $request, $id)
    {
        $manager = Auth::user();
        $tenant = $manager->tenant;

        $resetReq = AdminPasswordResetRequest::where('tenant_id', $tenant->id)
            ->where('target_type', 'admin')
            ->findOrFail($id);

        if ($resetReq->status !== 'pending') {
            return redirect()->back()->with('error', 'Permohonan reset password ini sudah pernah diproses sebelumnya.');
        }

        $user = $resetReq->user;
        if (!$user) {
            return redirect()->back()->with('error', 'Data pengguna tidak ditemukan.');
        }

        // Batasi scope approval manager hanya untuk staf operasional
        if (!in_array($user->role, ['supervisor', 'receptionist', 'trainer'])) {
            return redirect()->back()->with('error', 'Manager hanya dapat menyetujui reset password untuk staf operasional.');
        }

        try {
            $status = Password::sendResetLink(['email' => $user->email]);
        } catch (\Throwable $e) {
            report($e);
            return redirect()->back()->with('error', 'Gagal mengirim email tautan reset password. Periksa koneksi SMTP server email.');
        }

        $resetReq->update([
            'status'           => 'approved',
            'approved_by'      => $manager->id,
            'approved_at'      => now(),
            'token_expires_at' => now()->addMinutes(60),
            'notes'            => $request->notes,
        ]);

        StaffLog::create([
            'tenant_id'   => $tenant->id,
            'user_id'     => $manager->id,
            'action'      => 'Approve Reset Password Staf',
            'description' => "Manager {$manager->name} menyetujui permohonan reset password untuk {$user->name} ({$user->role} - {$user->email}). Tautan reset dikirim ke email.",
            'ip_address'  => $request->ip(),
        ]);

        return redirect()->back()->with('success', "Permohonan reset password untuk {$user->name} ({$user->email}) berhasil disetujui! Tautan pembuatan kata sandi baru telah dikirimkan ke email.");
    }

    /**
     * Tolak Permohonan Reset Password Staf Operasional.
     */
    public function rejectPasswordReset(Request $request, $id)
    {
        $manager = Auth::user();
        $tenant = $manager->tenant;

        $resetReq = AdminPasswordResetRequest::where('tenant_id', $tenant->id)
            ->where('target_type', 'admin')
            ->findOrFail($id);

        if ($resetReq->status !== 'pending') {
            return redirect()->back()->with('error', 'Permohonan reset password ini sudah pernah diproses sebelumnya.');
        }

        $resetReq->update([
            'status'      => 'rejected',
            'approved_by' => $manager->id,
            'approved_at' => now(),
            'notes'       => $request->notes ?: 'Ditolak oleh Manager Gym.',
        ]);

        StaffLog::create([
            'tenant_id'   => $tenant->id,
            'user_id'     => $manager->id,
            'action'      => 'Tolak Reset Password Staf',
            'description' => "Manager {$manager->name} menolak permohonan reset password untuk {$resetReq->admin_name} ({$resetReq->email}).",
            'ip_address'  => $request->ip(),
        ]);

        return redirect()->back()->with('success', "Permohonan reset password untuk '{$resetReq->admin_name}' berhasil ditolak.");
    }
}
