@extends('layouts.layout')

@section('title', 'Dashboard Trainer &mdash; PetGym')
@section('page_title', 'Dashboard Personal Trainer')
@section('page_subtitle', 'Jadwal pengajaran kelas kebugaran dan profil spesialisasi pelatih')

@section('content')
<div class="row">
  <!-- Left Side: Trainer Profile Summary -->
  <div class="col-md-4 mb-4">
    <div class="card-custom">
      <h6 class="font-weight-bold text-dark mb-3">Profil Personal Trainer</h6>
      <div class="p-3 bg-light rounded mb-3 text-center">
        <div class="font-weight-bold text-dark" style="font-size: 17px;">{{ $user->name }}</div>
        <small class="text-muted d-block mb-2">{{ $user->email }}</small>
        <span class="badge badge-success px-3 py-1 font-weight-bold">Status: Active Trainer</span>
      </div>

      <div style="font-size: 13px;">
        <div class="mb-2">
          <span class="text-muted d-block font-weight-bold" style="font-size: 11px; text-transform: uppercase;">Spesialisasi:</span>
          <span class="font-weight-bold text-dark">{{ $trainerProfile->specialization ?? 'Fitness & Conditioning' }}</span>
        </div>
        <div class="mb-2">
          <span class="text-muted d-block font-weight-bold" style="font-size: 11px; text-transform: uppercase;">Sertifikasi:</span>
          <span class="font-weight-bold text-primary">{{ $trainerProfile->certification ?? 'Certified Fitness Instructor' }}</span>
        </div>
        <div>
          <span class="text-muted d-block font-weight-bold" style="font-size: 11px; text-transform: uppercase;">Bio / Catatan:</span>
          <p class="text-muted mb-0" style="font-size: 12px;">{{ $trainerProfile->bio ?? 'Pelatih kebugaran terdedikasi untuk membantu member mencapai target kesehatan ideal.' }}</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Right Side: My Gym Classes Schedule -->
  <div class="col-md-8 mb-4">
    <div class="card-custom">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="font-weight-bold text-dark mb-0">Jadwal Kelas Senam / Mengajar Anda</h6>
        <span class="badge badge-info px-3 py-2 font-weight-bold" style="border-radius: 12px;">{{ count($myClasses) }} Jadwal Kelas</span>
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
            <tr>
              <th>Hari</th>
              <th>Nama Kelas</th>
              <th>Waktu Jam</th>
              <th>Kapasitas Peserta</th>
            </tr>
          </thead>
          <tbody>
            @forelse($myClasses as $c)
              <tr>
                <td><span class="badge badge-primary px-2 py-1">{{ $c->day }}</span></td>
                <td class="font-weight-bold text-dark">{{ $c->name }}</td>
                <td style="font-size: 13px;">{{ substr($c->start_time, 0, 5) }} - {{ substr($c->end_time, 0, 5) }} WIB</td>
                <td style="font-size: 13px;">{{ $c->max_capacity }} Maks Peserta</td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center py-4 text-muted">Belum ada penugasan kelas gym untuk Anda.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
