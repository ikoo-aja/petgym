@extends('layouts.layout')

@section('title', 'Beranda Supervisor — PetGym')
@section('page_title', 'Beranda Supervisor Gym')
@section('page_subtitle', 'Pengawasan operasional lapangan, otorisasi kasir, dan fasilitas gym')

@section('content')
<div class="container-fluid p-0">

  <!-- Header Banner -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h3 class="font-weight-bold text-dark mb-1">Pengawasan Operasional Lapangan</h3>
      <p class="text-muted small mb-0">Selamat datang, <strong>{{ $user->name }}</strong>. Pantau otorisasi kasir, jadwal kerja staf, ketersediaan stok, dan fasilitas gym hari ini.</p>
    </div>
    <div>
      <span class="badge badge-info px-3 py-2 font-weight-bold" style="font-size: 13px;">
        <i class="icon-calendar mr-1"></i> {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
      </span>
    </div>
  </div>

  <!-- Stat Cards -->
  <div class="row mb-4">
    <div class="col-md-3 mb-3 mb-md-0">
      <div class="card-custom p-3 border-left" style="border-left: 4px solid #ef4444 !important;">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <small class="text-muted font-weight-bold text-uppercase" style="font-size: 11px;">Permintaan Batal Transaksi</small>
            <h3 class="font-weight-bold text-dark mt-1 mb-0">{{ $pendingVoidCount }}</h3>
          </div>
          <div class="rounded-circle p-3 bg-light text-danger">
            <span class="icon-check" style="font-size: 22px;"></span>
          </div>
        </div>
        <div class="mt-2 pt-2 border-top">
          @if($pendingVoidCount > 0)
            <span class="text-danger font-weight-bold small"><i class="icon-exclamation-circle mr-1"></i> Butuh persetujuan segera</span>
          @else
            <span class="text-success small"><i class="icon-check-circle mr-1"></i> Tidak ada antrean pembatalan</span>
          @endif
        </div>
      </div>
    </div>

    <div class="col-md-3 mb-3 mb-md-0">
      <div class="card-custom p-3 border-left" style="border-left: 4px solid #f59e0b !important;">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <small class="text-muted font-weight-bold text-uppercase" style="font-size: 11px;">Stok Ritel Menipis</small>
            <h3 class="font-weight-bold text-dark mt-1 mb-0">{{ $lowStockCount }}</h3>
          </div>
          <div class="rounded-circle p-3 bg-light text-warning">
            <span class="icon-shopping-cart" style="font-size: 22px;"></span>
          </div>
        </div>
        <div class="mt-2 pt-2 border-top">
          @if($lowStockCount > 0)
            <span class="text-warning font-weight-bold small"><i class="icon-exclamation-triangle mr-1"></i> {{ $lowStockCount }} produk sisa &le; 10 unit</span>
          @else
            <span class="text-success small"><i class="icon-check-circle mr-1"></i> Semua stok aman</span>
          @endif
        </div>
      </div>
    </div>

    <div class="col-md-3 mb-3 mb-md-0">
      <div class="card-custom p-3 border-left" style="border-left: 4px solid #3b82f6 !important;">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <small class="text-muted font-weight-bold text-uppercase" style="font-size: 11px;">Staf Bertugas Hari Ini</small>
            <h3 class="font-weight-bold text-dark mt-1 mb-0">{{ $activeShiftsCount }}</h3>
          </div>
          <div class="rounded-circle p-3 bg-light text-primary">
            <span class="icon-people" style="font-size: 22px;"></span>
          </div>
        </div>
        <div class="mt-2 pt-2 border-top">
          <span class="text-muted small">Resepsionis & Personal Trainer</span>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card-custom p-3 border-left" style="border-left: 4px solid #10b981 !important;">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <small class="text-muted font-weight-bold text-uppercase" style="font-size: 11px;">Keluhan Member Aktif</small>
            <h3 class="font-weight-bold text-dark mt-1 mb-0">{{ $openComplaintsCount }}</h3>
          </div>
          <div class="rounded-circle p-3 bg-light text-success">
            <span class="icon-person" style="font-size: 22px;"></span>
          </div>
        </div>
        <div class="mt-2 pt-2 border-top">
          @if($openComplaintsCount > 0)
            <span class="text-danger font-weight-bold small">{{ $openComplaintsCount }} tiket perlu ditindaklanjuti</span>
          @else
            <span class="text-success small"><i class="icon-check-circle mr-1"></i> Tidak ada keluhan terbuka</span>
          @endif
        </div>
      </div>
    </div>
  </div>

  <!-- Row: Main Tasks & Operations -->
  <div class="row">
    <!-- Antrean Otorisasi Pembatalan Transaksi Kasir -->
    <div class="col-md-12 mb-4">
      <div class="card-custom">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
          <div>
            <h6 class="font-weight-bold text-dark mb-0"><i class="icon-check-circle text-danger mr-1"></i> Antrean Otorisasi Pembatalan Transaksi</h6>
            <small class="text-muted">Persetujuan pembatalan struk dari meja kasir resepsionis</small>
          </div>
          <a href="/supervisor/features?tab=void" class="btn btn-sm btn-outline-primary font-weight-bold">Buka Modul Pembatalan</a>
        </div>
        <div class="p-3">
          @if($pendingVoids->count() > 0)
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
                  <tr>
                    <th>Invoice</th>
                    <th>Kasir Pemohon</th>
                    <th>Total Nominal</th>
                    <th>Alasan Pembatalan</th>
                    <th class="text-right">Aksi Otorisasi</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($pendingVoids as $pv)
                    <tr>
                      <td>
                        <strong class="text-dark">{{ $pv->invoice_number }}</strong><br>
                        <small class="text-muted">{{ $pv->created_at->format('H:i') }} WIB</small>
                      </td>
                      <td>{{ $pv->user->name ?? 'Kasir Staf' }}</td>
                      <td><span class="font-weight-bold text-danger">Rp {{ number_format($pv->total_amount, 0, ',', '.') }}</span></td>
                      <td style="max-width: 250px;" class="small text-muted">{{ $pv->void_reason ?? 'Kesalahan input kasir' }}</td>
                      <td class="text-right">
                        <div class="d-flex justify-content-end gap-1">
                          <form action="{{ route('supervisor.void.approve', $pv->id) }}" method="POST" class="d-inline mr-1" data-confirm="Setujui pembatalan struk ini?">
                            @csrf
                            <button type="submit" class="btn btn-xs btn-success font-weight-bold" style="border-radius: 6px;">Setujui</button>
                          </form>
                          <form action="{{ route('supervisor.void.reject', $pv->id) }}" method="POST" class="d-inline" data-confirm="Tolak permohonan pembatalan ini?">
                            @csrf
                            <button type="submit" class="btn btn-xs btn-outline-danger font-weight-bold" style="border-radius: 6px;">Tolak</button>
                          </form>
                        </div>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @else
            <div class="text-center py-4 text-muted">
              <span class="icon-check-circle text-success d-block mb-2" style="font-size: 32px;"></span>
              <p class="mb-0 font-weight-bold">Semua transaksi kasir bersih!</p>
              <small>Tidak ada permohonan pembatalan struk yang menunggu persetujuan.</small>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>

</div>
@endsection
