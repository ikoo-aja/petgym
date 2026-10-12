@extends('layouts.member')

@section('title', 'Pelatih Pribadi')
@section('page_title', 'Pelatih Pribadi (Personal Trainer)')
@section('page_subtitle', 'Beli kuota sesi latihan dan jadwalkan latihan bersama pelatih pilihan Anda.')

@section('content')
<!-- Quota Summary Header Card -->
<div class="card-custom mb-3 mb-md-4">
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
    <div>
      <h6 class="font-weight-bold text-dark mb-1">Status Kuota Sesi Pelatih Aktif</h6>
      <p class="text-muted small mb-0">Total sisa sesi latihan yang dapat digunakan untuk penjadwalan latihan.</p>
    </div>
    <div class="mt-2 mt-md-0">
      <span class="h4 font-weight-bold text-primary mb-0">{{ $quotas->sum('remaining_sessions') }} Sesi Tersisa</span>
    </div>
  </div>

  @if($quotas->count())
    <div class="row mt-3">
      @foreach($quotas as $q)
        <div class="col-12 col-md-4 mb-2">
          <div class="p-3 border rounded bg-light d-flex justify-content-between align-items-center" style="border-radius: 10px !important;">
            <div>
              <strong class="text-dark d-block" style="font-size: 13.5px;">{{ $q->trainer ? $q->trainer->name : 'Pelatih' }}</strong>
              <small class="text-muted">Total Pembelian: {{ $q->total_sessions }} Sesi</small>
            </div>
            <span class="badge badge-primary font-weight-bold">{{ $q->remaining_sessions }} Sesi</span>
          </div>
        </div>
      @endforeach
    </div>
  @else
    <div class="alert alert-warning border-0 mt-3 mb-0" style="border-radius: 8px;">
      <small>Anda belum memiliki kuota sesi latihan. Silakan pilih pelatih di bawah dan klik <strong>Beli Kuota</strong>.</small>
    </div>
  @endif
</div>

<!-- PT Catalog Grid -->
<h6 class="font-weight-bold text-dark mb-3">Katalog Pelatih Pribadi Resmi Gym</h6>
<div class="row mb-3 mb-md-4">
  @forelse($trainers as $t)
    <div class="col-12 col-md-4 mb-3">
      <div class="card-custom h-100 d-flex flex-column">
        <div class="d-flex align-items-center mb-3">
          <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3 font-weight-bold" style="width: 44px; height: 44px; font-size: 16px;">
            {{ substr($t->name, 0, 1) }}
          </div>
          <div>
            <h6 class="font-weight-bold text-dark mb-0">{{ $t->name }}</h6>
            <small class="text-primary font-weight-bold">{{ $t->specialization ?? 'Pelatih Kebugaran' }}</small>
          </div>
        </div>

        <p class="text-muted small mb-3 flex-grow-1">
          Spesialisasi pembentukan postur, kekuatan otot, penurunan berat badan, serta program nutrisi harian.
        </p>

        <div class="d-flex justify-content-between align-items-center mb-3 text-muted small">
          <span>Penilaian: <strong>4.9 / 5.0</strong></span>
          <span>Pengalaman: <strong>3+ Tahun</strong></span>
        </div>

        <div class="row">
          <div class="col-6 pr-1">
            <button type="button" class="btn btn-outline-primary btn-block btn-sm py-2 font-weight-bold btn-buy-quota"
                    data-id="{{ $t->id }}"
                    data-name="{{ $t->name }}"
                    data-toggle="modal"
                    data-target="#buyQuotaModal">
              Beli Kuota
            </button>
          </div>
          <div class="col-6 pl-1">
            @php
              $trainerQuota = $quotas->where('trainer_id', $t->id)->first();
              $remainingSess = $trainerQuota ? $trainerQuota->remaining_sessions : 0;
            @endphp
            <button type="button" class="btn btn-primary btn-block btn-sm py-2 font-weight-bold btn-book-pt"
                    data-id="{{ $t->id }}"
                    data-name="{{ $t->name }}"
                    data-quota="{{ $remainingSess }}"
                    data-toggle="modal"
                    data-target="#bookPtModal">
              Jadwalkan
            </button>
          </div>
        </div>
      </div>
    </div>
  @empty
    <div class="col-12 text-center py-4 text-muted bg-light rounded border">
      Belum ada data pelatih terdaftar saat ini.
    </div>
  @endforelse
