@php
  $primary   = $settings->primary_color ?: '#f43f5e';
  $secondary = $settings->secondary_color ?: '#0f172a';
  $sections  = is_array($settings->sections_enabled) ? $settings->sections_enabled : [];
  $showStats = in_array('stats', $sections) && !empty($settings->stats);
  $stats     = is_array($settings->stats) ? $settings->stats : [];
  $features  = is_array($settings->features) && count($settings->features) ? $settings->features : null;
  $tenantFeatures = is_array($tenant->features) ? $tenant->features : [];
  $heroTitle = $settings->hero_title ?: $tenant->name;
  $ctaText   = $settings->cta_text ?: 'Daftar Sekarang';
  $ctaUrl    = $settings->cta_url ?: route('tenant.register', ['slug' => $tenant->slug]);

  // Convert hex to rgb for alpha channels
  $cleanHex = ltrim($primary, '#');
  if (strlen($cleanHex) === 3) {
    $cleanHex = $cleanHex[0].$cleanHex[0].$cleanHex[1].$cleanHex[1].$cleanHex[2].$cleanHex[2];
  }
  $r = hexdec(substr($cleanHex, 0, 2));
  $g = hexdec(substr($cleanHex, 2, 2));
  $b = hexdec(substr($cleanHex, 4, 2));
  $brandRgb = "{$r}, {$g}, {$b}";

  // Assign authentic gym photography (custom uploaded photos take priority, with fallback to default)
  $isPowerhouse = ($tenant->slug === 'powerhouse');
  $defaultHeroBg  = $isPowerhouse ? asset('images/bg_2.jpg') : asset('images/bg_1.jpg');
  $defaultAboutImg = $isPowerhouse ? asset('images/img_1.jpg') : asset('images/img_2.jpg');

  $defaultCardImg = $isPowerhouse ? asset('images/img_3.jpg') : asset('images/img_4.jpg');
  $heroBgImage    = !empty($settings->hero_image) ? asset($settings->hero_image) : $defaultHeroBg;
  $aboutImage     = !empty($settings->about_image) ? asset($settings->about_image) : $defaultAboutImg;
  $cardHeroImg    = !empty($settings->hero_card_image) ? asset($settings->hero_card_image) : $defaultCardImg;
  $ctaBgImage     = asset('images/bg_3.jpg');

  $facilityPhotos = [
    asset('images/img_1.jpg'),
    asset('images/img_2.jpg'),
    asset('images/img_3.jpg'),
    asset('images/img_4.jpg'),
    asset('images/img_5.jpg'),
  ];
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>{{ $tenant->name }}: {{ $settings->hero_tagline ?: 'Pusat Kebugaran & Gym' }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --brand: {{ $primary }};
      --brand-rgb: {{ $brandRgb }};
      --brand-dark: {{ $secondary }};
      --surface-dark: #0b1120;
      --surface-card: #ffffff;
      --surface-subtle: #f8fafc;
      --on-surface: #0f172a;
      --on-surface-variant: #64748b;
      --outline-variant: rgba(15, 23, 42, 0.08);
      --outline-dark: rgba(255, 255, 255, 0.12);
      --radius-default: 6px;
      --radius-md: 8px;
      --radius-lg: 12px;
      --radius-full: 999px;
      --shadow-sm: 0 1px 3px rgba(15, 23, 42, 0.06);
      --shadow-md: 0 6px 20px rgba(15, 23, 42, 0.08);
      --transition-base: 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    html {
      scroll-behavior: smooth;
    }

    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      color: var(--on-surface);
      background-color: #ffffff;
      line-height: 1.6;
      -webkit-font-smoothing: antialiased;
    }

    a {
      text-decoration: none;
      color: inherit;
    }

    .container {
      max-width: 1240px;
      margin: 0 auto;
      padding: 0 clamp(20px, 4vw, 48px);
    }

    /* Action Buttons */
    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      height: 44px;
      padding: 0 24px;
      border-radius: var(--radius-default);
      font-size: 14.5px;
      font-weight: 600;
      letter-spacing: 0.01em;
      cursor: pointer;
      border: 1px solid transparent;
      transition: background-color var(--transition-base), border-color var(--transition-base), color var(--transition-base), transform var(--transition-base);
      white-space: nowrap;
    }

    .btn:active {
      transform: translateY(1px);
    }

    .btn-brand {
      background-color: var(--brand);
      color: #ffffff;
      border-color: var(--brand);
    }

    .btn-brand:hover {
      filter: brightness(0.92);
      color: #ffffff;
    }

    .btn-dark {
      background-color: var(--brand-dark);
      color: #ffffff;
      border-color: var(--brand-dark);
    }

    .btn-dark:hover {
      background-color: #000000;
      border-color: #000000;
      color: #ffffff;
    }

    .btn-outline-white {
      background-color: rgba(255, 255, 255, 0.08);
      color: #ffffff;
      border-color: rgba(255, 255, 255, 0.28);
      backdrop-filter: blur(6px);
    }

    .btn-outline-white:hover {
      background-color: rgba(255, 255, 255, 0.18);
      border-color: #ffffff;
      color: #ffffff;
    }

    .btn-secondary {
      background-color: transparent;
      color: var(--on-surface);
      border-color: rgba(15, 23, 42, 0.2);
    }

    .btn-secondary:hover {
      border-color: var(--on-surface);
      background-color: var(--surface-subtle);
    }

    /* Badges & Chips */
    .chip {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 4px 10px;
      border-radius: var(--radius-default);
      font-size: 12px;
      font-weight: 500;
      letter-spacing: 0.02em;
    }

    .chip-white {
      background-color: rgba(255, 255, 255, 0.12);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: #ffffff;
      backdrop-filter: blur(8px);
    }

    .chip-brand {
      background-color: rgba(var(--brand-rgb), 0.1);
      border: 1px solid rgba(var(--brand-rgb), 0.25);
      color: var(--brand);
    }

    .chip-dot {
      width: 6px;
      height: 6px;
      border-radius: var(--radius-full);
      background-color: var(--brand);
    }

    /* Navigation */
    .site-nav {
      position: sticky;
      top: 0;
      z-index: 100;
      background-color: rgba(11, 17, 32, 0.95);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border-bottom: 1px solid var(--outline-dark);
      transition: border-color var(--transition-base);
    }

    .site-nav .container {
      display: flex;
      align-items: center;
      justify-content: space-between;
      height: 68px;
    }

    .brand-link {
      display: flex;
      align-items: center;
      gap: 12px;
      font-size: 18px;
      font-weight: 700;
      letter-spacing: -0.02em;
      color: #ffffff;
    }

    .brand-link img {
      max-height: 38px;
      max-width: 160px;
      object-fit: contain;
    }

    .brand-link .brand-dot {
      color: var(--brand);
    }

    .nav-links {
      display: flex;
      align-items: center;
      gap: 24px;
    }

    .nav-links a {
      font-size: 14.5px;
      font-weight: 500;
      color: rgba(255, 255, 255, 0.78);
      transition: color var(--transition-base);
    }

    .nav-links a:hover {
      color: #ffffff;
    }

    .nav-actions {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .nav-toggle {
      display: none;
      background: transparent;
      border: 1px solid var(--outline-dark);
      border-radius: var(--radius-default);
      width: 38px;
      height: 38px;
      align-items: center;
      justify-content: center;
      color: #ffffff;
      cursor: pointer;
    }

    /* Hero Section with Fitness Photography */
    .hero {
      position: relative;
      background: linear-gradient(180deg, rgba(11, 17, 32, 0.82) 0%, rgba(11, 17, 32, 0.94) 100%), url('{{ $heroBgImage }}') center/cover no-repeat;
      padding: clamp(64px, 8vw, 110px) 0 clamp(64px, 8vw, 100px);
      color: #ffffff;
      border-bottom: 1px solid var(--outline-dark);
    }

    .hero-grid {
      display: grid;
      grid-template-columns: 1.15fr 0.85fr;
      gap: clamp(36px, 5vw, 64px);
      align-items: center;
    }

    .hero-content {
      max-width: 600px;
    }

    .hero-content .chip {
      margin-bottom: 22px;
    }

    .hero h1 {
      font-size: clamp(38px, 5vw, 62px);
      font-weight: 300;
      line-height: 1.1;
      letter-spacing: -0.025em;
      color: #ffffff;
      margin-bottom: 20px;
    }

    .hero h1 strong {
      font-weight: 700;
      color: #ffffff;
    }

    .hero p.lead {
      font-size: 16.5px;
      font-weight: 400;
      line-height: 1.65;
      color: rgba(255, 255, 255, 0.82);
      margin-bottom: 34px;
    }

    .hero-buttons {
      display: flex;
      align-items: center;
      gap: 14px;
      flex-wrap: wrap;
    }

    /* Hero Live Facility Card */
    .hero-card {
      background-color: #ffffff;
      color: var(--on-surface);
      border-radius: var(--radius-lg);
      overflow: hidden;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.35);
      border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .hero-card-media {
      position: relative;
      height: 190px;
      background-image: url('{{ $cardHeroImg }}');
      background-size: cover;
      background-position: center;
    }

    .hero-card-media-overlay {
      position: absolute;
      inset: 0;
      background: linear-gradient(180deg, rgba(0,0,0,0.15) 0%, rgba(11, 17, 32, 0.85) 100%);
      display: flex;
      align-items: flex-end;
      padding: 16px;
    }

    .hero-card-media-title {
      color: #ffffff;
      font-size: 15px;
      font-weight: 600;
    }

    .hero-card-media-badge {
      position: absolute;
      top: 14px;
      left: 14px;
      display: flex;
      align-items: center;
      gap: 6px;
      background-color: rgba(16, 185, 129, 0.9);
      color: #ffffff;
      font-size: 11.5px;
      font-weight: 600;
      padding: 3px 9px;
      border-radius: var(--radius-default);
    }

    .live-dot {
      width: 6px;
      height: 6px;
      border-radius: var(--radius-full);
      background-color: #ffffff;
      animation: pulse 1.8s infinite;
    }

    @keyframes pulse {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.4; }
    }

    .hero-card-content {
      padding: 20px;
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .hero-card-item {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 10px 12px;
      background-color: var(--surface-subtle);
      border: 1px solid var(--outline-variant);
      border-radius: var(--radius-default);
    }

    .hero-card-label {
      font-size: 13.5px;
      font-weight: 500;
      color: var(--on-surface);
    }

    .hero-card-value {
      font-size: 13px;
      font-weight: 600;
      color: var(--brand);
    }

    /* Common Section Elements */
    section {
      padding: clamp(64px, 7vw, 100px) 0;
      border-bottom: 1px solid var(--outline-variant);
    }

    .section-header {
      margin-bottom: clamp(36px, 4vw, 56px);
      max-width: 680px;
    }

    .section-eyebrow {
      display: inline-block;
      font-size: 12px;
      font-weight: 600;
      letter-spacing: 0.05em;
      text-transform: uppercase;
      color: var(--brand);
      margin-bottom: 12px;
    }

    .section-title {
      font-size: clamp(28px, 3.5vw, 40px);
      font-weight: 300;
      line-height: 1.2;
      letter-spacing: -0.02em;
      color: var(--on-surface);
      margin-bottom: 12px;
    }

    .section-desc {
      font-size: 15.5px;
      line-height: 1.6;
      color: var(--on-surface-variant);
    }

    /* About Section with Photo Split */
    .about-section {
      background-color: #ffffff;
    }

    .about-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: clamp(36px, 5vw, 64px);
      align-items: center;
    }

    .about-media {
      position: relative;
      border-radius: var(--radius-lg);
      overflow: hidden;
      box-shadow: var(--shadow-md);
      aspect-ratio: 4 / 3;
    }

    .about-media img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
      transition: transform 0.5s ease;
    }

    .about-media:hover img {
      transform: scale(1.03);
    }

    .about-badge-card {
      position: absolute;
      bottom: 20px;
      left: 20px;
      right: 20px;
      background-color: rgba(11, 17, 32, 0.88);
      backdrop-filter: blur(8px);
      color: #ffffff;
      padding: 16px 20px;
      border-radius: var(--radius-md);
      border: 1px solid rgba(255, 255, 255, 0.15);
    }

    .about-badge-title {
      font-size: 14.5px;
      font-weight: 600;
      margin-bottom: 2px;
    }

    .about-badge-desc {
      font-size: 12.5px;
      color: rgba(255, 255, 255, 0.75);
    }

    .about-checklist {
      list-style: none;
      display: flex;
      flex-direction: column;
      gap: 16px;
      margin: 28px 0;
    }

    .about-check-item {
      display: flex;
      align-items: flex-start;
      gap: 12px;
      font-size: 14.5px;
      color: var(--on-surface);
    }

    .check-icon {
      width: 22px;
      height: 22px;
      border-radius: var(--radius-full);
      background-color: rgba(var(--brand-rgb), 0.12);
      color: var(--brand);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      margin-top: 2px;
    }

    /* Facility & Services Grid with Photo Cards */
    .features-section {
      background-color: var(--surface-subtle);
    }

    .features-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 24px;
    }

    .feature-card {
      background-color: #ffffff;
      border: 1px solid var(--outline-variant);
      border-radius: var(--radius-lg);
      overflow: hidden;
      box-shadow: var(--shadow-sm);
      transition: transform var(--transition-base), box-shadow var(--transition-base);
    }

    .feature-card:hover {
      transform: translateY(-4px);
      box-shadow: var(--shadow-md);
    }

    .feature-card-media {
      height: 180px;
      position: relative;
      overflow: hidden;
      background-color: #0f172a;
    }

    .feature-card-media img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.4s ease;
    }

    .feature-card:hover .feature-card-media img {
      transform: scale(1.05);
    }

    .feature-card-tag {
      position: absolute;
      top: 12px;
      left: 12px;
      background-color: rgba(11, 17, 32, 0.75);
      backdrop-filter: blur(4px);
      color: #ffffff;
      font-size: 11.5px;
      font-weight: 500;
      padding: 3px 8px;
      border-radius: var(--radius-default);
    }

    .feature-card-body {
      padding: 22px;
    }

    .feature-card-body h4 {
      font-size: 17px;
      font-weight: 600;
      letter-spacing: -0.01em;
      color: var(--on-surface);
      margin-bottom: 8px;
    }

    .feature-card-body p {
      font-size: 14px;
      line-height: 1.55;
      color: var(--on-surface-variant);
    }

    /* Performance Stats Band */
    .stats-band {
      background-color: var(--brand-dark);
      color: #ffffff;
      padding: clamp(48px, 5vw, 68px) 0;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .stats-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 28px;
    }

    .stat-item {
      padding-left: 16px;
      border-left: 2px solid var(--brand);
    }

    .stat-num {
      font-size: clamp(34px, 4vw, 48px);
      font-weight: 300;
      line-height: 1.1;
      letter-spacing: -0.03em;
      color: #ffffff;
      margin-bottom: 6px;
    }

    .stat-lbl {
      font-size: 13.5px;
      font-weight: 500;
      color: rgba(255, 255, 255, 0.75);
      letter-spacing: 0.01em;
    }

    /* Contact Section */
    .contact-section {
      background-color: #ffffff;
    }

    .contact-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 24px;
    }

    .contact-card {
      background-color: var(--surface-subtle);
      border: 1px solid var(--outline-variant);
      border-radius: var(--radius-lg);
      padding: 28px;
    }

    .contact-icon-box {
      width: 40px;
      height: 40px;
      border-radius: var(--radius-default);
      background-color: rgba(var(--brand-rgb), 0.1);
      color: var(--brand);
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 18px;
    }

    .contact-card h5 {
      font-size: 15px;
      font-weight: 600;
      color: var(--on-surface);
      margin-bottom: 8px;
    }

    .contact-card p,
    .contact-card a {
      font-size: 14px;
      color: var(--on-surface-variant);
      line-height: 1.55;
    }

    .contact-card a {
      transition: color var(--transition-base);
    }

    .contact-card a:hover {
      color: var(--brand);
    }

    .social-row {
      display: flex;
      gap: 10px;
      margin-top: 24px;
    }

    /* Immersive Gym CTA Banner */
    .cta-banner {
      position: relative;
      background: linear-gradient(180deg, rgba(11, 17, 32, 0.88) 0%, rgba(11, 17, 32, 0.94) 100%), url('{{ $ctaBgImage }}') center/cover no-repeat;
      padding: clamp(70px, 8vw, 100px) 0;
      color: #ffffff;
      text-align: center;
    }

    .cta-wrapper {
      max-width: 660px;
      margin: 0 auto;
    }

    .cta-banner h2 {
      font-size: clamp(30px, 4vw, 46px);
      font-weight: 300;
      letter-spacing: -0.02em;
      color: #ffffff;
      margin-bottom: 16px;
    }

    .cta-banner p {
      font-size: 16.5px;
      color: rgba(255, 255, 255, 0.82);
      margin-bottom: 32px;
    }

    .cta-action-row {
      display: flex;
      justify-content: center;
      gap: 14px;
      flex-wrap: wrap;
    }

    /* Modern Dark Footer */
    footer {
      background-color: var(--surface-dark);
      padding: 40px 0;
      font-size: 13.5px;
      color: rgba(255, 255, 255, 0.6);
      border-top: 1px solid var(--outline-dark);
    }

    footer .container {
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 16px;
    }

    footer .brand-footer {
      font-weight: 600;
      color: #ffffff;
    }

    footer .footer-note {
      font-size: 12.5px;
      color: rgba(255, 255, 255, 0.4);
    }

    /* Responsive Queries */
    @media (max-width: 900px) {
      .hero-grid {
        grid-template-columns: 1fr;
      }
      .about-grid {
        grid-template-columns: 1fr;
      }
      .stats-grid {
        grid-template-columns: repeat(2, 1fr);
      }
      .contact-grid {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 640px) {
      .nav-links {
        display: none;
      }
      .nav-links.active {
        display: flex;
        flex-direction: column;
        position: absolute;
        top: 68px;
        left: 0;
        right: 0;
        background-color: var(--surface-dark);
        padding: 20px;
        border-bottom: 1px solid var(--outline-dark);
      }
      .nav-toggle {
        display: flex;
      }
      .stats-grid {
        grid-template-columns: 1fr;
      }
      footer .container {
        flex-direction: column;
        text-align: center;
      }
    }
  </style>
</head>
<body>

  <!-- SITE NAVIGATION -->
  <nav class="site-nav">
    <div class="container">
      <a href="{{ $tenant->publicLandingUrl() }}" onclick="if(window.location.pathname === '/' || window.location.pathname === ''){ window.scrollTo({top: 0, behavior: 'smooth'}); if(history.replaceState){ history.replaceState(null, null, window.location.pathname); } return false; }" class="brand-link">
        @php
          $displayMode = $settings->brand_display_mode ?? 'both';
          $hasLogo = !empty($tenant->logo_url);
        @endphp
        @if(($displayMode === 'logo' || $displayMode === 'both') && $hasLogo)
          <img src="{{ asset($tenant->logo_url) }}?v={{ time() }}" alt="{{ $tenant->name }}">
        @endif
        @if($displayMode === 'text' || ($displayMode === 'both' && !$hasLogo) || ($displayMode === 'logo' && !$hasLogo))
          <span>{{ $tenant->name }}<span class="brand-dot">.</span></span>
        @elseif($displayMode === 'both' && $hasLogo)
          <span>{{ $tenant->name }}</span>
        @endif
      </a>

      <div class="nav-links" id="navLinks">
        @if(in_array('about', $sections))
          <a href="#tentang">Tentang</a>
        @endif
        @if($showStats || in_array('features', $sections))
          <a href="#fitur">Fasilitas</a>
        @endif
        @if(in_array('contact', $sections))
          <a href="#kontak">Kontak</a>
        @endif
      </div>

      <div class="nav-actions">
        <a href="{{ route('tenant.login', ['slug' => $tenant->slug]) }}" class="btn btn-outline-white">
          Masuk Member
        </a>
        <a href="{{ route('tenant.register', ['slug' => $tenant->slug]) }}" class="btn btn-brand">
          Daftar Member
        </a>
        <button class="nav-toggle" id="navToggle" aria-label="Buka Menu Navigasi" onclick="document.getElementById('navLinks').classList.toggle('active')">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="3" y1="12" x2="21" y2="12"></line>
            <line x1="3" y1="6" x2="21" y2="6"></line>
            <line x1="3" y1="18" x2="21" y2="18"></line>
          </svg>
        </button>
      </div>
    </div>
  </nav>

  <!-- HERO SECTION WITH GYM PHOTOGRAPHY -->
  <header class="hero" id="beranda">
    <div class="container">
      <div class="hero-grid">
        <div class="hero-content">
          <div class="chip chip-white">
            <span class="chip-dot"></span>
            <span>Studio Kebugaran & Keanggotaan Terpadu</span>
          </div>
          <h1>{{ $heroTitle }}</h1>
          <p class="lead">{{ $settings->hero_tagline ?: 'Fasilitas latihan modern, bimbingan trainer berdedikasi, dan lingkungan latihan terukur untuk hasil fisik nyata.' }}</p>
          <div class="hero-buttons">
            <a href="{{ $ctaUrl }}" class="btn btn-brand">{{ $ctaText }}</a>
            @if(in_array('contact', $sections))
              <a href="#kontak" class="btn btn-outline-white">Informasi Studio</a>
            @endif
          </div>
        </div>

        <!-- LIVE FACILITY HIGHLIGHT CARD -->
        <div class="hero-card">
          <div class="hero-card-media">
            <div class="hero-card-media-badge">
              <span class="live-dot"></span>
              <span>Buka Sekarang</span>
            </div>
            <div class="hero-card-media-overlay">
              <div class="hero-card-media-title">{{ $tenant->name }} Fitness Center</div>
            </div>
          </div>
          <div class="hero-card-content">
            <div class="hero-card-item">
              <span class="hero-card-label">Jam Operasional Hari Ini</span>
              <span class="hero-card-value">{{ $settings->opening_hours ?: '06.00 - 22.00 WIB' }}</span>
            </div>
            <div class="hero-card-item">
              <span class="hero-card-label">Akses Latihan</span>
              <span class="hero-card-value">Cardio, Free Weights, Functional</span>
            </div>
            <div class="hero-card-item">
              <span class="hero-card-label">Pendampingan Instruktur</span>
              <span class="hero-card-value">Tersedia di Lokasi</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>

  <!-- ABOUT SECTION WITH REAL TRAINING PHOTO -->
  @if(in_array('about', $sections))
  <section class="about-section" id="tentang">
    <div class="container">
      <div class="about-grid">
        <div class="about-media">
          <img src="{{ $aboutImage }}" alt="Latihan Kebugaran di {{ $tenant->name }}" loading="lazy">
          <div class="about-badge-card">
            <div class="about-badge-title">Standar Kebersihan & Peralatan Berkualitas</div>
            <div class="about-badge-desc">Perawatan alat berkala dan sanitasi higienis untuk kenyamanan latihan Anda.</div>
          </div>
        </div>

        <div>
          <span class="section-eyebrow">Profil & Filosofi</span>
          <h2 class="section-title">{{ $settings->about_text ? 'Tentang ' . $tenant->name : 'Mengenal ' . $tenant->name }}</h2>
          <p class="section-desc">
            {{ $settings->about_text ?: $tenant->name . ' hadir untuk memberikan ruang latihan yang kondusif dengan peralatan terawat dan instruktur yang siap mendampingi target kesehatan Anda secara terukur.' }}
          </p>

          <ul class="about-checklist">
            <li class="about-check-item">
              <div class="check-icon">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
              </div>
              <div>
                <strong>Zona Latihan Terbagi Rapi:</strong> Area kardio, free weights, rack beban, dan zona peregangan terpisah nyaman.
              </div>
            </li>
            <li class="about-check-item">
              <div class="check-icon">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
              </div>
              <div>
                <strong>Sistem Check-in Praktis:</strong> Kehadiran tercatat otomatis via portal member digital tanpa kartu fisik.
              </div>
            </li>
            <li class="about-check-item">
              <div class="check-icon">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
              </div>
              <div>
                <strong>Komunitas Positif:</strong> Atmosfer olahraga suportif untuk pemula maupun atlet berpengalaman.
              </div>
            </li>
          </ul>

          <a href="{{ route('tenant.register', ['slug' => $tenant->slug]) }}" class="btn btn-dark">
            Mulai Keanggotaan
          </a>
        </div>
      </div>
    </div>
  </section>
  @endif

  <!-- FACILITIES & SERVICES WITH ATHLETE PHOTOGRAPHY -->
  @if(in_array('features', $sections))
  <section class="features-section" id="fitur">
    <div class="container">
      <div class="section-header">
        <span class="section-eyebrow">Fasilitas Unggulan</span>
        <h2 class="section-title">Fasilitas Lengkap untuk Hasil Maksimal</h2>
        <p class="section-desc">Nikmati sarana olahraga lengkap yang sudah termasuk dalam paket keanggotaan Anda di {{ $tenant->name }}.</p>
      </div>

      @if($features)
      <div class="features-grid">
        @foreach($features as $index => $f)
        @php
          $photoUrl = !empty($f['image']) ? asset($f['image']) : $facilityPhotos[$index % count($facilityPhotos)];
        @endphp
        <div class="feature-card">
          <div class="feature-card-media">
            <img src="{{ $photoUrl }}" alt="{{ $f['label'] }}" loading="lazy">
            <span class="feature-card-tag">Fasilitas Terverifikasi</span>
          </div>
          <div class="feature-card-body">
            <h4>{{ $f['label'] }}</h4>
            <p>{{ $f['description'] }}</p>
          </div>
        </div>
        @endforeach
      </div>
      @else
      <div class="features-grid">
        @foreach($tenantFeatures as $index => $tf)
        @php
          $photoUrl = $facilityPhotos[$index % count($facilityPhotos)];
        @endphp
        <div class="feature-card">
          <div class="feature-card-media">
            <img src="{{ $photoUrl }}" alt="{{ $tf }}" loading="lazy">
            <span class="feature-card-tag">Program Unggulan</span>
          </div>
          <div class="feature-card-body">
            <h4>{{ ucfirst($tf) }}</h4>
            <p>Layanan berkualitas tinggi yang dirancang untuk mendukung performa latihan harian Anda.</p>
          </div>
        </div>
        @endforeach
      </div>
      @endif
    </div>
  </section>
  @endif

  <!-- STATS BAND -->
  @if($showStats)
  <div class="stats-band">
    <div class="container">
      <div class="stats-grid">
        @foreach($stats as $s)
        <div class="stat-item">
          <div class="stat-num">{{ $s['label'] }}</div>
          <div class="stat-lbl">{{ $s['description'] }}</div>
        </div>
        @endforeach
      </div>
    </div>
  </div>
  @endif

  <!-- CONTACT SECTION -->
  @if(in_array('contact', $sections))
  <section class="contact-section" id="kontak">
    <div class="container">
      <div class="section-header">
        <span class="section-eyebrow">Informasi Kontak</span>
        <h2 class="section-title">Kunjungi & Hubungi Kami</h2>
        <p class="section-desc">Punya pertanyaan seputar fasilitas atau ingin berkonsultasi? Tim resepsionis kami siap membantu Anda.</p>
      </div>

      <div class="contact-grid">
        <div class="contact-card">
          <div class="contact-icon-box">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
              <circle cx="12" cy="10" r="3"></circle>
            </svg>
          </div>
          <h5>Lokasi Studio</h5>
          <p>{{ $settings->address ?: 'Silakan hubungi administrator untuk detail alamat lengkap.' }}</p>
        </div>

        <div class="contact-card">
          <div class="contact-icon-box">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
            </svg>
          </div>
          <h5>Kontak Langsung</h5>
          <p>
            @if($settings->phone)
              <a href="tel:{{ $settings->phone }}">{{ $settings->phone }}</a><br>
            @endif
            @if($settings->email)
              <a href="mailto:{{ $settings->email }}">{{ $settings->email }}</a>
            @else
              <span>Tersedia langsung di resepsionis</span>
            @endif
          </p>
        </div>

        <div class="contact-card">
          <div class="contact-icon-box">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10"></circle>
              <polyline points="12 6 12 12 16 14"></polyline>
            </svg>
          </div>
          <h5>Jam Buka</h5>
          <p>{{ $settings->opening_hours ?: 'Setiap hari: 06.00 - 22.00 WIB' }}</p>
        </div>
      </div>

      @if($settings->instagram || $settings->facebook)
      <div class="social-row">
        @if($settings->instagram)
          <a href="{{ $settings->instagram }}" target="_blank" rel="noopener noreferrer" class="chip chip-brand">
            <span>Instagram: {{ '@' . basename(parse_url($settings->instagram, PHP_URL_PATH)) }}</span>
          </a>
        @endif
        @if($settings->facebook)
          <a href="{{ $settings->facebook }}" target="_blank" rel="noopener noreferrer" class="chip chip-brand">
            <span>Facebook Resmi</span>
          </a>
        @endif
      </div>
      @endif
    </div>
  </section>
  @endif

  <!-- CALL TO ACTION BANNER WITH ATHLETE BACKGROUND -->
  <div class="cta-banner">
    <div class="container">
      <div class="cta-wrapper">
        <h2>Mulai Langkah Sehat Anda Bersama {{ $tenant->name }}</h2>
        <p>Gabung bersama ratusan member lainnya yang telah membuktikan perubahan fisik dan kebugaran mereka.</p>
        <div class="cta-action-row">
          <a href="{{ route('tenant.register', ['slug' => $tenant->slug]) }}" class="btn btn-brand">Daftar Sekarang</a>
          <a href="{{ route('tenant.login', ['slug' => $tenant->slug]) }}" class="btn btn-outline-white">Masuk Portal Member</a>
        </div>
      </div>
    </div>
  </div>

  <!-- MODERN FOOTER -->
  <footer>
    <div class="container">
      <div>
        <span class="brand-footer">{{ $tenant->name }}</span>
        <span>: {{ $settings->hero_tagline ?: 'Pusat Kebugaran & Gym' }}</span>
      </div>
      <div>
        <small>&copy; {{ date('Y') }} {{ $tenant->name }}. Seluruh hak cipta dilindungi.</small>
      </div>
      <div class="footer-note">
        <span>Didukung oleh PetGym SaaS Platform</span>
      </div>
    </div>
  </footer>

</body>
</html>
