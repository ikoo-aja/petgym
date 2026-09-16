<!DOCTYPE html>
<html lang="en">

<head>
  <title>Pet Gym SaaS Management System</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <x-dynamic-favicon />

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
      <div class="container-fluid px-4 px-lg-5">
        <div class="d-flex align-items-center justify-content-between">
          <div class="site-logo">
            <x-brand-logo type="full" theme="dark" size="42" url="/" />
          </div>
          <div class="ml-auto d-flex align-items-center">
            <nav class="site-navigation position-relative text-right" role="navigation">
              <ul class="site-menu main-menu js-clone-nav mr-auto d-none d-lg-block">
                <li><a href="#home-section" class="nav-link">Beranda</a></li>
                <li><a href="#cara-kerja-section" class="nav-link">Cara Kerja</a></li>
                <li><a href="#fitur-section" class="nav-link">Fitur Utama</a></li>
                <li><a href="#pricing-section" class="nav-link">Paket Harga</a></li>
                <li><a href="#contact-section" class="nav-link">Kontak</a></li>
                <li class="d-inline-block ml-3"><a href="{{ route('login') }}" class="btn btn-outline-white text-white px-4 py-2 font-weight-bold" style="border-radius: 30px; border: 2px solid rgba(255,255,255,0.7);">Masuk</a></li>
                <li class="d-inline-block ml-2"><a href="{{ route('register') }}" class="btn btn-primary text-white px-4 py-2 font-weight-bold shadow-sm" style="border-radius: 30px;">Daftar Akun</a></li>
              </ul>
            </nav>
            <a href="#" class="d-inline-block d-lg-none site-menu-toggle js-menu-toggle float-right"><span class="icon-menu h3"></span></a>
          </div>
        </div>
      </div>
    </header>

    <!-- 1. HERO SECTION -->
    <div class="intro-section" id="home-section" style="background-image: url('{{ asset('images/hero_bg.png') }}'); background-size: cover; background-position: center;">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-lg-10 mx-auto text-center" data-aos="fade-up">
            <h1 class="mb-3 text-white font-weight-bold">Satu Platform untuk Seluruh Kendali Gym Anda.</h1>
            <p class="lead mx-auto desc mb-5 text-white-50" style="max-width: 900px;">
              Tinggalkan pencatatan manual dan sistem kasir yang berantakan. Kami menghadirkan mesin manajemen gym berbasis <em>cloud</em> (SaaS) yang dirancang khusus untuk mempermudah operasional harian. Dari pendaftaran member, riwayat transaksi, hingga presensi kelas, semuanya terintegrasi dalam satu sistem cerdas yang bisa Anda pantau dari mana saja.
            </p>
            <p class="text-center">
              <a href="#contact-section" class="nav-link btn btn-outline-white py-3 px-5" style="border-radius: 30px; font-weight: 700;">Mulai Eksekusi Tanpa Ribet - Free Trial 14 Hari</a>
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- 2. CARA KERJA (ALUR OPERASIONAL TANPA HAMBATAN) -->
    <div class="site-section" id="cara-kerja-section">
      <div class="container">
        <div class="row justify-content-center text-center mb-5">
          <div class="col-md-8 section-heading" data-aos="fade-up">
            <span class="subheading">Alur Operasional</span>
            <h2 class="heading mb-3">Cara Kerja Tanpa Hambatan</h2>
            <p class="text-black font-semibold">Bagian ini menceritakan betapa mudahnya pemilik gym beralih ke sistem digital kami tanpa pusing memikirkan teknis IT.</p>
          </div>
        </div>

        <div class="row">
          <!-- Langkah 1 -->
          <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="">
            <div class="ftco-feature-1" style="height: 100%;">
              <span class="icon flaticon-fit"></span>
              <div class="ftco-feature-1-text">
                <h2>Langkah 1: Peralihan Tanpa Hambatan</h2>
                <p>
                  Memulai digitalisasi gym Anda tidak pernah semudah ini. Anda hanya perlu memilih paket, dan sistem kami akan langsung mengalokasikan ruang data mandiri untuk gym Anda. Tanpa perlu investasi <em>server</em> fisik atau instalasi perangkat lunak yang rumit.
                </p>
              </div>
            </div>
          </div>

          <!-- Langkah 2 -->
          <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="100">
            <div class="ftco-feature-1" style="height: 100%;">
              <span class="icon flaticon-gym-1"></span>
              <div class="ftco-feature-1-text">
                <h2>Langkah 2: Sentralisasi Data Otomatis</h2>
                <p>
                  Setelah akun aktif, Anda memiliki kendali penuh untuk mengatur master data. Tentukan harga paket <em>membership</em>, buat jadwal kelas mingguan, dan atur struktur karyawan—mulai dari Resepsionis hingga Manager—dengan hak akses yang aman dan terisolasi.
                </p>
              </div>
            </div>
          </div>

          <!-- Langkah 3 -->
          <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="200">
            <div class="ftco-feature-1" style="height: 100%;">
              <span class="icon flaticon-gym"></span>
              <div class="ftco-feature-1-text">
                <h2>Langkah 3: Operasional Harian yang Mulus</h2>
                <p>
                  Sistem kami mengambil alih beban kerja repetitif. Member masuk hanya dengan menggunakan kode akses statis, resepsionis mencetak tagihan secara lokal tanpa <em>delay</em>, dan Anda sebagai pemilik bisa melihat ringkasan pendapatan hari itu juga melalui dasbor metrik.
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
            <h2>Solusi Manajemen Gym Berbasis Cloud (SaaS)</h2>
            <p class="lead mx-auto desc mb-5">Satu sistem terpadu untuk efisiensi penuh seluruh aktivitas operasional gym Anda.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- 3. FITUR UTAMA (FOKUS PADA SOLUSI BISNIS) -->
    <div class="site-section" id="fitur-section">
      <div class="container">
        <div class="row justify-content-center text-center mb-5">
          <div class="col-md-8 section-heading" data-aos="fade-up">
            <span class="subheading">Solusi Bisnis</span>
            <h2 class="heading mb-3">Fitur Utama</h2>
            <p class="text-black font-semibold">Penjelasan fitur yang langsung menjawab <em>pain points</em> yang sering dialami pengelola gym.</p>
          </div>
        </div>

        <div class="row">
          <!-- Fitur 1 -->
          <div class="col-lg-6 mb-4" data-aos="fade-up">
            <div class="ftco-feature-1">
              <span class="icon flaticon-stationary-bike"></span>
              <div class="ftco-feature-1-text">
                <h2>Sistem Kasir & POS Terintegrasi</h2>
                <p>
                  Setiap transaksi di meja depan, mulai dari perpanjangan <em>membership</em> hingga penjualan suplemen, tercatat seketika. Kami memisahkan pencatatan layanan gym dan penjualan ritel untuk memastikan pelaporan keuangan akhir hari Anda akurat dan mudah diaudit.
                </p>
              </div>
            </div>
          </div>

          <!-- Fitur 2 -->
          <div class="col-lg-6 mb-4" data-aos="fade-up" data-aos-delay="100">
            <div class="ftco-feature-1">
              <span class="icon flaticon-fit"></span>
              <div class="ftco-feature-1-text">
                <h2>Kontrol Hierarki Karyawan yang Ketat</h2>
                <p>
                  Amankan data Anda melalui pembagian <em>role</em> yang presisi. Resepsionis fokus pada kecepatan pelayanan, Manager memegang otorisasi pembatalan transaksi, dan Anda memegang kendali penuh atas analitik bisnis. Tidak ada lagi kecurangan atau kebocoran data.
                </p>
              </div>
            </div>
          </div>

          <!-- Fitur 3 -->
          <div class="col-lg-6 mb-4" data-aos="fade-up" data-aos-delay="200">
            <div class="ftco-feature-1">
              <span class="icon flaticon-gym-1"></span>
              <div class="ftco-feature-1-text">
                <h2>Manajemen Kelas dan Retensi Member</h2>
                <p>
                  Sistem secara otomatis mendeteksi member yang masa aktifnya segera habis atau sudah lama tidak datang ke gym, sehingga tim Anda bisa langsung melakukan <em>follow-up</em>. Terintegrasi dengan manajemen kuota kelas dan penjadwalan Personal Trainer agar operasional lebih rapi.
                </p>
              </div>
            </div>
          </div>

          <!-- Fitur 4 -->
          <div class="col-lg-6 mb-4" data-aos="fade-up" data-aos-delay="300">
            <div class="ftco-feature-1">
              <span class="icon flaticon-gym"></span>
              <div class="ftco-feature-1-text">
                <h2>Kontrol Inventaris & Fasilitas</h2>
                <p>
                  Pantau ketersediaan barang jualan (suplemen/minuman) dan pantau manajemen peminjaman loker langsung dari meja depan untuk meminimalisir kehilangan aset gym Anda.
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
            <h2>Pilih Paket Sesuai Kebutuhan Bisnis Anda</h2>
            <p>Investasi transparan tanpa biaya tersembunyi untuk mendukung pertumbuhan gym Anda.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- 4. PAKET SEWA WEBSITE GYM (PRICING PLANS) -->
    <div class="site-section bg-light" id="pricing-section">
      <div class="container">
        @if(session('checkout_success'))
          <div class="row justify-content-center mb-4">
            <div class="col-md-10">
              <div class="alert alert-success border-0 shadow-sm p-4 text-center rounded" style="border-radius: 12px; background-color: #d1e7dd; color: #0f5132;">
                <h5 class="font-weight-bold mb-2">🎉 Pendaftaran & Transfer DP 50% Berhasil Dikirim!</h5>
                <p class="mb-0">{{ session('checkout_success') }}</p>
              </div>
            </div>
          </div>
        @endif

        <div class="row justify-content-center text-center mb-5">
          <div class="col-md-8 section-heading" data-aos="fade-up">
            <span class="subheading">Harga Transparan</span>
            <h2 class="heading mb-3">Paket Sewa Website Gym</h2>
            <p class="text-black font-semibold">Informasi harga yang transparan dengan batasan kuota yang jelas agar pemilik gym bisa memilih sesuai ukuran bisnis mereka.</p>
          </div>
        </div>

        <div class="row align-items-stretch">
          <!-- Paket Basic -->
          <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="">
            <div class="card h-100 border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
              <div class="card-body p-4 d-flex flex-column">
                <h3 class="font-weight-bold text-dark mb-2">Paket Basic</h3>
                <p class="text-muted small mb-4">Cocok untuk gym skala kecil atau baru buka.</p>
                <div class="mb-4">
                  <span class="h2 font-weight-bold text-primary">Rp 500.000</span>
                  <span class="text-muted"> / bulan</span>
                </div>
                <ul class="list-unstyled mb-4 text-left text-dark" style="line-height: 2;">
                  <li>✔ Kapasitas maksimal 150 Member Aktif</li>
                  <li>✔ Maksimal 5 Akun Karyawan (Admin & Resepsionis)</li>
                  <li>✔ Termasuk Modul POS, Kasir, dan Check-in Cepat</li>
                </ul>
                <button type="button" class="btn btn-outline-primary btn-block py-3 mt-auto font-weight-bold" onclick="openWebCheckout('Paket Basic', 500000, 250000)" style="border-radius: 30px;">
                  🛒 Pilih & Sewa Paket Basic
                </button>
              </div>
            </div>
          </div>

          <!-- Paket Pro (Paling Direkomendasikan) -->
          <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="100">
            <div class="card h-100 border-primary shadow" style="border-radius: 15px; overflow: hidden; border-width: 2px; transform: scale(1.02);">
              <div class="bg-primary text-white text-center py-2 font-weight-bold uppercase small" style="letter-spacing: 1px;">
                ⭐ Paling Direkomendasikan
              </div>
              <div class="card-body p-4 d-flex flex-column">
                <h3 class="font-weight-bold text-dark mb-2">Paket Pro</h3>
                <p class="text-muted small mb-4">Ideal untuk gym berkembang dengan operasional penuh.</p>
                <div class="mb-4">
                  <span class="h2 font-weight-bold text-primary">Rp 1.200.000</span>
                  <span class="text-muted"> / bulan</span>
                </div>
                <ul class="list-unstyled mb-4 text-left text-dark" style="line-height: 2;">
                  <li>✔ Kapasitas maksimal 500 Member Aktif</li>
                  <li>✔ Maksimal 15 Akun Karyawan (Termasuk Manager & PT)</li>
                  <li>✔ Termasuk semua fitur Basic</li>
                  <li>✔ Manajemen Inventaris Ritel</li>
                  <li>✔ Modul Retensi Member</li>
                  <li>✔ Analitik Kelas</li>
                </ul>
                <button type="button" class="btn btn-primary btn-block py-3 mt-auto font-weight-bold text-white shadow-sm" onclick="openWebCheckout('Paket Pro', 1200000, 600000)" style="border-radius: 30px;">
                  🚀 Pilih & Sewa Paket Pro
                </button>
              </div>
            </div>
          </div>

          <!-- Paket Enterprise -->
          <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="200">
            <div class="card h-100 border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
              <div class="card-body p-4 d-flex flex-column">
                <h3 class="font-weight-bold text-dark mb-2">Paket Enterprise</h3>
                <p class="text-muted small mb-4">Kapasitas maksimal untuk mega-gym atau franchise.</p>
                <div class="mb-4">
                  <span class="h2 font-weight-bold text-primary">Rp 2.500.000</span>
                  <span class="text-muted"> / bulan</span>
                </div>
                <ul class="list-unstyled mb-4 text-left text-dark" style="line-height: 2;">
                  <li>✔ Kapasitas Member Aktif Tanpa Batas (Unlimited)</li>
                  <li>✔ Akun Karyawan Tanpa Batas (Unlimited)</li>
                  <li>✔ Termasuk semua fitur Pro</li>
                  <li>✔ Custom Domain (nama website gym sendiri)</li>
                  <li>✔ Prioritas Support 24/7</li>
                </ul>
                <button type="button" class="btn btn-outline-primary btn-block py-3 mt-auto font-weight-bold" onclick="openWebCheckout('Paket Enterprise', 2500000, 1250000)" style="border-radius: 30px;">
                  👑 Pilih & Sewa Enterprise
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- 5. KONTAK SECTION -->


    <!-- Footer -->
