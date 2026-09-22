@php
  $currentUser = Auth::user();
  $tenantName = $currentUser && $currentUser->tenant ? $currentUser->tenant->name : 'Gym Portal';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
  <title>@yield('title', 'Portal Member') - {{ $tenantName }}</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <x-dynamic-favicon />

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('fonts/icomoon/style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">

  <script>
    (function() {
      const savedTheme = localStorage.getItem('member_theme');
      if (savedTheme === 'dark') {
        document.documentElement.setAttribute('data-theme', 'dark');
      } else {
        document.documentElement.setAttribute('data-theme', 'light');
      }
    })();
  </script>

  <style>
    /* ═══════════════════════════════════════════════════
       PETGYM MEMBER PORTAL — DEDICATED LIGHT & DARK THEME
       Font: Inter | Primary Brand Color: Red (#f43f5e)
    ═══════════════════════════════════════════════════ */
    :root {
      --brand-red: #f43f5e;
      --brand-red-hover: #e11d48;
      --brand-red-light: rgba(244, 63, 94, 0.08);
      --brand-red-border: rgba(244, 63, 94, 0.2);
      --mp-bg: #f8fafc;
      --mp-card-bg: #ffffff;
      --mp-text: #1e293b;
      --mp-text-muted: #64748b;
      --mp-border: #e2e8f0;
      --mp-radius: 12px;
      --mp-nav-bg: #ffffff;
      --mp-sidebar-bg: #ffffff;
      --mp-pill-bg: #f1f5f9;
      --mp-input-bg: #ffffff;
      --mp-box-light: #f8fafc;
    }

    [data-theme="dark"] {
      --mp-bg: #0f172a;
      --mp-card-bg: #1e293b;
      --mp-text: #f8fafc;
      --mp-text-muted: #94a3b8;
      --mp-border: #334155;
      --mp-nav-bg: #1e293b;
      --mp-sidebar-bg: #1e293b;
      --mp-pill-bg: #334155;
      --mp-input-bg: #0f172a;
      --mp-box-light: #0f172a;
    }

    body {
      background-color: var(--mp-bg);
      color: var(--mp-text) !important;
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
      margin: 0;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      transition: background-color 0.2s ease, color 0.2s ease;
    }

    /* ── Dark Mode Global Overrides ── */
    [data-theme="dark"] .text-dark {
      color: #f8fafc !important;
    }
    [data-theme="dark"] .text-muted {
      color: #94a3b8 !important;
    }
    [data-theme="dark"] .bg-light,
    [data-theme="dark"] .bg-white {
      background-color: var(--mp-card-bg) !important;
      color: var(--mp-text) !important;
    }
    [data-theme="dark"] .border {
      border-color: var(--mp-border) !important;
    }
    [data-theme="dark"] .card {
      background-color: var(--mp-card-bg) !important;
      border-color: var(--mp-border) !important;
      color: var(--mp-text) !important;
    }

    /* ── Top Header ── */
    .member-top-nav {
      background: var(--mp-nav-bg);
      border-bottom: 1px solid var(--mp-border);
      padding: 12px 30px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.03);
      position: sticky;
      top: 0;
      z-index: 1000;
      transition: background-color 0.2s ease, border-color 0.2s ease;
    }

    .brand-logo-text {
      font-weight: 800;
      font-size: 18px;
      color: var(--brand-red);
      letter-spacing: -0.5px;
      text-decoration: none;
    }
    .brand-logo-text:hover {
      color: var(--brand-red-hover);
      text-decoration: none;
    }

    .member-profile-pill {
      background: var(--mp-pill-bg);
      border-radius: 30px;
      padding: 5px 14px 5px 6px;
      display: inline-flex;
      align-items: center;
      gap: 10px;
    }
    .member-avatar-circle {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      background: var(--brand-red);
      color: #ffffff;
      font-weight: 800;
      font-size: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    /* ── Theme Toggle Button ── */
    .theme-toggle-btn {
      background: var(--mp-pill-bg);
      border: 1px solid var(--mp-border);
      color: var(--mp-text);
      width: 38px;
      height: 38px;
      border-radius: 50%;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      font-size: 18px;
      transition: all 0.2s ease;
      padding: 0;
      outline: none !important;
    }
    .theme-toggle-btn:hover {
      transform: scale(1.08);
      background: var(--brand-red-light);
      color: var(--brand-red);
      border-color: var(--brand-red-border);
    }

    /* ── Member Top Header & Brand Logo Override ── */
    .member-top-nav .brand-logo-text-airs {
      color: var(--mp-text) !important;
    }

    /* ── Member Sidebar & Backdrop Overlay ── */
    .member-sidebar-overlay {
      position: fixed;
      top: 0; left: 0; right: 0; bottom: 0;
      background: rgba(15, 23, 42, 0.5);
      backdrop-filter: blur(2px);
      z-index: 1040;
      opacity: 0;
      pointer-events: none;
      transition: opacity 0.3s ease;
    }
    .member-sidebar-overlay.active {
      opacity: 1;
      pointer-events: auto;
    }

    .member-sidebar {
      position: fixed;
      top: 0;
      left: -280px;
      width: 280px;
      height: 100vh;
      background: var(--mp-sidebar-bg);
      border-right: 1px solid var(--mp-border);
      z-index: 1050;
      display: flex;
      flex-direction: column;
      transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1), background-color 0.2s ease;
      box-shadow: 4px 0 25px rgba(0,0,0,0.12);
    }
    .member-sidebar.active {
      left: 0;
    }

    .member-sidebar-header {
      padding: 16px 20px;
      border-bottom: 1px solid var(--mp-border);
      display: flex;
      align-items: center;
      justify-content: space-between;
      background: var(--mp-sidebar-bg);
    }
    .member-sidebar-body {
      padding: 16px 0;
      overflow-y: auto;
      flex-grow: 1;
    }
    .member-sidebar-nav {
      list-style: none;
      padding: 0;
      margin: 0;
    }
    .member-sidebar-nav li a {
      display: flex;
      align-items: center;
      padding: 12px 24px;
      color: var(--mp-text-muted);
      font-weight: 600;
      font-size: 14px;
      text-decoration: none;
      border-left: 4px solid transparent;
      transition: all 0.2s ease;
    }
    .member-sidebar-nav li a:hover {
      color: var(--brand-red);
      background: rgba(244, 63, 94, 0.08);
      border-left-color: var(--brand-red);
    }
    .member-sidebar-nav li a.active {
      color: var(--brand-red);
      background: rgba(244, 63, 94, 0.12);
      border-left-color: var(--brand-red);
      font-weight: 700;
    }

    /* ── Main Content Container ── */
    .member-container {
      padding: 30px;
      flex-grow: 1;
      max-width: 1280px;
      width: 100%;
      margin: 0 auto;
    }

    /* ── Custom Cards ── */
    .card-custom {
      background: var(--mp-card-bg);
      border: 1px solid var(--mp-border);
      border-radius: var(--mp-radius);
      box-shadow: 0 1px 3px rgba(0,0,0,0.03);
      padding: 24px;
      margin-bottom: 24px;
      transition: all 0.2s ease;
      color: var(--mp-text);
    }
    .card-custom:hover {
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }

    /* ── Form Inputs Override ── */
    [data-theme="dark"] .form-control,
    [data-theme="dark"] select.form-control,
    [data-theme="dark"] .custom-select {
      background-color: var(--mp-input-bg) !important;
      color: var(--mp-text) !important;
      border-color: var(--mp-border) !important;
    }
    [data-theme="dark"] .form-control:focus {
      border-color: var(--brand-red) !important;
      box-shadow: 0 0 0 0.2rem rgba(244, 63, 94, 0.25) !important;
    }

    /* ── Buttons Override ── */
    .btn-primary {
      background-color: var(--brand-red) !important;
      border-color: var(--brand-red) !important;
      color: #ffffff !important;
      font-weight: 700 !important;
      border-radius: 8px !important;
      box-shadow: 0 2px 4px rgba(244, 63, 94, 0.2) !important;
    }
    .btn-primary:hover, .btn-primary:focus {
      background-color: var(--brand-red-hover) !important;
      border-color: var(--brand-red-hover) !important;
      box-shadow: 0 4px 8px rgba(244, 63, 94, 0.3) !important;
    }
    .btn-outline-primary {
      color: var(--brand-red) !important;
      border-color: var(--brand-red) !important;
      font-weight: 700 !important;
      border-radius: 8px !important;
    }
    .btn-outline-primary:hover {
      background-color: var(--brand-red) !important;
      color: #ffffff !important;
    }

    [data-theme="dark"] .btn-light {
      background-color: var(--mp-pill-bg) !important;
      border-color: var(--mp-border) !important;
      color: var(--mp-text) !important;
    }

    /* ── Text & Badges Override ── */
    .text-primary { color: var(--brand-red) !important; }
    .border-primary { border-color: var(--brand-red) !important; }

    .badge-primary {
      background-color: var(--brand-red-light) !important;
      color: var(--brand-red) !important;
      border: 1px solid var(--brand-red-border) !important;
    }

    /* ── Modal Styling ── */
    .modal-content {
      border-radius: 14px !important;
      border: 1px solid var(--mp-border) !important;
      box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1) !important;
    }
    [data-theme="dark"] .modal-content {
      background-color: var(--mp-card-bg) !important;
      color: var(--mp-text) !important;
      border-color: var(--mp-border) !important;
    }
    .modal-header { border-bottom: 1px solid var(--mp-border) !important; }
    .modal-footer { border-top: 1px solid var(--mp-border) !important; background: var(--mp-pill-bg) !important; border-radius: 0 0 14px 14px !important; }
    [data-theme="dark"] .modal-header,
    [data-theme="dark"] .modal-footer {
      background-color: var(--mp-card-bg) !important;
      border-color: var(--mp-border) !important;
    }
    [data-theme="dark"] .modal-header .close {
      color: var(--mp-text) !important;
    }

    /* ── Footer ── */
    .member-footer {
      background: var(--mp-nav-bg);
      border-top: 1px solid var(--mp-border);
      padding: 20px 30px;
      text-align: center;
      font-size: 13px;
      color: var(--mp-text-muted);
      margin-top: auto;
    }
  </style>
  @yield('styles')
