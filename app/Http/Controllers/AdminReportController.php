<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Member;
use App\Models\CheckIn;
use App\Models\PosTransaction;

class AdminReportController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        return view('admin.reports.index', compact('tenant'));
    }

    public function exportMembers()
    {
        $tenant = Auth::user()->tenant;
        $members = Member::where('tenant_id', $tenant->id)->get();

        $filename = "rekap_member_" . date('Ymd_His') . ".csv";

        $headers = [
            "Content-Type" => "text/csv; charset=utf-8",
            "Content-Disposition" => "attachment; filename=\"$filename\"",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($members) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Nama Member', 'Kode Akses PIN', 'Email', 'No HP', 'Gender', 'Status', 'Tgl Expired', 'Tanggal Daftar']);

            foreach ($members as $m) {
                fputcsv($file, [
                    $m->id,
                    $m->name,
                    $m->access_code,
                    $m->email ?? '-',
                    $m->phone ?? '-',
                    $m->gender,
                    $m->status,
                    $m->expired_at ? $m->expired_at->format('Y-m-d') : '-',
                    $m->created_at ? $m->created_at->format('Y-m-d H:i:s') : '-'
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportCheckIns()
    {
        $tenant = Auth::user()->tenant;
        $checkins = CheckIn::where('tenant_id', $tenant->id)->with('member')->latest()->get();

        $filename = "rekap_absensi_" . date('Ymd_His') . ".csv";

        $headers = [
            "Content-Type" => "text/csv; charset=utf-8",
            "Content-Disposition" => "attachment; filename=\"$filename\"",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($checkins) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Nama Member', 'Kode Akses', 'Metode Check-In', 'Waktu Check-In']);

            foreach ($checkins as $c) {
                fputcsv($file, [
                    $c->id,
                    $c->member ? $c->member->name : 'Unknown',
                    $c->access_code,
                    $c->check_in_method,
                    $c->checked_in_at ? $c->checked_in_at->format('Y-m-d H:i:s') : '-'
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportTransactions()
    {
        $tenant = Auth::user()->tenant;
        $transactions = PosTransaction::where('tenant_id', $tenant->id)->with(['member', 'user'])->latest()->get();

        $filename = "rekap_transaksi_" . date('Ymd_His') . ".csv";

        $headers = [
            "Content-Type" => "text/csv; charset=utf-8",
            "Content-Disposition" => "attachment; filename=\"$filename\"",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID Invoice', 'Nama Member', 'Kasir Staf', 'Tipe Transaksi', 'Metode Bayar', 'Total (Rp)', 'Tanggal Transaksi']);

            foreach ($transactions as $t) {
                fputcsv($file, [
                    $t->invoice_number,
                    $t->member ? $t->member->name : 'Umum / Retail',
                    $t->user ? $t->user->name : 'System',
                    $t->type,
                    $t->payment_method,
                    $t->total_amount,
                    $t->created_at ? $t->created_at->format('Y-m-d H:i:s') : '-'
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
