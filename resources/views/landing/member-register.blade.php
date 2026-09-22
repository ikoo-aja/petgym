@php
  $primary   = $settings->primary_color ?: '#f43f5e';
  $secondary = $settings->secondary_color ?: '#111827';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Pendaftaran Member — {{ $tenant->name }}</title>
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
      <a href="{{ route('tenant.landing', ['slug' => $tenant->slug]) }}" class="brand-name">
        @if($tenant->logo_url)
          <img src="{{ asset($tenant->logo_url) }}" alt="{{ $tenant->name }}" style="height: 38px;" class="mr-2">
        @endif
        {{ $tenant->name }}<span>.</span>
      </a>
    </div>
  </div>

  <!-- Main Register Section -->
  <div class="py-5 my-auto">
    <div class="container">

      <div class="row justify-content-center text-center mb-4">
        <div class="col-md-8">
          <span class="text-uppercase font-weight-bold small" style="color: var(--brand); letter-spacing: 1.5px;">Portal Member</span>
          <h2 class="font-weight-bold text-dark mt-1 mb-2">Formulir Member Baru {{ $tenant->name }}</h2>
          <p class="text-muted small">Buat akun keanggotaan Anda sekarang untuk bergabung bersama {{ $tenant->name }}.</p>
        </div>
      </div>

      <div class="row justify-content-center">
        <div class="col-md-6">

          <form action="{{ route('tenant.register.submit', ['slug' => $tenant->slug]) }}" method="POST" class="bg-white p-4 p-md-5 card-register">
            @csrf
            <input type="hidden" name="tenant_id" value="{{ $tenant->id }}">

            <!-- Alert Errors -->
            @if($errors->any())
              <div class="alert alert-danger py-2 mb-4" role="alert" style="border-radius: 8px;">
                <ul class="mb-0 pl-3">
                  @foreach($errors->all() as $err)
                    <li class="small font-weight-bold">{{ $err }}</li>
                  @endforeach
                </ul>
              </div>
            @endif

            <div class="form-group mb-3">
              <label for="name" class="text-dark font-weight-bold small">Nama Lengkap Member *</label>
              <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-control" placeholder="Contoh: Budi Santoso" required autofocus style="height: 48px; border-radius: 8px; font-size: 14px;">
            </div>

            <div class="form-group mb-3">
              <label for="email" class="text-dark font-weight-bold small">Email Member *</label>
              <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="budi@example.com" required style="height: 48px; border-radius: 8px; font-size: 14px;">
            </div>

            <div class="form-group mb-3">
              <label for="phone" class="text-dark font-weight-bold small">Nomor WhatsApp / HP *</label>
              <input type="text" id="phone" name="phone" value="{{ old('phone') }}" class="form-control" placeholder="081234567890" required style="height: 48px; border-radius: 8px; font-size: 14px;">
            </div>

            <div class="row">
              <div class="col-md-6 form-group mb-3">
                <label for="password" class="text-dark font-weight-bold small">Kata Sandi *</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Min. 4 karakter" required style="height: 48px; border-radius: 8px; font-size: 14px;">
              </div>
              <div class="col-md-6 form-group mb-3">
                <label for="password_confirmation" class="text-dark font-weight-bold small">Konfirmasi Kata Sandi *</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Ulangi kata sandi" required style="height: 48px; border-radius: 8px; font-size: 14px;">
              </div>
            </div>

            <div class="form-group mb-4 mt-2">
              <button type="submit" class="btn btn-brand btn-block shadow-sm">
                Daftar Member {{ $tenant->name }}
              </button>
            </div>

            <div class="text-center mt-4 pt-3 border-top">
              <span class="text-muted small">Sudah memiliki akun member {{ $tenant->name }}?</span>
              <a href="{{ route('tenant.login', ['slug' => $tenant->slug]) }}" class="font-weight-bold ml-1" style="color: var(--brand);">Masuk / Login Di Sini</a>
            </div>
          </form>

        </div>
      </div>

    </div>
  </div>

</body>
</html>
