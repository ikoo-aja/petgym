@extends('layouts.admin')

@section('title', 'Dashboard Admin &mdash; PetGym')
@section('page_title', 'Dashboard Admin Gym')
@section('page_subtitle', 'Pantauan harian metriks operasional dan limit kuota SaaS')

@section('content')
<!-- Top Stat Cards (Daily Metrics) -->
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
      <div class="text-muted text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Pendapatan Hari Ini</div>
      <div class="d-flex align-items-baseline mt-2">
        <h3 class="font-weight-bold mb-0 text-success">Rp {{ number_format($revenueToday, 0, ',', '.') }}</h3>
      </div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card-custom">
      <div class="text-muted text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Member Aktif</div>
      <div class="d-flex align-items-baseline mt-2">
        <h2 class="font-weight-bold mb-0 text-primary">{{ $activeMembersCount }}</h2>
        <span class="ml-2 text-muted" style="font-size: 12px;">dari {{ $totalMembersCount }} total</span>
      </div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card-custom">
      <div class="text-muted text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Limit Kuota Member SaaS</div>
      <div class="mt-2">
        <div class="d-flex justify-content-between align-items-center mb-1">
          <span class="font-weight-bold text-dark" style="font-size: 13px;">{{ $totalMembersCount }} / {{ $maxMembersLimit ?? 'Unlimited' }}</span>
          <span class="badge badge-info" style="font-size: 10px;">{{ $usagePercent }}%</span>
        </div>
        <div class="progress" style="height: 8px; border-radius: 4px;">
          <div class="progress-bar {{ $usagePercent > 85 ? 'bg-danger' : 'bg-primary' }}" role="progressbar" style="width: {{ $usagePercent }}%;"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <!-- Left Column: Alert Expiring Members -->
  <div class="col-md-7">
    <div class="card-custom">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="font-weight-bold text-dark mb-0">Alert: Member Masa Aktif Habis (3 - 7 Hari)</h6>
        <span class="badge badge-warning text-dark font-weight-bold">{{ count($expiringMembers) }} Member</span>
      </div>

      @if(count($expiringMembers) > 0)
        <div class="table-responsive">
          <table class="table table-hover table-borderless align-middle mb-0">
            <thead class="text-muted bg-light" style="font-size: 11px; text-transform: uppercase;">
              <tr>
                <th>Nama Member</th>
                <th>Kode PIN</th>
                <th>Kontak</th>
                <th>Tgl Expired</th>
                <th>Sisa Hari</th>
              </tr>
            </thead>
            <tbody>
              @foreach($expiringMembers as $m)
                <tr>
                  <td class="font-weight-bold text-dark">{{ $m->name }}</td>
                  <td><span class="badge badge-secondary" style="font-size: 12px; letter-spacing: 1px;">{{ $m->access_code }}</span></td>
                  <td style="font-size: 13px;">{{ $m->phone ?? '-' }}</td>
                  <td style="font-size: 13px;">{{ $m->expired_at->format('d M Y') }}</td>
                  <td><span class="badge badge-warning" style="font-size: 11px;">{{ $m->days_left }} Hari</span></td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @else
        <p class="text-muted text-center py-3 mb-0" style="font-size: 13px;">Tidak ada member yang masa aktifnya akan habis dalam 7 hari ke depan.</p>
      @endif
    </div>
  </div>

  <!-- Right Column: Announcements Inbox from Superadmin -->
  <div class="col-md-5">
    <div class="card-custom">
      <h6 class="font-weight-bold text-dark mb-3">Pengumuman & Notifikasi Superadmin</h6>
      @if(count($announcements) > 0)
        @foreach($announcements as $anc)
          <div class="p-3 mb-3 border-left border-primary bg-light" style="border-radius: 6px;">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="font-weight-bold text-dark" style="font-size: 13.5px;">{{ $anc->title }}</span>
              <small class="text-muted">{{ $anc->created_at ? $anc->created_at->format('d M Y') : '' }}</small>
            </div>
            <div class="text-muted" style="font-size: 12.5px;">{!! $anc->message !!}</div>
          </div>
        @endforeach
      @else
        <p class="text-muted text-center py-3 mb-0" style="font-size: 13px;">Belum ada pengumuman baru dari Superadmin.</p>
      @endif
    </div>
  </div>
</div>
@endsection
