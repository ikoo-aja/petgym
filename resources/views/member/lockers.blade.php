@extends('layouts.member')

@section('title', 'Booking loker - PetGym')
@section('page_title', 'Booking Loker')
@section('page_subtitle', '')
@section('member_tier_badge', 'Tier ' . strtoupper($member->membership_tier ?? 'Basic'))

@section('content')
<!-- Active Rental Card -->
@if($activeRental)
<div class="card-custom border-left border-success mb-4" style="border-left-width: 4px !important;">
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
    <div>
      <span class="badge badge-success font-weight-bold text-uppercase mb-2">Sewa Loker Aktif</span>
      <h4 class="font-weight-bold text-dark mb-1">Nomor Loker: <span class="text-primary">#{{ $activeRental->locker ? $activeRental->locker->locker_number : '-' }}</span></h4>
      <p class="mb-0 text-muted small">Tipe Sewa: <strong>{{ strtoupper($activeRental->rental_type) }}</strong> | Berakhir pada: <strong>{{ \Carbon\Carbon::parse($activeRental->end_date)->format('d M Y') }}</strong></p>
    </div>
    <div class="mt-3 mt-md-0 text-md-right">
      <small class="text-muted text-uppercase font-weight-bold d-block mb-1">Kode Access PIN</small>
      <div class="d-inline-block bg-light text-dark font-weight-bold px-3 py-2 rounded border shadow-sm" style="font-size: 22px; letter-spacing: 4px;">
        {{ $activeRental->pin_code ?? '849201' }}
      </div>
      <form action="{{ route('member.lockers.return', $activeRental->id) }}" method="POST" class="mt-2" data-confirm="Selesaikan sewa loker ini sekarang?">
        @csrf
        <button type="submit" class="btn btn-sm btn-outline-danger font-weight-bold">Kembalikan Loker</button>
      </form>
    </div>
  </div>
</div>
@endif

<!-- Non-Premium Upgrade Banner Teaser -->
@if($member->membership_tier !== 'premium')
<div class="alert alert-info border-0 shadow-sm mb-4 p-3" style="background-color: #eff6ff; color: #1e40af; border-radius: 12px;">
  <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center">
    <div class="mb-3 mb-sm-0 mr-sm-3">
      <div class="d-flex align-items-center mb-1">
        <strong style="font-size: 14px;">Informasi Sewa Loker Gratis</strong>
      </div>
      <p class="mb-0 small" style="color: #2563eb;">
        Member Tier <strong>Premium</strong> berhak mendapatkan fasilitas <strong>Gratis Sewa Loker Bulanan</strong>!
      </p>
    </div>
    <div class="flex-shrink-0">
      <a href="{{ route('member.membership') }}" class="btn btn-sm btn-primary font-weight-bold btn-block d-sm-inline-block px-3 py-2" style="border-radius: 8px;">
        Upgrade Ke Premium
      </a>
    </div>
  </div>
</div>
@endif

<!-- Interactive Locker Grid Layout Card -->
<div class="card-custom">
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
      <h6 class="font-weight-bold text-dark mb-1">Denah Pemilihan Loker Visual</h6>
      <p class="text-muted small mb-0">Klik pada kotak loker yang tersedia (berwarna hijau) untuk memilih dan membuka alur pembayaran.</p>
    </div>

    <!-- Status Legend Bar -->
    <div class="d-flex gap-3 align-items-center mt-3 mt-md-0">
      <div class="d-flex align-items-center mr-3">
        <span class="d-inline-block rounded mr-1" style="width: 12px; height: 12px; background-color: #10b981;"></span>
        <small class="font-weight-bold text-muted">Tersedia</small>
      </div>
      <div class="d-flex align-items-center mr-3">
        <span class="d-inline-block rounded mr-1" style="width: 12px; height: 12px; background-color: #ef4444;"></span>
        <small class="font-weight-bold text-muted">Terpakai</small>
      </div>
      <div class="d-flex align-items-center">
        <span class="d-inline-block rounded mr-1" style="width: 12px; height: 12px; background-color: #f59e0b;"></span>
        <small class="font-weight-bold text-muted">Rusak</small>
      </div>
    </div>
  </div>

  <div class="row text-center">
    @forelse($lockers as $l)
      <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-3">
        @if($l->status == 'tersedia')
          <button type="button"
                  class="btn btn-block p-3 border btn-select-locker"
                  style="border-radius: 10px; background-color: #f0fdf4; border-color: #bbf7d0 !important; transition: transform 0.2s;"
                  data-id="{{ $l->id }}"
                  data-number="{{ $l->locker_number }}"
                  data-toggle="modal"
                  data-target="#rentLockerModal">
            <span class="font-weight-bold d-block text-dark" style="font-size: 15px;">Loker #{{ $l->locker_number }}</span>
            <span class="badge badge-success font-weight-bold mt-2">Tersedia</span>
          </button>
        @elseif($l->status == 'terpakai')
          <div class="p-3 border rounded" style="border-radius: 10px; background-color: #fef2f2; border-color: #fecaca !important;">
            <span class="font-weight-bold d-block text-dark" style="font-size: 15px;">Loker #{{ $l->locker_number }}</span>
            <span class="badge badge-danger font-weight-bold mt-2">Terpakai</span>
          </div>
        @else
          <div class="p-3 border rounded" style="border-radius: 10px; background-color: #fffbebf; border-color: #fef3c7 !important;">
            <span class="font-weight-bold d-block text-dark" style="font-size: 15px;">Loker #{{ $l->locker_number }}</span>
            <span class="badge badge-warning font-weight-bold mt-2">Rusak</span>
          </div>
        @endif
      </div>
    @empty
      <div class="col-12 text-center py-5 text-muted">
        Belum ada unit loker terdaftar di sistem.
      </div>
    @endforelse
  </div>
