<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\StaffLog;

class AdminLogController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        $logs = StaffLog::where('tenant_id', $tenant->id)
            ->with('user')
            ->latest()
            ->paginate(20);

        return view('admin.logs.index', compact('logs', 'tenant'));
    }

    public function clear(Request $request)
    {
        $user = Auth::user();
        if ($user->isOwner()) {
            return redirect()->back()->with('error', 'Mode Pemantauan Owner: Anda hanya memiliki hak akses untuk melihat data.');
        }

        $tenant = $user->tenant;
        StaffLog::where('tenant_id', $tenant->id)->delete();

        return redirect()->route('admin.logs.index')->with('success', 'Seluruh riwayat pesan log audit berhasil dibersihkan dari sistem.');
    }
}
