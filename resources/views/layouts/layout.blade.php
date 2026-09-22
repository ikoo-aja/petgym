@php
  $currentUser = Auth::user();
  $userRole = $currentUser ? $currentUser->role : 'admin';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
  <title>@yield('title', ($currentUser && $currentUser->tenant ? $currentUser->tenant->name : 'Gym Portal'))</title>
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
  } elseif ($userRole === 'supervisor') {
      $dashUrl = route('supervisor.dashboard');
  } elseif ($userRole === 'receptionist') {
      $dashUrl = route('receptionist.dashboard');
  } elseif ($userRole === 'trainer') {
      $dashUrl = route('trainer.dashboard');
  } elseif ($userRole === 'member') {
      $dashUrl = route('member.dashboard');
  }
  $tenantObj = $currentUser ? $currentUser->tenant : null;
  $tenantLandingSettings = $tenantObj ? $tenantObj->landingSettings() : null;
  $displayMode = $tenantLandingSettings ? ($tenantLandingSettings->brand_display_mode ?? 'both') : 'both';
  $hasLogo = $tenantObj && $tenantObj->logo_url;
  $tenantName = $tenantObj ? $tenantObj->name : 'Gym Portal';
  $tenantLandingUrl = $tenantObj ? $tenantObj->publicLandingUrl() : '/';
@endphp

