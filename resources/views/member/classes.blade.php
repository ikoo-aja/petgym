@extends('layouts.admin')

@section('title', 'Kelas Kebugaran (Member) &mdash; PetGym')
@section('page_title', 'Kelas Kebugaran & Sistem RSVP Waitlist')
@section('page_subtitle', 'Daftar kelas kebugaran gratis (Yoga/Zumba/HIIT) dengan batasan H-2 dan sistem sanksi Penalti No-Show otomatis.')

@section('content')
<!-- No-Show Penalty Status Alert Card -->
@if($member->is_penalty_blocked)
<div class="alert alert-danger border-0 shadow-sm mb-4 p-4" style="border-radius: 12px; background: linear-gradient(135deg, #ef4444, #dc2626); color: white;">
  <div class="d-flex align-items-center">
    <span class="icon-lock h2 mb-0 mr-3 text-white"></span>
    <div>
      <h5 class="font-weight-bold mb-1 text-white">🚫 AKSES BOOKING KELAS DIBLOKIR SEMENTARA</h5>
      <p class="mb-0 text-white-50">
        Anda telah mengumpulkan {{ $member->no_show_count }} kali pelanggaran No-Show (mendaftar kelas tetapi tidak hadir). 
        Akses pendaftaran kelas Anda ditangguhkan hingga <strong>{{ $member->penalty_blocked_until->format('d M Y H:i') }} WIB</strong>.
      </p>
    </div>
  </div>
</div>
@else
<div class="alert alert-info border-0 shadow-sm mb-4" style="background-color: #e0f2fe; color: #0369a1; border-radius: 10px;">
  <div class="d-flex align-items-center">
    <span class="icon-check h4 mb-0 mr-3 text-primary"></span>
    <div>
      <strong class="d-block">ℹ️ Informasi Aturan RSVP Kelas & Penalti No-Show:</strong>
      <small>&bull; Pendaftaran kelas dibuka maksimal <strong>H-2 sebelum tanggal kelas</strong>.<br>&bull; Jika kuota kelas penuh, Anda akan otomatis ditempatkan pada <strong>Sistem Antrean Waitlist</strong>.<br>&bull; Pelanggaran tidak hadir tanpa pembatalan (No-Show &ge; 3 kali) akan memicu pemblokiran pendaftaran otomatis selama 7 hari.</small>
    </div>
  </div>
</div>
@endif

<!-- Classes Catalog -->
<h5 class="font-weight-bold text-dark mb-3">🧘 Jadwal & Kuota Kelas Kebugaran Gym</h5>
<div class="row mb-4">
  @forelse($classes as $c)
    <div class="col-md-6 mb-4">
      <div class="card-custom h-100 d-flex flex-column">
        <div class="d-flex justify-content-between align-items-start mb-3">
          <div>
            <h5 class="font-weight-bold text-dark mb-1">{{ $c->name }}</h5>
            <small class="text-muted">Instruktur: <strong class="text-dark">{{ $c->trainer ? $c->trainer->name : 'Staff Instruktur' }}</strong></small>
          </div>
          <span class="badge badge-info px-3 py-2 font-weight-bold" style="font-size: 12px;">{{ $c->day }}</span>
        </div>

        <div class="p-3 bg-light rounded mb-3">
          <div class="d-flex justify-content-between text-dark small font-weight-bold mb-1">
            <span>Jam Latihan: {{ substr($c->start_time, 0, 5) }} - {{ substr($c->end_time, 0, 5) }} WIB</span>
            <span>Ruangan: {{ $c->room ?? 'Studio 1' }}</span>
          </div>
          <small class="text-muted">Kapasitas Maksimal: <strong>{{ $c->max_capacity }} Peserta</strong> per sesi</small>
        </div>

        <div class="mt-auto">
          @if($member->is_penalty_blocked)
            <button type="button" class="btn btn-secondary btn-block font-weight-bold py-2" disabled>Booking Diblokir (Penalti)</button>
          @else
            <button type="button" class="btn btn-primary btn-block font-weight-bold py-2 text-white btn-rsvp-class" data-id="{{ $c->id }}" data-name="{{ $c->name }}" data-toggle="modal" data-target="#rsvpModal">
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
  <h6 class="font-weight-bold text-dark mb-3">📋 Riwayat & Status Antrean Kelas Saya</h6>
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
            <td class="font-weight-bold text-dark" style="font-size: 12.5px;">{{ \Carbon\Carbon::parse($r->class_date)->format('d M Y') }}</td>
            <td class="text-dark font-weight-bold" style="font-size: 12.5px;">{{ $r->gymClass ? $r->gymClass->name : '-' }}</td>
            <td>
              @if($r->status == 'confirmed')
                <span class="badge badge-success px-2 py-1">Dikonfirmasi (Confirmed)</span>
              @elseif($r->status == 'waitlist')
                <span class="badge badge-warning px-2 py-1">Antrean Waitlist Posisi #{{ $r->queue_position }}</span>
              @elseif($r->status == 'attended')
                <span class="badge badge-primary px-2 py-1">Hadir</span>
              @elseif($r->status == 'noshow')
                <span class="badge badge-danger px-2 py-1">Tidak Hadir (No-Show)</span>
              @else
                <span class="badge badge-secondary px-2 py-1">Batal</span>
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
            <td colspan="4" class="text-center py-4 text-muted">Belum ada riwayat RSVP kelas.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<!-- Modal RSVP Kelas -->
<div class="modal fade" id="rsvpModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
      <div class="modal-header bg-primary text-white p-4">
        <h5 class="modal-title font-weight-bold text-white mb-0" id="rsvpTitle">RSVP Pendaftaran Kelas</h5>
        <button type="button" class="close text-white opacity-100" data-dismiss="modal" aria-label="Close">&times;</button>
      </div>
      <form action="{{ route('member.classes.rsvp') }}" method="POST">
        @csrf
        <input type="hidden" name="gym_class_id" id="rsvpClassId">
        <div class="modal-body p-4">
          <div class="form-group mb-3">
            <label class="font-weight-bold text-dark small">Pilih Tanggal Kelas (Maksimal H-2)</label>
            <input type="date" name="class_date" class="form-control form-control-lg" min="{{ date('Y-m-d') }}" max="{{ date('Y-m-d', strtotime('+2 days')) }}" value="{{ date('Y-m-d') }}" required>
          </div>
          <small class="text-muted d-block">Jika kuota kelas pada tanggal yang Anda pilih sudah penuh, Anda akan otomatis ditempatkan pada sistem antrean Waitlist Queue.</small>
        </div>
        <div class="modal-footer bg-light px-4 py-3">
          <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success font-weight-bold px-4 shadow-sm">Konfirmasi RSVP Kelas</button>
        </div>
      </form>
    </div>
  </div>
</div>

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
