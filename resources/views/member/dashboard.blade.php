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

<!-- Dashboard Shortcut Cards -->
<div class="card-custom mb-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="font-weight-bold text-dark mb-0">⚡ Shortcut Utama</h6>
    <small class="text-muted">Akses kilat ke fasilitas & jadwal kebugaran Anda</small>
  </div>
  <div class="row">
    <div class="col-md-4 mb-3 mb-md-0">
      <a href="{{ route('member.lockers') }}" class="card border text-decoration-none p-3 h-100 shadow-sm text-left" style="border-radius: 12px; transition: transform 0.2s ease;">
        <div class="d-flex align-items-center">
          <div class="rounded-circle p-3 mr-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(244, 63, 94, 0.08); color: var(--brand-red);">
            <span class="icon-settings" style="font-size: 22px;"></span>
          </div>
          <div>
            <h6 class="font-weight-bold text-dark mb-1" style="font-size: 15px;">Loker Saya</h6>
            <small class="text-muted">Sewa & akses PIN loker digital</small>
          </div>
        </div>
      </a>
    </div>

    <div class="col-md-4 mb-3 mb-md-0">
      <a href="{{ route('member.pt') }}" class="card border text-decoration-none p-3 h-100 shadow-sm text-left" style="border-radius: 12px; transition: transform 0.2s ease;">
        <div class="d-flex align-items-center">
          <div class="rounded-circle p-3 mr-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(244, 63, 94, 0.08); color: var(--brand-red);">
            <span class="icon-person" style="font-size: 22px;"></span>
          </div>
          <div>
            <h6 class="font-weight-bold text-dark mb-1" style="font-size: 15px;">Personal Trainer</h6>
            <small class="text-muted">Beli sesi & booking pelatih</small>
          </div>
        </div>
      </a>
    </div>

    <div class="col-md-4">
      <a href="{{ route('member.classes') }}" class="card border text-decoration-none p-3 h-100 shadow-sm text-left" style="border-radius: 12px; transition: transform 0.2s ease;">
        <div class="d-flex align-items-center">
          <div class="rounded-circle p-3 mr-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(244, 63, 94, 0.08); color: var(--brand-red);">
            <span class="icon-calendar" style="font-size: 22px;"></span>
          </div>
          <div>
            <h6 class="font-weight-bold text-dark mb-1" style="font-size: 15px;">Class Kebugaran</h6>
            <small class="text-muted">Jadwal & RSVP kelas harian</small>
          </div>
        </div>
      </a>
    </div>
  </div>
</div>

<div class="row">
  <!-- Upcoming PT Bookings Card List -->
  <div class="col-md-6 mb-4">
    <div class="card-custom h-100">
      <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
        <h6 class="font-weight-bold text-dark mb-0">🏋️ Jadwal PT Mendatang</h6>
        <a href="{{ route('member.pt') }}" class="btn btn-sm btn-link text-primary font-weight-bold p-0">Lihat Semua &rarr;</a>
      </div>

      @forelse($upcomingPtBookings as $ptb)
        <div class="border rounded p-3 mb-3 bg-light d-flex justify-content-between align-items-center" style="border-radius: 10px !important;">
          <div class="d-flex align-items-center">
            <div class="rounded-circle bg-white text-primary p-2 mr-3 border d-flex align-items-center justify-content-center shadow-sm" style="width: 42px; height: 42px; font-weight: 800; font-size: 16px;">
              {{ substr($ptb->trainer ? $ptb->trainer->name : 'P', 0, 1) }}
            </div>
            <div>
              <h6 class="font-weight-bold text-dark mb-1" style="font-size: 14px;">{{ $ptb->trainer ? $ptb->trainer->name : 'Personal Trainer' }}</h6>
              <small class="text-muted"><span class="icon-calendar mr-1"></span> {{ \Carbon\Carbon::parse($ptb->booking_date)->format('d M Y') }} &bull; <strong class="text-primary">{{ $ptb->booking_time }}</strong></small>
            </div>
          </div>
          <div class="ml-2">
            <span class="badge badge-success px-2 py-1 font-weight-bold" style="font-size: 11px;">Terjadwal</span>
          </div>
        </div>
      @empty
        <div class="text-center py-4 text-muted bg-light rounded border">
          <span class="icon-person d-block mb-1" style="font-size: 24px;"></span>
          <small class="d-block font-weight-semibold">Belum ada jadwal sesi PT terdaftar.</small>
        </div>
      @empty
      @endforelse
    </div>
  </div>

  <!-- Upcoming Class RSVPs Card List -->
  <div class="col-md-6 mb-4">
    <div class="card-custom h-100">
      <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
        <h6 class="font-weight-bold text-dark mb-0">🧘 Kelas Kebugaran Didaftarkan</h6>
        <a href="{{ route('member.classes') }}" class="btn btn-sm btn-link text-primary font-weight-bold p-0">Cari Kelas &rarr;</a>
      </div>

      @forelse($upcomingClassRsvps as $cr)
        <div class="border rounded p-3 mb-3 bg-light d-flex justify-content-between align-items-center" style="border-radius: 10px !important;">
          <div class="d-flex align-items-center">
            <div class="rounded-circle bg-white text-primary p-2 mr-3 border d-flex align-items-center justify-content-center shadow-sm" style="width: 42px; height: 42px; font-weight: 800; font-size: 16px;">
              <span class="icon-calendar"></span>
            </div>
            <div>
              <h6 class="font-weight-bold text-dark mb-1" style="font-size: 14px;">{{ $cr->gymClass ? $cr->gymClass->name : 'Kelas Gym' }}</h6>
              <small class="text-muted"><span class="icon-person mr-1"></span> {{ $cr->gymClass && $cr->gymClass->trainer ? $cr->gymClass->trainer->name : 'Instruktur Gym' }} &bull; {{ \Carbon\Carbon::parse($cr->class_date)->format('d M Y') }}</small>
            </div>
          </div>
          <div class="ml-2">
            @if($cr->status == 'confirmed')
              <span class="badge badge-success px-2 py-1 font-weight-bold" style="font-size: 11px;">Dikonfirmasi</span>
            @elseif($cr->status == 'waitlist')
              <span class="badge badge-warning px-2 py-1 font-weight-bold" style="font-size: 11px;">Waitlist #{{ $cr->queue_position }}</span>
            @else
              <span class="badge badge-secondary px-2 py-1 font-weight-bold" style="font-size: 11px;">{{ ucfirst($cr->status) }}</span>
            @endif
          </div>
        </div>
      @empty
        <div class="text-center py-4 text-muted bg-light rounded border">
          <span class="icon-calendar d-block mb-1" style="font-size: 24px;"></span>
          <small class="d-block font-weight-semibold">Belum ada kelas yang diikuti.</small>
        </div>
      @endforelse
    </div>
  </div>
</div>
@endsection
