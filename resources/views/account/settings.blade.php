@extends('layouts.layout')

@section('title', 'Pengaturan Akun')
@section('page_title', 'Pengaturan Akun')
@section('page_subtitle', 'Kelola informasi profil, nama pengguna, dan keamanan kata sandi akun Anda')

@section('content')
<div class="container-fluid py-2">

    <div class="row">
        <!-- Kolom Kiri: Informasi Profil & Username -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm bg-white h-100" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom pt-4 px-4 pb-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-primary mr-3" style="width: 42px; height: 42px; min-width: 42px;">
                            <span class="icon-person" style="font-size: 20px;"></span>
                        </div>
                        <div>
                            <h6 class="font-weight-bold text-dark mb-0">Informasi Profil</h6>
                            <small class="text-muted">Perbarui nama pengguna dan alamat email Anda</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('account.profile.update') }}" method="POST">
                        @csrf

                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark small mb-1">Nama Lengkap / Nama Pengguna <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required placeholder="Masukkan nama atau nama pengguna">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark small mb-1">Alamat Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required placeholder="contoh@domain.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted" style="font-size: 11.5px;">Email digunakan untuk proses masuk (login) dan notifikasi sistem.</small>
                        </div>

                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-dark small mb-1">Peran Akun</label>
                            <input type="text" class="form-control bg-light" value="{{ ucfirst($user->role) }}" readonly style="cursor: not-allowed;">
                        </div>

                        <div class="text-right">
                            <button type="submit" class="btn btn-primary font-weight-bold px-4" style="border-radius: 8px;">
                                Simpan Perubahan Profil
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Keamanan & Ganti Password -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm bg-white h-100" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom pt-4 px-4 pb-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-primary mr-3" style="width: 42px; height: 42px; min-width: 42px;">
                            <span class="icon-lock" style="font-size: 20px;"></span>
                        </div>
                        <div>
                            <h6 class="font-weight-bold text-dark mb-0">Keamanan &amp; Ganti Kata Sandi</h6>
                            <small class="text-muted">Pastikan akun Anda menggunakan kombinasi kata sandi yang kuat</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('account.password.update') }}" method="POST">
                        @csrf

                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark small mb-1">Kata Sandi Saat Ini <span class="text-danger">*</span></label>
                            <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required placeholder="Masukkan kata sandi saat ini">
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark small mb-1">Kata Sandi Baru <span class="text-danger">*</span></label>
                            <input type="password" name="new_password" class="form-control @error('new_password') is-invalid @enderror" required placeholder="Minimal 4 karakter">
                            @error('new_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-dark small mb-1">Konfirmasi Kata Sandi Baru <span class="text-danger">*</span></label>
                            <input type="password" name="new_password_confirmation" class="form-control" required placeholder="Ulangi kata sandi baru">
                        </div>

                        <div class="text-right">
                            <button type="submit" class="btn btn-primary font-weight-bold px-4" style="border-radius: 8px;">
                                Perbarui Kata Sandi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
