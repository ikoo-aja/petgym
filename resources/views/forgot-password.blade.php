<!DOCTYPE html>
<html lang="id">

<head>
  <title>Lupa Password — Pet Gym Management System</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <x-dynamic-favicon />

  <link href="https://fonts.googleapis.com/css?family=Muli:300,400,700,900" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('fonts/icomoon/style.css') }}">

  <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/jquery-ui.css') }}">
  <link rel="stylesheet" href="{{ asset('css/owl.carousel.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/owl.theme.default.min.css') }}">

  <link rel="stylesheet" href="{{ asset('css/jquery.fancybox.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/bootstrap-datepicker.css') }}">
  <link rel="stylesheet" href="{{ asset('fonts/flaticon/font/flaticon.css') }}">

  <link rel="stylesheet" href="{{ asset('css/aos.css') }}">
  <link href="{{ asset('css/jquery.mb.YTPlayer.min.css') }}" media="all" rel="stylesheet" type="text/css">

  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body data-spy="scroll" data-target=".site-navbar-target" data-offset="300" class="bg-light">

  <div class="site-wrap">

    <!-- Header Logo Sederhana -->
    <div class="py-3 bg-white border-bottom shadow-sm">
      <div class="container text-center">
        <div class="site-logo d-flex justify-content-center align-items-center">
          <x-brand-logo type="full" theme="light" size="44" url="/" />
        </div>
      </div>
    </div>

    <!-- Main Forgot Password Section -->
    <div class="site-section bg-light contact-wrap" style="padding-top: 60px; padding-bottom: 80px; min-height: calc(100vh - 220px);">
      <div class="container">

        <div class="row justify-content-center text-center mb-4" data-aos="fade-up">
          <div class="col-md-8 section-heading mb-2">
            <span class="subheading text-primary font-weight-bold" style="letter-spacing: 1px;">Pet Gym Platform</span>
            <h2 class="heading mb-2 text-dark font-weight-bold">Lupa Password?</h2>
            <p class="text-muted">Masukkan email akun Anda, kami akan mengirimkan link untuk mengatur ulang password.</p>
          </div>
        </div>

        <div class="row justify-content-center">
          <div class="col-md-5" data-aos="fade-up" data-aos-delay="100">
            <form action="{{ route('password.email') }}" method="POST" class="bg-white p-5 shadow-sm rounded-lg border">
              @csrf

              <!-- Alert Error Validasi -->
              @if($errors->has('email'))
                <div class="alert alert-danger py-2 mb-4" role="alert">
                  <small class="font-weight-bold"><i class="icon-exclamation-circle mr-1"></i> {{ $errors->first('email') }}</small>
                </div>
              @endif

              <!-- Alert Sukses / Info (anti user enumeration: pesan sama untuk email terdaftar/tidak) -->
              @if(session('status'))
                <div class="alert alert-success py-2 mb-4" role="alert">
                  <small class="font-weight-bold"><i class="icon-check-circle mr-1"></i> {{ session('status') }}</small>
                </div>
              @endif

              <div class="form-group mb-3">
                <label for="email" class="text-dark font-weight-bold small">Email Address *</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control form-control-lg" placeholder="Enter your email" required autofocus style="font-size: 15px;">
              </div>

              <div class="form-group mb-3">
                <button type="submit" class="btn btn-primary py-3 px-5 btn-block font-weight-bold shadow-sm" style="border-radius: 30px; font-size: 16px;">
                  Kirim Link Reset Password
                </button>
              </div>

              <div class="text-center mt-4 pt-2 border-top">
                <a href="{{ route('login') }}" class="text-primary small font-weight-bold">← Kembali ke Halaman Login</a>
              </div>
            </form>
          </div>
        </div>

      </div>
    </div>

    <!-- Footer -->
    <footer class="footer-section bg-dark py-5">
      <div class="container text-center">
        <h3 class="text-white mb-2">Pet Gym SaaS Management</h3>
        <p class="text-white-50 mb-3 small">Platform terpadu kendali operasional, presensi member, POS kasir, dan manajemen kelas gym Anda.</p>
        <p class="mb-0 text-white-50 small">
          Copyright &copy; <script>document.write(new Date().getFullYear());</script> All rights reserved | Pet Gym Management System
        </p>
      </div>
    </footer>

  </div>
  <!-- .site-wrap -->

  <script src="{{ asset('js/jquery-3.3.1.min.js') }}"></script>
  <script src="{{ asset('js/jquery-migrate-3.0.1.min.js') }}"></script>
  <script src="{{ asset('js/jquery-ui.js') }}"></script>
  <script src="{{ asset('js/popper.min.js') }}"></script>
  <script src="{{ asset('js/bootstrap.min.js') }}"></script>
  <script src="{{ asset('js/owl.carousel.min.js') }}"></script>
  <script src="{{ asset('js/jquery.stellar.min.js') }}"></script>
  <script src="{{ asset('js/jquery.countdown.min.js') }}"></script>
  <script src="{{ asset('js/bootstrap-datepicker.min.js') }}"></script>
  <script src="{{ asset('js/jquery.easing.1.3.js') }}"></script>
  <script src="{{ asset('js/aos.js') }}"></script>
  <script src="{{ asset('js/jquery.fancybox.min.js') }}"></script>
  <script src="{{ asset('js/jquery.sticky.js') }}"></script>
  <script src="{{ asset('js/jquery.mb.YTPlayer.min.js') }}"></script>
  <script src="{{ asset('js/main.js') }}"></script>

</body>

</html>