<div class="admin-wrapper">
  <!-- Dynamic Sidebar per Role -->
  <aside class="admin-sidebar">
    <div class="sidebar-brand d-flex flex-column align-items-center gap-1 p-3">
      <a href="{{ $tenantLandingUrl }}" target="_blank" class="d-block text-center w-100 mb-1 text-decoration-none" title="Buka Landing Page Gym">
        @if(($displayMode === 'logo' || $displayMode === 'both') && $hasLogo)
          <img src="{{ asset($tenantObj->logo_url) }}?v={{ time() }}" alt="{{ $tenantName }}" style="max-height: 55px; max-width: 190px; object-fit: contain; border-radius: 8px;">
        @endif
        @if($displayMode === 'text' || ($displayMode === 'both' && !$hasLogo) || ($displayMode === 'logo' && !$hasLogo))
          <div class="font-weight-extrabold text-white text-center py-2 px-3 rounded" style="font-size: 16px; letter-spacing: 0.5px; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15);">
            <i class="icon-fitness_center text-danger mr-1"></i> {{ $tenantName }}
          </div>
        @elseif($displayMode === 'both' && $hasLogo)
          <div class="font-weight-bold text-white text-center mt-2" style="font-size: 14px;">{{ $tenantName }}</div>
        @endif
      </a>
      <a href="{{ $tenantLandingUrl }}" target="_blank" class="tenant-badge text-decoration-none" style="font-size: 12px;" title="Buka Landing Page Gym">{{ $tenantName }} <i class="icon-external-link ml-1" style="font-size: 10px;"></i></a>
    </div>

    <ul class="sidebar-menu">
      <li class="menu-header">Utama</li>
      <li>
        <a href="{{ $dashUrl }}" class="{{ request()->routeIs('*.dashboard') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-dashboard"></span></span> Beranda {{ ucfirst($userRole) }}
        </a>
      </li>

      @if(in_array($userRole, ['member', 'owner', 'admin']))
      <li class="menu-header">Operasional</li>
      @endif

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
        <a href="{{ route('owner.performance') }}" class="{{ request()->routeIs('owner.performance*') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-people"></span></span> Kinerja Karyawan
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

      @if($userRole === 'admin')
      <li>
        <a href="{{ route('admin.subscription.index') }}" class="{{ request()->routeIs('admin.subscription.*') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-credit-card"></span></span> Langganan website
        </a>
      </li>
      <li>
        <a href="{{ route('admin.staff.index') }}" class="{{ request()->routeIs('admin.staff.*') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-people"></span></span> Akun Staf
        </a>
      </li>
      <li>
        <a href="{{ route('account.settings') }}" class="{{ request()->routeIs('account.settings*') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-person"></span></span> Pengaturan Akun
        </a>
      </li>
      <li>
        <a href="{{ route('admin.landing.edit') }}" class="{{ request()->routeIs('admin.landing.*') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-globe"></span></span> Pengaturan Web
        </a>
      </li>
      @endif

      @if($userRole === 'receptionist')
      <li class="menu-header">Operasional Resepsionis</li>
      <li>
        <a href="{{ route('manager.members.index') }}" class="{{ request()->routeIs('manager.members.*') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-person"></span></span> Data Member
        </a>
      </li>
      <li>
        <a href="{{ route('receptionist.checkin.index') }}" class="{{ request()->routeIs('receptionist.checkin.*') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-check"></span></span> Presensi Member
        </a>
      </li>
      <li>
        <a href="{{ route('receptionist.pos.index') }}" class="{{ request()->routeIs('receptionist.pos.*') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-shopping-cart"></span></span> Kasir
        </a>
      </li>
      <li>
        <a href="{{ route('receptionist.lockers') }}" class="{{ request()->routeIs('receptionist.lockers*') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-settings"></span></span> Manajemen Loker
        </a>
      </li>
      <li>
        <a href="{{ route('receptionist.guests') }}" class="{{ request()->routeIs('receptionist.guests*') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-person"></span></span> Buku Tamu
        </a>
      </li>
      <li>
        <a href="{{ route('receptionist.lost-found') }}" class="{{ request()->routeIs('receptionist.lost-found*') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-search"></span></span> Barang Tertinggal
        </a>
      </li>
      <li>
        <a href="{{ route('receptionist.shifts') }}" class="{{ request()->routeIs('receptionist.shifts*') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-history"></span></span> Shift Kasir
        </a>
      </li>
      <li>
        <a href="{{ route('receptionist.complaints') }}" class="{{ request()->routeIs('receptionist.complaints*') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-chat"></span></span> Keluhan Member
        </a>
      </li>
      @endif

      @if($userRole === 'manager')
      <li class="menu-header">Manajerial & Strategis</li>
      <li>
        <a href="{{ route('manager.members.index') }}" class="{{ request()->routeIs('manager.members.*') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-person"></span></span> Data Member
        </a>
      </li>
      <li>
        <a href="{{ route('manager.staff.index') }}" class="{{ request()->routeIs('manager.staff.*') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-people"></span></span> Data Staf
        </a>
      </li>
      <li>
        <a href="{{ route('manager.classes.index') }}" class="{{ request()->routeIs('manager.classes.*') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-calendar"></span></span> Jadwal Kelas & Trainer
        </a>
      </li>
      <li>
        <a href="/manager/features?tab=classes" class="{{ request()->is('manager/features*') && (request()->query('tab', 'classes') === 'classes') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-calendar"></span></span> Master Kelas Rencana
        </a>
      </li>
      <li>
        <a href="/manager/features?tab=promo" class="{{ request()->is('manager/features*') && (request()->query('tab') === 'promo') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-file-text"></span></span> Promo & Voucher
        </a>
      </li>
      <li>
        <a href="/manager/features?tab=performance" class="{{ request()->is('manager/features*') && (request()->query('tab') === 'performance') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-people"></span></span> Kinerja Karyawan
        </a>
      </li>
      <li>
        <a href="/manager/features?tab=cash" class="{{ request()->is('manager/features*') && (request()->query('tab') === 'cash') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-file-text"></span></span> Rekap Kas Keuangan
        </a>
      </li>
      <li>
        <a href="/manager/features?tab=vendor" class="{{ request()->is('manager/features*') && (request()->query('tab') === 'vendor') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-people"></span></span> Database Vendor Mitra
        </a>
      </li>
      <li class="menu-header">Pengaturan</li>
      <li>
        <a href="{{ route('account.settings') }}" class="{{ request()->routeIs('account.settings*') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-settings"></span></span> Pengaturan Akun
        </a>
      </li>
      @endif

      @if($userRole === 'supervisor')
      <li class="menu-header">Operasional Lapangan</li>
      <li>
        <a href="/supervisor/features?tab=void" class="{{ request()->is('supervisor/features*') && (request()->query('tab', 'void') === 'void') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-check"></span></span> Otorisasi Pembatalan Kasir
        </a>
      </li>
      <li>
        <a href="/supervisor/features?tab=shift" class="{{ request()->is('supervisor/features*') && (request()->query('tab') === 'shift') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-calendar"></span></span> Shift dan Cuti Staf
        </a>
      </li>
      <li>
        <a href="/supervisor/features?tab=equipment" class="{{ request()->is('supervisor/features*') && (request()->query('tab') === 'equipment') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-settings"></span></span> Aset dan Alat Gym
        </a>
      </li>
      <li>
        <a href="/supervisor/features?tab=stock" class="{{ request()->is('supervisor/features*') && (request()->query('tab') === 'stock') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-shopping-cart"></span></span> Peringatan Stok Barang
        </a>
      </li>
      <li>
        <a href="/supervisor/features?tab=complaint" class="{{ request()->is('supervisor/features*') && (request()->query('tab') === 'complaint') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-person"></span></span> Komplain Member
        </a>
      </li>
      <li>
        <a href="/supervisor/features?tab=locker" class="{{ request()->is('supervisor/features*') && (request()->query('tab') === 'locker') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-settings"></span></span> Master Loker Gym
        </a>
      </li>
      @endif

      @if($userRole === 'trainer')
      <li class="menu-header">Operasional Pelatih</li>
      <li>
        <a href="{{ route('trainer.pt-sessions') }}" class="{{ request()->routeIs('trainer.pt-sessions*') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-person"></span></span> Booking Sesi PT
        </a>
      </li>
      <li>
        <a href="{{ route('trainer.classes') }}" class="{{ request()->routeIs('trainer.classes*') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-calendar"></span></span> Penugasan Kelas
        </a>
      </li>
      <li>
        <a href="{{ route('trainer.rsvps') }}" class="{{ request()->routeIs('trainer.rsvps*') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-people"></span></span> Peserta RSVP Kelas
        </a>
      </li>
      <li class="menu-header">Pengaturan</li>
      <li>
        <a href="{{ route('account.settings') }}" class="{{ request()->routeIs('account.settings*') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-settings"></span></span> Pengaturan Akun
        </a>
      </li>
      @endif
    </ul>
  </aside>

  <!-- Main Content Area -->
  <main class="admin-main">
    <div class="top-navbar">
      <div class="page-title-box">
        <h5>@yield('page_title', 'Beranda ' . ucfirst($userRole))</h5>
        <p>@yield('page_subtitle', 'Sistem Pengelolaan Gym')</p>
      </div>

      <div class="user-profile-nav d-flex align-items-center">
        <div class="text-right mr-3">
          <div style="font-weight: 700; color: #111827; font-size: 13.5px;">{{ $currentUser ? $currentUser->name : 'Pengguna' }}</div>
          <div style="font-size: 11.5px; color: #6b7280; font-weight: 700;">Peran: {{ ucfirst($userRole) }}</div>
        </div>

        @php
          $receptionistOpenShift = null;
          if ($userRole === 'receptionist' && $currentUser && $currentUser->tenant_id) {
              $receptionistOpenShift = \App\Models\ReceptionistShift::where('tenant_id', $currentUser->tenant_id)
                  ->where('user_id', $currentUser->id)
                  ->where('status', 'open')
                  ->first();
          }
        @endphp

        @if($receptionistOpenShift)
          <button type="button" class="btn btn-sm btn-outline-danger font-weight-bold" data-toggle="modal" data-target="#receptionistCloseShiftModal" style="border-radius: 8px; padding: 6px 14px;">
            <span class="icon-power mr-1"></span> Keluar
          </button>
        @else
          <form action="{{ route('logout') }}" method="POST" class="d-inline m-0">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-danger font-weight-bold" style="border-radius: 8px; padding: 6px 14px;">
              <span class="icon-power mr-1"></span> Keluar
            </button>
          </form>
        @endif
      </div>
    </div>

    <div class="admin-content">
       @include('partials.flash-toast')

       @yield('content')
     </div>
  </main>
