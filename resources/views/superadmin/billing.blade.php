@extends('layouts.superadmin')

@section('title', 'Keuangan & Tagihan Perpanjangan Sewa — Superadmin Panel')

@section('page_title', 'Keuangan & Tagihan Perpanjangan Sewa')
@section('page_subtitle', 'Verifikasi pembayaran perpanjangan sewa website gym dari admin tenant')

@section('content')
<section id="billing" class="mb-5">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="font-weight-bold text-black mb-0">Daftar Pengajuan Tagihan & Perpanjangan Sewa</h4>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-radius: 10px;">
      <strong>Sukses!</strong> {{ session('success') }}
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-radius: 10px;">
      <strong>Peringatan!</strong> {{ session('error') }}
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
  @endif

  <div class="table-custom p-4">
    <!-- Filter & Search Bar -->
    <form action="{{ route('superadmin.billing') }}" method="GET" class="row mb-4">
      <div class="col-md-6 mb-2 mb-md-0">
        <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari no invoice, nama gym, domain, atau pemilik..." value="{{ request('search') }}">
      </div>
      <div class="col-md-4 mb-2 mb-md-0">
        <select name="status" class="form-control form-control-sm" onchange="this.form.submit()">
          <option value="all" {{ $status == 'all' ? 'selected' : '' }}>Semua Status Pembayaran</option>
          <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Menunggu Verifikasi Superadmin</option>
          <option value="paid" {{ $status == 'paid' ? 'selected' : '' }}>Disetujui / Lunas (Masa Aktif Bertambah)</option>
          <option value="rejected" {{ $status == 'rejected' ? 'selected' : '' }}>Ditolak</option>
        </select>
      </div>
      <div class="col-md-2">
        <button type="submit" class="btn btn-sm btn-outline-primary btn-block px-3">Filter</button>
      </div>
    </form>

    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead>
          <tr>
            <th class="text-black font-weight-bold" style="width: 140px;">No. Invoice</th>
            <th class="text-black font-weight-bold">Gym / Tenant</th>
            <th class="text-black font-weight-bold">Paket & Durasi</th>
            <th class="text-black font-weight-bold">Nominal Pembayaran</th>
            <th class="text-black font-weight-bold">Tanggal Tagihan</th>
            <th class="text-black font-weight-bold">Bukti Transfer</th>
            <th class="text-black font-weight-bold">Status</th>
            <th class="text-black font-weight-bold text-right" style="min-width: 190px;">Aksi Verifikasi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($invoices as $invoice)
          @php
            $invNo = $invoice->invoice_number;
            $tenant = $invoice->tenant;
            $tenantName = $tenant ? $tenant->name : 'Tenant Gym';
            $subdomain = $tenant ? $tenant->subdomain : '';
            $ownerName = $tenant ? $tenant->owner_name : '-';
            $duration = $invoice->duration_months ?: 1;
            $statusVal = strtolower($invoice->status);
            $proofVal = $invoice->proof_url ? asset($invoice->proof_url) : null;
          @endphp
          <tr>
            <!-- 1. No Invoice -->
            <td>
              <strong class="text-primary font-weight-bold" style="font-size: 13px;">{{ $invNo }}</strong><br>
              <small class="text-muted">{{ $invoice->payment_method ?? 'Transfer Bank' }}</small>
            </td>

            <!-- 2. Gym / Tenant -->
            <td>
              <strong class="text-dark">{{ $tenantName }}</strong><br>
              <small class="text-muted"><i class="icon-globe mr-1"></i> {{ $subdomain }}</small><br>
              <small class="text-muted"><i class="icon-user mr-1"></i> {{ $ownerName }}</small>
            </td>

            <!-- 3. Paket & Durasi -->
            <td>
              <span class="badge badge-info px-2 py-1 font-weight-bold">{{ $invoice->plan_name ?? ($tenant->plan_name ?? 'Paket Sewa') }}</span>
              <div class="mt-1 small text-dark font-weight-bold">
                +{{ $duration }} Bulan ({{ $duration * 30 }} Hari)
              </div>
            </td>

            <!-- 4. Nominal Tagihan -->
            <td>
              <span class="font-weight-bold text-success" style="font-size: 14px;">
                Rp {{ number_format($invoice->amount, 0, ',', '.') }}
              </span>
            </td>

            <!-- 5. Tanggal Tagihan -->
            <td>
              <span class="text-dark small font-weight-bold">{{ $invoice->created_at ? $invoice->created_at->format('d M Y') : '-' }}</span><br>
              <small class="text-muted">{{ $invoice->created_at ? $invoice->created_at->format('H:i') . ' WIB' : '' }}</small>
            </td>

            <!-- 6. Bukti Transfer -->
            <td>
              @if($proofVal)
                <button type="button" class="btn btn-sm btn-outline-info py-1 px-2 font-weight-bold btn-view-proof"
                        data-toggle="modal"
                        data-target="#viewProofModal"
                        data-id="{{ $invoice->id }}"
                        data-invoice="{{ $invNo }}"
                        data-tenant="{{ $tenantName }}"
                        data-amount="Rp {{ number_format($invoice->amount, 0, ',', '.') }}"
                        data-duration="{{ $duration }} Bulan"
                        data-proof="{{ $proofVal }}"
                        style="font-size: 12px; border-radius: 6px;">
                  <span class="icon-image mr-1"></span> Cek SS Bukti
                </button>
              @else
                <span class="text-muted small font-italic">Tidak ada bukti</span>
              @endif
            </td>

            <!-- 7. Status -->
            <td>
              @if($statusVal === 'paid')
                <span class="badge badge-success px-2 py-1 font-weight-bold rounded">
                  <i class="icon-check mr-1"></i> Lunas / Disetujui
                </span>
                @if($invoice->paid_at)
                  <small class="d-block text-muted" style="font-size: 10px;">{{ $invoice->paid_at->format('d/m/y H:i') }}</small>
                @endif
              @elseif($statusVal === 'pending' || $statusVal === 'dp_pending')
                <span class="badge badge-warning px-2 py-1 text-dark font-weight-bold rounded">
                  <i class="icon-clock-o mr-1"></i> Menunggu Verifikasi
                </span>
              @elseif($statusVal === 'rejected')
                <span class="badge badge-danger px-2 py-1 font-weight-bold rounded">
                  <i class="icon-close mr-1"></i> Ditolak
                </span>
              @else
                <span class="badge badge-secondary px-2 py-1 font-weight-bold rounded">{{ strtoupper($statusVal) }}</span>
              @endif
            </td>

            <!-- 8. Aksi Verifikasi Superadmin -->
            <td class="text-right">
              @if($statusVal === 'pending' || $statusVal === 'dp_pending')
                <div class="d-inline-flex align-items-center" style="gap: 5px;">
                  <!-- Tombol Setujui Perpanjangan -->
                  <form action="{{ route('superadmin.billing.approve', $invoice->id) }}" method="POST" onsubmit="return confirm('Setujui pembayaran dan perpanjang masa aktif gym {{ $tenantName }} selama {{ $duration }} bulan?');">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-success py-1 px-3 font-weight-bold" style="font-size: 12px; border-radius: 6px;">
                      <span class="icon-check mr-1"></span> Setujui (+{{ $duration }} bln)
                    </button>
                  </form>

                  <!-- Dropdown Tolak -->
                  <div class="dropdown">
                    <button class="btn btn-sm btn-outline-danger py-1 px-2 font-weight-bold" type="button" data-toggle="dropdown" title="Tolak Pengajuan">
                      <span class="icon-close"></span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right shadow-sm border-0 p-2" style="width: 240px; border-radius: 8px;">
                      <form action="{{ route('superadmin.billing.reject', $invoice->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menolak pengajuan tagihan ini?');">
                        @csrf
                        <div class="form-group mb-2">
                          <label class="small font-weight-bold text-dark mb-1">Alasan Penolakan:</label>
                          <input type="text" name="notes" class="form-control form-control-sm" placeholder="Bukti tidak valid / kurang transfer..." required>
                        </div>
                        <button type="submit" class="btn btn-sm btn-danger btn-block font-weight-bold">
                          Konfirmasi Tolak
                        </button>
                      </form>
                    </div>
                  </div>
                </div>
              @elseif($statusVal === 'paid')
                <span class="badge badge-light border border-success text-success px-2 py-1 font-weight-bold small">
                  <i class="icon-check mr-1"></i> Masa Aktif Diperpanjang
                </span>
              @else
                <span class="badge badge-light border border-danger text-danger px-2 py-1 font-weight-bold small">
                  Pengajuan Ditolak
                </span>
              @endif
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="8" class="text-center py-5 text-muted">
              <span class="icon-inbox h3 d-block mb-2 text-muted"></span>
              Belum ada data pengajuan tagihan perpanjangan sewa.
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-3">
      <small class="text-muted">Menampilkan {{ $invoices->count() }} dari {{ $invoices->total() }} tagihan</small>
      {{ $invoices->links('pagination::bootstrap-4') }}
    </div>
  </div>
