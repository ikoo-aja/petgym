@extends('layouts.member')

@section('title', 'Status Keanggotaan - PetGym')
@section('page_title', 'Status Keanggotaan')
@section('page_subtitle', '')
@section('member_tier_badge', 'Tier ' . strtoupper($member->membership_tier ?? 'Basic'))

@section('content')
<!-- Active Tier Banner -->
<div class="card-custom border-left border-primary mb-4" style="border-left-width: 4px !important;">
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
    <div>
      <span class="badge badge-primary font-weight-bold text-uppercase mb-2">Tier Aktif</span>
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

<!-- Benefit Matrix Section -->
<div class="card-custom">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="font-weight-bold text-dark mb-0">Fitur Tiap Tier</h6>
  </div>

  <!-- Mobile View (Cards) -->
  <div class="d-block d-md-none">
    <!-- Item 1 -->
    <div class="border rounded p-3 mb-3 bg-light" style="border-radius: 12px !important;">
      <div class="font-weight-bold text-dark mb-2" style="font-size: 13.5px;">Akses Fitnes & Beban</div>
      <div class="row text-center pt-2 border-top" style="font-size: 12px;">
        <div class="col-4 border-right">
          <span class="text-muted d-block small mb-1">Basic</span>
          <span class="badge badge-success px-2 py-1">Ya</span>
        </div>
        <div class="col-4 border-right">
          <span class="text-muted d-block small mb-1">Standard</span>
          <span class="badge badge-success px-2 py-1">Ya</span>
        </div>
        <div class="col-4">
          <span class="text-muted d-block small mb-1">Premium</span>
          <span class="badge badge-success px-2 py-1">Ya</span>
        </div>
      </div>
    </div>

    <!-- Item 2 -->
    <div class="border rounded p-3 mb-3 bg-light" style="border-radius: 12px !important;">
      <div class="font-weight-bold text-dark mb-2" style="font-size: 13.5px;">Kelas Kebugaran (Yoga/Zumba)</div>
      <div class="row text-center pt-2 border-top" style="font-size: 12px;">
        <div class="col-4 border-right">
          <span class="text-muted d-block small mb-1">Basic</span>
          <span class="badge badge-secondary px-2 py-1">Tidak</span>
        </div>
        <div class="col-4 border-right">
          <span class="text-muted d-block small mb-1">Standard</span>
          <span class="badge badge-success px-2 py-1">Bebas</span>
        </div>
        <div class="col-4">
          <span class="text-muted d-block small mb-1">Premium</span>
          <span class="badge badge-primary px-2 py-1">Prioritas</span>
        </div>
      </div>
    </div>

    <!-- Item 3 -->
    <div class="border rounded p-3 mb-3 bg-light" style="border-radius: 12px !important;">
      <div class="font-weight-bold text-dark mb-2" style="font-size: 13.5px;">Diskon Paket Personal Trainer</div>
      <div class="row text-center pt-2 border-top" style="font-size: 12px;">
        <div class="col-4 border-right">
          <span class="text-muted d-block small mb-1">Basic</span>
          <span class="badge badge-secondary px-2 py-1">Tidak</span>
        </div>
        <div class="col-4 border-right">
          <span class="text-muted d-block small mb-1">Standard</span>
          <span class="badge badge-info px-2 py-1">10%</span>
        </div>
        <div class="col-4">
          <span class="text-muted d-block small mb-1">Premium</span>
          <span class="badge badge-success px-2 py-1 font-weight-bold">20%</span>
        </div>
      </div>
    </div>

    <!-- Item 4 -->
    <div class="border rounded p-3 mb-2 bg-light" style="border-radius: 12px !important;">
      <div class="font-weight-bold text-dark mb-2" style="font-size: 13.5px;">Sewa Loker Bulanan</div>
      <div class="row text-center pt-2 border-top" style="font-size: 11.5px;">
        <div class="col-4 border-right">
          <span class="text-muted d-block small mb-1">Basic</span>
          <span class="text-muted font-weight-bold">Rp 150rb</span>
        </div>
        <div class="col-4 border-right">
          <span class="text-muted d-block small mb-1">Standard</span>
          <span class="text-muted font-weight-bold">Rp 150rb</span>
        </div>
        <div class="col-4">
          <span class="text-muted d-block small mb-1">Premium</span>
          <span class="badge badge-success px-2 py-1 font-weight-bold">GRATIS</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Desktop View (Full Matrix Table) -->
  <div class="table-responsive d-none d-md-block">
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
          <td><span class="badge badge-success">Ya</span></td>
          <td><span class="badge badge-success">Ya</span></td>
          <td><span class="badge badge-success">Ya</span></td>
        </tr>
        <tr>
          <td class="text-left font-weight-bold">Kelas Kebugaran (Yoga/Zumba)</td>
          <td><span class="badge badge-secondary">Tidak</span></td>
          <td><span class="badge badge-success">Ya (Bebas)</span></td>
          <td><span class="badge badge-primary">Ya (Prioritas)</span></td>
        </tr>
        <tr>
          <td class="text-left font-weight-bold">Diskon Paket PT</td>
          <td><span class="badge badge-secondary">Tidak</span></td>
          <td><span class="badge badge-info">10%</span></td>
          <td><span class="badge badge-success">20%</span></td>
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
