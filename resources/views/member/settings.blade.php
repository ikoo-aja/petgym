@extends('layouts.member')

@section('title', 'Profil- PetGym')
@section('page_title', 'Profil')
@section('page_subtitle', '')
@section('member_tier_badge', 'Tier ' . strtoupper($member->membership_tier ?? 'Basic'))

@section('styles')
<style>
  .profile-hero-card {
    background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
    border-radius: 16px;
    color: #ffffff;
    padding: 24px;
    margin-bottom: 20px;
    box-shadow: 0 10px 20px rgba(59, 130, 246, 0.2);
  }
  .profile-avatar-box {
    width: 80px;
    height: 80px;
    border-radius: 16px;
    background: #ffffff;
    color: #1e40af;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 38px;
    font-weight: 900;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  }
  .referral-banner {
    background: linear-gradient(90deg, #3b82f6 0%, #60a5fa 100%);
    color: #ffffff;
    border-radius: 12px;
    padding: 14px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 24px;
  }
  .stat-box {
    background: #ffffff;
    border: 1px solid var(--mp-border);
    border-radius: 12px;
    padding: 16px;
    text-align: center;
  }
  .stat-label {
    font-size: 12px;
    font-weight: 700;
    color: #64748b;
    margin-bottom: 4px;
  }
  .stat-val {
    font-size: 20px;
    font-weight: 800;
    color: #0f172a;
  }
  .btn-gold {
    background-color: #f59e0b !important;
    border-color: #f59e0b !important;
    color: #ffffff !important;
    font-weight: 700 !important;
    border-radius: 10px !important;
    padding: 12px !important;
    font-size: 15px !important;
  }
  .btn-gold:hover {
    background-color: #d97706 !important;
    border-color: #d97706 !important;
  }
  .btn-soft-blue {
    background-color: #e0f2fe !important;
    border-color: #bae6fd !important;
    color: #0284c7 !important;
    font-weight: 700 !important;
    border-radius: 10px !important;
    padding: 12px !important;
    font-size: 15px !important;
  }
  .btn-soft-blue:hover {
    background-color: #bae6fd !important;
  }
  .setting-list-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 16px;
    border-bottom: 1px solid #f1f5f9;
    color: #1e293b;
    text-decoration: none !important;
    transition: background 0.2s ease;
  }
  .setting-list-item:last-child {
    border-bottom: none;
  }
  .setting-list-item:hover {
    background: #f8fafc;
    color: var(--brand-red);
  }
  .setting-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: #f1f5f9;
    color: #475569;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
  }
</style>
@endsection