</section>
@endsection

@section('modals')
<!-- MODAL: LIHAT BUKTI TRANSFER PERPANJANGAN -->
<div class="modal fade" id="viewProofModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
      <div class="modal-header bg-white border-bottom py-3 px-4 text-dark">
        <div>
          <h5 class="modal-title font-weight-bold text-dark mb-0" id="proofModalLabel">Bukti Pembayaran Perpanjangan Sewa</h5>
          <small class="text-muted">Periksa bukti transfer penyewa sebelum melakukan verifikasi</small>
        </div>
        <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body text-center bg-light p-4">
        <div class="mb-3 text-left bg-white p-3 rounded border">
          <div class="d-flex justify-content-between">
            <span class="text-muted small">No. Invoice:</span>
            <strong class="text-primary font-weight-bold" id="proofInvoiceNo">#INV-XXX</strong>
          </div>
          <div class="d-flex justify-content-between">
            <span class="text-muted small">Gym / Tenant:</span>
            <strong class="text-dark" id="proofTenantName">Nama Gym</strong>
          </div>
          <div class="d-flex justify-content-between">
            <span class="text-muted small">Durasi & Nominal:</span>
            <strong class="text-success" id="proofAmountInfo">Rp 0</strong>
          </div>
        </div>

        <div class="p-2 border bg-white rounded shadow-sm d-inline-block mb-2 w-100">
          <img id="proofImage" src="" alt="Bukti Transfer Pembayaran" class="img-fluid rounded" style="max-height: 400px; object-fit: contain;">
        </div>
      </div>

      <div class="modal-footer bg-white py-3 px-4 d-flex justify-content-between">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
        <a id="proofOpenNewTab" href="#" target="_blank" class="btn btn-outline-primary btn-sm font-weight-bold">
          <span class="icon-external-link mr-1"></span> Buka Gambar Penuh
        </a>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  $(document).ready(function() {
    $('.btn-view-proof').on('click', function() {
      const invoice = $(this).data('invoice');
      const tenant = $(this).data('tenant');
      const amount = $(this).data('amount');
      const duration = $(this).data('duration');
      const proofUrl = $(this).data('proof');

      $('#proofInvoiceNo').text(invoice);
      $('#proofTenantName').text(tenant);
      $('#proofAmountInfo').text(amount + ' (' + duration + ')');
      $('#proofImage').attr('src', proofUrl);
      $('#proofOpenNewTab').attr('href', proofUrl);
    });
  });
</script>
@endsection
