@extends('layouts.member')

@section('title', 'Profil & Pengaturan')
@section('page_title', 'Profil & Pengaturan')
@section('page_subtitle', 'Kelola informasi pribadi, keamanan akun, dan preferensi member.')

@section('styles')
<style>
  .profile-hero-card {
    background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
    border-radius: 16px;
    color: #ffffff;
    padding: 20px;
    margin-bottom: 20px;
    border: 1px solid rgba(255,255,255,0.1);
  }
  .profile-avatar-box {
    width: 64px;
    height: 64px;
    border-radius: 14px;
    background: var(--brand-red);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    font-weight: 900;
  }
  .referral-banner {
    background: var(--brand-red-light);
    border: 1px solid var(--brand-red-border);
    color: var(--mp-text);
    border-radius: 12px;
    padding: 14px 18px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
  }
  .stat-box {
    background: var(--mp-card-bg);
    border: 1px solid var(--mp-border);
    border-radius: 12px;
    padding: 14px;
    text-align: center;
  }
  .stat-label {
    font-size: 11.5px;
    font-weight: 700;
    color: var(--mp-text-muted);
    margin-bottom: 4px;
  }
  .stat-val {
    font-size: 18px;
    font-weight: 800;
    color: var(--mp-text);
  }
  .setting-list-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 16px;
    border-bottom: 1px solid var(--mp-border);
    color: var(--mp-text);
    text-decoration: none !important;
    transition: background 0.2s ease;
  }
  .setting-list-item:last-child {
    border-bottom: none;
  }
  .setting-list-item:hover {
    background: var(--brand-red-light);
    color: var(--brand-red);
  }
  .setting-icon {
    width: 34px;
    height: 34px;
    border-radius: 8px;
    background: var(--mp-pill-bg);
    color: var(--brand-red);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
    font-size: 15px;
  }
  @media (max-width: 575.98px) {
    .profile-hero-card {
      padding: 16px;
    }
    .profile-avatar-box {
      width: 52px;
      height: 52px;
      font-size: 22px;
    }
    .referral-banner {
      flex-direction: column;
      align-items: flex-start;
      gap: 10px;
    }
  }
</style>
@endsection

