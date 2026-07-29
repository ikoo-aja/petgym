<!DOCTYPE html>
<html lang="en">

<head>
  <title>Login Page</title>
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

<body data-spy="scroll" data-target=".site-navbar-target" data-offset="300">

  <div class="site-wrap">

    <div class="site-mobile-menu site-navbar-target">
      <div class="site-mobile-menu-header">
        <div class="site-mobile-menu-close mt-3">
          <span class="icon-close2 js-menu-toggle"></span>
        </div>
      </div>
      <div class="site-mobile-menu-body"></div>
    </div>

    <!-- Header Navigation -->
    <header class="site-navbar py-4 js-sticky-header site-navbar-target" role="banner">
      <div class="container-fluid">
        <div class="d-flex align-items-center">
          <div class="site-logo"><a href="index.html">Workout<span>.</span> </a></div>
          <div class="ml-auto">
            <nav class="site-navigation position-relative text-right" role="navigation">
              <ul class="site-menu main-menu js-clone-nav mr-auto d-none d-lg-block">
                <li><a href="index.html#home-section" class="nav-link">Home</a></li>
                <li><a href="index.html#classes-section" class="nav-link">Classes</a></li>
                <li><a href="index.html#schedule-section" class="nav-link">Schedule</a></li>
                <li><a href="index.html#trainer-section" class="nav-link">Trainer</a></li>
                <li><a href="index.html#services-section" class="nav-link">Services</a></li>
                <li><a href="index.html#contact-section" class="nav-link">Contact</a></li>
                <li class="active"><a href="login.html" class="nav-link">Login</a></li>
              </ul>
            </nav>
            <a href="#" class="d-inline-block d-lg-none site-menu-toggle js-menu-toggle float-right"><span class="icon-menu h3"></span></a>
          </div>
        </div>
      </div>
    </header>

    <!-- Main Login Section -->
    <div class="site-section bg-light contact-wrap" style="padding-top: 150px; min-height: calc(100vh - 200px);">
      <div class="container">

        <div class="row justify-content-center text-center mb-5" data-aos="fade-up">
          <div class="col-md-8 section-heading">
            <span class="subheading">Welcome Back</span>
            <h2 class="heading mb-3">Login To Your Account</h2>
            <p>Access your fitness schedule, trainer notes, and personal workout progress.</p>
          </div>
        </div>

        <div class="row justify-content-center">
          <div class="col-md-6" data-aos="fade-up" data-aos-delay="100">
            <form action="{{ route('login') }}" method="POST" class="bg-white p-5 shadow-sm rounded">
                @csrf

                <!-- Alert Error Umum/Gagal Login -->
                @if($errors->has('email'))
                    <div class="alert alert-danger py-2" role="alert">
                    <small>{{ $errors->first('email') }}</small>
                    </div>
                @endif

                <div class="form-group row">
                    <div class="col-md-12">
                    <label for="email" class="text-black">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="Enter your email" required autofocus>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-12">
                    <label for="password" class="text-black">Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
                    </div>
                </div>

                <div class="form-group row align-items-center">
                    <div class="col-md-6 mb-3 mb-md-0">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="rememberMe" name="remember">
                        <label class="custom-control-label" for="rememberMe">Remember me</label>
                    </div>
                    </div>
                    <div class="col-md-6 text-md-right">
                    <a href="#" class="text-primary">Forgot password?</a>
                    </div>
                </div>

                <div class="form-group row mt-4">
                    <div class="col-md-12">
                    <input type="submit" class="btn btn-primary py-3 px-5 btn-block" value="Login">
                    </div>
                </div>
                </form>
          </div>
        </div>

      </div>
    </div>

    <!-- Footer -->
    <footer class="footer-section bg-dark">
      <div class="container">
        <div class="row">
          <div class="col-md-4">
            <h3 class="text-white">About Workout</h3>
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Veniam facere optio eligendi.</p>
          </div>

          <div class="col-md-3 ml-auto">
            <h3 class="text-white">Links</h3>
            <ul class="list-unstyled footer-links">
              <li><a href="index.html#home-section">Home</a></li>
              <li><a href="index.html#classes-section">Classes</a></li>
              <li><a href="index.html#schedule-section">Schedule</a></li>
              <li><a href="index.html#trainer-section">Trainer</a></li>
            </ul>
          </div>

          <div class="col-md-4">
            <h3 class="text-white">Subscribe</h3>
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Possimus, odio beatae accusantium.</p>
            <form action="#">
              <div class="d-flex mb-5">
                <input type="text" class="form-control rounded-0" placeholder="Email">
                <input type="submit" class="btn btn-primary rounded-0" value="Subscribe">
              </div>
            </form>
          </div>
        </div>

        <div class="row pt-5 mt-5 text-center">
          <div class="col-md-12">
            <div class="pt-5">
              <p>
                Copyright &copy;
                <script document.write(new Date().getFullYear());></script> All rights reserved | This template is made with
                <i class="icon-heart text-danger" aria-hidden="true"></i> by <a href="https://colorlib.com" target="_blank">Colorlib</a>
              </p>
            </div>
          </div>
        </div>
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
