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
            <small class="text-muted font-weight-bold text-uppercase" style="font-size: 11px;">Permintaan Void Kasir</small>
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
            <span class="text-success small"><i class="icon-check-circle mr-1"></i> Tidak ada antrean void</span>
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
    <!-- Kolom Kiri: Otorisasi Void Kasir & Jadwal Shift Hari Ini -->
    <div class="col-lg-7 mb-4">
      
      <!-- Card Permintaan Void Kasir -->
      <div class="card-custom mb-4">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
          <div>
            <h6 class="font-weight-bold text-dark mb-0"><i class="icon-check-circle text-danger mr-1"></i> Antrean Otorisasi Void Kasir</h6>
            <small class="text-muted">Persetujuan pembatalan struk dari meja kasir resepsionis</small>
          </div>
          <a href="/supervisor/features?tab=void" class="btn btn-sm btn-outline-primary font-weight-bold">Lihat Semua</a>
        </div>
        <div class="p-3">
          @if($pendingVoids->count() > 0)
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
                  <tr>
                    <th>Invoice</th>
                    <th>Kasir</th>
                    <th>Total</th>
                    <th>Alasan</th>
                    <th>Aksi Otorisasi</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($pendingVoids->take(5) as $pv)
                    <tr>
                      <td>
                        <strong class="text-dark">{{ $pv->invoice_number }}</strong><br>
                        <small class="text-muted">{{ $pv->created_at->format('H:i') }} WIB</small>
                      </td>
                      <td>{{ $pv->user->name ?? 'Kasir Staf' }}</td>
                      <td><span class="font-weight-bold text-danger">Rp {{ number_format($pv->total_amount, 0, ',', '.') }}</span></td>
                      <td style="max-width: 150px;" class="small text-muted text-truncate">{{ $pv->void_reason ?? 'Kesalahan input kasir' }}</td>
                      <td>
                        <div class="d-flex gap-1">
                          <form action="{{ route('supervisor.void.approve', $pv->id) }}" method="POST" class="d-inline" data-confirm="Setujui pembatalan struk ini?">
                            @csrf
                            <button type="submit" class="btn btn-xs btn-success font-weight-bold mr-1">Setujui</button>
                          </form>
                          <form action="{{ route('supervisor.void.reject', $pv->id) }}" method="POST" class="d-inline" data-confirm="Tolak permohonan void ini?">
                            @csrf
                            <button type="submit" class="btn btn-xs btn-outline-danger font-weight-bold">Tolak</button>
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

      <!-- Card Shift Staf Hari Ini -->
      <div class="card-custom">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
          <div>
            <h6 class="font-weight-bold text-dark mb-0"><i class="icon-calendar text-primary mr-1"></i> Shift Staf Bertugas Hari Ini</h6>
            <small class="text-muted">Plotting jam kerja staf di lapangan</small>
          </div>
          <a href="/supervisor/features?tab=shift" class="btn btn-sm btn-outline-primary font-weight-bold">+ Atur Shift</a>
        </div>
        <div class="p-3">
          @if($todayShifts->count() > 0)
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
                  <tr>
                    <th>Nama Staf</th>
                    <th>Jabatan</th>
                    <th>Nama Shift</th>
                    <th>Jam Kerja</th>
                    <th>Catatan</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($todayShifts as $ts)
                    <tr>
                      <td class="font-weight-bold text-dark">{{ $ts->user->name ?? '-' }}</td>
                      <td>
                        @if(($ts->user->role ?? '') === 'receptionist')
                          <span class="badge badge-info">Resepsionis</span>
                        @elseif(($ts->user->role ?? '') === 'trainer')
                          <span class="badge badge-primary">Personal Trainer</span>
                        @else
                          <span class="badge badge-secondary">{{ ucfirst($ts->user->role ?? 'Staf') }}</span>
                        @endif
                      </td>
                      <td><span class="badge badge-secondary font-weight-bold">{{ $ts->shift_name }}</span></td>
                      <td class="font-weight-bold">{{ substr($ts->start_time, 0, 5) }} - {{ substr($ts->end_time, 0, 5) }}</td>
                      <td class="small text-muted">{{ $ts->notes ?? '-' }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @else
            <div class="text-center py-4 text-muted">
              <span class="icon-clock-o text-muted d-block mb-2" style="font-size: 32px;"></span>
              <p class="mb-0">Belum ada plotting shift staf untuk hari ini.</p>
              <a href="/supervisor/features?tab=shift" class="btn btn-sm btn-primary mt-2 font-weight-bold">Buat Jadwal Shift</a>
            </div>
          @endif
        </div>
      </div>

    </div>

    <!-- Kolom Kanan: Peringatan Stok & Tiket Komplain Member -->
    <div class="col-lg-5 mb-4">
      
      <!-- Card Peringatan Stok Ritel -->
      <div class="card-custom mb-4">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
          <div>
            <h6 class="font-weight-bold text-dark mb-0"><i class="icon-shopping-cart text-warning mr-1"></i> Peringatan Stok Ritel</h6>
            <small class="text-muted">Barang jualan kasir yang menipis</small>
          </div>
          <a href="/supervisor/features?tab=stock" class="btn btn-sm btn-outline-warning font-weight-bold">Kelola Stok</a>
        </div>
        <div class="p-3">
          @if($lowStockProducts->count() > 0)
            <div class="list-group list-group-flush">
              @foreach($lowStockProducts->take(4) as $lsp)
                <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                  <div>
                    <strong class="text-dark d-block" style="font-size: 13.5px;">{{ $lsp->name }}</strong>
                    <small class="text-muted">Kategori: {{ $lsp->category ?? 'Umum' }} &bull; Rp {{ number_format($lsp->price, 0, ',', '.') }}</small>
                  </div>
                  <div>
                    @if($lsp->stock == 0)
                      <span class="badge badge-danger px-2 py-1 font-weight-bold">Habis (0)</span>
                    @else
                      <span class="badge badge-warning text-dark px-2 py-1 font-weight-bold">Sisa {{ $lsp->stock }}</span>
                    @endif
                  </div>
                </div>
              @endforeach
            </div>
          @else
            <div class="text-center py-4 text-muted">
              <span class="icon-check-circle text-success d-block mb-2" style="font-size: 30px;"></span>
              <p class="mb-0 font-weight-bold">Ketersediaan Stok Aman</p>
              <small>Semua stok produk ritel berada di atas batas minimum.</small>
            </div>
          @endif
        </div>
      </div>

      <!-- Card Tiket Keluhan Member -->
      <div class="card-custom">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
          <div>
            <h6 class="font-weight-bold text-dark mb-0"><i class="icon-person text-danger mr-1"></i> Tiket Komplain Terbuka</h6>
            <small class="text-muted">Keluhan fasilitas dan layanan member</small>
          </div>
          <a href="/supervisor/features?tab=complaint" class="btn btn-sm btn-outline-primary font-weight-bold">Tindak Lanjuti</a>
        </div>
        <div class="p-3">
          @if($activeComplaints->count() > 0)
            <div class="list-group list-group-flush">
              @foreach($activeComplaints->take(4) as $ac)
                <div class="list-group-item px-0 py-2">
                  <div class="d-flex justify-content-between align-items-center mb-1">
                    <strong class="text-dark" style="font-size: 13px;">{{ $ac->title }}</strong>
                    @if($ac->status === 'open')
                      <span class="badge badge-danger">Menunggu</span>
                    @else
                      <span class="badge badge-warning text-dark">Diproses</span>
                    @endif
                  </div>
                  <p class="small text-muted mb-1 text-truncate">{{ $ac->description }}</p>
                  <small class="text-muted">Member: <strong>{{ $ac->member->name ?? 'Anonim' }}</strong> &bull; {{ $ac->created_at->diffForHumans() }}</small>
                </div>
              @endforeach
            </div>
          @else
            <div class="text-center py-4 text-muted">
              <span class="icon-smile-o text-success d-block mb-2" style="font-size: 32px;"></span>
              <p class="mb-0 font-weight-bold">Tidak ada keluhan aktif</p>
              <small>Semua keluhan member telah terselesaikan.</small>
            </div>
          @endif
        </div>
      </div>

    </div>
  </div>

</div>
@endsection
