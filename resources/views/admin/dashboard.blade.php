@extends('layouts.layout')

@section('title', 'Dashboard Admin — PetGym')
@section('page_title', 'Dashboard Website & Pengelolaan')
@section('page_subtitle', 'Pantau performa website gym, status masa sewa SaaS, dan pengumuman sistem')

@section('content')
<div class="container-fluid py-4">

    <!-- 1. 4 KARTU METRIK UTAMA (FOKUS WEBSITE & SAAS) -->
    <div class="row mb-4">
        <!-- Kartu 1: Status & Masa Sewa -->
        <div class="col-xl-3 col-md-6 mb-3 mb-xl-0">
            <div class="card border-0 shadow-sm h-100 bg-white" style="border-radius: 12px;">
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Status & Masa Sewa</span>
                            <span class="badge badge-light border text-primary" style="font-size: 11px; border-radius: 6px;">
                                <i class="icon-credit-card"></i>
                            </span>
                        </div>
                        <h4 class="font-weight-bold text-dark mb-1">
                            {{ $plan->name ?? ($tenant->plan_name ?? 'Paket Pro') }}
                        </h4>
                        <div class="mb-2">
                            @if($tenant && $tenant->status === 'active' && $tenant->expires_in_days > 7)
                                <span class="badge badge-success font-weight-bold px-2 py-1" style="font-size: 11px; border-radius: 6px;">
                                    <i class="icon-check mr-1"></i> Aktif ({{ $tenant->expires_in_days }} Hari Lagi)
                                </span>
                            @elseif($tenant && $tenant->status === 'active' && $tenant->expires_in_days >= 0)
                                <span class="badge badge-warning text-dark font-weight-bold px-2 py-1" style="font-size: 11px; border-radius: 6px;">
                                    <i class="icon-clock-o mr-1"></i> Sisa {{ $tenant->expires_in_days }} Hari
                                </span>
                            @else
                                <span class="badge badge-danger font-weight-bold px-2 py-1" style="font-size: 11px; border-radius: 6px;">
                                    <i class="icon-close mr-1"></i> Habis Masa Aktif
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="pt-2 border-top">
                        <a href="{{ route('admin.subscription.index') }}" class="text-primary font-weight-bold small text-decoration-none d-flex align-items-center justify-content-between">
                            <span>Perpanjang Sewa</span>
                            <i class="icon-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kartu 2: Pengunjung Web -->
        <div class="col-xl-3 col-md-6 mb-3 mb-xl-0">
            <div class="card border-0 shadow-sm h-100 bg-white" style="border-radius: 12px;">
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Pengunjung Web</span>
                            <span class="badge badge-light border text-info" style="font-size: 11px; border-radius: 6px;">
                                <i class="icon-eye"></i>
                            </span>
                        </div>
                        <h4 class="font-weight-bold text-dark mb-1">
                            {{ number_format($websiteVisits, 0, ',', '.') }}
                            <small class="text-muted font-weight-normal" style="font-size: 13px;">Kunjungan</small>
                        </h4>
                        <div class="mb-2">
                            <span class="text-success font-weight-bold small">
                                <i class="icon-arrow-up mr-1"></i> +{{ $visitsGrowth }}% bulan ini
                            </span>
                        </div>
                    </div>
                    <div class="pt-2 border-top text-muted small">
                        Trafik landing page publik gym
                    </div>
                </div>
            </div>
        </div>

        <!-- Kartu 3: Pesan Masuk (Leads) -->
        <div class="col-xl-3 col-md-6 mb-3 mb-md-0">
            <div class="card border-0 shadow-sm h-100 bg-white" style="border-radius: 12px;">
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Pesan Masuk (Leads)</span>
                            <span class="badge badge-light border text-warning" style="font-size: 11px; border-radius: 6px;">
                                <i class="icon-mail"></i>
                            </span>
                        </div>
                        <h4 class="font-weight-bold text-dark mb-1">
                            {{ $unreadLeadsCount }}
                            <small class="text-muted font-weight-normal" style="font-size: 13px;">Pesan Baru</small>
                        </h4>
                        <div class="mb-2">
                            <span class="badge badge-warning text-dark font-weight-bold px-2 py-1" style="font-size: 11px; border-radius: 6px;">
                                Belum Dikonversi
                            </span>
                        </div>
                    </div>
                    <div class="pt-2 border-top text-muted small">
                        Calon klien formulir kontak web
                    </div>
                </div>
            </div>
        </div>

        <!-- Kartu 4: Status Landing Page -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 bg-white" style="border-radius: 12px;">
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Status Landing Page</span>
                            <span class="badge badge-light border text-success" style="font-size: 11px; border-radius: 6px;">
                                <i class="icon-globe"></i>
                            </span>
                        </div>
                        <h4 class="font-weight-bold text-dark mb-1">
                            @if($tenant && $tenant->status === 'active')
                                <span class="text-success font-weight-bold" style="font-size: 18px;">
                                    <i class="icon-check-circle mr-1"></i> Online (Live)
                                </span>
                            @else
                                <span class="text-danger font-weight-bold" style="font-size: 18px;">
                                    <i class="icon-alert-triangle mr-1"></i> Maintenance
                                </span>
                            @endif
                        </h4>
                        <div class="mb-2 text-truncate" style="max-width: 100%;">
                            <small class="text-muted font-weight-bold">{{ $tenant->subdomain ?? 'website.domain' }}</small>
                        </div>
                    </div>
                    <div class="pt-2 border-top">
                        <a href="{{ $landingUrl }}" target="_blank" class="text-primary font-weight-bold small text-decoration-none d-flex align-items-center justify-content-between">
                            <span>Buka Landing Page</span>
                            <i class="icon-external-link"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. STATUS PENYIAPAN AKUN PENGELOLA (OWNER & MANAGER) -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-white" style="border-radius: 14px;">
                <div class="card-header bg-white border-bottom pt-4 px-4 pb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h6 class="font-weight-bold text-dark mb-1">
                            <i class="icon-people text-primary mr-1"></i> Penyiapan Akun Pengelola Utama (Owner &amp; Manager)
                        </h6>
                        <small class="text-muted">Sebagai Admin, Anda bertugas membuat akun Owner (pemantauan &amp; rekening kasir) dan Manager (operasional &amp; staf).</small>
                    </div>
                    <a href="{{ route('admin.staff.index') }}" class="btn btn-sm btn-primary font-weight-bold px-3 py-2" style="border-radius: 8px;">
                        <i class="icon-plus mr-1"></i> Buka Manajemen Akun
                    </a>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <!-- Akun Owner -->
                        <div class="col-md-6 mb-3 mb-md-0">
                            <div class="p-3 border rounded h-100 d-flex flex-column justify-content-between {{ $ownerUser ? 'bg-light-success border-success' : 'bg-light border-warning' }}" style="border-radius: 10px;">
                                <div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge {{ $ownerUser ? 'badge-success' : 'badge-warning text-dark' }} font-weight-bold px-2 py-1" style="font-size: 11px; border-radius: 6px;">
                                            {{ $ownerUser ? '✓ Akun Owner Aktif' : '⚠ Belum Ada Akun Owner' }}
                                        </span>
                                        <small class="text-muted font-weight-bold">Peran: Owner</small>
                                    </div>
                                    <h5 class="font-weight-bold text-dark mb-1">
                                        {{ $ownerUser ? $ownerUser->name : 'Pemilik Gym (Owner)' }}
                                    </h5>
                                    <p class="text-muted small mb-3">
                                        @if($ownerUser)
                                            Login: <strong>{{ $ownerUser->email }}</strong> &bull; Memiliki hak pantau laporan omset, audit trail, serta pengaturan rekening bank &amp; QRIS kasir.
                                        @else
                                            Belum ada akun Owner terdaftar. Buat akun ini agar pemilik gym dapat login memantau bisnis dan memasukkan nomor rekening/QRIS untuk kasir.
                                        @endif
                                    </p>
                                </div>
                                <div>
                                    @if($ownerUser)
                                        <a href="{{ route('admin.staff.index') }}" class="btn btn-sm btn-outline-success font-weight-bold btn-block" style="border-radius: 6px;">
                                            Kelola Akun Owner
                                        </a>
                                    @else
                                        <a href="{{ route('admin.staff.index') }}" class="btn btn-sm btn-warning font-weight-bold btn-block text-dark" style="border-radius: 6px;">
                                            + Buat Akun Owner Sekarang
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Akun Manager -->
                        <div class="col-md-6">
                            <div class="p-3 border rounded h-100 d-flex flex-column justify-content-between {{ $managerUser ? 'bg-light-success border-success' : 'bg-light border-info' }}" style="border-radius: 10px;">
                                <div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge {{ $managerUser ? 'badge-success' : 'badge-info text-white' }} font-weight-bold px-2 py-1" style="font-size: 11px; border-radius: 6px;">
                                            {{ $managerUser ? '✓ Akun Manager Aktif' : 'ℹ Belum Ada Akun Manager' }}
                                        </span>
                                        <small class="text-muted font-weight-bold">Peran: Manager</small>
                                    </div>
                                    <h5 class="font-weight-bold text-dark mb-1">
                                        {{ $managerUser ? $managerUser->name : 'Manager Gym' }}
                                    </h5>
                                    <p class="text-muted small mb-3">
                                        @if($managerUser)
                                            Login: <strong>{{ $managerUser->email }}</strong> &bull; Mengelola operasional harian, master kelas &amp; trainer, approval member, serta merekrut Supervisor/Kasir/PT.
                                        @else
                                            Belum ada akun Manager terdaftar. Buat akun Manager untuk memimpin operasional, menyetujui member, dan mengelola staf lapangan.
                                        @endif
                                    </p>
                                </div>
                                <div>
                                    @if($managerUser)
                                        <a href="{{ route('admin.staff.index') }}" class="btn btn-sm btn-outline-success font-weight-bold btn-block" style="border-radius: 6px;">
                                            Kelola Akun Manager
                                        </a>
                                    @else
                                        <a href="{{ route('admin.staff.index') }}" class="btn btn-sm btn-primary font-weight-bold btn-block" style="border-radius: 6px;">
                                            + Buat Akun Manager Sekarang
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. PENGUMUMAN & NOTIFIKASI SISTEM (FULL WIDTH HORIZONTAL) -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-white" style="border-radius: 14px;">
                <div class="card-header bg-white border-bottom pt-4 px-4 pb-3">
                    <h6 class="font-weight-bold text-dark mb-0">Pengumuman &amp; Informasi Sistem</h6>
                    <small class="text-muted">Pemberitahuan resmi jadwal pemeliharaan dan pembaruan sistem</small>
                </div>
                <div class="card-body p-4">
                    @if(count($announcements) > 0)
                        <div class="row">
                            @foreach($announcements as $anc)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="p-3 border-left border-primary bg-light rounded h-100" style="border-left-width: 4px !important; border-radius: 8px;">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="font-weight-bold text-dark" style="font-size: 13.5px;">{{ $anc->title }}</span>
                                            <small class="text-muted font-weight-bold" style="font-size: 11px;">
                                                {{ $anc->created_at ? $anc->created_at->format('d M Y') : '' }}
                                            </small>
                                        </div>
                                        <div class="text-muted small" style="line-height: 1.5;">
                                            {!! $anc->message !!}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 text-muted small">
                            <span class="icon-bell-off h4 d-block mb-1 text-muted"></span>
                            Belum ada pengumuman baru.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
