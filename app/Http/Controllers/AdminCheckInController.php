<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Member;
use App\Models\CheckIn;
use App\Models\StaffLog;
use Carbon\Carbon;

class AdminCheckInController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        $todayCheckIns = CheckIn::where('tenant_id', $tenant->id)
            ->whereDate('checked_in_at', Carbon::today())
            ->with('member')
            ->latest()
            ->get();

        $allActiveMembers = Member::where('tenant_id', $tenant->id)->where('status', 'active')->orderBy('name')->get();

        return view('admin.checkin.index', compact('todayCheckIns', 'allActiveMembers', 'tenant'));
    }

    public function processCheckIn(Request $request)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        $request->validate([
            'access_code' => 'required|string',
        ]);

        $member = Member::where('tenant_id', $tenant->id)
            ->where('access_code', trim($request->access_code))
            ->first();

        if (!$member) {
            return redirect()->route('admin.checkin.index')->with('error', "Kode akses PIN '{$request->access_code}' tidak ditemukan!");
        }

        if ($member->status === 'inactive' || ($member->expired_at && $member->expired_at->isPast())) {
            return redirect()->route('admin.checkin.index')->with('error', "Member {$member->name} Gagal Check-in! Masa aktif membership telah habis.");
        }

        $checkin = CheckIn::create([
            'tenant_id' => $tenant->id,
            'member_id' => $member->id,
            'access_code' => $member->access_code,
            'checked_in_at' => Carbon::now(),
            'check_in_method' => 'code',
        ]);

        StaffLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'Check-in Member',
            'description' => "Check-in berhasil untuk member: {$member->name} via PIN {$member->access_code}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.checkin.index')->with('success', "CHECK-IN BERHASIL! Selamat datang, {$member->name} (Exp: " . ($member->expired_at ? $member->expired_at->format('d M Y') : 'Aktif') . ")");
    }

    public function manualCheckIn(Request $request)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        $request->validate([
            'member_id' => 'required|exists:members,id',
        ]);

        $member = Member::where('tenant_id', $tenant->id)->findOrFail($request->member_id);

        if ($member->status === 'inactive' || ($member->expired_at && $member->expired_at->isPast())) {
            return redirect()->route('admin.checkin.index')->with('error', "Member {$member->name} Gagal Check-in! Masa aktif membership telah habis.");
        }

        CheckIn::create([
            'tenant_id' => $tenant->id,
            'member_id' => $member->id,
            'access_code' => $member->access_code,
            'checked_in_at' => Carbon::now(),
            'check_in_method' => 'manual',
        ]);

        StaffLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'Check-in Manual',
            'description' => "Check-in manual oleh kasir untuk member: {$member->name}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.checkin.index')->with('success', "CHECK-IN MANUAL BERHASIL! Selamat datang, {$member->name}.");
    }

    public function destroyCheckIn(Request $request, $id)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        $checkin = CheckIn::where('tenant_id', $tenant->id)->findOrFail($id);
        $memberName = $checkin->member ? $checkin->member->name : 'Member';
        $checkin->delete();

        StaffLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'Hapus Log Check-in',
            'description' => "Membatalkan/menghapus log check-in member: {$memberName}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.checkin.index')->with('success', 'Log check-in berhasil dihapus.');
    }
}