@section('content')
<div class="row">
  <!-- Left Side: Profile Hero, Club Membership, Body Stats -->
  <div class="col-12 col-lg-7 mb-3 mb-lg-4">
    <!-- Profile Hero Card -->
    <div class="profile-hero-card">
      <div class="d-flex align-items-center">
        <div class="mr-3">
          <div class="profile-avatar-box">
            {{ substr($user->name ?? 'M', 0, 1) }}
          </div>
        </div>
        <div>
          <h4 class="font-weight-bold text-white mb-1" style="font-size: 18px;">{{ $member->name ?? $user->name }}</h4>
          <p class="mb-1 text-white-50 small"><span class="icon-envelope mr-1"></span> {{ $user->email }}</p>
          <p class="mb-0 text-white-50 small"><span class="icon-phone mr-1"></span> {{ $member->phone ?? '-' }}</p>
        </div>
      </div>
    </div>

    <!-- Referral / Promo Banner -->
    <div class="referral-banner shadow-sm">
      <div>
        <strong class="d-block" style="font-size: 13.5px; color: var(--brand-red);">Program Rujukan Teman (Referral)</strong>
        <small class="text-muted">Ajak teman bergabung untuk mendapatkan bonus masa aktif keanggotaan.</small>
      </div>
      <button type="button" class="btn btn-sm btn-primary font-weight-bold" style="border-radius: 6px; font-size: 12px;" onclick="showToast('Kode Rujukan Anda', 'REFF-{{ strtoupper(substr(md5($user->id), 0, 6)) }} — bagikan kepada teman Anda.', 'info')">
        Undang Teman
      </button>
    </div>

    <!-- Profile Body Stats -->
    <div class="card-custom mb-3 mb-md-4">
      <h6 class="font-weight-bold text-dark mb-3">Statistik Fisik Anggota</h6>
      <div class="row">
        <div class="col-4">
          <div class="stat-box">
            <div class="stat-label">Berat Badan</div>
            <div class="stat-val">- <small class="text-muted" style="font-size:11px;">kg</small></div>
          </div>
        </div>
        <div class="col-4">
          <div class="stat-box">
            <div class="stat-label">Tinggi Badan</div>
            <div class="stat-val">- <small class="text-muted" style="font-size:11px;">cm</small></div>
          </div>
        </div>
        <div class="col-4">
          <div class="stat-box">
            <div class="stat-label">Skor BMI</div>
            <div class="stat-val text-primary">0</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Club Membership Section -->
    <div class="card-custom mb-3 mb-md-4">
      <h6 class="font-weight-bold text-dark mb-3">Status Keanggotaan Gym</h6>

      <div class="text-center py-3 mb-3 border rounded bg-light">
        @if($hasActiveSubscription)
          <span class="badge badge-success px-3 py-1 font-weight-bold mb-2">Keanggotaan Aktif</span>
          <h5 class="font-weight-bold text-dark mb-1">Tingkat {{ ucfirst($member->membership_tier) }}</h5>
          <p class="text-muted small mb-1">Masa berlaku hingga: <strong>{{ $member->expired_at ? $member->expired_at->format('d M Y') : '-' }}</strong> (Sisa {{ $member->days_left }} hari)</p>
        @else
          <span class="badge badge-warning px-3 py-1 font-weight-bold mb-2 text-dark">Belum Ada Paket Langganan</span>
          <p class="text-muted mb-2 font-weight-semibold small">Lakukan aktivasi dan pembayaran di Meja Resepsionis</p>
          <div class="badge badge-light border text-dark px-3 py-2 font-weight-bold mb-2 d-inline-block" style="font-size: 12px; letter-spacing: 1px;">
            PIN AKSES: <span class="text-primary">{{ $member->access_code ?? 'MBR-000000' }}</span>
          </div>
        @endif
        <div class="mt-1">
          <a href="{{ route('member.membership') }}" class="btn btn-sm btn-link text-primary font-weight-bold p-0">Lihat Pilihan Paket &rarr;</a>
        </div>
      </div>

      <div class="row">
        <div class="col-12 col-md-6 mb-2">
          <a href="{{ route('member.membership') }}" class="btn btn-primary btn-block font-weight-bold py-2" style="font-size: 13.5px;">
            Aktivasi / Perpanjang Paket
          </a>
        </div>
        <div class="col-12 col-md-6 mb-2">
          <button type="button" class="btn btn-outline-primary btn-block font-weight-bold py-2" style="font-size: 13.5px;" data-toggle="modal" data-target="#dailyPassModal">
            Tiket Harian Perusahaan
          </button>
        </div>
      </div>
    </div>

    <!-- Reformer Pilates / Add-on Access Section -->
    <div class="card-custom mb-3 mb-md-4">
      <h6 class="font-weight-bold text-dark mb-2">Layanan Tambahan (Add-on)</h6>
      <div class="text-center py-3 mb-3 border rounded bg-light">
        <p class="text-muted mb-0 small">Belum ada akses layanan tambahan aktif</p>
      </div>
      <button type="button" class="btn btn-outline-primary btn-block py-2 font-weight-bold" style="border-radius: 8px; font-size: 13.5px;" onclick="showToast('Informasi', 'Layanan tambahan dapat diaktifkan melalui petugas resepsionis gym.', 'info')">
        Informasi Layanan Tambahan
      </button>
    </div>
  </div>

  <!-- Right Side: History, Feedback, Account & Logout -->
  <div class="col-12 col-lg-5 mb-3 mb-lg-4">
    <!-- History Section -->
    <div class="card-custom mb-3 mb-md-4 p-0 overflow-hidden">
      <div class="p-3 border-bottom bg-light">
        <h6 class="font-weight-bold text-dark mb-0">Riwayat</h6>
      </div>
      <div>
        <a href="{{ route('member.billing') }}" class="setting-list-item">
          <div class="d-flex align-items-center">
            <span class="setting-icon"><span class="icon-file-text"></span></span>
            <span class="font-weight-semibold" style="font-size: 13.5px;">Riwayat Pembelian & Tagihan</span>
          </div>
          <span class="text-muted">&rsaquo;</span>
        </a>
        <a href="#activityHistoryModal" data-toggle="modal" class="setting-list-item">
          <div class="d-flex align-items-center">
            <span class="setting-icon"><span class="icon-check"></span></span>
            <span class="font-weight-semibold" style="font-size: 13.5px;">Riwayat Presensi Kunjungan</span>
          </div>
          <span class="text-muted">&rsaquo;</span>
        </a>
      </div>
    </div>

    <!-- Feedback Section -->
    <div class="card-custom mb-3 mb-md-4 p-0 overflow-hidden">
      <div class="p-3 border-bottom bg-light">
        <h6 class="font-weight-bold text-dark mb-0">Umpan Balik & Saran</h6>
      </div>
      <div>
        <a href="#feedbackModal" data-toggle="modal" class="setting-list-item">
          <div class="d-flex align-items-center">
            <span class="setting-icon"><span class="icon-star"></span></span>
            <span class="font-weight-semibold" style="font-size: 13.5px;">Penilaian Fasilitas Gym</span>
          </div>
          <span class="text-muted">&rsaquo;</span>
        </a>
        <a href="#feedbackModal" data-toggle="modal" class="setting-list-item">
          <div class="d-flex align-items-center">
            <span class="setting-icon"><span class="icon-pencil"></span></span>
            <span class="font-weight-semibold" style="font-size: 13.5px;">Kirim Masukan & Saran</span>
          </div>
          <span class="text-muted">&rsaquo;</span>
        </a>
      </div>
    </div>

    <!-- Account Section -->
    <div class="card-custom mb-3 mb-md-4 p-0 overflow-hidden">
      <div class="p-3 border-bottom bg-light">
        <h6 class="font-weight-bold text-dark mb-0">Akun & Pengaturan</h6>
      </div>
      <div>
        <a href="#voucherModal" data-toggle="modal" class="setting-list-item">
          <div class="d-flex align-items-center">
            <span class="setting-icon"><span class="icon-ticket"></span></span>
            <span class="font-weight-semibold" style="font-size: 13.5px;">Voucher & Promosi</span>
          </div>
          <span class="text-muted">&rsaquo;</span>
        </a>
        <a href="{{ route('member.guide') }}" class="setting-list-item">
          <div class="d-flex align-items-center">
            <span class="setting-icon"><span class="icon-help"></span></span>
            <span class="font-weight-semibold" style="font-size: 13.5px;">Panduan Penggunaan Portal</span>
          </div>
          <span class="text-muted">&rsaquo;</span>
        </a>
        <a href="{{ route('member.guide') }}" class="setting-list-item">
          <div class="d-flex align-items-center">
            <span class="setting-icon"><span class="icon-file-text"></span></span>
            <span class="font-weight-semibold" style="font-size: 13.5px;">Syarat dan Ketentuan</span>
          </div>
          <span class="text-muted">&rsaquo;</span>
        </a>
        <a href="{{ route('member.guide') }}" class="setting-list-item">
          <div class="d-flex align-items-center">
            <span class="setting-icon"><span class="icon-lock"></span></span>
            <span class="font-weight-semibold" style="font-size: 13.5px;">Kebijakan Privasi</span>
          </div>
          <span class="text-muted">&rsaquo;</span>
        </a>
        <a href="#editSecurityModal" data-toggle="modal" class="setting-list-item">
          <div class="d-flex align-items-center">
            <span class="setting-icon"><span class="icon-key"></span></span>
            <span class="font-weight-semibold" style="font-size: 13.5px;">Keamanan Kata Sandi & PIN</span>
          </div>
          <span class="text-muted">&rsaquo;</span>
        </a>
        <a href="#editProfileModal" data-toggle="modal" class="setting-list-item">
          <div class="d-flex align-items-center">
            <span class="setting-icon"><span class="icon-user"></span></span>
            <span class="font-weight-semibold" style="font-size: 13.5px;">Perbarui Data Diri</span>
          </div>
          <span class="text-muted">&rsaquo;</span>
        </a>
        <div class="setting-list-item">
          <div class="d-flex align-items-center">
            <span class="setting-icon"><span class="icon-cog"></span></span>
            <div>
              <span class="font-weight-semibold d-block" style="font-size: 13.5px;">Mode Tampilan Gelap</span>
              <small class="text-muted d-block" style="font-size: 11px;" id="themeSettingStatusText">Saat ini: Mode Terang</small>
            </div>
          </div>
          <div class="custom-control custom-switch">
            <input type="checkbox" class="custom-control-input" id="settingsThemeSwitch" onchange="toggleMemberTheme()">
            <label class="custom-control-label" for="settingsThemeSwitch" style="cursor: pointer;"></label>
          </div>
        </div>
      </div>
    </div>

    <!-- LOGOUT BUTTON SECTION -->
    <div class="card-custom p-3 border-danger mb-3 mb-md-4">
      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-outline-danger btn-block font-weight-bold py-2" style="border-radius: 8px;">
          <span class="icon-power-off mr-1"></span> Keluar dari Akun
        </button>
      </form>
    </div>

    <!-- Footer App Version -->
    <div class="text-center text-muted py-2" style="font-size: 11.5px;">
      <div class="font-weight-bold">{{ $tenantName }} Portal Anggota</div>
      <div style="font-size: 10px;">&copy; {{ date('Y') }} {{ $tenantName }}. Hak Cipta Dilindungi Undang-Undang.</div>
    </div>
  </div>