</head>
<body>

<!-- Member Sidebar Overlay -->
<div class="member-sidebar-overlay" id="sidebarOverlay" onclick="toggleMemberSidebar()"></div>

<!-- Member Sidebar Drawer -->
<aside class="member-sidebar" id="memberSidebar">
  <div class="member-sidebar-header">
    @if($currentUser && $currentUser->tenant && $currentUser->tenant->logo_url)
      <a href="{{ route('member.dashboard') }}" class="d-flex align-items-center text-decoration-none">
        <img src="{{ asset($currentUser->tenant->logo_url) }}" alt="{{ $tenantName }}" style="max-height: 38px; max-width: 160px; object-fit: contain;">
      </a>
    @else
      <a href="{{ route('member.dashboard') }}" class="font-weight-extrabold text-dark text-decoration-none" style="font-size: 18px;">
        <i class="icon-fitness_center text-danger mr-1"></i> {{ $tenantName }}
      </a>
    @endif
    <button type="button" class="btn btn-sm btn-light border p-1" onclick="toggleMemberSidebar()" style="font-size: 18px; line-height: 1; border-radius: 6px;" title="Tutup Menu">&times;</button>
  </div>

  <div class="member-sidebar-body">
    <div class="px-4 py-2 text-uppercase font-weight-bold text-muted" style="font-size: 11px; letter-spacing: 0.5px;">Navigasi Portal Member</div>
    <ul class="member-sidebar-nav">
      <li>
        <a href="{{ route('member.dashboard') }}" class="{{ request()->routeIs('member.dashboard') ? 'active' : '' }}">
          <span class="icon-dashboard mr-3" style="font-size: 16px;"></span> Beranda
        </a>
      </li>
      <li>
        <a href="{{ route('member.membership') }}" class="{{ request()->routeIs('member.membership') ? 'active' : '' }}">
          <span class="icon-file-text mr-3" style="font-size: 16px;"></span> Keanggotaan
        </a>
      </li>
      <li>
        <a href="{{ route('member.lockers') }}" class="{{ request()->routeIs('member.lockers*') ? 'active' : '' }}">
          <span class="icon-settings mr-3" style="font-size: 16px;"></span> Booking Loker
        </a>
      </li>
      <li>
        <a href="{{ route('member.pt') }}" class="{{ request()->routeIs('member.pt*') ? 'active' : '' }}">
          <span class="icon-person mr-3" style="font-size: 16px;"></span> Personal Trainer
        </a>
      </li>
      <li>
        <a href="{{ route('member.classes') }}" class="{{ request()->routeIs('member.classes*') ? 'active' : '' }}">
          <span class="icon-calendar mr-3" style="font-size: 16px;"></span> Kelas Kebugaran
        </a>
      </li>
      <li>
        <a href="{{ route('member.billing') }}" class="{{ request()->routeIs('member.billing*') ? 'active' : '' }}">
          <span class="icon-shopping-cart mr-3" style="font-size: 16px;"></span> Struk & Tagihan
        </a>
      </li>
      <li>
        <a href="{{ route('member.guide') }}" class="{{ request()->routeIs('member.guide*') ? 'active' : '' }}">
          <span class="icon-help-with-circle mr-3" style="font-size: 16px;"></span> Panduan Penggunaan
        </a>
      </li>
      <li>
        <a href="{{ route('member.settings') }}" class="{{ request()->routeIs('member.settings*') ? 'active' : '' }}">
          <span class="icon-cog mr-3" style="font-size: 16px;"></span> Pengaturan Profil
        </a>
      </li>
    </ul>
  </div>

  <div class="p-3 border-top bg-light text-center">
    <div class="d-flex align-items-center justify-content-between mb-2">
      <small class="text-muted font-weight-bold">Mode Tampilan</small>
      <button type="button" class="theme-toggle-btn" style="width:32px; height:32px; font-size:15px;" onclick="toggleMemberTheme()" title="Ganti Mode Terang/Gelap">
        <span class="themeToggleBtnIcon">🌙</span>
      </button>
    </div>
    <small class="text-muted d-block font-weight-semibold">{{ $tenantName }}</small>
    <small class="text-muted" style="font-size: 10px;">Portal Keanggotaan Member</small>
  </div>
