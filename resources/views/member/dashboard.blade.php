@extends('layouts.member')

@section('title', 'Beranda Anggota')
@section('page_title', 'Beranda')
@section('page_subtitle', 'Ringkasan aktivitas keanggotaan dan jadwal latihan Anda')

@section('content')
<!-- Active Locker Banner (Only when active) -->
@if($activeRental && $hasActiveSubscription)
<div class="card-custom border-left border-primary mb-3 mb-md-4" style="border-left-width: 4px !important;">
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
    <div>
      <span class="badge badge-primary font-weight-bold text-uppercase mb-2">Akses Loker Digital Aktif</span>
      <h4 class="font-weight-bold text-dark mb-1">Nomor Loker: <span class="text-primary">#{{ $activeRental->locker ? $activeRental->locker->locker_number : '-' }}</span></h4>
      <p class="mb-0 text-muted small">Gunakan kode PIN digital ini pada loker IoT atau konfirmasi ke resepsionis gym.</p>
    </div>
    <div class="mt-3 mt-md-0 text-md-right">
      <small class="text-muted text-uppercase font-weight-bold d-block mb-1">Kode PIN Akses (6 Digit)</small>
      <div class="d-inline-block bg-light text-dark font-weight-bold px-3 py-2 rounded border" style="font-size: 20px; letter-spacing: 4px;">
        {{ $activeRental->pin_code ?? '849201' }}
      </div>
      <div class="mt-1 text-muted small">Berlaku sampai: {{ \Carbon\Carbon::parse($activeRental->end_date)->format('d M Y') }}</div>
    </div>
  </div>
</div>
@endif

<!-- Key Metrics Grid -->
<div class="row mb-3 mb-md-4">
  <div class="col-12 col-md-4 mb-3 mb-md-0">
    <div class="card-custom h-100">
      <div class="text-muted text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Status Keanggotaan</div>
      @if($hasActiveSubscription)
        <h3 class="font-weight-bold text-dark mb-1 mt-2">Tingkat {{ ucfirst($member->membership_tier ?? 'Basic') }}</h3>
        <div class="d-flex align-items-center justify-content-between mt-2">
          <span class="badge badge-success font-weight-bold">Aktif</span>
          <span class="text-muted small">Sisa {{ $member->days_left }} Hari</span>
        </div>
      @else
        <h3 class="font-weight-bold text-warning mb-1 mt-2" style="font-size: 1.35rem;">Belum Berlangganan</h3>
        <div class="d-flex align-items-center justify-content-between mt-2">
          <span class="badge badge-secondary font-weight-bold">Menunggu Kasir</span>
          <a href="{{ route('member.membership') }}" class="btn btn-sm btn-primary py-1 px-2 font-weight-bold" style="font-size: 11px;">Pilih Paket &rarr;</a>
        </div>
      @endif
    </div>
  </div>

  <div class="col-12 col-md-4 mb-3 mb-md-0">
    <div class="card-custom h-100">
      <div class="text-muted text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Paket Pelatih Pribadi</div>
      <h3 class="font-weight-bold text-primary mb-1 mt-2">{{ $ptQuotas->sum('remaining_sessions') }} Sesi</h3>
      <div class="d-flex align-items-center justify-content-between mt-2">
        <span class="text-muted small">Dari {{ $ptQuotas->count() }} paket pelatih</span>
        @if($hasActiveSubscription)
          <a href="{{ route('member.pt') }}" class="btn btn-sm btn-outline-primary py-1 px-2 font-weight-bold" style="border-radius: 6px; font-size: 12px;">Beli Sesi</a>
        @else
          <span class="badge badge-light border text-muted" style="font-size: 11px;">Perlu Langganan</span>
        @endif
      </div>
    </div>
  </div>

  <div class="col-12 col-md-4">
    <div class="card-custom h-100">
      <div class="text-muted text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Kode Akses Member</div>
      <h3 class="font-weight-bold text-dark mb-1 mt-2" style="letter-spacing: 1px;">
        {{ $member->access_code ?? 'MBR-000000' }}
      </h3>
      <div class="mt-2">
        <span class="badge badge-info text-white font-weight-bold">Tunjukkan ke Kasir Gym</span>
      </div>
    </div>
  </div>
</div>

