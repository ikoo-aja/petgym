<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Models\User;
use App\Models\AdminPasswordResetRequest;
use App\Models\SystemLog;

class ForgotPasswordController extends Controller
{
    /**
     * Pesan anti user-enumeration & notifikasi permohonan.
     */
    private const SAFE_RESPONSE = 'Jika email tersebut terdaftar di sistem kami, permohonan atur ulang kata sandi telah diproses ke Administrator.';
    private const ADMIN_PENDING_RESPONSE = 'Permohonan atur ulang kata sandi Admin telah dikirimkan ke Superadmin untuk persetujuan (approval). Anda akan menerima email tautan reset segera setelah disetujui.';
    private const USER_PENDING_RESPONSE = 'Permohonan atur ulang kata sandi telah dikirimkan ke Admin Gym Anda untuk persetujuan (approval). Anda akan menerima email tautan reset segera setelah disetujui.';

    /**
     * Menampilkan form "Lupa Password" (input email).
     */
    public function showLinkRequestForm()
    {
        return view('forgot-password');
    }

    /**
     * Mengirim link reset password ke email pengguna.
     */
    public function sendResetLinkEmail(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
        ]);

        $email = trim(strtolower($request->email));
        $targetUser = User::where('email', $email)->first();

        // 2. KHUSUS ROLE ADMIN: Memerlukan approval dari Superadmin
        if ($targetUser && $targetUser->isAdmin()) {
            $existingRequest = AdminPasswordResetRequest::where('user_id', $targetUser->id)
                ->where('status', 'pending')
                ->where('created_at', '>=', now()->subHours(24))
                ->first();

            if (!$existingRequest) {
                AdminPasswordResetRequest::create([
                    'user_id'     => $targetUser->id,
                    'tenant_id'   => $targetUser->tenant_id,
                    'target_type' => 'superadmin',
                    'email'       => $targetUser->email,
                    'admin_name'  => $targetUser->name,
                    'user_role'   => $targetUser->role,
                    'status'      => 'pending',
                    'ip_address'  => $request->ip(),
                ]);

                SystemLog::create([
                    'user_id'     => $targetUser->id,
                    'action'      => 'Pengajuan Reset Password Admin',
                    'description' => "Admin {$targetUser->name} ({$targetUser->email}) mengajukan permohonan reset password (menunggu approval Superadmin).",
                    'ip_address'  => $request->ip(),
                ]);
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'status'  => 'success',
                    'role'    => 'admin',
                    'message' => 'Permintaan reset kata sandi akun Admin telah dikirim ke Superadmin. Silakan tunggu konfirmasi persetujuan dari Superadmin.',
                ]);
            }

            return back()->with('status', self::ADMIN_PENDING_RESPONSE);
        }

        // 3. ROLE OWNER, MANAGER, SUPERVISOR, TRAINER, RECEPTIONIST, MEMBER:
        //    Memerlukan approval dari Admin gym (tenant) mereka!
        if ($targetUser && in_array($targetUser->role, ['owner', 'manager', 'supervisor', 'trainer', 'receptionist', 'member'])) {
            $existingRequest = AdminPasswordResetRequest::where('user_id', $targetUser->id)
                ->where('status', 'pending')
                ->where('created_at', '>=', now()->subHours(24))
                ->first();

            if (!$existingRequest) {
                AdminPasswordResetRequest::create([
                    'user_id'     => $targetUser->id,
                    'tenant_id'   => $targetUser->tenant_id,
                    'target_type' => 'admin',
                    'email'       => $targetUser->email,
                    'admin_name'  => $targetUser->name,
                    'user_role'   => $targetUser->role,
                    'status'      => 'pending',
                    'ip_address'  => $request->ip(),
                ]);

                if ($targetUser->tenant_id) {
                    \App\Models\StaffLog::create([
                        'tenant_id'   => $targetUser->tenant_id,
                        'user_id'     => $targetUser->id,
                        'action'      => 'Pengajuan Reset Password',
                        'description' => "Pengguna {$targetUser->name} ({$targetUser->role} - {$targetUser->email}) mengajukan permohonan reset password (menunggu approval Admin).",
                        'ip_address'  => $request->ip(),
                    ]);
                }
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'status'  => 'success',
                    'role'    => $targetUser->role,
                    'message' => 'Permintaan reset kata sandi Anda telah dikirim ke Admin Gym untuk disetujui. Tautan pembuatan kata sandi baru akan dikirimkan ke email Anda setelah disetujui.',
                ]);
            }

            return back()->with('status', self::USER_PENDING_RESPONSE);
        }

        // 4. Default fallback jika email tidak terdaftar atau superadmin
        if ($request->wantsJson()) {
            return response()->json([
                'status'  => 'success',
                'role'    => 'user',
                'message' => self::SAFE_RESPONSE,
            ]);
        }

        return back()->with('status', self::SAFE_RESPONSE);
    }
}