</div>

<!-- Modal Edit Profil -->
<div class="modal fade" id="editProfileModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-light">
        <h5 class="modal-title font-weight-bold text-dark mb-0">Perbarui Data Diri</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">&times;</button>
      </div>
      <form action="{{ route('member.settings.update_profile') }}" method="POST">
        @csrf
        <div class="modal-body p-4">
          <div class="form-group mb-3">
            <label class="font-weight-bold text-dark small">Nama Lengkap *</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $member->name ?? $user->name) }}" required style="border-radius: 8px;">
          </div>
          <div class="form-group mb-3">
            <label class="font-weight-bold text-dark small">Alamat Email (Akun Login)</label>
            <input type="email" class="form-control bg-light" value="{{ $user->email }}" readonly disabled style="border-radius: 8px;">
          </div>
          <div class="row">
            <div class="col-12 col-md-6 mb-3">
              <label class="font-weight-bold text-dark small">Nomor WhatsApp / HP *</label>
              <input type="text" name="phone" class="form-control" value="{{ old('phone', $member->phone ?? '') }}" required style="border-radius: 8px;">
            </div>
            <div class="col-12 col-md-6 mb-3">
              <label class="font-weight-bold text-dark small">Jenis Kelamin *</label>
              <select name="gender" class="form-control" required style="border-radius: 8px;">
                <option value="Laki-laki" {{ (old('gender', $member->gender ?? '') === 'Laki-laki') ? 'selected' : '' }}>Laki-laki</option>
                <option value="Perempuan" {{ (old('gender', $member->gender ?? '') === 'Perempuan') ? 'selected' : '' }}>Perempuan</option>
              </select>
            </div>
          </div>
          <div class="form-group mb-0">
            <label class="font-weight-bold text-dark small">Alamat Domisili</label>
            <textarea name="address" class="form-control" rows="3" style="border-radius: 8px;">{{ old('address', $member->address ?? '') }}</textarea>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary font-weight-bold">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Keamanan Akun & PIN -->
