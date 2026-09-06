@php
  $currentUser = Auth::user();
  $userRole = $currentUser ? $currentUser->role : 'admin';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
  <title>@yield('title', 'Portal Tenant &mdash; PetGym SaaS')</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <x-dynamic-favicon />

  <link href="https://fonts.googleapis.com/css?family=Muli:300,400,700,900" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('fonts/icomoon/style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">

  <style>
    body {
      background-color: #f4f6f9;
      color: #1f2937 !important;
      font-family: 'Muli', sans-serif;
    }
    .admin-main, .admin-content, .admin-content p, .admin-content label {
      color: #1f2937;
    }
    .admin-main table td, .admin-main table th,
    .admin-content table td, .admin-content table th {
      color: #1f2937 !important;
    }
    .admin-sidebar {
      color: #9ca3af;
    }
    ::selection {
      background: #e2e8f0 !important;
      color: #0f172a !important;
    }
    ::-moz-selection {
      background: #e2e8f0 !important;
      color: #0f172a !important;
    }
    .admin-wrapper {
      display: flex;
      min-height: 100vh;
    }
    .admin-sidebar {
      width: 260px;
      background: #111827;
      color: #9ca3af;
      position: fixed;
      top: 0;
      bottom: 0;
      left: 0;
      z-index: 1000;
      overflow-y: auto;
      display: flex;
      flex-direction: column;
      box-shadow: 4px 0 15px rgba(0,0,0,0.1);
    }
    .sidebar-brand {
      padding: 24px 20px;
      background: #0f172a;
      border-bottom: 1px solid #1e293b;
    }
    .sidebar-brand h3 {
      color: #f43f5e;
      font-weight: 900;
      margin: 0;
      font-size: 18px;
      letter-spacing: -0.5px;
    }
    .sidebar-brand .tenant-badge {
      display: inline-block;
      margin-top: 4px;
      background: #1e293b;
      color: #38bdf8;
      font-size: 11px;
      font-weight: 700;
      padding: 2px 8px;
      border-radius: 12px;
    }
    .sidebar-menu {
      padding: 15px 0;
      list-style: none;
      margin: 0;
      flex-grow: 1;
    }
    .sidebar-menu .menu-header {
      padding: 12px 20px 6px;
      font-size: 10px;
      font-weight: 800;
      text-transform: uppercase;
      color: #6b7280;
      letter-spacing: 0.8px;
    }
    .sidebar-menu li a {
      display: flex;
      align-items: center;
      padding: 11px 20px;
      color: #9ca3af;
      text-decoration: none;
      font-weight: 600;
      font-size: 13.5px;
      transition: all 0.2s ease;
      border-left: 3px solid transparent;
    }
    .sidebar-menu li a:hover {
      color: #ffffff;
      background: #1f2937;
    }
    .sidebar-menu li a.active {
      color: #ffffff;
      background: #1f2937;
      border-left-color: #f43f5e;
    }
    .sidebar-menu li a span.icon-wrapper {
      width: 24px;
      margin-right: 10px;
      font-size: 16px;
      text-align: center;
    }
    .admin-main {
      margin-left: 260px;
      flex-grow: 1;
      display: flex;
      flex-direction: column;
      min-width: 0;
    }
    .top-navbar {
      background: #ffffff;
      height: 64px;
      padding: 0 30px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-bottom: 1px solid #e5e7eb;
      position: sticky;
      top: 0;
      z-index: 900;
    }
    .page-title-box h5 {
      margin: 0;
      font-weight: 800;
      color: #111827;
      font-size: 17px;
    }
    .page-title-box p {
      margin: 0;
      font-size: 12px;
      color: #6b7280;
    }
    .user-profile-nav {
      display: flex;
      align-items: center;
      gap: 15px;
    }
    .admin-content {
      padding: 30px;
      flex-grow: 1;
    }
    .card-custom {
      border: none;
      border-radius: 12px;
      background: #ffffff;
      box-shadow: 0 1px 3px rgba(0,0,0,0.05), 0 1px 2px rgba(0,0,0,0.03);
      padding: 24px;
      margin-bottom: 24px;
    }
  </style>

  @if($userRole === 'member')
  <style>
    /* Clean White & Red Branding for Member Portal */
    :root {
      --brand-red: #f43f5e;
      --brand-red-hover: #e11d48;
      --brand-red-light: rgba(244, 63, 94, 0.08);
      --brand-red-glow: rgba(244, 63, 94, 0.15);
      --mp-border: #e5e7eb;
    }

    body {
      background-color: #f8fafc;
      color: #1e293b !important;
    }

    /* Override buttons */
    .btn-primary {
      background-color: var(--brand-red) !important;
      border-color: var(--brand-red) !important;
      color: #ffffff !important;
      font-weight: 700 !important;
      border-radius: 8px !important;
      box-shadow: 0 2px 4px rgba(244, 63, 94, 0.2) !important;
      transition: all 0.2s ease !important;
    }
    .btn-primary:hover, .btn-primary:focus, .btn-primary:active {
      background-color: var(--brand-red-hover) !important;
      border-color: var(--brand-red-hover) !important;
      transform: translateY(-1px);
      box-shadow: 0 4px 6px rgba(244, 63, 94, 0.3) !important;
    }
    .btn-outline-primary {
      color: var(--brand-red) !important;
      border-color: var(--brand-red) !important;
      font-weight: 700 !important;
      border-radius: 8px !important;
      transition: all 0.2s ease !important;
    }
    .btn-outline-primary:hover, .btn-outline-primary:focus, .btn-outline-primary:active {
      background-color: var(--brand-red) !important;
      border-color: var(--brand-red) !important;
      color: #ffffff !important;
    }

    /* Override primary text & borders */
    .text-primary {
      color: var(--brand-red) !important;
    }
    .border-primary {
      border-color: var(--brand-red) !important;
    }

    /* Custom Cards for Member Portal */
    .card-custom {
      background: #ffffff !important;
      border: 1px solid var(--mp-border) !important;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02), 0 1px 2px rgba(0, 0, 0, 0.04) !important;
      border-radius: 12px !important;
      transition: all 0.2s ease !important;
    }
    .card-custom:hover {
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05) !important;
    }

    /* Custom Badges */
    .badge-primary {
      background-color: var(--brand-red-light) !important;
      color: var(--brand-red) !important;
      border: 1px solid rgba(244, 63, 94, 0.2) !important;
    }
    .badge-success {
      background-color: rgba(16, 185, 129, 0.1) !important;
      color: #10b981 !important;
      border: 1px solid rgba(16, 185, 129, 0.2) !important;
    }
    .badge-warning {
      background-color: rgba(245, 158, 11, 0.1) !important;
      color: #d97706 !important;
      border: 1px solid rgba(245, 158, 11, 0.2) !important;
    }
    .badge-danger {
      background-color: rgba(239, 68, 68, 0.1) !important;
      color: #ef4444 !important;
      border: 1px solid rgba(239, 68, 68, 0.2) !important;
    }
    .badge-info {
      background-color: rgba(59, 130, 246, 0.1) !important;
      color: #3b82f6 !important;
      border: 1px solid rgba(59, 130, 246, 0.2) !important;
    }
    .badge-secondary {
      background-color: #f1f5f9 !important;
      color: #475569 !important;
      border: 1px solid #e2e8f0 !important;
    }

    /* Table styles */
    .table thead th {
      border-bottom: 2px solid #f1f5f9 !important;
      color: #475569 !important;
      font-weight: 700 !important;
    }
    .table td {
      border-bottom: 1px solid #f1f5f9 !important;
    }

    /* Modal Styling */
    .modal-content {
      border-radius: 16px !important;
      border: 1px solid var(--mp-border) !important;
      box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04) !important;
    }
    .modal-header {
      border-bottom: 1px solid #f1f5f9 !important;
    }
    .modal-footer {
      border-top: 1px solid #f1f5f9 !important;
      background-color: #f8fafc !important;
      border-radius: 0 0 16px 16px !important;
    }
  </style>
  @endif