@section('content')
<div class="row">
  <!-- Left Side: Profile Hero, Club Membership, Body Stats (Foto 1) -->
  <div class="col-lg-7 mb-4">
    <!-- Profile Hero Card -->
    <div class="profile-hero-card">
      <div class="d-flex align-items-center">
        <div class="profile-avatar-circle mr-3">
          <div class="profile-avatar-box">
            {{ substr($user->name ?? 'M', 0, 1) }}
          </div>
        </div>
        <div>
          <h4 class="font-weight-bold text-white mb-1" style="font-size: 20px;">{{ $member->name ?? $user->name }}</h4>
          <p class="mb-1 text-white-50 small"><span class="icon-envelope mr-1"></span> {{ $user->email }}</p>
          <p class="mb-0 text-white-50 small"><span class="icon-phone mr-1"></span> {{ $member->phone ?? '-' }}</p>
        </div>
      </div>
    </div>

    <!-- Referral / Promo Banner -->
    <div class="referral-banner shadow-sm">
      <div class="d-flex align-items-center">
        <div>
          <strong class="d-block style-14" style="font-size: 13.5px;">Dapatkan lebih dari 12 bulan Membership GRATIS!</strong>
          <small class="text-white-50">Bagikan kode referral Anda ke teman & keluarga.</small>
        </div>
      </div>
      <button type="button" class="btn btn-sm btn-light font-weight-bold ml-2 text-primary" style="border-radius: 8px; font-size: 12px;" onclick="showToast('Kode Referral Anda', 'REFF-{{ strtoupper(substr(md5($user->id), 0, 6)) }} — bagikan ke teman Anda.', 'info')">
        Undang Teman
      </button>
    </div>

    <!-- Profile Body Stats (Foto 1) -->
    <div class="card-custom mb-4">
      <h6 class="font-weight-bold text-dark mb-3">Statistik Fisik (Profile Stats)</h6>
      <div class="row">
        <div class="col-4">
          <div class="stat-box">
            <div class="stat-label">Weight (BB)</div>
            <div class="stat-val">- <small class="text-muted" style="font-size:12px;">kg</small></div>
          </div>
        </div>
        <div class="col-4">
          <div class="stat-box">
            <div class="stat-label">Height (TB)</div>
            <div class="stat-val">- <small class="text-muted" style="font-size:12px;">cm</small></div>
          </div>
        </div>
        <div class="col-4">
          <div class="stat-box">
            <div class="stat-label">BMI Score</div>
            <div class="stat-val text-primary">0</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Club Membership Section (Foto 1) -->
    <div class="card-custom mb-4">
      <h6 class="font-weight-bold text-dark mb-3">Club Membership</h6>

      <div class="text-center py-3 mb-3 border rounded bg-light">
        @if($member->status === 'active' && $member->membership_tier)
          <span class="badge badge-success px-3 py-1 font-weight-bold mb-2">Membership Aktif</span>
          <h5 class="font-weight-bold text-dark mb-1">Tier {{ strtoupper($member->membership_tier) }}</h5>
          <p class="text-muted small mb-1">Masa berlaku hingga: <strong>{{ $member->expired_at ? $member->expired_at->format('d M Y') : '-' }}</strong> (Sisa {{ $member->days_left }} hari)</p>
        @else
          <p class="text-muted mb-2 font-weight-semibold">Tidak ada membership yang aktif</p>
        @endif
        <a href="{{ route('member.membership') }}" class="btn btn-sm btn-link text-info font-weight-bold p-0">Lihat Riwayat & Detail Membership &rarr;</a>
      </div>

      <div class="row">
        <div class="col-md-6 mb-2">
          <a href="{{ route('member.membership') }}" class="btn btn-gold btn-block font-weight-bold">
            Aktivasi / Upgrade Membership
          </a>
        </div>
        <div class="col-md-6 mb-2">
          <button type="button" class="btn btn-soft-blue btn-block font-weight-bold" data-toggle="modal" data-target="#dailyPassModal">
            Corporate Daily Pass
          </button>
        </div>
      </div>
    </div>

    <!-- Reformer Pilates / Add-on Access Section (Foto 1) -->
    <div class="card-custom mb-4">
      <h6 class="font-weight-bold text-dark mb-2">Reformer Pilates & Add-on Access</h6>
      <div class="text-center py-3 mb-3 border rounded bg-light">
        <p class="text-muted mb-0 small">Tidak ada Reformer Pilates Access yang aktif</p>
      </div>
      <button type="button" class="btn btn-outline-info btn-block py-2 font-weight-bold" style="border-radius: 10px;" onclick="showToast('Info', 'Layanan Reformer Pilates dapat dibeli melalui kasir resepsionis gym PetGym.', 'info')">
        Lihat Cara Beli Reformer Pilates
      </button>
    </div>
  </div>

  <!-- Right Side: History, Feedback, Account & Logout (Foto 2) -->
  <div class="col-lg-5 mb-4">
    <!-- History Section (Foto 2) -->
    <div class="card-custom mb-4 p-0 overflow-hidden">
      <div class="p-3 border-bottom bg-light">
        <h6 class="font-weight-bold text-dark mb-0">History</h6>
      </div>
      <div>
        <a href="{{ route('member.billing') }}" class="setting-list-item">
          <div class="d-flex align-items-center">
            <span class="setting-icon"><span class="icon-file-text"></span></span>
            <span class="font-weight-semibold" style="font-size: 14px;">Riwayat Pembelian Aplikasi</span>
          </div>
          <span class="text-muted">&rsaquo;</span>
        </a>
        <a href="#activityHistoryModal" data-toggle="modal" class="setting-list-item">
          <div class="d-flex align-items-center">
            <span class="setting-icon"><span class="icon-history"></span></span>
            <span class="font-weight-semibold" style="font-size: 14px;">Riwayat Aktifitas & Presensi</span>
          </div>
          <span class="text-muted">&rsaquo;</span>
        </a>
      </div>
    </div>

    <!-- Feedback Section (Foto 2) -->
    <div class="card-custom mb-4 p-0 overflow-hidden">
      <div class="p-3 border-bottom bg-light">
        <h6 class="font-weight-bold text-dark mb-0">Feedback</h6>
      </div>
      <div>
        <a href="#feedbackModal" data-toggle="modal" class="setting-list-item">
          <div class="d-flex align-items-center">
            <span class="setting-icon text-warning"><span class="icon-star"></span></span>
            <span class="font-weight-semibold" style="font-size: 14px;">Rating Aplikasi PetGym</span>
          </div>
          <span class="text-muted">&rsaquo;</span>
        </a>
        <a href="#feedbackModal" data-toggle="modal" class="setting-list-item">
          <div class="d-flex align-items-center">
            <span class="setting-icon"><span class="icon-pencil"></span></span>
            <span class="font-weight-semibold" style="font-size: 14px;">Beri Feedback untuk PetGym</span>
          </div>
          <span class="text-muted">&rsaquo;</span>
        </a>
      </div>
    </div>

    <!-- Account Section (Foto 2) -->
    <div class="card-custom mb-4 p-0 overflow-hidden">
      <div class="p-3 border-bottom bg-light">
        <h6 class="font-weight-bold text-dark mb-0">Account & Pengaturan</h6>
      </div>
      <div>
        <a href="#voucherModal" data-toggle="modal" class="setting-list-item">
          <div class="d-flex align-items-center">
            <span class="setting-icon text-primary"><span class="icon-shopping-cart"></span></span>
            <span class="font-weight-semibold" style="font-size: 14px;">Voucher & Promo</span>
          </div>
          <span class="text-muted">&rsaquo;</span>
        </a>
        <a href="{{ route('member.guide') }}" class="setting-list-item">
          <div class="d-flex align-items-center">
            <span class="setting-icon text-info"><span class="icon-file-text"></span></span>
            <span class="font-weight-semibold" style="font-size: 14px;">Panduan Penggunaan Portal</span>
          </div>
          <span class="text-muted">&rsaquo;</span>
        </a>
        <a href="{{ route('member.guide') }}" class="setting-list-item">
          <div class="d-flex align-items-center">
            <span class="setting-icon"><span class="icon-file-text"></span></span>
            <span class="font-weight-semibold" style="font-size: 14px;">Syarat dan Ketentuan</span>
          </div>
          <span class="text-muted">&rsaquo;</span>
        </a>
        <a href="{{ route('member.guide') }}" class="setting-list-item">
          <div class="d-flex align-items-center">
            <span class="setting-icon"><span class="icon-lock"></span></span>
            <span class="font-weight-semibold" style="font-size: 14px;">Kebijakan Privasi</span>
          </div>
          <span class="text-muted">&rsaquo;</span>
        </a>
        <a href="#editSecurityModal" data-toggle="modal" class="setting-list-item">
          <div class="d-flex align-items-center">
            <span class="setting-icon"><span class="icon-key"></span></span>
            <span class="font-weight-semibold" style="font-size: 14px;">Keamanan Akun (Sandi & PIN)</span>
          </div>
          <span class="text-muted">&rsaquo;</span>
        </a>
        <a href="#editProfileModal" data-toggle="modal" class="setting-list-item">
          <div class="d-flex align-items-center">
            <span class="setting-icon"><span class="icon-person"></span></span>
            <span class="font-weight-semibold" style="font-size: 14px;">Atur Profil Pribadi</span>
          </div>
          <span class="text-muted">&rsaquo;</span>
        </a>
      </div>
    </div>

    <!-- LOGOUT BUTTON SECTION (Foto 2 Requirement) -->
    <div class="card-custom p-3 border-danger mb-4">
      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-outline-danger btn-block font-weight-bold py-2" style="border-radius: 10px;">
          <span class="icon-power-off mr-1"></span> Keluar dari Akun (Logout)
        </button>
      </form>
    </div>

    <!-- Footer App Version (Foto 2) -->
    <div class="text-center text-muted py-2" style="font-size: 12px;">
      <div class="font-weight-bold">PetGym Member Portal v2.1.0</div>
      <div style="font-size: 10.5px;">&copy; {{ date('Y') }} PT. Jaya Digital Properti. All Rights Reserved</div>
    </div>
  </div>