<div class="modal fade" id="editSecurityModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-light">
        <h5 class="modal-title font-weight-bold text-dark mb-0">Keamanan Akun & PIN Akses</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">&times;</button>
      </div>
      <div class="modal-body p-4">
        <!-- Access Code PIN Display -->
        <div class="p-3 bg-light rounded text-center mb-4 border">
          <small class="text-muted text-uppercase font-weight-bold d-block mb-1">Kode PIN Akses Presensi Anda</small>
          <h3 class="font-weight-bold text-primary mb-0" style="letter-spacing: 4px;">{{ $member->access_code ?? 'MBR-DEFAULT' }}</h3>
          <small class="text-muted">Gunakan kode PIN ini saat check-in di meja resepsionis gym.</small>
        </div>

        <form action="{{ route('member.settings.update_password') }}" method="POST">
          @csrf
          <h6 class="font-weight-bold text-dark mb-3">Ubah Kata Sandi Akun</h6>
          <div class="form-group mb-3">
            <label class="font-weight-bold text-dark small">Kata Sandi Saat Ini *</label>
            <div class="input-group">
              <input type="password" name="current_password" id="currentPassword" class="form-control" required style="border-radius: 8px;">
              <div class="input-group-append">
                <span class="input-group-text bg-white border-left-0" style="cursor: pointer; border-radius: 0 8px 8px 0;" id="toggleCurrentPasswordBtn">
                  <i class="icon-eye text-muted" id="toggleCurrentPasswordIcon"></i>
                </span>
              </div>
            </div>
          </div>
          <div class="form-group mb-3">
            <label class="font-weight-bold text-dark small">Kata Sandi Baru *</label>
            <div class="input-group">
              <input type="password" name="new_password" id="newPassword" class="form-control" required style="border-radius: 8px;">
              <div class="input-group-append">
                <span class="input-group-text bg-white border-left-0" style="cursor: pointer; border-radius: 0 8px 8px 0;" id="toggleNewPasswordBtn">
                  <i class="icon-eye text-muted" id="toggleNewPasswordIcon"></i>
                </span>
              </div>
            </div>
          </div>
          <div class="form-group mb-4">
            <label class="font-weight-bold text-dark small">Konfirmasi Kata Sandi Baru *</label>
            <div class="input-group">
              <input type="password" name="new_password_confirmation" id="newPasswordConfirmation" class="form-control" required style="border-radius: 8px;">
              <div class="input-group-append">
                <span class="input-group-text bg-white border-left-0" style="cursor: pointer; border-radius: 0 8px 8px 0;" id="toggleNewPasswordConfirmBtn">
                  <i class="icon-eye text-muted" id="toggleNewPasswordConfirmIcon"></i>
                </span>
              </div>
            </div>
          </div>
          <button type="submit" class="btn btn-primary btn-block font-weight-bold py-2">Perbarui Kata Sandi</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal Corporate Daily Pass -->
