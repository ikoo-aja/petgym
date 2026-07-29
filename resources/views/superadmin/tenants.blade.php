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
          @forelse($tenants as $tenant)
          @php
            $pName = $tenant->plan_name ?? $tenant['plan_name'] ?? $tenant['plan'] ?? 'Basic';
            $oName = $tenant->owner_name ?? $tenant['owner_name'] ?? $tenant['owner'] ?? 'Owner';
            $oEmail = $tenant->owner_email ?? $tenant['owner_email'] ?? $tenant['email'] ?? 'owner@gym.com';
            $expDays = isset($tenant->expires_in_days) ? $tenant->expires_in_days : ($tenant['expires_in'] ?? 30);
            $featuresList = is_array($tenant->features) ? $tenant->features : ($tenant['features'] ?? ['POS', 'Class']);
          @endphp
          <tr>
            <td>
              <strong class="text-black">{{ $tenant->name ?? $tenant['name'] }}</strong><br>
              <small class="text-muted">{{ $tenant->subdomain ?? $tenant['subdomain'] }}</small>
            </td>
            <td>
              {{ $oName }}<br>
              <small class="text-muted">{{ $oEmail }}</small>
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
            <td>{{ is_object($tenant->joined_at) ? $tenant->joined_at->format('d M Y') : ($tenant['joined_at'] ?? 'Hari Ini') }}</td>
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
                  <!-- Impersonation Action -->
                  <a class="dropdown-item btn-login-tenant" href="#" data-name="{{ $tenant->name ?? $tenant['name'] }}">
                    <span class="icon-forward text-primary mr-2"></span> Login as Tenant
                  </a>
                  <a class="dropdown-item btn-features-tenant" href="#">
                    <span class="icon-settings text-secondary mr-2"></span> Atur Fitur
                  </a>
                  @if($tenant->status == 'active')
                    <a class="dropdown-item text-warning btn-suspend-tenant" href="#">
                      <span class="icon-pause mr-2"></span> Suspend
                    </a>
                  @else
                    <a class="dropdown-item text-success btn-activate-tenant" href="#">
                      <span class="icon-play_arrow mr-2"></span> Aktifkan
                    </a>
                  @endif
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item text-danger btn-delete-tenant" href="#">
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
        <h5 class="modal-header-title font-weight-bold text-black" id="exampleModalLabel">Manual Onboarding Gym Baru</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="#" method="POST" id="addTenantForm">
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
            <select name="plan" class="form-control" id="addTenantPlan">
              <option value="basic">Basic Plan</option>
              <option value="pro">Pro Plan</option>
              <option value="enterprise">Enterprise Plan</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary btn-sm">Buat Akun Tenant</button>
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
      <form action="#" method="POST" id="configureFeaturesForm">
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
              <input type="checkbox" class="custom-control-input feat-sw" id="featPOS" data-slug="POS">
              <label class="custom-control-label font-weight-bold text-black" for="featPOS" style="cursor: pointer;">Akses POS / Kasir</label>
              <small class="form-text text-muted" style="margin-left: 28px;">Mengaktifkan modul transaksi kasir dan invoice harian.</small>
            </div>
            
            <div class="custom-control custom-switch mb-3">
              <input type="checkbox" class="custom-control-input feat-sw" id="featClass" data-slug="Class">
              <label class="custom-control-label font-weight-bold text-black" for="featClass" style="cursor: pointer;">Manajemen Kelas & Sesi</label>
              <small class="form-text text-muted" style="margin-left: 28px;">Membuat jadwal kelas, booking member, dan absensi trainer.</small>
            </div>
            
            <div class="custom-control custom-switch mb-3">
              <input type="checkbox" class="custom-control-input feat-sw" id="featTrainer" data-slug="Trainer">
              <label class="custom-control-label font-weight-bold text-black" for="featTrainer" style="cursor: pointer;">Manajemen Trainer</label>
              <small class="form-text text-muted" style="margin-left: 28px;">Penjadwalan personal trainer (PT) dan perhitungan komisi.</small>
            </div>
            
            <div class="custom-control custom-switch mb-3">
              <input type="checkbox" class="custom-control-input feat-sw" id="featInventory" data-slug="Inventory">
              <label class="custom-control-label font-weight-bold text-black" for="featInventory" style="cursor: pointer;">Manajemen Inventaris</label>
              <small class="form-text text-muted" style="margin-left: 28px;">Melacak stok suplemen, merchandise, handuk, dan logistik gym.</small>
            </div>
            
            <div class="custom-control custom-switch mb-3">
              <input type="checkbox" class="custom-control-input feat-sw" id="featMobile" data-slug="Mobile">
              <label class="custom-control-label font-weight-bold text-black" for="featMobile" style="cursor: pointer;">Akses Mobile App Member</label>
              <small class="form-text text-muted" style="margin-left: 28px;">Memberikan hak login ke aplikasi iOS/Android untuk check-in QR Code.</small>
            </div>

            <div class="custom-control custom-switch mb-3">
              <input type="checkbox" class="custom-control-input feat-sw" id="featAnalytics" data-slug="Analytics">
              <label class="custom-control-label font-weight-bold text-black" for="featAnalytics" style="cursor: pointer;">Advanced Analytics & Laporan</label>
              <small class="form-text text-muted" style="margin-left: 28px;">Laporan perkiraan churn rate member dan analisis finansial mendalam.</small>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary btn-sm">Simpan Fitur Aktif</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  $(document).ready(function() {
    let currentFeaturesRow = null;

    // 1. Submit form tambah tenant secara dinamis
    $('#addTenantForm').on('submit', function(e) {
      e.preventDefault();
      const name = $('input[name="name"]').val();
      const sub = $('input[name="subdomain"]').val();
      const subdomain = sub + '.workout.id';
      const email = $('input[name="owner_email"]').val();
      const owner = email.split('@')[0];
      const plan = $('select[name="plan"] option:selected').text();
      const planVal = $('#addTenantPlan').val();
      const joinedDate = 'Hari Ini';
      
      // Tentukan badge fitur default berdasarkan pilihan paket
      let featBadges = '';
      if (planVal === 'enterprise') {
        featBadges = `
          <span class="badge badge-light border text-muted px-1" style="font-size: 10px; margin-right: 3px; margin-bottom: 2px;">POS</span>
          <span class="badge badge-light border text-muted px-1" style="font-size: 10px; margin-right: 3px; margin-bottom: 2px;">Class</span>
          <span class="badge badge-light border text-muted px-1" style="font-size: 10px; margin-right: 3px; margin-bottom: 2px;">Trainer</span>
          <span class="badge badge-light border text-muted px-1" style="font-size: 10px; margin-right: 3px; margin-bottom: 2px;">Inventory</span>
          <span class="badge badge-light border text-muted px-1" style="font-size: 10px; margin-right: 3px; margin-bottom: 2px;">Mobile</span>
          <span class="badge badge-light border text-muted px-1" style="font-size: 10px; margin-right: 3px; margin-bottom: 2px;">Analytics</span>
        `;
      } else if (planVal === 'pro') {
        featBadges = `
          <span class="badge badge-light border text-muted px-1" style="font-size: 10px; margin-right: 3px; margin-bottom: 2px;">POS</span>
          <span class="badge badge-light border text-muted px-1" style="font-size: 10px; margin-right: 3px; margin-bottom: 2px;">Class</span>
          <span class="badge badge-light border text-muted px-1" style="font-size: 10px; margin-right: 3px; margin-bottom: 2px;">Trainer</span>
          <span class="badge badge-light border text-muted px-1" style="font-size: 10px; margin-right: 3px; margin-bottom: 2px;">Inventory</span>
        `;
      } else {
        featBadges = `
          <span class="badge badge-light border text-muted px-1" style="font-size: 10px; margin-right: 3px; margin-bottom: 2px;">POS</span>
          <span class="badge badge-light border text-muted px-1" style="font-size: 10px; margin-right: 3px; margin-bottom: 2px;">Class</span>
        `;
      }

      const newRow = `
        <tr class="new-tenant-row" style="background-color: #fff9f9;">
          <td>
            <strong class="text-black">${name}</strong><br>
            <small class="text-muted">${subdomain}</small>
          </td>
          <td>
            ${owner}<br>
            <small class="text-muted">${email}</small>
          </td>
          <td>
            <span class="badge badge-info">${plan}</span>
            <div class="tenant-features-list mt-1 d-flex flex-wrap">${featBadges}</div>
          </td>
          <td>${joinedDate}</td>
          <td><span class="text-success font-weight-bold" style="font-weight: 700;">14 Hari (Trial)</span></td>
          <td><span class="badge badge-status-active px-2 py-1 rounded">Aktif</span></td>
          <td>
            <div class="dropdown">
              <button class="btn btn-sm btn-outline-secondary dropdown-toggle py-1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="font-size: 12px; font-weight: 700;">
                Pilihan
              </button>
              <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item btn-login-tenant" href="#" data-name="${name}">
                  <span class="icon-forward text-primary mr-2"></span> Login as Tenant
                </a>
                <a class="dropdown-item btn-features-tenant" href="#">
                  <span class="icon-settings text-secondary mr-2"></span> Atur Fitur
                </a>
                <a class="dropdown-item text-warning btn-suspend-tenant" href="#">
                  <span class="icon-pause mr-2"></span> Suspend
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item text-danger btn-delete-tenant" href="#">
                  <span class="icon-close mr-2"></span> Hapus
                </a>
              </div>
            </div>
          </td>
        </tr>
      `;
      
      $('table tbody').prepend(newRow);
      $('#addTenantModal').modal('hide');
      $('#addTenantForm')[0].reset();
      
      showToast('Penyewa Ditambahkan', `Gym "${name}" berhasil didaftarkan pada subdomain ${subdomain}`, 'success');

      setTimeout(function() {
        $('.new-tenant-row').first().css('background-color', '');
      }, 2000);
    });

    // 2. Aksi Login as Tenant (Impersonasi)
    $(document).on('click', '.btn-login-tenant', function(e) {
      e.preventDefault();
      const tenantName = $(this).data('name');
      
      $('#overlay-text').text(`Menghubungkan ke dashboard ${tenantName}...`);
      $('#impersonation-overlay').css('display', 'flex').hide().fadeIn(300);
      
      setTimeout(function() {
        localStorage.setItem('impersonating_tenant', tenantName);
        window.location.reload();
      }, 1500);
    });

    // 3. Aksi Suspend Tenant
    $(document).on('click', '.btn-suspend-tenant', function(e) {
      e.preventDefault();
      const row = $(this).closest('tr');
      const statusBadge = row.find('.badge-status-active, .badge-status-suspended');
      const expireCell = row.find('td:nth-child(5)');
      const tenantName = row.find('strong').first().text();
      
      statusBadge.removeClass('badge-status-active').addClass('badge-status-suspended').text('Suspended');
      expireCell.html('<span class="text-danger" style="font-weight: 700;">N/A (Suspended)</span>');
      
      $(this).removeClass('text-warning btn-suspend-tenant').addClass('text-success btn-activate-tenant').html('<span class="icon-play_arrow mr-2"></span> Aktifkan');
      showToast('Akses Ditangguhkan', `Akses untuk "${tenantName}" dinonaktifkan sementara.`, 'warning');
    });

    // 4. Aksi Aktifkan Tenant
    $(document).on('click', '.btn-activate-tenant', function(e) {
      e.preventDefault();
      const row = $(this).closest('tr');
      const statusBadge = row.find('.badge-status-active, .badge-status-suspended');
      const expireCell = row.find('td:nth-child(5)');
      const tenantName = row.find('strong').first().text();
      
      statusBadge.removeClass('badge-status-suspended').addClass('badge-status-active').text('Aktif');
      expireCell.html('<span class="text-success font-weight-bold" style="font-weight: 700;">30 Hari</span>');
      
      $(this).removeClass('text-success btn-activate-tenant').addClass('text-warning btn-suspend-tenant').html('<span class="icon-pause mr-2"></span> Suspend');
      showToast('Akses Dipulihkan', `Akses tenant "${tenantName}" telah aktif kembali.`, 'success');
    });

    // 5. Aksi Hapus Tenant
    $(document).on('click', '.btn-delete-tenant', function(e) {
      e.preventDefault();
      const row = $(this).closest('tr');
      const tenantName = row.find('strong').first().text();
      
      row.css('background-color', '#fff3f3');
      setTimeout(function() {
        row.fadeOut(400, function() {
          row.remove();
          showToast('Tenant Dihapus', `Data tenant "${tenantName}" berhasil dibersihkan dari sistem.`, 'error');
        });
      }, 100);
    });

    // 6. Aksi Atur Fitur Tenant (Membuka Modal dan Memetakan Centang)
    $(document).on('click', '.btn-features-tenant', function(e) {
      e.preventDefault();
      const row = $(this).closest('tr');
      currentFeaturesRow = row;
      const tenantName = row.find('strong').first().text();

      $('#featuresTenantName').text(tenantName);

      // Reset semua centang
      $('.feat-sw').prop('checked', false);

      // Cari badge yang aktif di baris ini
      row.find('.tenant-features-list span').each(function() {
        const slug = $(this).text().trim();
        $(`.feat-sw[data-slug="${slug}"]`).prop('checked', true);
      });

      $('#configureFeaturesModal').modal('show');
    });

    // 7. Simpan Fitur Aktif secara dinamis
    $('#configureFeaturesForm').on('submit', function(e) {
      e.preventDefault();
      if (!currentFeaturesRow) return;

      const listContainer = currentFeaturesRow.find('.tenant-features-list');
      listContainer.empty();

      // Regenerasi badge di baris tabel sesuai centang
      $('.feat-sw:checked').each(function() {
        const slug = $(this).data('slug');
        listContainer.append(`<span class="badge badge-light border text-muted px-1" style="font-size: 10px; margin-right: 3px; margin-bottom: 2px;">${slug}</span>`);
      });

      const tenantName = $('#featuresTenantName').text();
      $('#configureFeaturesModal').modal('hide');
      showToast('Fitur Diperbarui', `Modul fitur aktif untuk "${tenantName}" berhasil disimpan & disinkronkan.`, 'success');
    });
  });
</script>
@endsection
