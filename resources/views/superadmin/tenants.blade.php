@extends('layouts.superadmin')

@section('title', 'Kelola Penyewa &mdash; Superadmin Panel')

@section('page_title', 'Kelola Penyewa')
@section('page_subtitle', 'Kelola data tenant/gym yang terdaftar di platform')

@section('content')
<!-- 2. KELOLA PENYEWA (TENANT MANAGEMENT) -->
<section id="tenants" class="mb-5">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="font-weight-bold text-black mb-0">Daftar Seluruh Gym (Tenant List)</h4>
    <button class="btn btn-primary btn-sm px-3" data-toggle="modal" data-target="#addTenantModal">+ Tambah Gym Baru</button>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
      <strong>Sukses!</strong> {{ session('success') }}
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
  @endif

  <div class="table-custom p-4">
    <!-- Filter & Search Bar -->
    <form action="{{ route('superadmin.tenants') }}" method="GET" class="row mb-3">
      <div class="col-md-6">
        <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama gym, pemilik, atau subdomain..." value="{{ request('search') }}">
      </div>
      <div class="col-md-3">
        <select name="status" class="form-control form-control-sm" onchange="this.form.submit()">
          <option value="">Semua Status</option>
          <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
          <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
          <option value="trial" {{ request('status') == 'trial' ? 'selected' : '' }}>Free Trial</option>
        </select>
      </div>
      <div class="col-md-3">
        <button type="submit" class="btn btn-sm btn-outline-primary px-3">Filter / Cari</button>
        @if(request()->has('search') || request()->has('status'))
          <a href="{{ route('superadmin.tenants') }}" class="btn btn-sm btn-link text-muted">Reset</a>
        @endif
      </div>
    </form>

    <div class="table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th class="text-black font-weight-bold">Nama Gym / Subdomain</th>
            <th class="text-black font-weight-bold">Pemilik & Kontak</th>
            <th class="text-black font-weight-bold">Paket Sewa</th>
            <th class="text-black font-weight-bold">Tanggal Bergabung</th>
            <th class="text-black font-weight-bold">Sisa Masa Aktif</th>
            <th class="text-black font-weight-bold">Status</th>
            <th class="text-black font-weight-bold">Aksi / Kontrol</th>
          </tr>
        </thead>
        <tbody>
          @php
            if (!function_exists('maskEmail')) {
                function maskEmail($email) {
                    if (!$email || !str_contains($email, '@')) return $email;
                    [$name, $domain] = explode('@', $email, 2);
                    $length = strlen($name);
                    if ($length <= 2) {
                        $maskedName = substr($name, 0, 1) . '*';
                    } else {
                        $maskedName = substr($name, 0, 1) . str_repeat('*', max(3, $length - 2)) . substr($name, -1);
                    }
                    return $maskedName . '@' . $domain;
                }
            }
          @endphp
          @forelse($tenants as $tenant)
          @php
            $pName = $tenant->plan_name ?? 'Basic';
            $oName = $tenant->owner_name ?? 'Owner';
            $oEmail = $tenant->owner_email ?? 'owner@gym.com';
            $maskedEmail = maskEmail($oEmail);
            $expDays = $tenant->expires_in_days ?? 30;
            $featuresList = is_array($tenant->features) ? $tenant->features : ['Akses Manajemen Kelas', 'Kasir / POS Sederhana'];
          @endphp
          <tr>
            <td>
              <strong class="text-black">{{ $tenant->name }}</strong><br>
              <small class="text-muted">{{ $tenant->subdomain }}</small>
            </td>
            <td>
              <div class="font-weight-bold text-black">{{ $oName }}</div>
              <div class="d-inline-flex align-items-center" style="gap: 5px;">
                <small class="text-muted email-text" style="font-family: monospace; font-size: 12px;">{{ $maskedEmail }}</small>
                <button type="button" 
                        class="btn btn-link p-0 text-secondary btn-toggle-email" 
                        data-full="{{ $oEmail }}" 
                        data-masked="{{ $maskedEmail }}" 
                        data-shown="false"
                        title="Tampilkan Email Pemilik"
                        style="font-size: 12px; text-decoration: none; outline: none; box-shadow: none; line-height: 1;">
                  <span class="icon-eye"></span>
                </button>
              </div>
            </td>
            <td>
              @if(strpos($pName, 'Enterprise') !== false)
                <span class="badge badge-primary">{{ $pName }}</span>
              @elseif(strpos($pName, 'Pro') !== false)
                <span class="badge badge-info">{{ $pName }}</span>
              @else
                <span class="badge badge-secondary">{{ $pName }}</span>
              @endif
              
              <!-- Container for dynamic active feature tags -->
              <div class="tenant-features-list mt-1 d-flex flex-wrap">
                @foreach($featuresList as $feat)
                  <span class="badge badge-light border text-muted px-1" style="font-size: 10px; margin-right: 3px; margin-bottom: 2px;">{{ $feat }}</span>
                @endforeach
              </div>
            </td>
            <td>{{ is_object($tenant->joined_at) ? $tenant->joined_at->format('d M Y') : ($tenant->joined_at ?? 'Hari Ini') }}</td>
            <td>
              @if($tenant->status == 'suspended')
                <span class="text-danger" style="font-weight: 700;">N/A (Suspended)</span>
              @elseif($expDays <= 7)
                <span class="text-danger font-weight-bold" style="font-weight: 700; text-decoration: underline;">
                  {{ $expDays }} Hari Lagi
                </span>
              @elseif($expDays <= 30)
                <span class="text-warning font-weight-bold" style="font-weight: 700;">
                  {{ $expDays }} Hari Lagi
                </span>
              @else
                <span class="text-success font-weight-bold" style="font-weight: 700;">
                  {{ $expDays }} Hari
                </span>
              @endif
            </td>
            <td>
              @if($tenant->status == 'active')
                <span class="badge badge-status-active px-2 py-1 rounded">Aktif</span>
              @else
                <span class="badge badge-status-suspended px-2 py-1 rounded">Suspended</span>
              @endif
            </td>
            <td>
              <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle py-1" type="button" id="dropdownMenuButton{{ $loop->index }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="font-size: 12px; font-weight: 700;">
                  Pilihan
                </button>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton{{ $loop->index }}">
                  <a class="dropdown-item btn-features-tenant" href="#" data-id="{{ $tenant->id }}" data-name="{{ $tenant->name }}" data-features='@json($featuresList)'>
                    <span class="icon-settings text-secondary mr-2"></span> Atur Fitur
                  </a>
                  @if($tenant->status == 'active')
                    <a class="dropdown-item text-warning btn-suspend-tenant" href="#" data-id="{{ $tenant->id }}" data-name="{{ $tenant->name }}">
                      <span class="icon-pause mr-2"></span> Suspend
                    </a>
                  @else
                    <a class="dropdown-item text-success btn-activate-tenant" href="#" data-id="{{ $tenant->id }}" data-name="{{ $tenant->name }}">
                      <span class="icon-play_arrow mr-2"></span> Aktifkan
                    </a>
                  @endif
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item text-danger btn-delete-tenant" href="#" data-id="{{ $tenant->id }}" data-name="{{ $tenant->name }}">
                    <span class="icon-close mr-2"></span> Hapus
                  </a>
                </div>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="7" class="text-center py-4 text-muted">Tidak ada tenant penyewa ditemukan.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Pagination Footer -->
    <hr class="my-4">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center">
      <div class="text-muted mb-3 mb-sm-0" style="font-size: 13px;">
        Menampilkan {{ $tenants->firstItem() ?? 0 }} sampai {{ $tenants->lastItem() ?? 0 }} dari {{ $tenants->total() }} penyewa
      </div>
      <div>
        {{ $tenants->links() }}
      </div>
    </div>
  </div>
</section>
@endsection

@section('modals')
<!-- MODAL: TAMBAH GYM BARU (MANUAL ONBOARDING) -->
<div class="modal fade" id="addTenantModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-header-title font-weight-bold text-black">Manual Onboarding Gym Baru</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{ route('superadmin.tenants.store') }}" method="POST" id="addTenantForm">
        @csrf
        <div class="modal-body">
          <div class="form-group">
            <label class="text-black font-weight-bold">Nama Gym / Tenant</label>
            <input type="text" name="name" class="form-control" placeholder="Contoh: Gold Gym Sunter" required>
          </div>
          <div class="form-group">
            <label class="text-black font-weight-bold">Subdomain Akses</label>
            <div class="input-group">
              <input type="text" name="subdomain" class="form-control" placeholder="goldgym" required>
              <div class="input-group-append">
                <span class="input-group-text">.workout.id</span>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label class="text-black font-weight-bold">Email Pemilik (Admin Gym)</label>
            <input type="email" name="owner_email" class="form-control" placeholder="owner@goldgym.com" required>
          </div>
          <div class="form-group">
            <label class="text-black font-weight-bold">Pilih Paket Sewa</label>
            <select name="plan_id" class="form-control" id="addTenantPlan">
              @foreach($plans as $p)
                <option value="{{ $p->id }}">{{ $p->name }} (Rp {{ number_format($p->price, 0, ',', '.') }})</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary btn-sm">Buat Akun Tenant ke Database</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- MODAL: ATUR FITUR & ADD-ON TENANT -->
