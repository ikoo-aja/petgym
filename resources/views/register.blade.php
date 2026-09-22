<!DOCTYPE html>
<html lang="id">

<head>
  <title>Pendaftaran Member Gym — Pet Gym Platform</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <x-dynamic-favicon />

  <link href="https://fonts.googleapis.com/css?family=Muli:300,400,700,900" rel="stylesheet">
  <link rel="stylesheet" href="fonts/icomoon/style.css">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/style.css">
</head>

<body class="bg-light">

  <div class="site-wrap">
    <!-- Header Logo Sederhana -->
    <div class="py-3 bg-white border-bottom shadow-sm">
      <div class="container text-center">
        <div class="site-logo d-flex justify-content-center align-items-center">
          <x-brand-logo type="full" theme="light" size="44" url="/" />
        </div>
      </div>
    </div>

    <!-- Main Register Section -->
    <div class="site-section bg-light contact-wrap" style="padding-top: 50px; padding-bottom: 80px; min-height: calc(100vh - 220px);">
      <div class="container">

        <div class="row justify-content-center text-center mb-4">
          <div class="col-md-8 section-heading mb-2">
            <span class="subheading text-primary font-weight-bold" style="letter-spacing: 1px;">Portal Member Gym</span>
            <h2 class="heading mb-2 text-dark font-weight-bold">Formulir Pendaftaran Member Baru</h2>
            <p class="text-muted">Buat akun keanggotaan gym Anda sekarang dan dapatkan akses penuh ke loker digital, booking kelas, dan sesi Personal Trainer.</p>
          </div>
        </div>

        <div class="row justify-content-center">
          <div class="col-md-6">
            <div class="alert alert-info border-0 p-3 mb-4 rounded shadow-sm" style="background-color: #e0f2fe; color: #0369a1; border-radius: 10px; font-size: 13px;">
              <i class="icon-info mr-1"></i> <strong>Pendaftaran Member Gym:</strong> Formulir ini khusus untuk masyarakat / pelanggan yang ingin mendaftar keanggotaan <strong>Member Gym</strong>.
              <hr class="my-2 border-info">
              <span class="d-block mt-1">Apakah Anda Pemilik Gym yang ingin <strong>membeli / menyewa website PetGym</strong>? <a href="{{ url('/#pricing-section') }}" class="font-weight-bold text-primary">Lihat Paket Sewa Web di sini &rarr;</a></span>
            </div>

            <form action="{{ route('register') }}" method="POST" class="bg-white p-5 shadow-sm rounded-lg border">
              @csrf

              <!-- Alert Errors -->
              @if($errors->any())
                <div class="alert alert-danger py-2 mb-4" role="alert">
                  <ul class="mb-0 pl-3">
                    @foreach($errors->all() as $err)
                      <li class="small font-weight-bold">{{ $err }}</li>
                    @endforeach
                  </ul>
                </div>
              @endif

              <div class="form-group mb-3">
                <label for="name" class="text-dark font-weight-bold small">Nama Lengkap Member *</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-control form-control-lg" placeholder="Contoh: Budi Santoso" required style="font-size: 15px;">
              </div>

              <div class="form-group mb-3">
                <label for="email" class="text-dark font-weight-bold small">Email Member *</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control form-control-lg" placeholder="budi@example.com" required style="font-size: 15px;">
              </div>

              <div class="form-group mb-3">
                <label for="phone" class="text-dark font-weight-bold small">Nomor WhatsApp / HP *</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone') }}" class="form-control form-control-lg" placeholder="081234567890" required style="font-size: 15px;">
              </div>

              <div class="row">
                <div class="col-md-6 form-group mb-3">
                  <label for="password" class="text-dark font-weight-bold small">Kata Sandi *</label>
                  <input type="password" id="password" name="password" class="form-control form-control-lg" placeholder="Min. 4 karakter" required style="font-size: 15px;">
                </div>
                <div class="col-md-6 form-group mb-3">
                  <label for="password_confirmation" class="text-dark font-weight-bold small">Konfirmasi Kata Sandi *</label>
                  <input type="password" id="password_confirmation" name="password_confirmation" class="form-control form-control-lg" placeholder="Ulangi kata sandi" required style="font-size: 15px;">
                </div>
              </div>

              @if(isset($tenants) && $tenants->count())
                <div class="form-group mb-4">
                  <label for="tenant_id" class="text-dark font-weight-bold small">Pilih Cabang Gym *</label>
                  <select name="tenant_id" id="tenant_id" class="form-control form-control-lg" style="font-size: 15px;">
                    @foreach($tenants as $t)
                      <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->subdomain }})</option>
                    @endforeach
                  </select>
                </div>
              @endif

              <div class="form-group mb-3">
                <button type="submit" class="btn btn-primary py-3 px-5 btn-block font-weight-bold shadow-sm" style="border-radius: 30px; font-size: 16px;">
                  Daftar Akun Member Baru
                </button>
              </div>

              <div class="text-center mt-4 pt-3 border-top">
                <span class="text-muted small">Sudah memiliki akun?</span>
                <a href="{{ route('login') }}" class="text-primary small font-weight-bold ml-1">Masuk di Sini</a>
              </div>
            </form>
          </div>
        </div>

      </div>
    </div>
  </div>

</body>
</html>
