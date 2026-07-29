@extends('layouts.admin')

@section('title', 'Dashboard Manager &mdash; PetGym')
@section('page_title', 'Dashboard Manager Gym')
@section('page_subtitle', 'Pengawasan operasional harian, pendapatan bulanan, dan log aktivitas staf')

@section('content')
<div class="row">
  <div class="col-md-3">
    <div class="card-custom">
      <div class="text-muted text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Check-In Hari Ini</div>
      <div class="d-flex align-items-baseline mt-2">
        <h2 class="font-weight-bold mb-0 text-dark">{{ $checkinsToday }}</h2>
        <span class="ml-2 text-muted" style="font-size: 12px;">kunjungan</span>
      </div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card-custom">
      <div class="text-muted text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Omset Kasir Hari Ini</div>
      <div class="d-flex align-items-baseline mt-2">
        <h3 class="font-weight-bold mb-0 text-success">Rp {{ number_format($revenueToday, 0, ',', '.') }}</h3>
      </div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card-custom">
      <div class="text-muted text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Total Omset Bulan Ini</div>
      <div class="d-flex align-items-baseline mt-2">
        <h3 class="font-weight-bold mb-0 text-primary">Rp {{ number_format($revenueMonth, 0, ',', '.') }}</h3>
      </div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card-custom">
      <div class="text-muted text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Total Member Terdaftar</div>
      <div class="d-flex align-items-baseline mt-2">
        <h2 class="font-weight-bold mb-0 text-info">{{ $totalMembers }}</h2>
        <span class="ml-2 text-muted" style="font-size: 12px;">member</span>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <!-- Profile & Manager Info -->
  <div class="col-md-4">
    <div class="card-custom">
      <h6 class="font-weight-bold text-dark mb-3">Profil Manager</h6>
      <div class="p-3 bg-light rounded mb-3">
        <div class="font-weight-bold text-dark" style="font-size: 15px;">{{ $user->name }}</div>
        <small class="text-muted d-block">{{ $user->email }}</small>
        <span class="badge badge-warning text-dark mt-2 font-weight-bold px-3 py-1">Role: Manager Gym</span>
      </div>

      <div style="font-size: 13px;">
        <div class="d-flex justify-content-between mb-2">
          <span class="text-muted">Departemen:</span>
          <span class="font-weight-bold text-dark">{{ $managerProfile->department ?? 'Operasional' }}</span>
        </div>
        <div class="d-flex justify-content-between">
          <span class="text-muted">Status Akun:</span>
          <span class="badge badge-success">Aktif</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Recent Audit Logs -->
  <div class="col-md-8">
    <div class="card-custom">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="font-weight-bold text-dark mb-0">Log Aktivitas Staf Terbaru (Audit Trail)</h6>
        <a href="{{ route('admin.logs.index') }}" class="btn btn-sm btn-link text-primary font-weight-bold">Lihat Semua Log &rarr;</a>
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
            <tr>
              <th>Waktu</th>
              <th>Staf</th>
              <th>Aksi</th>
              <th>Deskripsi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($recentStaffLogs as $log)
              <tr>
                <td style="font-size: 12px;" class="text-muted">{{ $log->created_at ? $log->created_at->format('d M H:i') : '-' }}</td>
                <td class="font-weight-bold text-dark" style="font-size: 13px;">{{ $log->user ? $log->user->name : 'System' }}</td>
                <td><span class="badge badge-info py-1">{{ $log->action }}</span></td>
                <td style="font-size: 12.5px;">{{ $log->description }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center py-4 text-muted">Belum ada aktivitas log tercatat.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
