@extends('layouts.admin')

@section('title', 'Pengaturan Gym &mdash; PetGym')
@section('page_title', 'Pengaturan Profil Gym (Tenant Preferences)')
@section('page_subtitle', 'Kustomisasi nama gym, pemilik, kontak resmi, dan preferensi operasional')

@section('content')
<div class="row">
  <div class="col-md-8">
    <div class="card-custom">
      <h6 class="font-weight-bold text-dark mb-4">Profil & Identitas Gym</h6>

      <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Nama Gym / Tenant *</label>
          <input type="text" name="name" class="form-control" value="{{ $tenant->name ?? '' }}" required style="border-radius: 8px;">
        </div>

        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Subdomain Sistem SaaS</label>
          <input type="text" class="form-control bg-light" value="{{ $tenant->subdomain ?? 'subdomain.workout.id' }}" readonly style="border-radius: 8px;">
        </div>

        <div class="row">
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark" style="font-size: 13px;">Nama Pemilik (Owner) *</label>
            <input type="text" name="owner_name" class="form-control" value="{{ $tenant->owner_name ?? '' }}" required style="border-radius: 8px;">
          </div>
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark" style="font-size: 13px;">Email Resmi Owner *</label>
            <input type="email" name="owner_email" class="form-control" value="{{ $tenant->owner_email ?? '' }}" required style="border-radius: 8px;">
          </div>
        </div>

        <hr class="my-4">

        <h6 class="font-weight-bold text-dark mb-3">Jam Operasional Gym</h6>
        <div class="row">
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark" style="font-size: 13px;">Jam Buka Hari Kerja (Senin - Jumat)</label>
            <input type="text" class="form-control" value="06:00 - 22:00 WIB" style="border-radius: 8px;">
          </div>
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark" style="font-size: 13px;">Jam Buka Akhir Pekan (Sabtu - Minggu)</label>
            <input type="text" class="form-control" value="07:00 - 20:00 WIB" style="border-radius: 8px;">
          </div>
        </div>

        <button type="submit" class="btn btn-primary font-weight-bold px-4 py-2" style="border-radius: 8px;">
          Simpan Perubahan Pengaturan
        </button>
      </form>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card-custom">
      <h6 class="font-weight-bold text-dark mb-3">Paket Langganan Gym</h6>
      <div class="p-3 bg-light rounded mb-3">
        <small class="text-uppercase text-muted font-weight-bold" style="font-size: 10px;">Paket Sewa Aktif</small>
        <h4 class="font-weight-bold text-success mb-1">{{ $tenant->plan_name ?? 'Paket Basic' }}</h4>
        <small class="text-muted">Status: <span class="badge badge-success">{{ ucfirst($tenant->status ?? 'active') }}</span></small>
      </div>

      <div class="mb-3" style="font-size: 13px;">
        <div class="d-flex justify-content-between mb-1">
          <span class="text-muted">Tanggal Bergabung:</span>
          <span class="font-weight-bold text-dark">{{ $tenant->joined_at ? $tenant->joined_at->format('d M Y') : '-' }}</span>
        </div>
        <div class="d-flex justify-content-between mb-1">
          <span class="text-muted">Masa Kadaluarsa:</span>
          <span class="font-weight-bold text-dark">{{ $tenant->expires_at ? $tenant->expires_at->format('d M Y') : '-' }}</span>
        </div>
        <div class="d-flex justify-content-between">
          <span class="text-muted">Sisa Kuota:</span>
          <span class="font-weight-bold text-primary">{{ $tenant->expires_in_days }} Hari</span>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
