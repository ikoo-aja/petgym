<!DOCTYPE html>
<html lang="id">

<head>
  <title>Login Portal — Pet Gym Management System</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <link href="https://fonts.googleapis.com/css?family=Muli:300,400,700,900" rel="stylesheet">
  <link rel="stylesheet" href="fonts/icomoon/style.css">

  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/jquery-ui.css">
  <link rel="stylesheet" href="css/owl.carousel.min.css">
  <link rel="stylesheet" href="css/owl.theme.default.min.css">

  <link rel="stylesheet" href="css/jquery.fancybox.min.css">
  <link rel="stylesheet" href="css/bootstrap-datepicker.css">
  <link rel="stylesheet" href="fonts/flaticon/font/flaticon.css">

  <link rel="stylesheet" href="css/aos.css">
  <link href="css/jquery.mb.YTPlayer.min.css" media="all" rel="stylesheet" type="text/css">

  <link rel="stylesheet" href="css/style.css">
</head>

<body data-spy="scroll" data-target=".site-navbar-target" data-offset="300" class="bg-light">

  <div class="site-wrap">

    <!-- Header Logo Sederhana (Tanpa Menu Navbar Navbar Nav Links) -->
    <div class="py-4 bg-white border-bottom shadow-sm">
      <div class="container text-center">
        <div class="site-logo">
          <a href="{{ url('/') }}" class="text-dark font-weight-bold" style="font-size: 28px; text-decoration: none;">
            Pet<span class="text-primary">Gym</span>
          </a>
        </div>
      </div>
    </div>

    <!-- Main Login Section -->
    <div class="site-section bg-light contact-wrap" style="padding-top: 60px; padding-bottom: 80px; min-height: calc(100vh - 220px);">
      <div class="container">

        <div class="row justify-content-center text-center mb-4" data-aos="fade-up">
          <div class="col-md-8 section-heading mb-2">
            <span class="subheading text-primary font-weight-bold" style="letter-spacing: 1px;">Pet Gym Platform</span>
            <h2 class="heading mb-2 text-dark font-weight-bold">Login To Your Account</h2>
            <p class="text-muted">Masuk ke portal akun staf, resepsionis, manager, atau admin Gym Anda.</p>
          </div>
        </div>

        <div class="row justify-content-center">
          <div class="col-md-5" data-aos="fade-up" data-aos-delay="100">
            <form action="{{ route('login') }}" method="POST" class="bg-white p-5 shadow-sm rounded-lg border">
              @csrf

              <!-- Alert Error Umum/Gagal Login -->
              @if($errors->has('email'))
                <div class="alert alert-danger py-2 mb-4" role="alert">
                  <small class="font-weight-bold">⚠️ {{ $errors->first('email') }}</small>
                </div>
              @endif

              <div class="form-group mb-3">
                <label for="email" class="text-dark font-weight-bold small">Email Address *</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control form-control-lg" placeholder="Enter your email" required autofocus style="font-size: 15px;">
              </div>

              <div class="form-group mb-3">
                <label for="password" class="text-dark font-weight-bold small">Password *</label>
                <input type="password" id="password" name="password" class="form-control form-control-lg" placeholder="Enter your password" required style="font-size: 15px;">
              </div>

              <div class="form-group row align-items-center mb-4">
                <div class="col-6">
                  <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="rememberMe" name="remember">
                    <label class="custom-control-label text-muted small" for="rememberMe">Ingat saya</label>
                  </div>
                </div>
                <div class="col-6 text-right">
                  <a href="#" class="text-primary small font-weight-bold">Lupa password?</a>
                </div>
              </div>

              <div class="form-group mb-3">
                <button type="submit" class="btn btn-primary py-3 px-5 btn-block font-weight-bold shadow-sm" style="border-radius: 30px; font-size: 16px;">
                  Login Ke Sistem
                </button>
              </div>

              <div class="text-center mt-4 pt-2 border-top">
                <a href="{{ url('/') }}" class="text-muted small">← Kembali ke Halaman Utama</a>
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

  <script src="js/jquery-3.3.1.min.js"></script>
  <script src="js/jquery-migrate-3.0.1.min.js"></script>
  <script src="js/jquery-ui.js"></script>
  <script src="js/popper.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/owl.carousel.min.js"></script>
  <script src="js/jquery.stellar.min.js"></script>
  <script src="js/jquery.countdown.min.js"></script>
  <script src="js/bootstrap-datepicker.min.js"></script>
  <script src="js/jquery.easing.1.3.js"></script>
  <script src="js/aos.js"></script>
  <script src="js/jquery.fancybox.min.js"></script>
  <script src="js/jquery.sticky.js"></script>
  <script src="js/jquery.mb.YTPlayer.min.js"></script>
  <script src="js/main.js"></script>

</body>

</html>
