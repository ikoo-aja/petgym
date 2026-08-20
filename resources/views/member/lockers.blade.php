@extends('layouts.admin')

@section('title', 'Manajemen Loker Gym (Member) &mdash; PetGym')
@section('page_title', 'Sistem Penyewaan Loker & Akses PIN Digital')
@section('page_subtitle', 'Pilih lokasi nomor loker gym secara visual interaktif dan dapatkan Access PIN 6-digit untuk membuka loker.')

@section('content')
<!-- Active Rental Card if Exists -->
@if($activeRental)
<div class="card border-0 shadow-sm mb-4" style="border-radius: 15px; background: linear-gradient(135deg, #10b981, #059669); color: #fff;">
  <div class="card-body p-4">
    <div class="row align-items-center">
      <div class="col-md-8">
        <span class="badge badge-light text-success font-weight-bold uppercase mb-2">🔑 SEWA LOKER AKTIF ANDA</span>
        <h3 class="font-weight-bold text-white mb-1">Nomor Loker: <span class="text-warning">#{{ $activeRental->locker ? $activeRental->locker->locker_number : '-' }}</span></h3>
        <p class="mb-0 text-white-50">Tipe Sewa: <strong>{{ strtoupper($activeRental->rental_type) }}</strong> | Berakhir pada: <strong>{{ \Carbon\Carbon::parse($activeRental->end_date)->format('d M Y') }}</strong></p>
      </div>
      <div class="col-md-4 text-md-right mt-3 mt-md-0">
        <small class="text-white-50 uppercase font-weight-bold d-block mb-1">ACCESS PIN 6-DIGIT</small>
        <div class="d-inline-block bg-white text-dark font-weight-bold px-3 py-2 rounded shadow" style="font-size: 24px; letter-spacing: 4px;">
          {{ $activeRental->pin_code ?? '849201' }}
        </div>
        <form action="{{ route('member.lockers.return', $activeRental->id) }}" method="POST" class="mt-2" onsubmit="return confirm('Akhiri sewa loker ini sekarang?')">
          @csrf
          <button type="submit" class="btn btn-sm btn-light text-danger font-weight-bold shadow-sm">Akhiri Sewa Loker</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endif

<!-- Visual Selection UI Card -->
<div class="card-custom">
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
      <h5 class="font-weight-bold text-dark mb-1">🎯 Denah Pemilihan Loker Visual</h5>
      <p class="text-muted small mb-0">Klik pada kotak loker berwarna hijau yang tersedia untuk melakukan booking penyewaan.</p>
    </div>

    <!-- Legend Bar -->
    <div class="d-flex gap-3 align-items-center mt-3 mt-md-0">
      <div class="d-flex align-items-center">
        <span class="d-inline-block rounded mr-2" style="width: 16px; height: 16px; background-color: #10b981;"></span>
        <small class="font-weight-bold text-muted">Tersedia</small>
      </div>
      <div class="d-flex align-items-center">
        <span class="d-inline-block rounded mr-2" style="width: 16px; height: 16px; background-color: #ef4444;"></span>
        <small class="font-weight-bold text-muted">Terpakai / Disewa</small>
      </div>
      <div class="d-flex align-items-center">
        <span class="d-inline-block rounded mr-2" style="width: 16px; height: 16px; background-color: #f59e0b;"></span>
        <small class="font-weight-bold text-muted">Rusak</small>
      </div>
    </div>
  </div>

  <!-- Interactive Locker Grid Layout -->
  <div class="row text-center">
    @forelse($lockers as $l)
      <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-3">
        @if($l->status == 'tersedia')
          <button type="button" 
                  class="btn btn-block p-3 border-0 shadow-sm btn-select-locker" 
                  style="border-radius: 12px; background: linear-gradient(135deg, #10b981, #059669); color: white; transition: transform 0.2s;"
                  data-id="{{ $l->id }}" 
                  data-number="{{ $l->locker_number }}"
                  data-toggle="modal" 
                  data-target="#rentLockerModal">
            <span class="icon-lock h4 d-block mb-1"></span>
            <span class="font-weight-bold d-block" style="font-size: 15px;">Loker #{{ $l->locker_number }}</span>
            <small class="badge badge-light text-success font-weight-bold mt-1">Tersedia</small>
          </button>
        @elseif($l->status == 'terpakai')
          <div class="p-3 shadow-sm rounded" style="border-radius: 12px; background: linear-gradient(135deg, #ef4444, #dc2626); color: white; opacity: 0.85;">
            <span class="icon-lock h4 d-block mb-1"></span>
            <span class="font-weight-bold d-block" style="font-size: 15px;">Loker #{{ $l->locker_number }}</span>
            <small class="badge badge-light text-danger font-weight-bold mt-1">Terpakai</small>
          </div>
        @else
          <div class="p-3 shadow-sm rounded" style="border-radius: 12px; background: linear-gradient(135deg, #f59e0b, #d97706); color: white; opacity: 0.85;">
            <span class="icon-lock h4 d-block mb-1"></span>
            <span class="font-weight-bold d-block" style="font-size: 15px;">Loker #{{ $l->locker_number }}</span>
            <small class="badge badge-light text-warning font-weight-bold mt-1">Rusak</small>
          </div>
        @endif
      </div>
    @empty
      <div class="col-12 text-center py-5 text-muted">
        Belum ada unit loker terdaftar di master data.
      </div>
    @endforelse
  </div>
