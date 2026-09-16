<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use App\Models\ReceptionistShift;
use App\Models\User;

class LoginController extends Controller
{
    /**
     * Maksimal percobaan login yang diperbolehkan sebelum dikunci sementara.
     */
    protected int $maxAttempts = 5;

    /**
     * Durasi penguncian (detik) setelah terlalu banyak percobaan gagal.
     */
    protected int $decaySeconds = 60;

    /**
     * Menampilkan halaman/view login.
     */
    public function showLoginForm()
    {
        return view('login');
    }

    /**
     * Kunci throttle unik per email + IP (anti brute-force terdistribusi).
     */
    protected function throttleKey(Request $request): string
    {
        return 'login:' . Str::lower($request->input('email')) . '|' . $request->ip();
    }

    /**
     * Memproses autentikasi pengguna.
     */
    public function login(Request $request)
    {
        // 1. Validasi Input
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // 2. Proteksi Brute-Force: kunci sementara jika terlalu banyak percobaan gagal
        $throttleKey = $this->throttleKey($request);
        if (RateLimiter::tooManyAttempts($throttleKey, $this->maxAttempts)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $menit   = (int) ceil($seconds / 60);

            return back()
                ->withErrors(['email' => "Terlalu banyak percobaan login. Akun Anda dikunci sementara, coba lagi dalam {$menit} menit."])
                ->onlyInput('email');
        }

        // Cek input "Remember Me" dari form
        $remember = $request->has('remember');

        // 3. Percobaan Autentikasi
        if (Auth::attempt($credentials, $remember)) {
            // Regenerasi session untuk mencegah session fixation attack
            $request->session()->regenerate();

            /** @var \App\Models\User $user */
            $user = Auth::user();

            // Proteksi: Akun member TIDAK boleh login dari portal utama PetGym.
            // Member HANYA boleh login melalui subdomain/halaman web gym tempat mereka mendaftar.
            $isTenantSubdomain = $request->route() && $request->route()->hasParameter('slug');
            if ($user->role === 'member' && !$isTenantSubdomain) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $tenant = $user->tenant;
                $gymUrl = $tenant ? $tenant->publicLandingUrl() . '/login' : null;
                $errorMsg = $gymUrl 
                    ? "Akun keanggotaan member tidak dapat login dari portal PetGym utama. Silakan masuk melalui halaman login gym Anda: <a href='{$gymUrl}' class='font-weight-bold text-danger'>{$gymUrl}</a>"
                    : "Akun keanggotaan member tidak dapat login dari portal PetGym utama. Silakan masuk melalui alamat website gym tempat Anda mendaftar.";

                return back()->withErrors(['email' => $errorMsg])->onlyInput('email');
            }

            // 3a. Wajib verifikasi email sebelum bisa masuk ke dashboard
            // (user tetap login tapi diarahkan ke halaman verifikasi dulu)
            if (!$user->hasVerifiedEmail()) {
                RateLimiter::clear($throttleKey);

                return redirect()->route('verification.notice');
            }

            // 3b. Login sukses -> bersihkan hitungan percobaan gagal
            RateLimiter::clear($throttleKey);

            // 3c. Akun staf baru wajib ganti password default sebelum ke dashboard
            if ($user->must_change_password) {
                return redirect()->route('password.change')
                    ->with('warning', 'Silakan ubah password default Anda sebelum melanjutkan.');
            }

            if ($user->isSuperadmin()) {
                return redirect()->intended('/superadmin/dashboard')->with('success', 'Selamat datang Superadmin!');
            }

            if ($user->isOwner()) {
                return redirect()->intended('/owner/dashboard')->with('success', 'Selamat datang Pemilik Gym! Anda dalam mode pemantauan bisnis (Read-Only).');
            }

            if ($user->isAdmin()) {
                return redirect()->intended('/admin/dashboard')->with('success', 'Selamat datang di Dashboard Admin!');
            }

            if ($user->isManager()) {
                return redirect()->intended('/manager/dashboard')->with('success', 'Selamat datang di Dashboard Manager!');
            }

            if ($user->isReceptionist()) {
                return redirect()->intended('/receptionist/dashboard')->with('success', 'Selamat datang di Dashboard Resepsionis!');
            }

            if ($user->role === 'trainer') {
                return redirect()->intended('/trainer/dashboard')->with('success', 'Selamat datang di Dashboard Personal Trainer!');
            }

            if ($user->role === 'member') {
                return redirect()->intended('/member/dashboard')->with('success', 'Selamat datang di Portal Keanggotaan Member Gym!');
            }

            return redirect()->intended('/member/dashboard')->with('success', 'Selamat datang kembali!');
        }

