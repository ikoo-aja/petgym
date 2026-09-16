@extends('layouts.member')

@section('title', 'Kelas Kebugaran - PetGym')
@section('page_title', 'Kelas Kebugaran')
@section('page_subtitle', '')
@section('member_tier_badge', 'Tier ' . strtoupper($member->membership_tier ?? 'Basic'))

@section('content')
<!-- No-Show Penalty Status Alert Card -->
@if($member->is_penalty_blocked)
<div class="alert alert-danger border-0 shadow-sm mb-4 p-4" style="border-radius: 12px;">
  <div class="d-flex align-items-center">
    <div>
      <h5 class="font-weight-bold mb-1 text-danger">Akses Pendaftaran Kelas Diblokir Sementara</h5>
      <p class="mb-0 text-muted small">
        Anda telah mengumpulkan {{ $member->no_show_count }} kali pelanggaran No-Show.
        Akses pendaftaran kelas Anda ditangguhkan hingga <strong>{{ $member->penalty_blocked_until->format('d M Y H:i') }} WIB</strong>.
      </p>
    </div>
  </div>
</div>
@else
<div class="card-custom mb-4 p-3 border-left border-primary" style="border-left-width: 4px !important; background: #ffffff;">
  <div class="d-flex align-items-center mb-2">
    <h6 class="font-weight-bold text-dark mb-0" style="font-size: 14px;">Aturan Pendaftaran Kelas & Ketentuan No-Show</h6>
  </div>
  <div class="row" style="font-size: 12.5px;">
    <div class="col-md-4 mb-2 mb-md-0">
      <div class="p-3 bg-light rounded border h-100">
        <strong class="d-block text-dark mb-1">Batas Pendaftaran</strong>
        <span class="text-muted">Pendaftaran kelas dibuka maksimal <strong>H-2 sebelum tanggal kelas</strong>.</span>
      </div>
    </div>
    <div class="col-md-4 mb-2 mb-md-0">
      <div class="p-3 bg-light rounded border h-100">
        <strong class="d-block text-dark mb-1">Antrean</strong>
        <span class="text-muted">Jika kuota penuh, otomatis masuk ke <strong>Sistem Antrean Waitlist</strong>.</span>
      </div>
    </div>
    <div class="col-md-4">
      <div class="p-3 bg-light rounded border h-100">
        <strong class="d-block text-danger mb-1">Penalti</strong>
        <span class="text-muted">Tidak hadir tanpa pembatalan &ge; 3x memicu pemblokiran selama 7 hari.</span>
      </div>
    </div>
  </div>
</div>
@endif

<!-- Classes Catalog Grid -->
<h6 class="font-weight-bold text-dark mb-3">Jadwal & Kuota Kelas Kebugaran Gym</h6>
<div class="row mb-4">
  @forelse($classes as $c)
    <div class="col-md-6 mb-4">
      <div class="card-custom h-100 d-flex flex-column">
        <div class="d-flex justify-content-between align-items-start mb-3">
          <div>
            <h5 class="font-weight-bold text-dark mb-1">{{ $c->name }}</h5>
            <small class="text-muted">Instruktur: <strong class="text-dark">{{ $c->trainer ? $c->trainer->name : 'Staf Instruktur' }}</strong></small>
          </div>
          <span class="badge badge-info font-weight-bold">{{ $c->day }}</span>
        </div>

        <div class="p-3 bg-light rounded mb-3" style="font-size: 13px;">
          <div class="d-flex justify-content-between text-dark font-weight-bold mb-1">
            <span>Jam: {{ substr($c->start_time, 0, 5) }} - {{ substr($c->end_time, 0, 5) }} WIB</span>
            <span>Ruangan: {{ $c->room ?? 'Studio 1' }}</span>
          </div>
          <small class="text-muted">Kapasitas Maksimal: <strong>{{ $c->max_capacity }} Peserta</strong> per sesi</small>
        </div>

        <div class="mt-auto">
          @if($member->is_penalty_blocked)
            <button type="button" class="btn btn-secondary btn-block font-weight-bold py-2" disabled>Booking Diblokir (Penalti)</button>
          @else
            <button type="button" class="btn btn-primary btn-block font-weight-bold py-2 btn-rsvp-class"
                    data-id="{{ $c->id }}"
                    data-name="{{ $c->name }}"
                    data-toggle="modal"
                    data-target="#rsvpModal">
              RSVP Ikuti Kelas
            </button>
          @endif
        </div>
      </div>
    </div>
  @empty
    <div class="col-12 text-center py-4 text-muted">
      Belum ada jadwal kelas dari Manager.
    </div>
  @endforelse
