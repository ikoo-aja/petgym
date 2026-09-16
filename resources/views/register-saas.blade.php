<!DOCTYPE html>
<html lang="id">

<head>
  <title>Daftar Akun Pengelola Web Gym — Pet Gym SaaS</title>
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

    <!-- Main Register Section -->
    <div class="site-section bg-light contact-wrap" style="padding-top: 50px; padding-bottom: 80px; min-height: calc(100vh - 220px);">
      <div class="container">

        <div class="row justify-content-center text-center mb-4">
          <div class="col-md-8 section-heading mb-2">
            <span class="subheading text-primary font-weight-bold" style="letter-spacing: 1px;">Pet Gym SaaS Platform</span>
            <h2 class="heading mb-2 text-dark font-weight-bold">Daftar Akun Admin Pengelola Web Gym</h2>
            <p class="text-muted">Isi data gym, pilih paket sewa, daftarkan akun Admin Gym Anda, dan upload bukti transfer DP 50% untuk mengaktifkan sistem & website pribadi gym Anda.</p>
          </div>
        </div>

        <div class="row justify-content-center">
          <div class="col-md-8 col-lg-7">

            <form action="{{ route('register.saas') }}" method="POST" enctype="multipart/form-data" class="bg-white p-4 p-md-5 shadow-sm rounded-lg border" style="border-radius: 12px;">
              @csrf

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
                <label for="gym_name" class="text-dark font-weight-bold small">Nama Gym / Studio *</label>
                <input type="text" id="gym_name" name="gym_name" value="{{ old('gym_name') }}" class="form-control" placeholder="Contoh: FitLife Studio" required autofocus style="height: 48px; border-radius: 8px; font-size: 14px;">
              </div>

              <div class="form-group mb-3">
                <label for="subdomain" class="text-dark font-weight-bold small">Subdomain Website Gym *</label>
                <div class="input-group">
                  <input type="text" id="subdomain" name="subdomain" value="{{ old('subdomain') }}" class="form-control" placeholder="fitlife" required style="height: 48px; border-top-left-radius: 8px; border-bottom-left-radius: 8px; font-size: 14px;">
                  <div class="input-group-append">
                    <span class="input-group-text bg-light font-weight-bold text-muted" style="border-top-right-radius: 8px; border-bottom-right-radius: 8px; font-size: 13px;">.workout.id</span>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 form-group mb-3">
                  <label for="owner_name" class="text-dark font-weight-bold small">Nama Admin / Pengelola Gym *</label>
                  <input type="text" id="owner_name" name="owner_name" value="{{ old('owner_name') }}" class="form-control" placeholder="Contoh: Alfredo Admin" required style="height: 48px; border-radius: 8px; font-size: 14px;">
                </div>
                <div class="col-md-6 form-group mb-3">
                  <label for="phone" class="text-dark font-weight-bold small">No. WhatsApp / HP Admin *</label>
                  <input type="text" id="phone" name="phone" value="{{ old('phone') }}" class="form-control" placeholder="081234567890" required style="height: 48px; border-radius: 8px; font-size: 14px;">
                </div>
              </div>

              <div class="form-group mb-3">
                <label for="email" class="text-dark font-weight-bold small">Email Resmi Admin Gym *</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="admin@fitlife.com" required style="height: 48px; border-radius: 8px; font-size: 14px;">
              </div>

              <div class="row">
                <div class="col-md-6 form-group mb-3">
                  <label for="password" class="text-dark font-weight-bold small">Password Admin Gym *</label>
                  <input type="password" id="password" name="password" class="form-control" placeholder="Min. 4 karakter" required style="height: 48px; border-radius: 8px; font-size: 14px;">
                </div>
                <div class="col-md-6 form-group mb-3">
                  <label for="password_confirmation" class="text-dark font-weight-bold small">Konfirmasi Password *</label>
                  <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Ulangi password" required style="height: 48px; border-radius: 8px; font-size: 14px;">
                </div>
              </div>

              <div class="form-group mb-3">
                <label for="plan_name" class="text-dark font-weight-bold small">Pilih Paket Sewa Website Gym *</label>
                <select name="plan_name" id="plan_name" class="form-control" style="height: 48px; border-radius: 8px; font-size: 14px;">
                  <option value="Paket Basic">Paket Basic — Rp 500.000 / bln (DP 50%: Rp 250.000)</option>
                  <option value="Paket Pro" selected>Paket Pro ⭐ — Rp 1.200.000 / bln (DP 50%: Rp 600.000)</option>
                  <option value="Paket Enterprise">Paket Enterprise 👑 — Rp 2.500.000 / bln (DP 50%: Rp 1.250.000)</option>
                </select>
              </div>

              <!-- Box Informasi Pembayaran DP 50% -->
              <div class="p-3 mb-4 rounded bg-light border" style="border-radius: 10px;">
                <label class="font-weight-bold text-dark small d-block mb-1">🏦 Transfer DP 50% Ke Rekening Superadmin:</label>
                <div class="font-weight-bold text-primary mb-2" style="font-size: 15px;">
                  Bank BCA: <span class="text-dark font-weight-extrabold">8830-1234-5678</span> a.n. PetGym SaaS Superadmin
                </div>
                <small class="text-muted d-block">Setelah transfer DP 50%, upload foto / bukti transfer di bawah ini untuk diverifikasi Superadmin.</small>
              </div>

              <div class="form-group mb-4">
                <label for="proof_file" class="text-dark font-weight-bold small">Upload Bukti Transfer DP 50% *</label>
                <input type="file" id="proof_file" name="proof_file" class="form-control-file border p-2 rounded bg-white" accept="image/*" required style="border-radius: 8px;">
                <small class="text-muted">Format: JPG, PNG. Maksimal 5MB.</small>
              </div>

              <div class="form-group mb-4 mt-2">
                <button type="submit" class="btn btn-primary btn-block font-weight-bold shadow-sm" style="height: 48px; border-radius: 8px; font-size: 15px;">
                  🚀 Kirim Pendaftaran & Bukti Transfer DP 50%
                </button>
              </div>

              <div class="text-center mt-4 pt-3 border-top">
                <span class="text-muted small">Sudah memiliki akun pengelola?</span>
                <a href="{{ route('login') }}" class="text-primary small font-weight-bold ml-1">Masuk / Login Di Sini</a>
              </div>
            </form>

          </div>
        </div>

      </div>
    </div>
  </div>

</body>
</html>
