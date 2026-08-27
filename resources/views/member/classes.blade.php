@extends('layouts.member')

@section('title', 'Kelas Kebugaran - PetGym')
@section('page_title', 'Kelas Kebugaran & RSVP Waitlist')
@section('page_subtitle', 'Pendaftaran kelas kebugaran gym dengan batasan booking H-2 dan sistem antrean waitlist.')
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
<div class="alert alert-info border-0 shadow-sm mb-4" style="background-color: #eff6ff; color: #1e40af; border-radius: 10px;">
  <div class="d-flex align-items-center">
    <div>
      <strong class="d-block">Informasi Aturan Pendaftaran Kelas & Penalti No-Show:</strong>
      <small>&bull; Pendaftaran kelas dibuka maksimal <strong>H-2 sebelum tanggal kelas</strong>.<br>&bull; Jika kuota kelas penuh, Anda akan otomatis ditempatkan pada <strong>Sistem Antrean Waitlist</strong>.<br>&bull; Pelanggaran tidak hadir tanpa pembatalan (No-Show &ge; 3 kali) akan memicu pemblokiran pendaftaran otomatis selama 7 hari.</small>
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

<!-- My RSVPs & Waitlist Queue Table -->
<div class="card-custom">
  <h6 class="font-weight-bold text-dark mb-3">Riwayat & Status Antrean Kelas Saya</h6>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
        <tr>
          <th>Tanggal Kelas</th>
          <th>Nama Kelas</th>
          <th>Status Kehadiran / Antrean</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($myRsvps as $r)
          <tr>
            <td class="font-weight-bold text-dark">{{ \Carbon\Carbon::parse($r->class_date)->format('d M Y') }}</td>
            <td class="text-dark font-weight-bold">{{ $r->gymClass ? $r->gymClass->name : '-' }}</td>
            <td>
              @if($r->status == 'confirmed')
                <span class="badge badge-success">Dikonfirmasi</span>
              @elseif($r->status == 'waitlist')
                <span class="badge badge-warning">Waitlist Posisi #{{ $r->queue_position }}</span>
              @elseif($r->status == 'attended')
                <span class="badge badge-primary">Hadir</span>
              @elseif($r->status == 'noshow')
                <span class="badge badge-danger">Tidak Hadir (No-Show)</span>
              @else
                <span class="badge badge-secondary">Batal</span>
              @endif
            </td>
            <td>
              @if(in_array($r->status, ['confirmed', 'waitlist']))
                <form action="{{ route('member.classes.cancel', $r->id) }}" method="POST" onsubmit="return confirm('Batalkan RSVP kelas ini?')">
                  @csrf
                  <button type="submit" class="btn btn-sm btn-outline-danger font-weight-bold">Batalkan RSVP</button>
                </form>
              @else
                <span class="text-muted small">-</span>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="4" class="text-center py-4 text-muted small">Belum ada riwayat RSVP kelas.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
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
