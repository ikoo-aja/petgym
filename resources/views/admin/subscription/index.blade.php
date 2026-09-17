@extends('layouts.layout')

@section('title', 'Kelola Langganan — PetGym Admin')
@section('page_title', 'Kelola Langganan')
@section('page_subtitle', 'Pantau status masa aktif website dan riwayat transaksi langganan')

@section('content')
<div class="container-fluid py-4">

    <!-- 1. KARTU RINGKASAN ATAS (TOP SUMMARY BAR) -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border shadow-sm rounded-lg overflow-hidden bg-white" style="border-radius: 14px;">
                <div class="card-body p-4 text-dark">
                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center" style="gap: 20px;">
                        
                        <!-- Info Status & Masa Aktif -->
                        <div>
                            <div class="d-flex flex-wrap align-items-center mb-2" style="gap: 8px;">
                                <span class="badge badge-light border text-muted font-weight-bold px-3 py-1" style="border-radius: 8px; font-size: 11px;">SaaS Subscription</span>
                                <span class="badge badge-primary font-weight-bold px-3 py-1" style="border-radius: 8px; font-size: 11px;">{{ $plan->name ?? ($tenant->plan_name ?? 'Paket Pro') }}</span>
                                @if($tenant->status === 'active' && $tenant->expires_in_days > 0)
                                    <span class="badge badge-success font-weight-bold px-3 py-1" style="border-radius: 8px; font-size: 11px;">
                                        <i class="icon-check mr-1"></i> Aktif ({{ $tenant->expires_in_days }} Hari Lagi)
                                    </span>
                                @else
                                    <span class="badge badge-danger font-weight-bold px-3 py-1" style="border-radius: 8px; font-size: 11px;">
                                        <i class="icon-close mr-1"></i> Suspended / Habis Masa Aktif
                                    </span>
                                @endif
                            </div>

                            <h3 class="font-weight-bold text-dark mb-1">
                                {{ $tenant->name ?? 'Gym Portal' }}
                                <small class="text-muted font-weight-normal" style="font-size: 14px;">({{ $tenant->subdomain }})</small>
                            </h3>

                            <div class="d-flex flex-wrap align-items-center text-muted small mt-2" style="gap: 20px;">
                                <div>
                                    <i class="icon-calendar mr-1 text-primary"></i> Masa Aktif Hingga: 
                                    <strong class="text-dark">{{ $tenant->expires_at ? $tenant->expires_at->format('d M Y') : 'Tidak Terbatas' }}</strong>
                                </div>
                                <div>
                                    <i class="icon-credit-card mr-1 text-primary"></i> Biaya Paket: 
                                    <strong class="text-dark">Rp {{ number_format($plan->price ?? 1200000, 0, ',', '.') }} / bulan</strong>
                                </div>
                                <div>
                                    <i class="icon-clock-o mr-1 text-primary"></i> Sisa Masa Aktif: 
                                    <strong class="{{ $tenant->expires_in_days <= 7 ? 'text-danger' : 'text-success' }}">{{ $tenant->expires_in_days }} Hari</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol CTA Utama / Proteksi Status Pending -->
                        <div class="text-lg-right">
                            @if($pendingInvoice)
                                <div>
                                    <button class="btn btn-secondary font-weight-bold px-4 py-3 shadow-sm" disabled style="border-radius: 10px; font-size: 14px; opacity: 0.9; cursor: not-allowed;">
                                        <i class="icon-clock-o mr-2"></i> Menunggu Verifikasi Superadmin
                                    </button>
                                    <small class="d-block text-muted mt-2 font-weight-bold" style="font-size: 12px;">
                                        <i class="icon-info mr-1 text-primary"></i> Pengajuan perpanjangan (<strong>{{ $pendingInvoice->invoice_number }}</strong>) sedang diproses Superadmin.
                                    </small>
                                </div>
                            @else
                                <div>
                                    <button type="button" class="btn btn-primary font-weight-bold px-4 py-3 shadow-sm" data-toggle="modal" data-target="#renewalModal" style="border-radius: 10px; font-size: 14px;">
                                        <i class="icon-plus mr-1"></i> Perpanjang Sewa
                                    </button>
                                </div>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Flash Notifications -->
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm p-3 mb-4 rounded-lg d-flex align-items-center" style="border-radius: 12px; background-color: #d1e7dd; color: #0f5132;">
            <span class="icon-check-circle h4 mb-0 mr-3"></span>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm p-3 mb-4 rounded-lg d-flex align-items-center" style="border-radius: 12px;">
            <span class="icon-alert-triangle h4 mb-0 mr-3"></span>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm p-3 mb-4 rounded-lg" style="border-radius: 12px;">
            <ul class="mb-0 pl-3">
                @foreach($errors->all() as $err)
                    <li class="font-weight-bold small">{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- 2. TABEL RIWAYAT TRANSAKSI / INVOICE LENGKAP -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-lg" style="border-radius: 14px;">
                <div class="card-header bg-white border-bottom pt-4 px-4 pb-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                    <div>
                        <h5 class="font-weight-bold text-dark mb-0">Riwayat Tagihan</h5>
                        <small class="text-muted">Daftar transaksi perpanjangan sewa website gym yang telah diajukan ke Superadmin</small>
                    </div>
                    @if(!$pendingInvoice)
                        <button type="button" class="btn btn-sm btn-outline-primary font-weight-bold px-3 mt-2 mt-md-0" data-toggle="modal" data-target="#renewalModal" style="border-radius: 8px;">
                            <i class="icon-plus mr-1"></i> Perpanjang Sewa
                        </button>
                    @endif
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3 text-dark font-weight-bold" style="width: 170px;">No. Invoice</th>
                                    <th class="py-3 text-dark font-weight-bold">Paket & Durasi</th>
                                    <th class="py-3 text-dark font-weight-bold">Jumlah Tagihan</th>
                                    <th class="py-3 text-dark font-weight-bold">Metode Pembayaran</th>
                                    <th class="py-3 text-dark font-weight-bold">Status Verifikasi</th>
                                    <th class="py-3 text-dark font-weight-bold">Bukti Transfer</th>
                                    <th class="px-4 py-3 text-right text-dark font-weight-bold">Tanggal Pengajuan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoices as $inv)
                                    <tr>
                                        <td class="px-4 font-weight-bold text-primary">{{ $inv->invoice_number }}</td>
                                        <td>
                                            <strong class="text-dark">{{ $inv->plan_name ?? ($tenant->plan_name ?? 'Paket Sewa') }}</strong>
                                            <span class="badge badge-light border text-muted ml-1">+{{ $inv->duration_months ?? 1 }} Bulan</span>
                                        </td>
                                        <td class="font-weight-bold text-dark">Rp {{ number_format($inv->amount, 0, ',', '.') }}</td>
                                        <td>
                                            <small class="text-muted"><i class="icon-credit-card mr-1"></i> {{ $inv->payment_method ?? 'Transfer Bank' }}</small>
                                        </td>
                                        <td>
                                            @if(strtolower($inv->status) === 'paid')
                                                <span class="badge badge-success px-3 py-1 font-weight-bold" style="border-radius: 6px;">
                                                    <i class="icon-check mr-1"></i> DISETUJUI / LUNAS
                                                </span>
                                            @elseif(strtolower($inv->status) === 'pending' || strtolower($inv->status) === 'dp_pending')
                                                <span class="badge badge-warning text-dark px-3 py-1 font-weight-bold" style="border-radius: 6px;">
                                                    <i class="icon-clock-o mr-1"></i> MENUNGGU VERIFIKASI
                                                </span>
                                            @elseif(strtolower($inv->status) === 'rejected')
                                                <span class="badge badge-danger px-3 py-1 font-weight-bold" style="border-radius: 6px;">
                                                    <i class="icon-close mr-1"></i> DITOLAK
                                                </span>
                                            @else
                                                <span class="badge badge-secondary px-3 py-1 font-weight-bold" style="border-radius: 6px;">{{ strtoupper($inv->status) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($inv->proof_url)
                                                <a href="{{ asset($inv->proof_url) }}" target="_blank" class="btn btn-sm btn-outline-info font-weight-bold py-1 px-2" style="border-radius: 6px; font-size: 12px;">
                                                    <i class="icon-image mr-1"></i> Lihat Bukti
                                                </a>
                                            @else
                                                <span class="text-muted small">-</span>
                                            @endif
                                        </td>
                                        <td class="px-4 text-right text-muted small">
                                            {{ $inv->created_at ? $inv->created_at->format('d M Y, H:i') : '-' }} WIB
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">
                                            <span class="icon-inbox h3 d-block mb-2 text-muted"></span>
                                            Belum ada riwayat pengajuan perpanjangan sewa website.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($invoices->hasPages())
                        <div class="p-3 border-top d-flex justify-content-between align-items-center">
                            <small class="text-muted">Menampilkan {{ $invoices->count() }} data transaksi</small>
                            {{ $invoices->links('pagination::bootstrap-4') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL POP-UP: PERPANJANG SEWA WEBSITE (3 LANGKAH RINGKAS DALAM 1 TAMPILAN) -->
<!-- ========================================================================= -->
@if(!$pendingInvoice)
<div class="modal fade text-left" id="renewalModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <form action="{{ route('admin.subscription.pay') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;" id="renewalModalForm">
            @csrf

            <!-- Modal Header -->
            <div class="modal-header bg-white border-bottom py-3 px-4 text-dark">
                <div>
                    <h5 class="modal-title font-weight-bold text-dark mb-0">Perpanjang Sewa</h5>
                    <small class="text-muted">Pilih durasi dan kirim bukti pembayaran perpanjangan sewa</small>
                </div>
                <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body p-4" style="max-height: 75vh; overflow-y: auto;">
                
                <!-- LANGKAH 1: PILIH PAKET & DURASI -->
                <div class="mb-4">
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge badge-primary rounded-circle mr-2 d-inline-flex justify-content-center align-items-center" style="width: 24px; height: 24px; font-size: 12px;">1</span>
                        <h6 class="font-weight-bold text-dark mb-0">Pilih Paket & Durasi Perpanjangan</h6>
                    </div>

                    <!-- Kunci Paket Aktif + Toggle Ganti Paket -->
                    <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded border mb-3" style="border-radius: 10px;">
                        <div>
                            <span class="text-muted small d-block">Paket Saat Ini:</span>
                            <span class="badge badge-primary font-weight-bold" id="badgePlanName" style="font-size: 13px;">{{ $plan->name ?? ($tenant->plan_name ?? 'Paket Pro') }}</span>
                            <span class="text-muted small ml-1" id="badgePlanPrice">Rp {{ number_format($plan->price ?? 1200000, 0, ',', '.') }} / bulan</span>
                        </div>
                        <button type="button" class="btn btn-sm btn-link text-primary font-weight-bold p-0 text-decoration-none" id="togglePlanBtn">
                            <i class="icon-swap_horiz mr-1"></i> <span id="togglePlanBtnText">Ingin ganti paket?</span>
                        </button>
                    </div>

                    <!-- Hidden Inputs Paket -->
                    <input type="hidden" name="plan_id" id="selectedPlanId" value="{{ $plan->id ?? 2 }}">
                    <input type="hidden" id="selectedPlanPriceVal" value="{{ $plan->price ?? 1200000 }}">
                    <input type="hidden" id="selectedPlanNameVal" value="{{ $plan->name ?? ($tenant->plan_name ?? 'Paket Pro') }}">

                    <!-- Dropdown Pilihan Ganti Paket (Tersembunyi secara Default) -->
                    <div id="changePlanSection" class="mb-3 p-3 rounded border bg-white shadow-sm d-none" style="border-radius: 10px;">
                        <small class="text-muted font-weight-bold d-block mb-2">Pilih paket baru yang ingin Anda gunakan:</small>
                        <div class="row">
                            @foreach($plans as $p)
                                <div class="col-md-4 mb-2">
                                    <div class="plan-chip-option p-2 border rounded text-center cursor-pointer {{ ($plan->id == $p->id) ? 'border-primary bg-light-primary active-plan' : '' }}" 
                                         data-id="{{ $p->id }}" 
                                         data-price="{{ $p->price }}" 
                                         data-name="{{ $p->name }}"
                                         style="cursor: pointer; border-radius: 8px; transition: all 0.2s;">
                                        <strong class="d-block text-dark small">{{ $p->name }}</strong>
                                        <span class="text-primary font-weight-bold small">Rp {{ number_format($p->price, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Pilihan Durasi Chips / Pills -->
                    <label class="font-weight-bold text-dark small d-block mb-2">Tentukan Durasi Perpanjangan:</label>
                    <div class="row" style="row-gap: 8px;">
                        <div class="col-6 col-md-3">
                            <div class="duration-pill p-2 text-center border rounded cursor-pointer active-pill" data-months="1" style="cursor: pointer; border-radius: 10px; transition: all 0.2s;">
                                <div class="font-weight-bold text-dark" style="font-size: 13px;">1 Bulan</div>
                                <small class="text-muted">+30 Hari</small>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="duration-pill p-2 text-center border rounded cursor-pointer" data-months="3" style="cursor: pointer; border-radius: 10px; transition: all 0.2s;">
                                <div class="font-weight-bold text-dark" style="font-size: 13px;">3 Bulan</div>
                                <small class="text-muted">+90 Hari</small>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="duration-pill p-2 text-center border rounded cursor-pointer" data-months="6" style="cursor: pointer; border-radius: 10px; transition: all 0.2s;">
                                <div class="font-weight-bold text-dark" style="font-size: 13px;">6 Bulan</div>
                                <small class="text-muted">+180 Hari</small>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="duration-pill p-2 text-center border rounded cursor-pointer position-relative" data-months="12" style="cursor: pointer; border-radius: 10px; transition: all 0.2s;">
                                <span class="badge badge-warning position-absolute text-dark font-weight-bold" style="top: -8px; right: 6px; font-size: 9px;">Hemat</span>
                                <div class="font-weight-bold text-dark" style="font-size: 13px;">12 Bulan</div>
                                <small class="text-muted">1 Thn (+360 Hari)</small>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="duration_months" id="selectedDurationVal" value="1">
                </div>

                <hr class="my-3">

                <!-- LANGKAH 2: INSTRUKSI TRANSFER & SALIN DATA REKENING -->
                <div class="mb-4">
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge badge-primary rounded-circle mr-2 d-inline-flex justify-content-center align-items-center" style="width: 24px; height: 24px; font-size: 12px;">2</span>
                        <h6 class="font-weight-bold text-dark mb-0">Instruksi Transfer Pembayaran</h6>
                    </div>

                    <div class="p-3 rounded border" style="background-color: #f8fafc; border-radius: 12px;">
                        <!-- Total Nominal -->
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                            <div>
                                <span class="font-weight-bold text-dark d-block small">Total Tagihan Transfer:</span>
                                <small class="text-muted" id="calcBreakdownText">{{ $plan->name ?? 'Paket Pro' }} (1 Bulan x Rp {{ number_format($plan->price ?? 1200000, 0, ',', '.') }})</small>
                            </div>
                            <div class="text-right">
                                <h4 class="font-weight-extrabold text-primary mb-0" id="calcTotalText">Rp {{ number_format($plan->price ?? 1200000, 0, ',', '.') }}</h4>
                                <button type="button" class="btn btn-sm btn-link text-secondary p-0 mt-1 btn-copy" data-copy="{{ $plan->price ?? 1200000 }}" title="Salin Nominal Angka" style="font-size: 11px; text-decoration: none;">
                                    <i class="icon-copy mr-1"></i> <span class="copy-label">Salin Nominal</span>
                                </button>
                            </div>
                        </div>

                        <!-- Pilihan Bank Tujuan -->
                        <div>
                            <label class="font-weight-bold text-dark small mb-2 d-block">Pilih Rekening Tujuan Transfer Superadmin:</label>
                            
                            <div class="d-flex flex-wrap mb-3" style="gap: 8px;">
                                <button type="button" class="btn btn-sm btn-payment-tab active-pay-tab font-weight-bold px-3 py-1" data-method="Bank Transfer BCA" data-bank="BCA" data-rekening="883012345678" data-rekening-fmt="8830-1234-5678" style="border-radius: 8px; font-size: 12px;">
                                    Bank BCA
                                </button>
                                <button type="button" class="btn btn-sm btn-payment-tab font-weight-bold px-3 py-1" data-method="Bank Transfer Mandiri" data-bank="Mandiri" data-rekening="1370098765432" data-rekening-fmt="137-00-9876543-2" style="border-radius: 8px; font-size: 12px;">
                                    Bank Mandiri
                                </button>
                                <button type="button" class="btn btn-sm btn-payment-tab font-weight-bold px-3 py-1" data-method="QRIS / E-Wallet" data-bank="QRIS" data-rekening="QRIS-PETGYM" data-rekening-fmt="QRIS PetGym Instant" style="border-radius: 8px; font-size: 12px;">
                                    QRIS Instant
                                </button>
                            </div>
                            <input type="hidden" name="payment_method" id="selectedPaymentMethodVal" value="Bank Transfer BCA">

                            <!-- Kotak Salin Rekening -->
                            <div class="bg-white p-3 rounded border d-flex justify-content-between align-items-center" style="border-radius: 10px;">
                                <div>
                                    <span class="text-muted small d-block" id="bankNameDisplay">Bank BCA (Transfer Antar Bank / M-Banking)</span>
                                    <span class="font-weight-bold text-dark h5 mb-0" id="bankNumberDisplay" style="letter-spacing: 0.5px; font-family: monospace;">8830-1234-5678</span>
                                    <small class="text-muted d-block mt-1">a.n. <strong>PT PetGym Digital Indonesia (Superadmin)</strong></small>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-sm btn-outline-primary font-weight-bold px-3 py-2 btn-copy" id="btnCopyRekening" data-copy="883012345678" style="border-radius: 8px; font-size: 12px;">
                                        <i class="icon-copy mr-1"></i> <span class="copy-label">Salin No. Rekening</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-3">

                <!-- LANGKAH 3: UPLOAD BUKTI TRANSFER & CATATAN -->
                <div class="mb-2">
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge badge-primary rounded-circle mr-2 d-inline-flex justify-content-center align-items-center" style="width: 24px; height: 24px; font-size: 12px;">3</span>
                        <h6 class="font-weight-bold text-dark mb-0">Upload Bukti Pembayaran</h6>
                    </div>

                    <div id="dropzoneArea" class="dropzone-box text-center p-4 border rounded cursor-pointer mb-3" style="border: 2px dashed #94a3b8; background-color: #f8fafc; border-radius: 12px; cursor: pointer; transition: all 0.2s;">
                        <!-- State 1: Prompt Kosong -->
                        <div id="dropzonePrompt">
                            <span class="icon-upload text-primary mb-2 d-block" style="font-size: 28px;"></span>
                            <div class="font-weight-bold text-dark" style="font-size: 13px;">
                                Tarik & lepas foto struk transfer di sini, atau <span class="text-primary text-decoration-underline">klik untuk memilih file</span>
                            </div>
                            <small class="text-muted d-block mt-1">Format: JPG, PNG, WEBP (Maksimal 4MB)</small>
                        </div>

                        <!-- State 2: Preview Gambar -->
                        <div id="dropzonePreview" class="d-none">
                            <div class="position-relative d-inline-block mb-2">
                                <img id="previewImage" src="" alt="Pratinjau Bukti Transfer" class="img-fluid rounded border shadow-sm" style="max-height: 180px; object-fit: contain; background: #fff;">
                                <button type="button" class="btn btn-sm btn-danger position-absolute shadow-sm" id="removeImageBtn" style="top: -10px; right: -10px; border-radius: 50%; width: 24px; height: 24px; padding: 0; line-height: 20px;" title="Hapus Gambar">&times;</button>
                            </div>
                            <div class="small font-weight-bold text-dark" id="fileNameDisplay">bukti_transfer.jpg</div>
                            <small class="text-muted d-block" id="fileSizeDisplay">1.2 MB</small>
                            <button type="button" class="btn btn-sm btn-outline-secondary mt-2 font-weight-bold px-3" id="changeImageBtn" style="font-size: 11px; border-radius: 6px;">
                                <i class="icon-refresh mr-1"></i> Ganti File
                            </button>
                        </div>

                        <input type="file" name="proof_file" id="proofFileInput" class="d-none" accept="image/jpeg,image/png,image/webp" required>
                    </div>

                    <div class="form-group mb-0">
                        <label class="font-weight-bold text-dark small">Catatan Tambahan (Opsional)</label>
                        <input type="text" name="notes" class="form-control form-control-sm" placeholder="Contoh: Transfer dari Rekening BCA a.n. Budi Pratama..." style="border-radius: 8px;">
                    </div>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="modal-footer bg-light py-3 px-4 d-flex justify-content-between">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 8px;">Batal</button>
                <button type="submit" class="btn btn-primary font-weight-bold px-4 shadow-sm" style="border-radius: 8px;">
                    <i class="icon-send mr-1"></i> Kirim Konfirmasi Pembayaran
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<style>
    .cursor-pointer { cursor: pointer; }
    .bg-light-primary { background-color: #f0fdf4 !important; border-color: #22c55e !important; }
    
    /* Duration Pills */
    .duration-pill {
        border-color: #e2e8f0;
        background-color: #ffffff;
    }
    .duration-pill:hover {
        border-color: #3b82f6;
        background-color: #f8fafc;
    }
    .duration-pill.active-pill {
        background-color: #2563eb !important;
        border-color: #2563eb !important;
        color: #ffffff !important;
    }
    .duration-pill.active-pill .text-dark {
        color: #ffffff !important;
    }
    .duration-pill.active-pill .text-muted {
        color: #e0e7ff !important;
    }

    /* Payment Method Tabs */
    .btn-payment-tab {
        background-color: #f1f5f9;
        color: #475569;
        border: 1px solid #cbd5e1;
    }
    .btn-payment-tab.active-pay-tab {
        background-color: #2563eb !important;
        color: #ffffff !important;
        border-color: #2563eb !important;
    }

    /* Dropzone Hover */
    .dropzone-box:hover {
        border-color: #2563eb !important;
        background-color: #f1f5f9 !important;
    }
    .dropzone-box.dragover {
        border-color: #2563eb !important;
        background-color: #eff6ff !important;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Toggle Ganti Paket
    var togglePlanBtn = document.getElementById('togglePlanBtn');
    var changePlanSection = document.getElementById('changePlanSection');
    var togglePlanBtnText = document.getElementById('togglePlanBtnText');
    var selectedPlanId = document.getElementById('selectedPlanId');
    var selectedPlanPriceVal = document.getElementById('selectedPlanPriceVal');
    var selectedPlanNameVal = document.getElementById('selectedPlanNameVal');
    var badgePlanName = document.getElementById('badgePlanName');
    var badgePlanPrice = document.getElementById('badgePlanPrice');

    if (togglePlanBtn && changePlanSection) {
        togglePlanBtn.addEventListener('click', function() {
            var isHidden = changePlanSection.classList.contains('d-none');
            if (isHidden) {
                changePlanSection.classList.remove('d-none');
                togglePlanBtnText.textContent = 'Tutup pilihan paket';
            } else {
                changePlanSection.classList.add('d-none');
                togglePlanBtnText.textContent = 'Ingin ganti paket?';
            }
        });
    }

    // Pilihan Chip Paket Baru
    document.querySelectorAll('.plan-chip-option').forEach(function(chip) {
        chip.addEventListener('click', function() {
            document.querySelectorAll('.plan-chip-option').forEach(function(c) {
                c.classList.remove('border-primary', 'bg-light-primary', 'active-plan');
            });
            this.classList.add('border-primary', 'bg-light-primary', 'active-plan');

            var planId = this.getAttribute('data-id');
            var planPrice = parseFloat(this.getAttribute('data-price')) || 1200000;
            var planName = this.getAttribute('data-name');

            if (selectedPlanId) selectedPlanId.value = planId;
            if (selectedPlanPriceVal) selectedPlanPriceVal.value = planPrice;
            if (selectedPlanNameVal) selectedPlanNameVal.value = planName;

            if (badgePlanName) badgePlanName.textContent = planName;
            if (badgePlanPrice) badgePlanPrice.textContent = 'Rp ' + planPrice.toLocaleString('id-ID') + ' / bulan';

            updateTotalCalculation();
        });
    });

    // 2. Duration Pills
    var selectedDurationVal = document.getElementById('selectedDurationVal');
    document.querySelectorAll('.duration-pill').forEach(function(pill) {
        pill.addEventListener('click', function() {
            document.querySelectorAll('.duration-pill').forEach(function(p) {
                p.classList.remove('active-pill');
            });
            this.classList.add('active-pill');

            var months = this.getAttribute('data-months');
            if (selectedDurationVal) selectedDurationVal.value = months;
            updateTotalCalculation();
        });
    });

    // Kalkulasi Total Pembayaran
    function updateTotalCalculation() {
        var price = parseFloat(selectedPlanPriceVal ? selectedPlanPriceVal.value : 1200000) || 1200000;
        var duration = parseInt(selectedDurationVal ? selectedDurationVal.value : 1) || 1;
        var planName = selectedPlanNameVal ? selectedPlanNameVal.value : 'Paket Pro';
        var total = price * duration;

        var calcTotalText = document.getElementById('calcTotalText');
        var calcBreakdownText = document.getElementById('calcBreakdownText');

        if (calcTotalText) calcTotalText.textContent = 'Rp ' + total.toLocaleString('id-ID');
        if (calcBreakdownText) calcBreakdownText.textContent = planName + ' (' + duration + ' Bulan x Rp ' + price.toLocaleString('id-ID') + ')';

        // Update tombol copy nominal
        var btnCopyNominal = document.querySelector('.btn-copy[data-copy]:not(#btnCopyRekening)');
        if (btnCopyNominal) {
            btnCopyNominal.setAttribute('data-copy', total);
        }
    }

    // 3. Payment Method Tabs
    var selectedPaymentMethodVal = document.getElementById('selectedPaymentMethodVal');
    var bankNameDisplay = document.getElementById('bankNameDisplay');
    var bankNumberDisplay = document.getElementById('bankNumberDisplay');
    var btnCopyRekening = document.getElementById('btnCopyRekening');

    document.querySelectorAll('.btn-payment-tab').forEach(function(tab) {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.btn-payment-tab').forEach(function(t) {
                t.classList.remove('active-pay-tab');
            });
            this.classList.add('active-pay-tab');

            var method = this.getAttribute('data-method');
            var bank = this.getAttribute('data-bank');
            var rekening = this.getAttribute('data-rekening');
            var rekeningFmt = this.getAttribute('data-rekening-fmt');

            if (selectedPaymentMethodVal) selectedPaymentMethodVal.value = method;

            if (bankNameDisplay) bankNameDisplay.textContent = 'Bank ' + bank + ' (Transfer Superadmin)';
            if (bankNumberDisplay) bankNumberDisplay.textContent = rekeningFmt;
            if (btnCopyRekening) btnCopyRekening.setAttribute('data-copy', rekening);
        });
    });

    // 4. Tombol Salin ke Clipboard
    document.querySelectorAll('.btn-copy').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var textToCopy = this.getAttribute('data-copy');
            var labelSpan = this.querySelector('.copy-label');
            var originalText = labelSpan ? labelSpan.textContent : 'Salin';

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(textToCopy);
            } else {
                var tempInput = document.createElement('textarea');
                tempInput.value = textToCopy;
                document.body.appendChild(tempInput);
                tempInput.select();
                document.execCommand('copy');
                document.body.removeChild(tempInput);
            }

            if (labelSpan) {
                labelSpan.textContent = 'Tersalin!';
                setTimeout(function() {
                    labelSpan.textContent = originalText;
                }, 2000);
            }
        });
    });

    // 5. Drag-and-Drop + Live Image Preview
    var dropzoneArea = document.getElementById('dropzoneArea');
    var proofFileInput = document.getElementById('proofFileInput');
    var dropzonePrompt = document.getElementById('dropzonePrompt');
    var dropzonePreview = document.getElementById('dropzonePreview');
    var previewImage = document.getElementById('previewImage');
    var fileNameDisplay = document.getElementById('fileNameDisplay');
    var fileSizeDisplay = document.getElementById('fileSizeDisplay');
    var removeImageBtn = document.getElementById('removeImageBtn');
    var changeImageBtn = document.getElementById('changeImageBtn');

    if (dropzoneArea && proofFileInput) {
        dropzoneArea.addEventListener('click', function(e) {
            if (e.target !== removeImageBtn && e.target !== changeImageBtn && !removeImageBtn.contains(e.target)) {
                proofFileInput.click();
            }
        });

        if (changeImageBtn) {
            changeImageBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                proofFileInput.click();
            });
        }

        if (removeImageBtn) {
            removeImageBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                proofFileInput.value = '';
                dropzonePreview.classList.add('d-none');
                dropzonePrompt.classList.remove('d-none');
            });
        }

        proofFileInput.addEventListener('change', function() {
            handleFileSelect(this.files);
        });

        // Drag events
        ['dragenter', 'dragover'].forEach(function(eventName) {
            dropzoneArea.addEventListener(eventName, function(e) {
                e.preventDefault();
                e.stopPropagation();
                dropzoneArea.classList.add('dragover');
            }, false);
        });

        ['dragleave', 'drop'].forEach(function(eventName) {
            dropzoneArea.addEventListener(eventName, function(e) {
                e.preventDefault();
                e.stopPropagation();
                dropzoneArea.classList.remove('dragover');
            }, false);
        });

        dropzoneArea.addEventListener('drop', function(e) {
            var dt = e.dataTransfer;
            var files = dt.files;
            if (files && files.length > 0) {
                proofFileInput.files = files;
                handleFileSelect(files);
            }
        });
    }

    function handleFileSelect(files) {
        if (!files || files.length === 0) return;
        var file = files[0];

        if (!file.type.match('image.*')) {
            alert('Silakan pilih file gambar (JPG, PNG, WEBP).');
            return;
        }

        var reader = new FileReader();
        reader.onload = function(e) {
            previewImage.src = e.target.result;
            fileNameDisplay.textContent = file.name;
            fileSizeDisplay.textContent = (file.size / (1024 * 1024)).toFixed(2) + ' MB';

            dropzonePrompt.classList.add('d-none');
            dropzonePreview.classList.remove('d-none');
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endsection
