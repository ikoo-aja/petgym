@extends('layouts.layout')

@section('title', 'Beranda Pelatih &mdash; PetGym')
@section('page_title', 'Beranda Personal Trainer')
@section('page_subtitle', 'Ringkasan jadwal sesi latihan privat, penugasan kelas, dan absensi peserta')

@section('content')
<!-- Ringkasan Statistik Trainer -->
<div class="row mb-4">
  <div class="col-md-3 mb-3 mb-md-0">
    <div class="card-custom bg-white border-0 shadow-sm p-3 d-flex align-items-center" style="border-radius: 12px;">
      <div class="mr-3 d-flex align-items-center justify-content-center bg-primary text-white rounded-circle" style="width: 48px; height: 48px; font-size: 20px;">
        <span class="icon-person"></span>
      </div>
      <div>
        <small class="text-muted text-uppercase font-weight-bold" style="font-size: 11px;">Sesi PT Aktif</small>
        <h4 class="font-weight-bold text-dark mb-0">{{ $activePtCount }}</h4>
      </div>
    </div>
  </div>

  <div class="col-md-3 mb-3 mb-md-0">
    <div class="card-custom bg-white border-0 shadow-sm p-3 d-flex align-items-center" style="border-radius: 12px;">
      <div class="mr-3 d-flex align-items-center justify-content-center bg-success text-white rounded-circle" style="width: 48px; height: 48px; font-size: 20px;">
        <span class="icon-check"></span>
      </div>
      <div>
        <small class="text-muted text-uppercase font-weight-bold" style="font-size: 11px;">Sesi PT Selesai</small>
        <h4 class="font-weight-bold text-dark mb-0">{{ $completedPtCount }}</h4>
      </div>
    </div>
  </div>

  <div class="col-md-3 mb-3 mb-md-0">
    <div class="card-custom bg-white border-0 shadow-sm p-3 d-flex align-items-center" style="border-radius: 12px;">
      <div class="mr-3 d-flex align-items-center justify-content-center bg-info text-white rounded-circle" style="width: 48px; height: 48px; font-size: 20px;">
        <span class="icon-calendar"></span>
      </div>
      <div>
        <small class="text-muted text-uppercase font-weight-bold" style="font-size: 11px;">Kelas Mengajar</small>
        <h4 class="font-weight-bold text-dark mb-0">{{ count($myClasses) }}</h4>
      </div>
    </div>
  </div>

  <div class="col-md-3 mb-3 mb-md-0">
    <div class="card-custom bg-white border-0 shadow-sm p-3 d-flex align-items-center" style="border-radius: 12px;">
      <div class="mr-3 d-flex align-items-center justify-content-center bg-warning text-dark rounded-circle" style="width: 48px; height: 48px; font-size: 20px;">
        <span class="icon-people"></span>
      </div>
      <div>
        <small class="text-muted text-uppercase font-weight-bold" style="font-size: 11px;">Total RSVP Peserta</small>
        <h4 class="font-weight-bold text-dark mb-0">{{ count($myClassRsvps) }}</h4>
      </div>
    </div>
  </div>
</div>

<!-- Menu Navigasi Cepat -->
<div class="row mb-4">
  <div class="col-md-4 mb-3 mb-md-0">
    <a href="{{ route('trainer.pt-sessions') }}" class="card-custom d-flex flex-column justify-content-center align-items-center text-center text-decoration-none h-100 bg-primary text-white py-4" style="border-radius: 12px; transition: transform 0.2s ease;">
      <span class="icon-person mb-2" style="font-size: 28px;"></span>
      <h6 class="font-weight-bold text-white mb-1">Booking Sesi PT</h6>
      <small class="text-white-50">Kelola jadwal privat member</small>
    </a>
  </div>
  <div class="col-md-4 mb-3 mb-md-0">
    <a href="{{ route('trainer.classes') }}" class="card-custom d-flex flex-column justify-content-center align-items-center text-center text-decoration-none h-100 bg-info text-white py-4" style="border-radius: 12px; transition: transform 0.2s ease;">
      <span class="icon-calendar mb-2" style="font-size: 28px;"></span>
      <h6 class="font-weight-bold text-white mb-1">Penugasan Kelas</h6>
      <small class="text-white-50">Jadwal mengajar mingguan</small>
    </a>
  </div>
  <div class="col-md-4">
    <a href="{{ route('trainer.rsvps') }}" class="card-custom d-flex flex-column justify-content-center align-items-center text-center text-decoration-none h-100 bg-secondary text-white py-4" style="border-radius: 12px; transition: transform 0.2s ease;">
      <span class="icon-people mb-2" style="font-size: 28px;"></span>
      <h6 class="font-weight-bold text-white mb-1">Peserta RSVP Kelas</h6>
      <small class="text-white-50">Presensi & kehadiran kelas</small>
    </a>
  </div>