<footer class="footer-section bg-dark py-5" id="contact-section">
  <div class="container">
    <div class="row align-items-stretch">

      <!-- Kolom 1: Hubungi Kami -->
      <div class="col-lg-4 mb-4" data-aos="fade-up">
        <!-- bg-transparent & border-0 membuat card menyatu dengan background footer -->
        <div class="card h-100 border-0 bg-transparent">
          <div class="card-body p-0 text-left">
            <h3 class="font-weight-bold text-white mb-2 h4">Hubungi Kami</h3>
            <p class="text-white-50 small mb-4">Kami siap membantu Anda dengan pertanyaan atau keluhan apa pun.</p>

            <div class="contact-info">
              <!-- Telepon -->
              <div class="d-flex align-items-center mb-3">
                <div class="icon-wrap mr-3 bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; min-width: 40px;">
                  <span class="icon-phone h6 mb-0"></span>
                </div>
                <div>
                  <small class="text-white-50 d-block font-weight-bold" style="font-size: 0.75rem; letter-spacing: 1px;">PHONE NUMBER</small>
                  <a href="https://wa.me/6288977713600" target="_blank" class="text-white font-weight-bold text-decoration-none">+62 889-7771-3600</a>
                </div>
              </div>

              <!-- Email -->
              <div class="d-flex align-items-center">
                <div class="icon-wrap mr-3 bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; min-width: 40px;">
                  <span class="icon-envelope h6 mb-0"></span>
                </div>
                <div>
                  <small class="text-white-50 d-block font-weight-bold" style="font-size: 0.75rem; letter-spacing: 1px;">EMAIL</small>
                  <a href="mailto:shfwn121984@gmail.com" class="text-white font-weight-bold text-decoration-none">shfwn121984@gmail.com</a>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>

      <!-- Kolom 2: Layanan Utama -->
      <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="100">
        <div class="card h-100 border-0 bg-transparent">
          <div class="card-body p-0 text-left">
            <h3 class="font-weight-bold text-white mb-2 h4">Layanan Utama</h3>
            <p class="text-white-50 small mb-4">Nikmati berbagai program kebugaran terbaik dari kami.</p>

            <ul class="list-unstyled mb-0">
              <li class="mb-2">
                <a href="index.html#classes-section" class="text-white-50 font-weight-bold text-decoration-none d-flex align-items-center">
                  <span class="icon-check text-primary mr-2"></span> Membership / Paket
                </a>
              </li>
              <li class="mb-2">
                <a href="index.html#trainer-section" class="text-white-50 font-weight-bold text-decoration-none d-flex align-items-center">
                  <span class="icon-check text-primary mr-2"></span> Personal Trainer
                </a>
              </li>
              <li class="mb-2">
                <a href="index.html#schedule-section" class="text-white-50 font-weight-bold text-decoration-none d-flex align-items-center">
                  <span class="icon-check text-primary mr-2"></span> Jadwal Kelas
                </a>
              </li>
              <li class="mb-2">
                <a href="index.html#services-section" class="text-white-50 font-weight-bold text-decoration-none d-flex align-items-center">
                  <span class="icon-check text-primary mr-2"></span> Fasilitas Gym
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Kolom 3: Kemitraan & Informasi -->
      <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="200">
        <div class="card h-100 border-0 bg-transparent">
          <div class="card-body p-0 text-left">
            <h3 class="font-weight-bold text-white mb-2 h4">Kemitraan & Informasi</h3>
            <p class="text-white-50 small mb-4">Terbuka untuk kerjasama perusahaan dan media.</p>

            <div class="mb-3">
              <small class="text-white-50 d-block font-weight-bold" style="font-size: 0.75rem; letter-spacing: 1px;">PARTNERSHIP</small>
              <a href="mailto:partnership@petgym.com" class="text-white font-weight-bold text-decoration-none">partnership@petgym.com</a>
            </div>

            <div class="mb-0">
              <small class="text-white-50 d-block font-weight-bold" style="font-size: 0.75rem; letter-spacing: 1px;">JAM OPERASIONAL</small>
              <span class="text-white font-weight-bold">Senin - Minggu (06.00 - 22.00)</span>
            </div>
          </div>
        </div>
      </div>

    </div>

    <!-- Copyright -->
    <div class="row pt-4 text-center border-top border-secondary">
      <div class="col-md-12">
        <p class="text-white-50 small mb-0">
          Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved | Pet Gym Management System
        </p>
      </div>
    </div>

  </div>
