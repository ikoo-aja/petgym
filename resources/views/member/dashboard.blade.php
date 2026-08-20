@extends('layouts.admin')

@section('title', 'Dashboard Member &mdash; PetGym')
@section('page_title', 'Dashboard Portal Keanggotaan Member')
@section('page_subtitle', 'Selamat datang di area member gym. Kelola loker digital, kuota sesi PT, jadwal kelas, dan paket keanggotaan Anda.')

@section('content')
<!-- Active Access PIN & Locker Proof Banner -->
@if($activeRental)
<div class="card border-0 shadow-sm mb-4" style="border-radius: 15px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #fff;">
  <div class="card-body p-4">
    <div class="row align-items-center">
      <div class="col-md-8">
        <span class="badge badge-light text-primary font-weight-bold uppercase mb-2" style="letter-spacing: 1px;">🔐 AKSES LOKER DIGITAL AKTIF</span>
        <h2 class="font-weight-bold mb-1 text-white">Nomor Loker: <span class="text-warning">#{{ $activeRental->locker ? $activeRental->locker->locker_number : '-' }}</span></h2>
        <p class="mb-0 text-white-50">Tunjukkan bukti digital ini kepada resepsionis gym atau gunakan PIN Digital di bawah pada loker IoT.</p>
      </div>
      <div class="col-md-4 text-md-right mt-3 mt-md-0">
        <small class="text-white-50 uppercase font-weight-bold d-block mb-1">ACCESS PIN 6-DIGIT</small>
        <div class="d-inline-block bg-white text-dark font-weight-bold px-3 py-2 rounded shadow-sm" style="font-size: 24px; letter-spacing: 4px;">
          {{ $activeRental->pin_code ?? '849201' }}
        </div>
        <div class="mt-2 text-white-50 small">Berlaku s/d: {{ \Carbon\Carbon::parse($activeRental->end_date)->format('d M Y') }}</div>
      </div>
    </div>
  </div>
</div>
@endif

<!-- Key Stats Grid -->
<div class="row mb-4">
  <div class="col-md-4">
    <div class="card-custom text-white" style="background: linear-gradient(135deg, #0ea5e9, #0284c7);">
      <small class="text-white-50 text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Status Keanggotaan</small>
      <h3 class="font-weight-bold text-white mb-0 mt-1">Tier {{ strtoupper($member->membership_tier ?? 'Basic') }}</h3>
      <small class="text-white-50">Masa berlaku: {{ $member->days_left }} hari lagi ({{ $member->expired_at ? $member->expired_at->format('d M Y') : '-' }})</small>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card-custom text-white" style="background: linear-gradient(135deg, #10b981, #059669);">
      <small class="text-white-50 text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Sisa Sesi PT Aktif</small>
      <h3 class="font-weight-bold text-white mb-0 mt-1">{{ $ptQuotas->sum('remaining_sessions') }} Sesi</h3>
      <small class="text-white-50">Tersedia dari {{ $ptQuotas->count() }} paket trainer</small>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card-custom text-white" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
      <small class="text-white-50 text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Status Penalti No-Show</small>
      <h3 class="font-weight-bold text-white mb-0 mt-1">
        @if($member->is_penalty_blocked)
          DIBLOKIR 🚫
        @else
          {{ $member->no_show_count ?? 0 }} / 3 Pelanggaran
        @endif
      </h3>
      <small class="text-white-50">
        @if($member->is_penalty_blocked)
          Ditangguhkan s/d {{ $member->penalty_blocked_until->format('d M Y') }}
        @else
          Akses booking kelas aktif
        @endif
      </small>
    </div>
  </div>
</div>

<div class="row">
  <!-- Upcoming PT Bookings -->
  <div class="col-md-6 mb-4">
    <div class="card-custom h-100">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="font-weight-bold text-dark mb-0">🏋️ Jadwal Sesi PT Mendatang</h6>
        <a href="{{ route('member.pt') }}" class="btn btn-sm btn-link text-primary font-weight-bold">Booking PT &rarr;</a>
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
                <td class="font-weight-bold text-dark" style="font-size: 12.5px;">{{ $ptb->trainer ? $ptb->trainer->name : '-' }}</td>
                <td style="font-size: 12px;" class="text-dark">{{ \Carbon\Carbon::parse($ptb->booking_date)->format('d M Y') }}</td>
                <td style="font-size: 12px;" class="text-primary font-weight-bold">{{ $ptb->booking_time }}</td>
                <td><span class="badge badge-success px-2 py-1">Tersedang</span></td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center py-4 text-muted">Belum ada jadwal sesi PT terdaftar.</td>
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
        <h6 class="font-weight-bold text-dark mb-0">📅 Kelas Kebugaran Didaftarkan</h6>
        <a href="{{ route('member.classes') }}" class="btn btn-sm btn-link text-primary font-weight-bold">Cari Kelas &rarr;</a>
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
                <td class="font-weight-bold text-dark" style="font-size: 12.5px;">{{ $cr->gymClass ? $cr->gymClass->name : '-' }}</td>
                <td style="font-size: 12px;" class="text-dark">{{ \Carbon\Carbon::parse($cr->class_date)->format('d M Y') }}</td>
                <td style="font-size: 12px;" class="text-muted">{{ $cr->gymClass && $cr->gymClass->trainer ? $cr->gymClass->trainer->name : 'Staff' }}</td>
                <td>
                  @if($cr->status == 'confirmed')
                    <span class="badge badge-success px-2 py-1">Dikonfirmasi</span>
                  @elseif($cr->status == 'waitlist')
                    <span class="badge badge-warning px-2 py-1">Waitlist #{{ $cr->queue_position }}</span>
                  @else
                    <span class="badge badge-secondary px-2 py-1">{{ ucfirst($cr->status) }}</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center py-4 text-muted">Belum ada kelas yang Anda ikuti.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
