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
      font-family: 'Muli', sans-serif;
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
</head>
<body>

@php
  $currentUser = Auth::user();
  $userRole = $currentUser ? $currentUser->role : 'admin';

  // Tentukan rute dashboard sesuai role
  $dashUrl = route('admin.dashboard');
  if ($userRole === 'manager') {
      $dashUrl = route('manager.dashboard');
  } elseif ($userRole === 'receptionist') {
      $dashUrl = route('receptionist.dashboard');
  } elseif ($userRole === 'trainer') {
      $dashUrl = route('trainer.dashboard');
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
          <span class="icon-wrapper"><span class="icon-dashboard"></span></span> Dashboard {{ ucfirst($userRole) }}
        </a>
      </li>

      <li class="menu-header">Fitur Operasional</li>

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
          <span class="icon-wrapper"><span class="icon-shopping-cart"></span></span> POS Kasir & Struk
        </a>
      </li>
      <li>
        <a href="{{ route('admin.lockers.index') }}" class="{{ request()->routeIs('admin.lockers.*') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-settings"></span></span> Master Loker Gym
        </a>
      </li>
      @endif

      @if($userRole === 'receptionist')
      <li class="menu-header">Operasional Resepsionis</li>
      <li>
        <a href="{{ route('admin.checkin.index') }}" class="{{ request()->routeIs('admin.checkin.*') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-check"></span></span> Check-In / Absensi
        </a>
      </li>
      <li>
        <a href="{{ route('admin.pos.index') }}" class="{{ request()->routeIs('admin.pos.*') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-shopping-cart"></span></span> POS Kasir & Struk
        </a>
      </li>
      <li>
        <a href="{{ route('receptionist.lockers') }}" class="{{ request()->routeIs('receptionist.lockers') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-settings"></span></span> Manajemen Loker
        </a>
      </li>
      <li>
        <a href="{{ route('receptionist.guests') }}" class="{{ request()->routeIs('receptionist.guests') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-person"></span></span> Buku Tamu & Lost Found
        </a>
      </li>
      <li>
        <a href="{{ route('receptionist.shifts') }}" class="{{ request()->routeIs('receptionist.shifts') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-history"></span></span> Shift & Keluhan Staf
        </a>
      </li>
      @endif

      @if(in_array($userRole, ['admin', 'trainer']))
      <li>
        <a href="{{ route('admin.classes.index') }}" class="{{ request()->routeIs('admin.classes.*') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-calendar"></span></span> Kelas & Trainer
        </a>
      </li>
      @endif

      @if($userRole === 'manager')
      <li class="menu-header">Operasional Manager</li>
      <li>
        <a href="/manager/features?tab=class" class="{{ request()->query('tab', 'class') === 'class' && request()->routeIs('manager.features') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-calendar"></span></span> Master Kelas
        </a>
      </li>
      <li>
        <a href="/manager/features?tab=maintenance" class="{{ request()->query('tab') === 'maintenance' ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-settings"></span></span> Alat & Maintenance
        </a>
      </li>
      <li>
        <a href="/manager/features?tab=shift" class="{{ request()->query('tab') === 'shift' ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-calendar"></span></span> Shift & Cuti
        </a>
      </li>
      <li>
        <a href="/manager/features?tab=approval" class="{{ request()->query('tab') === 'approval' ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-check"></span></span> Otorisasi Void
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
          <span class="icon-wrapper"><span class="icon-person"></span></span> Komplain Member
        </a>
      </li>
      <li>
        <a href="/manager/features?tab=vendors" class="{{ request()->query('tab') === 'vendors' ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-people"></span></span> Kontak Vendor
        </a>
      </li>
      @endif

      @if(in_array($userRole, ['admin', 'manager']))
      <li class="menu-header">Manajemen Internal</li>
      @endif

      @if($userRole === 'admin')
      <li>
        <a href="{{ route('admin.staff.index') }}" class="{{ request()->routeIs('admin.staff.*') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-people"></span></span> Akun Staf (RBAC)
        </a>
      </li>
      @endif

      @if(in_array($userRole, ['admin', 'manager']))
      <li>
        <a href="{{ route('admin.logs.index') }}" class="{{ request()->routeIs('admin.logs.*') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-history"></span></span> Audit Trail Log
        </a>
      </li>
      @endif

      @if(in_array($userRole, ['admin', 'manager']))
      <li>
        <a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-file-text"></span></span> Ekspor Laporan
        </a>
      </li>
      @endif

      @if($userRole === 'admin')
      <li>
        <a href="{{ route('admin.settings.index') }}" class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
          <span class="icon-wrapper"><span class="icon-settings"></span></span> Pengaturan Gym
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
          <div style="font-size: 11px; color: #6b7280;">Role: {{ ucfirst($userRole) }}</div>
        </div>

        <form action="{{ route('logout') }}" method="POST" class="d-inline">
          @csrf
          <button type="submit" class="btn btn-sm btn-outline-danger font-weight-bold" style="border-radius: 8px;">Logout</button>
        </form>
      </div>
    </div>

    <div class="admin-content">
      @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 10px;">
          <strong>Sukses!</strong> {{ session('success') }}
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      @endif

      @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 10px;">
          <strong>Peringatan!</strong> {{ session('error') }}
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      @endif

      @yield('content')
    </div>
  </main>
</div>

<script src="{{ asset('js/jquery-3.3.1.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
@yield('scripts')

</body>
</html>
