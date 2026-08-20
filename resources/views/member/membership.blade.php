@extends('layouts.admin')

@section('title', 'Paket Keanggotaan Member &mdash; PetGym')
@section('page_title', 'Multi-Tier Membership & Paket Keanggotaan')
@section('page_subtitle', 'Pilih tier keanggotaan gym yang sesuai dengan kebutuhan latihan Anda. Nikmati benefit bundling eksklusif.')

@section('content')
<!-- Active Tier Banner -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 15px; background: linear-gradient(135deg, #0ea5e9, #0284c7); color: #fff;">
  <div class="card-body p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center">
    <div>
      <span class="badge badge-light text-primary font-weight-bold uppercase mb-2">STATUS TIER SAAT INI</span>
      <h3 class="font-weight-bold text-white mb-1">Paket Keanggotaan Tier {{ strtoupper($member->membership_tier ?? 'Basic') }}</h3>
      <p class="mb-0 text-white-50">Berlaku hingga: <strong>{{ $member->expired_at ? $member->expired_at->format('d M Y') : '-' }}</strong> (Sisa {{ $member->days_left }} hari)</p>
    </div>
    <div class="mt-3 mt-md-0">
      <span class="badge badge-success px-4 py-2 font-weight-bold" style="font-size: 14px;">Status Aktif</span>
    </div>
  </div>
</div>

<!-- Tier Selector Cards -->
<div class="row align-items-stretch">
  <!-- Basic Tier -->
  <div class="col-lg-4 mb-4">
    <div class="card h-100 border-0 shadow-sm {{ $member->membership_tier === 'basic' ? 'border-primary' : '' }}" style="border-radius: 15px; overflow: hidden; {{ $member->membership_tier === 'basic' ? 'border-width: 2px;' : '' }}">
      <div class="card-body p-4 d-flex flex-column">
        <h3 class="font-weight-bold text-dark mb-2">Tier Basic</h3>
        <p class="text-muted small mb-3">Akses dasar peralatan fitness gym & locker harian.</p>
        <div class="mb-4">
          <span class="h2 font-weight-bold text-primary">Rp 500.000</span>
          <span class="text-muted"> / bulan</span>
        </div>
        <ul class="list-unstyled mb-4 text-left text-dark" style="line-height: 2; font-size: 13.5px;">
          <li>✔ Akses Gym 7 Hari Seminggu</li>
          <li>✔ Penggunaan Peralatan Beban & Kardio</li>
          <li>✔ Opsi Sewa Loker Harian (Rp 15.000)</li>
        </ul>
        @if($member->membership_tier === 'basic')
          <button type="button" class="btn btn-secondary btn-block py-3 mt-auto font-weight-bold" disabled style="border-radius: 30px;">Tier Saat Ini</button>
        @else
          <form action="{{ route('member.membership.upgrade') }}" method="POST" class="mt-auto">
            @csrf
            <input type="hidden" name="tier" value="basic">
            <button type="submit" class="btn btn-outline-primary btn-block py-3 font-weight-bold" style="border-radius: 30px;" onclick="return confirm('Pilih Paket Tier Basic?')">Pilih Basic</button>
          </form>
        @endif
      </div>
    </div>
  </div>

  <!-- Standard Tier -->
  <div class="col-lg-4 mb-4">
    <div class="card h-100 border-0 shadow-sm {{ $member->membership_tier === 'standard' ? 'border-primary' : '' }}" style="border-radius: 15px; overflow: hidden; {{ $member->membership_tier === 'standard' ? 'border-width: 2px;' : '' }}">
      <div class="card-body p-4 d-flex flex-column">
        <h3 class="font-weight-bold text-dark mb-2">Tier Standard</h3>
        <p class="text-muted small mb-3">Pilihan populer untuk hasil kebugaran optimal.</p>
        <div class="mb-4">
          <span class="h2 font-weight-bold text-primary">Rp 1.200.000</span>
          <span class="text-muted"> / bulan</span>
        </div>
        <ul class="list-unstyled mb-4 text-left text-dark" style="line-height: 2; font-size: 13.5px;">
          <li>✔ Semua fitur Tier Basic</li>
          <li>✔ Bebas Ikut Seluruh Kelas Kebugaran (Yoga/Zumba)</li>
          <li>✔ Diskon 10% Pembelian Sesi PT</li>
          <li>✔ Opsi Sewa Loker Bulanan</li>
        </ul>
        @if($member->membership_tier === 'standard')
          <button type="button" class="btn btn-secondary btn-block py-3 mt-auto font-weight-bold" disabled style="border-radius: 30px;">Tier Saat Ini</button>
        @else
          <form action="{{ route('member.membership.upgrade') }}" method="POST" class="mt-auto">
            @csrf
            <input type="hidden" name="tier" value="standard">
            <button type="submit" class="btn btn-primary btn-block py-3 font-weight-bold shadow-sm" style="border-radius: 30px;" onclick="return confirm('Pilih Paket Tier Standard?')">Pilih Standard</button>
          </form>
        @endif
      </div>
    </div>
  </div>

  <!-- Premium Tier -->
  <div class="col-lg-4 mb-4">
    <div class="card h-100 border-primary shadow {{ $member->membership_tier === 'premium' ? 'border-success' : '' }}" style="border-radius: 15px; overflow: hidden; border-width: 2px; transform: scale(1.02);">
      <div class="bg-primary text-white text-center py-2 font-weight-bold uppercase small" style="letter-spacing: 1px;">
        ⭐ BUNDLING SEWA LOKER GRATIS
      </div>
      <div class="card-body p-4 d-flex flex-column">
        <h3 class="font-weight-bold text-dark mb-2">Tier Premium</h3>
        <p class="text-muted small mb-3">Pengalaman All-Inclusive kebugaran terlengkap.</p>
        <div class="mb-4">
          <span class="h2 font-weight-bold text-primary">Rp 2.500.000</span>
          <span class="text-muted"> / bulan</span>
        </div>
        <ul class="list-unstyled mb-4 text-left text-dark" style="line-height: 2; font-size: 13.5px;">
          <li>✔ Semua fitur Tier Standard</li>
          <li class="font-weight-bold text-success">🎁 GRATIS Sewa Loker Bulanan (Bundling Auto-Free)</li>
          <li>✔ Prioritas Booking Kelas & PT</li>
          <li>✔ 1 Sesi Konsultasi Nutrisi Gratis</li>
        </ul>
        @if($member->membership_tier === 'premium')
          <button type="button" class="btn btn-secondary btn-block py-3 mt-auto font-weight-bold" disabled style="border-radius: 30px;">Tier Saat Ini</button>
        @else
          <form action="{{ route('member.membership.upgrade') }}" method="POST" class="mt-auto">
            @csrf
            <input type="hidden" name="tier" value="premium">
            <button type="submit" class="btn btn-success btn-block py-3 font-weight-bold shadow" style="border-radius: 30px;" onclick="return confirm('Pilih Paket Tier Premium?')">Upgrade ke Premium</button>
          </form>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
