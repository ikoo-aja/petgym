<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Tenant;
use App\Models\Plan;
use App\Models\Invoice;
use App\Models\StaffLog;
use App\Models\SystemLog;

class AdminSubscriptionController extends Controller
{
    /**
     * Tampilkan Halaman Pembayaran & Perpanjangan Sewa Web.
     */
    public function index()
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        if (!$tenant) {
            return redirect()->route('admin.dashboard')->with('error', 'Data gym Anda belum ditemukan.');
        }

        $plans = Plan::where('status', 'active')->get();
        $invoices = Invoice::where('tenant_id', $tenant->id)->latest('id')->paginate(10);
        $plan = $tenant->plan ?: Plan::where('name', 'like', "%{$tenant->plan_name}%")->first();
        $pendingInvoice = Invoice::where('tenant_id', $tenant->id)
            ->whereIn('status', ['pending', 'dp_pending'])
            ->latest('id')
            ->first();

        return view('admin.subscription.index', compact('tenant', 'plans', 'plan', 'invoices', 'pendingInvoice'));
    }

    /**
     * Kirim Pengajuan Pembayaran Perpanjangan Sewa ke Superadmin.
     */
    public function pay(Request $request)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        if (!$tenant) {
            return redirect()->route('admin.dashboard')->with('error', 'Data gym Anda belum ditemukan.');
        }

        // Cegah pengajuan berulang jika masih ada invoice pending
        $hasPending = Invoice::where('tenant_id', $tenant->id)
            ->whereIn('status', ['pending', 'dp_pending'])
            ->exists();

        if ($hasPending) {
            return redirect()->route('admin.subscription.index')->with('error', 'Anda masih memiliki pengajuan perpanjangan yang sedang menunggu verifikasi Superadmin.');
        }

        $request->validate([
            'plan_id'         => ['required', 'exists:plans,id'],
            'duration_months' => ['required', 'integer', 'min:1', 'max:24'],
            'payment_method'  => ['required', 'string', 'max:255'],
            'proof_file'      => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'notes'           => ['nullable', 'string', 'max:500'],
        ], [
            'plan_id.required'         => 'Silakan pilih paket langganan.',
            'duration_months.required' => 'Silakan tentukan durasi perpanjangan.',
            'payment_method.required'  => 'Metode pembayaran wajib dipilih.',
            'proof_file.required'      => 'Bukti transfer / screenshot pembayaran wajib diunggah.',
            'proof_file.image'         => 'File bukti harus berupa gambar (JPG, PNG, atau WEBP).',
            'proof_file.max'           => 'Ukuran file bukti maksimal 4MB.',
        ]);

        $plan = Plan::findOrFail($request->plan_id);
        $durationMonths = (int) $request->duration_months;
        $totalAmount = $plan->price * $durationMonths;

        // Upload bukti pembayaran
        $proofUrl = null;
        if ($request->hasFile('proof_file')) {
            $file = $request->file('proof_file');
            $filename = 'renewal_proof_' . $tenant->id . '_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $targetDir = public_path('uploads/proofs');
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            $file->move($targetDir, $filename);
            $proofUrl = 'uploads/proofs/' . $filename;
        }

        // Generate nomor invoice perpanjangan yang unik
        $invNumber = '#INV-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

        $invoice = Invoice::create([
            'invoice_number'  => $invNumber,
            'tenant_id'       => $tenant->id,
            'amount'          => $totalAmount,
            'plan_name'       => $plan->name,
            'duration_months' => $durationMonths,
            'payment_method'  => $request->payment_method,
            'due_date'        => now()->addDays(3),
            'status'          => 'pending',
            'proof_url'       => $proofUrl,
            'notes'           => $request->notes,
        ]);

        // Audit Trail
        StaffLog::create([
            'tenant_id'   => $tenant->id,
            'user_id'     => $user->id,
            'action'      => 'Pengajuan Perpanjangan Sewa',
            'description' => "Admin {$user->name} mengajukan perpanjangan sewa {$plan->name} ({$durationMonths} bulan) dengan invoice {$invoice->invoice_number}.",
            'ip_address'  => $request->ip(),
        ]);

        SystemLog::create([
            'user_id'     => $user->id,
            'action'      => 'Pengajuan Perpanjangan Sewa Web',
            'description' => "Tenant {$tenant->name} mengajukan perpanjangan sewa {$plan->name} ({$durationMonths} bulan) sebesar Rp " . number_format($totalAmount, 0, ',', '.') . " (#{$invoice->invoice_number}).",
            'ip_address'  => $request->ip(),
        ]);

        return redirect()->route('admin.subscription.index')->with(
            'success',
            "Pengajuan perpanjangan sewa website gym Anda ({$invoice->invoice_number}) berhasil dikirimkan! Superadmin akan segera memeriksa dan memverifikasi perpanjangan masa aktif Anda."
        );
    }
}
