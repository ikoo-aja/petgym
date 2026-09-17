<!DOCTYPE html>
<html lang="id">

<head>
  <title>Daftar Minat Sewa Web Gym — Pet Gym SaaS</title>
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

        @if(session('success_registration'))
          <div class="row justify-content-center mb-5">
            <div class="col-md-8">
              <div class="card border-0 shadow-sm p-4 text-center rounded-lg" style="border-radius: 16px; background-color: #ecfdf5; border: 1px solid #a7f3d0;">
                <div class="mb-3">
                  <span class="icon-check_circle display-4 text-success"></span>
                </div>
                <h3 class="font-weight-bold text-dark mb-2">🎉 Pendaftaran Berhasil Terkirim!</h3>
                <p class="text-muted mb-4" style="font-size: 15px;">
                  {{ session('success_registration') }}
                </p>
                <div class="d-flex justify-content-center" style="gap: 10px;">
                  <a href="/" class="btn btn-outline-secondary font-weight-bold px-4" style="border-radius: 20px;">Kembali ke Beranda</a>
                  <a href="{{ route('login') }}" class="btn btn-primary font-weight-bold px-4 shadow-sm" style="border-radius: 20px;">Halaman Login</a>
                </div>
              </div>
            </div>
          </div>
        @else
          <div class="row justify-content-center text-center mb-4">
            <div class="col-md-8 section-heading mb-2">
              <span class="subheading text-primary font-weight-bold" style="letter-spacing: 1px;">Pet Gym SaaS Platform</span>
              <h2 class="heading mb-2 text-dark font-weight-bold">Formulir Pendaftaran Sewa Web Gym</h2>
              <p class="text-muted">Isi informasi kontak Anda dan pilih paket yang diinginkan. Tim Superadmin kami akan segera menghubungi Anda melalui WhatsApp untuk konsultasi dan aktivasi akun.</p>
            </div>
          </div>

          <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">

              <form action="{{ route('register.saas') }}" method="POST" class="bg-white p-4 p-md-5 shadow-sm rounded-lg border" style="border-radius: 16px;">
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
                  <label for="name" class="text-dark font-weight-bold small">Nama Lengkap Anda <span class="text-danger">*</span></label>
                  <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-control form-control-lg" placeholder="Contoh: Budi Pratama" required autofocus style="border-radius: 8px; font-size: 14px;">
                </div>

                <div class="form-group mb-3">
                  <label for="email" class="text-dark font-weight-bold small">Alamat Email Aktif <span class="text-danger">*</span></label>
                  <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control form-control-lg" placeholder="budi@gmail.com" required style="border-radius: 8px; font-size: 14px;">
                  <small class="text-muted">Email ini akan digunakan untuk pengiriman kredensial akun dan konfirmasi.</small>
                </div>

                <div class="form-group mb-3">
                  <label for="phone" class="text-dark font-weight-bold small">Nomor WhatsApp Aktif <span class="text-danger">*</span></label>
                  <input type="text" id="phone" name="phone" value="{{ old('phone') }}" class="form-control form-control-lg" placeholder="081234567890" required style="border-radius: 8px; font-size: 14px;">
                  <small class="text-muted">Superadmin akan menghubungi Anda via WhatsApp ini.</small>
                </div>

                <div class="form-group mb-3">
                  <label for="plan_name" class="text-dark font-weight-bold small">Pilihan Paket Sewa Web Gym <span class="text-danger">*</span></label>
                  <select name="plan_name" id="plan_name" class="form-control form-control-lg" required style="border-radius: 8px; font-size: 14px;">
                    <option value="Paket Basic" {{ (old('plan_name', $selectedPlan) == 'Paket Basic') ? 'selected' : '' }}>Paket Basic — Rp 500.000 / bulan</option>
                    <option value="Paket Pro" {{ (old('plan_name', $selectedPlan) == 'Paket Pro' || empty(old('plan_name', $selectedPlan))) ? 'selected' : '' }}>Paket Pro ⭐ — Rp 1.200.000 / bulan</option>
                    <option value="Paket Enterprise" {{ (old('plan_name', $selectedPlan) == 'Paket Enterprise') ? 'selected' : '' }}>Paket Enterprise 👑 — Rp 2.500.000 / bulan</option>
                  </select>
                </div>

                <div class="form-group mb-4">
                  <label for="notes" class="text-dark font-weight-bold small">Catatan / Kebutuhan Khusus (Opsional)</label>
                  <textarea id="notes" name="notes" rows="2" class="form-control" placeholder="Tuliskan jika ada pertanyaan atau jadwal preferensi untuk dihubungi..." style="border-radius: 8px; font-size: 14px;">{{ old('notes') }}</textarea>
                </div>

                <div class="form-group mb-3">
                  <button type="submit" class="btn btn-primary btn-block font-weight-bold shadow-sm py-3" style="border-radius: 30px; font-size: 15px;">
                    <i class="icon-send mr-1"></i> Kirim Formulir Pendaftaran
                  </button>
                </div>

                <div class="text-center mt-3 pt-3 border-top">
                  <span class="text-muted small">Sudah memiliki akun pengelola?</span>
                  <a href="{{ route('login') }}" class="text-primary small font-weight-bold ml-1">Masuk / Login Di Sini</a>
                </div>
              </form>

            </div>
          </div>
        @endif

      </div>
    </div>
  </div>

</body>
</html>
