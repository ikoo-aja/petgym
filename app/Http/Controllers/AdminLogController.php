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
}
