@extends('layouts.superadmin')

@section('title', 'Profil &mdash; Superadmin Panel')

@section('page_title', 'Profil Saya')
@section('page_subtitle', 'Kelola informasi akun administrator Anda')

@section('content')
<div class="row">
  <div class="col-lg-8">
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-radius: 10px;">
      <ul class="mb-0 pl-3">
        @foreach($errors->all() as $err)
          <li class="font-weight-bold small">{{ $err }}</li>
        @endforeach
      </ul>
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
    @endif

    <!-- 1. Form Informasi Pribadi -->
    <div class="bg-white p-4 rounded shadow-sm mb-4">
      <h5 class="font-weight-bold text-black mb-4">Informasi Pribadi</h5>
      <form action="{{ route('account.profile.update') }}" method="POST">
        @csrf
        <div class="form-group row">
          <label class="col-sm-4 col-form-label text-black font-weight-bold">Nama Lengkap <span class="text-danger">*</span></label>
          <div class="col-sm-8">
            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', Auth::user()->name ?? 'Superadmin') }}" required>
            @error('name')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-4 col-form-label text-black font-weight-bold">Alamat Email <span class="text-danger">*</span></label>
          <div class="col-sm-8">
            <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', Auth::user()->email ?? 'admin@workout.id') }}" required>
            @error('email')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
        <div class="row mt-4">
          <div class="col-sm-8 offset-sm-4">
            <button type="submit" class="btn btn-primary btn-sm px-4 font-weight-bold">
              <span class="icon-save mr-1"></span> Perbarui Profil
            </button>
          </div>
        </div>
      </form>
    </div>

    <!-- 2. Form Ubah Kata Sandi -->
    <div class="bg-white p-4 rounded shadow-sm">
      <h5 class="font-weight-bold text-black mb-4">Ubah Kata Sandi</h5>
      <form action="{{ route('account.password.update') }}" method="POST">
        @csrf
        <div class="form-group row">
          <label class="col-sm-4 col-form-label text-black font-weight-bold">Kata Sandi Saat Ini <span class="text-danger">*</span></label>
          <div class="col-sm-8">
            <input type="password" class="form-control @error('current_password') is-invalid @enderror" name="current_password" required>
            @error('current_password')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-4 col-form-label text-black font-weight-bold">Kata Sandi Baru <span class="text-danger">*</span></label>
          <div class="col-sm-8">
            <input type="password" class="form-control @error('new_password') is-invalid @enderror" name="new_password" required minlength="4">
            <small class="text-muted">Minimal 4 karakter.</small>
            @error('new_password')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-4 col-form-label text-black font-weight-bold">Konfirmasi Kata Sandi Baru <span class="text-danger">*</span></label>
          <div class="col-sm-8">
            <input type="password" class="form-control" name="new_password_confirmation" required minlength="4">
          </div>
        </div>
        <div class="row mt-4">
          <div class="col-sm-8 offset-sm-4">
            <button type="submit" class="btn btn-primary btn-sm px-4 font-weight-bold">
              <span class="icon-lock mr-1"></span> Simpan Kata Sandi Baru
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="bg-white p-4 rounded shadow-sm text-center">
      <div class="mb-3">
        <div class="d-inline-flex align-items-center justify-content-center bg-dark text-white rounded-circle font-weight-bold shadow-sm" style="width: 100px; height: 100px; font-size: 36px; border: 4px solid #f38181;">
          {{ strtoupper(substr(Auth::user()->name ?? 'S', 0, 1)) }}
        </div>
      </div>
      <h5 class="font-weight-bold text-black mb-1">{{ Auth::user()->name ?? 'Superadmin' }}</h5>
      <p class="text-muted mb-3" style="font-family: monospace;">{{ Auth::user()->email ?? 'admin@workout.id' }}</p>
      <span class="badge badge-primary px-3 py-2 font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">ROLE: SUPERADMIN</span>
    </div>
  </div>
</div>
@endsection
