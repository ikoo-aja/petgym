@extends('layouts.superadmin')

@section('title', 'Pengaturan &mdash; Superadmin Panel')

@section('page_title', 'Pengaturan Sistem')
@section('page_subtitle', 'Pengaturan konfigurasi sistem superadmin')

@section('content')
<div class="row">
  <!-- Sisi Kiri (Form Konfigurasi) -->
  <div class="col-lg-8">
    <!-- Pengaturan Umum -->
    <div class="bg-white p-4 rounded shadow-sm mb-4">
      <h5 class="font-weight-bold text-black mb-4">Pengaturan Umum</h5>
      <form action="#" method="POST">
        @csrf
        <div class="form-group row">
          <label class="col-sm-4 col-form-label text-black font-weight-bold">Nama Platform</label>
          <div class="col-sm-8">
            <input type="text" class="form-control" name="app_name" value="Workout SaaS Platform">
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-4 col-form-label text-black font-weight-bold">Domain Utama</label>
          <div class="col-sm-8">
            <input type="text" class="form-control" name="app_domain" value="workout.id">
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-4 col-form-label text-black font-weight-bold">Email Dukungan (Support)</label>
          <div class="col-sm-8">
            <input type="email" class="form-control" name="support_email" value="support@workout.id">
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-4 col-form-label text-black font-weight-bold">Status Registrasi Tenant</label>
          <div class="col-sm-8">
            <select class="form-control" name="registration_status">
              <option value="open">Terbuka untuk Umum</option>
              <option value="closed">Ditutup (Hanya Manual Onboarding)</option>
            </select>
          </div>
        </div>
        <div class="row mt-4">
          <div class="col-sm-8 offset-sm-4">
            <button type="submit" class="btn btn-primary btn-sm px-4" onclick="event.preventDefault(); showToast('Pengaturan Disimpan', 'Konfigurasi umum platform berhasil diperbarui!', 'success');">Simpan Perubahan</button>
          </div>
        </div>
      </form>
    </div>

    <!-- Konfigurasi Free Trial -->
    <div class="bg-white p-4 rounded shadow-sm mb-4">
      <h5 class="font-weight-bold text-black mb-3">Konfigurasi Free Trial</h5>
      <p class="text-muted" style="font-size: 14px;">Atur durasi percobaan gratis untuk registrasi tenant mandiri baru.</p>
      <form action="#" method="POST">
        @csrf
        <div class="form-group row">
          <label class="col-sm-4 col-form-label text-black font-weight-bold">Durasi Trial (Hari)</label>
          <div class="col-sm-8">
            <input type="number" name="trial_duration" class="form-control" value="14">
          </div>
        </div>
        <div class="row mt-3">
          <div class="col-sm-8 offset-sm-4">
            <button type="submit" class="btn btn-warning text-white btn-sm px-4" onclick="event.preventDefault(); showToast('Durasi Trial Diubah', 'Durasi free trial baru berhasil disimpan: ' + $('input[name=\'trial_duration\']').val() + ' Hari', 'success');">Simpan Durasi Trial</button>
          </div>
        </div>
      </form>
    </div>

    <!-- Konfigurasi SMTP Email -->
    <div class="bg-white p-4 rounded shadow-sm mb-4">
      <h5 class="font-weight-bold text-black mb-4">Konfigurasi Email (SMTP)</h5>
      <form action="#" method="POST">
        @csrf
        <div class="form-group row">
          <label class="col-sm-4 col-form-label text-black font-weight-bold">SMTP Host</label>
          <div class="col-sm-8">
            <input type="text" class="form-control" name="mail_host" value="smtp.mailtrap.io">
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-4 col-form-label text-black font-weight-bold">SMTP Port</label>
          <div class="col-sm-8">
            <input type="number" class="form-control" name="mail_port" value="2525">
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-4 col-form-label text-black font-weight-bold">SMTP Username</label>
          <div class="col-sm-8">
            <input type="text" class="form-control" name="mail_username" value="ae7dcf0b1bc89a">
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-4 col-form-label text-black font-weight-bold">SMTP Password</label>
          <div class="col-sm-8">
            <input type="password" class="form-control" name="mail_password" value="••••••••••••">
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-4 col-form-label text-black font-weight-bold">SMTP Encryption</label>
          <div class="col-sm-8">
            <select class="form-control" name="mail_encryption">
              <option value="tls">TLS</option>
              <option value="ssl">SSL</option>
              <option value="none">None</option>
            </select>
          </div>
        </div>
        <div class="row mt-4">
          <div class="col-sm-8 offset-sm-4">
            <button type="submit" class="btn btn-primary btn-sm px-4" onclick="event.preventDefault(); showToast('SMTP Diperbarui', 'Konfigurasi kredensial SMTP berhasil disimpan!', 'success');">Simpan SMTP</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Sisi Kanan (Maintenance, Cache, Info) -->
  <div class="col-lg-4">
    <!-- Maintenance Mode Toggle -->
    <div class="bg-white p-4 rounded shadow-sm mb-4 border-top border-warning" style="border-top-width: 4px !important;">
      <h5 class="font-weight-bold text-black mb-3">Mode Pemeliharaan</h5>
      <p class="text-muted" style="font-size: 13px;">Aktifkan mode pemeliharaan untuk menutup sementara akses dashboard bagi semua tenant selama masa pembaruan sistem.</p>
      <hr>
      <div class="custom-control custom-switch mt-2">
        <input type="checkbox" class="custom-control-input" id="maintenanceToggle" onchange="showToast(this.checked ? 'Maintenance Mode Aktif' : 'Maintenance Mode Nonaktif', this.checked ? 'Mode pemeliharaan diaktifkan. Dashboard tenant ditutup sementara.' : 'Mode pemeliharaan dinonaktifkan. Sesi tenant berjalan normal.', this.checked ? 'warning' : 'success');">
        <label class="custom-control-label font-weight-bold text-black" for="maintenanceToggle" style="cursor: pointer; font-size: 14px;">Status Maintenance</label>
      </div>
    </div>

    <!-- Clear Cache Button -->
    <div class="bg-white p-4 rounded shadow-sm mb-4 border-top border-danger" style="border-top-width: 4px !important;">
      <h5 class="font-weight-bold text-black mb-3">Utilitas & Cache</h5>
      <p class="text-muted" style="font-size: 13px;">Gunakan tombol ini untuk menghapus cache optimasi Laravel (config, route, view, application cache) dari sistem superadmin.</p>
      <hr>
      <form action="{{ route('superadmin.clear-cache') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-danger btn-sm btn-block px-4">
          <span class="icon-refresh"></span> Bersihkan Cache Sistem
        </button>
      </form>
    </div>

    <!-- Informasi Sistem -->
    <div class="bg-white p-4 rounded shadow-sm border-left border-info" style="border-left-width: 4px !important;">
      <h5 class="font-weight-bold text-black mb-3">Informasi Sistem</h5>
      <ul class="list-unstyled text-muted" style="line-height: 2; font-size: 13px;">
        <li><strong>Laravel Version:</strong> {{ app()->version() }}</li>
        <li><strong>PHP Version:</strong> {{ phpversion() }}</li>
        <li><strong>Environment:</strong> {{ app()->environment() }}</li>
        <li><strong>Database Driver:</strong> mysql</li>
      </ul>
    </div>
  </div>
</div>
@endsection
