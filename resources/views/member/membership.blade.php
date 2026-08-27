@extends('layouts.member')

@section('title', 'Paket Keanggotaan Member - PetGym')
@section('page_title', 'Paket Keanggotaan & Tier Status')
@section('page_subtitle', 'Pilih tier keanggotaan gym yang sesuai dengan kebutuhan latihan Anda dan selesaikan pembayaran.')
@section('member_tier_badge', 'Tier ' . strtoupper($member->membership_tier ?? 'Basic'))

@section('content')
<!-- Active Tier Banner -->
<div class="card-custom border-left border-primary mb-4" style="border-left-width: 4px !important;">
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
    <div>
      <span class="badge badge-primary font-weight-bold text-uppercase mb-2">Tier Keanggotaan Aktif Saat Ini</span>
      <h3 class="font-weight-bold text-dark mb-1">Paket Tier {{ strtoupper($member->membership_tier ?? 'Basic') }}</h3>
      <p class="mb-0 text-muted">Masa berlaku hingga: <strong>{{ $member->expired_at ? $member->expired_at->format('d M Y') : '-' }}</strong> (Sisa {{ $member->days_left }} hari)</p>
    </div>
    <div class="mt-3 mt-md-0">
      <span class="badge badge-success px-3 py-2 font-weight-bold" style="font-size: 13px;">Status Aktif</span>
    </div>
  </div>
</div>

<!-- Tier Options Grid -->
<div class="row align-items-stretch mb-4">
  <!-- Basic Tier -->
  <div class="col-lg-4 mb-4">
    <div class="card-custom h-100 d-flex flex-column {{ $member->membership_tier === 'basic' ? 'border-primary' : '' }}">
      <div class="mb-3">
        <h4 class="font-weight-bold text-dark mb-1">Tier Basic</h4>
        <p class="text-muted small">Akses dasar peralatan fitness gym & locker harian.</p>
      </div>
      <div class="mb-4">
        <span class="h3 font-weight-bold text-primary">Rp 500.000</span>
        <span class="text-muted"> / bulan</span>
      </div>
      <ul class="list-unstyled mb-4 text-dark" style="line-height: 2; font-size: 13.5px;">
        <li>- Akses Gym 7 Hari Seminggu</li>
        <li>- Penggunaan Peralatan Beban & Kardio</li>
        <li>- Opsi Sewa Loker Harian (Rp 15.000)</li>
      </ul>
      <div class="mt-auto">
        @if($member->membership_tier === 'basic')
          <button type="button" class="btn btn-secondary btn-block py-2 font-weight-bold" disabled>Tier Saat Ini</button>
        @else
          <button type="button" class="btn btn-outline-primary btn-block py-2 font-weight-bold btn-upgrade-tier"
                  data-tier="basic"
                  data-tier-name="Basic"
                  data-price="500000"
                  data-toggle="modal"
                  data-target="#membershipPaymentModal">
            Pilih Basic
          </button>
        @endif
      </div>
    </div>
  </div>

  <!-- Standard Tier -->
  <div class="col-lg-4 mb-4">
    <div class="card-custom h-100 d-flex flex-column {{ $member->membership_tier === 'standard' ? 'border-primary' : '' }}">
      <div class="mb-3">
        <h4 class="font-weight-bold text-dark mb-1">Tier Standard</h4>
        <p class="text-muted small">Pilihan populer untuk hasil kebugaran optimal.</p>
      </div>
      <div class="mb-4">
        <span class="h3 font-weight-bold text-primary">Rp 1.200.000</span>
        <span class="text-muted"> / bulan</span>
      </div>
      <ul class="list-unstyled mb-4 text-dark" style="line-height: 2; font-size: 13.5px;">
        <li>- Semua fitur Tier Basic</li>
        <li>- Bebas Ikut Seluruh Kelas Kebugaran</li>
        <li>- Diskon 10% Pembelian Sesi PT</li>
        <li>- Opsi Sewa Loker Bulanan</li>
      </ul>
      <div class="mt-auto">
        @if($member->membership_tier === 'standard')
          <button type="button" class="btn btn-secondary btn-block py-2 font-weight-bold" disabled>Tier Saat Ini</button>
        @else
          <button type="button" class="btn btn-primary btn-block py-2 font-weight-bold btn-upgrade-tier"
                  data-tier="standard"
                  data-tier-name="Standard"
                  data-price="1200000"
                  data-toggle="modal"
                  data-target="#membershipPaymentModal">
            Pilih Standard
          </button>
        @endif
      </div>
    </div>
  </div>

  <!-- Premium Tier -->
  <div class="col-lg-4 mb-4">
    <div class="card-custom h-100 d-flex flex-column border-primary position-relative" style="border-width: 2px !important;">
      <div class="position-absolute" style="top: -12px; right: 20px;">
        <span class="badge badge-primary font-weight-bold text-uppercase px-3 py-1">Gratis Loker Bulanan</span>
      </div>
      <div class="mb-3">
        <h4 class="font-weight-bold text-dark mb-1">Tier Premium</h4>
        <p class="text-muted small">Pengalaman All-Inclusive kebugaran terlengkap.</p>
      </div>
      <div class="mb-4">
        <span class="h3 font-weight-bold text-primary">Rp 2.500.000</span>
        <span class="text-muted"> / bulan</span>
      </div>
      <ul class="list-unstyled mb-4 text-dark" style="line-height: 2; font-size: 13.5px;">
        <li>- Semua fitur Tier Standard</li>
        <li class="font-weight-bold text-success">- GRATIS Sewa Loker Bulanan (Bundling Auto-Free)</li>
        <li>- Prioritas Booking Kelas & PT</li>
        <li>- 1 Sesi Konsultasi Nutrisi Gratis</li>
      </ul>
      <div class="mt-auto">
        @if($member->membership_tier === 'premium')
          <button type="button" class="btn btn-secondary btn-block py-2 font-weight-bold" disabled>Tier Saat Ini</button>
        @else
          <button type="button" class="btn btn-primary btn-block py-2 font-weight-bold btn-upgrade-tier"
                  data-tier="premium"
                  data-tier-name="Premium"
                  data-price="2500000"
                  data-toggle="modal"
                  data-target="#membershipPaymentModal">
            Upgrade Ke Premium
          </button>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Modal Gateway Pembayaran Keanggotaan -->
