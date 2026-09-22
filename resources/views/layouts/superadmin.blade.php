<!DOCTYPE html>
<html lang="en">

<head>
  <title>@yield('title', 'Superadmin Dashboard &mdash; Workout Website Template by Colorlib')</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <x-dynamic-favicon />

  <link href="https://fonts.googleapis.com/css?family=Muli:300,400,700,900" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('fonts/icomoon/style.css') }}">

  <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/jquery-ui.css') }}">
  <link rel="stylesheet" href="{{ asset('css/owl.carousel.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/owl.theme.default.min.css') }}">

  <link rel="stylesheet" href="{{ asset('css/jquery.fancybox.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/bootstrap-datepicker.css') }}">
  <link rel="stylesheet" href="{{ asset('fonts/flaticon/font/flaticon.css') }}">

  <link rel="stylesheet" href="{{ asset('css/aos.css') }}">
  <link href="{{ asset('css/jquery.mb.YTPlayer.min.css') }}" media="all" rel="stylesheet" type="text/css">

  <link rel="stylesheet" href="{{ asset('css/style.css') }}">

  <style>
    body {
      color: #1f2937 !important;
    }
    .admin-content, .admin-content table, .admin-content td, .admin-content th,
    .admin-content p, .admin-content label {
      color: #1f2937;
    }
    .admin-content table td, .admin-content table th {
      color: #1f2937 !important;
    }
    ::selection {
      background: #e2e8f0 !important;
      color: #0f172a !important;
    }
    ::-moz-selection {
      background: #e2e8f0 !important;
      color: #0f172a !important;
    }

    /* Custom Admin Sidebar & Content Layout Override */
    .admin-wrapper {
      display: flex;
      min-height: 100vh;
      background-color: #f8f9fa;
    }
    .admin-sidebar {
      width: 260px;
      background: #1a1a1a;
      color: #fff;
      position: fixed;
      top: 0;
      bottom: 0;
      left: 0;
      z-index: 100;
      overflow-y: auto;
      transition: top 0.2s ease;
    }
    .admin-sidebar .sidebar-brand {
      padding: 25px 20px;
      font-size: 24px;
      font-weight: 900;
      color: #fff;
      border-bottom: 1px solid #2a2a2a;
    }
    .admin-sidebar .sidebar-brand span {
      color: #f38181;
    }
    .admin-sidebar .nav-link {
      color: #b3b3b3;
      padding: 12px 20px;
      font-weight: 700;
      font-size: 14px;
      display: flex;
      align-items: center;
      transition: all 0.3s ease;
    }
    .admin-sidebar .nav-link:hover,
    .admin-sidebar .nav-link.active {
      color: #fff;
      background: #262626;
      border-left: 4px solid #f38181;
    }
    .admin-sidebar .nav-link span[class^="icon-"] {
      margin-right: 12px;
      font-size: 16px;
    }
    .admin-content {
      margin-left: 260px;
      width: calc(100% - 260px);
      padding: 30px;
    }
    .admin-header {
      background: #fff;
      padding: 15px 30px;
      margin: -30px -30px 30px -30px;
      border-bottom: 1px solid #e9ecef;
    }
    .stat-card {
      background: #fff;
      border-radius: 4px;
      padding: 20px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
      border-left: 4px solid #ee6e73;
    }
    .stat-card .stat-number {
      font-size: 28px;
      font-weight: 900;
      color: #000;
    }
    .table-custom {
      background: #fff;
      border-radius: 4px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .badge-status-active { background: #d4edda; color: #155724; }
    .badge-status-suspended { background: #f8d7da; color: #721c24; }
    .badge-status-pending { background: #fff3cd; color: #856404; }

    @media (max-width: 991.98px) {
      .admin-sidebar { left: -260px; }
      .admin-content { margin-left: 0; width: 100%; }
    }

    /* Custom Toast Notifications */
    .custom-toast {
      min-width: 320px;
      max-width: 400px;
      background: #fff;
      border-radius: 8px;
      padding: 16px 20px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.08);
      display: flex;
      align-items: flex-start;
      gap: 12px;
      transform: translateX(120%);
      transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
      border-left: 5px solid #888;
    }
    .custom-toast.show {
      transform: translateX(0);
    }
    .custom-toast.toast-success { border-left-color: #28a745; }
    .custom-toast.toast-error { border-left-color: #dc3545; }
    .custom-toast.toast-warning { border-left-color: #ffc107; }
    .custom-toast.toast-info { border-left-color: #17a2b8; }

    .custom-toast-icon {
      font-size: 20px;
      margin-top: 2px;
    }
    .toast-success .custom-toast-icon { color: #28a745; }
    .toast-error .custom-toast-icon { color: #dc3545; }
    .toast-warning .custom-toast-icon { color: #ffc107; }
    .toast-info .custom-toast-icon { color: #17a2b8; }

    .custom-toast-content {
      flex: 1;
    }
    .custom-toast-title {
      font-weight: 800;
      color: #1a1a1a;
      font-size: 14px;
      margin-bottom: 2px;
    }
    .custom-toast-message {
      color: #666;
      font-size: 12px;
      line-height: 1.4;
    }
    .custom-toast-close {
      background: none;
      border: none;
      color: #aaa;
      cursor: pointer;
      font-size: 18px;
      padding: 0;
      line-height: 1;
      transition: color 0.2s;
    }
    .custom-toast-close:hover {
      color: #666;
    }
  </style>
  @yield('styles')
</head>

<body>

  <div class="admin-wrapper">

    <!-- Sidebar Navigation -->
    <aside class="admin-sidebar">
      <div class="sidebar-brand d-flex align-items-center justify-content-between">
        <x-brand-logo type="full" theme="dark" size="36" url="/" />
        <small style="font-size: 9px; color:#9ca3af; font-weight:800; background:#1e293b; padding:2px 6px; border-radius:4px;">SUPER ADMIN</small>
      </div>
      <ul class="nav flex-column mt-3">
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}" href="{{ route('superadmin.dashboard') }}"><span class="icon-dashboard"></span> Beranda</a>
        </li>
        <li class="nav-item">
          @php
            $pendingRegistrationsBadge = \App\Models\TenantRegistration::where('status', 'pending')->count();
          @endphp
          <a class="nav-link {{ request()->routeIs('superadmin.registrations*') ? 'active' : '' }} d-flex justify-content-between align-items-center" href="{{ route('superadmin.registrations') }}">
            <span><span class="icon-user-plus"></span> Pendaftaran Penyewa</span>
            @if($pendingRegistrationsBadge > 0)
              <span class="badge badge-danger badge-pill px-2" style="font-size: 10px;">{{ $pendingRegistrationsBadge }}</span>
            @endif
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('superadmin.tenants') ? 'active' : '' }}" href="{{ route('superadmin.tenants') }}"><span class="icon-building"></span> Kelola Penyewa</a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('superadmin.plans') ? 'active' : '' }}" href="{{ route('superadmin.plans') }}"><span class="icon-layers"></span> Paket Sewa</a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('superadmin.billing') ? 'active' : '' }}" href="{{ route('superadmin.billing') }}"><span class="icon-credit-card"></span> Keuangan & Tagihan</a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('superadmin.announcements') ? 'active' : '' }}" href="{{ route('superadmin.announcements') }}"><span class="icon-notifications"></span> Pengumuman</a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('superadmin.logs') ? 'active' : '' }}" href="{{ route('superadmin.logs') }}"><span class="icon-history"></span> Log Sistem</a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('superadmin.settings') ? 'active' : '' }}" href="{{ route('superadmin.settings') }}"><span class="icon-settings"></span> Pengaturan</a>
        </li>
        <li class="nav-item mt-4">
          <a class="nav-link text-danger" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <span class="icon-power_settings_new"></span> Keluar
          </a>
          <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
          </form>
        </li>
      </ul>
    </aside>

    <!-- Main Content Area -->
    <main class="admin-content">

      <!-- Top Header Bar -->
      <header class="admin-header d-flex align-items-center justify-content-between">
        <div>
          <h4 class="mb-0 font-weight-bold text-black">@yield('page_title', 'Ringkasan Beranda')</h4>
          <small class="text-muted">@yield('page_subtitle', 'Selamat datang kembali!')</small>
        </div>
        <div class="d-flex align-items-center">
          <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle btn-sm" type="button" id="userMenu" data-toggle="dropdown">
              {{ Auth::user()->name ?? 'Administrator' }}
            </button>
            <div class="dropdown-menu dropdown-menu-right">
              <a class="dropdown-item" href="{{ route('superadmin.profile') }}">Profil</a>
              <a class="dropdown-item" href="{{ route('superadmin.settings') }}">Pengaturan</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item text-danger" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Keluar</a>
            </div>
          </div>
        </div>
      </header>

      @yield('content')

      <!-- Footer Admin -->
      <footer class="mt-5 pt-4 border-top text-center text-muted" style="font-size: 13px;">
        <p>Copyright &copy; {{ date('Y') }} Workout SaaS Management Engine. Integrated with Colorlib Admin UI Standard.</p>
      </footer>

    </main>
  </div>

  @yield('modals')

  <!-- Toast Notification Container & Standard Flash Toast -->
  @include('partials.flash-toast')
  @include('partials.toast-helper')

  @yield('scripts')

</body>

</html>
