@extends('layouts.superadmin')

@section('title', 'Superadmin Dashboard &mdash; Workout')

@section('page_title', 'Dashboard Ringkasan')
@section('page_subtitle', 'Selamat datang kembali, ' . (Auth::user()->name ?? 'Superadmin') . '!')

@section('content')
<!-- 1. RINGKASAN STATISTIK BISNIS -->
<section id="dashboard" class="mb-5">
  <div class="row">
    <div class="col-md-3 mb-4">
      <div class="stat-card">
        <span class="text-muted font-weight-bold">Total Gym Aktif</span>
        <div class="stat-number">{{ $activeTenantsCount }} <small class="text-success font-weight-normal" style="font-size: 14px;">/ {{ $totalTenantsCount }} Total</small></div>
        <small class="text-muted">{{ $suspendedTenantsCount }} Suspend / Expired</small>
        <hr class="my-2">
        <a href="{{ route('superadmin.tenants') }}" class="text-primary d-inline-flex align-items-center" style="font-size: 12px; font-weight: 700; text-decoration: none;">
          Lihat Semua Tenant <span class="icon-keyboard_arrow_right ml-1"></span>
        </a>
      </div>
    </div>
    <div class="col-md-3 mb-4">
      <div class="stat-card" style="border-left-color: #28a745;">
        <span class="text-muted font-weight-bold">Pendapatan Bulan Ini</span>
        <div class="stat-number">Rp {{ number_format($monthlyIncome, 0, ',', '.') }}</div>
        <small class="text-success">+12% dibanding bulan lalu</small>
        <hr class="my-2">
        <a href="{{ route('superadmin.billing') }}" class="text-success d-inline-flex align-items-center" style="font-size: 12px; font-weight: 700; text-decoration: none;">
          Lihat Keuangan <span class="icon-keyboard_arrow_right ml-1"></span>
        </a>
      </div>
    </div>
    <div class="col-md-3 mb-4">
      <div class="stat-card" style="border-left-color: #17a2b8;">
        <span class="text-muted font-weight-bold">Penyewa Baru (Bulan Ini)</span>
        <div class="stat-number">{{ $newTenantsCount }} Gym</div>
        <small class="text-muted">Target: 10 Tenant/Bulan</small>
        <hr class="my-2">
        <a href="{{ route('superadmin.tenants') }}" class="text-info d-inline-flex align-items-center" style="font-size: 12px; font-weight: 700; text-decoration: none;">
          Lihat Detail <span class="icon-keyboard_arrow_right ml-1"></span>
        </a>
      </div>
    </div>
    <div class="col-md-3 mb-4">
      <div class="stat-card" style="border-left-color: #ffc107;">
        <span class="text-muted font-weight-bold">Hampir Jatuh Tempo</span>
        <div class="stat-number">{{ $expiringTenantsCount }} Gym</div>
        <small class="text-danger">Akses berakhir dalam 7 hari</small>
        <hr class="my-2">
        <a href="{{ route('superadmin.tenants') }}?status=expiring" class="text-warning d-inline-flex align-items-center" style="font-size: 12px; font-weight: 700; text-decoration: none;">
          Lihat Detail <span class="icon-keyboard_arrow_right ml-1"></span>
        </a>
      </div>
    </div>
  </div>

  <!-- Grafik Perkembangan Dummy Visual Placeholder -->
  <div class="row mt-2">
    <div class="col-lg-8 mb-4">
      <div class="bg-white p-4 rounded shadow-sm">
        <h5 class="font-weight-bold mb-3 text-black">Grafik Pertumbuhan Penyewa & Estimasi Pendapatan</h5>
        <div style="height: 250px; background: #f1f3f5; border-radius: 4px;" class="d-flex align-items-center justify-content-center text-muted">
          [Visualisasi Graphic Chart Pertumbuhan Tenant & Income]
        </div>
      </div>
    </div>
    <div class="col-lg-4 mb-4">
      <div class="bg-white p-4 rounded shadow-sm">
        <h5 class="font-weight-bold mb-3 text-black">Distribusi Paket Sewa</h5>
        <ul class="list-group list-group-flush">
          <li class="list-group-item d-flex justify-content-between align-items-center px-0">
            Paket Enterprise
            <span class="badge badge-primary badge-pill">3 Gym</span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center px-0">
            Paket Pro
            <span class="badge badge-info badge-pill">4 Gym</span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center px-0">
            Paket Basic
            <span class="badge badge-secondary badge-pill">3 Gym</span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center px-0">
            Free Trial
            <span class="badge badge-warning badge-pill">0 Gym</span>
          </li>
        </ul>
      </div>
    </div>
  </div>

  <!-- Log Aktivitas Terkini (Recent Activities) -->
  <div class="row mt-2">
    <div class="col-lg-12">
      <div class="bg-white p-4 rounded shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="font-weight-bold text-black mb-0">Log Aktivitas Terkini</h5>
          <span class="badge badge-secondary px-3 py-1" style="font-size: 11px;">Real-time</span>
        </div>
        <div class="table-responsive">
          <table class="table table-hover table-sm">
            <thead>
              <tr>
                <th class="border-top-0 text-black py-2">Waktu</th>
                <th class="border-top-0 text-black py-2">Kategori</th>
                <th class="border-top-0 text-black py-2">Deskripsi Aktivitas</th>
                <th class="border-top-0 text-black py-2">Status</th>
              </tr>
            </thead>
            <tbody>
              @forelse($recentLogs as $log)
              <tr>
                <td class="py-2"><small class="text-muted">{{ $log->created_at->format('d M Y, H:i') }}</small></td>
                <td class="py-2">
                  @if(strpos(strtolower($log->action), 'pembayaran') !== false)
                    <span class="badge badge-success px-2 py-1" style="font-size: 10px;">Pembayaran</span>
                  @elseif(strpos(strtolower($log->action), 'suspend') !== false)
                    <span class="badge badge-danger px-2 py-1" style="font-size: 10px;">Sanksi</span>
                  @else
                    <span class="badge badge-info px-2 py-1" style="font-size: 10px;">{{ $log->action }}</span>
                  @endif
                </td>
                <td class="py-2">{{ $log->description }}</td>
                <td class="py-2 text-success" style="font-size: 13px; font-weight: bold;"><span class="icon-check"></span> Selesai</td>
              </tr>
              @empty
              <tr>
                <td colspan="4" class="text-center py-3 text-muted">Belum ada aktivitas tercatat.</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
