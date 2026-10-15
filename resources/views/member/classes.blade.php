@extends('layouts.member')

@section('title', 'Kelas Kebugaran')
@section('page_title', 'Kelas Kebugaran')
@section('page_subtitle', 'Jadwal dan reservasi kelas latihan kebugaran')

@section('content')
<!-- No-Show Penalty Status Alert Card -->
@if($member->is_penalty_blocked)
<div class="alert alert-danger border-0 shadow-sm mb-3 mb-md-4 p-3 p-md-4" style="border-radius: 12px;">
  <div class="d-flex align-items-center">
    <div>
      <h5 class="font-weight-bold mb-1 text-danger">Akses Reservasi Kelas Ditangguhkan</h5>
      <p class="mb-0 text-muted small">
        Anda telah mencatat {{ $member->no_show_count }} kali pelanggaran ketidakhadiran (No-Show).
        Akses reservasi kelas Anda ditangguhkan hingga <strong>{{ $member->penalty_blocked_until->format('d M Y H:i') }} WIB</strong>.
      </p>
    </div>
  </div>
</div>
@else
<div class="card-custom mb-3 mb-md-4 p-3 border-left border-primary" style="border-left-width: 4px !important;">
  <div class="d-flex align-items-center mb-2">
    <h6 class="font-weight-bold text-dark mb-0" style="font-size: 14px;">Ketentuan Reservasi & Aturan Kehadiran</h6>
  </div>
  <div class="row" style="font-size: 12.5px;">
    <div class="col-12 col-md-4 mb-2 mb-md-0">
      <div class="p-3 bg-light rounded border h-100">
        <strong class="d-block text-dark mb-1">Batas Reservasi</strong>
        <span class="text-muted">Pendaftaran kelas dibuka maksimal <strong>H-2 sebelum tanggal kelas</strong>.</span>
      </div>
    </div>
    <div class="col-12 col-md-4 mb-2 mb-md-0">
      <div class="p-3 bg-light rounded border h-100">
        <strong class="d-block text-dark mb-1">Sistem Antrean</strong>
        <span class="text-muted">Jika kuota penuh, Anda otomatis masuk ke <strong>Antrean Tunggu (Waitlist)</strong>.</span>
      </div>
    </div>
    <div class="col-12 col-md-4">
      <div class="p-3 bg-light rounded border h-100">
        <strong class="d-block text-danger mb-1">Sanksi No-Show</strong>
        <span class="text-muted">Tidak hadir tanpa pembatalan &ge; 3x memicu penangguhan selama 7 hari.</span>
      </div>
    </div>
  </div>
</div>
@endif

<!-- Classes Catalog Grid -->
<h6 class="font-weight-bold text-dark mb-3">Jadwal & Kuota Kelas Gym</h6>
<div class="row mb-3 mb-md-4">
  @forelse($classes as $c)
    <div class="col-12 col-md-6 mb-3">
      <div class="card-custom h-100 d-flex flex-column">
        <div class="d-flex justify-content-between align-items-start mb-2">
          <div>
            <h5 class="font-weight-bold text-dark mb-1" style="font-size: 16px;">{{ $c->name }}</h5>
            <small class="text-muted">Trainer: <strong class="text-dark">{{ $c->trainer ? $c->trainer->name : 'Trainer Gym' }}</strong></small>
          </div>
          <span class="badge badge-info font-weight-bold px-2 py-1" style="font-size: 11px;">{{ $c->day }}</span>
        </div>

        <div class="p-3 bg-light rounded mb-3" style="font-size: 13px;">
          <div class="d-flex justify-content-between text-dark font-weight-bold mb-1">
            <span><span class="icon-clock-o mr-1"></span> {{ substr($c->start_time, 0, 5) }} - {{ substr($c->end_time, 0, 5) }} WIB</span>
            <span>{{ $c->room ?? 'Studio 1' }}</span>
          </div>
          <small class="text-muted">Kapasitas Maksimal: <strong>{{ $c->max_capacity }} Peserta</strong> per sesi</small>
        </div>

        <div class="mt-auto">
          @if($member->is_penalty_blocked)
            <button type="button" class="btn btn-secondary btn-block font-weight-bold py-2" disabled>Reservasi Ditangguhkan</button>
          @else
            <button type="button" class="btn btn-primary btn-block font-weight-bold py-2 btn-rsvp-class"
                    data-id="{{ $c->id }}"
                    data-name="{{ $c->name }}"
                    data-toggle="modal"
                    data-target="#rsvpModal">
              Reservasi Kelas
            </button>
          @endif
        </div>
      </div>
    </div>
  @empty
    <div class="col-12 text-center py-4 text-muted bg-light rounded border">
      Belum ada jadwal kelas yang tersedia saat ini.
    </div>
  @endforelse