@if(!$hasActiveSubscription)
<!-- Paket Membership di Kasir Gym Section -->
<div class="card-custom mb-3 mb-md-4 border-primary">
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 pb-2 border-bottom">
    <div>
      <h5 class="font-weight-bold text-dark mb-1">
        <span class="icon-file-text text-primary mr-1"></span> Pilihan Paket Langganan di Kasir
      </h5>
      <p class="text-muted small mb-0">Pilih paket keanggotaan di bawah ini dan lakukan pembayaran langsung di meja kasir gym.</p>
    </div>
    <div class="mt-2 mt-md-0">
      <a href="{{ route('member.membership') }}" class="btn btn-sm btn-outline-primary font-weight-bold">
        Detail Lengkap Paket &rarr;
      </a>
    </div>
  </div>

  <div class="row">
    @if(isset($cashierPlans) && $cashierPlans->count() > 0)
      @foreach($cashierPlans as $plan)
        <div class="col-12 col-md-4 mb-3 mb-md-0">
          <div class="card border h-100 p-3 shadow-sm text-center" style="border-radius: 12px; background: var(--mp-box-light);">
            <h6 class="font-weight-bold text-dark mb-1">{{ $plan->name }}</h6>
            <div class="my-2">
              <span class="h4 font-weight-bold text-primary">Rp {{ number_format($plan->price, 0, ',', '.') }}</span>
            </div>
            <p class="text-muted small mb-3">Tersedia untuk aktivasi langsung di Kasir.</p>
            <button type="button" class="btn btn-sm btn-primary btn-block font-weight-bold" data-toggle="modal" data-target="#cashierActivationModal" data-plan-name="{{ $plan->name }}" data-plan-price="Rp {{ number_format($plan->price, 0, ',', '.') }}">
              Aktivasi di Kasir
            </button>
          </div>
        </div>
      @endforeach
    @else
      <div class="col-12 col-md-4 mb-3 mb-md-0">
        <div class="card border h-100 p-3 shadow-sm text-center" style="border-radius: 12px; background: var(--mp-box-light);">
          <h6 class="font-weight-bold text-dark mb-1">Tingkat Basic</h6>
          <div class="my-2">
            <span class="h4 font-weight-bold text-primary">Rp 500.000</span>
            <small class="text-muted">/ bulan</small>
          </div>
          <p class="text-muted small mb-3">Akses beban & kardio harian.</p>
          <button type="button" class="btn btn-sm btn-primary btn-block font-weight-bold" data-toggle="modal" data-target="#cashierActivationModal" data-plan-name="Tingkat Basic" data-plan-price="Rp 500.000">
            Aktivasi di Kasir
          </button>
        </div>
      </div>
      <div class="col-12 col-md-4 mb-3 mb-md-0">
        <div class="card border border-primary h-100 p-3 shadow-sm text-center" style="border-radius: 12px; background: var(--mp-box-light);">
          <span class="badge badge-primary font-weight-bold mb-2">Paling Populer</span>
          <h6 class="font-weight-bold text-dark mb-1">Tingkat Standard</h6>
          <div class="my-2">
            <span class="h4 font-weight-bold text-primary">Rp 1.200.000</span>
            <small class="text-muted">/ bulan</small>
          </div>
          <p class="text-muted small mb-3">Semua fasilitas + Bebas kelas gym.</p>
          <button type="button" class="btn btn-sm btn-primary btn-block font-weight-bold" data-toggle="modal" data-target="#cashierActivationModal" data-plan-name="Tingkat Standard" data-plan-price="Rp 1.200.000">
            Aktivasi di Kasir
          </button>
        </div>
      </div>
      <div class="col-12 col-md-4">
        <div class="card border h-100 p-3 shadow-sm text-center" style="border-radius: 12px; background: var(--mp-box-light);">
          <h6 class="font-weight-bold text-dark mb-1">Tingkat Premium</h6>
          <div class="my-2">
            <span class="h4 font-weight-bold text-primary">Rp 2.500.000</span>
            <small class="text-muted">/ bulan</small>
          </div>
          <p class="text-muted small mb-3">Fasilitas lengkap + Gratis Loker bulanan.</p>
          <button type="button" class="btn btn-sm btn-primary btn-block font-weight-bold" data-toggle="modal" data-target="#cashierActivationModal" data-plan-name="Tingkat Premium" data-plan-price="Rp 2.500.000">
            Aktivasi di Kasir
          </button>
        </div>
      </div>
    @endif
  </div>
</div>
@endif