</div>

<!-- Modal Checkout Sewa Loker -->
<div class="modal fade" id="rentLockerModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
      <div class="modal-header bg-primary text-white p-4">
        <div>
          <h5 class="modal-title font-weight-bold text-white mb-0" id="rentModalTitle">Sewa Loker #01</h5>
          <small class="text-white-50">Lengkapi durasi dan konfirmasi PIN Digital otomatis.</small>
        </div>
        <button type="button" class="close text-white opacity-100" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="{{ route('member.lockers.rent') }}" method="POST">
        @csrf
        <input type="hidden" name="locker_id" id="modalLockerId">

        <div class="modal-body p-4">
          @if($member->membership_tier === 'premium')
            <div class="alert alert-success border-0 p-3 mb-3 rounded" style="background-color: #d1fae5; color: #065f46;">
              <strong class="d-block" style="font-size: 13.5px;">🎁 Hak Akses Spesial Tier Premium!</strong>
              <small>Sebagai member Tier Premium, Anda mendapatkan fasiltas <strong>GRATIS Sewa Loker Bulanan (Rp 0)</strong>!</small>
            </div>
          @endif

          <div class="form-group mb-3">
            <label class="font-weight-bold text-dark small">Pilihan Opsi Sewa</label>
            <select name="rental_type" id="modalRentalType" class="form-control form-control-lg" required>
              <option value="daily">Harian (1 Hari - Rp 15.000 / sekali bayar)</option>
              <option value="monthly" {{ $member->membership_tier === 'premium' ? 'selected' : '' }}>
                Bulanan (30 Hari - {{ $member->membership_tier === 'premium' ? 'GRATIS (Bundling Premium)' : 'Rp 150.000' }})
              </option>
            </select>
          </div>

          <div class="p-3 bg-light rounded text-center my-3">
            <small class="text-muted text-uppercase font-weight-bold d-block mb-1" style="font-size: 11px;">PREVIEW GENERATED ACCESS PIN</small>
            <span class="h3 font-weight-bold text-primary mb-0" style="letter-spacing: 4px;">******</span>
            <small class="text-muted d-block mt-1">PIN 6-digit otomatis dibuat dan disimpan di dasbor Anda setelah checkout.</small>
          </div>
        </div>

        <div class="modal-footer bg-light px-4 py-3">
          <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success font-weight-bold px-4 shadow-sm">Bayar & Aktifkan Loker</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  $(document).ready(function() {
    $('.btn-select-locker').on('click', function() {
      const id = $(this).data('id');
      const number = $(this).data('number');

      $('#modalLockerId').val(id);
      $('#rentModalTitle').text('Sewa Loker #' + number);
    });
  });
</script>
@endsection