</div>

<!-- Modal Edit Profil -->
<div class="modal fade" id="editProfileModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-light">
        <h5 class="modal-title font-weight-bold text-dark mb-0">Atur Profil Pribadi</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <form action="{{ route('member.settings.update_profile') }}" method="POST">
        @csrf
        <div class="modal-body p-4">
          <div class="form-group mb-3">
            <label class="font-weight-bold text-dark small">Nama Lengkap *</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $member->name ?? $user->name) }}" required style="border-radius: 8px;">
          </div>
          <div class="form-group mb-3">
            <label class="font-weight-bold text-dark small">Email Akun (Login)</label>
            <input type="email" class="form-control bg-light" value="{{ $user->email }}" readonly disabled style="border-radius: 8px;">
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="font-weight-bold text-dark small">Nomor HP / WhatsApp *</label>
              <input type="text" name="phone" class="form-control" value="{{ old('phone', $member->phone ?? '') }}" required style="border-radius: 8px;">
            </div>
            <div class="col-md-6 mb-3">
              <label class="font-weight-bold text-dark small">Jenis Kelamin *</label>
              <select name="gender" class="form-control" required style="border-radius: 8px;">
                <option value="Laki-laki" {{ (old('gender', $member->gender ?? '') === 'Laki-laki') ? 'selected' : '' }}>Laki-laki</option>
                <option value="Perempuan" {{ (old('gender', $member->gender ?? '') === 'Perempuan') ? 'selected' : '' }}>Perempuan</option>
              </select>
            </div>
          </div>
          <div class="form-group mb-0">
            <label class="font-weight-bold text-dark small">Alamat Tempat Tinggal</label>
            <textarea name="address" class="form-control" rows="3" style="border-radius: 8px;">{{ old('address', $member->address ?? '') }}</textarea>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary font-weight-bold">Simpan Profil</button>
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
        <h5 class="modal-title font-weight-bold text-dark mb-0">Keamanan Akun & Kode PIN</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body p-4">
        <!-- Access Code PIN Display -->
        <div class="p-3 bg-light rounded text-center mb-4 border">
          <small class="text-muted text-uppercase font-weight-bold d-block mb-1">Kode Akses PIN Check-In Anda</small>
          <h3 class="font-weight-bold text-primary mb-0" style="letter-spacing: 4px;">{{ $member->access_code ?? 'MBR-DEFAULT' }}</h3>
          <small class="text-muted">Tunjukkan kode PIN ini pada scanner resepsionis gym.</small>
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
        <h5 class="modal-title font-weight-bold text-dark mb-0">Corporate Daily Pass</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body p-4 text-center">
        <span style="font-size: 48px;">🎫</span>
        <h5 class="font-weight-bold text-dark mt-2 mb-1">Tiket Harian Corporate Pass</h5>
        <p class="text-muted small mb-3">Gunakan pass harian khusus perusahaan mitra PetGym untuk akses 1 hari penuh latihan.</p>
        <div class="p-3 bg-light rounded border mb-3">
          <small class="text-muted d-block mb-1">Harga Pass Harian Standar</small>
          <h4 class="font-weight-bold text-primary mb-0">Rp 50.000 / Hari</h4>
        </div>
        <a href="{{ route('member.billing') }}" class="btn btn-primary btn-block font-weight-bold">Beli Pass Harian Sekarang</a>
      </div>
    </div>
  </div>
