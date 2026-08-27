@extends('layouts.layout')

@section('title', 'Dashboard Trainer - PetGym')
@section('page_title', 'Dashboard Personal Trainer')
@section('page_subtitle', 'Jadwal booking sesi PT member dan daftar peserta kelas kebugaran Anda.')

@section('content')
<div class="row mb-4">
  <!-- Trainer Profile Summary Card -->
  <div class="col-md-4 mb-4 mb-md-0">
    <div class="card-custom">
      <h6 class="font-weight-bold text-dark mb-3">Profil Personal Trainer</h6>
      <div class="p-3 bg-light rounded mb-3 text-center">
        <div class="font-weight-bold text-dark" style="font-size: 17px;">{{ $user->name }}</div>
        <small class="text-muted d-block mb-2">{{ $user->email }}</small>
        <span class="badge badge-success px-3 py-1 font-weight-bold">Status: Trainer Aktif</span>
      </div>

      <div style="font-size: 13px;">
        <div class="mb-2">
          <span class="text-muted d-block font-weight-bold" style="font-size: 11px; text-transform: uppercase;">Spesialisasi:</span>
          <span class="font-weight-bold text-dark">{{ $trainerProfile->specialization ?? 'Fitness Coach' }}</span>
        </div>
        <div class="mb-2">
          <span class="text-muted d-block font-weight-bold" style="font-size: 11px; text-transform: uppercase;">Total Booking Sesi PT:</span>
          <span class="font-weight-bold text-primary">{{ count($myPtBookings) }} Sesi Terjadwal</span>
        </div>
        <div>
          <span class="text-muted d-block font-weight-bold" style="font-size: 11px; text-transform: uppercase;">Total RSVP Kelas:</span>
          <span class="font-weight-bold text-success">{{ count($myClassRsvps) }} Peserta Terdaftar</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Right Side: Scheduled PT Bookings from Members -->
  <div class="col-md-8 mb-4">
    <div class="card-custom h-100">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="font-weight-bold text-dark mb-0">Booking Sesi PT dari Member</h6>
        <span class="badge badge-primary font-weight-bold px-3 py-2">{{ count($myPtBookings) }} Sesi</span>
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
            <tr>
              <th>Member</th>
              <th>Kontak</th>
              <th>Tanggal Booking</th>
              <th>Jam Sesi</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse($myPtBookings as $b)
              <tr>
                <td class="font-weight-bold text-dark">{{ $b->member ? $b->member->name : '-' }}</td>
                <td style="font-size: 13px;">{{ $b->member ? $b->member->phone : '-' }}</td>
                <td style="font-size: 13px;" class="text-dark">{{ \Carbon\Carbon::parse($b->booking_date)->format('d M Y') }}</td>
                <td class="font-weight-bold text-primary">{{ $b->booking_time }}</td>
                <td>
                  @if($b->status == 'scheduled')
                    <span class="badge badge-success">Terjadwal</span>
                  @elseif($b->status == 'completed')
                    <span class="badge badge-secondary">Selesai</span>
                  @else
                    <span class="badge badge-danger">Batal</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center py-4 text-muted small">Belum ada booking sesi PT dari member.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <!-- My Gym Classes & Enrolled RSVPs -->
  <div class="col-md-6 mb-4">
    <div class="card-custom h-100">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="font-weight-bold text-dark mb-0">Penugasan Kelas Mengajar</h6>
        <span class="badge badge-info font-weight-bold">{{ count($myClasses) }} Kelas</span>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
            <tr>
              <th>Hari</th>
              <th>Nama Kelas</th>
              <th>Jam</th>
              <th>Kapasitas</th>
            </tr>
          </thead>
          <tbody>
            @forelse($myClasses as $c)
              <tr>
                <td><span class="badge badge-primary">{{ $c->day }}</span></td>
                <td class="font-weight-bold text-dark">{{ $c->name }}</td>
                <td style="font-size: 12.5px;">{{ substr($c->start_time, 0, 5) }} - {{ substr($c->end_time, 0, 5) }} WIB</td>
                <td style="font-size: 12.5px;">{{ $c->max_capacity }} Maks</td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center py-4 text-muted small">Belum ada kelas gym ditugaskan.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Member RSVPs List for My Classes -->
  <div class="col-md-6 mb-4">
    <div class="card-custom h-100">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="font-weight-bold text-dark mb-0">Daftar Peserta RSVP Kelas</h6>
        <span class="badge badge-success font-weight-bold">{{ count($myClassRsvps) }} Peserta</span>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
            <tr>
              <th>Member</th>
              <th>Kelas</th>
              <th>Tanggal</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse($myClassRsvps as $cr)
              <tr>
                <td class="font-weight-bold text-dark">{{ $cr->member ? $cr->member->name : '-' }}</td>
                <td class="text-dark">{{ $cr->gymClass ? $cr->gymClass->name : '-' }}</td>
                <td style="font-size: 12.5px;">{{ \Carbon\Carbon::parse($cr->class_date)->format('d M Y') }}</td>
                <td>
                  @if($cr->status == 'confirmed')
                    <span class="badge badge-success">Dikonfirmasi</span>
                  @elseif($cr->status == 'waitlist')
                    <span class="badge badge-warning">Waitlist #{{ $cr->queue_position }}</span>
                  @else
                    <span class="badge badge-secondary">{{ ucfirst($cr->status) }}</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center py-4 text-muted small">Belum ada peserta terdaftar di kelas Anda.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
