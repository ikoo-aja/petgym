@extends('layouts.member')

@section('title', 'Status Keanggotaan')
@section('page_title', 'Paket Keanggotaan')
@section('page_subtitle', 'Informasi masa aktif membership dan paket latihan Anda')

@section('content')
<!-- Active Tier Banner -->
<div class="card-custom border-left border-primary mb-3 mb-md-4" style="border-left-width: 4px !important;">
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
    <div>
      <span class="badge badge-primary font-weight-bold text-uppercase mb-2">Status Paket Keanggotaan</span>
      @if($hasActiveSubscription)
        <h3 class="font-weight-bold text-dark mb-1">Paket Tingkat {{ ucfirst($member->membership_tier ?? 'Basic') }}</h3>
        <p class="mb-0 text-muted small">Masa berlaku hingga: <strong>{{ $member->expired_at ? $member->expired_at->format('d M Y') : '-' }}</strong> (Sisa {{ $member->days_left }} hari)</p>
      @else
        <h3 class="font-weight-bold text-warning mb-1">Belum Ada Paket Langganan Aktif</h3>
        <p class="mb-0 text-muted small">Silakan pilih paket di bawah ini dan lakukan aktivasi pembayaran di <strong>Meja Kasir Gym</strong>.</p>
      @endif
    </div>
    <div class="mt-3 mt-md-0 text-md-right">
      @if($hasActiveSubscription)
        <span class="badge badge-success px-3 py-2 font-weight-bold" style="font-size: 13px;">Status Aktif</span>
      @else
        <div class="badge badge-light border text-dark px-3 py-2 font-weight-bold" style="font-size: 13px; letter-spacing: 1px;">
          KODE PIN: <span class="text-primary">{{ $member->access_code ?? 'MBR-000000' }}</span>
        </div>
      @endif
    </div>
  </div>
</div>