<!-- Dashboard Shortcut Cards -->
<div class="card-custom mb-3 mb-md-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="font-weight-bold text-dark mb-0">Menu Pintas Anggota</h6>
  </div>
  <div class="row">
    <div class="col-12 col-sm-4 mb-3 mb-sm-0">
      <a href="{{ $hasActiveSubscription ? route('member.lockers') : route('member.membership') }}" class="card border text-decoration-none p-3 h-100 shadow-sm text-left {{ !$hasActiveSubscription ? 'opacity-75' : '' }}" style="border-radius: 12px; transition: transform 0.2s ease;">
        <div class="d-flex align-items-center">
          <div class="rounded-circle p-3 mr-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: rgba(244, 63, 94, 0.08); color: var(--brand-red);">
            <span class="icon-lock" style="font-size: 20px;"></span>
          </div>
          <div>
            <div class="d-flex align-items-center">
              <h6 class="font-weight-bold text-dark mb-1" style="font-size: 14px;">Loker</h6>
              @if(!$hasActiveSubscription)
                <span class="badge badge-secondary ml-2" style="font-size: 10px;">Terkunci</span>
              @endif
            </div>
            <small class="text-muted">Sewa & Akses PIN Loker</small>
          </div>
        </div>
      </a>
    </div>

    <div class="col-12 col-sm-4 mb-3 mb-sm-0">
      <a href="{{ $hasActiveSubscription ? route('member.pt') : route('member.membership') }}" class="card border text-decoration-none p-3 h-100 shadow-sm text-left {{ !$hasActiveSubscription ? 'opacity-75' : '' }}" style="border-radius: 12px; transition: transform 0.2s ease;">
        <div class="d-flex align-items-center">
          <div class="rounded-circle p-3 mr-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: rgba(244, 63, 94, 0.08); color: var(--brand-red);">
            <span class="icon-user" style="font-size: 20px;"></span>
          </div>
          <div>
            <div class="d-flex align-items-center">
              <h6 class="font-weight-bold text-dark mb-1" style="font-size: 14px;">Pelatih Pribadi</h6>
              @if(!$hasActiveSubscription)
                <span class="badge badge-secondary ml-2" style="font-size: 10px;">Terkunci</span>
              @endif
            </div>
            <small class="text-muted">Reservasi Sesi Latihan PT</small>
          </div>
        </div>
      </a>
    </div>

    <div class="col-12 col-sm-4">
      <a href="{{ $hasActiveSubscription ? route('member.classes') : route('member.membership') }}" class="card border text-decoration-none p-3 h-100 shadow-sm text-left {{ !$hasActiveSubscription ? 'opacity-75' : '' }}" style="border-radius: 12px; transition: transform 0.2s ease;">
        <div class="d-flex align-items-center">
          <div class="rounded-circle p-3 mr-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: rgba(244, 63, 94, 0.08); color: var(--brand-red);">
            <span class="icon-calendar" style="font-size: 20px;"></span>
          </div>
          <div>
            <div class="d-flex align-items-center">
              <h6 class="font-weight-bold text-dark mb-1" style="font-size: 14px;">Kelas Kebugaran</h6>
              @if(!$hasActiveSubscription)
                <span class="badge badge-secondary ml-2" style="font-size: 10px;">Terkunci</span>
              @endif
            </div>
            <small class="text-muted">Jadwal & Reservasi Kelas</small>
          </div>
        </div>
      </a>
    </div>
  </div>
</div>

