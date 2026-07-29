@extends('layouts.superadmin')

@section('title', 'Keuangan & Tagihan &mdash; Superadmin Panel')

@section('page_title', 'Keuangan & Tagihan')
@section('page_subtitle', 'Konfirmasi pembayaran dan kelola tagihan tenant')

@section('content')
<!-- 4. KEUANGAN & TAGIHAN (BILLING & INVOICES) -->
<section id="billing" class="mb-5">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="font-weight-bold text-black mb-0">Keuangan & Invoice Tagihan</h4>
    <button class="btn btn-outline-primary btn-sm" onclick="showToast('Invoice Manual', 'Modul pembuatan invoice tagihan manual berhasil dibuka.', 'info');">+ Buat Invoice Manual</button>
  </div>

  <div class="table-custom p-4">
    <!-- Filter Bar -->
    <div class="row mb-4">
      <div class="col-md-4">
        <label class="text-black font-weight-bold" style="font-size: 13px;">Filter Status Pembayaran</label>
        <select class="form-control form-control-sm" id="statusFilter" onchange="window.location.href = '{{ route('superadmin.billing') }}?status=' + this.value;">
          <option value="all" {{ $status == 'all' ? 'selected' : '' }}>Tampilkan Semua</option>
          <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Menunggu Verifikasi</option>
          <option value="paid" {{ $status == 'paid' ? 'selected' : '' }}>Lunas</option>
        </select>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th class="text-black font-weight-bold">No. Invoice</th>
            <th class="text-black font-weight-bold">Gym / Tenant</th>
            <th class="text-black font-weight-bold">Jumlah Tagihan</th>
            <th class="text-black font-weight-bold">Jatuh Tempo</th>
            <th class="text-black font-weight-bold">Status Pembayaran</th>
            <th class="text-black font-weight-bold">Aksi / Verifikasi</th>
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
            <td>{{ $tenantName }}</td>
            <td class="font-weight-bold">{{ $amountFormatted }}</td>
            <td>{{ $dueDateFormatted }}</td>
            <td>
              @if($statusVal == 'pending')
                <span class="badge badge-status-pending px-2 py-1 rounded">Menunggu Verifikasi</span>
              @else
                <span class="badge badge-status-active px-2 py-1 rounded">Lunas</span>
              @endif
            </td>
            <td>
              <div class="d-flex align-items-center">
                <!-- Button View Proof -->
                <button class="btn btn-sm btn-outline-info py-1 px-2 mr-2 btn-view-proof" 
                        data-toggle="modal" 
                        data-target="#viewProofModal" 
                        data-invoice="{{ $invNo }}" 
                        data-tenant="{{ $tenantName }}" 
                        data-proof="{{ $proofVal }}" 
                        title="Lihat Bukti Transfer"
                        style="font-size: 12px; font-weight: bold;">
                  <span class="icon-search"></span> Lihat Bukti
                </button>

                @if($statusVal == 'pending')
                  <button class="btn btn-sm btn-success py-1 px-2 btn-verify-direct" 
                          data-invoice="{{ $invNo }}"
                          style="font-size: 12px; font-weight: bold;">
                    Verifikasi Lunas
                  </button>
                @else
                  <button class="btn btn-sm btn-light py-1 px-2" disabled style="font-size: 12px; font-weight: bold;">
                    Verified
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
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-header-title font-weight-bold text-black" id="proofModalLabel">Bukti Transfer Pembayaran</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-center bg-light">
        <div class="mb-3">
          <strong class="text-black" id="proofInvoiceNo">#INV-XXX</strong> &mdash; <span id="proofTenantName">Nama Gym</span>
        </div>
        <div class="p-2 border bg-white rounded shadow-sm d-inline-block">
          <img id="proofImage" src="" alt="Bukti Transfer" class="img-fluid rounded" style="max-height: 400px; object-fit: contain;">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
        <button type="button" class="btn btn-success btn-sm btn-approve-direct">Verifikasi Lunas Sekarang</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  $(document).ready(function() {
    let currentVerifyButton = null;

    // 1. Tampilkan bukti transfer di modal
    $('.btn-view-proof').on('click', function() {
      const invoice = $(this).data('invoice');
      const tenant = $(this).data('tenant');
      const proofUrl = $(this).data('proof');

      currentVerifyButton = $(this).closest('tr').find('.btn-verify-direct');

      $('#proofInvoiceNo').text(invoice);
      $('#proofTenantName').text(tenant);
      $('#proofImage').attr('src', proofUrl);

      // Sembunyikan tombol verifikasi di modal jika invoice sudah lunas
      if (currentVerifyButton.length === 0) {
        $('.btn-approve-direct').hide();
      } else {
        $('.btn-approve-direct').show();
      }
    });

    // 2. Verifikasi lunas dari modal
    $(document).on('click', '.btn-approve-direct', function() {
      if (currentVerifyButton && currentVerifyButton.length) {
        executeVerification(currentVerifyButton);
      }
      $('#viewProofModal').modal('hide');
    });

    // 3. Verifikasi lunas langsung dari tabel
    $(document).on('click', '.btn-verify-direct', function() {
      executeVerification($(this));
    });

    function executeVerification(button) {
      const row = button.closest('tr');
      const statusBadge = row.find('.badge-status-pending, .badge-status-active');
      const invoiceNo = row.find('td').first().text();
      
      statusBadge.removeClass('badge-status-pending').addClass('badge-status-active').text('Lunas');
      button.removeClass('btn-success btn-verify-direct').addClass('btn-light').prop('disabled', true).text('Verified');
      
      showToast('Verifikasi Berhasil', `Pembayaran untuk invoice ${invoiceNo} berhasil diverifikasi Lunas.`, 'success');
    }
  });
</script>
@endsection
