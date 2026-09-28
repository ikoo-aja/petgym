<!DOCTYPE html>
<html lang="id">

<head>
  <title>Pet Gym — Sistem Manajemen Pengelolaan Gym Modern (SaaS)</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <x-dynamic-favicon />

  <link href="https://fonts.googleapis.com/css?family=Muli:300,400,600,700,900&display=swap" rel="stylesheet">
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

  <style>
    /* Styling Tombol & Kartu Modern */
    .btn-gradient-primary {
      background: linear-gradient(135deg, #e11d48 0%, #be123c 100%);
      color: #ffffff !important;
      border: none;
      box-shadow: 0 4px 14px rgba(225, 29, 72, 0.35);
      transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .btn-gradient-primary:hover {
      background: linear-gradient(135deg, #be123c 0%, #9f1239 100%);
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(225, 29, 72, 0.45);
    }

    .btn-gradient-outline {
      background: rgba(255, 255, 255, 0.1);
      color: #ffffff !important;
      border: 2px solid rgba(255, 255, 255, 0.85);
      backdrop-filter: blur(4px);
      transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .btn-gradient-outline:hover {
      background: #ffffff;
      color: #111827 !important;
      border-color: #ffffff;
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(255, 255, 255, 0.25);
    }

    .plan-card {
      transition: all 0.3s ease;
      border-radius: 18px;
    }
    .plan-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 16px 32px rgba(0, 0, 0, 0.1) !important;
    }
    .plan-card-pro {
      border: 2px solid #e11d48 !important;
      transform: scale(1.03);
    }
    .plan-card-pro:hover {
      transform: scale(1.03) translateY(-6px);
      box-shadow: 0 20px 40px rgba(225, 29, 72, 0.2) !important;
    }

    .feature-box {
      border-radius: 16px;
      transition: all 0.3s ease;
      background: #ffffff;
      border: 1px solid #f1f5f9;
    }
    .feature-box:hover {
      transform: translateY(-4px);
      box-shadow: 0 12px 28px rgba(0, 0, 0, 0.08);
      border-color: #fecdd3;
    }

    /* ====================================================
       NAVBAR ADAPTIF (NATURAL SCROLL & BUTTON TRANSITION)
       ==================================================== */
    .site-navbar {
      transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
    }

    /* 1. Kondisi di Paling Atas (Over Hero Gelap): Transparan alami */
    .sticky-wrapper:not(.is-sticky) .site-navbar {
      background-color: transparent !important;
      box-shadow: none !important;
      border-bottom: 1px solid rgba(255, 255, 255, 0.12) !important;
    }
    .sticky-wrapper:not(.is-sticky) .site-navbar .site-navigation .site-menu > li > a:not(.btn-nav-register) {
      color: rgba(255, 255, 255, 0.9) !important;
      font-size: 14px;
      transition: color 0.2s ease;
    }
    .sticky-wrapper:not(.is-sticky) .site-navbar .site-navigation .site-menu > li > a:not(.btn-nav-register):hover,
    .sticky-wrapper:not(.is-sticky) .site-navbar .site-navigation .site-menu > li > a:not(.btn-nav-register).active {
      color: #ffffff !important;
    }

    /* 2. Kondisi Saat Di-scroll (Sticky): Background Putih Elegan */
    .sticky-wrapper.is-sticky .site-navbar,
    .site-navbar.shrink {
      background-color: #ffffff !important;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08) !important;
      border-bottom: 1px solid #f1f5f9 !important;
    }
    .sticky-wrapper.is-sticky .site-navbar .site-navigation .site-menu > li > a:not(.btn-nav-register),
    .site-navbar.shrink .site-navigation .site-menu > li > a:not(.btn-nav-register) {
      color: #1f2937 !important;
      font-size: 14px;
      transition: color 0.2s ease;
    }
    .sticky-wrapper.is-sticky .site-navbar .site-navigation .site-menu > li > a:not(.btn-nav-register):hover,
    .sticky-wrapper.is-sticky .site-navbar .site-navigation .site-menu > li > a:not(.btn-nav-register).active,
    .site-navbar.shrink .site-navigation .site-menu > li > a:not(.btn-nav-register):hover {
      color: #e11d48 !important;
    }

    /* 3. Tombol Adaptif "Daftar Akun" di Navbar */
    .site-navbar .site-navigation .site-menu > li > a.btn-nav-register {
      display: inline-flex !important;
      align-items: center;
      justify-content: center;
      padding: 9px 24px !important;
      border-radius: 30px !important;
      font-size: 13px !important;
      font-weight: 700 !important;
      text-transform: none !important;
      text-decoration: none !important;
      letter-spacing: 0.3px;
      transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
    }

    /* Di atas hero gelap: Background putih solid, teks hitam pekat (#111827) yang kontras dan jelas */
    .sticky-wrapper:not(.is-sticky) .site-navbar .site-navigation .site-menu > li > a.btn-nav-register {
      background-color: #ffffff !important;
      color: #111827 !important;
      border: 1px solid rgba(255, 255, 255, 0.95) !important;
      box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25) !important;
    }
    .sticky-wrapper:not(.is-sticky) .site-navbar .site-navigation .site-menu > li > a.btn-nav-register:hover {
      background-color: #f8fafc !important;
      color: #e11d48 !important;
      transform: translateY(-2px);
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.3) !important;
    }

    /* Saat di-scroll (Sticky navbar putih): Background merah gradient brand, teks putih bersih (#ffffff) */
    .sticky-wrapper.is-sticky .site-navbar .site-navigation .site-menu > li > a.btn-nav-register,
    .site-navbar.shrink .site-navigation .site-menu > li > a.btn-nav-register {
      background: linear-gradient(135deg, #e11d48 0%, #be123c 100%) !important;
      color: #ffffff !important;
      border: 1px solid transparent !important;
      box-shadow: 0 4px 14px rgba(225, 29, 72, 0.35) !important;
    }
    .sticky-wrapper.is-sticky .site-navbar .site-navigation .site-menu > li > a.btn-nav-register:hover,
    .site-navbar.shrink .site-navigation .site-menu > li > a.btn-nav-register:hover {
      background: linear-gradient(135deg, #be123c 0%, #9f1239 100%) !important;
      color: #ffffff !important;
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(225, 29, 72, 0.45) !important;
    }
  </style>
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
    <header class="site-navbar py-3 js-sticky-header site-navbar-target" role="banner">
      <div class="container-fluid px-4 px-lg-5">
        <div class="d-flex align-items-center justify-content-between">
          <div class="site-logo">
            <x-brand-logo type="full" theme="dark" size="40" url="/" onclick="if(window.location.pathname === '/' || window.location.pathname === ''){ window.scrollTo({top: 0, behavior: 'smooth'}); return false; }" />
          </div>
          <div class="ml-auto d-flex align-items-center">
            <nav class="site-navigation position-relative text-right" role="navigation">
              <ul class="site-menu main-menu js-clone-nav mr-auto d-none d-lg-block">
                <li><a href="#home-section" class="nav-link font-weight-bold">Beranda</a></li>
                <li><a href="#cara-kerja-section" class="nav-link font-weight-bold">Cara Kerja</a></li>
                <li><a href="#fitur-section" class="nav-link font-weight-bold">Fitur Utama</a></li>
                <li><a href="#pricing-section" class="nav-link font-weight-bold">Paket Harga</a></li>
                <li><a href="#contact-section" class="nav-link font-weight-bold">Kontak</a></li>
                <li class="d-inline-block ml-3">
                  <a href="{{ route('register') }}" class="btn-nav-register">
                    Daftar Akun
                  </a>
                </li>
              </ul>
            </nav>
            <a href="#" class="d-inline-block d-lg-none site-menu-toggle js-menu-toggle float-right"><span class="icon-menu h3 mb-0"></span></a>
          </div>
        </div>
      </div>
    </header>

    <!-- 1. HERO SECTION -->
    <div class="intro-section" id="home-section" style="background-image: url('{{ asset('images/hero_bg.png') }}'); background-size: cover; background-position: center;">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-lg-10 mx-auto text-center" data-aos="fade-up">
            <span class="badge badge-danger text-uppercase font-weight-bold px-3 py-2 mb-3" style="letter-spacing: 1.5px; border-radius: 30px; font-size: 11px;">
              CLOUD GYM MANAGEMENT SAAS
            </span>
            <h1 class="mb-3 text-white font-weight-bold display-4" style="line-height: 1.2;">
              Satu Platform untuk Seluruh Kendali Gym Anda.
            </h1>
            <p class="lead mx-auto desc mb-5 text-white-50" style="max-width: 900px; font-size: 1.15rem;">
              Tinggalkan pencatatan manual dan sistem kasir yang berantakan. Kami menghadirkan mesin manajemen gym berbasis cloud (SaaS) yang dirancang khusus untuk mempermudah operasional harian. Dari pendaftaran member, riwayat transaksi, hingga presensi kelas, semuanya terintegrasi dalam satu sistem cerdas yang bisa Anda pantau dari mana saja.
            </p>
            <div class="d-flex flex-wrap justify-content-center align-items-center">
              <a href="#pricing-section" class="btn btn-gradient-primary px-5 py-3 font-weight-bold m-2" style="border-radius: 50px; font-size: 14px; letter-spacing: 0.5px;">
                <span class="icon-rocket mr-2"></span> Berlangganan Sekarang
              </a>
              <a href="#fitur-section" class="btn btn-gradient-outline px-4 py-3 font-weight-bold m-2" style="border-radius: 50px; font-size: 14px; letter-spacing: 0.5px;">
                <span class="icon-list mr-2"></span> Lihat Semua Fitur
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- 2. CARA KERJA -->
    <div class="site-section" id="cara-kerja-section">
      <div class="container">
        <div class="row justify-content-center text-center mb-5">
          <div class="col-md-8 section-heading" data-aos="fade-up">
            <span class="subheading">Alur Operasional SaaS</span>
            <h2 class="heading mb-3">Cara Kerja Tanpa Hambatan</h2>
            <p class="text-muted">Proses transisi digital gym yang cepat, terstruktur, dan siap pakai tanpa perlu keahlian teknis khusus.</p>
          </div>
        </div>

        <div class="row">
          <!-- Langkah 1 -->
          <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="">
            <div class="ftco-feature-1 feature-box p-4" style="height: 100%;">
              <span class="icon flaticon-fit text-primary mb-3 d-inline-block" style="font-size: 40px;"></span>
              <div class="ftco-feature-1-text">
                <h3 class="h5 font-weight-bold text-dark">Langkah 1: Aktivasi Cloud Instan</h3>
                <p class="text-muted">
                  Cukup pilih paket sewa sesuai ukuran gym Anda. Sistem secara otomatis menyiapkan database terisolasi dan subdomain khusus tanpa perlu pengadaan server fisik maupun instalasi rumit.
                </p>
              </div>
            </div>
          </div>

          <!-- Langkah 2 -->
          <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="100">
            <div class="ftco-feature-1 feature-box p-4" style="height: 100%;">
              <span class="icon flaticon-gym-1 text-primary mb-3 d-inline-block" style="font-size: 40px;"></span>
              <div class="ftco-feature-1-text">
                <h3 class="h5 font-weight-bold text-dark">Langkah 2: Sentralisasi Data & Role Karyawan</h3>
                <p class="text-muted">
                  Atur paket keanggotaan (membership), kuota loker, jadwal kelas mingguan, serta buat akun staf dengan hak akses terpisah: Owner, Manager, Resepsionis, hingga Personal Trainer.
                </p>
              </div>
            </div>
          </div>

          <!-- Langkah 3 -->
          <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="200">
            <div class="ftco-feature-1 feature-box p-4" style="height: 100%;">
              <span class="icon flaticon-gym text-primary mb-3 d-inline-block" style="font-size: 40px;"></span>
              <div class="ftco-feature-1-text">
                <h3 class="h5 font-weight-bold text-dark">Langkah 3: Operasional Harian Otomatis</h3>
                <p class="text-muted">
                  Member check-in instan dengan PIN/Access Code, kasir POS memproses pembayaran & cetak struk tanpa delay, dan pemilik dapat memantau grafik keuangan real-time dari mana saja.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Banner Break Background -->
    <div class="bgimg" style="background-image: url('images/bg_2.jpg');" data-stellar-background-ratio="0.5">
      <div class="container">
        <div class="row align-items-center justify-content-center text-center">
          <div class="col-md-8" data-aos="fade-up">
            <h2 class="text-white font-weight-bold mb-3">Solusi Manajemen Gym Berbasis Cloud (SaaS)</h2>
            <p class="lead mx-auto desc mb-5 text-white-50">Satu sistem terpadu untuk efisiensi penuh seluruh aktivitas operasional gym Anda.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- 3. FITUR UTAMA -->
    <div class="site-section" id="fitur-section">
      <div class="container">
        <div class="row justify-content-center text-center mb-5">
          <div class="col-md-8 section-heading" data-aos="fade-up">
            <span class="subheading">Solusi Lengkap Bisnis Gym</span>
            <h2 class="heading mb-3">Fitur Utama Sistem</h2>
            <p class="text-muted">Fitur lengkap end-to-end yang dirancang menjawab tantangan operasional, kasir, member, dan keuangan gym Anda.</p>
          </div>
        </div>

        <div class="row">
          <!-- Fitur 1: POS & Kasir -->
          <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up">
            <div class="ftco-feature-1 feature-box p-4 h-100">
              <span class="icon flaticon-stationary-bike text-danger mb-3 d-inline-block" style="font-size: 38px;"></span>
              <div class="ftco-feature-1-text">
                <h3 class="h5 font-weight-bold text-dark">Sistem Kasir & POS Terintegrasi</h3>
                <p class="text-muted small">
                  Pencatatan cepat untuk perpanjangan membership, tiket harian (guest), suplemen, dan minuman. Dilengkapi cetak struk otomatis, rekapitulasi shift kasir, dan audit pembatalan transaksi.
                </p>
              </div>
            </div>
          </div>

          <!-- Fitur 2: Presensi & Loker -->
          <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
            <div class="ftco-feature-1 feature-box p-4 h-100">
              <span class="icon flaticon-fit text-danger mb-3 d-inline-block" style="font-size: 38px;"></span>
              <div class="ftco-feature-1-text">
                <h3 class="h5 font-weight-bold text-dark">Presensi Check-in & Loker Digital</h3>
                <p class="text-muted small">
                  Proses check-in cepat member menggunakan kode akses statis (PIN) di front desk. Dilengkapi manajemen peminjaman kunci loker dengan status real-time (tersedia, terpakai, rusak).
                </p>
              </div>
            </div>
          </div>

          <!-- Fitur 3: Kelas & Personal Trainer -->
          <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
            <div class="ftco-feature-1 feature-box p-4 h-100">
              <span class="icon flaticon-gym-1 text-danger mb-3 d-inline-block" style="font-size: 38px;"></span>
              <div class="ftco-feature-1-text">
                <h3 class="h5 font-weight-bold text-dark">Jadwal Kelas & Sesi Personal Trainer</h3>
                <p class="text-muted small">
                  Kelola jadwal kelas mingguan (Yoga, Zumba, HIIT) lengkap dengan kuota peserta & RSVP. Manajemen sesi booking 1-on-1 Personal Trainer dengan pemotongan kuota otomatis.
                </p>
              </div>
            </div>
          </div>

          <!-- Fitur 4: Multi-Role & RBAC -->
          <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
            <div class="ftco-feature-1 feature-box p-4 h-100">
              <span class="icon flaticon-gym text-danger mb-3 d-inline-block" style="font-size: 38px;"></span>
              <div class="ftco-feature-1-text">
                <h3 class="h5 font-weight-bold text-dark">Kontrol Hak Akses 7 Role (RBAC)</h3>
                <p class="text-muted small">
                  Pemisahan peran terisolasi: Superadmin, Owner (pantau bisnis), Admin, Manager (otorisasi & approval), Resepsionis (layanan harian), Trainer (sesi & kelas), hingga Member.
                </p>
              </div>
            </div>
          </div>

          <!-- Fitur 5: Retensi & Member Expired -->
          <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="400">
            <div class="ftco-feature-1 feature-box p-4 h-100">
              <span class="icon flaticon-fit text-danger mb-3 d-inline-block" style="font-size: 38px;"></span>
              <div class="ftco-feature-1-text">
                <h3 class="h5 font-weight-bold text-dark">Otomatisasi Retensi & Peringatan Expired</h3>
                <p class="text-muted small">
                  Deteksi otomatis member yang masa berlakunya segera habis atau tidak hadir dalam jangka panjang. Membantu staf melakukan penawaran perpanjangan tepat waktu.
                </p>
              </div>
            </div>
          </div>

          <!-- Fitur 6: Portal Mandiri Member -->
          <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="500">
            <div class="ftco-feature-1 feature-box p-4 h-100">
              <span class="icon flaticon-stationary-bike text-danger mb-3 d-inline-block" style="font-size: 38px;"></span>
              <div class="ftco-feature-1-text">
                <h3 class="h5 font-weight-bold text-dark">Portal Self-Service Khusus Member</h3>
                <p class="text-muted small">
                  Member dapat masuk ke portal mandiri untuk melihat status masa aktif membership, jadwal kelas mendatang, sisa kuota sesi PT, tagihan, dan riwayat presensi harian.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Banner Break Background -->
    <div class="bgimg" style="background-image: url('images/bg_3.jpg');" data-stellar-background-ratio="0.5">
      <div class="container">
        <div class="row align-items-center justify-content-center text-center">
          <div class="col-md-8" data-aos="fade-up">
            <h2 class="text-white font-weight-bold mb-3">Pilih Paket Sesuai Kebutuhan Bisnis Anda</h2>
            <p class="lead mx-auto desc mb-5 text-white-50">Investasi transparan tanpa biaya tersembunyi untuk mendukung pertumbuhan gym Anda.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- 4. PAKET SEWA WEBSITE GYM (PRICING PLANS) -->
    <div class="site-section bg-light" id="pricing-section">
      <div class="container">
        @if(session('success_registration'))
          <div class="row justify-content-center mb-4">
            <div class="col-md-10">
              <div class="alert alert-success border-0 shadow-sm p-4 text-center rounded" style="border-radius: 12px; background-color: #d1e7dd; color: #0f5132;">
                <h5 class="font-weight-bold mb-2">🎉 Pendaftaran Berhasil Terkirim!</h5>
                <p class="mb-0">{{ session('success_registration') }}</p>
              </div>
            </div>
          </div>
        @endif

        <div class="row justify-content-center text-center mb-5">
          <div class="col-md-8 section-heading" data-aos="fade-up">
            <span class="subheading">Harga Transparan</span>
            <h2 class="heading mb-3">Paket Sewa SaaS Gym</h2>
            <p class="text-muted">Pilihan paket fleksibel dan terjangkau yang siap bertumbuh bersama skala bisnis gym Anda.</p>
          </div>
        </div>

        <div class="row align-items-stretch">
          <!-- Paket Basic -->
          <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="">
            <div class="card h-100 border-0 shadow-sm plan-card" style="border-radius: 16px; overflow: hidden; background: #ffffff;">
              <div class="card-body p-4 d-flex flex-column">
                <div class="mb-3">
                  <span class="badge badge-light border text-uppercase font-weight-bold px-3 py-1 text-muted" style="font-size: 11px;">Skala Pemula</span>
                  <h3 class="font-weight-bold text-dark mt-2 mb-1">Paket Basic</h3>
                  <p class="text-muted small">Cocok untuk gym skala studio atau usaha kebugaran baru.</p>
                </div>
                <div class="mb-4 pb-3 border-bottom">
                  <span class="h2 font-weight-bold text-dark">Rp 500.000</span>
                  <span class="text-muted small"> / bulan</span>
                </div>
                <ul class="list-unstyled mb-4 text-left text-dark small" style="line-height: 2.2;">
                  <li><span class="icon-check text-success mr-2"></span> Kapasitas maksimal <strong>150 Member Aktif</strong></li>
                  <li><span class="icon-check text-success mr-2"></span> Maksimal <strong>5 Akun Staf</strong> (Admin & Resepsionis)</li>
                  <li><span class="icon-check text-success mr-2"></span> Modul Kasir & POS Layanan Dasar Front-Desk</li>
                  <li><span class="icon-check text-success mr-2"></span> Presensi & Check-in Cepat (PIN)</li>
                  <li><span class="icon-check text-success mr-2"></span> Manajemen Kelas & Monitoring Loker Dasar</li>
                  <li><span class="icon-check text-success mr-2"></span> Website Gym (Kustomisasi Teks Dasar)</li>
                </ul>
                <button type="button" class="btn btn-outline-danger btn-block py-3 mt-auto font-weight-bold" onclick="openProspectModal('Paket Basic', '500.000')" style="border-radius: 30px; font-size: 13px;">
                  <span class="icon-shopping-cart mr-1"></span> Pilih Paket Basic
                </button>
              </div>
            </div>
          </div>

          <!-- Paket Pro (Paling Direkomendasikan) -->
          <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="100">
            <div class="card h-100 shadow-lg plan-card plan-card-pro" style="border-radius: 16px; overflow: hidden; background: #ffffff;">
              <div class="text-white text-center py-2 font-weight-bold uppercase small" style="background: #e11d48; letter-spacing: 1px;">
                <span class="icon-star mr-1"></span> PALING DIREKOMENDASIKAN
              </div>
              <div class="card-body p-4 d-flex flex-column">
                <div class="mb-3">
                  <span class="badge badge-danger text-uppercase font-weight-bold px-3 py-1" style="font-size: 11px;">Best Value</span>
                  <h3 class="font-weight-bold text-dark mt-2 mb-1">Paket Pro</h3>
                  <p class="text-muted small">Ideal untuk gym berkembang dengan operasional penuh & personal trainer.</p>
                </div>
                <div class="mb-4 pb-3 border-bottom">
                  <span class="h2 font-weight-bold text-danger">Rp 1.200.000</span>
                  <span class="text-muted small"> / bulan</span>
                </div>
                <ul class="list-unstyled mb-4 text-left text-dark small" style="line-height: 2.2;">
                  <li><span class="icon-check text-success mr-2"></span> Kapasitas maksimal <strong>500 Member Aktif</strong></li>
                  <li><span class="icon-check text-success mr-2"></span> Maksimal <strong>15 Akun Staf</strong> (Buka Role Manager & PT)</li>
                  <li><span class="icon-check text-success mr-2"></span> <strong>Termasuk Semua Fitur Paket Basic</strong></li>
                  <li><span class="icon-check text-success mr-2"></span> Manajemen Personal Trainer & Sesi 1-on-1</li>
                  <li><span class="icon-check text-success mr-2"></span> Manajemen Inventaris Ritel (Suplemen & Minuman)</li>
                  <li><span class="icon-check text-success mr-2"></span> Modul Retensi & Deteksi Member Expired</li>
                  <li><span class="icon-check text-success mr-2"></span> Analitik Keuangan & Performa Trainer</li>
                  <li><span class="icon-check text-success mr-2"></span> Kustomisasi Warna & Bagian Halaman Gym</li>
                </ul>
                <button type="button" class="btn btn-gradient-primary btn-block py-3 mt-auto font-weight-bold" onclick="openProspectModal('Paket Pro', '1.200.000')" style="border-radius: 30px; font-size: 13px;">
                  <span class="icon-rocket mr-1"></span> Pilih Paket Pro
                </button>
              </div>
            </div>
          </div>

          <!-- Paket Enterprise -->
          <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="200">
            <div class="card h-100 border-0 shadow-sm plan-card" style="border-radius: 16px; overflow: hidden; background: #ffffff;">
              <div class="card-body p-4 d-flex flex-column">
                <div class="mb-3">
                  <span class="badge badge-dark text-uppercase font-weight-bold px-3 py-1" style="font-size: 11px;">Skala Besar</span>
                  <h3 class="font-weight-bold text-dark mt-2 mb-1">Paket Enterprise</h3>
                  <p class="text-muted small">Kapasitas maksimal tanpa batasan untuk mega-gym atau waralaba/cabang.</p>
                </div>
                <div class="mb-4 pb-3 border-bottom">
                  <span class="h2 font-weight-bold text-dark">Rp 2.500.000</span>
                  <span class="text-muted small"> / bulan</span>
                </div>
                <ul class="list-unstyled mb-4 text-left text-dark small" style="line-height: 2.2;">
                  <li><span class="icon-check text-success mr-2"></span> Kapasitas Member Aktif <strong>Tanpa Batas (Unlimited)</strong></li>
                  <li><span class="icon-check text-success mr-2"></span> Akun Karyawan <strong>Tanpa Batas (Semua Role)</strong></li>
                  <li><span class="icon-check text-success mr-2"></span> <strong>Termasuk Semua Fitur Paket Pro</strong></li>
                  <li><span class="icon-check text-success mr-2"></span> Custom Domain Mandiri (misal: <em>gymanda.com</em>)</li>
                  <li><span class="icon-check text-success mr-2"></span> Live Counter Statistik Angka di Website Gym</li>
                  <li><span class="icon-check text-success mr-2"></span> Analitik Lanjutan Penuh & Log Audit Sistem</li>
                  <li><span class="icon-check text-success mr-2"></span> Prioritas Dukungan Teknis 24/7 & Dedicated Backup</li>
                </ul>
                <button type="button" class="btn btn-outline-dark btn-block py-3 mt-auto font-weight-bold" onclick="openProspectModal('Paket Enterprise', '2.500.000')" style="border-radius: 30px; font-size: 13px;">
                  <span class="icon-diamond mr-1"></span> Pilih Paket Enterprise
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- 5. FOOTER & KONTAK -->
    <footer class="footer-section bg-dark py-5" id="contact-section">
      <div class="container">
        <div class="row align-items-stretch">

          <!-- Kolom 1: Hubungi Kami -->
          <div class="col-lg-4 mb-4" data-aos="fade-up">
            <div class="card h-100 border-0 bg-transparent">
              <div class="card-body p-0 text-left">
                <div class="mb-3">
                  <x-brand-logo type="full" theme="dark" size="36" url="/" onclick="if(window.location.pathname === '/' || window.location.pathname === ''){ window.scrollTo({top: 0, behavior: 'smooth'}); return false; }" />
                </div>
                <p class="text-white-50 small mb-4">
                  Platform manajemen gym cloud terbaik untuk mempermudah operasional, otomatisasi kasir, retensi member, dan analitik bisnis kebugaran Anda.
                </p>

                <div class="contact-info">
                  <!-- Telepon -->
                  <div class="d-flex align-items-center mb-3">
                    <div class="icon-wrap mr-3 bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; min-width: 38px;">
                      <span class="icon-phone small"></span>
                    </div>
                    <div>
                      <small class="text-white-50 d-block font-weight-bold" style="font-size: 0.75rem; letter-spacing: 1px;">WHATSAPP / TELEPON</small>
                      <a href="https://wa.me/6288977713600" target="_blank" class="text-white font-weight-bold text-decoration-none">+62 889-7771-3600</a>
                    </div>
                  </div>

                  <!-- Email -->
                  <div class="d-flex align-items-center">
                    <div class="icon-wrap mr-3 bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; min-width: 38px;">
                      <span class="icon-envelope small"></span>
                    </div>
                    <div>
                      <small class="text-white-50 d-block font-weight-bold" style="font-size: 0.75rem; letter-spacing: 1px;">EMAIL DUKUNGAN</small>
                      <a href="mailto:support@petgym.com" class="text-white font-weight-bold text-decoration-none">support@petgym.com</a>
                    </div>
                  </div>
                </div>

              </div>
            </div>
          </div>

          <!-- Kolom 2: Fitur Unggulan -->
          <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="100">
            <div class="card h-100 border-0 bg-transparent">
              <div class="card-body p-0 text-left">
                <h3 class="font-weight-bold text-white mb-3 h5">Fitur Unggulan</h3>
                <p class="text-white-50 small mb-3">Modul terintegrasi yang siap pakai untuk gym Anda.</p>

                <ul class="list-unstyled mb-0 small">
                  <li class="mb-2">
                    <a href="#fitur-section" class="text-white-50 text-decoration-none d-flex align-items-center hover-text-white">
                      <span class="icon-check text-danger mr-2"></span> Sistem Kasir & POS Front-Desk
                    </a>
                  </li>
                  <li class="mb-2">
                    <a href="#fitur-section" class="text-white-50 text-decoration-none d-flex align-items-center">
                      <span class="icon-check text-danger mr-2"></span> Check-in Presensi & Loker Digital
                    </a>
                  </li>
                  <li class="mb-2">
                    <a href="#fitur-section" class="text-white-50 text-decoration-none d-flex align-items-center">
                      <span class="icon-check text-danger mr-2"></span> Manajemen Kelas & Sesi PT
                    </a>
                  </li>
                  <li class="mb-2">
                    <a href="#fitur-section" class="text-white-50 text-decoration-none d-flex align-items-center">
                      <span class="icon-check text-danger mr-2"></span> Portal Mandiri Khusus Member
                    </a>
                  </li>
                  <li class="mb-2">
                    <a href="#fitur-section" class="text-white-50 text-decoration-none d-flex align-items-center">
                      <span class="icon-check text-danger mr-2"></span> Analitik & Rekapitulasi Keuangan
                    </a>
                  </li>
                </ul>
              </div>
            </div>
          </div>

          <!-- Kolom 3: Ajakan Bergabung & Konsultasi -->
          <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="200">
            <div class="card h-100 border-0 bg-transparent">
              <div class="card-body p-0 text-left">
                <h3 class="font-weight-bold text-white mb-3 h5">Siap Melangkah Lebih Jauh?</h3>
                <p class="text-white-50 small mb-3">
                  Tinggalkan pencatatan manual dan sistem kasir yang berantakan. Bergabunglah bersama pengelola gym modern dan rasakan kemudahan kendali penuh berbasis cloud dari mana saja.
                </p>

                <div class="mb-4">
                  <a href="{{ route('register') }}" class="btn btn-gradient-primary btn-block py-3 font-weight-bold text-center" style="border-radius: 30px; font-size: 14px; text-transform: none;">
                    <span class="icon-rocket mr-2"></span> Mulai Bangun Website Anda
                  </a>
                </div>

                <div class="mb-0 pt-2 border-top border-secondary">
                  <small class="text-white-50 d-block font-weight-bold" style="font-size: 0.75rem; letter-spacing: 1px;">JAM OPERASIONAL LAYANAN</small>
                  <span class="text-white small">Senin - Minggu (08.00 - 22.00 WIB)</span>
                </div>
              </div>
            </div>
          </div>

        </div>

        <!-- Copyright -->
        <div class="row pt-4 text-center border-top border-secondary">
          <div class="col-md-12">
            <p class="text-white-50 small mb-0">
              Hak Cipta &copy;<script>document.write(new Date().getFullYear());</script> PetGym SaaS. Seluruh hak cipta dilindungi undang-undang.
            </p>
          </div>
        </div>

      </div>
    </footer>

  </div>
  <!-- .site-wrap -->

  <!-- Modal Pendaftaran Prospek Sewa Web Gym -->
  <div class="modal fade" id="prospectModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
      <form action="{{ route('register.saas') }}" method="POST" class="modal-content" style="border-radius: 16px; border: none; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
        @csrf
        <input type="hidden" name="plan_name" id="modalPlanName" value="Paket Pro">

        <div class="modal-header bg-dark text-white p-4">
          <div>
            <span class="badge badge-danger text-uppercase font-weight-bold px-3 py-1 mb-1" style="font-size: 10px;">Pendaftaran Sewa SaaS</span>
            <h4 class="modal-title font-weight-bold text-white mb-0" id="modalTitle">Pendaftaran Sewa PetGym</h4>
          </div>
          <button type="button" class="close text-white" data-dismiss="modal" style="opacity: 0.8;">&times;</button>
        </div>

        <div class="modal-body p-4">
          <div class="p-3 mb-3 rounded d-flex justify-content-between align-items-center" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px;">
            <div>
              <small class="text-muted d-block">Paket Yang Dipilih:</small>
              <strong class="text-danger h5 mb-0 font-weight-bold" id="displayPlanName">Paket Pro</strong>
            </div>
            <div class="text-right">
              <small class="text-muted d-block">Biaya Langganan:</small>
              <strong class="text-dark font-weight-bold" id="displayFullPrice">Rp 1.200.000 / bln</strong>
            </div>
          </div>

          <div class="form-group mb-3">
            <label class="font-weight-bold text-dark small">Nama Pemilik / Penanggung Jawab <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" placeholder="Contoh: Budi Pratama" required style="border-radius: 8px; font-size: 14px;">
          </div>

          <div class="form-group mb-3">
            <label class="font-weight-bold text-dark small">Alamat Email Aktif <span class="text-danger">*</span></label>
            <input type="email" name="email" class="form-control" placeholder="budi@fitlife.com" required style="border-radius: 8px; font-size: 14px;">
            <small class="text-muted">Digunakan untuk pengiriman akun login & notifikasi tagihan.</small>
          </div>

          <div class="form-group mb-3">
            <label class="font-weight-bold text-dark small">Nomor WhatsApp Aktif <span class="text-danger">*</span></label>
            <input type="text" name="phone" class="form-control" placeholder="081234567890" required style="border-radius: 8px; font-size: 14px;">
            <small class="text-muted">Untuk konfirmasi aktivasi dan bantuan teknis.</small>
          </div>

          <div class="form-group mb-0">
            <label class="font-weight-bold text-dark small">Nama Gym / Kebutuhan Khusus (Opsional)</label>
            <textarea name="notes" rows="2" class="form-control" placeholder="Tuliskan nama gym atau kebutuhan spesifik Anda..." style="border-radius: 8px; font-size: 14px;"></textarea>
          </div>
        </div>

        <div class="modal-footer bg-light p-3">
          <button type="button" class="btn btn-secondary px-3 py-2" data-dismiss="modal" style="border-radius: 20px; font-size: 13px;">Batal</button>
          <button type="submit" class="btn btn-gradient-primary font-weight-bold px-4 py-2" style="border-radius: 20px; font-size: 13px;">
            <span class="icon-send mr-1"></span> Kirim Formulir Pendaftaran
          </button>
        </div>
      </form>
    </div>
  </div>

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

  <script>
    function openProspectModal(planName, price) {
      document.getElementById('modalPlanName').value = planName;
      document.getElementById('modalTitle').innerText = 'Pendaftaran Sewa — ' + planName;
      document.getElementById('displayPlanName').innerText = planName;
      document.getElementById('displayFullPrice').innerText = 'Rp ' + price + ' / bln';
      $('#prospectModal').modal('show');
    }
  </script>

</body>

</html>
