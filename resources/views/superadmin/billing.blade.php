@extends('layouts.superadmin')

@section('title', 'Keuangan & Tagihan &mdash; Superadmin Panel')

@section('page_title', 'Keuangan & Tagihan')
@section('page_subtitle', 'Konfirmasi pembayaran dan kelola tagihan tenant')

@section('content')
<!-- 4. KEUANGAN & TAGIHAN (BILLING & INVOICES) -->
<section id="billing" class="mb-5">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="font-weight-bold text-black mb-0">Keuangan & Invoice Tagihan</h4>
    {{-- <button class="btn btn-outline-primary btn-sm" onclick="showToast('Invoice Manual', 'Modul pembuatan invoice tagihan manual berhasil dibuka.', 'info');">+ Buat Invoice Manual</button> --}}
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
      <strong>Sukses!</strong> {{ session('success') }}
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
  @endif

  <div class="table-custom p-4">
    <!-- Filter Bar -->
    <div class="row mb-4">
      <div class="col-md-4">
        <label class="text-black font-weight-bold" style="font-size: 13px;">Filter Status Pembayaran</label>
        <select class="form-control form-control-sm" id="statusFilter" onchange="window.location.href = '{{ route('superadmin.billing') }}?status=' + this.value;">
          <option value="all" {{ $status == 'all' ? 'selected' : '' }}>Tampilkan Semua</option>
          <option value="dp_pending" {{ $status == 'dp_pending' ? 'selected' : '' }}>Menunggu Verifikasi DP 50%</option>
          <option value="dp_paid" {{ $status == 'dp_paid' ? 'selected' : '' }}>DP 50% Disetujui (Menunggu Pelunasan)</option>
          <option value="paid" {{ $status == 'paid' ? 'selected' : '' }}>Lunas 100%</option>
        </select>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th class="text-black font-weight-bold">No. Invoice</th>
            <th class="text-black font-weight-bold">Gym / Tenant</th>
            <th class="text-black font-weight-bold">Nominal Tagihan</th>
            <th class="text-black font-weight-bold">Tanggal Tagihan</th>
            <th class="text-black font-weight-bold">Status Pembayaran</th>
            <th class="text-black font-weight-bold">Aksi / Verifikasi Superadmin</th>
          </tr>
        </thead>
        <tbody>
          @forelse($invoices as $invoice)
          @php
            $invNo = $invoice->invoice_number ?? $invoice['invoice_no'];
            $tenantName = isset($invoice->tenant) ? $invoice->tenant->name : ($invoice['tenant'] ?? 'Gym');
            $amountFormatted = is_numeric($invoice->amount ?? null) ? 'Rp ' . number_format($invoice->amount, 0, ',', '.') : ($invoice['amount'] ?? 'Rp 0');
            $dueDateFormatted = is_object($invoice->due_date ?? null) ? $invoice->due_date->format('d M Y') : ($invoice['due_date'] ?? '-');
            $statusVal = $invoice->status ?? $invoice['status'];
            $proofVal = $invoice->proof_url ?? 'https://raw.githubusercontent.com/Antigravity-AI/mock-assets/main/receipt-mockup.png';
          @endphp
          <tr>
            <td class="font-weight-bold text-black">{{ $invNo }}</td>
            <td class="text-black">
              <strong>{{ $tenantName }}</strong><br>
              <small class="text-muted">{{ $invoice->tenant->subdomain ?? '' }}</small>
            </td>
            <td class="font-weight-bold text-success">{{ $amountFormatted }}</td>
            <td class="text-black">{{ $dueDateFormatted }}</td>
            <td>
              @if($statusVal == 'dp_pending' || $statusVal == 'pending')
                <span class="badge badge-warning px-2 py-1 text-dark font-weight-bold rounded">Menunggu Verifikasi DP 50%</span>
              @elseif($statusVal == 'dp_paid')
                <span class="badge badge-info px-2 py-1 font-weight-bold rounded">DP 50% Disetujui (Aktif)</span>
              @else
                <span class="badge badge-success px-2 py-1 font-weight-bold rounded">Lunas 100%</span>
              @endif
            </td>
            <td>
              <div class="d-flex align-items-center">
                <!-- Button View Proof -->
                <button class="btn btn-sm btn-outline-info py-1 px-2 mr-2 btn-view-proof"
                        data-toggle="modal"
                        data-target="#viewProofModal"
                        data-id="{{ $invoice->id }}"
                        data-invoice="{{ $invNo }}"
                        data-tenant="{{ $tenantName }}"
                        data-proof="{{ $proofVal }}"
                        title="Lihat SS Bukti Transfer"
                        style="font-size: 12px; font-weight: bold;">
                  <span class="icon-search"></span> Lihat Bukti SS
                </button>

                @if($statusVal == 'dp_pending' || $statusVal == 'pending')
                  <button class="btn btn-sm btn-primary py-1 px-2 btn-verify-direct"
                          data-id="{{ $invoice->id }}"
                          data-invoice="{{ $invNo }}"
                          data-action="verify-dp"
                          style="font-size: 12px; font-weight: bold;">
                    Verifikasi DP 50%
                  </button>
                @elseif($statusVal == 'dp_paid')
                  <button class="btn btn-sm btn-success py-1 px-2 btn-verify-direct"
                          data-id="{{ $invoice->id }}"
                          data-invoice="{{ $invNo }}"
                          data-action="verify"
                          style="font-size: 12px; font-weight: bold;">
                    Verifikasi Pelunasan 100%
                  </button>
                @else
                  <button class="btn btn-sm btn-light py-1 px-2" disabled style="font-size: 12px; font-weight: bold;">
                    Verified (Lunas 100%)
                  </button>
                @endif
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="6" class="text-center py-4 text-muted">Tidak ada tagihan dengan status ini.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</section>
@endsection

