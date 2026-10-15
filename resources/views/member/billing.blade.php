@extends('layouts.member')

@section('title', 'Riwayat Tagihan')
@section('page_title', 'Struk & Tagihan')
@section('page_subtitle', 'Riwayat pembayaran dan bukti transaksi digital Anda')

@section('content')
<!-- Filter Navigation (Separated Buttons & Spacing) -->
<div class="mb-3 mb-md-4">
  <div class="d-flex flex-wrap align-items-center" style="gap: 8px;">
    <a href="{{ route('member.billing') }}" class="btn font-weight-bold px-3 py-1 {{ !request()->has('type') ? 'btn-primary' : 'btn-outline-primary' }}" style="border-radius: 8px; font-size: 13px;">
      Semua
    </a>
    <a href="{{ route('member.billing', ['type' => 'membership']) }}" class="btn font-weight-bold px-3 py-1 {{ request('type') == 'membership' ? 'btn-primary' : 'btn-outline-primary' }}" style="border-radius: 8px; font-size: 13px;">
      Keanggotaan & Personal Trainer
    </a>
    <a href="{{ route('member.billing', ['type' => 'inventory']) }}" class="btn font-weight-bold px-3 py-1 {{ request('type') == 'inventory' ? 'btn-primary' : 'btn-outline-primary' }}" style="border-radius: 8px; font-size: 13px;">
      Loker & Ritel
    </a>
  </div>

  <div class="mt-2">
    <span class="badge badge-light border px-2 py-1 text-muted" style="font-size: 11.5px; border-radius: 6px;">
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
    <div class="border rounded p-3 mb-2 bg-light" style="border-radius: 10px !important;">
      <div class="d-flex justify-content-between align-items-start mb-2">
        <div>
          <strong class="text-dark d-block" style="font-size: 13.5px;">{{ $t->invoice_number }}</strong>
          <small class="text-muted">{{ $t->created_at ? $t->created_at->format('d M Y H:i') : '-' }} WIB</small>
        </div>
        <span class="badge badge-success px-2 py-1 font-weight-bold" style="font-size: 11px;">Lunas</span>
      </div>

      <div class="d-flex justify-content-between align-items-center py-2 border-top border-bottom my-2" style="font-size: 13px;">
        <div>
          <span class="badge badge-secondary text-uppercase mr-1" style="font-size: 10.5px;">{{ $t->type ?? 'membership' }}</span>
          <span class="badge badge-light border text-uppercase" style="font-size: 10.5px;">{{ $t->payment_method ?? 'QRIS' }}</span>
        </div>
        <div class="font-weight-bold text-primary" style="font-size: 14px;">
          @if($t->total_amount == 0)
            <span class="text-success">GRATIS (Paket Premium)</span>
          @else
            Rp {{ number_format($t->total_amount, 0, ',', '.') }}
          @endif
        </div>
      </div>

      <div class="text-right">
        <button type="button" class="btn btn-sm btn-outline-primary font-weight-bold px-3 py-1"
                style="border-radius: 6px; font-size: 12px;"
                onclick="openInvoiceReceiptModal({{ $t->id }})">
          <span class="icon-file-text mr-1"></span> Bukti Struk
        </button>
      </div>
    </div>
  @empty
    <div class="text-center py-4 text-muted bg-light rounded border">
      <span class="icon-file-text d-block mb-1" style="font-size: 22px;"></span>
      <small class="d-block font-weight-semibold">Belum ada riwayat transaksi yang tercatat.</small>
    </div>
  @endforelse
</div>

@include('partials.receipt-modal')

@endsection
