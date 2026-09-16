@extends('layouts.member')

@section('title', 'Riwayat Tagihan - PetGym')
@section('page_title', 'Riwayat Tagihan')
@section('page_subtitle', '')
@section('member_tier_badge', 'Tier ' . strtoupper($member->membership_tier ?? 'Basic'))

@section('content')
<!-- Filter Navigation (Separated Buttons & Spacing) -->
<div class="mb-4 pt-2">
  <div class="d-flex flex-wrap align-items-center" style="gap: 8px;">
    <a href="{{ route('member.billing') }}" class="btn font-weight-bold px-2 py-1 {{ !request()->has('type') ? 'btn-primary' : 'btn-outline-primary' }}" style="border-radius: 10px; font-size: 13px;">
      Semua
    </a>
    <a href="{{ route('member.billing', ['type' => 'membership']) }}" class="btn font-weight-bold px-2 py-1 {{ request('type') == 'membership' ? 'btn-primary' : 'btn-outline-primary' }}" style="border-radius: 10px; font-size: 13px;">
      Keanggotaan & PT
    </a>
    <a href="{{ route('member.billing', ['type' => 'inventory']) }}" class="btn font-weight-bold px-2 py-1 {{ request('type') == 'inventory' ? 'btn-primary' : 'btn-outline-primary' }}" style="border-radius: 10px; font-size: 13px;">
      Loker & Ritel
    </a>
  </div>

  <div class="mt-3 pt-2">
    <span class="badge badge-light border px-3 py-2 text-muted" style="font-size: 12px; border-radius: 8px;">
      Total: <strong class="text-dark">{{ count($transactions) }} Transaksi</strong>
    </span>
  </div>
</div>

<!-- Transactions Card List -->
<div class="card-custom">
  <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
    <h6 class="font-weight-bold text-dark mb-0">Daftar Transaksi</h6>
    <span class="badge badge-primary font-weight-bold">{{ count($transactions) }} Transaksi</span>
  </div>

  @forelse($transactions as $t)
    <div class="border rounded p-3 mb-3 bg-light" style="border-radius: 12px !important;">
      <div class="d-flex justify-content-between align-items-start mb-2">
        <div>
          <strong class="text-dark d-block" style="font-size: 14px;">{{ $t->invoice_number }}</strong>
          <small class="text-muted">{{ $t->created_at ? $t->created_at->format('d M Y H:i') : '-' }} WIB</small>
        </div>
        <span class="badge badge-success px-2 py-1 font-weight-bold" style="font-size: 11px;">Lunas</span>
      </div>

      <div class="d-flex justify-content-between align-items-center py-2 border-top border-bottom my-2" style="font-size: 13px;">
        <div>
          <span class="badge badge-secondary text-uppercase mr-1">{{ $t->type ?? 'membership' }}</span>
          <span class="badge badge-light border text-uppercase">{{ $t->payment_method ?? 'QRIS' }}</span>
        </div>
        <div class="font-weight-bold text-primary" style="font-size: 15px;">
          @if($t->total_amount == 0)
            <span class="text-success">GRATIS (Bundling)</span>
          @else
            Rp {{ number_format($t->total_amount, 0, ',', '.') }}
          @endif
        </div>
      </div>

      <div class="text-right">
        <button type="button" class="btn btn-sm btn-outline-primary font-weight-bold btn-receipt px-3 py-1"
                style="border-radius: 8px;"
                data-invoice="{{ $t->invoice_number }}"
                data-date="{{ $t->created_at ? $t->created_at->format('d M Y H:i') : '-' }}"
                data-method="{{ strtoupper($t->payment_method ?? 'QRIS') }}"
                data-amount="{{ $t->total_amount == 0 ? 'GRATIS (Bundling Premium)' : 'Rp ' . number_format($t->total_amount, 0, ',', '.') }}"
                data-type="{{ strtoupper($t->type ?? 'MEMBERSHIP') }}"
                data-toggle="modal"
                data-target="#receiptModal">
          <span class="icon-file-text mr-1"></span> Lihat Bukti Struk
        </button>
      </div>
    </div>
  @empty
    <div class="text-center py-4 text-muted bg-light rounded border">
      <span class="icon-file-text d-block mb-1" style="font-size: 24px;"></span>
      <small class="d-block font-weight-semibold">Belum ada riwayat transaksi tagihan tercatat.</small>
    </div>
  @endforelse
</div>

<!-- Modal Struk Pembayaran -->
<div class="modal fade" id="receiptModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-light">
        <div>
          <h5 class="modal-title font-weight-bold text-dark mb-0" id="receiptInvoice">INV-XXXXX</h5>
          <small class="text-muted">Bukti Pembayaran Resmi Gym</small>
        </div>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body p-4">
        <div class="text-center mb-4">
          <span class="badge badge-success px-3 py-1 font-weight-bold text-uppercase mb-2">PEMBAYARAN LUNAS</span>
          <h2 class="font-weight-bold text-primary mb-0" id="receiptAmount">Rp 0</h2>
        </div>

        <div class="p-3 bg-light rounded mb-3" style="font-size: 13px;">
          <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">Tanggal Transaksi:</span>
            <strong class="text-dark" id="receiptDate">-</strong>
          </div>
          <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">Metode Pembayaran:</span>
            <strong class="text-dark" id="receiptMethod">QRIS</strong>
          </div>
          <div class="d-flex justify-content-between">
            <span class="text-muted">Kategori Transaksi:</span>
            <strong class="text-dark" id="receiptType">MEMBERSHIP</strong>
          </div>
        </div>

        <small class="text-muted d-block text-center">Struk digital ini sah sebagai bukti pembayaran di PetGym.</small>
      </div>

      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Tutup</button>
        <button type="button" class="btn btn-primary font-weight-bold" onclick="window.print()">Cetak Struk</button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script>
  $(document).ready(function() {
    $('.btn-receipt').on('click', function() {
      const invoice = $(this).data('invoice');
      const date = $(this).data('date');
      const method = $(this).data('method');
      const amount = $(this).data('amount');
      const type = $(this).data('type');

      $('#receiptInvoice').text(invoice);
      $('#receiptDate').text(date);
      $('#receiptMethod').text(method);
      $('#receiptAmount').text(amount);
      $('#receiptType').text(type);
    });
  });
</script>
@endsection