<!-- Tier Options Grid -->
<div class="row align-items-stretch mb-3 mb-md-4">
  <!-- Basic Tier -->
  <div class="col-12 col-lg-4 mb-3 mb-lg-0">
    <div class="card-custom h-100 d-flex flex-column {{ $member->membership_tier === 'basic' && $hasActiveSubscription ? 'border-primary' : '' }}">
      <div class="mb-2">
        <h4 class="font-weight-bold text-dark mb-1">Tingkat Basic</h4>
        <p class="text-muted small">Akses dasar fasilitas gym dan opsi sewa loker harian.</p>
      </div>
      <div class="mb-3">
        <span class="h3 font-weight-bold text-primary">Rp 500.000</span>
        <span class="text-muted small"> / bulan</span>
      </div>
      <ul class="list-unstyled mb-4 text-dark small" style="line-height: 2;">
        <li><span class="icon-check text-success mr-2"></span> Akses Gym 7 Hari Seminggu</li>
        <li><span class="icon-check text-success mr-2"></span> Fasilitas Beban & Kardio</li>
        <li><span class="icon-check text-success mr-2"></span> Opsi Sewa Loker Harian (Rp 15.000)</li>
      </ul>
      <div class="mt-auto">
        @if($member->membership_tier === 'basic' && $hasActiveSubscription)
          <button type="button" class="btn btn-secondary btn-block py-2 font-weight-bold" disabled>Tingkat Saat Ini</button>
        @else
          <button type="button" class="btn btn-outline-primary btn-block py-2 font-weight-bold btn-upgrade-tier"
                  data-tier-name="Tingkat Basic"
                  data-price="Rp 500.000"
                  data-toggle="modal"
                  data-target="#membershipPaymentModal">
            Aktivasi Paket Basic di Kasir
          </button>
        @endif
      </div>
    </div>
  </div>

  <!-- Standard Tier -->
  <div class="col-12 col-lg-4 mb-3 mb-lg-0">
    <div class="card-custom h-100 d-flex flex-column {{ $member->membership_tier === 'standard' && $hasActiveSubscription ? 'border-primary' : '' }}">
      <div class="mb-2">
        <h4 class="font-weight-bold text-dark mb-1">Tingkat Standard</h4>
        <p class="text-muted small">Pilihan favorit untuk akses ke seluruh jadwal kelas kebugaran.</p>
      </div>
      <div class="mb-3">
        <span class="h3 font-weight-bold text-primary">Rp 1.200.000</span>
        <span class="text-muted small"> / bulan</span>
      </div>
      <ul class="list-unstyled mb-4 text-dark small" style="line-height: 2;">
        <li><span class="icon-check text-success mr-2"></span> Semua fasilitas Tingkat Basic</li>
        <li><span class="icon-check text-success mr-2"></span> Bebas Ikut Seluruh Kelas Kebugaran</li>
        <li><span class="icon-check text-success mr-2"></span> Diskon 10% Pembelian Sesi PT</li>
        <li><span class="icon-check text-success mr-2"></span> Opsi Sewa Loker Bulanan</li>
      </ul>
      <div class="mt-auto">
        @if($member->membership_tier === 'standard' && $hasActiveSubscription)
          <button type="button" class="btn btn-secondary btn-block py-2 font-weight-bold" disabled>Tingkat Saat Ini</button>
        @else
          <button type="button" class="btn btn-primary btn-block py-2 font-weight-bold btn-upgrade-tier"
                  data-tier-name="Tingkat Standard"
                  data-price="Rp 1.200.000"
                  data-toggle="modal"
                  data-target="#membershipPaymentModal">
            Aktivasi Paket Standard di Kasir
          </button>
        @endif
      </div>
    </div>
  </div>

  <!-- Premium Tier -->
  <div class="col-12 col-lg-4">
    <div class="card-custom h-100 d-flex flex-column border-primary position-relative" style="border-width: 2px !important;">
      <div class="mb-2">
        <h4 class="font-weight-bold text-dark mb-1">Tingkat Premium</h4>
        <p class="text-muted small">Paket serba lengkap dengan fasilitas loker gratis dan prioritas.</p>
      </div>
      <div class="mb-3">
        <span class="h3 font-weight-bold text-primary">Rp 2.500.000</span>
        <span class="text-muted small"> / bulan</span>
      </div>
      <ul class="list-unstyled mb-4 text-dark small" style="line-height: 2;">
        <li><span class="icon-check text-success mr-2"></span> Semua fasilitas Tingkat Standard</li>
        <li class="font-weight-bold text-success"><span class="icon-check text-success mr-2"></span> GRATIS Sewa Loker Bulanan</li>
        <li><span class="icon-check text-success mr-2"></span> Prioritas Reservasi Kelas & PT</li>
        <li><span class="icon-check text-success mr-2"></span> 1 Sesi Konsultasi Nutrisi Gratis</li>
      </ul>
      <div class="mt-auto">
        @if($member->membership_tier === 'premium' && $hasActiveSubscription)
          <button type="button" class="btn btn-secondary btn-block py-2 font-weight-bold" disabled>Tingkat Saat Ini</button>
        @else
          <button type="button" class="btn btn-primary btn-block py-2 font-weight-bold btn-upgrade-tier"
                  data-tier-name="Tingkat Premium"
                  data-price="Rp 2.500.000"
                  data-toggle="modal"
                  data-target="#membershipPaymentModal">
            Aktivasi ke Premium di Kasir
          </button>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Modal Informasi Aktivasi Kasir -->
<div class="modal fade" id="membershipPaymentModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-light">
        <div>
          <h5 class="modal-title font-weight-bold text-dark mb-0">Aktivasi Paket di Kasir Gym</h5>
          <small class="text-muted">Prosedur Pembayaran & Aktivasi Member</small>
        </div>
        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">&times;</button>
      </div>

      <div class="modal-body p-4 text-center">
        <div class="p-3 bg-light rounded text-center mb-3 border">
          <small class="text-muted text-uppercase font-weight-bold d-block mb-1">Paket yang Dipilih</small>
          <h4 class="font-weight-bold text-dark mb-1" id="modalUpgradeTierName">Tingkat Standard</h4>
          <span class="h3 font-weight-bold text-primary mb-0" id="modalUpgradePrice">Rp 1.200.000</span>
          <small class="text-muted d-block mt-1">Masa aktif 30 hari kalender</small>
        </div>

        <div class="alert alert-info text-left small mb-3">
          <strong>Langkah Aktivasi di Gym:</strong>
          <ol class="mb-0 pl-3 mt-1 font-weight-semibold">
            <li>Kunjungi <strong>Meja Kasir / Resepsionis</strong> di gym.</li>
            <li>Tunjukkan atau sebutkan <strong>PIN / Kode Akses</strong> member Anda di bawah ini:</li>
          </ol>
        </div>

        <div class="p-3 rounded border mb-3" style="background: var(--mp-box-light);">
          <small class="text-muted text-uppercase font-weight-bold d-block mb-1">PIN / KODE AKSES MEMBER ANDA</small>
          <div class="h3 font-weight-bold text-primary mb-0" style="letter-spacing: 2px;">
            {{ $member->access_code ?? 'MBR-000000' }}
          </div>
          <small class="text-muted">Nama: <strong>{{ $member->name }}</strong></small>
        </div>

        <p class="text-muted small mb-0">
          Kasir akan memilih paket Anda dan memproses pembayaran (Tunai, QRIS, atau Transfer Bank). Paket akan langsung aktif seketika setelah kasir menyelesaikan transaksi POS.
        </p>
      </div>

      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-primary btn-block font-weight-bold" data-dismiss="modal">Saya Mengerti</button>
      </div>
    </div>
  </div>