</div>

<!-- Scheduled Bookings Card List -->
<div class="card-custom">
  <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
    <h6 class="font-weight-bold text-dark mb-0">Riwayat & Jadwal Latihan</h6>
    <small class="text-muted">{{ count($bookings) }} Sesi Terdaftar</small>
  </div>

  @forelse($bookings as $b)
    <div class="border rounded p-3 mb-2 bg-light d-flex justify-content-between align-items-center" style="border-radius: 10px !important;">
      <div class="d-flex align-items-center">
        <div class="rounded-circle bg-white text-primary p-2 mr-3 border d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px; font-weight: 800; font-size: 15px;">
          {{ substr($b->trainer ? $b->trainer->name : 'P', 0, 1) }}
        </div>
        <div>
          <h6 class="font-weight-bold text-dark mb-1" style="font-size: 13.5px;">{{ $b->trainer ? $b->trainer->name : 'Pelatih Pribadi' }}</h6>
          <small class="text-muted"><span class="icon-calendar mr-1"></span> {{ \Carbon\Carbon::parse($b->booking_date)->format('d M Y') }} &bull; <strong class="text-primary">{{ $b->booking_time }}</strong></small>
        </div>
      </div>
      <div class="ml-2 text-right">
        @if($b->status == 'scheduled')
          <span class="badge badge-success px-2 py-1 font-weight-bold" style="font-size: 11px;">Terjadwal</span>
        @elseif($b->status == 'completed')
          <span class="badge badge-secondary px-2 py-1 font-weight-bold" style="font-size: 11px;">Selesai</span>
        @else
          <span class="badge badge-danger px-2 py-1 font-weight-bold" style="font-size: 11px;">Dibatalkan</span>
        @endif
      </div>
    </div>
  @empty
    <div class="text-center py-4 text-muted bg-light rounded border">
      <span class="icon-user d-block mb-1" style="font-size: 22px;"></span>
      <small class="d-block font-weight-semibold">Belum ada riwayat penjadwalan sesi latihan.</small>
    </div>
  @endforelse
</div>

<!-- Modal Gateway Pembayaran Paket Kuota PT -->
<div class="modal fade" id="buyQuotaModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-light">
        <div>
          <h5 class="modal-title font-weight-bold text-dark mb-0" id="buyQuotaTitle">Beli Paket Sesi Pelatih</h5>
          <small class="text-muted">Gerbang Pembayaran Digital</small>
        </div>
        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">&times;</button>
      </div>
      <form action="{{ route('member.pt.buy_quota') }}" method="POST">
        @csrf
        <input type="hidden" name="trainer_id" id="buyTrainerId">
        <div class="modal-body p-4">
          <div class="form-group mb-3">
            <label class="font-weight-bold text-dark small">Pilih Jumlah Sesi Latihan</label>
            <select name="sessions" id="selectSessions" class="form-control" required onchange="updatePtPrice()">
              <option value="5" data-price="750000">Paket 5 Sesi (Rp 750.000)</option>
              <option value="10" data-price="1500000" selected>Paket 10 Sesi (Rp 1.500.000)</option>
              <option value="20" data-price="3000000">Paket 20 Sesi (Rp 3.000.000)</option>
            </select>
          </div>

          <div class="p-3 bg-light rounded text-center mb-3">
            <small class="text-muted text-uppercase font-weight-bold d-block mb-1">Total Tagihan Pembayaran</small>
            <span class="h3 font-weight-bold text-primary mb-0" id="displayPtPrice">Rp 1.500.000</span>
          </div>

          <div class="form-group mb-3">
            <label class="font-weight-bold text-dark small">Metode Pembayaran</label>
            <select name="payment_method" class="form-control" required>
              <option value="qris">QRIS (Scan Kode QR)</option>
              <option value="transfer">Transfer Bank Virtual Account</option>
              <option value="cash">Pembayaran Tunai di Kasir Resepsionis</option>
            </select>
          </div>

          <small class="text-muted d-block text-center">Pembelian sesi akan menambahkan sisa kuota latihan Anda secara otomatis setelah pembayaran terverifikasi.</small>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary font-weight-bold">Konfirmasi & Bayar Sekarang</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Booking Jadwal PT -->