@section('modals')
<!-- MODAL: LIHAT BUKTI PEMBAYARAN -->
<div class="modal fade" id="viewProofModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
      <div class="modal-header bg-dark text-white">
        <h5 class="modal-header-title font-weight-bold text-white mb-0" id="proofModalLabel">Bukti Transfer Pembayaran DP 50%</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-center bg-light p-4">
        <div class="mb-3">
          <strong class="text-black h5 d-block" id="proofInvoiceNo">#INV-XXX</strong>
          <span id="proofTenantName" class="text-muted font-weight-bold">Nama Gym</span>
        </div>
        <div class="p-2 border bg-white rounded shadow-sm d-inline-block mb-3">
          <img id="proofImage" src="" alt="Bukti Transfer SS Pembelian" class="img-fluid rounded" style="max-height: 420px; object-fit: contain;">
        </div>
      </div>
      <div class="modal-footer bg-white">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
        <button type="button" class="btn btn-primary btn-sm btn-approve-direct font-weight-bold">Verifikasi & Aktifkan Sekarang</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  $(document).ready(function() {
    let currentVerifyButton = null;

    // 1. Tampilkan bukti transfer SS di modal
    $('.btn-view-proof').on('click', function() {
      const invoice = $(this).data('invoice');
      const tenant = $(this).data('tenant');
      const proofUrl = $(this).data('proof');

      currentVerifyButton = $(this).closest('tr').find('.btn-verify-direct');

      $('#proofInvoiceNo').text(invoice);
      $('#proofTenantName').text(tenant);
      $('#proofImage').attr('src', proofUrl);

      if (currentVerifyButton.length === 0) {
        $('.btn-approve-direct').hide();
      } else {
        $('.btn-approve-direct').show();
      }
    });

    // 2. Verifikasi dari modal
    $(document).on('click', '.btn-approve-direct', function() {
      if (currentVerifyButton && currentVerifyButton.length) {
        executeVerification(currentVerifyButton);
      }
      $('#viewProofModal').modal('hide');
    });

    // 3. Verifikasi langsung dari tabel
    $(document).on('click', '.btn-verify-direct', function() {
      executeVerification($(this));
    });

    function executeVerification(button) {
      const invoiceId = button.data('id');
      const actionType = button.data('action') || 'verify';

      button.prop('disabled', true).text('Memproses...');

      fetch("{{ url('/superadmin/billing') }}/" + invoiceId + "/" + actionType, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        }
      })
      .then(res => res.json())
      .then(data => {
        window.location.reload();
      })
      .catch(err => {
        window.location.reload();
      });
    }
  });
</script>
@endsection
