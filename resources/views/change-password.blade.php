<!DOCTYPE html>
<html lang="id">
<head>
  <title>Ubah Password — Pet Gym Management System</title>
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

  <!-- Main Change Password Section -->
  <div class="site-section bg-light contact-wrap" style="padding-top: 60px; padding-bottom: 80px;">
    <div class="container">
      <div class="row justify-content-center text-center mb-4">
        <div class="col-md-8 section-heading mb-2">
          <span class="subheading text-primary font-weight-bold" style="letter-spacing: 1px;">Pet Gym Platform</span>
          <h2 class="heading mb-2 text-dark font-weight-bold">Ubah Password Default</h2>
          <p class="text-muted">Anda harus mengubah password default sebelum dapat menggunakan dashboard.</p>
        </div>
      </div>

      <div class="row justify-content-center">
        <div class="col-md-5">
          <form action="{{ route('password.change.update') }}" method="POST" class="bg-white p-5 shadow-sm rounded-lg border">
            @csrf

            <!-- Alert Error -->
            @if($errors->has('current_password'))
              <div class="alert alert-danger py-2 mb-4" role="alert">
                <strong>Password saat ini salah.</strong>
              </div>
            @endif

            <!-- Alert Success -->
            @if(session('success'))
              <div class="alert alert-success py-2 mb-4" role="alert">
                {{ session('success') }}
              </div>
            @endif

            <div class="form-group mb-3">
              <label class="font-weight-bold text-dark" style="font-size: 13px;">Password Saat Ini</label>
              <div class="input-group">
                <input type="password" name="current_password" class="form-control" placeholder="Masukkan password saat ini" required>
                <div class="input-group-append">
                  <span class="input-group-text bg-white border-left-0" style="cursor: pointer;" onclick="togglePw(this)">
                    <i class="icon-eye text-muted"></i>
                  </span>
                </div>
              </div>
            </div>

            <div class="form-group mb-3">
              <label class="font-weight-bold text-dark" style="font-size: 13px;">Password Baru</label>
              <div class="input-group">
                <input type="password" name="new_password" class="form-control" placeholder="Masukkan password baru" minlength="4" required>
                <div class="input-group-append">
                  <span class="input-group-text bg-white border-left-0" style="cursor: pointer;" onclick="togglePw(this)">
                    <i class="icon-eye text-muted"></i>
                  </span>
                </div>
              </div>
            </div>

            <div class="form-group mb-0">
              <label class="font-weight-bold text-dark" style="font-size: 13px;">Konfirmasi Password Baru</label>
              <div class="input-group">
                <input type="password" name="new_password_confirmation" class="form-control" placeholder="Masukkan ulang password baru" minlength="4" required>
                <div class="input-group-append">
                  <span class="input-group-text bg-white border-left-0" style="cursor: pointer;" onclick="togglePw(this)">
                    <i class="icon-eye text-muted"></i>
                  </span>
                </div>
              </div>
            </div>

            <button type="submit" class="btn btn-success font-weight-bold w-100 mt-3" style="border-radius: 8px;">
              Simpan Password Baru
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  function togglePw(btn) {
    const input = btn.closest('.input-group').querySelector('input');
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
      input.type = 'text';
      icon.classList.remove('icon-eye');
      icon.classList.add('icon-eye-slash');
      icon.classList.remove('text-muted');
      icon.classList.add('text-primary');
    } else {
      input.type = 'password';
      icon.classList.remove('icon-eye-slash');
      icon.classList.add('icon-eye');
      icon.classList.remove('text-primary');
      icon.classList.add('text-muted');
    }
  }
</script>

</body>
</html>