@extends('layouts.superadmin')

@section('title', 'Profil &mdash; Superadmin Panel')

@section('page_title', 'Profil Saya')
@section('page_subtitle', 'Kelola informasi akun administrator Anda')

@section('content')
<div class="row">
  <div class="col-lg-8">
    <div class="bg-white p-4 rounded shadow-sm mb-4">
      <h5 class="font-weight-bold text-black mb-4">Informasi Pribadi</h5>
      <form action="#" method="POST">
        @csrf
        <div class="form-group row">
          <label class="col-sm-4 col-form-name text-black font-weight-bold">Nama Lengkap</label>
          <div class="col-sm-8">
            <input type="text" class="form-control" name="name" value="{{ Auth::user()->name ?? 'Superadmin' }}">
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-4 col-form-name text-black font-weight-bold">Alamat Email</label>
          <div class="col-sm-8">
            <input type="email" class="form-control" name="email" value="{{ Auth::user()->email ?? 'admin@workout.id' }}">
          </div>
        </div>
        <div class="row mt-4">
          <div class="col-sm-8 offset-sm-4">
            <button type="submit" class="btn btn-primary btn-sm px-4">Perbarui Profil</button>
          </div>
        </div>
      </form>
    </div>

    <div class="bg-white p-4 rounded shadow-sm">
      <h5 class="font-weight-bold text-black mb-4">Ubah Password</h5>
      <form action="#" method="POST">
        @csrf
        <div class="form-group row">
          <label class="col-sm-4 col-form-name text-black font-weight-bold">Password Saat Ini</label>
          <div class="col-sm-8">
            <input type="password" class="form-control" name="current_password">
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-4 col-form-name text-black font-weight-bold">Password Baru</label>
          <div class="col-sm-8">
            <input type="password" class="form-control" name="new_password">
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-4 col-form-name text-black font-weight-bold">Konfirmasi Password Baru</label>
          <div class="col-sm-8">
            <input type="password" class="form-control" name="new_password_confirmation">
          </div>
        </div>
        <div class="row mt-4">
          <div class="col-sm-8 offset-sm-4">
            <button type="submit" class="btn btn-primary btn-sm px-4">Ganti Password</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="bg-white p-4 rounded shadow-sm text-center">
      <div class="mb-3">
        <div class="d-inline-flex align-items-center justify-content-center bg-secondary text-white rounded-circle font-weight-bold" style="width: 100px; height: 100px; font-size: 36px;">
          {{ strtoupper(substr(Auth::user()->name ?? 'S', 0, 1)) }}
        </div>
      </div>
      <h5 class="font-weight-bold text-black mb-1">{{ Auth::user()->name ?? 'Superadmin' }}</h5>
      <p class="text-muted mb-3">{{ Auth::user()->email ?? 'admin@workout.id' }}</p>
      <span class="badge badge-primary px-3 py-2">ROLE: SUPERADMIN</span>
    </div>
  </div>
</div>
@endsection