</head>
<body>

@php
  // Tentukan rute dashboard sesuai role
  $dashUrl = route('admin.dashboard');
  if ($userRole === 'owner') {
      $dashUrl = route('owner.dashboard');
  } elseif ($userRole === 'manager') {
      $dashUrl = route('manager.dashboard');
  } elseif ($userRole === 'receptionist') {
      $dashUrl = route('receptionist.dashboard');
  } elseif ($userRole === 'trainer') {
      $dashUrl = route('trainer.dashboard');
  } elseif ($userRole === 'member') {
      $dashUrl = route('member.dashboard');
  }
@endphp

<div class="admin-wrapper">
  <!-- Dynamic Sidebar per Role -->
  <aside class="admin-sidebar">
    <div class="sidebar-brand d-flex flex-column align-items-center  gap-1 p-2 ">
      <x-brand-logo type="full" theme="dark" size="49" url="/" />
      <span class="tenant-badge" style="font-size: 15px;">{{ $currentUser && $currentUser->tenant ? $currentUser->tenant->name : 'Tenant' }}</span>
    </div>

    <ul class="sidebar-menu">
      <li class="menu-header">Utama</li>
      <li>
        <a href="{{ $dashUrl }}" class="{{ request()->routeIs('*.dashboard') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-dashboard"></span></span> Beranda {{ ucfirst($userRole) }}
        </a>
        </a>
      </li>

      <li class="menu-header">Operasional</li>

      @if($userRole === 'member')
      <li>
        <a href="{{ route('member.lockers') }}" class="{{ request()->routeIs('member.lockers') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-settings"></span></span> Manajemen Loker Gym
        </a>
      </li>
      <li>
        <a href="{{ route('member.membership') }}" class="{{ request()->routeIs('member.membership') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-star"></span></span> Paket Keanggotaan
        </a>
      </li>
      <li>
        <a href="{{ route('member.pt') }}" class="{{ request()->routeIs('member.pt') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-person"></span></span> Personal Trainer
        </a>
      </li>
      <li>
        <a href="{{ route('member.classes') }}" class="{{ request()->routeIs('member.classes') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-calendar"></span></span> Kelas Kebugaran
        </a>
      </li>
      <li>
        <a href="{{ route('member.billing') }}" class="{{ request()->routeIs('member.billing') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-file-text"></span></span> Struk & Tagihan
        </a>
      </li>
      @endif

      @if($userRole === 'owner')
      <li>
        <a href="{{ route('owner.transactions') }}" class="{{ request()->routeIs('owner.transactions') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-shopping-cart"></span></span> Detail Transaksi
        </a>
      </li>
      <li>
        <a href="{{ route('owner.members') }}" class="{{ request()->routeIs('owner.members') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-person"></span></span> Data & Status Member
        </a>
      </li>
      <li>
        <a href="{{ route('owner.classes') }}" class="{{ request()->routeIs('owner.classes') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-calendar"></span></span> Jadwal Kelas & Trainer
        </a>
      </li>
      <li>
        <a href="{{ route('owner.inventory') }}" class="{{ request()->routeIs('owner.inventory') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-settings"></span></span> Stok Produk & Loker
        </a>
      </li>
      <li class="menu-header">Pemantauan & Laporan</li>
      <li>
        <a href="{{ route('owner.staff') }}" class="{{ request()->routeIs('owner.staff') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-people"></span></span> Data Staf
        </a>
      </li>
      <li>
        <a href="{{ route('owner.logs') }}" class="{{ request()->routeIs('owner.logs') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-history"></span></span> Riwayat Aktivitas
        </a>
      </li>
      <li>
        <a href="{{ route('owner.reports') }}" class="{{ request()->routeIs('owner.reports') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-file-text"></span></span> Laporan Keuangan
        </a>
      </li>
      <li>
        <a href="{{ route('owner.settings') }}" class="{{ request()->routeIs('owner.settings') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-settings"></span></span> Pengaturan
        </a>
      </li>
      @endif

      @if(in_array($userRole, ['admin', 'manager', 'receptionist', 'trainer']))
      <li>
        <a href="{{ route('admin.members.index') }}" class="{{ request()->routeIs('admin.members.*') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-person"></span></span> Data Member
        </a>
      </li>
      @endif

      @if($userRole === 'admin')
      <li>
        <a href="{{ route('admin.pos.index') }}" class="{{ request()->routeIs('admin.pos.*') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-shopping-cart"></span></span>Kasir
        </a>
      </li>
      <li>
        <a href="{{ route('admin.lockers.index') }}" class="{{ request()->routeIs('admin.lockers.*') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-settings"></span></span>Loker Gym
        </a>
      </li>
      <li>
        <a href="{{ route('admin.classes.index') }}" class="{{ request()->routeIs('admin.classes.*') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-calendar"></span></span> Kelas & Trainer
        </a>
      </li>
      <li>
        <a href="{{ route('admin.staff.index') }}" class="{{ request()->routeIs('admin.staff.*') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-people"></span></span> Akun Staf
        </a>
      </li>
      <li>
        <a href="{{ route('admin.logs.index') }}" class="{{ request()->routeIs('admin.logs.*') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-history"></span></span> Riwayat Aktivitas
        </a>
      </li>
      <li>
        <a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-file-text"></span></span>Laporan
        </a>
      </li>
      <li>
        <a href="{{ route('admin.settings.index') }}" class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-settings"></span></span> Pengaturan
        </a>
      </li>
      <li>
        <a href="{{ route('admin.landing.edit') }}" class="{{ request()->routeIs('admin.landing.*') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-globe"></span></span> Landing Page
        </a>
      </li>
      @endif

      @if($userRole === 'receptionist')
      <li class="menu-header">Operasional Resepsionis</li>
      <li>
        <a href="{{ route('admin.checkin.index') }}" class="{{ request()->routeIs('admin.checkin.*') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-check"></span></span>Absensi
        </a>
      </li>
      <li>
        <a href="{{ route('admin.pos.index') }}" class="{{ request()->routeIs('admin.pos.*') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-shopping-cart"></span></span>Kasir
        </a>
      </li>
      <li>
        <a href="{{ route('receptionist.lockers') }}" class="{{ request()->routeIs('receptionist.lockers') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-settings"></span></span> Manajemen Loker
        </a>
      </li>
      <li>
        <a href="{{ route('receptionist.guests') }}" class="{{ request()->routeIs('receptionist.guests') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-person"></span></span> Buku Tamu
        </a>
      </li> <li>
        <a href="{{ route('receptionist.shifts') }}" class="{{ request()->routeIs('receptionist.shifts') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-history"></span></span> Shift & Keluhan Staf
        </a>
      </li>
      @endif

      @if($userRole === 'manager')
      <li class="menu-header">Operasional Manager</li>
      <li>
        <a href="/manager/features?tab=class" class="{{ request()->query('tab', 'class') === 'class' && request()->routeIs('manager.features') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-calendar"></span></span>Kelas
        </a>
      </li>
      <li>
        <a href="/manager/features?tab=maintenance" class="{{ request()->query('tab') === 'maintenance' ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-settings"></span></span> Alat & Perawatan
        </a>
      </li>
      <li>
        <a href="/manager/features?tab=shift" class="{{ request()->query('tab') === 'shift' ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-calendar"></span></span> Shift & Cuti
        </a>
      </li>
      <li>
        <a href="/manager/features?tab=approval" class="{{ request()->query('tab') === 'approval' ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-check"></span></span> Kelola Pembatalan
        </a>
      </li>
      <li>
        <a href="/manager/features?tab=promo" class="{{ request()->query('tab') === 'promo' ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-file-text"></span></span> Promo & Voucher
        </a>
      </li>
      <li>
        <a href="/manager/features?tab=performance" class="{{ request()->query('tab') === 'performance' ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-people"></span></span> Kinerja Karyawan
        </a>
      </li>
      <li>
        <a href="/manager/features?tab=report" class="{{ request()->query('tab') === 'report' ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-file-text"></span></span> Rekap Kas & Kehadiran
        </a>
      </li>
      <li>
        <a href="/manager/features?tab=stock" class="{{ request()->query('tab') === 'stock' ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-shopping-cart"></span></span> Stok Ritel
        </a>
      </li>
      <li>
        <a href="/manager/features?tab=complaints" class="{{ request()->query('tab') === 'complaints' ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-person"></span></span> Komplain Pelanggan
        </a>
      </li>
      <li>
        <a href="/manager/features?tab=vendors" class="{{ request()->query('tab') === 'vendors' ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-people"></span></span> Kontak Vendor
        </a>
      </li>
      @endif
    </ul>
  </aside>

  <!-- Main Content Area -->
  <main class="admin-main">
    <div class="top-navbar">
      <div class="page-title-box">
        <h5>@yield('page_title', 'Dashboard ' . ucfirst($userRole))</h5>
        <p>@yield('page_subtitle', 'Sistem Pengelolaan Tenant Gym PetGym')</p>
      </div>

      <div class="user-profile-nav">
        <div class="text-right mr-2">
          <div style="font-weight: 700; color: #111827; font-size: 13.5px;">{{ $currentUser ? $currentUser->name : 'User' }}</div>
          <div style="font-size: 12px; color: #36393f; font-weight: Bold">Role: {{ ucfirst($userRole) }}</div>
        </div>

        <form action="{{ route('logout') }}" method="POST" class="d-inline">
          @csrf
          <button type="submit" class="btn btn-sm btn-outline-danger font-weight-bold" style="border-radius: 8px;">Logout</button>
        </form>
      </div>
    </div>

    <div class="admin-content">
      @include('partials.flash-toast')

      @yield('content')
    </div>
  </main>
</div>

<script src="{{ asset('js/jquery-3.3.1.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
@yield('scripts')

</body>
</html>
