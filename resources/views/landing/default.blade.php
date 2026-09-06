@php
  // Warna brand tenant (fallback ke default jika belum diatur / paket Basic)
  $primary   = $settings->primary_color ?: '#f43f5e';
  $secondary = $settings->secondary_color ?: '#111827';
  $sections  = is_array($settings->sections_enabled) ? $settings->sections_enabled : [];
  $showStats = in_array('stats', $sections) && !empty($settings->stats);
  $stats     = is_array($settings->stats) ? $settings->stats : [];
  $features  = is_array($settings->features) && count($settings->features) ? $settings->features : null;
  $tenantFeatures = is_array($tenant->features) ? $tenant->features : [];
  $heroTitle = $settings->hero_title ?: $tenant->name;
  $ctaText   = $settings->cta_text ?: 'Mulai Sekarang';
  $ctaUrl    = $settings->cta_url ?: config('app.url');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>{{ $tenant->name }} — {{ $settings->hero_tagline ?: 'Pusat Kebugaran' }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Muli:wght@300;400;600;700;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('fonts/icomoon/style.css') }}">
  <style>
    :root {
      --brand: {{ $primary }};
      --brand-dark: {{ $secondary }};
    }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: 'Muli', sans-serif;
      color: #1f2937;
      background: #ffffff;
      line-height: 1.7;
    }
    a { text-decoration: none; }
    .container { max-width: 1140px; margin: 0 auto; padding: 0 24px; }
    .text-center { text-align: center; }
    .btn {
      display: inline-block;
      padding: 14px 32px;
      border-radius: 999px;
      font-weight: 700;
      font-size: 14px;
      letter-spacing: .3px;
      transition: transform .2s ease, box-shadow .2s ease;
    }
    .btn-brand { background: var(--brand); color: #fff; box-shadow: 0 8px 20px rgba(0,0,0,.12); }
    .btn-brand:hover { transform: translateY(-2px); box-shadow: 0 12px 26px rgba(0,0,0,.18); }
    .btn-ghost { border: 2px solid rgba(255,255,255,.85); color: #fff; }
    .btn-ghost:hover { background: rgba(255,255,255,.12); }

    /* ===== NAVBAR ===== */
    .site-nav {
      position: sticky; top: 0; z-index: 50;
      background: var(--brand-dark);
      padding: 16px 0;
    }
    .site-nav .container { display: flex; align-items: center; justify-content: space-between; }
    .brand-name { color: #fff; font-weight: 900; font-size: 19px; letter-spacing: -.3px; }
    .brand-name span { color: var(--brand); }
    .nav-links { display: flex; align-items: center; gap: 28px; }
    .nav-links a { color: rgba(255,255,255,.82); font-size: 14px; font-weight: 600; transition: color .2s; }
    .nav-links a:hover { color: #fff; }
    .nav-login {
      border: 1px solid rgba(255,255,255,.4);
      padding: 8px 18px;
      border-radius: 999px;
    }
    .nav-login:hover { background: rgba(255,255,255,.1); }
    @media (max-width: 640px) { .nav-links a:not(.nav-login) { display: none; } }

    /* ===== HERO ===== */
    .hero {
      background: var(--brand-dark);
      color: #fff;
      padding: 96px 0 110px;
      position: relative;
      overflow: hidden;
    }
    .hero::before {
      content: '';
      position: absolute; right: -140px; top: -140px;
      width: 460px; height: 460px; border-radius: 50%;
      background: radial-gradient(circle, {{ $primary }}44, transparent 70%);
    }
    .hero .container { position: relative; display: grid; grid-template-columns: 1.15fr .85fr; gap: 48px; align-items: center; }
    .hero-eyebrow {
      display: inline-block; font-size: 12px; font-weight: 800; letter-spacing: 1.6px;
      text-transform: uppercase; color: var(--brand); margin-bottom: 18px;
    }
    .hero h1 { font-size: 46px; line-height: 1.15; font-weight: 900; letter-spacing: -1px; margin-bottom: 20px; }
    .hero p.lead { font-size: 17px; color: rgba(255,255,255,.78); margin-bottom: 34px; max-width: 520px; }
    .hero-actions { display: flex; gap: 14px; flex-wrap: wrap; }
    .hero-card {
      background: #fff; border-radius: 18px; padding: 28px;
      box-shadow: 0 24px 50px rgba(0,0,0,.25);
    }
    .hero-card .dummy {
      height: 190px; border-radius: 12px;
      background: linear-gradient(135deg, var(--brand) 0%, var(--brand-dark) 120%);
      display: flex; align-items: center; justify-content: center;
    }
    .hero-card .dummy span { font-size: 58px; }
    @media (max-width: 900px) { .hero .container { grid-template-columns: 1fr; } .hero h1 { font-size: 36px; } }

    /* ===== SECTION UMUM ===== */
    section { padding: 88px 0; }
    .section-head { text-align: center; max-width: 640px; margin: 0 auto 56px; }
    .section-eyebrow {
      font-size: 12px; font-weight: 800; letter-spacing: 1.6px; text-transform: uppercase;
      color: var(--brand); margin-bottom: 12px;
    }
    .section-head h2 { font-size: 32px; font-weight: 900; letter-spacing: -.6px; color: var(--brand-dark); }
    .section-head p { color: #6b7280; font-size: 15.5px; margin-top: 10px; }

    /* ===== ABOUT ===== */
    .about { background: #f8fafc; }
    .about-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 48px; align-items: center; }
    .about h3 { font-size: 26px; font-weight: 900; color: var(--brand-dark); margin-bottom: 16px; }
    .about p { color: #4b5563; }
    .about-visual {
      background: var(--brand); border-radius: 20px; height: 260px;
      display: flex; align-items: center; justify-content: center; color: #fff;
    }
    .about-visual span { font-size: 84px; opacity: .9; }
    @media (max-width: 900px) { .about-grid { grid-template-columns: 1fr; } }

    /* ===== FEATURES ===== */
    .features-grid {
      display: grid; grid-template-columns: repeat(3, 1fr); gap: 22px;
    }
    .feature-card {
      background: #fff; border: 1px solid #eef2f6; border-radius: 16px; padding: 26px;
      transition: transform .2s ease, box-shadow .2s ease;
    }
    .feature-card:hover { transform: translateY(-4px); box-shadow: 0 16px 34px rgba(17,24,39,.08); }
    .feature-card .ic {
      width: 46px; height: 46px; border-radius: 12px;
      background: {{ $primary }}1a; color: var(--brand);
      display: flex; align-items: center; justify-content: center;
      font-size: 21px; font-weight: 900; margin-bottom: 16px;
    }
    .feature-card h4 { font-size: 16.5px; font-weight: 800; color: var(--brand-dark); margin-bottom: 6px; }
    .feature-card p { font-size: 13.8px; color: #6b7280; }
    .pill-tag {
      background: {{ $primary }}12; color: var(--brand); border: 1px solid {{ $primary }}33;
      font-weight: 700; font-size: 13px; border-radius: 999px; padding: 10px 22px; display: inline-block;
    }
    @media (max-width: 900px) { .features-grid { grid-template-columns: 1fr; } }

    /* ===== STATS ===== */
    .stats-band { background: var(--brand-dark); color: #fff; padding: 64px 0; }
    .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px; text-align: center; }
    .stats-grid .num { font-size: 40px; font-weight: 900; color: var(--brand); }
    .stats-grid .lbl { color: rgba(255,255,255,.75); font-size: 14px; margin-top: 4px; }
    @media (max-width: 900px) { .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 40px; } }

    /* ===== CONTACT ===== */
    .contact { background: #f8fafc; }
    .contact-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 22px; }
    .contact-card {
      background: #fff; border-radius: 16px; padding: 24px; text-align: center;
      border: 1px solid #eef2f6;
    }
    .contact-card .ic {
      width: 48px; height: 48px; margin: 0 auto 14px; border-radius: 50%;
      background: {{ $primary }}1a; color: var(--brand);
      display: flex; align-items: center; justify-content: center; font-size: 20px;
    }
    .contact-card h5 { font-size: 14px; font-weight: 800; color: var(--brand-dark); text-transform: uppercase; letter-spacing: .5px; }
    .contact-card p, .contact-card a { font-size: 14px; color: #4b5563; margin-top: 4px; display: block; }
    .contact-card a:hover { color: var(--brand); }
    @media (max-width: 900px) { .contact-grid { grid-template-columns: 1fr; } }

    /* ===== CTA ===== */
    .cta-band { text-align: center; padding: 84px 0; }
    .cta-band h2 { font-size: 30px; font-weight: 900; color: var(--brand-dark); margin-bottom: 12px; }
    .cta-band p { color: #6b7280; margin-bottom: 30px; }

    /* ===== FOOTER ===== */
    footer { background: var(--brand-dark); color: rgba(255,255,255,.7); padding: 40px 0; text-align: center; }
    footer .brand-name { display: block; margin-bottom: 6px; }
    footer small { font-size: 13px; }
    footer .powered { margin-top: 14px; font-size: 12px; color: rgba(255,255,255,.45); }
  </style>
</head>
<body>

  <!-- NAVBAR -->
  <nav class="site-nav">
    <div class="container">
      <a href="{{ $ctaUrl }}" class="brand-name">{{ $tenant->name }}<span>.</span></a>
      <div class="nav-links">
        @if(in_array('about', $sections))<a href="#tentang">Tentang</a>@endif
        @if($showStats || in_array('features', $sections))<a href="#fitur">Fasilitas</a>@endif
        @if(in_array('contact', $sections))<a href="#kontak">Kontak</a>@endif
        <a href="{{ url('/login') }}" class="nav-login">Masuk / Daftar</a>
      </div>
    </div>
  </nav>

  <!-- HERO -->
  <header class="hero" id="beranda">
    <div class="container">
      <div>
        <span class="hero-eyebrow">Gym &amp; Fitness Center</span>
        <h1>{{ $heroTitle }}</h1>
        <p class="lead">{{ $settings->hero_tagline ?: 'Raih kebugaran terbaik Anda bersama tim profesional kami.' }}</p>
        <div class="hero-actions">
          <a href="{{ $ctaUrl }}" class="btn btn-brand">{{ $ctaText }}</a>
          @if(in_array('contact', $sections))<a href="#kontak" class="btn btn-ghost">Hubungi Kami</a>@endif
        </div>
      </div>
      <div class="hero-card">
        <div class="dummy"><span class="icon-heartbeat" style="color:#fff;"></span></div>
      </div>
    </div>
  </header>

  <!-- ABOUT -->
  @if(in_array('about', $sections))
  <section class="about" id="tentang">
    <div class="container">
      <div class="about-grid">
        <div>
          <span class="section-eyebrow">Tentang Kami</span>
          <h3>{{ $settings->about_text ? 'Kenali ' . $tenant->name : 'Selamat Datang di ' . $tenant->name }}</h3>
          <p>{{ $settings->about_text ?: $tenant->name . ' adalah pusat kebugaran yang siap menemani perjalanan kesehatan Anda.' }}</p>
        </div>
        <div class="about-visual"><span class="icon-users" style="color:#fff;"></span></div>
      </div>
    </div>
  </section>
  @endif

  <!-- FEATURES -->
  @if(in_array('features', $sections))
  <section id="fitur">
    <div class="container">
      <div class="section-head">
        <span class="section-eyebrow">Fasilitas</span>
        <h2>Kenapa Memilih {{ $tenant->name }}?</h2>
        <p>Fasilitas dan layanan unggulan yang sudah termasuk dalam keanggotaan Anda.</p>
      </div>
      @if($features)
      <div class="features-grid">
        @foreach($features as $f)
        <div class="feature-card">
          <div class="ic"><span class="icon-check-circle"></span></div>
          <h4>{{ $f['label'] }}</h4>
          <p>{{ $f['description'] }}</p>
        </div>
        @endforeach
      </div>
      @else
      <div class="text-center">
        @foreach($tenantFeatures as $tf)
          <span class="pill-tag" style="margin: 0 6px 10px 0;">{{ $tf }}</span>
        @endforeach
      </div>
      @endif
    </div>
  </section>
  @endif

  <!-- STATS -->
  @if($showStats)
  <div class="stats-band">
    <div class="container">
      <div class="stats-grid">
        @foreach($stats as $s)
        <div>
          <div class="num">{{ $s['label'] }}</div>
          <div class="lbl">{{ $s['description'] }}</div>
        </div>
        @endforeach
      </div>
    </div>
  </div>
  @endif

  <!-- CTA BAND -->
  <div class="cta-band">
    <div class="container">
      <h2>Siap Memulai Perjalanan Kebugaran Anda?</h2>
      <p>Bergabunglah dengan {{ $tenant->name }} dan rasakan perbedaannya mulai hari ini.</p>
      <a href="{{ $ctaUrl }}" class="btn btn-brand">{{ $ctaText }}</a>
    </div>
  </div>

  <!-- CONTACT -->
  @if(in_array('contact', $sections))
  <section class="contact" id="kontak">
    <div class="container">
      <div class="section-head">
        <span class="section-eyebrow">Kontak</span>
        <h2>Hubungi Kami</h2>
        <p>Kunjungi kami atau hubungi tim {{ $tenant->name }} untuk informasi lebih lanjut.</p>
      </div>
      <div class="contact-grid">
        <div class="contact-card">
          <div class="ic"><span class="icon-map-marker"></span></div>
          <h5>Alamat</h5>
          <p>{{ $settings->address ?: 'Jl. Kebugaran No. 1' }}</p>
        </div>
        <div class="contact-card">
          <div class="ic"><span class="icon-phone"></span></div>
          <h5>Telepon / Email</h5>
          <a href="tel:{{ $settings->phone }}">{{ $settings->phone ?: '- / -' }}</a>
          @if($settings->email)<a href="mailto:{{ $settings->email }}">{{ $settings->email }}</a>@endif
        </div>
        <div class="contact-card">
          <div class="ic"><span class="icon-clock-o"></span></div>
          <h5>Jam Operasional</h5>
          <p>{{ $settings->opening_hours ?: 'Senin - Minggu: 06.00 - 22.00' }}</p>
        </div>
      </div>
      @if($settings->instagram || $settings->facebook)
      <p class="text-center" style="margin-top: 32px;">
        @if($settings->instagram)<a href="{{ $settings->instagram }}" style="color:#4b5563; margin: 0 10px;">Instagram</a>@endif
        @if($settings->facebook)<a href="{{ $settings->facebook }}" style="color:#4b5563; margin: 0 10px;">Facebook</a>@endif
      </p>
      @endif
    </div>
  </section>
  @endif

  <!-- FOOTER -->
  <footer>
    <span class="brand-name">{{ $tenant->name }}<span style="color: var(--brand);">.</span></span>
    <small>&copy; {{ date('Y') }} {{ $tenant->name }}. Seluruh hak cipta dilindungi.</small>
    <div class="powered">Powered by PetGym SaaS — Platform Manajemen Gym</div>
  </footer>

</body>
</html>
