@extends('layouts.admin')

@section('title', 'Landing Page Tenant &mdash; PetGym')
@section('page_title', 'Landing Page Publik Tenant')
@section('page_subtitle', 'Kustomisasi halaman website gym Anda — tingkat kustomisasi mengikuti paket langganan')

@section('content')

@php
  $sections = is_array($settings->sections_enabled) ? $settings->sections_enabled : [];
  $features = is_array($settings->features) ? $settings->features : [];
  $stats    = is_array($settings->stats) ? $settings->stats : [];
  $rowCount = max(count($features) + 1, 2);
@endphp

<div class="row">
  <div class="col-md-8">

    @if(session('success'))
    <div class="alert alert-success py-2" role="alert">
      <small class="font-weight-bold"><i class="icon-check-circle mr-1"></i> {{ session('success') }}</small>
    </div>
    @endif

    <form action="{{ route('admin.landing.update') }}" method="POST">
      @csrf

      <!-- ============ HERO (semua paket) ============ -->
      <div class="card-custom">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h6 class="font-weight-bold text-dark mb-0">1. Bagian Hero (Pembuka)</h6>
          <span class="badge badge-success px-2 py-1" style="font-size: 10px;">Semua Paket</span>
        </div>
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Judul Utama (Hero Title) *</label>
          <input type="text" name="hero_title" class="form-control" value="{{ $settings->hero_title ?: $tenant->name }}" style="border-radius: 8px;">
        </div>
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Tagline / Sub Judul</label>
          <textarea name="hero_tagline" rows="2" class="form-control" style="border-radius: 8px;">{{ $settings->hero_tagline }}</textarea>
        </div>
        <div class="row">
          <div class="col-md-6 form-group mb-0">
            <label class="font-weight-bold text-dark" style="font-size: 13px;">Teks Tombol CTA</label>
            <input type="text" name="cta_text" class="form-control" value="{{ $settings->cta_text ?: 'Mulai Sekarang' }}" style="border-radius: 8px;">
          </div>
          <div class="col-md-6 form-group mb-0">
            <label class="font-weight-bold text-dark" style="font-size: 13px;">Link Tombol CTA</label>
            <input type="text" name="cta_url" class="form-control" value="{{ $settings->cta_url ?: config('app.url') }}" placeholder="https://..." style="border-radius: 8px;">
          </div>
        </div>
      </div>

      <!-- ============ TENTANG (semua paket) ============ -->
      <div class="card-custom">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h6 class="font-weight-bold text-dark mb-0">2. Tentang Gym</h6>
          <span class="badge badge-success px-2 py-1" style="font-size: 10px;">Semua Paket</span>
        </div>
        <div class="form-group mb-0">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Deskripsi Tentang Gym</label>
          <textarea name="about_text" rows="4" class="form-control" style="border-radius: 8px;">{{ $settings->about_text }}</textarea>
        </div>
      </div>

      <!-- ============ FITUR UNGGULAN (Pro+) ============ -->
      <div class="card-custom">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h6 class="font-weight-bold text-dark mb-0">3. Fitur Unggulan</h6>
          @if($tenant->canLanding('features'))
            <span class="badge badge-primary px-2 py-1" style="font-size: 10px;">Paket Pro ke atas</span>
          @else
            <span class="badge badge-secondary px-2 py-1" style="font-size: 10px;"><i class="icon-lock mr-1"></i>Terbuka di Paket Pro</span>
          @endif
        </div>

        @if($tenant->canLanding('features'))
          <div id="featureRows">
            @for($i = 0; $i < $rowCount; $i++)
            <div class="feature-row row mb-2 align-items-center">
              <div class="col-md-5">
                <input type="text" name="features_title[]" class="form-control" placeholder="Judul fitur (mis. Kelas Zumba)" value="{{ $features[$i]['label'] ?? '' }}" style="border-radius: 8px;">
              </div>
              <div class="col-md-6">
                <input type="text" name="features_desc[]" class="form-control" placeholder="Deskripsi singkat" value="{{ $features[$i]['description'] ?? '' }}" style="border-radius: 8px;">
              </div>
              <div class="col-md-1 text-right">
                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-feature" style="border-radius: 6px;">&times;</button>
              </div>
            </div>
            @endfor
          </div>
          <button type="button" class="btn btn-sm btn-outline-primary mt-1" id="btnAddFeature" style="border-radius: 6px;">
            <i class="icon-plus mr-1"></i>Tambah Fitur
          </button>
        @else
          <p class="text-muted small mb-2">Daftar fitur unggulan dapat dikustomisasi di <strong>Paket Pro</strong> ke atas.</p>
          <div>
            @foreach((array) $tenant->features as $tf)
              <span class="badge badge-light border px-3 py-2 mr-1 mb-1" style="font-size: 12px;">{{ $tf }}</span>
            @endforeach
          </div>
        @endif
      </div>

      <!-- ============ WARNA BRAND (Pro+) ============ -->
      <div class="card-custom">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h6 class="font-weight-bold text-dark mb-0">4. Warna Brand</h6>
          @if($tenant->canLanding('colors'))
            <span class="badge badge-primary px-2 py-1" style="font-size: 10px;">Paket Pro ke atas</span>
          @else
            <span class="badge badge-secondary px-2 py-1" style="font-size: 10px;"><i class="icon-lock mr-1"></i>Terbuka di Paket Pro</span>
          @endif
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
          <p class="text-muted small mb-0">Kustomisasi warna brand tersedia di <strong>Paket Pro</strong> ke atas.</p>
        @endif
      </div>

      <!-- ============ BAGIAN HALAMAN (Pro+) ============ -->
      <div class="card-custom">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h6 class="font-weight-bold text-dark mb-0">5. Bagian yang Ditampilkan</h6>
          @if($tenant->canLanding('sections'))
            <span class="badge badge-primary px-2 py-1" style="font-size: 10px;">Paket Pro ke atas</span>
          @else
            <span class="badge badge-secondary px-2 py-1" style="font-size: 10px;"><i class="icon-lock mr-1"></i>Terbuka di Paket Pro</span>
          @endif
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
          <p class="text-muted small mb-0">Pengaturan bagian halaman tersedia di <strong>Paket Pro</strong> ke atas.</p>
        @endif
      </div>

      <!-- ============ STATISTIK (Enterprise) ============ -->
      <div class="card-custom">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h6 class="font-weight-bold text-dark mb-0">6. Statistik Angka</h6>
          @if($tenant->canLanding('stats'))
            <span class="badge badge-dark px-2 py-1" style="font-size: 10px;">Paket Enterprise</span>
          @else
            <span class="badge badge-secondary px-2 py-1" style="font-size: 10px;"><i class="icon-lock mr-1"></i>Eksklusif Paket Enterprise</span>
          @endif
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
          <p class="text-muted small mb-0">
            <i class="icon-lock mr-1"></i>
            Statistik angka besar (jumlah member, kelas, dsb.) adalah fitur <strong>Eksklusif Paket Enterprise</strong>.
            @if($tenant->plan_name == 'Paket Pro')
            <a href="#" class="text-primary font-weight-bold">Upgrade ke Enterprise</a> untuk mengaktifkannya.
            @endif
          </p>
        @endif
      </div>

      <!-- ============ KONTAK (semua paket) ============ -->
      <div class="card-custom">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h6 class="font-weight-bold text-dark mb-0">7. Info Kontak</h6>
          <span class="badge badge-success px-2 py-1" style="font-size: 10px;">Semua Paket</span>
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
      <h6 class="font-weight-bold text-dark mb-3">Paket &amp; Alamat Landing Page</h6>
      <div class="p-3 bg-light rounded mb-3">
        <small class="text-uppercase text-muted font-weight-bold" style="font-size: 10px;">Paket Aktif</small>
        <h4 class="font-weight-bold text-success mb-0">{{ $tenant->plan_name ?: 'Paket Basic' }}</h4>
      </div>
      <a href="{{ $landingUrl }}" target="_blank" class="btn btn-dark btn-block font-weight-bold mb-3" style="border-radius: 8px;">
        <i class="icon-globe mr-1"></i> Buka Landing Page
      </a>
      <div class="mb-2" style="font-size: 12.5px;">
        <div class="d-flex justify-content-between py-1">
          <span class="text-muted">Alamat publik:</span>
          <span class="font-weight-bold text-dark" style="word-break: break-all;">{{ $landingUrl }}</span>
        </div>
        <div class="text-muted small pt-2 border-top mt-2">
          <i class="icon-exclamation-circle mr-1"></i>
          Buka aplikasi lewat <strong>http://localhost:8000</strong> agar alamat subdomain di atas bisa diakses.
        </div>
      </div>
    </div>

    <div class="card-custom">
      <h6 class="font-weight-bold text-dark mb-3">Level Kustomisasi per Paket</h6>
      <table class="table table-sm table-borderless mb-0" style="font-size: 12.5px;">
        <tr>
          <td class="text-muted">Semua Paket</td>
          <td class="font-weight-bold text-dark">Teks &amp; kontak</td>
        </tr>
        <tr>
          <td class="text-muted">Paket Pro</td>
          <td class="font-weight-bold text-dark">+ Warna, bagian, fitur</td>
        </tr>
        <tr>
          <td class="text-muted">Paket Enterprise</td>
          <td class="font-weight-bold text-dark">+ Statistik angka</td>
        </tr>
      </table>
      <div class="alert alert-warning py-2 mb-0 mt-2" style="font-size: 12px;">
        <i class="icon-exclamation-circle mr-1"></i>
        Field terlarang <strong>tidak bisa diubah</strong> — sistem menolaknya di sisi server, bukan hanya di tampilan.
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  $(document).ready(function() {
    // Tambah baris fitur (maks 6)
    $('#btnAddFeature').on('click', function() {
      var rows = $('#featureRows .feature-row');
      if (rows.length >= 6) { alert('Maksimal 6 fitur unggulan.'); return; }
      $('#featureRows').append(
        '<div class="feature-row row mb-2 align-items-center">' +
          '<div class="col-md-5"><input type="text" name="features_title[]" class="form-control" placeholder="Judul fitur" style="border-radius: 8px;"></div>' +
          '<div class="col-md-6"><input type="text" name="features_desc[]" class="form-control" placeholder="Deskripsi singkat" style="border-radius: 8px;"></div>' +
          '<div class="col-md-1 text-right"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-feature" style="border-radius: 6px;">&times;</button></div>' +
        '</div>'
      );
    });

    $(document).on('click', '.btn-remove-feature', function() {
      if ($('#featureRows .feature-row').length <= 1) return;
      $(this).closest('.feature-row').remove();
    });

    $(document).on('click', '.btn-remove-stat', function() {
      if ($('#statRows .stat-row').length <= 1) return;
      $(this).closest('.stat-row').remove();
    });
  });
</script>
@endsection