<div class="modal fade" id="membershipPaymentModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-light">
        <div>
          <h5 class="modal-title font-weight-bold text-dark mb-0">Pembayaran Upgrade Keanggotaan</h5>
          <small class="text-muted">Checkout Payment Gateway PetGym</small>
        </div>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
      </div>

      <form action="{{ route('member.membership.upgrade') }}" method="POST">
        @csrf
        <input type="hidden" name="tier" id="modalUpgradeTier">

        <div class="modal-body p-4">
          <div class="p-3 bg-light rounded text-center mb-3">
            <small class="text-muted text-uppercase font-weight-bold d-block mb-1">Paket Keanggotaan Yang Dipilih</small>
            <h4 class="font-weight-bold text-dark mb-1" id="modalUpgradeTierName">Tier Standard</h4>
            <span class="h3 font-weight-bold text-primary mb-0" id="modalUpgradePrice">Rp 1.200.000</span>
            <small class="text-muted d-block mt-1">Berlaku untuk 30 Hari Akses Gym</small>
          </div>

          <div class="form-group mb-3">
            <label class="font-weight-bold text-dark small">Metode Pembayaran</label>
            <select name="payment_method" class="form-control" required>
              <option value="qris">QRIS Standar Nasional (Scan QR Code)</option>
              <option value="transfer">Transfer Bank Virtual Account (BCA / Mandiri / BRI)</option>
              <option value="cash">Pembayaran Tunai di Kasir Resepsionis</option>
            </select>
          </div>

          <small class="text-muted d-block text-center">Setelah pembayaran dikonfirmasi, status tier keanggotaan Anda akan otomatis aktif.</small>
        </div>

        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary font-weight-bold">Konfirmasi & Bayar Sekarang</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Benefit Matrix Table -->
<div class="card-custom">
  <h6 class="font-weight-bold text-dark mb-3">Tabel Perbandingan Fitur Keanggotaan</h6>
  <div class="table-responsive">
    <table class="table table-bordered align-middle text-center mb-0" style="font-size: 13px;">
      <thead class="bg-light">
        <tr>
          <th class="text-left">Fitur / Benefit</th>
          <th>Basic</th>
          <th>Standard</th>
          <th>Premium</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="text-left font-weight-bold">Akses Fitnes & Beban</td>
          <td>Ya</td>
          <td>Ya</td>
          <td>Ya</td>
        </tr>
        <tr>
          <td class="text-left font-weight-bold">Kelas Kebugaran (Yoga/Zumba)</td>
          <td>Tidak</td>
          <td>Ya (Bebas)</td>
          <td>Ya (Prioritas)</td>
        </tr>
        <tr>
          <td class="text-left font-weight-bold">Diskon Paket PT</td>
          <td>Tidak</td>
          <td>10%</td>
          <td>20%</td>
        </tr>
        <tr>
          <td class="text-left font-weight-bold">Sewa Loker Bulanan</td>
          <td>Berbayar (Rp 150rb)</td>
          <td>Berbayar (Rp 150rb)</td>
          <td class="font-weight-bold text-success">GRATIS (Rp 0)</td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

@endsection

@section('scripts')
<script>
  $(document).ready(function() {
    $('.btn-upgrade-tier').on('click', function() {
      const tier = $(this).data('tier');
      const tierName = $(this).data('tier-name');
      const price = parseInt($(this).data('price'));

      $('#modalUpgradeTier').val(tier);
      $('#modalUpgradeTierName').text('Tier ' + tierName);
      $('#modalUpgradePrice').text('Rp ' + price.toLocaleString('id-ID'));
    });
  });
</script>
@endsection
