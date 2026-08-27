@extends('layouts.member')

@section('title', 'Pengaturan Akun Member - PetGym')
@section('page_title', 'Pengaturan Akun & Keamanan')
@section('page_subtitle', 'Kelola informasi profil pribadi, alamat, jenis kelamin, dan perbarui kata sandi akun Anda.')
@section('member_tier_badge', 'Tier ' . strtoupper($member->membership_tier ?? 'Basic'))

@section('content')
<div class="row">
  <!-- Profile Info Form -->
  <div class="col-md-7 mb-4">
    <div class="card-custom h-100">
      <h6 class="font-weight-bold text-dark mb-3">Informasi Profil Member</h6>
      <form action="{{ route('member.settings.update_profile') }}" method="POST">
        @csrf
        
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark small">Nama Lengkap</label>
          <input type="text" name="name" class="form-control" value="{{ old('name', $member->name ?? $user->name) }}" required>
        </div>

        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark small">Alamat Email (Akun Login)</label>
          <input type="email" class="form-control bg-light" value="{{ $user->email }}" readonly disabled>
          <small class="text-muted">Email terikat pada sistem login dan tidak dapat diubah secara mandiri.</small>
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <div class="form-group mb-0">
              <label class="font-weight-bold text-dark small">Nomor Telepon / WhatsApp</label>
              <input type="text" name="phone" class="form-control" value="{{ old('phone', $member->phone ?? '') }}" required>
            </div>
          </div>

          <div class="col-md-6 mb-3">
            <div class="form-group mb-0">
              <label class="font-weight-bold text-dark small">Jenis Kelamin</label>
              <select name="gender" class="form-control" required>
                <option value="Laki-laki" {{ (old('gender', $member->gender ?? '') === 'Laki-laki') ? 'selected' : '' }}>Laki-laki</option>
                <option value="Perempuan" {{ (old('gender', $member->gender ?? '') === 'Perempuan') ? 'selected' : '' }}>Perempuan</option>
              </select>
            </div>
          </div>
        </div>

        <div class="form-group mb-4">
          <label class="font-weight-bold text-dark small">Alamat Tempat Tinggal</label>
          <textarea name="address" class="form-control" rows="3">{{ old('address', $member->address ?? '') }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary font-weight-bold">Simpan Perubahan Profil</button>
      </form>
    </div>
  </div>

  <!-- Security & Password Form -->
  <div class="col-md-5 mb-4">
    <!-- Access Code Display Card -->
    <div class="card-custom mb-4 border-left border-primary" style="border-left-width: 4px !important;">
      <h6 class="font-weight-bold text-dark mb-1">Kode Akses Member Gym</h6>
      <p class="text-muted small mb-2">Gunakan kode akses statis ini untuk presensi check-in di meja depan.</p>
      <div class="d-inline-block bg-light text-dark font-weight-bold px-3 py-2 rounded border" style="font-size: 18px; letter-spacing: 3px;">
        {{ $member->access_code ?? 'MBR-DEFAULT' }}
      </div>
    </div>

    <!-- Password Form -->
    <div class="card-custom">
      <h6 class="font-weight-bold text-dark mb-3">Ubah Kata Sandi</h6>
      <form action="{{ route('member.settings.update_password') }}" method="POST">
        @csrf

        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark small">Kata Sandi Saat Ini</label>
          <input type="password" name="current_password" class="form-control" required>
        </div>

        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark small">Kata Sandi Baru</label>
          <input type="password" name="new_password" class="form-control" required>
          <small class="text-muted">Minimal 6 karakter.</small>
        </div>

        <div class="form-group mb-4">
          <label class="font-weight-bold text-dark small">Konfirmasi Kata Sandi Baru</label>
          <input type="password" name="new_password_confirmation" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-outline-primary btn-block font-weight-bold">Perbarui Kata Sandi</button>
      </form>
    </div>
  </div>
</div>
@endsection