</footer>

  </div>
  <!-- .site-wrap -->

  <!-- Modal Checkout Pembelian / Penyewaan Web Gym ke Superadmin -->
  <div class="modal fade" id="webCheckoutModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <form action="{{ route('public.checkout') }}" method="POST" enctype="multipart/form-data" class="modal-content" style="border-radius: 15px;">
        @csrf
        <input type="hidden" name="plan_name" id="modalPlanName">
        <input type="hidden" name="dp_price" id="modalDpPrice">

        <div class="modal-header bg-dark text-white p-4">
          <div>
            <span class="badge badge-danger text-uppercase font-weight-bold px-3 py-1 mb-1" style="font-size: 10px;">SaaS Web Checkout</span>
            <h4 class="modal-title font-weight-bold text-white mb-0" id="modalTitle">Formulir Penyewaan Web Gym</h4>
          </div>
          <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
        </div>

        <div class="modal-body p-4">
          <div class="alert alert-info border-0 p-3 mb-4 rounded-lg" style="background-color: #e0f2fe; color: #0369a1; border-radius: 10px; font-size: 13px;">
            <i class="icon-info mr-1"></i> <strong>Instruksi Pembelian Web:</strong> Isi identitas gym yang ingin dibuatkan website. Bayar **DP 50%** ke rekening Superadmin untuk mengaktifkan subdomain & sistem gym Anda.
          </div>

          <div class="row">
            <div class="col-md-6 form-group mb-3">
              <label class="font-weight-bold text-dark small">Nama Studio Gym / Tenant *</label>
              <input type="text" name="gym_name" class="form-control" placeholder="Contoh: FitLife Studio" required style="border-radius: 8px;">
            </div>
            <div class="col-md-6 form-group mb-3">
              <label class="font-weight-bold text-dark small">Subdomain Pilihan *</label>
              <div class="input-group">
                <input type="text" name="subdomain" class="form-control" placeholder="fitlife" required style="border-top-left-radius: 8px; border-bottom-left-radius: 8px;">
                <div class="input-group-append">
                  <span class="input-group-text bg-light font-weight-bold text-muted" style="border-top-right-radius: 8px; border-bottom-right-radius: 8px; font-size: 13px;">.workout.id</span>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-4 form-group mb-3">
              <label class="font-weight-bold text-dark small">Nama Pemilik (Owner) *</label>
              <input type="text" name="owner_name" class="form-control" placeholder="Contoh: Alfredo" required style="border-radius: 8px;">
            </div>
            <div class="col-md-4 form-group mb-3">
              <label class="font-weight-bold text-dark small">Email Resmi Owner *</label>
              <input type="email" name="owner_email" class="form-control" placeholder="owner@fitlife.com" required style="border-radius: 8px;">
            </div>
            <div class="col-md-4 form-group mb-3">
              <label class="font-weight-bold text-dark small">No. WhatsApp / Telepon *</label>
              <input type="text" name="owner_phone" class="form-control" placeholder="081234567890" required style="border-radius: 8px;">
            </div>
          </div>

          <div class="p-3 mb-4 rounded" style="background-color: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 10px;">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="text-muted small">Paket Sewa Dipilih:</span>
              <span class="font-weight-bold text-dark" id="displayPlanName">-</span>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="text-muted small">Harga Normal Bulanan:</span>
              <span class="font-weight-bold text-dark" id="displayFullPrice">-</span>
            </div>
            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
              <span class="font-weight-bold text-primary">DP 50% Yang Wajib Ditransfer:</span>
              <h4 class="font-weight-extrabold text-primary mb-0" id="displayDpPrice">-</h4>
            </div>
          </div>

          <div class="form-group mb-3">
            <label class="font-weight-bold text-dark small d-block">Transfer DP 50% Ke Rekening Superadmin:</label>
            <div class="p-3 bg-light rounded border mb-2 font-weight-bold text-dark" style="font-size: 14px;">
              🏦 Bank BCA: <span class="text-primary font-weight-extrabold">8830-1234-5678</span> a.n. PetGym SaaS Superadmin
            </div>
          </div>

          <div class="form-group mb-0">
            <label class="font-weight-bold text-dark small">Upload Bukti Transfer DP 50% *</label>
            <input type="file" name="proof_file" class="form-control-file border p-2 rounded bg-white" accept="image/*" required style="border-radius: 8px;">
            <small class="text-muted">Format: JPG, PNG. Maksimal 5MB.</small>
          </div>
        </div>

        <div class="modal-footer bg-light p-3">
          <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 20px;">Batal</button>
          <button type="submit" class="btn btn-primary font-weight-bold px-4 py-2 shadow-sm" style="border-radius: 20px;">
            <i class="icon-send mr-1"></i> Kirim Permohonan Sewa Web Gym
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
    function openWebCheckout(planName, fullPrice, dpPrice) {
      document.getElementById('modalPlanName').value = planName;
      document.getElementById('modalDpPrice').value = dpPrice;
      document.getElementById('modalTitle').innerText = 'Penyewaan Website Gym — ' + planName;
      document.getElementById('displayPlanName').innerText = planName;
      document.getElementById('displayFullPrice').innerText = 'Rp ' + Number(fullPrice).toLocaleString('id-ID') + ' / bulan';
      document.getElementById('displayDpPrice').innerText = 'Rp ' + Number(dpPrice).toLocaleString('id-ID');
      $('#webCheckoutModal').modal('show');
    }
  </script>

</body>

</html>