        // 4. Jika Autentikasi Gagal -> catat percobaan gagal untuk throttle
        RateLimiter::hit($throttleKey, $this->decaySeconds);

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    /**
     * Memproses Logout pengguna.
     */
    public function logout(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        // Resepsionis wajib menutup shift kasir yang masih terbuka sebelum logout,
        // supaya setiap transaksi kasir tetap tercatat ke shift (audit kas tidak putus).
        if ($user && $user->isReceptionist() && $user->tenant_id) {
            $openShift = ReceptionistShift::where('tenant_id', $user->tenant_id)
                ->where('user_id', $user->id)
                ->where('status', 'open')
                ->exists();

            if ($openShift) {
                return redirect()->route('receptionist.shifts')
                    ->with('error', 'Shift kasir Anda masih terbuka. Tutup shift terlebih dahulu sebelum logout.');
            }
        }

        Auth::logout();

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect('/login')->with('success', 'Anda telah berhasil keluar.');
    }

    /**
     * Halaman Login Member Gym.
     */
    public function showMemberLoginForm()
    {
        return view('member-login');
    }

    /**
     * Halaman Pendaftaran (Register) Akun Pembeli Web SaaS.
     */
    public function showRegisterForm()
    {
        return view('register-saas');
    }

    /**
     * Halaman Pendaftaran (Register) Member Gym Baru.
     */
    public function showMemberRegisterForm()
    {
        $tenants = \App\Models\Tenant::where('status', 'active')->get();
        return view('member-register', compact('tenants'));
    }

