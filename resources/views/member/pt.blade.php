@extends('layouts.admin')

@section('title', 'Personal Trainer (PT) &mdash; PetGym')
@section('page_title', 'Katalog Personal Trainer (PT) & Booking Sesi')
@section('page_subtitle', 'Beli paket kuota sesi PT dan booking jadwal latihan dengan garansi sistem anti-bentrok (Conflict Resolution Engine).')

@section('content')
<!-- Quota Summary Cards -->
<div class="card-custom mb-4">
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
    <div>
      <h6 class="font-weight-bold text-dark mb-1">🏋️ Status Kuota Sesi PT Aktif Anda</h6>
      <p class="text-muted small mb-0">Total sisa sesi latihan yang dapat digunakan untuk booking jadwal trainer.</p>
    </div>
    <div class="mt-3 mt-md-0">
      <span class="h4 font-weight-bold text-success mb-0">{{ $quotas->sum('remaining_sessions') }} Sesi Tersisa</span>
    </div>
  </div>

  @if($quotas->count())
    <div class="row mt-3">
      @foreach($quotas as $q)
        <div class="col-md-4 mb-2">
          <div class="p-3 border rounded bg-light d-flex justify-content-between align-items-center">
            <div>
              <strong class="text-dark d-block" style="font-size: 13.5px;">{{ $q->trainer ? $q->trainer->name : 'Trainer' }}</strong>
              <small class="text-muted">Total Dibeli: {{ $q->total_sessions }} Sesi</small>
            </div>
            <span class="badge badge-primary px-3 py-2 font-weight-bold" style="font-size: 13px;">{{ $q->remaining_sessions }} Sesi</span>
          </div>
        </div>
      @endforeach
    </div>
  @endif
</div>

<!-- PT Catalog -->
<h5 class="font-weight-bold text-dark mb-3">⭐ Katalog Personal Trainer Resmi Gym</h5>
<div class="row mb-4">
  @forelse($trainers as $t)
    <div class="col-md-4 mb-4">
      <div class="card-custom h-100 d-flex flex-column">
        <div class="d-flex align-items-center mb-3">
          <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3 font-weight-bold" style="width: 48px; height: 48px; font-size: 18px;">
            {{ substr($t->name, 0, 1) }}
          </div>
          <div>
            <h6 class="font-weight-bold text-dark mb-0">{{ $t->name }}</h6>
            <small class="text-primary font-weight-bold">{{ $t->specialization ?? 'Fitness & Bodybuilding Coach' }}</small>
          </div>
        </div>

        <p class="text-muted small mb-3 flex-grow-1">
          Spesialisasi pelantikan postur, pengurangan lemak, pembentukan otot, serta nutrisi atletis.
        </p>

        <div class="d-flex justify-content-between align-items-center mb-3 text-muted small">
          <span>⭐ Rating: <strong>4.9 / 5.0</strong></span>
          <span>Pengalaman: <strong>3+ Tahun</strong></span>
        </div>

        <div class="row gap-1">
          <div class="col-6">
            <button type="button" class="btn btn-outline-primary btn-block btn-sm font-weight-bold py-2 btn-buy-quota" data-id="{{ $t->id }}" data-name="{{ $t->name }}" data-toggle="modal" data-target="#buyQuotaModal">
              Beli Kuota
            </button>
          </div>
          <div class="col-6">
            <button type="button" class="btn btn-primary btn-block btn-sm font-weight-bold py-2 text-white btn-book-pt" data-id="{{ $t->id }}" data-name="{{ $t->name }}" data-toggle="modal" data-target="#bookPtModal">
              Booking Jadwal
            </button>
          </div>
        </div>
      </div>
    </div>
  @empty
    <div class="col-12 text-center py-4 text-muted">
      Belum ada trainer terdaftar.
    </div>
  @endforelse
</div>

<!-- Scheduled Bookings Table -->
<div class="card-custom">
  <h6 class="font-weight-bold text-dark mb-3">📅 Riwayat & Jadwal Booking PT Saya</h6>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
        <tr>
          <th>Tanggal Booking</th>
          <th>Jam Sesi</th>
          <th>Nama Trainer</th>
          <th>Status Booking</th>
        </tr>
      </thead>
      <tbody>
        @forelse($bookings as $b)
          <tr>
            <td class="font-weight-bold text-dark" style="font-size: 12.5px;">{{ \Carbon\Carbon::parse($b->booking_date)->format('d M Y') }}</td>
            <td class="text-primary font-weight-bold" style="font-size: 12.5px;">{{ $b->booking_time }}</td>
            <td class="text-dark font-weight-bold" style="font-size: 12.5px;">{{ $b->trainer ? $b->trainer->name : '-' }}</td>
            <td>
              @if($b->status == 'scheduled')
                <span class="badge badge-success px-2 py-1">Tersimpan (Scheduled)</span>
              @elseif($b->status == 'completed')
                <span class="badge badge-secondary px-2 py-1">Selesai</span>
              @else
                <span class="badge badge-danger px-2 py-1">Batal</span>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="4" class="text-center py-4 text-muted">Belum ada riwayat booking sesi PT.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Beli Kuota PT -->
