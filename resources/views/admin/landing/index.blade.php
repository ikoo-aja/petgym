@extends('layouts.admin')

@section('title', 'Pengaturan Gym & Landing Page')
@section('page_title', 'Landing Page')
@section('page_subtitle', 'Pengaturan konten dan tampilan website publik gym')

@section('content')

@php
  $sections = is_array($settings->sections_enabled) ? $settings->sections_enabled : [];
  $features = is_array($settings->features) ? $settings->features : [];
  $stats    = is_array($settings->stats) ? $settings->stats : [];
  $rowCount = count($features) > 0 ? count($features) : 2;
@endphp

<div class="row">
  <div class="col-md-8">

    <form action="{{ route('admin.landing.update') }}" method="POST" enctype="multipart/form-data">
      @csrf

      <!-- ============ KONTROL AKSES & STATUS LANDING PAGE ============ -->
      <div class="card-custom mb-4" id="section-status" style="border-left: 4px solid {{ $settings->is_active ? '#10b981' : '#f59e0b' }};">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h6 class="font-weight-bold text-dark mb-0">
            <i class="icon-power-off mr-1 {{ $settings->is_active ? 'text-success' : 'text-warning' }}"></i>
            Status &amp; Akses Landing Page Publik
          </h6>
          @if($settings->is_active)
            <span class="badge badge-success px-2 py-1" style="font-size: 11px;">
              <i class="icon-check mr-1"></i> Publik Terbuka (Aktif)
            </span>
          @else
            <span class="badge badge-warning text-dark px-2 py-1" style="font-size: 11px;">
              <i class="icon-clock-o mr-1"></i> Ditutup (Mode Maintenance)
            </span>
          @endif
        </div>
        <p class="text-muted small mb-3">
          Atur apakah website landing page publik gym Anda aktif terbuka atau sedang ditutup untuk pemeliharaan (maintenance mode). Jika ditutup, pengunjung umum akan melihat halaman pemeliharaan dan tetap dapat mengakses portal login member.
        </p>

        <div class="row mb-3">
          <div class="col-md-6 mb-2">
            <label class="d-block p-3 border rounded h-100 cursor-pointer status-card-label {{ $settings->is_active ? 'border-success bg-light' : 'bg-white' }}" style="cursor: pointer; transition: all 0.2s ease;">
              <div class="d-flex align-items-start">
                <input type="radio" name="is_active" value="1" {{ $settings->is_active ? 'checked' : '' }} class="mr-2 mt-1 status-radio">
                <div>
                  <div class="font-weight-bold text-dark" style="font-size: 13.5px;">
                    <span class="text-success mr-1">●</span> Buka Landing Page (Aktif)
                  </div>
                  <small class="text-muted d-block mt-1">Website publik aktif dan dapat diakses bebas oleh semua calon member.</small>
                </div>
              </div>
            </label>
          </div>

          <div class="col-md-6 mb-2">
            <label class="d-block p-3 border rounded h-100 cursor-pointer status-card-label {{ !$settings->is_active ? 'border-warning bg-light' : 'bg-white' }}" style="cursor: pointer; transition: all 0.2s ease;">
              <div class="d-flex align-items-start">
                <input type="radio" name="is_active" value="0" {{ !$settings->is_active ? 'checked' : '' }} class="mr-2 mt-1 status-radio">
                <div>
                  <div class="font-weight-bold text-dark" style="font-size: 13.5px;">
                    <span class="text-warning mr-1">●</span> Tutup Sementara (Maintenance)
                  </div>
                  <small class="text-muted d-block mt-1">Landing page ditutup sementara untuk perbaikan. Pengunjung melihat tampilan pemeliharaan.</small>
                </div>
              </div>
            </label>
          </div>
        </div>

        <!-- Pengaturan teks halaman maintenance -->
        <div id="maintenanceConfigBox" class="p-3 border rounded mb-2" style="background-color: #fffbeb; border-color: #fde68a !important; {{ $settings->is_active ? 'display: none;' : '' }}">
          <div class="d-flex align-items-center mb-2">
            <i class="icon-wrench text-warning mr-2" style="font-size: 16px;"></i>
            <h6 class="font-weight-bold text-dark mb-0" style="font-size: 13px;">Pengaturan Halaman Pemeliharaan (Maintenance)</h6>
          </div>
          <p class="text-muted mb-3" style="font-size: 12px;">
            Kustomisasi judul dan pesan penjelasan yang akan tampil kepada pengunjung saat landing page ditutup.
          </p>

          <div class="form-group mb-3">
            <label class="font-weight-bold text-dark" style="font-size: 12.5px;">Judul Pemeliharaan (Title)</label>
            <input type="text" name="maintenance_title" class="form-control bg-white"
                   value="{{ $settings->maintenance_title ?: 'Website Sedang Dalam Pemeliharaan' }}"
                   placeholder="Contoh: Website Sedang Dalam Pemeliharaan"
                   style="border-radius: 8px; font-size: 13px;">
          </div>

          <div class="form-group mb-0">
            <label class="font-weight-bold text-dark" style="font-size: 12.5px;">Pesan / Penjelasan Pemeliharaan</label>
            <textarea name="maintenance_message" rows="3" class="form-control bg-white"
                      placeholder="Contoh: Mohon maaf, kami sedang melakukan pembaruan sistem dan fasilitas. Website akan segera kembali aktif."
                      style="border-radius: 8px; font-size: 13px;">{{ $settings->maintenance_message }}</textarea>
            <small class="text-muted mt-1 d-block" style="font-size: 11.5px;">Kosongkan jika ingin menggunakan pesan bawaan sistem.</small>
          </div>
        </div>
      </div>

      <!-- ============ PROFIL & LOGO GYM ============ -->
      <div class="card-custom" id="gym-identity">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h6 class="font-weight-bold text-dark mb-0">Identitas Gym & Logo Brand</h6>
        </div>

        <div class="form-group mb-3 p-3 border rounded bg-light">
          <label class="font-weight-bold text-dark d-block" style="font-size: 13px;">Logo Gym / Brand Logo</label>
          @if($tenant->logo_url)
            <div class="mb-2">
              <img src="{{ asset($tenant->logo_url) }}?v={{ time() }}" alt="Current Logo" class="img-thumbnail bg-white" style="max-height: 80px; object-fit: contain;">
            </div>
          @endif
          <input type="file" name="logo" class="form-control-file border p-2 rounded bg-white" accept="image/*">
          <small class="form-text text-muted">Upload logo brand gym Anda (PNG, JPG, WEBP, SVG. Maks. 2MB). Logo akan tampil di header & sidebar.</small>
        </div>

        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Gaya Tampilan Brand (Header & Sidebar) *</label>
          <select name="brand_display_mode" class="form-control" style="border-radius: 8px;">
            <option value="both" {{ ($settings->brand_display_mode ?? 'both') === 'both' ? 'selected' : '' }}>Logo Gambar & Teks Nama Gym</option>
            <option value="logo" {{ ($settings->brand_display_mode ?? 'both') === 'logo' ? 'selected' : '' }}>Hanya Logo Gambar</option>
            <option value="text" {{ ($settings->brand_display_mode ?? 'both') === 'text' ? 'selected' : '' }}>Hanya Teks Nama Gym</option>
          </select>
          <small class="form-text text-muted">Pilih jenis tampilan identitas brand gym Anda pada sidebar admin dan website publik.</small>
        </div>

        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Nama Gym (Penyewa) *</label>
          <input type="text" name="name" class="form-control" value="{{ $tenant->name ?? '' }}" required style="border-radius: 8px;">
        </div>

        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Subdomain Sistem SaaS</label>
          <input type="text" class="form-control bg-light" value="{{ $tenant->subdomain ?? 'subdomain.workout.id' }}" readonly style="border-radius: 8px;">
        </div>

        <div class="row">
          <div class="col-md-6 form-group mb-0">
            <label class="font-weight-bold text-dark" style="font-size: 13px;">Nama Pemilik (Owner)</label>
            <input type="text" name="owner_name" class="form-control" value="{{ $tenant->owner_name ?? '' }}" style="border-radius: 8px;">
          </div>
          <div class="col-md-6 form-group mb-0">
            <label class="font-weight-bold text-dark" style="font-size: 13px;">Email Pemilik (Owner)</label>
            <input type="email" name="owner_email" class="form-control" value="{{ $tenant->owner_email ?? '' }}" style="border-radius: 8px;">
          </div>
        </div>
      </div>

      <!-- ============ HERO ============ -->
      <div class="card-custom" id="section-hero">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h6 class="font-weight-bold text-dark mb-0">1. Bagian Hero (Pembuka)</h6>
        </div>
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Judul Utama (Hero Title) *</label>
          <input type="text" name="hero_title" class="form-control" value="{{ $settings->hero_title ?: $tenant->name }}" style="border-radius: 8px;">
        </div>
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Tagline / Sub Judul</label>
          <textarea name="hero_tagline" rows="2" class="form-control" style="border-radius: 8px;">{{ $settings->hero_tagline }}</textarea>
        </div>
        <div class="row mb-3">
          <div class="col-md-6 form-group mb-0">
            <label class="font-weight-bold text-dark" style="font-size: 13px;">Teks Tombol CTA</label>
            <input type="text" name="cta_text" class="form-control" value="{{ $settings->cta_text ?: 'Mulai Sekarang' }}" style="border-radius: 8px;">
          </div>
          <div class="col-md-6 form-group mb-0">
            <label class="font-weight-bold text-dark" style="font-size: 13px;">Link Tombol CTA</label>
            <input type="text" name="cta_url" class="form-control" value="{{ $settings->cta_url ?: route('tenant.login', ['slug' => $tenant->slug]) }}" placeholder="https://..." style="border-radius: 8px;">
          </div>
        </div>

        <div class="form-group mb-0 p-3 border rounded bg-light">
          <label class="font-weight-bold text-dark d-block" style="font-size: 13px;">Foto Cover Banner (Hero Background)</label>
          @if($settings->hero_image)
            <div class="mb-2">
              <img src="{{ asset($settings->hero_image) }}?v={{ time() }}" alt="Current Hero Banner" class="img-thumbnail bg-white" style="max-height: 120px; width: 100%; object-fit: cover; border-radius: 6px;">
            </div>
          @else
            <div class="mb-2">
              <small class="text-muted d-block mb-1">Foto Default Saat Ini:</small>
              <img src="{{ asset($tenant->slug === 'powerhouse' ? 'images/bg_2.jpg' : 'images/bg_1.jpg') }}" alt="Default Banner" class="img-thumbnail bg-white" style="max-height: 100px; width: 100%; object-fit: cover; border-radius: 6px; opacity: 0.85;">
            </div>
          @endif
          <input type="file" name="hero_image" class="form-control-file border p-2 rounded bg-white" accept="image/*">
          <small class="form-text text-muted">Upload foto atmosfer olahraga berkualitas tinggi (JPG, PNG, WEBP. Maks 3MB). Rekomendasi landscape 1920x1080.</small>
        </div>

        <div class="form-group mb-0 p-3 border rounded bg-light mt-3">
          <label class="font-weight-bold text-dark d-block" style="font-size: 13px;">Foto Kartu Sorotan Fasilitas (Hero Highlight Card)</label>
          @if($settings->hero_card_image)
            <div class="mb-2">
              <img src="{{ asset($settings->hero_card_image) }}?v={{ time() }}" alt="Current Hero Highlight Card" class="img-thumbnail bg-white" style="max-height: 120px; width: 100%; object-fit: cover; border-radius: 6px;">
            </div>
          @else
            <div class="mb-2">
              <small class="text-muted d-block mb-1">Foto Default Saat Ini:</small>
              <img src="{{ asset($tenant->slug === 'powerhouse' ? 'images/img_3.jpg' : 'images/img_4.jpg') }}" alt="Default Hero Card" class="img-thumbnail bg-white" style="max-height: 100px; width: 100%; object-fit: cover; border-radius: 6px; opacity: 0.85;">
            </div>
          @endif
          <input type="file" name="hero_card_image" class="form-control-file border p-2 rounded bg-white" accept="image/*">
          <small class="form-text text-muted">Upload foto untuk kartu fasilitas di sebelah kanan hero (JPG, PNG, WEBP. Maks 3MB). Rekomendasi landscape atau rasio 16:9.</small>
        </div>
      </div>

      <!-- ============ TENTANG ============ -->
      <div class="card-custom" id="section-about">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h6 class="font-weight-bold text-dark mb-0">2. Tentang Gym</h6>
        </div>
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Deskripsi Tentang Gym</label>
          <textarea name="about_text" rows="4" class="form-control" style="border-radius: 8px;">{{ $settings->about_text }}</textarea>
        </div>
        <div class="form-group mb-0 p-3 border rounded bg-light">
          <label class="font-weight-bold text-dark d-block" style="font-size: 13px;">Foto Suasana Latihan (Tentang Kami)</label>
          @if($settings->about_image)
            <div class="mb-2">
              <img src="{{ asset($settings->about_image) }}?v={{ time() }}" alt="Current About Photo" class="img-thumbnail bg-white" style="max-height: 120px; object-fit: cover; border-radius: 6px;">
            </div>
          @else
            <div class="mb-2">
              <small class="text-muted d-block mb-1">Foto Default Saat Ini:</small>
              <img src="{{ asset($tenant->slug === 'powerhouse' ? 'images/img_1.jpg' : 'images/img_2.jpg') }}" alt="Default About Photo" class="img-thumbnail bg-white" style="max-height: 100px; object-fit: cover; border-radius: 6px; opacity: 0.85;">
            </div>
          @endif
          <input type="file" name="about_image" class="form-control-file border p-2 rounded bg-white" accept="image/*">
          <small class="form-text text-muted">Upload foto aktivitas member atau trainer saat berolahraga (JPG, PNG, WEBP. Maks 3MB). Rasio ideal 4:3.</small>
        </div>
      </div>

      <!-- ============ FITUR UNGGULAN ============ -->
      <div class="card-custom" id="section-features">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <h6 class="font-weight-bold text-dark mb-0">3. Fasilitas & Fitur Unggulan</h6>
            <small class="text-muted">Kustomisasi judul, deskripsi, dan foto masing-masing fasilitas di landing page.</small>
          </div>
          <span class="badge badge-light border text-muted">Maksimal 6</span>
        </div>

        @if($tenant->canLanding('features'))
          <div id="featureRows">
            @for($i = 0; $i < $rowCount; $i++)
            <div class="feature-row card p-3 mb-3 border bg-light" style="border-radius: 8px;">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="font-weight-bold text-secondary feature-index-label" style="font-size: 13px;">Fasilitas #{{ $i + 1 }}</span>
                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-feature" style="border-radius: 6px; font-size: 12px; padding: 2px 8px;">
                  &times; Hapus
                </button>
              </div>
              <div class="row">
                <div class="col-md-7">
                  <div class="form-group mb-2">
                    <label class="font-weight-bold text-dark mb-1" style="font-size: 12.5px;">Nama Fasilitas</label>
                    <input type="text" name="features_title[]" class="form-control" placeholder="mis. Kelas Terjadwal / Area Beban Bebas" value="{{ $features[$i]['label'] ?? '' }}" style="border-radius: 6px; font-size: 13.5px;">
                  </div>
                  <div class="form-group mb-0">
                    <label class="font-weight-bold text-dark mb-1" style="font-size: 12.5px;">Deskripsi Singkat</label>
                    <textarea name="features_desc[]" rows="2" class="form-control" placeholder="Deskripsi ringkas mengenai fasilitas ini..." style="border-radius: 6px; font-size: 13px;">{{ $features[$i]['description'] ?? '' }}</textarea>
                  </div>
                </div>
                <div class="col-md-5">
                  <label class="font-weight-bold text-dark mb-1" style="font-size: 12.5px;">Foto Fasilitas</label>
                  <input type="hidden" name="features_existing_image[]" value="{{ $features[$i]['image'] ?? '' }}">
                  @php
                    $featImg = $features[$i]['image'] ?? null;
                    $defaultFeatImg = 'images/img_' . (($i % 5) + 1) . '.jpg';
                  @endphp
                  <div class="mb-2">
                    @if($featImg)
                      <img src="{{ asset($featImg) }}?v={{ time() }}" alt="Foto Fasilitas" class="img-thumbnail bg-white feature-preview-img" style="height: 65px; width: 100%; object-fit: cover; border-radius: 6px;">
                    @else
                      <div class="d-flex align-items-center">
                        <img src="{{ asset($defaultFeatImg) }}" alt="Default Fasilitas" class="img-thumbnail bg-white mr-2 feature-preview-img" style="height: 55px; width: 75px; object-fit: cover; border-radius: 6px; opacity: 0.85;">
                        <small class="text-muted" style="font-size: 11px;">Default sistem</small>
                      </div>
                    @endif
                  </div>
                  <input type="file" name="features_image_{{ $i }}" class="form-control-file border p-1 rounded bg-white" accept="image/*" style="font-size: 12px;">
                  <small class="form-text text-muted" style="font-size: 11px;">Ganti foto (JPG, PNG, WEBP. Maks 2MB).</small>
                </div>
              </div>
            </div>
            @endfor
          </div>
          <button type="button" class="btn btn-sm btn-outline-primary mt-1" id="btnAddFeature" style="border-radius: 6px; font-weight: 600;">
            <i class="icon-plus mr-1"></i> Tambah Fasilitas
          </button>
        @else
          <p class="text-muted small mb-2">Daftar fitur unggulan gym Anda.</p>
          <div>
            @foreach((array) $tenant->features as $tf)
              <span class="badge badge-light border px-3 py-2 mr-1 mb-1" style="font-size: 12px;">{{ $tf }}</span>
            @endforeach
          </div>
        @endif
      </div>

      <!-- ============ WARNA BRAND ============ -->
      <div class="card-custom" id="section-colors">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h6 class="font-weight-bold text-dark mb-0">4. Warna Brand</h6>
        </div>

        @if($tenant->canLanding('colors'))
          <div class="row">
            <div class="col-md-6 form-group mb-0">
              <label class="font-weight-bold text-dark" style="font-size: 13px;">Warna Utama</label>
              <div class="d-flex align-items-center">
                <input type="color" name="primary_color" value="{{ $settings->primary_color ?: '#f43f5e' }}" class="form-control form-control-color mr-2" style="max-width: 70px; height: 40px; padding: 4px; border-radius: 8px;">
                <code>{{ $settings->primary_color ?: '#f43f5e' }}</code>
              </div>
            </div>
            <div class="col-md-6 form-group mb-0">
              <label class="font-weight-bold text-dark" style="font-size: 13px;">Warna Gelap / Teks</label>
              <div class="d-flex align-items-center">
                <input type="color" name="secondary_color" value="{{ $settings->secondary_color ?: '#111827' }}" class="form-control form-control-color mr-2" style="max-width: 70px; height: 40px; padding: 4px; border-radius: 8px;">
                <code>{{ $settings->secondary_color ?: '#111827' }}</code>
              </div>
            </div>
          </div>
        @else
          <p class="text-muted small mb-0">Pengaturan warna brand gym.</p>
        @endif
      </div>

      <!-- ============ BAGIAN HALAMAN ============ -->
      <div class="card-custom" id="section-visibility">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h6 class="font-weight-bold text-dark mb-0">5. Bagian yang Ditampilkan</h6>
        </div>

        @if($tenant->canLanding('sections'))
          <div class="d-flex flex-wrap" style="gap: 16px;">
            @php
              $sectionOptions = ['about' => 'Tentang Kami', 'features' => 'Fitur Unggulan', 'stats' => 'Statistik Angka', 'contact' => 'Kontak & Alamat'];
            @endphp
            @foreach($sectionOptions as $key => $label)
            <label class="mb-1 font-weight-normal text-dark" style="font-size: 13.5px; cursor: pointer;">
              <input type="checkbox" name="sections[]" value="{{ $key }}" {{ in_array($key, $sections) ? 'checked' : '' }} class="mr-1">
              {{ $label }}
            </label>
            @endforeach
          </div>
          <small class="text-muted d-block mt-2">Bagian Hero &amp; Footer selalu tampil.</small>
        @else
          <p class="text-muted small mb-0">Pengaturan bagian halaman publik gym.</p>
        @endif
      </div>

      <!-- ============ STATISTIK ============ -->
      <div class="card-custom" id="section-stats">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h6 class="font-weight-bold text-dark mb-0">6. Statistik Angka</h6>
        </div>

        @if($tenant->canLanding('stats'))
          <div id="statRows">
            @for($i = 0; $i < 4; $i++)
            <div class="stat-row row mb-2 align-items-center">
              <div class="col-md-3">
                <input type="text" name="stats_value[]" class="form-control" placeholder="500+" value="{{ $stats[$i]['label'] ?? '' }}" style="border-radius: 8px;">
              </div>
              <div class="col-md-8">
                <input type="text" name="stats_label[]" class="form-control" placeholder="Member Aktif" value="{{ $stats[$i]['description'] ?? '' }}" style="border-radius: 8px;">
              </div>
              <div class="col-md-1 text-right">
                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-stat" style="border-radius: 6px;">&times;</button>
              </div>
            </div>
            @endfor
          </div>
        @else
          <p class="text-muted small mb-0">Pengaturan statistik angka publik gym.</p>
        @endif
      </div>

      <!-- ============ KONTAK ============ -->
      <div class="card-custom" id="section-contact">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h6 class="font-weight-bold text-dark mb-0">7. Info Kontak</h6>
        </div>
        <div class="row">
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark" style="font-size: 13px;">Alamat</label>
            <input type="text" name="address" class="form-control" value="{{ $settings->address }}" style="border-radius: 8px;">
          </div>
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark" style="font-size: 13px;">Telepon</label>
            <input type="text" name="phone" class="form-control" value="{{ $settings->phone }}" style="border-radius: 8px;">
          </div>
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark" style="font-size: 13px;">Email Publik</label>
            <input type="email" name="email" class="form-control" value="{{ $settings->email }}" style="border-radius: 8px;">
          </div>
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark" style="font-size: 13px;">Jam Operasional</label>
            <input type="text" name="opening_hours" class="form-control" value="{{ $settings->opening_hours }}" style="border-radius: 8px;">
          </div>
          <div class="col-md-6 form-group mb-0">
            <label class="font-weight-bold text-dark" style="font-size: 13px;">Instagram (link profil)</label>
            <input type="text" name="instagram" class="form-control" value="{{ $settings->instagram }}" placeholder="https://instagram.com/..." style="border-radius: 8px;">
          </div>
          <div class="col-md-6 form-group mb-0">
            <label class="font-weight-bold text-dark" style="font-size: 13px;">Facebook (link halaman)</label>
            <input type="text" name="facebook" class="form-control" value="{{ $settings->facebook }}" placeholder="https://facebook.com/..." style="border-radius: 8px;">
          </div>
        </div>
      </div>

      <button type="submit" class="btn btn-primary font-weight-bold px-4 py-2 mb-4" style="border-radius: 8px;">
        <i class="icon-check mr-1"></i> Simpan Landing Page
      </button>
    </form>
  </div>

  <!-- ============ SIDEBAR INFO ============ -->
  <div class="col-md-4">
    <div class="card-custom">
      <h6 class="font-weight-bold text-dark mb-3">Status &amp; Akses Landing Page</h6>

      <div class="p-3 rounded mb-3" style="{{ !$settings->is_active ? 'background-color: #fffbeb; border: 1px solid #fde68a;' : 'background-color: #f8fafc; border: 1px solid #e2e8f0;' }}">
        <div class="d-flex justify-content-between align-items-center mb-1">
          <small class="text-uppercase text-muted font-weight-bold" style="font-size: 10px;">Status Website</small>
          @if($settings->is_active)
            <span class="badge badge-success px-2 py-1" style="font-size: 10px;">
              <i class="icon-check mr-1"></i> AKTIF (ONLINE)
            </span>
          @else
            <span class="badge badge-warning text-dark px-2 py-1" style="font-size: 10px;">
              <i class="icon-clock-o mr-1"></i> MAINTENANCE
            </span>
          @endif
        </div>
        <div class="font-weight-bold text-dark mb-0" style="font-size: 13px;">
          {{ $settings->is_active ? 'Website Terbuka untuk Umum' : 'Website Ditutup Sementara' }}
        </div>
        <small class="text-muted d-block mt-1" style="font-size: 11px;">
          {{ $settings->is_active ? 'Pengunjung umum dapat mengakses seluruh landing page.' : 'Pengunjung umum melihat tampilan maintenance.' }}
        </small>
      </div>

      <div class="p-3 bg-light rounded mb-3" style="border: 1px solid #e2e8f0;">
        <small class="text-uppercase text-muted font-weight-bold" style="font-size: 10px;">Paket Berlangganan</small>
        <h4 class="font-weight-bold text-success mb-0">{{ $tenant->plan_name ?: 'Paket Basic' }}</h4>
      </div>

      <div class="mb-3">
        @if($settings->is_active)
          <a href="{{ $landingUrl }}" target="_blank" class="btn btn-dark btn-block font-weight-bold" style="border-radius: 8px;">
            <i class="icon-globe mr-1"></i> Buka Landing Page
          </a>
        @else
          <a href="{{ $landingUrl }}" target="_blank" class="btn btn-warning btn-block font-weight-bold mb-2 text-dark" style="border-radius: 8px;">
            <i class="icon-eye mr-1"></i> Lihat Tampilan Maintenance
          </a>
          <a href="{{ $landingUrl }}?preview=1" target="_blank" class="btn btn-outline-dark btn-block font-weight-bold" style="border-radius: 8px;">
            <i class="icon-globe mr-1"></i> Pratinjau Landing Page Penuh
          </a>
        @endif
      </div>

      <div class="mb-2" style="font-size: 12.5px;">
        <div class="d-flex justify-content-between py-1">
          <span class="text-muted">Alamat publik:</span>
          <span class="font-weight-bold text-dark" style="word-break: break-all;">{{ $landingUrl }}</span>
        </div>
      </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
  $(document).ready(function() {
    function reindexFeatures() {
      $('#featureRows .feature-row').each(function(idx) {
        $(this).find('.feature-index-label').text('Fasilitas #' + (idx + 1));
        $(this).find('input[type="file"]').attr('name', 'features_image_' + idx);
      });
    }

    // Tambah baris fasilitas (maks 6)
    $('#btnAddFeature').on('click', function() {
      var rows = $('#featureRows .feature-row');
      if (rows.length >= 6) {
        if (typeof showToast === 'function') {
          showToast('Perhatian', 'Maksimal 6 fasilitas unggulan.', 'warning');
        } else {
          alert('Maksimal 6 fasilitas unggulan.');
        }
        return;
      }

      var nextIdx = rows.length;
      var defaultImgNum = (nextIdx % 5) + 1;
      var defaultImgUrl = "{{ asset('images') }}/img_" + defaultImgNum + ".jpg";

      var html = '' +
        '<div class="feature-row card p-3 mb-3 border bg-light" style="border-radius: 8px;">' +
          '<div class="d-flex justify-content-between align-items-center mb-2">' +
            '<span class="font-weight-bold text-secondary feature-index-label" style="font-size: 13px;">Fasilitas #' + (nextIdx + 1) + '</span>' +
            '<button type="button" class="btn btn-sm btn-outline-danger btn-remove-feature" style="border-radius: 6px; font-size: 12px; padding: 2px 8px;">&times; Hapus</button>' +
          '</div>' +
          '<div class="row">' +
            '<div class="col-md-7">' +
              '<div class="form-group mb-2">' +
                '<label class="font-weight-bold text-dark mb-1" style="font-size: 12.5px;">Nama Fasilitas</label>' +
                '<input type="text" name="features_title[]" class="form-control" placeholder="mis. Fasilitas Kebugaran" style="border-radius: 6px; font-size: 13.5px;">' +
              '</div>' +
              '<div class="form-group mb-0">' +
                '<label class="font-weight-bold text-dark mb-1" style="font-size: 12.5px;">Deskripsi Singkat</label>' +
                '<textarea name="features_desc[]" rows="2" class="form-control" placeholder="Deskripsi ringkas mengenai fasilitas ini..." style="border-radius: 6px; font-size: 13px;"></textarea>' +
              '</div>' +
            '</div>' +
            '<div class="col-md-5">' +
              '<label class="font-weight-bold text-dark mb-1" style="font-size: 12.5px;">Foto Fasilitas</label>' +
              '<input type="hidden" name="features_existing_image[]" value="">' +
              '<div class="mb-2">' +
                '<div class="d-flex align-items-center">' +
                  '<img src="' + defaultImgUrl + '" alt="Default Fasilitas" class="img-thumbnail bg-white mr-2 feature-preview-img" style="height: 55px; width: 75px; object-fit: cover; border-radius: 6px; opacity: 0.85;">' +
                  '<small class="text-muted" style="font-size: 11px;">Default sistem</small>' +
                '</div>' +
              '</div>' +
              '<input type="file" name="features_image_' + nextIdx + '" class="form-control-file border p-1 rounded bg-white" accept="image/*" style="font-size: 12px;">' +
              '<small class="form-text text-muted" style="font-size: 11px;">Ganti foto (JPG, PNG, WEBP. Maks 2MB).</small>' +
            '</div>' +
          '</div>' +
        '</div>';

      $('#featureRows').append(html);
      reindexFeatures();
    });

    $(document).on('click', '.btn-remove-feature', function() {
      if ($('#featureRows .feature-row').length <= 1) {
        if (typeof showToast === 'function') {
          showToast('Informasi', 'Minimal harus ada 1 fasilitas.', 'info');
        } else {
          alert('Minimal harus ada 1 fasilitas.');
        }
        return;
      }
      $(this).closest('.feature-row').remove();
      reindexFeatures();
    });

    $(document).on('click', '.btn-remove-stat', function() {
      if ($('#statRows .stat-row').length <= 1) return;
      $(this).closest('.stat-row').remove();
    });
    // Toggle status landing page & maintenance config box
    $('input[name="is_active"]').on('change', function() {
      var isOpened = $(this).val() === '1';
      $('.status-card-label').removeClass('border-success border-warning bg-light').addClass('bg-white');
      if (isOpened) {
        $(this).closest('.status-card-label').removeClass('bg-white').addClass('border-success bg-light');
        $('#maintenanceConfigBox').slideUp(200);
        $('#section-status').css('border-left-color', '#10b981');
      } else {
        $(this).closest('.status-card-label').removeClass('bg-white').addClass('border-warning bg-light');
        $('#maintenanceConfigBox').slideDown(200);
        $('#section-status').css('border-left-color', '#f59e0b');
      }
    });
  });
</script>
@endsection