<div class="row">
  <!-- Upcoming PT Bookings Card List -->
  <div class="col-12 col-md-6 mb-3 mb-md-4">
    <div class="card-custom h-100">
      <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
        <h6 class="font-weight-bold text-dark mb-0">Jadwal Pelatih Pribadi</h6>
        <a href="{{ route('member.pt') }}" class="btn btn-sm btn-link text-primary font-weight-bold p-0">Semua Jadwal &rarr;</a>
      </div>

      @forelse($upcomingPtBookings as $ptb)
        <div class="border rounded p-3 mb-2 bg-light d-flex justify-content-between align-items-center" style="border-radius: 10px !important;">
          <div class="d-flex align-items-center">
            <div class="rounded-circle bg-white text-primary p-2 mr-3 border d-flex align-items-center justify-content-center shadow-sm" style="width: 38px; height: 38px; font-weight: 800; font-size: 14px;">
              {{ substr($ptb->trainer ? $ptb->trainer->name : 'P', 0, 1) }}
            </div>
            <div>
              <h6 class="font-weight-bold text-dark mb-1" style="font-size: 13.5px;">{{ $ptb->trainer ? $ptb->trainer->name : 'Pelatih Pribadi' }}</h6>
              <small class="text-muted"><span class="icon-calendar mr-1"></span> {{ \Carbon\Carbon::parse($ptb->booking_date)->format('d M Y') }} &bull; <strong class="text-primary">{{ $ptb->booking_time }}</strong></small>
            </div>
          </div>
          <div class="ml-2">
            <span class="badge badge-success px-2 py-1 font-weight-bold" style="font-size: 11px;">Terjadwal</span>
          </div>
        </div>
      @empty
        <div class="text-center py-4 text-muted bg-light rounded border">
          <span class="icon-user d-block mb-1" style="font-size: 22px;"></span>
          <small class="d-block font-weight-semibold">Belum ada jadwal sesi latihan terdaftar.</small>
        </div>
      @endforelse
    </div>
  </div>

  <!-- Upcoming Class RSVPs Card List -->
  <div class="col-12 col-md-6 mb-3 mb-md-4">
    <div class="card-custom h-100">
      <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
        <h6 class="font-weight-bold text-dark mb-0">Daftar Kelas Kebugaran</h6>
        <a href="{{ route('member.classes') }}" class="btn btn-sm btn-link text-primary font-weight-bold p-0">Semua Kelas &rarr;</a>
      </div>

      @forelse($upcomingClassRsvps as $cr)
        <div class="border rounded p-3 mb-2 bg-light d-flex justify-content-between align-items-center" style="border-radius: 10px !important;">
          <div class="d-flex align-items-center">
            <div class="rounded-circle bg-white text-primary p-2 mr-3 border d-flex align-items-center justify-content-center shadow-sm" style="width: 38px; height: 38px; font-weight: 800; font-size: 14px;">
              <span class="icon-calendar"></span>
            </div>
            <div>
              <h6 class="font-weight-bold text-dark mb-1" style="font-size: 13.5px;">{{ $cr->gymClass ? $cr->gymClass->name : 'Kelas Gym' }}</h6>
              <small class="text-muted"><span class="icon-user mr-1"></span> {{ $cr->gymClass && $cr->gymClass->trainer ? $cr->gymClass->trainer->name : 'Instruktur Gym' }} &bull; {{ \Carbon\Carbon::parse($cr->class_date)->format('d M Y') }}</small>
            </div>
          </div>
          <div class="ml-2">
            @if($cr->status == 'confirmed')
              <span class="badge badge-success px-2 py-1 font-weight-bold" style="font-size: 11px;">Dikonfirmasi</span>
            @elseif($cr->status == 'waitlist')
              <span class="badge badge-warning px-2 py-1 font-weight-bold" style="font-size: 11px;">Antrean #{{ $cr->queue_position }}</span>
            @else
              <span class="badge badge-secondary px-2 py-1 font-weight-bold" style="font-size: 11px;">{{ ucfirst($cr->status) }}</span>
            @endif
          </div>
        </div>
      @empty
        <div class="text-center py-4 text-muted bg-light rounded border">
          <span class="icon-calendar d-block mb-1" style="font-size: 22px;"></span>
          <small class="d-block font-weight-semibold">Belum ada kelas yang diikuti.</small>
        </div>
      @endforelse
    </div>
  </div>
</div>

<!-- Modal Panduan Aktivasi di Kasir Gym -->
<div class="modal fade" id="cashierActivationModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-light">
        <div>
          <h5 class="modal-title font-weight-bold text-dark mb-0">Aktivasi Langganan di Kasir</h5>
          <small class="text-muted">Prosedur Pembayaran & Aktivasi Member</small>
        </div>
        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">&times;</button>
      </div>
      <div class="modal-body p-4 text-center">
        <div class="p-3 bg-light rounded mb-3 border">
          <small class="text-muted text-uppercase font-weight-bold d-block mb-1">Paket yang Dipilih</small>
          <h4 class="font-weight-bold text-dark mb-1" id="modalSelectedPlanName">Paket Membership</h4>
          <span class="h3 font-weight-bold text-primary mb-0" id="modalSelectedPlanPrice">Rp 0</span>
        </div>

        <div class="alert alert-info text-left small mb-3">
          <strong>Langkah Mudah Aktivasi:</strong>
          <ol class="mb-0 pl-3 mt-1 font-weight-semibold">
            <li>Kunjungi <strong>Meja Kasir / Resepsionis</strong> di gym.</li>
            <li>Sebutkan nama Anda atau tunjukkan <strong>PIN / Kode Akses</strong> berikut ke Kasir:</li>
          </ol>
        </div>

        <div class="p-3 rounded border mb-3" style="background: var(--mp-box-light);">
          <small class="text-muted text-uppercase font-weight-bold d-block mb-1">PIN / Kode Akses Member Anda</small>
          <div class="h3 font-weight-bold text-primary mb-0" style="letter-spacing: 2px;">
            {{ $member->access_code ?? 'MBR-000000' }}
          </div>
          <small class="text-muted">Nama: <strong>{{ $member->name }}</strong></small>
        </div>

        <p class="text-muted small mb-0">
          Kasir akan memproses transaksi POS (Cash / QRIS / Transfer) dan akun member Anda akan otomatis aktif seketika.
        </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-block font-weight-bold" data-dismiss="modal">Saya Mengerti</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  $('#cashierActivationModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var planName = button.data('plan-name') || 'Paket Membership';
    var planPrice = button.data('plan-price') || 'Rp 0';

    var modal = $(this);
    modal.find('#modalSelectedPlanName').text(planName);
    modal.find('#modalSelectedPlanPrice').text(planPrice);
  });
</script>
@endsection

