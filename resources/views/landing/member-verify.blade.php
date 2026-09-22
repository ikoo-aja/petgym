@php
  $tenant = $tenant ?? ($user ? $user->tenant : null);
  $settings = $settings ?? ($tenant ? $tenant->landingSettings() : null);
  $primary   = $settings && $settings->primary_color ? $settings->primary_color : '#f43f5e';
  $secondary = $settings && $settings->secondary_color ? $settings->secondary_color : '#111827';
  $tenantName = $tenant ? $tenant->name : 'Gym Portal';
  $tenantLoginUrl = $tenant ? $tenant->publicLandingUrl() . '/login' : route('member.login');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Verifikasi Email — {{ $tenantName }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Muli:wght@300;400;600;700;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('fonts/icomoon/style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
  <style>
    :root {
      --brand: {{ $primary }};
      --brand-dark: {{ $secondary }};
    }
    body {
      font-family: 'Muli', sans-serif;
      background-color: #f8fafc;
      color: #1f2937;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
    .header-bar {
      background: var(--brand-dark);
      padding: 16px 0;
      border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    .brand-name {
      color: #ffffff;
      font-weight: 900;
      font-size: 22px;
      letter-spacing: -0.5px;
      text-decoration: none;
    }
    .brand-name span {
      color: var(--brand);
    }
    .brand-name:hover {
      color: #ffffff;
      text-decoration: none;
    }
    .card-register {
      border: 1px solid #e2e8f0;
      border-radius: 14px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.06);
    }
    .btn-brand {
      background-color: var(--brand);
      color: #ffffff;
      font-weight: 700;
      border-radius: 8px;
      height: 48px;
      font-size: 15px;
      border: none;
      transition: opacity 0.2s;
    }
    .btn-brand:hover {
      color: #ffffff;
      opacity: 0.9;
    }
  </style>
</head>
<body>

  <!-- Topbar Brand Header -->
  <div class="header-bar text-center">
    <div class="container">
      <a href="{{ $tenant ? $tenant->publicLandingUrl() : '/' }}" class="brand-name">
        @if($tenant && $tenant->logo_url)
          <img src="{{ asset($tenant->logo_url) }}" alt="{{ $tenantName }}" style="height: 38px;" class="mr-2">
        @endif
        {{ $tenantName }}<span>.</span>
      </a>
    </div>
  </div>

  <!-- Main Verification Section -->
  <div class="py-5 my-auto">
    <div class="container">

      <div class="row justify-content-center text-center mb-4">
        <div class="col-md-8">
          <span class="text-uppercase font-weight-bold small" style="color: var(--brand); letter-spacing: 1.5px;">Portal Member</span>
          <h2 class="font-weight-bold text-dark mt-1 mb-2">Verifikasi Email {{ $tenantName }}</h2>
          <p class="text-muted small">Sebelum mengakses dasbor anggota, silakan verifikasi alamat email Anda terlebih dahulu.</p>
        </div>
      </div>

      <div class="row justify-content-center">
        <div class="col-md-6">
          <div class="bg-white p-4 p-md-5 card-register">

            @if(session('success'))
              <div class="alert alert-success py-2 mb-4" role="alert" style="border-radius: 8px; font-size: 13.5px;">
                <i class="icon-check-circle mr-1"></i> {{ session('success') }}
              </div>
            @endif

            <div class="text-center mb-4">
              <span class="icon-envelope-o d-inline-flex align-items-center justify-content-center" style="font-size: 48px; color: var(--brand);"></span>
              <h5 class="font-weight-bold text-dark mt-3 mb-1">Periksa Kotak Masuk Email Anda</h5>
              <p class="text-muted small mb-0">
                Tautan verifikasi telah dikirimkan ke alamat email:<br>
                <strong class="text-dark" style="font-size: 14px;">{{ $user->email }}</strong>
              </p>
            </div>

            <div class="alert alert-info py-3 mb-4 rounded" role="alert" style="background-color: #e0f2fe; color: #0369a1; font-size: 13px; border: none;">
              <i class="icon-exclamation-circle mr-1"></i>
              Tautan verifikasi telah dikirim ke kotak masuk email Anda. Silakan buka email dari <strong>{{ $tenantName }}</strong> dan klik tombol <strong>Verifikasi Email Member</strong>.
            </div>

            <form action="{{ route('verification.send') }}" method="POST" class="mb-3">
              @csrf
              <button type="submit" class="btn btn-brand btn-block">
                <i class="icon-envelope-o mr-1"></i> Kirim Ulang Tautan Verifikasi
              </button>
            </form>

            <div class="text-center mt-4 pt-3 border-top d-flex justify-content-between align-items-center" style="font-size: 13px;">
              <a href="{{ $tenantLoginUrl }}" style="color: var(--brand);" class="font-weight-bold">Sudah verifikasi? Masuk</a>
              <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-link text-muted p-0 font-weight-bold" style="font-size: 13px; text-decoration: none;">Keluar</button>
              </form>
            </div>

          </div>
        </div>
      </div>

    </div>
  </div>

</body>
</html>