<div class="modal fade" id="configureFeaturesModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-header-title font-weight-bold text-black" id="featuresModalLabel">Atur Fitur & Add-on Tenant</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="" method="POST" id="configureFeaturesForm">
        @csrf
        <div class="modal-body">
          <div class="mb-3 bg-light p-3 rounded" style="font-size: 14px;">
            Mengonfigurasi modul fitur aktif untuk: <strong class="text-black" id="featuresTenantName">Gym Name</strong>
          </div>
          <hr class="my-3">

          <div class="form-group">
            <label class="text-black font-weight-bold d-block mb-3">Fitur Terintegrasi</label>

            <!-- Feature Toggles -->
            <div class="custom-control custom-switch mb-3">
              <input type="checkbox" name="features[]" value="Akses Manajemen Kelas" class="custom-control-input feat-sw" id="featClass" data-slug="Class">
              <label class="custom-control-label font-weight-bold text-black" for="featClass" style="cursor: pointer;">Akses Manajemen Kelas</label>
              <small class="form-text text-muted" style="margin-left: 28px;">Membuat jadwal kelas, booking member, dan absensi trainer.</small>
            </div>

            <div class="custom-control custom-switch mb-3">
              <input type="checkbox" name="features[]" value="Kasir / POS Sederhana" class="custom-control-input feat-sw" id="featPOS" data-slug="POS">
              <label class="custom-control-label font-weight-bold text-black" for="featPOS" style="cursor: pointer;">Kasir / POS Sederhana</label>
              <small class="form-text text-muted" style="margin-left: 28px;">Mengaktifkan modul transaksi kasir dan invoice harian.</small>
            </div>

            <div class="custom-control custom-switch mb-3">
              <input type="checkbox" name="features[]" value="Akses Manajemen Trainer" class="custom-control-input feat-sw" id="featTrainer" data-slug="Trainer">
              <label class="custom-control-label font-weight-bold text-black" for="featTrainer" style="cursor: pointer;">Akses Manajemen Trainer</label>
              <small class="form-text text-muted" style="margin-left: 28px;">Penjadwalan personal trainer (PT) dan perhitungan komisi.</small>
            </div>

            <div class="custom-control custom-switch mb-3">
              <input type="checkbox" name="features[]" value="Manajemen Inventaris" class="custom-control-input feat-sw" id="featInventory" data-slug="Inventory">
              <label class="custom-control-label font-weight-bold text-black" for="featInventory" style="cursor: pointer;">Manajemen Inventaris</label>
              <small class="form-text text-muted" style="margin-left: 28px;">Melacak stok suplemen, merchandise, handuk, dan logistik gym.</small>
            </div>

            <div class="custom-control custom-switch mb-3">
              <input type="checkbox" name="features[]" value="Mobile App Member Access" class="custom-control-input feat-sw" id="featMobile" data-slug="Mobile">
              <label class="custom-control-label font-weight-bold text-black" for="featMobile" style="cursor: pointer;">Mobile App Member Access</label>
              <small class="form-text text-muted" style="margin-left: 28px;">Memberikan hak login ke aplikasi iOS/Android untuk check-in QR Code.</small>
            </div>

            <div class="custom-control custom-switch mb-3">
              <input type="checkbox" name="features[]" value="Analytics Lanjutan" class="custom-control-input feat-sw" id="featAnalytics" data-slug="Analytics">
              <label class="custom-control-label font-weight-bold text-black" for="featAnalytics" style="cursor: pointer;">Analytics Lanjutan</label>
              <small class="form-text text-muted" style="margin-left: 28px;">Laporan perkiraan churn rate member dan analisis finansial mendalam.</small>
            </div>

            <div class="custom-control custom-switch mb-3">
              <input type="checkbox" name="features[]" value="Kustom Domain Sendiri" class="custom-control-input feat-sw" id="featDomain" data-slug="Domain">
              <label class="custom-control-label font-weight-bold text-black" for="featDomain" style="cursor: pointer;">Kustom Domain Sendiri</label>
              <small class="form-text text-muted" style="margin-left: 28px;">Menggunakan nama domain sendiri untuk branding gym.</small>
            </div>

            <div class="custom-control custom-switch mb-3">
              <input type="checkbox" name="features[]" value="Dedicated Database" class="custom-control-input feat-sw" id="featDatabase" data-slug="Database">
              <label class="custom-control-label font-weight-bold text-black" for="featDatabase" style="cursor: pointer;">Dedicated Database</label>
              <small class="form-text text-muted" style="margin-left: 28px;">Basis data terisolasi khusus untuk performa & keamanan tinggi.</small>
            </div>

            <div class="custom-control custom-switch mb-3">
              <input type="checkbox" name="features[]" value="Support Prioritas 24/7" class="custom-control-input feat-sw" id="featSupport" data-slug="Support">
              <label class="custom-control-label font-weight-bold text-black" for="featSupport" style="cursor: pointer;">Support Prioritas 24/7</label>
              <small class="form-text text-muted" style="margin-left: 28px;">Layanan bantuan teknis dan customer service prioritas 24 jam.</small>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary btn-sm">Simpan Fitur Aktif ke Database</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  $(document).ready(function() {
    // 0. Toggle Masking / Sensor Email Pemilik
    $(document).on('click', '.btn-toggle-email', function(e) {
      e.preventDefault();
      const btn = $(this);
      const emailText = btn.siblings('.email-text');
      const isShown = btn.attr('data-shown') === 'true';

      if (isShown) {
        emailText.text(btn.data('masked'));
        btn.attr('data-shown', 'false');
        btn.find('span').removeClass('icon-eye-slash').addClass('icon-eye');
        btn.attr('title', 'Tampilkan Email Pemilik');
      } else {
        emailText.text(btn.data('full'));
        btn.attr('data-shown', 'true');
        btn.find('span').removeClass('icon-eye').addClass('icon-eye-slash');
        btn.attr('title', 'Sembunyikan Email Pemilik');
      }
    });
    // 1. Membuka Modal Atur Fitur & Populasikan Data DB
    $(document).on('click', '.btn-features-tenant', function(e) {
      e.preventDefault();
      const tenantId = $(this).data('id');
      const tenantName = $(this).data('name');
      const features = $(this).data('features') || [];

      $('#featuresTenantName').text(tenantName);
      $('#configureFeaturesForm').attr('action', "{{ url('/superadmin/tenants') }}/" + tenantId + "/features");

      // Reset centang
      $('.feat-sw').prop('checked', false);

      // Centang fitur aktif dari DB
      $('.feat-sw').each(function() {
        const val = $(this).val();
        const slug = $(this).data('slug');
        if (features.includes(val) || features.includes(slug)) {
          $(this).prop('checked', true);
        }
      });

      $('#configureFeaturesModal').modal('show');
    });

    // 2. Toggle Suspend / Aktifkan Tenant via Database
    $(document).on('click', '.btn-suspend-tenant, .btn-activate-tenant', function(e) {
      e.preventDefault();
      const tenantId = $(this).data('id');
      const tenantName = $(this).data('name');

      fetch("{{ url('/superadmin/tenants') }}/" + tenantId + "/toggle-status", {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === 'success') {
          window.location.reload();
        }
      })
      .catch(err => {
        showToast('Error', 'Gagal memperbarui status tenant di database.', 'error');
      });
    });

    // 3. Hapus Tenant dari Database
    $(document).on('click', '.btn-delete-tenant', function(e) {
      e.preventDefault();
      const tenantId = $(this).data('id');
      const tenantName = $(this).data('name');

      window.showConfirm({
        title: 'Hapus Tenant',
        message: `Apakah Anda yakin ingin menghapus tenant "${tenantName}" dari database?`,
        variant: 'danger',
        confirmText: 'Ya, Hapus'
      }, function() {
        fetch("{{ url('/superadmin/tenants') }}/" + tenantId, {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.status === 'success') {
            window.location.reload();
          }
        })
        .catch(err => {
          showToast('Error', 'Gagal menghapus tenant dari database.', 'error');
        });
      });
    });
  });
</script>
@endsection
