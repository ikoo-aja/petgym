@extends('layouts.layout')

@section('title', 'Dashboard Admin — PetGym')
@section('page_title', 'Dashboard Website & Pengelolaan')
@section('page_subtitle', 'Pantau performa website gym, status masa sewa SaaS, pesan prospek, dan kendali cepat landing page')

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

    <!-- 2. KONTEN UTAMA: 2 BAGIAN SEIMBANG -->
    <div class="row">
        
        <!-- ========================================== -->
        <!-- SISI KIRI: KENDALI CEPAT WEBSITE & PESAN MASUK -->
        <!-- ========================================== -->
        <div class="col-lg-6 mb-4 mb-lg-0">
            
            <!-- Card Akses Cepat Pengelolaan Halaman -->
            <div class="card border-0 shadow-sm mb-4 bg-white" style="border-radius: 14px;">
                <div class="card-header bg-white border-bottom pt-4 px-4 pb-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="font-weight-bold text-dark mb-0">Akses Cepat Pengelolaan Website</h6>
                        <small class="text-muted">Kendali ringkas pembaruan konten dan tampilan website publik</small>
                    </div>
                    <a href="{{ $landingUrl }}" target="_blank" class="btn btn-sm btn-outline-primary font-weight-bold px-3" style="border-radius: 8px;">
                        <i class="icon-external-link mr-1"></i> Buka Website
                    </a>
                </div>
                <div class="card-body p-4">
                    
                    <!-- Indikator Kelengkapan Data Web -->
                    <div class="p-3 bg-light rounded border mb-4" style="border-radius: 10px;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="font-weight-bold text-dark small">Kelengkapan Konten Website</span>
                            <span class="badge {{ $completenessPercent >= 80 ? 'badge-success' : 'badge-primary' }} font-weight-bold px-2 py-1" style="border-radius: 6px;">
                                {{ $completenessPercent }}% Lengkap
                            </span>
                        </div>
                        <div class="progress mb-2" style="height: 8px; border-radius: 4px; background-color: #e2e8f0;">
                            <div class="progress-bar {{ $completenessPercent >= 80 ? 'bg-success' : 'bg-primary' }}" role="progressbar" style="width: {{ $completenessPercent }}%;"></div>
                        </div>
                        <small class="text-muted d-block" style="font-size: 11.5px;">
                            <i class="icon-info mr-1 text-primary"></i> Lengkapi logo, profil, alamat, dan kontak agar calon member lebih percaya.
                        </small>
                    </div>

                    <!-- Tombol Pintas Pengelolaan Bagian -->
                    <label class="font-weight-bold text-dark small mb-2 d-block text-uppercase text-muted" style="letter-spacing: 0.5px; font-size: 11px;">
                        Pintas Pengaturan Konten
                    </label>
                    <div class="row" style="gap: 10px 0;">
                        <div class="col-sm-6 mb-2">
                            <a href="{{ route('admin.landing.edit') }}#section-hero" class="btn btn-light border btn-block text-left py-2 px-3 d-flex align-items-center justify-content-between shadow-none hover-shadow" style="border-radius: 8px;">
                                <div class="d-flex align-items-center">
                                    <span class="icon-image text-primary mr-2" style="font-size: 16px;"></span>
                                    <span class="font-weight-bold text-dark small">Edit Banner Utama</span>
                                </div>
                                <i class="icon-chevron-right text-muted small"></i>
                            </a>
                        </div>
                        <div class="col-sm-6 mb-2">
                            <a href="{{ route('admin.landing.edit') }}#section-contact" class="btn btn-light border btn-block text-left py-2 px-3 d-flex align-items-center justify-content-between shadow-none hover-shadow" style="border-radius: 8px;">
                                <div class="d-flex align-items-center">
                                    <span class="icon-phone text-primary mr-2" style="font-size: 16px;"></span>
                                    <span class="font-weight-bold text-dark small">Ubah Kontak & Alamat</span>
                                </div>
                                <i class="icon-chevron-right text-muted small"></i>
                            </a>
                        </div>
                        <div class="col-sm-6 mb-2">
                            <a href="{{ route('admin.landing.edit') }}#section-visibility" class="btn btn-light border btn-block text-left py-2 px-3 d-flex align-items-center justify-content-between shadow-none hover-shadow" style="border-radius: 8px;">
                                <div class="d-flex align-items-center">
                                    <span class="icon-sliders text-primary mr-2" style="font-size: 16px;"></span>
                                    <span class="font-weight-bold text-dark small">Atur Tampilan Section</span>
                                </div>
                                <i class="icon-chevron-right text-muted small"></i>
                            </a>
                        </div>
                        <div class="col-sm-6 mb-2">
                            <a href="{{ route('admin.landing.edit') }}#gym-identity" class="btn btn-light border btn-block text-left py-2 px-3 d-flex align-items-center justify-content-between shadow-none hover-shadow" style="border-radius: 8px;">
                                <div class="d-flex align-items-center">
                                    <span class="icon-tag text-primary mr-2" style="font-size: 16px;"></span>
                                    <span class="font-weight-bold text-dark small">Logo & Identitas Gym</span>
                                </div>
                                <i class="icon-chevron-right text-muted small"></i>
                            </a>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Card Pesan Masuk Terbaru (Form Kontak Landing Page) -->
            <div class="card border-0 shadow-sm bg-white" style="border-radius: 14px;">
                <div class="card-header bg-white border-bottom pt-4 px-4 pb-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="font-weight-bold text-dark mb-0">Pesan Masuk Terbaru</h6>
                        <small class="text-muted">Daftar calon klien dari formulir kontak website</small>
                    </div>
                    <span class="badge badge-light border text-muted font-weight-bold px-2 py-1" style="border-radius: 6px; font-size: 11px;">
                        {{ count($recentLeads) }} Kontak Terkini
                    </span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3 text-dark font-weight-bold small">Nama Pengirim</th>
                                    <th class="py-3 text-dark font-weight-bold small">Kontak</th>
                                    <th class="py-3 text-dark font-weight-bold small">Pesan / Catatan</th>
                                    <th class="px-4 py-3 text-right text-dark font-weight-bold small">Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentLeads as $lead)
                                    <tr>
                                        <td class="px-4 font-weight-bold text-dark small">
                                            {{ $lead->name }}
                                            @if(!$lead->converted_to_member_id)
                                                <span class="badge badge-warning text-dark ml-1 font-weight-bold" style="font-size: 10px; border-radius: 4px;">Baru</span>
                                            @else
                                                <span class="badge badge-success ml-1 font-weight-bold" style="font-size: 10px; border-radius: 4px;">Member</span>
                                            @endif
                                        </td>
                                        <td class="small text-muted">
                                            <div><i class="icon-phone mr-1 text-primary"></i> {{ $lead->phone ?: '-' }}</div>
                                            @if($lead->email)
                                                <div style="font-size: 11px;"><i class="icon-mail mr-1"></i> {{ $lead->email }}</div>
                                            @endif
                                        </td>
                                        <td class="small text-muted" style="max-width: 200px;">
                                            <span class="d-inline-block text-truncate" style="max-width: 190px;">
                                                {{ $lead->notes ?: 'Ingin informasi keanggotaan gym.' }}
                                            </span>
                                        </td>
                                        <td class="px-4 text-right small text-muted">
                                            {{ $lead->created_at ? $lead->created_at->diffForHumans() : '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted small">
                                            <span class="icon-inbox h4 d-block mb-1 text-muted"></span>
                                            Belum ada pesan masuk baru dari formulir kontak website.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        <!-- ========================================== -->
        <!-- SISI KANAN: HUBUNGAN SUPERADMIN & RIWAYAT -->
        <!-- ========================================== -->
        <div class="col-lg-6">
            
            <!-- Card Pengumuman Superadmin -->
            <div class="card border-0 shadow-sm mb-4 bg-white" style="border-radius: 14px;">
                <div class="card-header bg-white border-bottom pt-4 px-4 pb-3">
                    <h6 class="font-weight-bold text-dark mb-0">Pengumuman &amp; Notifikasi Superadmin</h6>
                    <small class="text-muted">Pemberitahuan resmi jadwal pemeliharaan dan pembaruan sistem</small>
                </div>
                <div class="card-body p-4">
                    @if(count($announcements) > 0)
                        @foreach($announcements as $anc)
                            <div class="p-3 mb-3 border-left border-primary bg-light rounded" style="border-left-width: 4px !important; border-radius: 8px;">
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
                        @endforeach
                    @else
                        <div class="text-center py-4 text-muted small">
                            <span class="icon-bell-off h4 d-block mb-1 text-muted"></span>
                            Belum ada pengumuman baru dari Superadmin.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Card Aktivitas Pengelolaan Terakhir -->
            <div class="card border-0 shadow-sm bg-white" style="border-radius: 14px;">
                <div class="card-header bg-white border-bottom pt-4 px-4 pb-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="font-weight-bold text-dark mb-0">Aktivitas Pengelolaan Terakhir</h6>
                        <small class="text-muted">Catatan audit log perubahan website dan pengaturan sistem</small>
                    </div>
                    <a href="{{ route('admin.logs.index') }}" class="btn btn-sm btn-link text-primary font-weight-bold p-0 text-decoration-none">
                        Lihat Semua &rarr;
                    </a>
                </div>
                <div class="card-body p-4">
                    @if(count($recentActivities) > 0)
                        <div class="timeline-activity">
                            @foreach($recentActivities as $act)
                                <div class="d-flex align-items-start pb-3 mb-3 border-bottom" style="gap: 12px;">
                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-primary font-weight-bold" style="width: 36px; height: 36px; min-width: 36px; font-size: 13px;">
                                        <i class="icon-activity"></i>
                                    </div>
                                    <div class="w-100">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="font-weight-bold text-dark small">{{ $act->action }}</span>
                                            <small class="text-muted" style="font-size: 11px;">
                                                {{ $act->created_at ? $act->created_at->diffForHumans() : '-' }}
                                            </small>
                                        </div>
                                        <div class="text-muted small mt-1" style="font-size: 12px;">
                                            {{ $act->description }}
                                        </div>
                                        <div class="text-muted small mt-1" style="font-size: 11px;">
                                            <i class="icon-user mr-1 text-primary"></i> Oleh: <strong>{{ $act->user->name ?? 'Admin' }}</strong>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 text-muted small">
                            <span class="icon-history h4 d-block mb-1 text-muted"></span>
                            Belum ada riwayat aktivitas pengelolaan terbaru.
                        </div>
                    @endif
                </div>
            </div>

        </div>

    </div>

</div>

<style>
    .hover-shadow:hover {
        background-color: #f8fafc !important;
        border-color: #cbd5e1 !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.04) !important;
    }
</style>
@endsection

