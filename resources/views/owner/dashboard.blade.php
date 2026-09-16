@extends('layouts.admin')

@section('title', 'Dashboard Pemilik (Owner) &mdash; PetGym')
@section('page_title', 'Dashboard Eksekutif Pemilik Gym')
@section('page_subtitle', 'Pusat pemantauan omset harian, statistik keanggotaan, okupansi loker, dan kesehatan bisnis gym Anda')

@section('content')
<!-- Key Performance Indicators (KPI Grid) -->
<div class="row">
  <div class="col-md-3">
    <div class="card-custom text-white" style="background: linear-gradient(135deg, #0ea5e9, #0284c7);">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <span class="text-white text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Omset</span>
        <span class="badge badge-light text-primary font-weight-bold">Harian</span>
      </div>
      <h3 class="font-weight-bold text-white mb-1">Rp {{ number_format($revenueToday, 0, ',', '.') }}</h3>
      <small class="text-white-50">Total transaksi Kasir</small>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card-custom text-white" style="background: linear-gradient(135deg, #10b981, #059669);">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <span class="text-white text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Omset Kasir</span>
        <span class="badge badge-light text-success font-weight-bold">Bulanan</span>
      </div>
      <h3 class="font-weight-bold text-white mb-1">Rp {{ number_format($revenueMonth, 0, ',', '.') }}</h3>
      <small class="text-white-50">Dari total {{ $totalTransactionsMonth }} transaksi kasir</small>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card-custom text-white" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <span class="text-white text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Kunjungan Pelanggan</span>
        <span class="badge badge-light text-purple font-weight-bold">Presensi</span>
      </div>
      <h3 class="font-weight-bold text-white mb-1">{{ $checkinsToday }} Pelanggan</h3>
      <small class="text-white-50">Check-in hari ini</small>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card-custom text-white" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <span class="text-white text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Total Member Aktif</span>
        <span class="badge badge-light text-warning font-weight-bold">{{ $expiringSoonMembers }} Kadaluarsa 7hr</span>
      </div>
      <h3 class="font-weight-bold text-white mb-1">{{ $activeMembers }} / {{ $totalMembers }}</h3>
      <small class="text-white-50">Member aktif dari total terdaftar</small>
    </div>
  </div>
</div>

<!-- Section 2: Pemantauan Loker & Presensi Kunjungan Terbaru -->
<div class="row">
  <!-- Status Loker Gym -->
  <div class="col-md-5">
    <div class="card-custom">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="font-weight-bold text-dark mb-0">Pemantauan Okupansi Loker Gym</h6>
        <a href="{{ route('owner.inventory') }}" class="btn btn-sm btn-link text-primary font-weight-bold">Lihat Semua Loker &rarr;</a>
      </div>

      <div class="row text-center my-3">
        <div class="col-4">
          <div class="p-3 bg-light rounded">
            <h4 class="font-weight-bold text-success mb-0">{{ $availableLockers }}</h4>
            <small class="text-muted d-block mt-1">Tersedia</small>
          </div>
        </div>
        <div class="col-4">
          <div class="p-3 bg-light rounded">
            <h4 class="font-weight-bold text-primary mb-0">{{ $occupiedLockers }}</h4>
            <small class="text-muted d-block mt-1">Terpakai</small>
          </div>
        </div>
        <div class="col-4">
          <div class="p-3 bg-light rounded">
            <h4 class="font-weight-bold text-danger mb-0">{{ $brokenLockers }}</h4>
            <small class="text-muted d-block mt-1">Rusak/Blokir</small>
          </div>
        </div>
      </div>

      <div class="progress mt-3" style="height: 12px; border-radius: 6px;">
        @php
          $occPercent = $totalLockers > 0 ? round(($occupiedLockers / $totalLockers) * 100) : 0;
          $brokPercent = $totalLockers > 0 ? round(($brokenLockers / $totalLockers) * 100) : 0;
        @endphp
        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $occPercent }}%" title="Terpakai {{ $occPercent }}%"></div>
        <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $brokPercent }}%" title="Rusak {{ $brokPercent }}%"></div>
      </div>
      <div class="d-flex justify-content-between mt-2" style="font-size: 12px;">
        <span class="text-muted">Tingkat Penggunaan: <strong>{{ $occPercent }}%</strong></span>
        <span class="text-muted">Total Kapasitas: <strong>{{ $totalLockers }} Loker</strong></span>
      </div>
    </div>
  </div>

  <!-- Presensi / Check-In Kunjungan Terbaru -->
  <div class="col-md-7">
    <div class="card-custom">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="font-weight-bold text-dark mb-0">Pemantauan Kunjungan Member Terbaru (Presensi Masked)</h6>
        <a href="{{ route('owner.members') }}" class="btn btn-sm btn-link text-primary font-weight-bold">Buka Data Member &rarr;</a>
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
            <tr>
              <th>Waktu Check-In</th>
              <th>Nama Member</th>
              <th>Metode</th>
            </tr>
          </thead>
          <tbody>
            @forelse($recentCheckins as $ci)
              <tr>
                <td style="font-size: 12.5px;" class="text-dark font-weight-bold">{{ $ci->checked_in_at ? \Carbon\Carbon::parse($ci->checked_in_at)->format('H:i:s WIB') : '-' }}</td>
                <td class="font-weight-bold text-dark">{{ $ci->member ? $ci->member->name : 'Non-Member' }}</td>
                <td>
                  <span class="badge badge-soft-info px-2 py-1" style="background: #e0f2fe; color: #0369a1;">
                    {{ $ci->check_in_method === 'code' ? 'PIN Kode' : 'Manual Resepsionis' }}
                  </span>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center py-4 text-muted">Belum ada kunjungan hari ini.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Section 3: Monitoring Kelas, Trainer & Audit Trail Staf -->
<div class="row">
  <!-- Jadwal Kelas & Trainer -->
  <div class="col-md-6">
    <div class="card-custom">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="font-weight-bold text-dark mb-0">Pemantauan Jadwal Kelas & Trainer</h6>
        <a href="{{ route('owner.classes') }}" class="btn btn-sm btn-link text-primary font-weight-bold">Buka Jadwal Kelas &rarr;</a>
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
            <tr>
              <th>Kelas</th>
              <th>Hari & Jam</th>
              <th>Ruangan</th>
              <th>Trainer</th>
            </tr>
          </thead>
          <tbody>
            @forelse($gymClasses as $gc)
              <tr>
                <td>
                  <div class="font-weight-bold text-dark">{{ $gc->name }}</div>
                  <small class="text-muted">Kuota: {{ $gc->max_capacity }} Peserta</small>
                </td>
                <td style="font-size: 12.5px;" class="text-dark">
                  <span class="badge badge-info px-2 py-1">{{ $gc->day }}</span>
                  <div class="mt-1">{{ substr($gc->start_time, 0, 5) }} - {{ substr($gc->end_time, 0, 5) }}</div>
                </td>
                <td style="font-size: 12.5px;" class="text-dark">{{ $gc->room ?? 'Belum Set' }}</td>
                <td style="font-size: 12.5px;" class="text-dark font-weight-bold">{{ $gc->trainer ? $gc->trainer->name : 'Tanpa Trainer' }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center py-3 text-muted">Belum ada jadwal kelas.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Audit Trail Aktivitas Staf -->
  <div class="col-md-6">
    <div class="card-custom">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="font-weight-bold text-dark mb-0">Audit Log Aktivitas Staf</h6>
        <a href="{{ route('owner.logs') }}" class="btn btn-sm btn-link text-primary font-weight-bold">Lihat Semua Log &rarr;</a>
      </div>

</div>
@endsection
