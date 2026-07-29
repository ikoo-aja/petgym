<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Member;
use App\Models\PosTransaction;
use App\Models\StaffLog;
use Carbon\Carbon;

class AdminMemberController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
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

        return view('admin.members.index', compact('members', 'tenant'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'gender' => 'required|in:Laki-laki,Perempuan',
            'address' => 'nullable|string',
            'expired_at' => 'nullable|date',
        ]);

        // Auto Generate Static Access Code (4-6 PIN or alphanumeric)
        do {
            $accessCode = rand(100000, 999999);
        } while (Member::where('tenant_id', $tenant->id)->where('access_code', $accessCode)->exists());

        $member = Member::create([
            'tenant_id' => $tenant->id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'address' => $request->address,
            'access_code' => (string)$accessCode,
            'status' => 'active',
            'expired_at' => $request->expired_at ? Carbon::parse($request->expired_at) : Carbon::today()->addMonth(),
        ]);

        // Audit Trail Staff Log
        StaffLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'Registrasi Member Baru',
            'description' => "Staf {$user->name} memproses pendaftaran member baru: {$member->name} (PIN: {$member->access_code})",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.members.index')->with('success', "Member {$member->name} berhasil didaftarkan dengan Kode Akses PIN: {$member->access_code}");
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
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

        return redirect()->route('admin.members.index')->with('success', "Data member {$member->name} berhasil diperbarui.");
    }

    public function destroy(Request $request, $id)
    {
        $user = Auth::user();
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

        return redirect()->route('admin.members.index')->with('success', "Member {$memberName} berhasil dihapus.");
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
}