<div class="modal fade" id="dailyPassModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-light">
        <h5 class="modal-title font-weight-bold text-dark mb-0">Tiket Harian Perusahaan</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">&times;</button>
      </div>
      <div class="modal-body p-4 text-center">
        <div class="rounded-circle bg-light text-primary mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
          <span class="icon-ticket" style="font-size: 28px;"></span>
        </div>
        <h5 class="font-weight-bold text-dark mt-2 mb-1">Tiket Harian Mitra Perusahaan</h5>
        <p class="text-muted small mb-3">Gunakan tiket harian khusus mitra {{ $tenantName }} untuk akses 1 hari penuh latihan.</p>
        <div class="p-3 bg-light rounded border mb-3">
          <small class="text-muted d-block mb-1">Tarif Tiket Harian Standar</small>
          <h4 class="font-weight-bold text-primary mb-0">Rp 50.000 / Hari</h4>
        </div>
        <a href="{{ route('member.billing') }}" class="btn btn-primary btn-block font-weight-bold">Beli Tiket Harian</a>
      </div>
    </div>
  </div>
</div>

<!-- Modal Activity History -->
<div class="modal fade" id="activityHistoryModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-light">
        <h5 class="modal-title font-weight-bold text-dark mb-0">Riwayat Presensi Kunjungan</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">&times;</button>
      </div>
      <div class="modal-body p-4 text-center">
        <div class="p-3 bg-light rounded border mb-3">
          <small class="text-muted d-block">Total Presensi Kunjungan Gym</small>
          <h3 class="font-weight-bold text-primary mb-0">{{ $checkInsCount ?? 0 }} Kunjungan</h3>
        </div>
        <p class="text-muted small mb-0">Catatan presensi otomatis tersimpan setiap kali Anda melakukan check-in dengan PIN akses di lokasi {{ $tenantName }}.</p>
      </div>
    </div>
  </div>
</div>

<!-- Modal Feedback -->
<div class="modal fade" id="feedbackModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-light">
        <div>
          <h5 class="modal-title font-weight-bold text-dark mb-0">Kritik, Saran & Masukan</h5>
          <small class="text-muted">Bantu kami meningkatkan kualitas layanan {{ $tenantName }}</small>
        </div>
        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">&times;</button>
      </div>
      <form action="{{ route('member.settings.feedback') }}" method="POST">
        @csrf
        <div class="modal-body p-4 text-center">
          <h6 class="font-weight-bold text-dark mb-2">Bagaimana Pengalaman Anda Menggunakan Fasilitas Gym?</h6>
          <p class="text-muted small mb-3">Berikan penilaian bintang dan sampaikan kritik/saran Anda:</p>

          <input type="hidden" name="rating" id="feedbackRatingInput" value="5">
          <div class="mb-3 text-warning d-flex justify-content-center align-items-center" style="font-size: 26px; cursor: pointer; gap: 6px;">
            <span class="icon-star feedback-star" data-rating="1"></span>
            <span class="icon-star feedback-star" data-rating="2"></span>
            <span class="icon-star feedback-star" data-rating="3"></span>
            <span class="icon-star feedback-star" data-rating="4"></span>
            <span class="icon-star feedback-star" data-rating="5"></span>
          </div>
          <div class="text-muted small font-weight-bold mb-3" id="feedbackRatingLabel">Sangat Memuaskan (5/5)</div>

          <textarea name="feedback" class="form-control mb-3" rows="4" placeholder="Tuliskan saran, masukan perbaikan fasilitas, atau keluhan Anda di sini..." required style="border-radius: 8px; font-size: 13.5px;"></textarea>

          <button type="submit" class="btn btn-primary btn-block font-weight-bold py-2">Kirim Masukan ke Pengelola</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Voucher -->