</div>

<!-- Modal Checkout Gateway Pembayaran Loker -->
<div class="modal fade" id="rentLockerModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-light">
        <div>
          <h5 class="modal-title font-weight-bold text-dark mb-0" id="rentModalTitle">Sewa Loker #01</h5>
          <small class="text-muted">Checkout Payment Gateway PetGym</small>
        </div>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="{{ route('member.lockers.rent') }}" method="POST">
        @csrf
        <input type="hidden" name="locker_id" id="modalLockerId">

        <div class="modal-body p-4">
          @if($member->membership_tier === 'premium')
            <div class="alert alert-success border-0 p-3 mb-3 rounded" style="background-color: #ecfdf5; color: #065f46;">
              <strong class="d-block">Hak Akses Tier Premium:</strong>
              <small>Sebagai member Tier Premium, Anda mendapatkan fasilitas <strong>Gratis Sewa Loker Bulanan (Rp 0)</strong>!</small>
            </div>
          @endif

          <div class="form-group mb-3">
            <label class="font-weight-bold text-dark small">Pilih Durasi Penyewaan</label>
            <select name="rental_type" id="modalRentalType" class="form-control" required onchange="updateLockerPrice()">
              <option value="daily" data-price="15000">Harian (1 Hari - Rp 15.000)</option>
              <option value="monthly" data-price="{{ $member->membership_tier === 'premium' ? 0 : 150000 }}" {{ $member->membership_tier === 'premium' ? 'selected' : '' }}>
                Bulanan (30 Hari - {{ $member->membership_tier === 'premium' ? 'GRATIS (Bundling Premium)' : 'Rp 150.000' }})
              </option>
            </select>
          </div>

          <div class="p-3 bg-light rounded text-center mb-3">
            <small class="text-muted text-uppercase font-weight-bold d-block mb-1">Total Tagihan Pembayaran</small>
            <span class="h3 font-weight-bold text-primary mb-0" id="displayLockerPrice">Rp 15.000</span>
          </div>

          <div class="form-group mb-3">
            <label class="font-weight-bold text-dark small">Metode Pembayaran</label>
            <select name="payment_method" class="form-control" required>
              <option value="qris">QRIS Standar Nasional (Scan QR Code)</option>
              <option value="transfer">Transfer Bank Virtual Account (BCA / Mandiri / BRI)</option>
              <option value="cash">Pembayaran Tunai di Kasir Resepsionis</option>
            </select>
          </div>

          <div class="p-3 bg-light rounded text-center border">
            <small class="text-muted text-uppercase font-weight-bold d-block mb-1">Simulasi Kode Access PIN 6-Digit</small>
            <span class="h4 font-weight-bold text-dark mb-0" style="letter-spacing: 4px;">******</span>
            <small class="text-muted d-block mt-1">Access PIN dibuat dan diaktifkan otomatis setelah pembayaran lunas.</small>
          </div>
        </div>

        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary font-weight-bold">Konfirmasi & Bayar Sekarang</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script>
  function updateLockerPrice() {
    const selected = $('#modalRentalType option:selected');
    const price = selected.data('price');
    if (price === 0) {
      $('#displayLockerPrice').text('GRATIS (Rp 0)');
    } else {
      $('#displayLockerPrice').text('Rp ' + price.toLocaleString('id-ID'));
    }
  }

  $(document).ready(function() {
    $('.btn-select-locker').on('click', function() {
      const id = $(this).data('id');
      const number = $(this).data('number');

      $('#modalLockerId').val(id);
      $('#rentModalTitle').text('Sewa Loker #' + number);
      updateLockerPrice();
    });
  });
</script>
@endsection
