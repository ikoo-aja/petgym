@extends('layouts.admin')

@section('title', 'Pengaturan Gym &mdash; PetGym')
@section('page_title', 'Pengaturan Profil Gym (Tenant Preferences)')
@section('page_subtitle', 'Kustomisasi nama gym, pemilik, kontak resmi, dan preferensi operasional')

@section('content')
<div class="row">
  <div class="col-md-8">
    <div class="card-custom">
      @if(Auth::user() && Auth::user()->isOwner())
      <!-- Tampilan Khusus Owner (Murni Informasi Tanpa Form Input) -->
      <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <div>
          <h5 class="font-weight-bold text-dark mb-1">🏢 Profil & Informasi Resmi Gym</h5>
          <p class="text-muted mb-0" style="font-size: 12.5px;">Informasi identitas tenant gym dan preferensi operasional resmi</p>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-6 mb-3">
          <div class="p-3 border rounded bg-light">
            <small class="text-uppercase text-muted font-weight-bold" style="font-size: 10.5px; letter-spacing: 0.5px;">Nama Gym / Tenant</small>
            <h5 class="font-weight-bold text-dark mb-0 mt-1">{{ $tenant->name ?? '-' }}</h5>
          </div>
        </div>
        <div class="col-md-6 mb-3">
          <div class="p-3 border rounded bg-light">
            <small class="text-uppercase text-muted font-weight-bold" style="font-size: 10.5px; letter-spacing: 0.5px;">Subdomain SaaS</small>
            <h5 class="font-weight-bold text-primary mb-0 mt-1">{{ $tenant->subdomain ?? '-' }}</h5>
          </div>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-6 mb-3">
          <div class="p-3 border rounded bg-light">
            <small class="text-uppercase text-muted font-weight-bold" style="font-size: 10.5px; letter-spacing: 0.5px;">Nama Pemilik (Owner)</small>
            <h6 class="font-weight-bold text-dark mb-0 mt-1">{{ $tenant->owner_name ?? '-' }}</h6>
          </div>
        </div>
        <div class="col-md-6 mb-3">
          <div class="p-3 border rounded bg-light">
            <small class="text-uppercase text-muted font-weight-bold" style="font-size: 10.5px; letter-spacing: 0.5px;">Email Resmi Owner (Masked)</small>
            <h6 class="font-weight-bold text-dark mb-0 mt-1">{{ $tenant->owner_email ? \App\Helpers\PrivacyHelper::maskEmail($tenant->owner_email) : '-' }}</h6>
          </div>
        </div>
      </div>

      <hr class="my-4">

      <h6 class="font-weight-bold text-dark mb-3">⏰ Jam Operasional Gym</h6>
      <div class="row">
        <div class="col-md-6 mb-3">
          <div class="p-3 border rounded bg-light">
            <small class="text-uppercase text-muted font-weight-bold" style="font-size: 10.5px;">Hari Kerja (Senin - Jumat)</small>
            <div class="font-weight-bold text-dark mt-1" style="font-size: 14.5px;">06:00 - 22:00 WIB</div>
          </div>
        </div>
        <div class="col-md-6 mb-3">
          <div class="p-3 border rounded bg-light">
            <small class="text-uppercase text-muted font-weight-bold" style="font-size: 10.5px;">Akhir Pekan (Sabtu - Minggu)</small>
            <div class="font-weight-bold text-dark mt-1" style="font-size: 14.5px;">07:00 - 20:00 WIB</div>
          </div>
        </div>
      </div>

      @else
      <!-- Form Edit Pengaturan untuk Admin -->
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
      @endif
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
