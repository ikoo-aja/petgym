@extends('layouts.admin')

@section('title', 'Laporan Eksekutif (Owner) &mdash; PetGym')
@section('page_title', 'Pusat Laporan & Omset Eksekutif')
@section('page_subtitle', 'Analisis keuangan, pertumbuhan member, rekapitulasi penjualan kasir, dan kesehatan finansial gym (Mode Pemantauan & Read-Only)')

@section('content')
<!-- Banner Mode Pemantauan Eksekutif -->


<!-- Financial Summary Cards -->
<div class="row mb-4">
  <div class="col-md-3">
    <div class="card-custom text-white" style="background: linear-gradient(135deg, #0ea5e9, #0284c7);">
      <small class="text-white text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Total Omset</small>
      <h3 class="font-weight-bold text-white mb-0 mt-1">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
      <small class="text-white-50">Total seluruh transaksi</small>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card-custom text-white" style="background: linear-gradient(135deg, #10b981, #059669);">
      <small class="text-white text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Omset Bulan Ini</small>
      <h3 class="font-weight-bold text-white mb-0 mt-1">Rp {{ number_format($monthRevenue, 0, ',', '.') }}</h3>
      <small class="text-white-50">Total pendapatan bulan ini</small>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card-custom text-white" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
      <small class="text-white text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Total Base Member</small>
      <h3 class="font-weight-bold text-white mb-0 mt-1">{{ $totalMembers }} Member</h3>
      <small class="text-white-50">Pertumbuhan basis pelanggan</small>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card-custom text-white" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
      <small class="text-white text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Member Aktif</small>
      <h3 class="font-weight-bold text-white mb-0 mt-1">{{ $activeMembers }} Member</h3>
      <small class="text-white-50">Tingkat retensi member aktif</small>
    </div>
  </div>
</div>

<div class="card-custom">
  <h6 class="font-weight-bold text-dark mb-3">Stream Transaksi Penjualan Terbaru</h6>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
        <tr>
          <th>No. Invoice</th>
          <th>Tanggal & Jam</th>
          <th>Kasir</th>
          <th>Metode Bayar</th>
          <th>Total Nominal</th>
        </tr>
      </thead>
      <tbody>
        @forelse($recentTransactions as $rt)
          <tr>
            <td class="font-weight-bold text-dark" style="font-size: 12.5px;">{{ $rt->invoice_number }}</td>
            <td style="font-size: 12px;" class="text-muted">{{ $rt->created_at ? $rt->created_at->format('d M Y H:i') : '-' }}</td>
            <td style="font-size: 12.5px;" class="text-dark">{{ $rt->user ? $rt->user->name : 'Kasir Ritel' }}</td>
            <td><span class="badge badge-light border text-uppercase px-2 py-1" style="font-size: 10px;">{{ $rt->payment_method ?? 'Cash' }}</span></td>
            <td class="font-weight-bold text-success" style="font-size: 13px;">Rp {{ number_format($rt->total_amount, 0, ',', '.') }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="text-center py-4 text-muted">Belum ada transaksi tercatat.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