<div class="modal fade" id="bookPtModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-light">
        <div>
          <h5 class="modal-title font-weight-bold text-dark mb-0" id="bookPtTitle">Jadwalkan Sesi Latihan</h5>
          <small class="text-muted">Pilih tanggal dan slot jam latihan</small>
        </div>
        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">&times;</button>
      </div>
      <form action="{{ route('member.pt.book') }}" method="POST">
        @csrf
        <input type="hidden" name="trainer_id" id="bookTrainerId">
        <div class="modal-body p-4">
          <div id="ptQuotaWarning" class="alert alert-warning border-0 p-3 mb-3 rounded d-none">
            <strong>Kuota Belum Tersedia:</strong> Anda belum memiliki sisa kuota sesi latihan dengan pelatih ini. Silakan beli paket kuota terlebih dahulu.
          </div>

          <div class="form-group mb-3">
            <label class="font-weight-bold text-dark small">Tanggal Sesi Latihan</label>
            <input type="date" name="booking_date" class="form-control" min="{{ date('Y-m-d') }}" max="{{ date('Y-m-d', strtotime('+14 days')) }}" value="{{ date('Y-m-d') }}" required>
          </div>

          <div class="form-group mb-3">
            <label class="font-weight-bold text-dark small">Slot Waktu Sesi</label>
            <select name="booking_time" class="form-control" required>
              <option value="08:00">08:00 - 09:00 WIB</option>
              <option value="10:00">10:00 - 11:00 WIB</option>
              <option value="14:00">14:00 - 15:00 WIB</option>
              <option value="16:00">16:00 - 17:00 WIB</option>
              <option value="18:00">18:00 - 19:00 WIB</option>
              <option value="20:00">20:00 - 21:00 WIB</option>
            </select>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Batal</button>
          <button type="submit" id="btnSubmitBookPt" class="btn btn-primary font-weight-bold">Konfirmasi Jadwal</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script>
  function updatePtPrice() {
    const price = parseInt($('#selectSessions option:selected').data('price'));
    $('#displayPtPrice').text('Rp ' + price.toLocaleString('id-ID'));
  }

  $(document).ready(function() {
    $('.btn-buy-quota').on('click', function() {
      const id = $(this).data('id');
      const name = $(this).data('name');
      $('#buyTrainerId').val(id);
      $('#buyQuotaTitle').text('Beli Paket Sesi - ' + name);
      updatePtPrice();
    });

    $('.btn-book-pt').on('click', function() {
      const id = $(this).data('id');
      const name = $(this).data('name');
      const quota = parseInt($(this).data('quota'));

      $('#bookTrainerId').val(id);
      $('#bookPtTitle').text('Jadwalkan Sesi - ' + name);

      if (quota <= 0) {
        $('#ptQuotaWarning').removeClass('d-none');
        $('#btnSubmitBookPt').prop('disabled', true).text('Kuota Habis');
      } else {
        $('#ptQuotaWarning').addClass('d-none');
        $('#btnSubmitBookPt').prop('disabled', false).text('Konfirmasi Jadwal');
      }
    });
  });
</script>
@endsection
