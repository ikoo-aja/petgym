<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Member;
use App\Models\User;
use App\Models\PosTransaction;
use App\Models\PosTransactionItem;
use App\Models\ReceptionistShift;
use App\Models\StaffLog;
use Carbon\Carbon;

class ManagerMemberController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard')->with('error', 'Akses Ditolak. Akun Admin tidak memiliki izin untuk melihat data member. Akses ini dikelola oleh Manager Gym.');
        }
        if ($user->isTrainer()) {
            return redirect()->route('trainer.dashboard')->with('error', 'Akses Ditolak: Pelatih (Trainer) tidak memiliki izin untuk mengakses data member.');
        }

        $tenant = $user->tenant;

        $query = Member::where('tenant_id', $tenant->id);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('access_code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $members = $query->latest()->paginate(10);

        return view('manager.members.index', compact('members', 'tenant'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->isOwner()) {
            return redirect()->back()->with('error', 'Mode Pemantauan Owner: Anda hanya memiliki hak akses untuk melihat data.');
        }
        if ($user->isManager()) {
            return redirect()->back()->with('error', 'Manager bertugas meng-approve (menyetujui) akun member, bukan menambahkan akun dari awal.');
        }
        if ($user->isAdmin()) {
            return redirect()->back()->with('error', 'Admin tidak menambahkan akun member baru secara langsung. Pendaftaran member dilakukan oleh Resepsionis/Kasir melalui POS.');
        }

        $tenant = $user->tenant;

        // Jika staf adalah resepsionis, pastikan shift kasir sedang aktif
        if ($user->isReceptionist()) {
            $openShift = ReceptionistShift::where('tenant_id', $tenant->id)
                ->where('user_id', $user->id)
                ->where('status', 'open')
                ->first();

            if (!$openShift) {
                return redirect()->back()->with('error', 'Pendaftaran member ditolak: Anda belum membuka shift kasir (Open Kasir). Silakan buka shift terlebih dahulu.');
            }
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'gender' => 'required|in:Laki-laki,Perempuan',
            'address' => 'nullable|string',
            'package_duration' => 'required|in:1,3,12',
            'payment_method' => 'required|in:cash,qris,transfer',
            'cash_paid' => 'nullable|numeric|min:0',
        ], [
            'name.required' => 'Nama lengkap calon member wajib diisi.',
            'phone.required' => 'Nomor WhatsApp / HP calon member wajib diisi.',
            'package_duration.required' => 'Pilihan paket keanggotaan wajib dipilih.',
            'payment_method.required' => 'Metode pembayaran kasir wajib dipilih.',
        ]);

        // Detail paket keanggotaan
        $packages = [
            '1' => [
                'name' => 'Paket Membership 1 Bulan',
                'price' => 500000,
                'days' => 30,
                'tier' => 'basic',
            ],
            '3' => [
                'name' => 'Paket Membership 3 Bulan',
                'price' => 1350000,
                'days' => 90,
                'tier' => 'standard',
            ],
            '12' => [
                'name' => 'Paket Membership 1 Tahun',
                'price' => 4500000,
                'days' => 365,
                'tier' => 'premium',
            ],
        ];

        $selectedPackage = $packages[$request->package_duration];
        $packageName = $selectedPackage['name'];
        $packagePrice = $selectedPackage['price'];
        $packageDays = $selectedPackage['days'];
        $tier = $selectedPackage['tier'];

        // Validasi nominal uang tunai jika metode bayar cash
        if ($request->payment_method === 'cash' && $request->filled('cash_paid')) {
            if ($request->cash_paid < $packagePrice) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Uang tunai yang diterima (Rp ' . number_format($request->cash_paid, 0, ',', '.') . ') kurang dari total tagihan paket (Rp ' . number_format($packagePrice, 0, ',', '.') . ').');
            }
        }

        // Generate PIN 6-digit acak unik
        do {
            $accessCode = rand(100000, 999999);
        } while (Member::where('tenant_id', $tenant->id)->where('access_code', $accessCode)->exists());

        // Buat data member
        $member = Member::create([
            'tenant_id' => $tenant->id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'address' => $request->address,
            'access_code' => (string) $accessCode,
            'membership_tier' => $tier,
            'status' => 'active',
            'expired_at' => Carbon::now()->addDays($packageDays),
        ]);

        // Buatkan akun login member jika email tersedia dan belum ada akun User
        if ($request->filled('email')) {
            $existingUser = User::where('email', $request->email)->first();
            if (!$existingUser) {
                $memberUser = User::create([
                    'tenant_id' => $tenant->id,
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make((string) $accessCode),
                    'role' => 'member',
                    'email_verified_at' => Carbon::now(),
                ]);
                $member->update(['user_id' => $memberUser->id]);
            } else {
                $member->update(['user_id' => $existingUser->id]);
            }
        }

        // Buat Invoice Transaksi Kasir POS
        $invNumber = '#POS-' . strtoupper($tenant->subdomain ? substr($tenant->subdomain, 0, 3) : 'GYM') . '-' . date('YmdHis') . '-' . rand(100, 999);

        $transaction = PosTransaction::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'member_id' => $member->id,
            'invoice_number' => $invNumber,
            'total_amount' => $packagePrice,
            'payment_method' => $request->payment_method,
            'type' => 'membership',
            'void_status' => 'none',
        ]);

        PosTransactionItem::create([
            'pos_transaction_id' => $transaction->id,
            'item_name' => $packageName,
            'qty' => 1,
            'price' => $packagePrice,
            'subtotal' => $packagePrice,
        ]);

        // Catat Audit Trail
        StaffLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'Registrasi Member & Pembayaran Kasir',
            'description' => "Kasir {$user->name} mendaftarkan member baru {$member->name} ({$packageName}) lunas Rp " . number_format($packagePrice, 0, ',', '.') . " [{$invNumber}] PIN Akses: {$accessCode}",
            'ip_address' => $request->ip(),
        ]);

        $cashChange = ($request->payment_method === 'cash' && $request->filled('cash_paid')) 
            ? ($request->cash_paid - $packagePrice) 
            : 0;

        return redirect()->route('manager.members.index')->with([
            'success' => "Member {$member->name} berhasil didaftarkan dan pembayaran {$packageName} (Rp " . number_format($packagePrice, 0, ',', '.') . ") Lunas.",
            'new_registered_member' => [
                'name' => $member->name,
                'phone' => $member->phone,
                'email' => $member->email ?? '-',
                'package_name' => $packageName,
                'tier' => strtoupper($tier),
                'invoice_number' => $invNumber,
                'pin' => (string) $accessCode,
                'expired_at' => $member->expired_at->format('d M Y'),
                'total_amount' => $packagePrice,
                'payment_method' => strtoupper($request->payment_method),
                'cash_paid' => $request->cash_paid ?? $packagePrice,
                'cash_change' => $cashChange,
            ],
            'print_transaction_id' => $transaction->id,
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->isOwner()) {
            return redirect()->back()->with('error', 'Mode Pemantauan Owner: Anda hanya memiliki hak akses untuk melihat data.');
        }
        $tenant = $user->tenant;

        $member = Member::where('tenant_id', $tenant->id)->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'gender' => 'required|in:Laki-laki,Perempuan',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive,expiring_soon',
            'expired_at' => 'nullable|date',
        ]);

        $member->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'address' => $request->address,
            'status' => $request->status,
            'expired_at' => $request->expired_at ? Carbon::parse($request->expired_at) : $member->expired_at,
        ]);

        StaffLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'Update Data Member',
            'description' => "Staf {$user->name} memperbarui profil member: {$member->name}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('manager.members.index')->with('success', "Data member {$member->name} berhasil diperbarui.");
    }

    public function destroy(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->isOwner()) {
            return redirect()->back()->with('error', 'Mode Pemantauan Owner: Anda hanya memiliki hak akses untuk melihat data.');
        }
        $tenant = $user->tenant;

        $member = Member::where('tenant_id', $tenant->id)->findOrFail($id);
        $memberName = $member->name;

        $member->delete();

        StaffLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'Hapus Member',
            'description' => "Staf {$user->name} menghapus data member: {$memberName}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('manager.members.index')->with('success', "Member {$memberName} berhasil dihapus.");
    }

    public function history($id)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        $member = Member::where('tenant_id', $tenant->id)->findOrFail($id);
        $transactions = PosTransaction::where('tenant_id', $tenant->id)
            ->where('member_id', $member->id)
            ->with('items')
            ->latest()
            ->get();

        return response()->json([
            'member' => $member,
            'transactions' => $transactions
        ]);
    }

    public function approve($id)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        $member = Member::where('tenant_id', $tenant->id)->findOrFail($id);
        $member->update([
            'status' => 'active',
            'expired_at' => $member->expired_at && $member->expired_at->isFuture() ? $member->expired_at : Carbon::now()->addDays(30),
        ]);

        StaffLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'Approve Member',
            'description' => "Pengelola {$user->name} menyetujui / mengaktifkan akun member: {$member->name}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->back()->with('success', "Akun member {$member->name} berhasil disetujui dan statusnya diubah menjadi AKTIF.");
    }
}
