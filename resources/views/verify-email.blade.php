<!DOCTYPE html>
<html lang="id">

<head>
  <title>Verifikasi Email — Pet Gym Management System</title>
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

    <!-- Main Verification Section -->
    <div class="site-section bg-light contact-wrap" style="padding-top: 60px; padding-bottom: 80px; min-height: calc(100vh - 220px);">
      <div class="container">

        <div class="row justify-content-center text-center mb-4" data-aos="fade-up">
          <div class="col-md-8 section-heading mb-2">
            <span class="subheading text-primary font-weight-bold" style="letter-spacing: 1px;">Keamanan Akun</span>
            <h2 class="heading mb-2 text-dark font-weight-bold">Verifikasi Email Anda</h2>
            <p class="text-muted">Sebelum mengakses dashboard, kami perlu memastikan email ini benar-benar milik Anda.</p>
          </div>
        </div>

        <div class="row justify-content-center">
          <div class="col-md-6" data-aos="fade-up" data-aos-delay="100">
            <div class="bg-white p-5 shadow-sm rounded-lg border">

              <!-- Alert Sukses -->
              @if(session('success'))
                <div class="alert alert-success py-2 mb-4" role="alert">
                  <small class="font-weight-bold"><i class="icon-check-circle mr-1"></i> {{ session('success') }}</small>
                </div>
              @endif

              <div class="text-center mb-4">
                <span class="icon-envelope-o d-inline-flex align-items-center justify-content-center text-primary" style="font-size: 44px;"></span>
                <h6 class="font-weight-bold text-dark mt-3 mb-1">Cek Inbox Email Anda</h6>
                <p class="text-muted small mb-0">
                  Kami sudah mengirimkan link verifikasi ke:<br>
                  <strong class="text-dark">{{ Auth::user()->email }}</strong>
                </p>
              </div>

              <div class="alert alert-info py-3 mb-4" role="alert">
                <small class="font-weight-bold">
                  <i class="icon-exclamation-circle mr-1"></i>
                  Link verifikasi berlaku <strong>60 menit</strong> dan hanya bisa dipakai sekali. Jika tidak menemukannya, periksa folder spam.
                </small>
              </div>

              <div class="form-group mb-3">
                <form action="{{ route('verification.send') }}" method="POST">
                  @csrf
                  <button type="submit" class="btn btn-primary py-3 btn-block font-weight-bold shadow-sm" style="border-radius: 30px; font-size: 15px;">
                    <i class="icon-envelope-o mr-1"></i> Kirim Ulang Link Verifikasi
                  </button>
                </form>
              </div>

              <div class="text-center mt-3 pt-3 border-top d-flex justify-content-between">
                <a href="{{ route('login') }}" class="text-primary small font-weight-bold">Sudah verifikasi? Login</a>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                  @csrf
                  <button type="submit" class="btn btn-link text-muted small font-weight-bold p-0">Logout</button>
                </form>
              </div>
            </div>
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
