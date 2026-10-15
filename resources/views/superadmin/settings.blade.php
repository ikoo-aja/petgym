@extends('layouts.superadmin')

@section('title', 'Pengaturan Sistem &mdash; Superadmin Panel')

@section('page_title', 'Pusat Kendali Pengaturan Platform')
@section('page_subtitle', 'Konfigurasi global billing sewa, mode pemeliharaan, kontak bantuan, dan gateway email SaaS')

@section('content')
<div class="row">
  <!-- Kolom Kiri: Form Konfigurasi Utama -->
  <div class="col-lg-8">

    <!-- PILAR 1: Rekening Pembayaran Sewa Platform -->
    <div class="card-custom mb-4">
      <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-3">
        <div>
          <h5 class="font-weight-bold text-dark mb-1">
            <span class="icon-credit-card text-primary mr-2"></span>Rekening Pembayaran Sewa Platform
          </h5>
          <p class="text-muted small mb-0">Tujuan transfer resmi yang otomatis tampil di menu Langganan seluruh Admin Gym saat perpanjangan sewa.</p>
        </div>
        <span class="badge badge-light border text-primary font-weight-bold px-2 py-1" style="font-size: 11px;">Billing SaaS</span>
      </div>

      <form action="{{ route('superadmin.settings.update') }}" method="POST">
        @csrf
        <input type="hidden" name="section" value="billing">

        <div class="row">
          <div class="col-md-4 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Bank Utama <span class="text-danger">*</span></label>
            <input type="text" name="billing_bank_name" class="form-control" value="{{ old('billing_bank_name', $billing['billing_bank_name'] ?? 'BCA') }}" placeholder="Contoh: BCA" required>
          </div>
          <div class="col-md-8 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Nomor Rekening Bank Utama <span class="text-danger">*</span></label>
            <input type="text" name="billing_bank_account" class="form-control" value="{{ old('billing_bank_account', $billing['billing_bank_account'] ?? '883012345678') }}" placeholder="Contoh: 883012345678" required>
          </div>
        </div>

        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark small mb-1">Nama Pemilik Rekening (Atas Nama) <span class="text-danger">*</span></label>
          <input type="text" name="billing_bank_owner" class="form-control" value="{{ old('billing_bank_owner', $billing['billing_bank_owner'] ?? 'PT PetGym Digital Indonesia') }}" placeholder="Contoh: PT PetGym Digital Indonesia" required>
        </div>

        <div class="row">
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Nomor Rekening Bank Mandiri (Opsional)</label>
            <input type="text" name="billing_mandiri_account" class="form-control" value="{{ old('billing_mandiri_account', $billing['billing_mandiri_account'] ?? '1370098765432') }}" placeholder="Contoh: 1370098765432">
          </div>
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Nama Merchant QRIS (Opsional)</label>
            <input type="text" name="billing_qris_name" class="form-control" value="{{ old('billing_qris_name', $billing['billing_qris_name'] ?? 'QRIS PetGym Instant') }}" placeholder="Contoh: QRIS PetGym Instant">
          </div>
        </div>

        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark small mb-1">Kode / ID Referensi QRIS (Opsional)</label>
          <input type="text" name="billing_qris_code" class="form-control" value="{{ old('billing_qris_code', $billing['billing_qris_code'] ?? 'QRIS-PETGYM') }}" placeholder="Contoh: QRIS-PETGYM">
        </div>

        <div class="form-group mb-4">
          <label class="font-weight-bold text-dark small mb-1">Instruksi & Catatan Tambahan Transfer</label>
          <textarea name="billing_bank_notes" class="form-control" rows="2" placeholder="Contoh: Sertakan kode unik invoice saat melakukan transfer pembayaran sewa.">{{ old('billing_bank_notes', $billing['billing_bank_notes'] ?? 'Sertakan kode invoice unik saat melakukan transfer pembayaran sewa.') }}</textarea>
        </div>

        <div class="text-right">
          <button type="submit" class="btn btn-primary font-weight-bold px-4">
            <span class="icon-check mr-1"></span> Simpan Rekening Sewa
          </button>
        </div>
      </form>
    </div>

    <!-- PILAR 3: Pusat Bantuan & Kontak Resmi Platform -->
    <div class="card-custom mb-4">
      <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-3">
        <div>
          <h5 class="font-weight-bold text-dark mb-1">
            <span class="icon-phone text-success mr-2"></span>Pusat Bantuan &amp; Kontak Resmi Platform
          </h5>
          <p class="text-muted small mb-0">Saluran bantuan resmi Superadmin yang dapat dihubungi langsung oleh Admin Gym dan penyewa.</p>
        </div>
        <span class="badge badge-light border text-success font-weight-bold px-2 py-1" style="font-size: 11px;">Helpdesk</span>
      </div>

      <form action="{{ route('superadmin.settings.update') }}" method="POST">
        @csrf
        <input type="hidden" name="section" value="support">

        <div class="row">
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Nomor WhatsApp Resmi Helpdesk <span class="text-danger">*</span></label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text bg-light text-muted small"><i class="icon-phone"></i></span>
              </div>
              <input type="text" name="support_whatsapp" class="form-control" value="{{ old('support_whatsapp', $support['support_whatsapp'] ?? '081234567890') }}" placeholder="081234567890" required>
            </div>
            <small class="text-muted">Akan terhubung otomatis ke tautan pesan WhatsApp CS.</small>
          </div>

          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Email Resmi Dukungan (Support) <span class="text-danger">*</span></label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text bg-light text-muted small"><i class="icon-envelope"></i></span>
              </div>
              <input type="email" name="support_email" class="form-control" value="{{ old('support_email', $support['support_email'] ?? 'support@petgym.website') }}" placeholder="support@petgym.website" required>
            </div>
          </div>
        </div>

        <div class="form-group mb-4">
          <label class="font-weight-bold text-dark small mb-1">Jam Operasional Layanan Bantuan</label>
          <input type="text" name="support_hours" class="form-control" value="{{ old('support_hours', $support['support_hours'] ?? 'Senin - Sabtu, 08:00 - 20:00 WIB') }}" placeholder="Contoh: Senin - Sabtu, 08:00 - 20:00 WIB">
        </div>

        <div class="text-right">
          <button type="submit" class="btn btn-primary font-weight-bold px-4">
            <span class="icon-check mr-1"></span> Simpan Kontak Bantuan
          </button>
        </div>
      </form>
    </div>

    <!-- PILAR 4: Konfigurasi Email Notifikasi Platform (SMTP) -->
    <div class="card-custom mb-4">
      <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-3">
        <div>
          <h5 class="font-weight-bold text-dark mb-1">
            <span class="icon-envelope text-info mr-2"></span>Konfigurasi Email Notifikasi Platform (SMTP)
          </h5>
          <p class="text-muted small mb-0">Gateway pengiriman email resmi platform untuk verifikasi akun, invoice sewa, dan reset kata sandi.</p>
        </div>
        <span class="badge badge-light border text-info font-weight-bold px-2 py-1" style="font-size: 11px;">SMTP Gateway</span>
      </div>

      <form action="{{ route('superadmin.settings.update') }}" method="POST">
        @csrf
        <input type="hidden" name="section" value="mail">

        <div class="row">
          <div class="col-md-8 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">SMTP Host <span class="text-danger">*</span></label>
            <input type="text" name="mail_host" class="form-control" value="{{ old('mail_host', $mail['mail_host'] ?? config('mail.mailers.smtp.host', 'smtp.mailtrap.io')) }}" placeholder="Contoh: smtp.mailtrap.io" required>
          </div>
          <div class="col-md-4 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">SMTP Port <span class="text-danger">*</span></label>
            <input type="number" name="mail_port" class="form-control" value="{{ old('mail_port', $mail['mail_port'] ?? config('mail.mailers.smtp.port', 2525)) }}" placeholder="2525" required>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Nama Pengguna SMTP (Username)</label>
            <input type="text" name="mail_username" class="form-control" value="{{ old('mail_username', $mail['mail_username'] ?? config('mail.mailers.smtp.username', '')) }}" placeholder="Username SMTP">
          </div>
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Kata Sandi SMTP</label>
            <input type="password" name="mail_password" class="form-control" value="{{ old('mail_password', $mail['mail_password'] ?? '') }}" placeholder="••••••••••••">
          </div>
        </div>

        <div class="row">
          <div class="col-md-4 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Protokol Enkripsi</label>
            <select name="mail_encryption" class="form-control">
              <option value="tls" {{ old('mail_encryption', $mail['mail_encryption'] ?? 'tls') === 'tls' ? 'selected' : '' }}>TLS</option>
              <option value="ssl" {{ old('mail_encryption', $mail['mail_encryption'] ?? 'tls') === 'ssl' ? 'selected' : '' }}>SSL</option>
              <option value="none" {{ old('mail_encryption', $mail['mail_encryption'] ?? 'tls') === 'none' ? 'selected' : '' }}>None</option>
            </select>
          </div>
          <div class="col-md-4 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Nama Pengirim Resmi <span class="text-danger">*</span></label>
            <input type="text" name="mail_from_name" class="form-control" value="{{ old('mail_from_name', $mail['mail_from_name'] ?? 'PetGym Platform') }}" placeholder="PetGym Platform" required>
          </div>
          <div class="col-md-4 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Alamat Email Pengirim <span class="text-danger">*</span></label>
            <input type="email" name="mail_from_address" class="form-control" value="{{ old('mail_from_address', $mail['mail_from_address'] ?? 'no-reply@petgym.website') }}" placeholder="no-reply@petgym.website" required>
          </div>
        </div>

        <div class="text-right">
          <button type="submit" class="btn btn-primary font-weight-bold px-4">
            <span class="icon-check mr-1"></span> Simpan Konfigurasi Email
          </button>
        </div>
      </form>

      <hr class="my-4">

      <!-- Tes Pengiriman Email -->
      <div>
        <h6 class="font-weight-bold text-dark mb-1">Uji Coba Pengiriman Email (Diagnostic)</h6>
        <p class="text-muted small mb-3">Kirim email pengujian cepat untuk memastikan pengaturan SMTP server terhubung dengan benar.</p>
        <form action="{{ route('superadmin.settings.update') }}" method="POST" class="form-inline">
          @csrf
          <input type="hidden" name="section" value="test_mail">
          <div class="form-group mr-2 mb-2">
            <input type="email" name="test_email" class="form-control form-control-sm" value="{{ Auth::user()->email ?? 'admin@petgym.website' }}" placeholder="tujuan@email.com" required style="min-width: 280px;">
          </div>
          <button type="submit" class="btn btn-outline-info btn-sm font-weight-bold mb-2">
            <span class="icon-send mr-1"></span> Kirim Email Tes
          </button>
        </form>
      </div>
    </div>

  </div>

  <!-- Kolom Kanan: Mode Pemeliharaan & Utilitas Sistem -->
  <div class="col-lg-4">

    <!-- PILAR 2: Mode Pemeliharaan Sistem Global -->
    <div class="card-custom mb-4 border-top border-warning" style="border-top: 4px solid #f59e0b !important;">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="font-weight-bold text-dark mb-0">Mode Pemeliharaan</h5>
        @if(($maintenance['maintenance_active'] ?? '0') === '1')
          <span class="badge badge-warning text-dark font-weight-bold px-2 py-1" style="font-size: 11px;">
            <i class="icon-warning mr-1"></i> AKTIF
          </span>
        @else
          <span class="badge badge-success font-weight-bold px-2 py-1" style="font-size: 11px;">
            <i class="icon-check mr-1"></i> Normal
          </span>
        @endif
      </div>
      <p class="text-muted small mb-3">Tutup sementara akses operasional bagi seluruh tenant saat pembaruan platform. Akun Superadmin tetap dapat mengakses sistem secara penuh.</p>

      <form action="{{ route('superadmin.settings.update') }}" method="POST">
        @csrf
        <input type="hidden" name="section" value="maintenance">

        <div class="p-3 bg-light rounded mb-3 border">
          <div class="custom-control custom-switch">
            <input type="checkbox" class="custom-control-input" id="maintenanceToggle" name="maintenance_active" value="1" {{ ($maintenance['maintenance_active'] ?? '0') === '1' ? 'checked' : '' }}>
            <label class="custom-control-label font-weight-bold text-dark" for="maintenanceToggle" style="cursor: pointer; font-size: 13.5px;">
              Aktifkan Mode Maintenance
            </label>
          </div>
          <small class="text-muted d-block mt-1">Saat aktif, seluruh pengguna level gym (Admin, Owner, Kasir, Member) akan melihat halaman pemeliharaan.</small>
        </div>

        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark small mb-1">Judul Pemeliharaan <span class="text-danger">*</span></label>
          <input type="text" name="maintenance_title" class="form-control form-control-sm" value="{{ old('maintenance_title', $maintenance['maintenance_title'] ?? 'Platform Sedang Dalam Pemeliharaan Terjadwal') }}" required>
        </div>

        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark small mb-1">Pesan Pemeliharaan <span class="text-danger">*</span></label>
          <textarea name="maintenance_message" class="form-control form-control-sm" rows="3" required>{{ old('maintenance_message', $maintenance['maintenance_message'] ?? 'Kami sedang melakukan peningkatan sistem dan infrastruktur server PetGym untuk performa yang lebih optimal.') }}</textarea>
        </div>

        <div class="form-group mb-4">
          <label class="font-weight-bold text-dark small mb-1">Estimasi Waktu Selesai</label>
          <input type="text" name="maintenance_estimated_end" class="form-control form-control-sm" value="{{ old('maintenance_estimated_end', $maintenance['maintenance_estimated_end'] ?? 'Estimasi selesai: 02:00 WIB') }}" placeholder="Contoh: Estimasi selesai: 02:00 WIB">
        </div>

        <button type="submit" class="btn btn-warning btn-block font-weight-bold text-dark shadow-sm">
          <span class="icon-check mr-1"></span> Simpan Status Pemeliharaan
        </button>
      </form>
    </div>

    <!-- PILAR 5: Utilitas Pembersihan Cache -->
    <div class="card-custom mb-4 border-top border-danger" style="border-top: 4px solid #ef4444 !important;">
      <h5 class="font-weight-bold text-dark mb-2">Utilitas &amp; Cache</h5>
      <p class="text-muted small mb-3">Bersihkan cache konfigurasi, rute, template Blade, dan aplikasi secara terpadu melalui perintah sistem.</p>

      <form action="{{ route('superadmin.clear-cache') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-outline-danger btn-block font-weight-bold py-2">
          <span class="icon-refresh mr-1"></span> Bersihkan Cache Sistem
        </button>
      </form>
    </div>

    <!-- Informasi Lingkungan Server -->
    <div class="card-custom mb-4 border-top border-info" style="border-top: 4px solid #3b82f6 !important;">
      <h5 class="font-weight-bold text-dark mb-2">Informasi Lingkungan Server</h5>
      <ul class="list-unstyled mb-0" style="font-size: 13px; line-height: 2;">
        <li class="d-flex justify-content-between border-bottom py-1">
          <span class="text-muted">Aplikasi / Framework:</span>
          <strong class="text-dark">Laravel {{ app()->version() }}</strong>
        </li>
        <li class="d-flex justify-content-between border-bottom py-1">
          <span class="text-muted">Versi PHP:</span>
          <strong class="text-dark">{{ phpversion() }}</strong>
        </li>
        <li class="d-flex justify-content-between border-bottom py-1">
          <span class="text-muted">Environment:</span>
          <span class="badge badge-light border text-dark font-weight-bold">{{ app()->environment() }}</span>
        </li>
        <li class="d-flex justify-content-between border-bottom py-1">
          <span class="text-muted">Driver Database:</span>
          <strong class="text-dark">{{ config('database.default') }}</strong>
        </li>
        <li class="d-flex justify-content-between py-1">
          <span class="text-muted">Waktu Server:</span>
          <span class="text-dark font-weight-bold">{{ now()->format('H:i') }} WIB</span>
        </li>
      </ul>
    </div>

  </div>
</div>
@endsection
