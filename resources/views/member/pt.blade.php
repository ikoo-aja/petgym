@extends('layouts.member')

@section('title', 'Personal Trainer - PetGym')
@section('page_title', 'Katalog Personal Trainer (PT) & Booking Sesi')
@section('page_subtitle', 'Beli paket kuota sesi PT dan booking jadwal latihan dengan garansi sistem anti-bentrok.')
@section('member_tier_badge', 'Tier ' . strtoupper($member->membership_tier ?? 'Basic'))

@section('content')
<!-- Quota Summary Header Card -->
<div class="card-custom mb-4">
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
    <div>
      <h6 class="font-weight-bold text-dark mb-1">Status Kuota Sesi PT Aktif</h6>
      <p class="text-muted small mb-0">Total sisa sesi latihan yang dapat digunakan untuk booking jadwal trainer.</p>
    </div>
    <div class="mt-3 mt-md-0">
      <span class="h4 font-weight-bold text-primary mb-0">{{ $quotas->sum('remaining_sessions') }} Sesi Tersisa</span>
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
            <span class="badge badge-primary font-weight-bold">{{ $q->remaining_sessions }} Sesi</span>
          </div>
        </div>
      @endforeach
    </div>
  @else
    <div class="alert alert-warning border-0 mt-3 mb-0" style="background-color: #fffbebf; color: #b45309; border-radius: 8px;">
      <small>Anda belum memiliki kuota sesi PT. Silakan pilih trainer di bawah dan klik <strong>Beli Kuota</strong>.</small>
    </div>
  @endif
</div>

<!-- PT Catalog Grid -->
<h6 class="font-weight-bold text-dark mb-3">Katalog Personal Trainer Resmi Gym</h6>
<div class="row mb-4">
  @forelse($trainers as $t)
    <div class="col-md-4 mb-4">
      <div class="card-custom h-100 d-flex flex-column">
        <div class="d-flex align-items-center mb-3">
          <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3 font-weight-bold" style="width: 44px; height: 44px; font-size: 16px;">
            {{ substr($t->name, 0, 1) }}
          </div>
          <div>
            <h6 class="font-weight-bold text-dark mb-0">{{ $t->name }}</h6>
            <small class="text-primary font-weight-bold">{{ $t->specialization ?? 'Fitness Coach' }}</small>
          </div>
        </div>

        <p class="text-muted small mb-3 flex-grow-1">
          Pelatihan postur, pembentukan otot, pembakaran lemak, serta panduan nutrisi atletis.
        </p>

        <div class="d-flex justify-content-between align-items-center mb-3 text-muted small">
          <span>Rating: <strong>4.9 / 5.0</strong></span>
          <span>Pengalaman: <strong>3+ Tahun</strong></span>
        </div>

        <div class="row">
          <div class="col-6">
            <button type="button" class="btn btn-outline-primary btn-block btn-sm py-2 font-weight-bold btn-buy-quota" 
                    data-id="{{ $t->id }}" 
                    data-name="{{ $t->name }}" 
                    data-toggle="modal" 
                    data-target="#buyQuotaModal">
              Beli Kuota
            </button>
          </div>
          <div class="col-6">
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
              Booking Sesi
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
  <h6 class="font-weight-bold text-dark mb-3">Riwayat & Jadwal Booking PT Saya</h6>
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
            <td class="font-weight-bold text-dark">{{ \Carbon\Carbon::parse($b->booking_date)->format('d M Y') }}</td>
            <td class="text-primary font-weight-bold">{{ $b->booking_time }}</td>
            <td class="text-dark font-weight-bold">{{ $b->trainer ? $b->trainer->name : '-' }}</td>
            <td>
              @if($b->status == 'scheduled')
                <span class="badge badge-success">Terjadwal</span>
              @elseif($b->status == 'completed')
                <span class="badge badge-secondary">Selesai</span>
              @else
                <span class="badge badge-danger">Batal</span>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="4" class="text-center py-4 text-muted small">Belum ada riwayat booking sesi PT.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Gateway Pembayaran Paket Kuota PT -->
<div class="modal fade" id="buyQuotaModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-light">
        <div>
          <h5 class="modal-title font-weight-bold text-dark mb-0" id="buyQuotaTitle">Beli Paket Sesi PT</h5>
          <small class="text-muted">Checkout Payment Gateway PetGym</small>
        </div>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
      </div>
      <form action="{{ route('member.pt.buy_quota') }}" method="POST">
        @csrf
        <input type="hidden" name="trainer_id" id="buyTrainerId">
        <div class="modal-body p-4">
          <div class="form-group mb-3">
            <label class="font-weight-bold text-dark small">Pilih Jumlah Sesi PT</label>
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
              <option value="qris">QRIS Standar Nasional (Scan QR Code)</option>
              <option value="transfer">Transfer Bank Virtual Account (BCA / Mandiri / BRI)</option>
              <option value="cash">Pembayaran Tunai di Kasir Resepsionis</option>
            </select>
          </div>

          <small class="text-muted d-block text-center">Pembelian sesi akan menambahkan sisa kuota PT Anda secara otomatis setelah pembayaran lunas.</small>
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
          <h5 class="modal-title font-weight-bold text-dark mb-0" id="bookPtTitle">Booking Jadwal PT</h5>
          <small class="text-muted">Pilih tanggal dan slot jam sesi</small>
        </div>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
      </div>
      <form action="{{ route('member.pt.book') }}" method="POST">
        @csrf
        <input type="hidden" name="trainer_id" id="bookTrainerId">
        <div class="modal-body p-4">
          <div id="ptQuotaWarning" class="alert alert-warning border-0 p-3 mb-3 rounded d-none">
            <strong>Kuota PT Kosong:</strong> Anda tidak memiliki sisa kuota sesi PT dengan trainer ini. Silakan beli paket kuota terlebih dahulu.
          </div>

          <div class="alert alert-info border-0 p-3 mb-3 rounded" style="background-color: #eff6ff; color: #1e40af;">
            <small><strong>Proteksi Jadwal:</strong> Sistem akan memeriksa ketersediaan jam trainer secara otomatis untuk mencegah booking ganda.</small>
          </div>

          <div class="form-group mb-3">
            <label class="font-weight-bold text-dark small">Tanggal Sesi</label>
            <input type="date" name="booking_date" class="form-control" min="{{ date('Y-m-d') }}" max="{{ date('Y-m-d', strtotime('+14 days')) }}" value="{{ date('Y-m-d') }}" required>
          </div>

          <div class="form-group mb-3">
            <label class="font-weight-bold text-dark small">Slot Jam Sesi</label>
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
          <button type="submit" id="btnSubmitBookPt" class="btn btn-primary font-weight-bold">Konfirmasi Booking</button>
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
      $('#buyQuotaTitle').text('Beli Paket Sesi PT - ' + name);
      updatePtPrice();
    });

    $('.btn-book-pt').on('click', function() {
      const id = $(this).data('id');
      const name = $(this).data('name');
      const quota = parseInt($(this).data('quota'));

      $('#bookTrainerId').val(id);
      $('#bookPtTitle').text('Booking Jadwal PT - ' + name);

      if (quota <= 0) {
        $('#ptQuotaWarning').removeClass('d-none');
        $('#btnSubmitBookPt').prop('disabled', true).text('Kuota PT Habis');
      } else {
        $('#ptQuotaWarning').addClass('d-none');
        $('#btnSubmitBookPt').prop('disabled', false).text('Konfirmasi Booking');
      }
    });
  });
</script>
@endsection
