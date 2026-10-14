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
     * Pesan respon netral berorientasi privasi (anti-enumeration & aman).
     */
    private const SAFE_RESPONSE = 'Permohonan atur ulang kata sandi telah diproses. Tautan verifikasi akan dikirimkan ke email terdaftar Anda.';
    private const PENDING_RESPONSE = 'Permohonan atur ulang kata sandi telah kami terima dan sedang diproses. Anda akan menerima email tautan verifikasi segera setelah diverifikasi.';

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
                    'message' => self::PENDING_RESPONSE,
                ]);
            }

            return back()->with('status', self::PENDING_RESPONSE);
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
                        'description' => "Pengguna {$targetUser->name} ({$targetUser->email}) mengajukan permohonan reset kata sandi.",
                        'ip_address'  => $request->ip(),
                    ]);
                }
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'status'  => 'success',
                    'message' => self::PENDING_RESPONSE,
                ]);
            }

            return back()->with('status', self::PENDING_RESPONSE);
        }

        // 4. Default fallback jika email tidak terdaftar atau superadmin
        if ($request->wantsJson()) {
            return response()->json([
                'status'  => 'success',
                'message' => self::SAFE_RESPONSE,
            ]);
        }

        return back()->with('status', self::SAFE_RESPONSE);
    }
}