</div>

@if($receptionistOpenShift)
<!-- Modal Tutup Shift Kasir Sebelum Logout -->
<div class="modal fade" id="receptionistCloseShiftModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <form action="{{ route('receptionist.shifts.close-logout') }}" method="POST" class="modal-content shadow border-0" style="border-radius: 12px;">
      @csrf
      <div class="modal-header bg-light border-bottom">
        <h5 class="modal-title font-weight-bold text-dark mb-0">Tutup Shift Kasir & Setoran Akhir</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body p-4">
        <div class="alert alert-warning border-0 mb-3" style="background-color: #fffbeb; color: #b45309;">
          <strong>Pemberitahuan Serah Terima Kas:</strong> Shift kasir Anda saat ini sedang aktif (dibuka sejak <strong>{{ $receptionistOpenShift->opened_at->format('H:i') }} WIB</strong> dengan kas awal <strong>Rp {{ number_format($receptionistOpenShift->start_cash, 0, ',', '.') }}</strong>). Sebelum keluar dari sistem, Anda wajib memasukkan nominal uang fisik kas akhir di laci.
        </div>

        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark small mb-1">Nominal Setoran Kas Fisik Akhir (Rp) *</label>
          <div class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text font-weight-bold">Rp</span>
            </div>
            <input type="number" name="end_cash" class="form-control font-weight-bold" placeholder="Hitung seluruh uang fisik di laci kasir" required min="0">
          </div>
          <small class="text-muted">Hitung seluruh uang tunai fisik yang ada di laci kasir saat ini untuk diserahterimakan.</small>
        </div>
      </div>
      <div class="modal-footer bg-light border-top d-flex justify-content-between">
        <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-danger font-weight-bold">
          Tutup Shift dan Keluar
        </button>
      </div>
    </form>
  </div>
</div>
@endif

<script src="{{ asset('js/jquery-3.3.1.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
@include('partials.confirm-modal')
@include('partials.toast-helper')

@yield('scripts')

</body>
</html>
