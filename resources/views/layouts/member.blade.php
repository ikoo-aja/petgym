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

    /* ── Sub Navigation Menu Bar ── */
    .member-menu-bar {
      background: #ffffff;
      border-bottom: 1px solid var(--mp-border);
      padding: 0 30px;
    }
    .member-nav-tabs {
      display: flex;
      gap: 8px;
      list-style: none;
      margin: 0;
      padding: 0;
      overflow-x: auto;
    }
    .member-nav-tabs .nav-item a {
      display: flex;
      align-items: center;
      padding: 14px 18px;
      color: var(--mp-text-muted);
      font-weight: 600;
      font-size: 13.5px;
      text-decoration: none;
      border-bottom: 3px solid transparent;
      transition: all 0.2s ease;
      white-space: nowrap;
    }
    .member-nav-tabs .nav-item a:hover {
      color: var(--brand-red);
      background: rgba(244, 63, 94, 0.03);
    }
    .member-nav-tabs .nav-item a.active {
      color: var(--brand-red);
      border-bottom-color: var(--brand-red);
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

<!-- Member Top Navigation Bar -->
<header class="member-top-nav">
  <div class="d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center gap-3">
      <a href="{{ route('member.dashboard') }}" class="brand-logo-text">
        PetGym <span class="badge badge-primary font-weight-bold ml-1" style="font-size:10px;">MEMBER PORTAL</span>
      </a>
      <span class="text-muted small d-none d-md-inline border-left pl-3 ml-2">{{ $tenantName }}</span>
    </div>

    <div class="d-flex align-items-center gap-3">
      <div class="member-profile-pill mr-2">
        <div class="member-avatar-circle">{{ substr($currentUser->name ?? 'M', 0, 1) }}</div>
        <div style="line-height: 1.2;">
          <strong style="font-size: 12.5px;" class="text-dark d-block">{{ $currentUser->name ?? 'Member' }}</strong>
          <span class="text-muted" style="font-size: 10.5px;">@yield('member_tier_badge', 'Member Gym')</span>
        </div>
      </div>

      <form action="{{ route('logout') }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-sm btn-outline-danger font-weight-bold" style="border-radius: 8px;">Logout</button>
      </form>
    </div>
  </div>
</header>

<!-- Member Sub Menu Tabs -->
<nav class="member-menu-bar">
  <ul class="member-nav-tabs">
    <li class="nav-item">
      <a href="{{ route('member.dashboard') }}" class="{{ request()->routeIs('member.dashboard') ? 'active' : '' }}">
        Beranda
      </a>
    </li>
    <li class="nav-item">
      <a href="{{ route('member.lockers') }}" class="{{ request()->routeIs('member.lockers') ? 'active' : '' }}">
        Loker Saya
      </a>
    </li>
    <li class="nav-item">
      <a href="{{ route('member.membership') }}" class="{{ request()->routeIs('member.membership') ? 'active' : '' }}">
        Keanggotaan
      </a>
    </li>
    <li class="nav-item">
      <a href="{{ route('member.pt') }}" class="{{ request()->routeIs('member.pt') ? 'active' : '' }}">
        Personal Trainer
      </a>
    </li>
    <li class="nav-item">
      <a href="{{ route('member.classes') }}" class="{{ request()->routeIs('member.classes') ? 'active' : '' }}">
        Kelas Kebugaran
      </a>
    </li>
    <li class="nav-item">
      <a href="{{ route('member.billing') }}" class="{{ request()->routeIs('member.billing') ? 'active' : '' }}">
        Riwayat Tagihan
      </a>
    </li>
    <li class="nav-item">
      <a href="{{ route('member.guide') }}" class="{{ request()->routeIs('member.guide') ? 'active' : '' }}">
        Panduan
      </a>
    </li>
    <li class="nav-item">
      <a href="{{ route('member.settings') }}" class="{{ request()->routeIs('member.settings') ? 'active' : '' }}">
        Pengaturan
      </a>
    </li>
  </ul>
</nav>

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
@yield('scripts')

</body>
</html>