</aside>

<!-- Member Top Navigation Bar -->
<header class="member-top-nav">
  <div class="d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center">
      <!-- Hamburger Toggle Button -->
      <button type="button" class="btn btn-light border mr-3" onclick="toggleMemberSidebar()" style="border-radius: 8px; padding: 6px 12px;" title="Buka Sidebar Navigasi">
        <span class="icon-menu" style="font-size: 18px; font-weight: bold; color: var(--mp-text);"></span>
      </button>

      @if($currentUser && $currentUser->tenant && $currentUser->tenant->logo_url)
        <a href="{{ route('member.dashboard') }}">
          <img src="{{ asset($currentUser->tenant->logo_url) }}" alt="{{ $tenantName }}" style="max-height: 40px; max-width: 150px; object-fit: contain;">
        </a>
      @else
        <a href="{{ route('member.dashboard') }}" class="font-weight-extrabold text-dark text-decoration-none" style="font-size: 18px;">
          <i class="icon-fitness_center text-danger mr-1"></i> {{ $tenantName }}
        </a>
      @endif
      <span class="badge badge-primary font-weight-bold ml-2 d-none d-sm-inline-block" style="font-size:10px; background-color: var(--brand-red-light) !important; color: var(--brand-red) !important; border: 1px solid var(--brand-red-border) !important;">PORTAL ANGGOTA</span>
    </div>

    <div class="d-flex align-items-center" style="gap: 12px;">
      <!-- Dark Mode Toggle Button in Top Header -->
      <button type="button" class="theme-toggle-btn mr-1" onclick="toggleMemberTheme()" title="Ganti Mode Terang/Gelap">
        <span class="themeToggleBtnIcon">🌙</span>
      </button>

      <a href="{{ route('member.settings') }}" class="d-inline-flex align-items-center justify-content-center text-decoration-none" title="Pengaturan Profil">
        <div class="member-avatar-circle" style="width: 36px; height: 36px; font-size: 15px; font-weight: 800; box-shadow: 0 2px 5px rgba(244,63,94,0.3);">
          {{ substr($currentUser->name ?? 'M', 0, 1) }}
        </div>
      </a>
    </div>
  </div>
