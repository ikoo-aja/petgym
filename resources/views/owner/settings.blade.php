@extends('layouts.admin')

@section('title', 'Profil Gym (Owner) &mdash; PetGym')
@section('page_title', 'Pengaturan')
@section('page_subtitle', 'Informasi profil gym dan langganan sistem')

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

<!-- Form Pengaturan Rekening Bank & QRIS Kasir POS -->
<div class="card-custom mb-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h5 class="font-weight-bold text-dark mb-1">
        <i class="icon-credit-card mr-1 text-primary"></i> Pengaturan Rekening Bank &amp; QRIS Kasir POS
      </h5>
      <p class="text-muted small mb-0">
        Rekening dan QRIS yang Anda input di sini akan secara otomatis muncul di layar kasir saat pelanggan memilih metode <strong>Bank Transfer</strong> atau <strong>QRIS</strong>.
      </p>
    </div>
  </div>

  <form action="{{ route('owner.settings.payment') }}" method="POST" enctype="multipart/form-data" class="border-top pt-3">
    @csrf
    <div class="row">
      <!-- 1. Rekening Bank Transfer -->
      <div class="col-md-6 border-right pr-md-4 mb-4 mb-md-0">
        <div class="d-flex align-items-center mb-3">
          <div class="p-2 bg-light rounded text-primary mr-2">
            <i class="icon-bank" style="font-size: 18px;"></i>
          </div>
          <h6 class="font-weight-bold text-dark mb-0">1. Rekening Bank Transfer Gym</h6>
        </div>

        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark small mb-1">Nama Bank *</label>
          <input type="text" name="bank_name" class="form-control" placeholder="Contoh: BCA, Mandiri, BRI, BNI, BSI" value="{{ old('bank_name', $tenant->bank_name) }}" style="border-radius: 8px;">
          <small class="text-muted">Nama bank tempat rekening aktif Anda terdaftar.</small>
        </div>

        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark small mb-1">Nomor Rekening *</label>
          <input type="text" name="bank_account_number" class="form-control font-weight-bold" placeholder="Contoh: 1234567890" value="{{ old('bank_account_number', $tenant->bank_account_number) }}" style="border-radius: 8px; font-size: 15px;">
          <small class="text-muted">Nomor rekening tujuan transfer yang akan disalin kasir / pembeli.</small>
        </div>

        <div class="form-group mb-0">
          <label class="font-weight-bold text-dark small mb-1">Atas Nama Pemilik Rekening *</label>
          <input type="text" name="bank_account_holder" class="form-control" placeholder="Contoh: PT Gym Sejahtera Bersama / Nama Pemilik" value="{{ old('bank_account_holder', $tenant->bank_account_holder) }}" style="border-radius: 8px;">
          <small class="text-muted">Nama resmi yang tertera pada buku tabungan / rekening.</small>
        </div>
      </div>

      <!-- 2. QRIS Pembayaran -->
      <div class="col-md-6 pl-md-4">
        <div class="d-flex align-items-center mb-3">
          <div class="p-2 bg-light rounded text-success mr-2">
            <i class="icon-qrcode" style="font-size: 18px;"></i>
          </div>
          <h6 class="font-weight-bold text-dark mb-0">2. QRIS Standar Pembayaran</h6>
        </div>

        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark small mb-1">Nama Merchant QRIS</label>
          <input type="text" name="qris_merchant_name" class="form-control" placeholder="Contoh: {{ $tenant->name }} Official QRIS" value="{{ old('qris_merchant_name', $tenant->qris_merchant_name) }}" style="border-radius: 8px;">
          <small class="text-muted">Nama toko / merchant resmi yang muncul pada saat scan QRIS.</small>
        </div>

        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark small mb-1">Upload Gambar QR Code QRIS</label>
          <input type="file" name="qris_image" class="form-control-file p-2 border rounded bg-light" accept="image/*" style="border-radius: 8px;">
          <small class="text-muted d-block mt-1">Format: JPG, PNG, JPEG, WebP. Maks: 2MB. Pastikan barcode jelas terbaca.</small>
        </div>

        @if($tenant->qris_image_url)
          <div class="p-3 bg-light rounded border text-center" style="border-radius: 8px;">
            <small class="text-muted d-block font-weight-bold mb-2">QRIS Aktif Saat Ini:</small>
            <img src="{{ asset('storage/' . $tenant->qris_image_url) }}" alt="QRIS Merchant" class="img-fluid rounded border shadow-sm" style="max-height: 180px; object-fit: contain; background: #fff; padding: 6px;">
            <div class="mt-2">
              <span class="badge badge-success px-2 py-1"><i class="icon-check mr-1"></i> QR Code Terpasang</span>
            </div>
          </div>
        @else
          <div class="p-3 bg-light rounded border text-center text-muted" style="border-radius: 8px;">
            <i class="icon-image mb-1 d-block" style="font-size: 28px;"></i>
            <small>Belum ada gambar QRIS yang di-upload.</small>
          </div>
        @endif
      </div>
    </div>

    <div class="text-right border-top pt-3 mt-4">
      <button type="submit" class="btn btn-primary font-weight-bold px-4 py-2" style="border-radius: 8px;">
        <i class="icon-save mr-1"></i> Simpan Pengaturan Pembayaran
      </button>
    </div>
  </form>
</div>
@endsection
