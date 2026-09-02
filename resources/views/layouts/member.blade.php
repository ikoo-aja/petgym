@php
  $currentUser = Auth::user();
  $tenantName = $currentUser && $currentUser->tenant ? $currentUser->tenant->name : 'PetGym';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
  <title>@yield('title', 'Portal Member - PetGym')</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <x-dynamic-favicon />

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('fonts/icomoon/style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">

  <style>
    /* ═══════════════════════════════════════════════════
       PETGYM MEMBER PORTAL — DEDICATED LIGHT & RED THEME
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
    }

    body {
      background-color: var(--mp-bg);
      color: var(--mp-text) !important;
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
      margin: 0;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    /* ── Top Header ── */
    .member-top-nav {
      background: #ffffff;
      border-bottom: 1px solid var(--mp-border);
      padding: 12px 30px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.03);
      position: sticky;
      top: 0;
      z-index: 1000;
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
      background: #f1f5f9;
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

    /* ── Member Top Header & Brand Logo Override ── */
    .member-top-nav .brand-logo-text-airs {
      color: #111827 !important;
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
      background: #ffffff;
      border-right: 1px solid var(--mp-border);
      z-index: 1050;
      display: flex;
      flex-direction: column;
      transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
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
      background: #ffffff;
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
      color: #334155;
      font-weight: 600;
      font-size: 14px;
      text-decoration: none;
      border-left: 4px solid transparent;
      transition: all 0.2s ease;
    }
    .member-sidebar-nav li a:hover {
      color: var(--brand-red);
      background: rgba(244, 63, 94, 0.05);
      border-left-color: var(--brand-red);
    }
    .member-sidebar-nav li a.active {
      color: var(--brand-red);
      background: rgba(244, 63, 94, 0.08);
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
    }
    .card-custom:hover {
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
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
    .modal-header { border-bottom: 1px solid var(--mp-border) !important; }
    .modal-footer { border-top: 1px solid var(--mp-border) !important; background: #f8fafc !important; border-radius: 0 0 14px 14px !important; }

    /* ── Footer ── */
    .member-footer {
      background: #ffffff;
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
    <x-brand-logo type="full" theme="light" size="36" :url="route('member.dashboard')" />
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
        <a href="{{ route('member.billing') }}" class="{{ request()->routeIs('member.billing') ? 'active' : '' }}">
          <span class="icon-shopping-cart mr-3" style="font-size: 16px;"></span> Struk & Tagihan
        </a>
      </li>
      <li>
        <a href="{{ route('member.settings') }}" class="{{ request()->routeIs('member.settings') ? 'active' : '' }}">
          <span class="icon-settings mr-3" style="font-size: 16px;"></span> Pengaturan Profil
        </a>
      </li>
    </ul>
  </div>

  <div class="p-3 border-top bg-light text-center">
    <small class="text-muted d-block font-weight-semibold">{{ $tenantName }}</small>
    <small class="text-muted" style="font-size: 10px;">PetGym Member Portal</small>
  </div>
</aside>

<!-- Member Top Navigation Bar -->
<header class="member-top-nav">
  <div class="d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center">
      <!-- Hamburger Toggle Button -->
      <button type="button" class="btn btn-light border mr-3" onclick="toggleMemberSidebar()" style="border-radius: 8px; padding: 6px 12px;" title="Buka Sidebar Navigasi">
        <span class="icon-menu" style="font-size: 18px; font-weight: bold; color: #1e293b;"></span>
      </button>

      <x-brand-logo type="full" theme="light" size="38" :url="route('member.dashboard')" />
      <span class="badge badge-primary font-weight-bold ml-2 d-none d-sm-inline-block" style="font-size:10px; background-color: var(--brand-red-light) !important; color: var(--brand-red) !important; border: 1px solid var(--brand-red-border) !important;">MEMBER PORTAL</span>
      <span class="text-muted small d-none d-md-inline border-left pl-3 ml-2">{{ $tenantName }}</span>
    </div>

    <div class="d-flex align-items-center gap-3">
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
    <h4 class="font-weight-bold text-dark mb-1">@yield('page_title', 'Beranda Member')</h4>
    <p class="text-muted small mb-0">@yield('page_subtitle', 'Sistem Pengelolaan Keanggotaan Gym PetGym')</p>
  </div>

  {{-- Flash Session Alerts --}}
  @if($errors->any())
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
  &copy; {{ date('Y') }} {{ $tenantName }} &mdash; PetGym Member Portal. All rights reserved.
</footer>

<script src="{{ asset('js/jquery-3.3.1.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script>
  function toggleMemberSidebar() {
    const sidebar = document.getElementById('memberSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    if (sidebar && overlay) {
      sidebar.classList.toggle('active');
      overlay.classList.toggle('active');
    }
  }
</script>
@yield('scripts')

</body>
</html>