    /**
     * Memproses Pendaftaran Akun Pengelola / Pembeli Web Gym (SaaS Owner).
     */
    public function registerSaas(Request $request)
    {
        $request->validate([
            'gym_name'   => ['required', 'string', 'max:255'],
            'subdomain'  => ['required', 'string', 'max:100', 'unique:tenants,subdomain'],
            'owner_name' => ['required', 'string', 'max:255'],
            'phone'      => ['required', 'string', 'max:20'],
            'email'      => ['required', 'email', 'unique:users,email'],
            'password'   => ['required', 'string', 'min:4', 'confirmed'],
            'plan_name'  => ['required', 'string'],
            'proof_file' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:5120'],
        ], [
            'subdomain.unique'   => 'Subdomain gym ini sudah terpakai. Silakan pilih subdomain lain.',
            'email.unique'       => 'Email ini sudah terdaftar di sistem. Silakan login.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'proof_file.required'=> 'Wajib mengunggah foto / bukti transfer DP 50%.',
        ]);

        // Upload Proof Image File
        $proofUrl = null;
        if ($request->hasFile('proof_file')) {
            $file = $request->file('proof_file');
            $filename = 'dp_proof_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $targetDir = public_path('uploads/proofs');
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            $file->move($targetDir, $filename);
            $proofUrl = asset('uploads/proofs/' . $filename);
        }

        $rawSub = strtolower(trim($request->subdomain));
        $cleanSub = Str::slug(explode('.', $rawSub)[0]);
        $subdomainFormatted = (strpos($rawSub, '.workout.id') === false) ? $rawSub . '.workout.id' : $rawSub;

        // Hitung DP 50% berdasarkan paket
        $fullPrice = match ($request->plan_name) {
            'Paket Enterprise' => 2500000,
            'Paket Basic'      => 500000,
            default            => 1200000, // Paket Pro
        };
        $dpPrice = $fullPrice * 0.5;

        // Buat Tenant Baru (Status: pending s/d verifikasi DP 50% oleh Superadmin)
        $tenant = \App\Models\Tenant::create([
            'name'        => $request->gym_name,
            'subdomain'   => $subdomainFormatted,
            'owner_name'  => $request->owner_name,
            'owner_email' => $request->email,
            'plan_name'   => $request->plan_name,
            'status'      => 'pending',
            'joined_at'   => now(),
            'expires_at'  => now()->addDays(30),
            'features'    => ['members', 'pos', 'classes', 'lockers'],
        ]);

        // Buat Invoice Tagihan DP 50% (Status: dp_pending)
        $invoiceCount = \App\Models\Invoice::count() + 1;
        $invNumber = '#INV-' . date('Y') . '-' . str_pad($invoiceCount, 3, '0', STR_PAD_LEFT);

        \App\Models\Invoice::create([
            'invoice_number' => $invNumber,
            'tenant_id'      => $tenant->id,
            'amount'          => $dpPrice,
            'due_date'        => now()->addDays(3),
            'status'          => 'dp_pending',
            'proof_url'       => $proofUrl,
        ]);

        // Buat Akun Admin (Pengelola Operasional Gym) — Akun Owner nanti dibuat oleh Admin di Dashboard
        $adminUser = User::updateOrCreate(
            ['email' => $request->email],
            [
                'tenant_id' => $tenant->id,
                'name'      => $request->owner_name, // Nama Admin / Pengelola
                'password'  => Hash::make($request->password),
                'role'      => 'admin',
            ]
        );
        if (!$adminUser->hasVerifiedEmail()) {
            $adminUser->markEmailAsVerified();
        }

        return redirect()->route('login')->with('success', "Pendaftaran & Bukti Transfer DP 50% untuk Gym '{$tenant->name}' berhasil terkirim! Akun Admin Anda ({$request->email}) akan diaktifkan setelah tim Superadmin memverifikasi DP 50% Anda (#{$invNumber}).");
    }

    /**
     * Memproses Pendaftaran (Register) Member Gym Baru.
     */
    public function registerMember(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'phone'    => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:4', 'confirmed'],
            'tenant_id'=> ['nullable', 'exists:tenants,id'],
        ], [
            'email.unique'       => 'Email sudah terdaftar di sistem. Silakan gunakan menu Masuk/Login.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $tenantId = $request->tenant_id;
        if (!$tenantId && $request->route() && $request->route()->hasParameter('slug')) {
            $slug = $request->route('slug');
            $tenant = \App\Models\Tenant::where('subdomain', 'like', "{$slug}%")->first();
            $tenantId = $tenant ? $tenant->id : 1;
        }
        $tenantId = $tenantId ?? 1;

        $user = User::create([
            'tenant_id' => $tenantId,
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role'      => 'member',
        ]);

        \App\Models\Member::create([
            'tenant_id'       => $tenantId,
            'user_id'         => $user->id,
            'name'            => $user->name,
            'email'           => $user->email,
            'phone'           => $request->phone,
            'access_code'     => 'MBR-' . strtoupper(substr(md5(uniqid()), 0, 6)),
            'membership_tier' => 'basic',
            'status'          => 'active',
            'expired_at'      => now()->addDays(30),
        ]);

        // Send email verification notification (to Mailpit)
        try {
            $user->sendEmailVerificationNotification();
        } catch (\Throwable $e) {
            report($e);
        }

        Auth::login($user);

        return redirect()->route('verification.notice');
    }
}
