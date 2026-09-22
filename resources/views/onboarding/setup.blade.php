<!DOCTYPE html>
<html lang="id">
<head>
  <title>Setup Website Gym Anda — Pet Gym SaaS</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <x-dynamic-favicon />

  <link href="https://fonts.googleapis.com/css?family=Muli:300,400,700,900" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('fonts/icomoon/style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">

  <style>
    body {
      background-color: #f1f5f9;
      font-family: 'Muli', sans-serif;
      color: #1e293b;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
    .onboarding-card {
      background: #ffffff;
      border-radius: 16px;
      box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
      border: 1px solid #e2e8f0;
    }
    .step-pill {
      font-size: 12px;
      font-weight: 700;
      padding: 6px 14px;
      border-radius: 20px;
    }
    .preview-box {
      background: #f8fafc;
      border: 2px dashed #cbd5e1;
      border-radius: 12px;
    }
  </style>
</head>
<body>

  <!-- Top Navbar Minimalis -->
  <header class="bg-white border-bottom py-3">
    <div class="container d-flex justify-content-between align-items-center">
      <x-brand-logo type="full" theme="light" size="38" url="/" />
      <div class="d-flex align-items-center">
        <span class="text-muted small mr-3 d-none d-sm-inline">Masuk sebagai: <strong>{{ Auth::user()->name }}</strong> ({{ Auth::user()->email }})</span>
        <a href="{{ route('logout') }}" class="btn btn-sm btn-outline-danger font-weight-bold" style="border-radius: 20px;">Keluar</a>
      </div>
    </div>
  </header>

  <main class="container my-auto py-5">
    <div class="row justify-content-center">
      <div class="col-lg-9 col-xl-8">

        <!-- Progress Steps -->
        <div class="d-flex justify-content-center align-items-center mb-4 text-center" style="gap: 10px;">
          <span class="step-pill bg-success text-white">1. Akun Dibuat</span>
          <span class="text-muted font-weight-bold">→</span>
          <span class="step-pill bg-primary text-white shadow-sm">2. Pengaturan Website & Brand</span>
          <span class="text-muted font-weight-bold">→</span>
          <span class="step-pill bg-light text-muted border">3. Dasbor Gym</span>
        </div>

        <div class="onboarding-card p-4 p-md-5">
          <div class="text-center mb-4">
            <h2 class="font-weight-extrabold text-dark mb-2">Selamat Datang di PetGym SaaS</h2>
            <p class="text-muted lead" style="font-size: 15px;">
              Pendaftaran akun gym Anda telah berhasil disetujui. Sekarang, mari lengkapi identitas website gym Anda untuk mengaktifkan subdomain dan ruang operasional sistem.
            </p>
          </div>

          @if(session('info'))
            <div class="alert alert-info border-0 shadow-sm mb-4" style="border-radius: 10px; background-color: #e0f2fe; color: #0369a1;">
              <i class="icon-info mr-1"></i> {{ session('info') }}
            </div>
          @endif

          @if($errors->any())
            <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 10px;">
              <ul class="mb-0 pl-3">
                @foreach($errors->all() as $error)
                  <li class="small font-weight-bold">{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <form action="{{ route('tenant.onboarding.provision') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Section 1: Brand & Domain -->
            <div class="mb-4">
              <h5 class="font-weight-bold text-dark mb-3 border-bottom pb-2">
                <i class="icon-globe text-primary mr-2"></i> Identitas & Domain Website Gym
              </h5>

              <div class="form-group mb-3">
                <label for="gym_name" class="font-weight-bold text-dark small">Nama Toko / Gym / Studio Anda <span class="text-danger">*</span></label>
                <input type="text" id="gym_name" name="gym_name" value="{{ old('gym_name') }}" class="form-control form-control-lg" placeholder="Contoh: FitLife Studio & Gym" required autofocus style="border-radius: 10px; font-size: 15px;">
                <small class="text-muted">Nama ini akan tampil sebagai judul brand di website dan struk kasir.</small>
              </div>

              <div class="form-group mb-3">
                <label for="subdomain" class="font-weight-bold text-dark small">Pilihan Subdomain Website <span class="text-danger">*</span></label>
                <div class="input-group input-group-lg">
                  <input type="text" id="subdomain" name="subdomain" value="{{ old('subdomain') }}" class="form-control" placeholder="fitlife" required style="border-top-left-radius: 10px; border-bottom-left-radius: 10px; font-size: 15px;">
                  <div class="input-group-append">
                    <span class="input-group-text bg-light font-weight-bold text-dark" style="border-top-right-radius: 10px; border-bottom-right-radius: 10px; font-size: 14px;">.workout.id</span>
                  </div>
                </div>
                <small class="text-muted">Gunakan huruf kecil, angka, dan tanpa spasi (contoh: fitlife, powerhouse, megagym).</small>

                <!-- Live URL Preview -->
                <div class="preview-box p-3 mt-2 d-flex align-items-center justify-content-between">
                  <div class="small">
                    <span class="text-muted d-block">Alamat Website Publik Gym Anda Nanti:</span>
                    <strong class="text-primary font-weight-bold" id="urlPreview" style="font-size: 14px;">http://fitlife.workout.id</strong>
                  </div>
                  <span class="badge badge-success px-2 py-1 font-weight-bold">Tautan Aktif</span>
                </div>
              </div>
            </div>

            <!-- Section 2: Detail Informasi Tambahan -->
            <div class="mb-4">
              <h5 class="font-weight-bold text-dark mb-3 border-bottom pb-2">
                <i class="icon-info text-primary mr-2"></i> Informasi & Profil Gym (Opsional)
              </h5>

              <div class="form-group mb-3">
                <label for="tagline" class="font-weight-bold text-dark small">Slogan / Tagline Singkat</label>
                <input type="text" id="tagline" name="tagline" value="{{ old('tagline') }}" class="form-control" placeholder="Contoh: Pusat Kebugaran Modern & Terjangkau" style="border-radius: 8px;">
              </div>

              <div class="form-group mb-3">
                <label for="description" class="font-weight-bold text-dark small">Deskripsi Singkat Gym</label>
                <textarea id="description" name="description" rows="3" class="form-control" placeholder="Jelaskan fasilitas, keunggulan, atau jam operasional gym Anda..." style="border-radius: 8px;">{{ old('description') }}</textarea>
              </div>

              <div class="row">
                <div class="col-md-6 form-group mb-3">
                  <label for="phone" class="font-weight-bold text-dark small">Nomor WhatsApp / Telepon Gym</label>
                  <input type="text" id="phone" name="phone" value="{{ old('phone', $registration->phone ?? '') }}" class="form-control" placeholder="081234567890" style="border-radius: 8px;">
                </div>
                <div class="col-md-6 form-group mb-3">
                  <label for="address" class="font-weight-bold text-dark small">Alamat Lokasi Gym</label>
                  <input type="text" id="address" name="address" value="{{ old('address') }}" class="form-control" placeholder="Jl. Sudirman No. 10, Jakarta" style="border-radius: 8px;">
                </div>
              </div>

              <div class="form-group mb-0">
                <label for="logo" class="font-weight-bold text-dark small">Unggah Logo Brand Gym (Opsional)</label>
                <input type="file" id="logo" name="logo" class="form-control-file border p-2 bg-light rounded" accept="image/*" style="border-radius: 8px;">
                <small class="text-muted">Format: PNG, JPG, WEBP. Maksimal 2MB.</small>
              </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-3 border-top text-center">
              <button type="submit" class="btn btn-primary btn-lg font-weight-bold px-5 py-3 shadow" style="border-radius: 30px; font-size: 16px;">
                Selesaikan Pengaturan & Buka Website Gym
              </button>
              <p class="text-muted small mt-2 mb-0">
                Sistem akan mengalokasikan ruang data dan halaman landing publik untuk gym Anda secara otomatis.
              </p>
            </div>

          </form>

        </div>
      </div>
    </div>
  </main>

  <footer class="bg-white border-top py-3 text-center text-muted small mt-auto">
    &copy; {{ date('Y') }} PetGym SaaS Management System. Hak cipta dilindungi undang-undang.
  </footer>

  <script src="{{ asset('js/jquery-3.3.1.min.js') }}"></script>
  <script>
    // Live Subdomain Preview
    $('#subdomain').on('input', function() {
      var val = $(this).val().toLowerCase().replace(/[^a-z0-9\-]/g, '');
      $(this).val(val);
      var preview = val ? val + '.workout.id' : 'fitlife.workout.id';
      $('#urlPreview').text('http://' + preview);
    });

    // Auto slug from Gym Name if subdomain is untouched
    $('#gym_name').on('input', function() {
      if (!$('#subdomain').data('touched')) {
        var slug = $(this).val().toLowerCase().replace(/[^a-z0-9]/g, '-').replace(/-+/g, '-').replace(/^-|-$/g, '');
        if (slug) {
          $('#subdomain').val(slug);
          $('#urlPreview').text('http://' + slug + '.workout.id');
        }
      }
    });

    $('#subdomain').on('focus', function() {
      $(this).data('touched', true);
    });
  </script>

</body>
</html>
