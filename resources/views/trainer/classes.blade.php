@extends('layouts.layout')

@section('title', 'Penugasan Kelas Mengajar &mdash; PetGym')
@section('page_title', 'Penugasan Kelas Mengajar')
@section('page_subtitle', 'Daftar jadwal dan alokasi kelas gym yang ditugaskan kepada Anda sebagai instruktur')

@section('content')
<div class="row">
  @forelse($myClasses as $c)
    @php
      $rsvpCount = $c->classRsvps ? $c->classRsvps->where('status', 'confirmed')->count() : 0;
      $percentage = $c->capacity > 0 ? min(100, round(($rsvpCount / $c->capacity) * 100)) : 0;
    @endphp
    <div class="col-lg-4 col-md-6 mb-4">
      <div class="card-custom h-100 d-flex flex-column justify-content-between p-4" style="border: 1px solid #e2e8f0; border-radius: 12px;">
        <div>
          <div class="d-flex justify-content-between align-items-start mb-3">
            <span class="badge badge-primary px-3 py-1 font-weight-bold" style="font-size: 11px; border-radius: 6px;">
              {{ $c->day }}
            </span>
            <span class="badge badge-light text-muted font-weight-bold" style="font-size: 11px; border-radius: 6px;">
              {{ $c->room ?? 'Studio Utama' }}
            </span>
          </div>

          <h5 class="font-weight-bold text-dark mb-1">{{ $c->name }}</h5>
          <div class="text-primary font-weight-bold mb-3" style="font-size: 14px;">
            Pukul {{ $c->time ?? '-' }} WIB
          </div>

          <div class="bg-light p-3 mb-3" style="border-radius: 8px;">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="text-muted" style="font-size: 12px;">Kapasitas Peserta</span>
              <span class="font-weight-bold text-dark" style="font-size: 12px;">{{ $rsvpCount }} / {{ $c->capacity }} Orang</span>
            </div>
            <div class="progress" style="height: 6px; border-radius: 4px;">
              <div class="progress-bar {{ $percentage >= 90 ? 'bg-danger' : ($percentage >= 60 ? 'bg-warning' : 'bg-primary') }}" 
                   role="progressbar" 
                   style="width: {{ $percentage }}%;" 
                   aria-valuenow="{{ $percentage }}" 
                   aria-valuemin="0" 
                   aria-valuemax="100">
              </div>
            </div>
          </div>
        </div>

        <div class="pt-2 border-top">
          <a href="{{ route('trainer.rsvps', ['gym_class_id' => $c->id]) }}" class="btn btn-sm btn-outline-primary btn-block font-weight-bold" style="border-radius: 8px;">
            Lihat Daftar Peserta RSVP
          </a>
        </div>
      </div>
    </div>
  @empty
    <div class="col-12">
      <div class="card-custom text-center py-5">
        <h6 class="font-weight-bold text-muted mb-1">Belum Ada Penugasan Kelas</h6>
        <p class="text-muted small mb-0">Anda belum memiliki jadwal kelas mengajar yang dialokasikan oleh pengelola gym.</p>
      </div>
    </div>
  @endforelse
</div>

<!-- Ringkasan Jadwal Mingguan -->
@if($myClasses->isNotEmpty())
<div class="card-custom mt-2">
  <div class="font-weight-bold text-dark mb-3" style="font-size: 15px;">Ringkasan Jadwal Mengajar Mingguan</div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
        <tr>
          <th>Hari</th>
          <th>Jam</th>
          <th>Nama Kelas</th>
          <th>Ruangan</th>
          <th>Kapasitas</th>
          <th>Peserta Aktif</th>
          <th class="text-right">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @foreach($myClasses as $c)
          <tr>
            <td class="font-weight-bold text-dark">{{ $c->day }}</td>
            <td class="font-weight-bold text-primary">{{ $c->time ?? '-' }} WIB</td>
            <td class="font-weight-bold">{{ $c->name }}</td>
            <td class="text-muted">{{ $c->room ?? 'Studio Utama' }}</td>
            <td>{{ $c->capacity }} Orang</td>
            <td>
              <span class="badge badge-info px-2 py-1">
                {{ $c->classRsvps ? $c->classRsvps->where('status', 'confirmed')->count() : 0 }} Peserta
              </span>
            </td>
            <td class="text-right">
              <a href="{{ route('trainer.rsvps', ['gym_class_id' => $c->id]) }}" class="btn btn-sm btn-primary font-weight-bold" style="border-radius: 6px;">
                Daftar RSVP
              </a>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endif
@endsection
