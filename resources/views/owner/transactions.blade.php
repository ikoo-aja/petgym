@extends('layouts.admin')

@section('title', 'Pemantauan Transaksi Kasir (Owner) &mdash; PetGym')
@section('page_title', 'Pemantauan Transaksi POS & Kasir')
@section('page_subtitle', 'Rekapitulasi seluruh riwayat transaksi penjualan kasir dan perpanjangan member (Mode Pemantauan & Read-Only)')

@section('content')


<!-- KPI Cards -->
<div class="row mb-4">
  <div class="col-md-4">
    <div class="card-custom text-white" style="background: linear-gradient(135deg, #0ea5e9, #0284c7);">
      <small class="text-white-50 text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Total Omset Kumulatif</small>
      <h3 class="font-weight-bold text-white mb-0 mt-1">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
      <small class="text-white-50">Dari total {{ $transactionCount }} transaksi kasir</small>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card-custom text-white" style="background: linear-gradient(135deg, #10b981, #059669);">
      <small class="text-white-50 text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Omset Kasir Hari Ini</small>
      <h3 class="font-weight-bold text-white mb-0 mt-1">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</h3>
      <small class="text-white-50">Penjualan produk & perpanjangan hari ini</small>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card-custom text-white" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
      <small class="text-white-50 text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Total Volume Transaksi</small>
      <h3 class="font-weight-bold text-white mb-0 mt-1">{{ $transactionCount }} Transaksi</h3>
      <small class="text-white-50">Tercatat di sistem POS kasir gym</small>
    </div>
  </div>
</div>

<!-- Table Section -->
<div class="card-custom">
  <form action="{{ route('owner.transactions') }}" method="GET" class="row mb-3">
    <div class="col-md-6">
      <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari No. Invoice..." value="{{ request('search') }}">
    </div>
    <div class="col-md-4">
      <select name="payment_method" class="form-control form-control-sm" onchange="this.form.submit()">
        <option value="">Semua Metode Pembayaran</option>
        <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Tunai (Cash)</option>
        <option value="qris" {{ request('payment_method') == 'qris' ? 'selected' : '' }}>QRIS / Digital</option>
        <option value="transfer" {{ request('payment_method') == 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
      </select>
    </div>
    <div class="col-md-2">
      <button type="submit" class="btn btn-sm btn-outline-primary btn-block">Filter</button>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
        <tr>
          <th>No. Invoice</th>
          <th>Waktu Transaksi</th>
          <th>Kasir / Petugas</th>
          <th>Metode Bayar</th>
          <th>Item Transaksi</th>
          <th>Total Nominal</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        @forelse($transactions as $t)
          <tr>
            <td class="font-weight-bold text-dark" style="font-size: 12.5px;">{{ $t->invoice_number }}</td>
            <td style="font-size: 12px;" class="text-muted">{{ $t->created_at ? $t->created_at->format('d M Y H:i') : '-' }}</td>
            <td style="font-size: 12.5px;" class="text-dark">{{ $t->user ? $t->user->name : 'Kasir Ritel' }}</td>
            <td>
              <span class="badge badge-light border text-uppercase px-2 py-1" style="font-size: 10px;">{{ $t->payment_method ?? 'Cash' }}</span>
            </td>
            <td style="font-size: 12px;">
              @if($t->items && $t->items->count())
                @foreach($t->items as $item)
                  <div>&bull; {{ $item->product_name ?? 'Produk' }} (x{{ $item->quantity }})</div>
                @endforeach
              @else
                <span class="text-muted">Perpanjangan / Layanan Gym</span>
              @endif
            </td>
            <td class="font-weight-bold text-success" style="font-size: 13px;">Rp {{ number_format($t->total_amount, 0, ',', '.') }}</td>
            <td>
              @if($t->status == 'void')
                <span class="badge badge-danger px-2 py-1">Dibatalkan (Void)</span>
              @else
                <span class="badge badge-success px-2 py-1">Lunas</span>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="text-center py-4 text-muted">Belum ada data transaksi kasir ditemukan.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-3">
    {{ $transactions->links() }}
  </div>
</div>
@endsection