</div>

<!-- My RSVPs & Waitlist Queue Cards -->
<div class="card-custom">
  <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
    <h6 class="font-weight-bold text-dark mb-0">Riwayat & Status Reservasi Kelas</h6>
    <small class="text-muted">{{ count($myRsvps) }} Terdaftar</small>
  </div>

  @forelse($myRsvps as $r)
    <div class="border rounded p-3 mb-2 bg-light d-flex flex-column flex-sm-row justify-content-between align-items-sm-center" style="border-radius: 10px !important;">
      <div class="d-flex align-items-center mb-2 mb-sm-0">
        <div class="rounded-circle bg-white text-primary p-2 mr-3 border d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px; font-weight: 800; font-size: 15px;">
          <span class="icon-calendar"></span>
        </div>
        <div>
          <h6 class="font-weight-bold text-dark mb-1" style="font-size: 13.5px;">{{ $r->gymClass ? $r->gymClass->name : 'Kelas Gym' }}</h6>
          <small class="text-muted">
            <span class="icon-calendar mr-1"></span> {{ \Carbon\Carbon::parse($r->class_date)->format('d M Y') }} &bull;
            Trainer: <strong class="text-dark">{{ $r->gymClass && $r->gymClass->trainer ? $r->gymClass->trainer->name : 'Staf' }}</strong>
          </small>
        </div>
      </div>
      <div class="d-flex align-items-center justify-content-between justify-content-sm-end" style="gap: 8px;">
        <div>
          @if($r->status == 'confirmed')
            <span class="badge badge-success px-2 py-1 font-weight-bold" style="font-size: 11px;">Dikonfirmasi</span>
          @elseif($r->status == 'waitlist')
            <span class="badge badge-warning px-2 py-1 font-weight-bold" style="font-size: 11px;">Antrean #{{ $r->queue_position }}</span>
          @elseif($r->status == 'attended')
            <span class="badge badge-primary px-2 py-1 font-weight-bold" style="font-size: 11px;">Hadir</span>
          @elseif($r->status == 'noshow')
            <span class="badge badge-danger px-2 py-1 font-weight-bold" style="font-size: 11px;">Tidak Hadir (No-Show)</span>
          @else
            <span class="badge badge-secondary px-2 py-1 font-weight-bold" style="font-size: 11px;">Dibatalkan</span>
          @endif
        </div>
        <div>
          @if(in_array($r->status, ['confirmed', 'waitlist']))
            <form action="{{ route('member.classes.cancel', $r->id) }}" method="POST" data-confirm="Batalkan reservasi kelas \"{{ $r->gymClass ? $r->gymClass->name : 'ini' }}\"? Slot Anda akan dilepas untuk member lain di daftar tunggu.">
              @csrf
              <button type="submit" class="btn btn-sm btn-outline-danger font-weight-bold py-1 px-2" style="border-radius: 6px; font-size: 11.5px;">Batal</button>
            </form>
          @endif
        </div>
      </div>
    </div>
  @empty
    <div class="text-center py-4 text-muted bg-light rounded border">
      <span class="icon-calendar d-block mb-1" style="font-size: 22px;"></span>
      <small class="d-block font-weight-semibold">Belum ada riwayat reservasi kelas.</small>
    </div>
  @endforelse
</div>

<!-- Modal RSVP Kelas -->
<div class="modal fade" id="rsvpModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-light">
        <h5 class="modal-title font-weight-bold text-dark mb-0" id="rsvpTitle">Reservasi Kelas</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">&times;</button>
      </div>
      <form action="{{ route('member.classes.rsvp') }}" method="POST">
        @csrf
        <input type="hidden" name="gym_class_id" id="rsvpClassId">
        <div class="modal-body p-4">
          <div class="form-group mb-3">
            <label class="font-weight-bold text-dark small">Pilih Tanggal Pelaksanaan Kelas</label>
            <input type="date" name="class_date" class="form-control" min="{{ date('Y-m-d') }}" max="{{ date('Y-m-d', strtotime('+7 days')) }}" value="{{ date('Y-m-d') }}" required>
          </div>
          <small class="text-muted d-block">Pendaftaran kelas dapat dilakukan hingga 7 hari ke depan. Jika kuota kelas penuh, Anda akan otomatis ditempatkan pada daftar antrean tunggu.</small>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary font-weight-bold">Konfirmasi Reservasi</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script>
  $(document).ready(function() {
    $('.btn-rsvp-class').on('click', function() {
      const id = $(this).data('id');
      const name = $(this).data('name');
      $('#rsvpClassId').val(id);
      $('#rsvpTitle').text('Reservasi Kelas - ' + name);
    });
  });
</script>
@endsection
