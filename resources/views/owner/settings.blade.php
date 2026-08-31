@extends('layouts.admin')

@section('title', 'Profil Gym (Owner) &mdash; PetGym')
@section('page_title', 'Informasi & Profil Tenant Gym')
@section('page_subtitle', 'Rincian data pendaftaran tenant gym, subdomain, email pemilik, dan paket langganan SaaS aktif (Mode Pemantauan & Read-Only)')

@section('content')
<!-- Banner Mode Pemantauan Eksekutif -->


<div class="row">
  <div class="col-md-7">
    <div class="card-custom mb-4">
      <h6 class="font-weight-bold text-dark mb-4">Rincian Identitas Tenant Gym</h6>

      <div class="row">
        <div class="col-md-6 mb-3">
          <small class="text-uppercase text-muted font-weight-bold" style="font-size: 10.5px; letter-spacing: 0.5px;">Nama Gym / Tenant</small>
          <h6 class="font-weight-bold text-dark mb-0 mt-1">{{ $tenant->name }}</h6>
        </div>

        <div class="col-md-6 mb-3">
          <small class="text-uppercase text-muted font-weight-bold" style="font-size: 10.5px; letter-spacing: 0.5px;">Subdomain</small>
          <h6 class="font-weight-bold text-primary mb-0 mt-1">{{ $tenant->subdomain }}</h6>
        </div>

        <div class="col-md-6 mb-3">
          <small class="text-uppercase text-muted font-weight-bold" style="font-size: 10.5px; letter-spacing: 0.5px;">Nama Pemilik</small>
          <h6 class="font-weight-bold text-dark mb-0 mt-1">{{ $tenant->owner_name ?? '-' }}</h6>
        </div>

        <div class="col-md-6 mb-3">
          <small class="text-uppercase text-muted font-weight-bold" style="font-size: 10.5px; letter-spacing: 0.5px;">Email Resmi Owner</small>
          <h6 class="font-weight-bold text-dark mb-0 mt-1">{{ $tenant->owner_email ? \App\Helpers\PrivacyHelper::maskEmail($tenant->owner_email) : '-' }}</h6>
        </div>

        <div class="col-md-6 mb-3">
          <small class="text-uppercase text-muted font-weight-bold" style="font-size: 10.5px; letter-spacing: 0.5px;">Tanggal Bergabung</small>
          <h6 class="font-weight-bold text-dark mb-0 mt-1">{{ $tenant->joined_at ? \Carbon\Carbon::parse($tenant->joined_at)->format('d M Y') : 'Hari Ini' }}</h6>
        </div>

        <div class="col-md-6 mb-3">
          <small class="text-uppercase text-muted font-weight-bold" style="font-size: 10.5px; letter-spacing: 0.5px;">Status Akun Tenant</small>
          <div>
            @if($tenant->status == 'active')
              <span class="badge badge-success px-3 py-1 font-weight-bold" style="font-size: 12px;">Aktif</span>
            @else
              <span class="badge badge-danger px-3 py-1 font-weight-bold" style="font-size: 12px;">Suspended</span>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-5">
    <div class="card-custom mb-4">
      <h6 class="font-weight-bold text-dark mb-3">Paket SaaS & Fitur Modul Aktif</h6>
      <div class="p-3 bg-light rounded mb-3">
        <small class="text-muted d-block text-uppercase font-weight-bold" style="font-size: 10px;">Paket Sewa Terdaftar</small>
        <span class="h5 font-weight-bold text-primary mb-0">{{ $tenant->plan_name ?? 'Paket Basic' }}</span>
      </div>

      <small class="text-uppercase text-muted font-weight-bold d-block mb-2" style="font-size: 11px;">Modul Fitur Terintegrasi Aktif:</small>
      <div class="d-flex flex-wrap gap-1">
        @if(is_array($tenant->features))
          @foreach($tenant->features as $feat)
            <span class="badge badge-light border text-dark px-2 py-1 mb-1" style="font-size: 11px;">✔ {{ $feat }}</span>
          @endforeach
        @else
          <span class="badge badge-light border text-dark px-2 py-1">✔ Manajemen Kelas & Sesi</span>
          <span class="badge badge-light border text-dark px-2 py-1">✔ POS Kasir Sederhana</span>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