<div class="modal fade" id="voucherModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-light">
        <h5 class="modal-title font-weight-bold text-dark mb-0">Voucher & Promosi</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">&times;</button>
      </div>
      <div class="modal-body p-4">
        <div class="p-3 border border-primary rounded bg-light mb-3 d-flex justify-content-between align-items-center">
          <div>
            <strong class="text-primary d-block font-weight-bold">PROMO ANGGOTA BARU</strong>
            <small class="text-muted">Diskon 20% Upgrade Paket Keanggotaan</small>
          </div>
          <span class="badge badge-primary font-weight-bold">NEW20</span>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  // Toggle lihat/sembunyi password — Kata Sandi Saat Ini
  const toggleCurrentPasswordBtn = document.querySelector('#toggleCurrentPasswordBtn');
  const currentPasswordInput = document.querySelector('#currentPassword');
  const toggleCurrentPasswordIcon = document.querySelector('#toggleCurrentPasswordIcon');

  if (toggleCurrentPasswordBtn) {
    toggleCurrentPasswordBtn.addEventListener('click', function () {
      const isPassword = currentPasswordInput.getAttribute('type') === 'password';
      currentPasswordInput.setAttribute('type', isPassword ? 'text' : 'password');

      toggleCurrentPasswordIcon.classList.toggle('icon-eye');
      toggleCurrentPasswordIcon.classList.toggle('icon-eye-slash');
      toggleCurrentPasswordIcon.classList.toggle('text-primary');
      toggleCurrentPasswordIcon.classList.toggle('text-muted');
    });
  }

  // Toggle lihat/sembunyi password — Kata Sandi Baru
  const toggleNewPasswordBtn = document.querySelector('#toggleNewPasswordBtn');
  const newPasswordInput = document.querySelector('#newPassword');
  const toggleNewPasswordIcon = document.querySelector('#toggleNewPasswordIcon');

  if (toggleNewPasswordBtn) {
    toggleNewPasswordBtn.addEventListener('click', function () {
      const isPassword = newPasswordInput.getAttribute('type') === 'password';
      newPasswordInput.setAttribute('type', isPassword ? 'text' : 'password');

      toggleNewPasswordIcon.classList.toggle('icon-eye');
      toggleNewPasswordIcon.classList.toggle('icon-eye-slash');
      toggleNewPasswordIcon.classList.toggle('text-primary');
      toggleNewPasswordIcon.classList.toggle('text-muted');
    });
  }

  // Toggle lihat/sembunyi password — Konfirmasi Kata Sandi Baru
  const toggleNewPasswordConfirmBtn = document.querySelector('#toggleNewPasswordConfirmBtn');
  const newPasswordConfirmInput = document.querySelector('#newPasswordConfirmation');
  const toggleNewPasswordConfirmIcon = document.querySelector('#toggleNewPasswordConfirmIcon');

  if (toggleNewPasswordConfirmBtn) {
    toggleNewPasswordConfirmBtn.addEventListener('click', function () {
      const isPassword = newPasswordConfirmInput.getAttribute('type') === 'password';
      newPasswordConfirmInput.setAttribute('type', isPassword ? 'text' : 'password');

      toggleNewPasswordConfirmIcon.classList.toggle('icon-eye');
      toggleNewPasswordConfirmIcon.classList.toggle('icon-eye-slash');
      toggleNewPasswordConfirmIcon.classList.toggle('text-primary');
      toggleNewPasswordConfirmIcon.classList.toggle('text-muted');
    });
  }

  // Interaktivitas Penilaian Bintang Feedback
  const starLabels = {
    1: 'Sangat Buruk (1/5)',
    2: 'Kurang Memuaskan (2/5)',
    3: 'Cukup Baik (3/5)',
    4: 'Memuaskan (4/5)',
    5: 'Sangat Memuaskan (5/5)'
  };

  $('.feedback-star').on('click', function () {
    const rating = parseInt($(this).data('rating'));
    $('#feedbackRatingInput').val(rating);
    $('#feedbackRatingLabel').text(starLabels[rating] || (rating + '/5'));

    $('.feedback-star').each(function () {
      const starRating = parseInt($(this).data('rating'));
      if (starRating <= rating) {
        $(this).css('opacity', '1');
      } else {
        $(this).css('opacity', '0.25');
      }
    });
  });
</script>
@endsection

