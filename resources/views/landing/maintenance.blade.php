@php
  $primary   = $settings->primary_color ?: '#f43f5e';
  $secondary = $settings->secondary_color ?: '#0f172a';
  $cleanHex = ltrim($primary, '#');
  if (strlen($cleanHex) === 3) {
    $cleanHex = $cleanHex[0].$cleanHex[0].$cleanHex[1].$cleanHex[1].$cleanHex[2].$cleanHex[2];
  }
  $r = hexdec(substr($cleanHex, 0, 2));
  $g = hexdec(substr($cleanHex, 2, 2));
  $b = hexdec(substr($cleanHex, 4, 2));
  $brandRgb = "{$r}, {$g}, {$b}";

  $displayMode = $settings->brand_display_mode ?? 'both';
  $hasLogo = !empty($tenant->logo_url);
  $authUser = Auth::user();
  $canPreview = $authUser && (
      ($authUser->tenant_id === $tenant->id && ($authUser->isAdmin() || $authUser->isOwner()))
      || (method_exists($authUser, 'isSuperadmin') && $authUser->isSuperadmin())
  );
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>{{ $settings->maintenance_title ?: 'Website Sedang Dalam Pemeliharaan' }} - {{ $tenant->name }}</title>
  <x-dynamic-favicon />
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
      --radius-default: 8px;
      --radius-lg: 16px;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      background-color: #0b1120;
      color: #ffffff;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      position: relative;
      overflow-x: hidden;
    }

    /* Ambient Glow Background */
    .bg-glow {
      position: absolute;
      top: -200px;
      left: 50%;
      transform: translateX(-50%);
      width: 800px;
      height: 600px;
      background: radial-gradient(circle, rgba(var(--brand-rgb), 0.18) 0%, rgba(11, 17, 32, 0) 70%);
      pointer-events: none;
      z-index: 0;
    }

    .container {
      max-width: 900px;
      margin: 0 auto;
      padding: 0 24px;
      position: relative;
      z-index: 1;
    }

    /* Header Nav */
    .header-bar {
      padding: 24px 0;
      border-bottom: 1px solid var(--outline-dark);
      background: rgba(11, 17, 32, 0.8);
      backdrop-filter: blur(10px);
    }

    .brand-link {
      display: inline-flex;
      align-items: center;
      gap: 12px;
      font-size: 20px;
      font-weight: 700;
      color: #ffffff;
      text-decoration: none;
    }

    .brand-link img {
      max-height: 42px;
      max-width: 160px;
      object-fit: contain;
    }

    .brand-dot {
      color: var(--brand);
    }

    /* Main Content */
    .main-wrapper {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 60px 0;
    }

    .maintenance-card {
      background: rgba(15, 23, 42, 0.75);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: var(--radius-lg);
      padding: clamp(32px, 5vw, 48px);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
      backdrop-filter: blur(12px);
      text-align: center;
    }

    .badge-pill {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 6px 16px;
      background: rgba(var(--brand-rgb), 0.15);
      color: #fca5a5;
      border: 1px solid rgba(var(--brand-rgb), 0.35);
      border-radius: 999px;
      font-size: 13px;
      font-weight: 600;
      letter-spacing: 0.04em;
      text-transform: uppercase;
      margin-bottom: 24px;
    }

    .badge-dot {
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background-color: var(--brand);
      box-shadow: 0 0 10px var(--brand);
      animation: pulse 2s infinite;
    }

    @keyframes pulse {
      0%, 100% { opacity: 1; transform: scale(1); }
      50% { opacity: 0.4; transform: scale(0.85); }
    }

    .maintenance-icon {
      width: 72px;
      height: 72px;
      margin: 0 auto 20px auto;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.12);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 32px;
      color: var(--brand);
    }

    h1.title {
      font-size: clamp(26px, 4vw, 36px);
      font-weight: 700;
      color: #ffffff;
      margin-bottom: 16px;
      line-height: 1.3;
    }

    p.description {
      font-size: 16px;
      color: #94a3b8;
      line-height: 1.7;
      max-width: 620px;
      margin: 0 auto 32px auto;
      white-space: pre-line;
    }

    /* Actions */
    .action-group {
      display: flex;
      justify-content: center;
      gap: 14px;
      flex-wrap: wrap;
      margin-bottom: 32px;
    }

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
      text-decoration: none;
      transition: all 0.2s ease;
      cursor: pointer;
    }

    .btn-brand {
      background-color: var(--brand);
      color: #ffffff;
      border: 1px solid var(--brand);
    }

    .btn-brand:hover {
      opacity: 0.92;
      transform: translateY(-1px);
    }

    .btn-outline {
      background: transparent;
      color: #cbd5e1;
      border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .btn-outline:hover {
      color: #ffffff;
      border-color: rgba(255, 255, 255, 0.4);
      background: rgba(255, 255, 255, 0.05);
      transform: translateY(-1px);
    }

    /* Contact Details Grid */
    .contact-box {
      background: rgba(11, 17, 32, 0.6);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: var(--radius-default);
      padding: 20px;
      margin-top: 16px;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 16px;
      text-align: left;
    }

    .contact-item {
      font-size: 13.5px;
    }

    .contact-label {
      color: #64748b;
      font-size: 11px;
      text-transform: uppercase;
      font-weight: 600;
      letter-spacing: 0.05em;
      margin-bottom: 4px;
    }

    .contact-val {
      color: #e2e8f0;
      font-weight: 500;
      word-break: break-word;
    }

    .contact-val a {
      color: #e2e8f0;
      text-decoration: none;
    }

    .contact-val a:hover {
      color: var(--brand);
      text-decoration: underline;
    }

    /* Admin Banner */
    .admin-notice {
      background: rgba(245, 158, 11, 0.12);
      border: 1px solid rgba(245, 158, 11, 0.35);
      border-radius: var(--radius-default);
      padding: 14px 20px;
      margin-bottom: 24px;
      text-align: left;
      font-size: 13.5px;
      color: #fbbf24;
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 12px;
    }

    .admin-notice a {
      color: #ffffff;
      background: #b45309;
      padding: 6px 12px;
      border-radius: 6px;
      text-decoration: none;
      font-weight: 600;
      font-size: 12.5px;
    }

    .admin-notice a:hover {
      background: #92400e;
    }

    /* Footer */
    footer {
      padding: 24px 0;
      border-top: 1px solid var(--outline-dark);
      text-align: center;
      font-size: 13px;
      color: #64748b;
    }
  </style>
</head>
<body>

  <div class="bg-glow"></div>

  <!-- Header -->
  <header class="header-bar">
    <div class="container">
      <div class="brand-link">
        @if(($displayMode === 'logo' || $displayMode === 'both') && $hasLogo)
          <img src="{{ asset($tenant->logo_url) }}?v={{ time() }}" alt="{{ $tenant->name }}">
        @endif
        @if($displayMode === 'text' || ($displayMode === 'both' && !$hasLogo) || ($displayMode === 'logo' && !$hasLogo))
          <span>{{ $tenant->name }}<span class="brand-dot">.</span></span>
        @elseif($displayMode === 'both' && $hasLogo)
          <span>{{ $tenant->name }}</span>
        @endif
      </div>
    </div>
  </header>

  <!-- Main Content -->
  <main class="main-wrapper">
    <div class="container">

      @if($canPreview)
        <div class="admin-notice">
          <div>
            <strong>Info Admin:</strong> Landing page ini sedang ditutup (non-aktif) untuk pengunjung umum.
          </div>
          <div style="display: flex; gap: 8px;">
            <a href="{{ url()->current() }}?preview=1">Pratinjau Halaman Penuh</a>
            <a href="{{ route('admin.landing.edit') }}" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.3);">Dashboard Pengaturan</a>
          </div>
        </div>
      @endif

      <div class="maintenance-card">
        <div class="badge-pill">
          <span class="badge-dot"></span>
          <span>Mode Pemeliharaan</span>
        </div>

        <div class="maintenance-icon">
          ⚙️
        </div>

        <h1 class="title">{{ $settings->maintenance_title ?: 'Website Sedang Dalam Pemeliharaan' }}</h1>

        <p class="description">
          {{ $settings->maintenance_message ?: 'Mohon maaf atas ketidaknyamanannya. Halaman website kami saat ini sedang ditutup sementara untuk peningkatan kualitas sistem dan layanan. Kami akan segera kembali online.' }}
        </p>

        <div class="action-group">
          <a href="{{ route('tenant.login', ['slug' => $tenant->slug]) }}" class="btn btn-brand">
            Portal Login Member &rarr;
          </a>

          @if(!empty($settings->phone))
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings->phone) }}" target="_blank" class="btn btn-outline">
              Hubungi via WhatsApp
            </a>
          @elseif(!empty($settings->email))
            <a href="mailto:{{ $settings->email }}" class="btn btn-outline">
              Kirim Email
            </a>
          @endif
        </div>

        @if(!empty($settings->address) || !empty($settings->phone) || !empty($settings->email) || !empty($settings->opening_hours))
          <div class="contact-box">
            @if(!empty($settings->opening_hours))
              <div class="contact-item">
                <div class="contact-label">Jam Operasional Gym</div>
                <div class="contact-val">{{ $settings->opening_hours }}</div>
              </div>
            @endif

            @if(!empty($settings->address))
              <div class="contact-item">
                <div class="contact-label">Lokasi Studio</div>
                <div class="contact-val">{{ $settings->address }}</div>
              </div>
            @endif

            @if(!empty($settings->phone))
              <div class="contact-item">
                <div class="contact-label">Telepon / CS</div>
                <div class="contact-val">{{ $settings->phone }}</div>
              </div>
            @endif

            @if(!empty($settings->email))
              <div class="contact-item">
                <div class="contact-label">Email</div>
                <div class="contact-val"><a href="mailto:{{ $settings->email }}">{{ $settings->email }}</a></div>
              </div>
            @endif
          </div>
        @endif
      </div>
    </div>
  </main>

  <!-- Footer -->
  <footer>
    <div class="container">
      &copy; {{ date('Y') }} {{ $tenant->name }}. Hak Cipta Dilindungi.
    </div>
  </footer>

</body>
</html>
