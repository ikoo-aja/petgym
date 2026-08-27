@extends('layouts.member')

@section('title', 'Dashboard Member - PetGym')
@section('page_title', 'Dashboard Portal Member')
@section('page_subtitle', 'Ringkasan aktivitas keanggotaan, sewa loker, kuota PT, dan jadwal kelas kebugaran Anda.')
@section('member_tier_badge', 'Tier ' . strtoupper($member->membership_tier ?? 'Basic'))

@section('content')
<!-- Active Locker Banner -->
@if($activeRental)
<div class="card-custom border-left border-primary mb-4" style="border-left-width: 4px !important;">
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
    <div>
      <span class="badge badge-primary font-weight-bold text-uppercase mb-2">Akses Locker Digital Aktif</span>
      <h4 class="font-weight-bold text-dark mb-1">Nomor Loker: <span class="text-primary">#{{ $activeRental->locker ? $activeRental->locker->locker_number : '-' }}</span></h4>
      <p class="mb-0 text-muted small">Tunjukkan kode PIN digital ini pada loker IoT atau petugas resepsionis gym.</p>
    </div>
    <div class="mt-3 mt-md-0 text-md-right">
      <small class="text-muted text-uppercase font-weight-bold d-block mb-1">Kode Access PIN 6-Digit</small>
      <div class="d-inline-block bg-light text-dark font-weight-bold px-3 py-2 rounded border" style="font-size: 22px; letter-spacing: 4px;">
        {{ $activeRental->pin_code ?? '849201' }}
      </div>
      <div class="mt-1 text-muted small">Berlaku sampai: {{ \Carbon\Carbon::parse($activeRental->end_date)->format('d M Y') }}</div>
    </div>
  </div>
</div>
@endif

<!-- Key Metrics Grid -->
<div class="row mb-4">
  <div class="col-md-4 mb-3 mb-md-0">
    <div class="card-custom h-100">
      <div class="text-muted text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Status Keanggotaan</div>
      <h3 class="font-weight-bold text-dark mb-1 mt-2">Tier {{ strtoupper($member->membership_tier ?? 'Basic') }}</h3>
      <div class="d-flex align-items-center justify-content-between mt-2">
        <span class="badge badge-success font-weight-bold">Aktif</span>
        <span class="text-muted small">Sisa {{ $member->days_left }} Hari</span>
      </div>
    </div>
  </div>

  <div class="col-md-4 mb-3 mb-md-0">
    <div class="card-custom h-100">
      <div class="text-muted text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Sisa Kuota PT</div>
      <h3 class="font-weight-bold text-primary mb-1 mt-2">{{ $ptQuotas->sum('remaining_sessions') }} Sesi</h3>
      <div class="d-flex align-items-center justify-content-between mt-2">
        <span class="text-muted small">Dari {{ $ptQuotas->count() }} paket trainer</span>
        <a href="{{ route('member.pt') }}" class="btn btn-sm btn-outline-primary py-0 font-weight-bold">Beli Sesi</a>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card-custom h-100">
      <div class="text-muted text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Status Penalti No-Show</div>
      <h3 class="font-weight-bold mb-1 mt-2 {{ $member->is_penalty_blocked ? 'text-danger' : 'text-dark' }}">
        @if($member->is_penalty_blocked)
          Diblokir
        @else
          {{ $member->no_show_count ?? 0 }} / 3 Pelanggaran
        @endif
      </h3>
      <div class="mt-2">
        @if($member->is_penalty_blocked)
          <span class="badge badge-danger">Tangguh s/d {{ $member->penalty_blocked_until->format('d M Y') }}</span>
        @else
          <span class="badge badge-secondary">Akses Booking Aktif</span>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Quick Navigation Bar -->
<div class="card-custom mb-4">
  <h6 class="font-weight-bold text-dark mb-3">Navigasi Cepat Portal</h6>
  <div class="row text-center">
    <div class="col-6 col-md-3 mb-2 mb-md-0">
      <a href="{{ route('member.lockers') }}" class="btn btn-outline-primary btn-block py-3 font-weight-bold">
        Loker Saya
      </a>
    </div>
    <div class="col-6 col-md-3 mb-2 mb-md-0">
      <a href="{{ route('member.membership') }}" class="btn btn-outline-primary btn-block py-3 font-weight-bold">
        Keanggotaan
      </a>
    </div>
    <div class="col-6 col-md-3">
      <a href="{{ route('member.pt') }}" class="btn btn-outline-primary btn-block py-3 font-weight-bold">
        Personal Trainer
      </a>
    </div>
    <div class="col-6 col-md-3">
      <a href="{{ route('member.classes') }}" class="btn btn-outline-primary btn-block py-3 font-weight-bold">
        Kelas Kebugaran
      </a>
    </div>
  </div>
</div>

<div class="row">
  <!-- Upcoming PT Bookings -->
  <div class="col-md-6 mb-4">
    <div class="card-custom h-100">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="font-weight-bold text-dark mb-0">Jadwal PT Mendatang</h6>
        <a href="{{ route('member.pt') }}" class="btn btn-sm btn-link text-primary font-weight-bold p-0">Lihat Semua &rarr;</a>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
            <tr>
              <th>Trainer</th>
              <th>Tanggal</th>
              <th>Jam</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse($upcomingPtBookings as $ptb)
              <tr>
                <td class="font-weight-bold text-dark">{{ $ptb->trainer ? $ptb->trainer->name : '-' }}</td>
                <td class="text-muted">{{ \Carbon\Carbon::parse($ptb->booking_date)->format('d M Y') }}</td>
                <td class="text-primary font-weight-bold">{{ $ptb->booking_time }}</td>
                <td><span class="badge badge-success">Terjadwal</span></td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center py-4 text-muted small">Belum ada jadwal sesi PT terdaftar.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Upcoming Class RSVPs -->
  <div class="col-md-6 mb-4">
    <div class="card-custom h-100">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="font-weight-bold text-dark mb-0">Kelas Kebugaran Didaftarkan</h6>
        <a href="{{ route('member.classes') }}" class="btn btn-sm btn-link text-primary font-weight-bold p-0">Cari Kelas &rarr;</a>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
            <tr>
              <th>Kelas</th>
              <th>Tanggal</th>
              <th>Instruktur</th>
              <th>Status RSVP</th>
            </tr>
          </thead>
          <tbody>
            @forelse($upcomingClassRsvps as $cr)
              <tr>
                <td class="font-weight-bold text-dark">{{ $cr->gymClass ? $cr->gymClass->name : '-' }}</td>
                <td class="text-muted">{{ \Carbon\Carbon::parse($cr->class_date)->format('d M Y') }}</td>
                <td class="text-muted">{{ $cr->gymClass && $cr->gymClass->trainer ? $cr->gymClass->trainer->name : 'Staf' }}</td>
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
                <td colspan="4" class="text-center py-4 text-muted small">Belum ada kelas yang diikuti.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
