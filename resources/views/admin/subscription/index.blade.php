@extends('layouts.layout')

@section('title', 'Pembayaran & Melanjutkan Penyewaan Web — PetGym Admin')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Page -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-lg overflow-hidden" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-radius: 12px;">
                <div class="card-body p-4 text-white">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                        <div>
                            <span class="badge badge-danger text-uppercase font-weight-bold px-3 py-2 mb-2" style="border-radius: 8px; font-size: 11px; letter-spacing: 1px;">SaaS Subscription</span>
                            <h3 class="font-weight-extrabold text-white mb-1">Pembayaran & Melanjutkan Penyewaan Web</h3>
                            <p class="text-white-50 mb-0">Kelola status berlangganan platform {{ $tenant->name ?? 'Gym' }} dan ajukan perpanjangan sewa website ke Superadmin.</p>
                        </div>
                        <div class="mt-3 mt-md-0 text-md-right">
                            <span class="d-block text-white-50 small mb-1">Status Langganan Saat Ini:</span>
                            @if(strtolower($tenant->subscription_status ?? '') === 'active' || strtolower($tenant->subscription_status ?? '') === 'aktif')
                                <span class="badge badge-success font-weight-bold px-3 py-2 text-capitalize" style="font-size: 13px; border-radius: 8px;">
                                    <i class="icon-check-circle mr-1"></i> Aktif (Berlangganan)
                                </span>
                            @elseif(strtolower($tenant->subscription_status ?? '') === 'pending')
                                <span class="badge badge-warning font-weight-bold px-3 py-2 text-capitalize text-dark" style="font-size: 13px; border-radius: 8px;">
                                    <i class="icon-clock-o mr-1"></i> Menunggu Verifikasi Superadmin
                                </span>
                            @else
                                <span class="badge badge-secondary font-weight-bold px-3 py-2 text-capitalize" style="font-size: 13px; border-radius: 8px;">
                                    <i class="icon-alert-triangle mr-1"></i> {{ ucfirst($tenant->subscription_status ?? 'Trial / Non-Aktif') }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm p-3 mb-4 rounded-lg d-flex align-items-center" style="border-radius: 10px; background-color: #d1e7dd; color: #0f5132;">
            <span class="icon-check-circle h4 mb-0 mr-3"></span>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm p-3 mb-4 rounded-lg d-flex align-items-center" style="border-radius: 10px;">
            <span class="icon-alert-triangle h4 mb-0 mr-3"></span>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    <div class="row">
        <!-- Status & Informasi Sewa Web -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm rounded-lg h-100" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
                    <h5 class="font-weight-bold text-dark mb-0">Rincian Paket Web</h5>
                </div>
                <div class="card-body p-4">
                    <div class="p-3 mb-4 rounded" style="background-color: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 10px;">
                        <span class="text-muted d-block small font-weight-bold text-uppercase">Nama Paket</span>
                        <h4 class="font-weight-bold text-primary mb-1">{{ $tenant->plan_name ?? ($plan->name ?? 'Paket Standard') }}</h4>
                        <p class="text-secondary small mb-0">
                            Kapasitas: {{ $tenant->plan->max_members ?? '150' }} Member Aktif
                        </p>
                    </div>

                    <div class="mb-3 d-flex justify-content-between align-items-center border-bottom pb-2">
                        <span class="text-muted">Biaya Sewa Website:</span>
                        <span class="font-weight-bold text-dark">Rp {{ number_format($tenant->plan->price ?? 500000, 0, ',', '.') }} / bln</span>
                    </div>

                    <div class="mb-3 d-flex justify-content-between align-items-center border-bottom pb-2">
                        <span class="text-muted">Masa Aktif Hingga:</span>
                        <span class="font-weight-bold text-dark">
                            {{ $tenant->valid_until ? \Carbon\Carbon::parse($tenant->valid_until)->translatedFormat('d F Y') : 'Tidak Terbatas' }}
                        </span>
                    </div>

                    <div class="mb-4 d-flex justify-content-between align-items-center border-bottom pb-2">
                        <span class="text-muted">Rekening Superadmin:</span>
                        <span class="font-weight-bold text-dark">BCA 8830-1234-5678</span>
                    </div>

                    <div class="alert alert-info border-0 p-3 mb-0" style="border-radius: 10px; font-size: 13px;">
                        <i class="icon-info-circle mr-1"></i>
                        <strong>Info Penyewaan:</strong> Untuk menjaga website gym tetap aktif beroperasi, pastikan melunasi tagihan sewa sebelum jatuh tempo.
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Perpanjangan Penyewaan Web ke Superadmin -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm rounded-lg" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom pt-4 px-4 pb-3">
                    <h5 class="font-weight-bold text-dark mb-0">Form Pembayaran & Perpanjang Sewa Web</h5>
                    <small class="text-muted">Kirimkan konfirmasi pembayaran perpanjangan sewa kepada Superadmin PetGym</small>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.subscription.pay') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold text-dark">Pilih Paket Langganan</label>
                                <select name="plan_id" class="form-control form-control-lg" style="border-radius: 8px; font-size: 14px;">
                                    @foreach($plans as $p)
                                        <option value="{{ $p->id }}" {{ ($tenant->plan_id == $p->id) ? 'selected' : '' }}>
                                            {{ $p->name }} — Rp {{ number_format($p->price, 0, ',', '.') }}/bulan
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold text-dark">Durasi Perpanjangan Sewa</label>
                                <select name="duration_months" id="durationSelect" class="form-control form-control-lg" style="border-radius: 8px; font-size: 14px;">
                                    <option value="1">1 Bulan</option>
                                    <option value="3">3 Bulan</option>
                                    <option value="6">6 Bulan</option>
                                    <option value="12">12 Bulan (1 Tahun)</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold text-dark">Metode Pembayaran</label>
                                <select name="payment_method" class="form-control form-control-lg" style="border-radius: 8px; font-size: 14px;">
                                    <option value="Bank Transfer BCA">Transfer Bank BCA (8830-1234-5678 a.n. PetGym Superadmin)</option>
                                    <option value="Bank Transfer Mandiri">Transfer Bank Mandiri (137-00-9876543-2 a.n. PetGym Superadmin)</option>
                                    <option value="QRIS / E-Wallet">QRIS / E-Wallet Instant</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold text-dark">Upload Bukti Transfer / Pembayaran</label>
                                <input type="file" name="proof_file" class="form-control-file p-2 border rounded" accept="image/*" style="border-radius: 8px;">
                                <small class="text-muted d-block mt-1">Format: JPG, PNG. Maksimal 2MB.</small>
                            </div>
                        </div>

                        <div class="p-3 mb-4 rounded" style="background-color: #eff6ff; border: 1px solid #bfdbfe; border-radius: 10px;">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="font-weight-bold text-dark">Total Yang Harus Dibayarkan Kepada Superadmin:</span>
                                <h4 class="font-weight-extrabold text-primary mb-0">Rp {{ number_format($tenant->plan->price ?? 500000, 0, ',', '.') }}</h4>
                            </div>
                        </div>

                        <div class="text-right">
                            <button type="submit" class="btn btn-primary px-4 py-2 font-weight-bold shadow-sm" style="border-radius: 8px;">
                                <i class="icon-send mr-1"></i> Kirim Konfirmasi Pembayaran Ke Superadmin
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Tagihan Penyewaan Web -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-lg" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom pt-4 px-4 pb-3">
                    <h5 class="font-weight-bold text-dark mb-0">Riwayat Tagihan & Pembayaran Penyewaan Web</h5>
                    <small class="text-muted">Daftar transaksi pembayaran sewa website yang telah dikirimkan ke Superadmin</small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3">No. Invoice</th>
                                    <th class="py-3">Jatuh Tempo</th>
                                    <th class="py-3">Jumlah Tagihan</th>
                                    <th class="py-3">Status Pembayaran</th>
                                    <th class="py-3">Bukti Transfer</th>
                                    <th class="px-4 py-3 text-right">Tanggal Kirim</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoices as $inv)
                                    <tr>
                                        <td class="px-4 font-weight-bold text-primary">{{ $inv->invoice_number }}</td>
                                        <td>{{ $inv->due_date ? \Carbon\Carbon::parse($inv->due_date)->format('d/m/Y') : '-' }}</td>
                                        <td class="font-weight-bold">Rp {{ number_format($inv->amount, 0, ',', '.') }}</td>
                                        <td>
                                            @if(strtolower($inv->status) === 'paid')
                                                <span class="badge badge-success px-3 py-1" style="border-radius: 6px;">LUNAS / VERIFIED</span>
                                            @elseif(strtolower($inv->status) === 'pending')
                                                <span class="badge badge-warning text-dark px-3 py-1" style="border-radius: 6px;">MENUNGGU VERIFIKASI SUPERADMIN</span>
                                            @else
                                                <span class="badge badge-danger px-3 py-1" style="border-radius: 6px;">{{ strtoupper($inv->status) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($inv->proof_url)
                                                <a href="{{ $inv->proof_url }}" target="_blank" class="btn btn-sm btn-outline-info font-weight-bold" style="border-radius: 6px;">
                                                    <i class="icon-image mr-1"></i> Lihat Bukti
                                                </a>
                                            @else
                                                <span class="text-muted small">Belum ada</span>
                                            @endif
                                        </td>
                                        <td class="px-4 text-right text-muted small">
                                            {{ $inv->created_at ? $inv->created_at->translatedFormat('d M Y, H:i') : '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            Belum ada riwayat tagihan penyewaan web.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
