@extends('layouts.superadmin')

@section('title', 'Superadmin Dashboard &mdash; Workout')

@section('page_title', 'Dashboard Ringkasan')
@section('page_subtitle', 'Selamat datang kembali, ' . (Auth::user()->name ?? 'Superadmin') . '!')

@section('content')
<!-- 1. RINGKASAN STATISTIK BISNIS -->
<section id="dashboard" class="mb-5">
  @if(isset($pendingRegistrationsCount) && $pendingRegistrationsCount > 0)
  <div class="alert alert-warning border-0 shadow-sm p-3 mb-4 rounded-lg d-flex justify-content-between align-items-center" style="background-color: #fffbeb; color: #b45309; border-radius: 12px; border-left: 4px solid #f59e0b !important;">
    <div class="d-flex align-items-center">
      <span class="icon-user-plus h4 mb-0 mr-3 text-warning"></span>
      <div>
        <h6 class="font-weight-bold mb-0">Ada {{ $pendingRegistrationsCount }} Pendaftaran Calon Penyewa Baru Menunggu Tindak Lanjut!</h6>
        <small>Calon penyewa telah mengisi formulir dari landing page dan siap dihubungi via WhatsApp / Email.</small>
      </div>
    </div>
    <a href="{{ route('superadmin.registrations') }}" class="btn btn-warning btn-sm font-weight-bold px-3 text-dark shadow-sm" style="border-radius: 20px;">
      Buka Pendaftaran <span class="icon-arrow-right ml-1"></span>
    </a>
  </div>
  @endif

  <div class="row">
    <div class="col-md-3 mb-4">
      <div class="stat-card" style="border-left-color: #f59e0b;">
        <span class="text-muted font-weight-bold">Pendaftaran Masuk (Leads)</span>
        <div class="stat-number">{{ $pendingRegistrationsCount ?? 0 }} <small class="text-warning font-weight-bold" style="font-size: 13px;">Menunggu Konfirmasi</small></div>
        <small class="text-muted">Siap dihubungi & approve</small>
        <hr class="my-2">
        <a href="{{ route('superadmin.registrations') }}" class="text-warning d-inline-flex align-items-center font-weight-bold" style="font-size: 12px; text-decoration: none;">
          Kelola Prospek <span class="icon-keyboard_arrow_right ml-1"></span>
        </a>
      </div>
    </div>
    <div class="col-md-3 mb-4">
      <div class="stat-card">
        <span class="text-muted font-weight-bold">Total Gym Aktif</span>
        <div class="stat-number">{{ $activeTenantsCount }} <small class="text-success font-weight-normal" style="font-size: 14px;">/ {{ $totalTenantsCount }} Total</small></div>
        <small class="text-muted">{{ $suspendedTenantsCount }} Suspend / Expired</small>
        <hr class="my-2">
        <a href="{{ route('superadmin.tenants') }}" class="text-primary d-inline-flex align-items-center font-weight-bold" style="font-size: 12px; text-decoration: none;">
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
        <a href="{{ route('superadmin.billing') }}" class="text-success d-inline-flex align-items-center font-weight-bold" style="font-size: 12px; text-decoration: none;">
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
        <a href="{{ route('superadmin.tenants') }}" class="text-info d-inline-flex align-items-center font-weight-bold" style="font-size: 12px; text-decoration: none;">
          Lihat Detail <span class="icon-keyboard_arrow_right ml-1"></span>
        </a>
      </div>
    </div>
  </div>

  <!-- Grafik Perkembangan & Distribusi Paket -->
  <div class="row mt-2">
    <div class="col-lg-8 mb-4">
      <div class="bg-white p-4 rounded shadow-sm h-100 d-flex flex-column">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="font-weight-bold text-black mb-0">Pertumbuhan Penyewa & Pendapatan (6 Bulan Terakhir)</h5>
          <div class="small text-muted d-flex align-items-center" style="gap: 12px;">
            <span><span class="d-inline-block rounded-circle mr-1" style="width: 10px; height: 10px; background: #ee6e73;"></span> Tenant Baru</span>
            <span><span class="d-inline-block rounded-circle mr-1" style="width: 10px; height: 10px; background: #28a745;"></span> Pendapatan (Rp)</span>
          </div>
        </div>

        @php
          $maxTenants = max(1, collect($monthlyStats ?? [])->max('tenants') ?: 5);
          $maxIncome = max(1, collect($monthlyStats ?? [])->max('income') ?: 1000000);
        @endphp

        <!-- Visual Bar Chart 6 Bulan -->
        <div class="mt-auto pt-3">
          <div class="d-flex align-items-end justify-content-between" style="height: 180px; border-bottom: 2px solid #e2e8f0; padding-bottom: 5px;">
            @foreach($monthlyStats ?? [] as $st)
              @php
                $tHeight = max(8, round(($st['tenants'] / $maxTenants) * 150));
                $iHeight = max(8, round(($st['income'] / $maxIncome) * 150));
              @endphp
              <div class="text-center flex-fill d-flex flex-column align-items-center justify-content-end h-100" style="gap: 4px;">
                <div class="d-flex align-items-end justify-content-center w-100" style="gap: 6px; height: 150px;">
                  <!-- Bar Tenant -->
                  <div class="rounded-top shadow-sm" style="width: 18px; height: {{ $tHeight }}px; background: linear-gradient(180deg, #f38181, #ee6e73);" title="{{ $st['label'] }}: {{ $st['tenants'] }} Tenant"></div>
                  <!-- Bar Pendapatan -->
                  <div class="rounded-top shadow-sm" style="width: 18px; height: {{ $iHeight }}px; background: linear-gradient(180deg, #34d399, #10b981);" title="{{ $st['label'] }}: Rp {{ number_format($st['income'], 0, ',', '.') }}"></div>
                </div>
                <div class="font-weight-bold text-dark mt-2" style="font-size: 11px;">{{ $st['short'] }}</div>
              </div>
            @endforeach
          </div>

          <div class="d-flex justify-content-between text-muted mt-2 small" style="font-size: 11px;">
            <span>* Data dihitung berdasarkan tenant yang bergabung dan tagihan berstatus lunas.</span>
            <a href="{{ route('superadmin.billing') }}" class="font-weight-bold text-primary">Detail Tagihan &raquo;</a>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-4 mb-4">
      <div class="bg-white p-4 rounded shadow-sm h-100">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="font-weight-bold text-black mb-0">Distribusi Paket Sewa</h5>
          <span class="badge badge-light border text-muted px-2 py-1 font-weight-bold" style="font-size: 11px;">Aktif</span>
        </div>
        <ul class="list-group list-group-flush">
          @forelse($planDistribution ?? [] as $planName => $count)
            @php
              $badgeClass = 'badge-secondary';
              if (stripos($planName, 'Enterprise') !== false) $badgeClass = 'badge-primary';
              elseif (stripos($planName, 'Pro') !== false) $badgeClass = 'badge-info';
              elseif (stripos($planName, 'Basic') !== false) $badgeClass = 'badge-success';
              elseif (stripos($planName, 'Trial') !== false) $badgeClass = 'badge-warning';
            @endphp
            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
              <span class="font-weight-bold text-dark" style="font-size: 13px;">{{ $planName ?: 'Paket Custom' }}</span>
              <span class="badge {{ $badgeClass }} badge-pill px-3 py-1 font-weight-bold" style="font-size: 12px;">{{ $count }} Gym</span>
            </li>
          @empty
            <li class="list-group-item px-0 text-muted small py-3">Belum ada tenant sewa terdaftar.</li>
          @endforelse
        </ul>

        <hr class="my-3">
        <a href="{{ route('superadmin.plans') }}" class="btn btn-outline-primary btn-sm btn-block font-weight-bold">
          <span class="icon-layers mr-1"></span> Kelola Paket Sewa
        </a>
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