</div>

<!-- Modal Activity History -->
<div class="modal fade" id="activityHistoryModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-light">
        <h5 class="modal-title font-weight-bold text-dark mb-0">Riwayat Aktifitas & Presensi</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body p-4 text-center">
        <div class="p-3 bg-light rounded border mb-3">
          <small class="text-muted d-block">Total Presensi Kunjungan Gym</small>
          <h3 class="font-weight-bold text-primary mb-0">{{ $checkInsCount ?? 0 }} Kunjungan</h3>
        </div>
        <p class="text-muted small mb-0">Catatan presensi otomatis tersimpan setiap kali Anda melakukan scan kode akses PIN di lokasi gym PetGym.</p>
      </div>
    </div>
  </div>
</div>

<!-- Modal Feedback -->
<div class="modal fade" id="feedbackModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-light">
        <h5 class="modal-title font-weight-bold text-dark mb-0">Rating & Feedback Aplikasi</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body p-4 text-center">
        <h6 class="font-weight-bold text-dark mb-3">Bagaimana Pengalaman Anda Menggunakan PetGym?</h6>
        <div class="mb-3" style="font-size: 28px;">⭐⭐⭐⭐⭐</div>
        <textarea class="form-control mb-3" rows="3" placeholder="Tuliskan masukan atau kritik saran Anda..." style="border-radius: 8px;"></textarea>
        <button type="button" class="btn btn-primary btn-block font-weight-bold" onclick="showToast('Terima Kasih', 'Feedback Anda telah kami terima. Terima kasih sudah meluangkan waktu!', 'success'); $('#feedbackModal').modal('hide');">Kirim Feedback</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Voucher -->
