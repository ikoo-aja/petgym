@php
  $primary   = $settings->primary_color ?: '#f43f5e';
  $secondary = $settings->secondary_color ?: '#111827';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Login Member — {{ $tenant->name }}</title>
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
    .card-login {
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
    .btn-outline-brand {
      border: 1.5px solid var(--brand);
      color: var(--brand);
      font-weight: 700;
      border-radius: 8px;
      padding: 10px;
      font-size: 13.5px;
    }
    .btn-outline-brand:hover {
      background-color: var(--brand);
      color: #ffffff;
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

  <!-- Main Login Section -->
  <div class="py-5 my-auto">
    <div class="container">

      <div class="row justify-content-center text-center mb-4">
        <div class="col-md-8">
          <span class="text-uppercase font-weight-bold small" style="color: var(--brand); letter-spacing: 1.5px;">Portal Member</span>
          <h2 class="font-weight-bold text-dark mt-1 mb-2">Login Member {{ $tenant->name }}</h2>
          <p class="text-muted small">Masuk ke akun keanggotaan Anda untuk cek kuota PT, reservasi kelas, dan presensi.</p>
        </div>
      </div>

      <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">

          <form action="{{ route('tenant.login', ['slug' => $tenant->slug]) }}" method="POST" class="bg-white p-4 p-md-5 card-login">
            @csrf

            <!-- Alert Error -->
            @if($errors->has('email'))
              <div class="alert alert-danger py-2 mb-4" role="alert" style="border-radius: 8px;">
                <small class="font-weight-bold"><i class="icon-exclamation-circle mr-1"></i> {{ $errors->first('email') }}</small>
              </div>
            @endif

            <div class="form-group mb-3">
              <label for="email" class="text-dark font-weight-bold small">Alamat Email Member</label>
              <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="member@email.com" required autofocus style="height: 48px; border-radius: 8px; font-size: 14px;">
            </div>

            <div class="form-group mb-3">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <label for="password" class="text-dark font-weight-bold small mb-0">Kata Sandi</label>
                <a href="{{ route('password.request') }}" class="small font-weight-bold" style="color: var(--brand);">Lupa kata sandi?</a>
              </div>
              <div class="input-group">
                <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan kata sandi Anda" required style="height: 48px; border-top-left-radius: 8px; border-bottom-left-radius: 8px; font-size: 14px;">
                <div class="input-group-append">
                  <button class="btn btn-outline-secondary border-left-0 bg-white text-muted" type="button" id="togglePasswordBtn" style="border-top-right-radius: 8px; border-bottom-right-radius: 8px; border-color: #ced4da;">
                    <i class="icon-eye" id="toggleIcon"></i>
                  </button>
                </div>
              </div>
            </div>

            <div class="form-group mb-4">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="rememberMe" name="remember">
                <label class="custom-control-label text-muted small" for="rememberMe">Ingat saya di perangkat ini</label>
              </div>
            </div>

            <div class="form-group mb-4">
              <button type="submit" class="btn btn-brand btn-block shadow-sm">
                Masuk ke {{ $tenant->name }}
              </button>
            </div>

            <!-- Footer Pendaftaran Member -->
            <div class="pt-4 border-top text-center">
              <p class="text-muted small mb-2">Belum menjadi member {{ $tenant->name }}?</p>
              <a href="{{ route('tenant.register', ['slug' => $tenant->slug]) }}" class="btn btn-outline-brand btn-block text-decoration-none">
                Daftar Member {{ $tenant->name }} Baru
              </a>
            </div>

            <div class="text-center mt-4 pt-2">
              <a href="{{ route('tenant.landing', ['slug' => $tenant->slug]) }}" class="text-muted small">← Kembali ke Halaman Utama {{ $tenant->name }}</a>
            </div>
          </form>

        </div>
      </div>

    </div>
  </div>

  <script>
    const togglePasswordBtn = document.querySelector('#togglePasswordBtn');
    const passwordInput = document.querySelector('#password');
    const toggleIcon = document.querySelector('#toggleIcon');

    togglePasswordBtn.addEventListener('click', function () {
      const isPassword = passwordInput.getAttribute('type') === 'password';
      passwordInput.setAttribute('type', isPassword ? 'text' : 'password');

      toggleIcon.classList.toggle('icon-eye');
      toggleIcon.classList.toggle('icon-eye-slash');
    });
  </script>

</body>
</html>
