<!DOCTYPE html>
<html lang="id">

<head>
  <title>Login Portal Member Gym — Pet Gym Platform</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <x-dynamic-favicon />

  <link href="https://fonts.googleapis.com/css?family=Muli:300,400,700,900" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('fonts/icomoon/style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body class="bg-light">

  <div class="site-wrap">

    <!-- Header Logo -->
    <div class="py-3 bg-white border-bottom shadow-sm">
      <div class="container text-center">
        <div class="site-logo d-flex justify-content-center align-items-center">
          <x-brand-logo type="full" theme="light" size="44" url="/" />
        </div>
      </div>
    </div>

    <!-- Main Member Login Section -->
    <div class="site-section bg-light contact-wrap" style="padding-top: 60px; padding-bottom: 80px; min-height: calc(100vh - 220px);">
      <div class="container">

        <div class="row justify-content-center text-center mb-4" data-aos="fade-up">
          <div class="col-md-8 section-heading mb-2">
            <span class="subheading text-primary font-weight-bold" style="letter-spacing: 1px;">Portal Member Gym</span>
            <h2 class="heading mb-2 text-dark font-weight-bold">Login Akun Member</h2>
            <p class="text-muted">Masuk ke portal keanggotaan gym Anda untuk cek sisa kuota PT, reservasi kelas, dan presensi.</p>
          </div>
        </div>

        <div class="row justify-content-center">
          <div class="col-md-6 col-lg-5" data-aos="fade-up" data-aos-delay="100">

            <form action="{{ route('member.login') }}" method="POST" class="bg-white p-4 p-md-5 shadow-sm rounded-lg border" style="border-radius: 12px;">
              @csrf

              <!-- Alert Error Gagal Login -->
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
                  <label for="password" class="text-dark font-weight-bold small mb-0">Password</label>
                  <a href="{{ route('password.request') }}" class="text-primary small font-weight-bold">Lupa password?</a>
                </div>
                <div class="input-group">
                  <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan password Anda" required style="height: 48px; border-top-left-radius: 8px; border-bottom-left-radius: 8px; font-size: 14px;">
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
                <button type="submit" class="btn btn-primary btn-block font-weight-bold shadow-sm" style="height: 48px; border-radius: 8px; font-size: 15px;">
                  Masuk ke Portal Member
                </button>
              </div>

              <!-- Section Belum Memiliki Akun Member -->
              <div class="pt-4 border-top text-center">
                <p class="text-muted small mb-2">Belum memiliki akun member gym?</p>
                <a href="{{ route('member.register') }}" class="btn btn-outline-primary btn-block font-weight-bold py-2" style="border-radius: 8px; font-size: 13.5px;">
                  Daftar Member Gym Baru
                </a>
              </div>

              <div class="text-center mt-4 pt-2">
                <a href="{{ url('/') }}" class="text-muted small">← Kembali ke Halaman Utama</a>
              </div>
            </form>
          </div>
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
      toggleIcon.classList.toggle('text-primary');
      toggleIcon.classList.toggle('text-muted');
    });
  </script>

</body>

</html>