</header>

<!-- Main Page Content -->
<main class="member-container">
  <!-- Page Title Header -->
  <div class="mb-4">
    <h4 class="font-weight-bold text-dark mb-1">@yield('page_title', 'Beranda Anggota')</h4>
    <p class="text-muted small mb-0">@yield('page_subtitle', 'Sistem Pengelolaan Keanggotaan Member Gym')</p>
  </div>

  {{-- Flash Session Alerts --}}
  @if(isset($errors) && $errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-radius: 10px;">
      <strong>Terjadi Kesalahan:</strong>
      <ul class="mb-0 mt-1 pl-3 font-weight-bold">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
  @endif

  @include('partials.flash-toast')

  @yield('content')
</main>

<footer class="member-footer">
  &copy; {{ date('Y') }} {{ $tenantName }}. Hak cipta dilindungi undang-undang.
</footer>

<script src="{{ asset('js/jquery-3.3.1.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
@include('partials.confirm-modal')
@include('partials.toast-helper')
<script>
  function toggleMemberSidebar() {
    const sidebar = document.getElementById('memberSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    if (sidebar && overlay) {
      sidebar.classList.toggle('active');
      overlay.classList.toggle('active');
    }
  }

  function updateThemeUI(theme) {
    const icons = document.querySelectorAll('.themeToggleBtnIcon');
    icons.forEach(icon => {
      icon.textContent = theme === 'dark' ? '☀️' : '🌙';
    });
    const switchEl = document.getElementById('settingsThemeSwitch');
    if (switchEl) {
      switchEl.checked = (theme === 'dark');
    }
    const statusTextEl = document.getElementById('themeSettingStatusText');
    if (statusTextEl) {
      statusTextEl.textContent = theme === 'dark' ? 'Saat ini: Mode Gelap (Dark Mode)' : 'Saat ini: Mode Terang (Default)';
    }
  }

  function toggleMemberTheme() {
    const currentTheme = document.documentElement.getAttribute('data-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-theme', newTheme);
    localStorage.setItem('member_theme', newTheme);
    updateThemeUI(newTheme);
  }

  document.addEventListener('DOMContentLoaded', function() {
    const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
    updateThemeUI(currentTheme);
  });
</script>
@yield('scripts')

</body>
</html>