</div>

<!-- Benefit Matrix Section -->
<div class="card-custom">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="font-weight-bold text-dark mb-0">Perbandingan Fasilitas Tiap Tingkat</h6>
  </div>

  <!-- Mobile View (Cards) -->
  <div class="d-block d-md-none">
    <div class="border rounded p-3 mb-2 bg-light" style="border-radius: 10px !important;">
      <div class="font-weight-bold text-dark mb-2" style="font-size: 13.5px;">Akses Fasilitas Gym</div>
      <div class="row text-center pt-2 border-top" style="font-size: 12px;">
        <div class="col-4 border-right">
          <span class="text-muted d-block small mb-1">Basic</span>
          <span class="badge badge-success px-2 py-1">Tersedia</span>
        </div>
        <div class="col-4 border-right">
          <span class="text-muted d-block small mb-1">Standard</span>
          <span class="badge badge-success px-2 py-1">Tersedia</span>
        </div>
        <div class="col-4">
          <span class="text-muted d-block small mb-1">Premium</span>
          <span class="badge badge-success px-2 py-1">Tersedia</span>
        </div>
      </div>
    </div>

    <div class="border rounded p-3 mb-2 bg-light" style="border-radius: 10px !important;">
      <div class="font-weight-bold text-dark mb-2" style="font-size: 13.5px;">Kelas Kebugaran</div>
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

    <div class="border rounded p-3 mb-2 bg-light" style="border-radius: 10px !important;">
      <div class="font-weight-bold text-dark mb-2" style="font-size: 13.5px;">Diskon Sesi Pelatih Pribadi</div>
      <div class="row text-center pt-2 border-top" style="font-size: 12px;">
        <div class="col-4 border-right">
          <span class="text-muted d-block small mb-1">Basic</span>
          <span class="badge badge-secondary px-2 py-1">-</span>
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

    <div class="border rounded p-3 bg-light" style="border-radius: 10px !important;">
      <div class="font-weight-bold text-dark mb-2" style="font-size: 13.5px;">Sewa Loker Bulanan</div>
      <div class="row text-center pt-2 border-top" style="font-size: 12px;">
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
          <th class="text-left">Fasilitas / Keuntungan</th>
          <th>Basic</th>
          <th>Standard</th>
          <th>Premium</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="text-left font-weight-bold">Akses Fasilitas Gym</td>
          <td><span class="badge badge-success">Tersedia</span></td>
          <td><span class="badge badge-success">Tersedia</span></td>
          <td><span class="badge badge-success">Tersedia</span></td>
        </tr>
        <tr>
          <td class="text-left font-weight-bold">Kelas Kebugaran</td>
          <td><span class="badge badge-secondary">Tidak</span></td>
          <td><span class="badge badge-success">Bebas</span></td>
          <td><span class="badge badge-primary">Prioritas</span></td>
        </tr>
        <tr>
          <td class="text-left font-weight-bold">Diskon Paket Pelatih Pribadi</td>
          <td><span class="badge badge-secondary">-</span></td>
          <td><span class="badge badge-info">10%</span></td>
          <td><span class="badge badge-success">20%</span></td>
        </tr>
        <tr>
          <td class="text-left font-weight-bold">Sewa Loker Bulanan</td>
          <td>Berbayar (Rp 150.000)</td>
          <td>Berbayar (Rp 150.000)</td>
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
      const tierName = $(this).data('tier-name') || 'Paket Membership';
      const price = $(this).data('price') || 'Rp 0';

      $('#modalUpgradeTierName').text(tierName);
      $('#modalUpgradePrice').text(price);
    });
  });
</script>
@endsection