<div class="modal fade" id="voucherModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-light">
        <h5 class="modal-title font-weight-bold text-dark mb-0">Voucher & Promo Saya</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body p-4">
        <div class="p-3 border border-primary rounded bg-light mb-3 d-flex justify-content-between align-items-center">
          <div>
            <strong class="text-primary d-block font-weight-bold">PROMO MEMBER BARU</strong>
            <small class="text-muted">Diskon 20% Upgrade Tier Keanggotaan</small>
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

  toggleCurrentPasswordBtn.addEventListener('click', function () {
    const isPassword = currentPasswordInput.getAttribute('type') === 'password';
    currentPasswordInput.setAttribute('type', isPassword ? 'text' : 'password');

    toggleCurrentPasswordIcon.classList.toggle('icon-eye');
    toggleCurrentPasswordIcon.classList.toggle('icon-eye-slash');
    toggleCurrentPasswordIcon.classList.toggle('text-primary');
    toggleCurrentPasswordIcon.classList.toggle('text-muted');
  });

  // Toggle lihat/sembunyi password — Kata Sandi Baru
  const toggleNewPasswordBtn = document.querySelector('#toggleNewPasswordBtn');
  const newPasswordInput = document.querySelector('#newPassword');
  const toggleNewPasswordIcon = document.querySelector('#toggleNewPasswordIcon');

  toggleNewPasswordBtn.addEventListener('click', function () {
    const isPassword = newPasswordInput.getAttribute('type') === 'password';
    newPasswordInput.setAttribute('type', isPassword ? 'text' : 'password');

    toggleNewPasswordIcon.classList.toggle('icon-eye');
    toggleNewPasswordIcon.classList.toggle('icon-eye-slash');
    toggleNewPasswordIcon.classList.toggle('text-primary');
    toggleNewPasswordIcon.classList.toggle('text-muted');
  });

  // Toggle lihat/sembunyi password — Konfirmasi Kata Sandi Baru
  const toggleNewPasswordConfirmBtn = document.querySelector('#toggleNewPasswordConfirmBtn');
  const newPasswordConfirmInput = document.querySelector('#newPasswordConfirmation');
  const toggleNewPasswordConfirmIcon = document.querySelector('#toggleNewPasswordConfirmIcon');

  toggleNewPasswordConfirmBtn.addEventListener('click', function () {
    const isPassword = newPasswordConfirmInput.getAttribute('type') === 'password';
    newPasswordConfirmInput.setAttribute('type', isPassword ? 'text' : 'password');

    toggleNewPasswordConfirmIcon.classList.toggle('icon-eye');
    toggleNewPasswordConfirmIcon.classList.toggle('icon-eye-slash');
    toggleNewPasswordConfirmIcon.classList.toggle('text-primary');
    toggleNewPasswordConfirmIcon.classList.toggle('text-muted');
  });
</script>
@endsection
