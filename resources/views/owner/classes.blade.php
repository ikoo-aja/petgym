@extends('layouts.admin')

@section('title', 'Pemantauan Kelas & Trainer (Owner) &mdash; PetGym')
@section('page_title', 'Pemantauan Jadwal Kelas & Personal Trainer')
@section('page_subtitle', 'Monitoring jadwal kelas kebugaran, alokasi instruktur, kuota ruangan, dan daftar Personal Trainer (Mode Pemantauan & Read-Only)')

@section('content')


<div class="row">
  <!-- Daftar Jadwal Kelas -->
  <div class="col-md-7">
    <div class="card-custom">
      <h6 class="font-weight-bold text-dark mb-3">📅 Jadwal Kelas Gym Terdaftar</h6>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
            <tr>
              <th>Kelas</th>
              <th>Hari & Jam</th>
              <th>Ruangan</th>
              <th>Instruktur / Trainer</th>
            </tr>
          </thead>
          <tbody>
            @forelse($classes as $c)
              <tr>
                <td>
                  <div class="font-weight-bold text-dark">{{ $c->name }}</div>
                  <small class="text-muted">Kuota: {{ $c->max_capacity }} Peserta</small>
                </td>
                <td style="font-size: 12.5px;">
                  <span class="badge badge-info px-2 py-1">{{ $c->day }}</span>
                  <div class="mt-1 text-dark">{{ substr($c->start_time, 0, 5) }} - {{ substr($c->end_time, 0, 5) }}</div>
                </td>
                <td style="font-size: 12.5px;" class="text-dark">{{ $c->room ?? 'Studio 1' }}</td>
                <td style="font-size: 12.5px;" class="font-weight-bold text-dark">
                  {{ $c->trainer ? $c->trainer->name : 'Tanpa Trainer' }}
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center py-4 text-muted">Belum ada jadwal kelas dari Manager.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Daftar Personal Trainer -->
  <div class="col-md-5">
    <div class="card-custom">
      <h6 class="font-weight-bold text-dark mb-3">🏋️ Tim Personal Trainer (PT)</h6>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
            <tr>
              <th>Nama Trainer</th>
              <th>Spesialisasi</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse($trainers as $t)
              <tr>
                <td class="font-weight-bold text-dark" style="font-size: 12.5px;">{{ $t->name }}</td>
                <td style="font-size: 12px;" class="text-muted">{{ $t->specialization ?? 'Fitness Coach' }}</td>
                <td>
                  <span class="badge badge-success px-2 py-1">Aktif</span>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="3" class="text-center py-4 text-muted">Belum ada data trainer terdaftar.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