</div>

<!-- My RSVPs & Waitlist Queue Cards -->
<div class="card-custom">
  <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
    <h6 class="font-weight-bold text-dark mb-0">Riwayat & Status Antrean</h6>
    <small class="text-muted">{{ count($myRsvps) }} Pendaftaran</small>
  </div>

  @forelse($myRsvps as $r)
    <div class="border rounded p-3 mb-3 bg-light d-flex flex-column flex-sm-row justify-content-between align-items-sm-center" style="border-radius: 12px !important;">
      <div class="d-flex align-items-center mb-2 mb-sm-0">
        <div class="rounded-circle bg-white text-primary p-2 mr-3 border d-flex align-items-center justify-content-center shadow-sm" style="width: 44px; height: 44px; font-weight: 800; font-size: 16px;">
          <span class="icon-calendar"></span>
        </div>
        <div>
          <h6 class="font-weight-bold text-dark mb-1" style="font-size: 14px;">{{ $r->gymClass ? $r->gymClass->name : 'Kelas Gym' }}</h6>
          <small class="text-muted">
            <span class="icon-calendar mr-1"></span> {{ \Carbon\Carbon::parse($r->class_date)->format('d M Y') }} &bull;
            Instruktur: <strong class="text-dark">{{ $r->gymClass && $r->gymClass->trainer ? $r->gymClass->trainer->name : 'Staf' }}</strong>
          </small>
        </div>
      </div>
      <div class="d-flex align-items-center justify-content-between justify-content-sm-end gap-2">
        <div>
          @if($r->status == 'confirmed')
            <span class="badge badge-success px-2 py-1 font-weight-bold" style="font-size: 11px;">Dikonfirmasi</span>
          @elseif($r->status == 'waitlist')
            <span class="badge badge-warning px-2 py-1 font-weight-bold" style="font-size: 11px;">Waitlist Posisi #{{ $r->queue_position }}</span>
          @elseif($r->status == 'attended')
            <span class="badge badge-primary px-2 py-1 font-weight-bold" style="font-size: 11px;">Hadir</span>
          @elseif($r->status == 'noshow')
            <span class="badge badge-danger px-2 py-1 font-weight-bold" style="font-size: 11px;">No-Show</span>
          @else
            <span class="badge badge-secondary px-2 py-1 font-weight-bold" style="font-size: 11px;">Batal</span>
          @endif
        </div>
        <div class="ml-2">
          @if(in_array($r->status, ['confirmed', 'waitlist']))
            <form action="{{ route('member.classes.cancel', $r->id) }}" method="POST" data-confirm="Batalkan RSVP kelas ini?">
              @csrf
              <button type="submit" class="btn btn-sm btn-outline-danger font-weight-bold py-1 px-2" style="border-radius: 6px; font-size: 11.5px;">Batal RSVP</button>
            </form>
          @endif
        </div>
      </div>
    </div>
  @empty
    <div class="text-center py-4 text-muted bg-light rounded border">
      <span class="icon-calendar d-block mb-1" style="font-size: 24px;"></span>
      <small class="d-block font-weight-semibold">Belum ada riwayat RSVP kelas.</small>
    </div>
  @endforelse
</div>

<!-- Modal RSVP Kelas -->
<div class="modal fade" id="rsvpModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-light">
        <h5 class="modal-title font-weight-bold text-dark mb-0" id="rsvpTitle">RSVP Pendaftaran Kelas</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
      </div>
      <form action="{{ route('member.classes.rsvp') }}" method="POST">
        @csrf
        <input type="hidden" name="gym_class_id" id="rsvpClassId">
        <div class="modal-body p-4">
          <div class="form-group mb-3">
            <label class="font-weight-bold text-dark small">Pilih Tanggal Kelas</label>
            <input type="date" name="class_date" class="form-control" min="{{ date('Y-m-d') }}" max="{{ date('Y-m-d', strtotime('+7 days')) }}" value="{{ date('Y-m-d') }}" required>
          </div>
          <small class="text-muted d-block">Pendaftaran kelas dapat dilakukan hingga 7 hari ke depan. Jika kuota kelas penuh, Anda otomatis ditempatkan pada Antrean Waitlist.</small>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary font-weight-bold">Konfirmasi RSVP Kelas</button>
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
      $('#rsvpTitle').text('RSVP Pendaftaran Kelas - ' + name);
    });
  });
</script>
@endsection
