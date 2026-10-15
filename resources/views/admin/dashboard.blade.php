@extends('layouts.layout')

@section('title', 'Dashboard Admin — PetGym')
@section('page_title', 'Beranda')
@section('page_subtitle', 'Ringkasan operasional dan informasi sistem gym')

@section('content')

    <!-- 1. 4 KARTU METRIK UTAMA (FOKUS WEBSITE & SAAS) -->
    @if(($pendingResetCount ?? 0) > 0)
    <div class="alert alert-warning border-0 shadow-sm d-flex justify-content-between align-items-center mb-4 p-3" style="border-radius: 12px; border-left: 5px solid #f59e0b !important;">
        <div class="d-flex align-items-center">
            <span class="icon-lock mr-3 text-warning" style="font-size: 24px;"></span>
            <div>
                <h6 class="mb-0 font-weight-bold text-dark">Ada {{ $pendingResetCount }} Permohonan Reset Password Menunggu Persetujuan Anda!</h6>
                <small class="text-muted">Staf atau Member gym Anda mengajukan reset kata sandi dan membutuhkan persetujuan Admin sebelum tautan dikirim.</small>
            </div>
        </div>
        <a href="{{ route('admin.staff.index') }}" class="btn btn-warning btn-sm font-weight-bold px-3 text-dark shadow-sm" style="border-radius: 8px;">
            Tinjau di Kelola Staf <span class="icon-arrow-right ml-1"></span>
        </a>
    </div>
    @endif
    <div class="row mb-4">
        <!-- Kartu 1: Status & Masa Sewa -->
        <div class="col-xl-3 col-md-6 mb-3 mb-xl-0">
            <div class="card-custom h-100 mb-0">
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
            <div class="card-custom h-100 mb-0">
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
            <div class="card-custom h-100 mb-0">
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Pesan Masuk (Leads)</span>
                            <span class="badge badge-light border text-warning" style="font-size: 11px; border-radius: 6px;">
                                <i class="icon-envelope"></i>
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
            <div class="card-custom h-100 mb-0">
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
                                    <i class="icon-warning mr-1"></i> Maintenance
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

    <!-- MODAL POPUP SELAMAT DATANG & INSTRUKSI PEMBUATAN AKUN (MUNCUL OTOMATIS) -->
    @if(!$ownerUser || !$managerUser)
    <div class="modal fade" id="welcomeSetupModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content shadow-lg border-0" style="border-radius: 16px; overflow: hidden;">
                <div class="modal-header bg-white border-bottom py-3 px-4">
                    <div>
                        <h5 class="modal-title font-weight-bold mb-0 text-dark">Penyiapan Akun Pengelola</h5>
                        <small class="text-muted">Langkah awal untuk memulai operasional gym</small>
                    </div>
                    <button type="button" class="close text-secondary" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-secondary small mb-3">
                        Langkah awal untuk memulai operasional: silakan daftarkan akun pengelola utama berikut.
                    </p>

                    <div class="row">
                        <!-- Item 1: Akun Owner -->
                        <div class="col-md-6 mb-3 mb-md-0">
                            <div class="p-3 border rounded h-100 bg-light" style="border-radius: 10px;">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <h6 class="font-weight-bold text-dark mb-0">Akun Pemilik</h6>
                                    <span class="badge {{ $ownerUser ? 'badge-success' : 'badge-light border text-muted' }} font-weight-bold px-2 py-1" style="font-size: 11px;">
                                        {{ $ownerUser ? 'Terdaftar' : 'Belum Dibuat' }}
                                    </span>
                                </div>
                                <p class="text-muted small mb-0" style="font-size: 12px; line-height: 1.5;">
                                    Untuk pemantauan laporan pendapatan dan pengaturan rekening kasir / QRIS.
                                </p>
                            </div>
                        </div>

                        <!-- Item 2: Akun Manager -->
                        <div class="col-md-6">
                            <div class="p-3 border rounded h-100 bg-light" style="border-radius: 10px;">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <h6 class="font-weight-bold text-dark mb-0">Akun Manager</h6>
                                    <span class="badge {{ $managerUser ? 'badge-success' : 'badge-light border text-muted' }} font-weight-bold px-2 py-1" style="font-size: 11px;">
                                        {{ $managerUser ? 'Terdaftar' : 'Belum Dibuat' }}
                                    </span>
                                </div>
                                <p class="text-muted small mb-0" style="font-size: 12px; line-height: 1.5;">
                                    Untuk pengelolaan operasional harian, jadwal kelas, dan staf lapangan.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-3 px-4 d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary btn-sm font-weight-bold px-3" data-dismiss="modal" style="border-radius: 8px;">
                        Tutup &amp; Lihat Dashboard
                    </button>
                    <a href="{{ route('admin.staff.index') }}" class="btn btn-primary btn-sm font-weight-bold px-4 shadow-sm" style="border-radius: 8px;">
                        <i class="icon-user-plus mr-1"></i> Buat Akun Sekarang &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

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
                                            {!! nl2br(e($anc->message)) !!}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 text-muted small">
                            <span class="icon-notifications_off h4 d-block mb-1 text-muted"></span>
                            Belum ada pengumuman baru.
                        </div>
                    @endif
                </div>
            </div>
        </div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        if ($('#welcomeSetupModal').length) {
            // Tampilkan popup selamat datang secara otomatis
            $('#welcomeSetupModal').modal('show');
        }
    });
</script>
@endsection