<div class="modal fade" id="buyQuotaModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
      <div class="modal-header bg-primary text-white p-4">
        <h5 class="modal-title font-weight-bold text-white mb-0" id="buyQuotaTitle">Beli Paket Sesi PT</h5>
        <button type="button" class="close text-white opacity-100" data-dismiss="modal" aria-label="Close">&times;</button>
      </div>
      <form action="{{ route('member.pt.buy_quota') }}" method="POST">
        @csrf
        <input type="hidden" name="trainer_id" id="buyTrainerId">
        <div class="modal-body p-4">
          <div class="form-group mb-3">
            <label class="font-weight-bold text-dark small">Pilih Jumlah Sesi</label>
            <select name="sessions" class="form-control form-control-lg" required>
              <option value="5">Paket 5 Sesi (Rp 750.000)</option>
              <option value="10" selected>Paket 10 Sesi (Rp 1.500.000)</option>
              <option value="20">Paket 20 Sesi (Rp 3.000.000)</option>
            </select>
          </div>
          <small class="text-muted d-block">Pembelian sesi akan menambahkan sisa kuota PT Anda secara otomatis.</small>
        </div>
        <div class="modal-footer bg-light px-4 py-3">
          <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success font-weight-bold px-4 shadow-sm">Bayar Paket PT</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Booking Jadwal PT (Conflict-Free Engine) -->
<div class="modal fade" id="bookPtModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
      <div class="modal-header bg-primary text-white p-4">
        <h5 class="modal-title font-weight-bold text-white mb-0" id="bookPtTitle">Booking Jadwal PT</h5>
        <button type="button" class="close text-white opacity-100" data-dismiss="modal" aria-label="Close">&times;</button>
      </div>
      <form action="{{ route('member.pt.book') }}" method="POST">
        @csrf
        <input type="hidden" name="trainer_id" id="bookTrainerId">
        <div class="modal-body p-4">
          <div class="alert alert-info border-0 p-3 mb-3 rounded" style="background-color: #e0f2fe; color: #0369a1;">
            <small><strong>🛡️ Anti-Bentrok Conflict Resolution System:</strong> Sistem akan memeriksa ketersediaan jam trainer secara otomatis untuk mencegah booking ganda pada slot jam yang sama.</small>
          </div>

          <div class="form-group mb-3">
            <label class="font-weight-bold text-dark small">Pilih Tanggal Sesi</label>
            <input type="date" name="booking_date" class="form-control form-control-lg" min="{{ date('Y-m-d') }}" required>
          </div>

          <div class="form-group mb-3">
            <label class="font-weight-bold text-dark small">Pilih Slot Jam Sesi</label>
            <select name="booking_time" class="form-control form-control-lg" required>
              <option value="08:00">08:00 - 09:00 WIB</option>
              <option value="10:00">10:00 - 11:00 WIB</option>
              <option value="14:00">14:00 - 15:00 WIB</option>
              <option value="16:00">16:00 - 17:00 WIB</option>
              <option value="18:00">18:00 - 19:00 WIB</option>
              <option value="20:00">20:00 - 21:00 WIB</option>
            </select>
          </div>
        </div>
        <div class="modal-footer bg-light px-4 py-3">
          <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary font-weight-bold px-4 shadow-sm text-white">Konfirmasi Booking PT</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  $(document).ready(function() {
    $('.btn-buy-quota').on('click', function() {
      const id = $(this).data('id');
      const name = $(this).data('name');
      $('#buyTrainerId').val(id);
      $('#buyQuotaTitle').text('Beli Paket Sesi PT - ' + name);
    });

    $('.btn-book-pt').on('click', function() {
      const id = $(this).data('id');
      const name = $(this).data('name');
      $('#bookTrainerId').val(id);
      $('#bookPtTitle').text('Booking Jadwal PT - ' + name);
    });
  });
</script>
@endsection