</div>

<!-- Agenda Hari Ini -->
<div class="row">
  <!-- Sesi Latihan PT Hari Ini -->
  <div class="col-md-6 mb-4">
    <div class="card-custom h-100">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
          <h6 class="font-weight-bold text-dark mb-0">Sesi Latihan PT Hari Ini</h6>
          <small class="text-muted">{{ \Carbon\Carbon::today()->format('d M Y') }}</small>
        </div>
        <a href="{{ route('trainer.pt-sessions') }}" class="btn btn-xs btn-outline-primary font-weight-bold" style="border-radius: 6px;">Lihat Semua</a>
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
            <tr>
              <th>Member</th>
              <th>Jam</th>
              <th>Status</th>
              <th class="text-right">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($todayPtBookings as $b)
              <tr>
                <td class="font-weight-bold text-dark">{{ $b->member ? $b->member->name : '-' }}</td>
                <td class="font-weight-bold text-primary" style="font-size: 13px;">{{ $b->booking_time }}</td>
                <td>
                  @if($b->status === 'scheduled')
                    <span class="badge badge-success px-2 py-1">Terjadwal</span>
                  @elseif($b->status === 'completed')
                    <span class="badge badge-secondary px-2 py-1">Selesai</span>
                  @else
                    <span class="badge badge-danger px-2 py-1">Batal</span>
                  @endif
                </td>
                <td class="text-right">
                  @if($b->status === 'scheduled')
                    <form action="{{ route('trainer.pt-sessions.status', $b->id) }}" method="POST" class="d-inline" data-confirm="Tandai sesi latihan PT bersama member ini telah selesai?">
                      @csrf
                      <input type="hidden" name="status" value="completed">
                      <button type="submit" class="btn btn-sm btn-outline-success font-weight-bold" style="border-radius: 6px;">Selesai</button>
                    </form>
                  @else
                    <span class="text-muted small">-</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center py-4 text-muted small">Tidak ada jadwal sesi latihan PT untuk hari ini.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Jadwal Kelas Mengajar Hari Ini -->
  <div class="col-md-6 mb-4">
    <div class="card-custom h-100">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
          <h6 class="font-weight-bold text-dark mb-0">Kelas Mengajar Hari Ini</h6>
          <small class="text-muted">Hari {{ \Carbon\Carbon::now()->translatedFormat('l') ?? 'Ini' }}</small>
        </div>
        <a href="{{ route('trainer.classes') }}" class="btn btn-xs btn-outline-info font-weight-bold" style="border-radius: 6px;">Lihat Jadwal Lengkap</a>
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
            <tr>
              <th>Kelas</th>
              <th>Jam</th>
              <th>Ruangan</th>
              <th>Peserta RSVP</th>
            </tr>
          </thead>
          <tbody>
            @forelse($todayClasses as $c)
              <tr>
                <td>
                  <div class="font-weight-bold text-dark">{{ $c->name }}</div>
                  <small class="text-muted">Kapasitas: {{ $c->capacity }} orang</small>
                </td>
                <td class="font-weight-bold text-dark" style="font-size: 13px;">{{ $c->time }}</td>
                <td style="font-size: 13px;">{{ $c->room ?? 'Studio Utama' }}</td>
                <td>
                  <span class="badge badge-info px-2 py-1">{{ count($c->classRsvps ?? []) }} Terdaftar</span>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center py-4 text-muted small">Tidak ada jadwal kelas mengajar untuk hari ini.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
