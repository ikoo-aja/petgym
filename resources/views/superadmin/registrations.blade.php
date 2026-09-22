@extends('layouts.superadmin')

@section('title', 'Pendaftaran Penyewa — Superadmin Panel')

@section('page_title', 'Pendaftaran Penyewa')
@section('page_subtitle', 'Kelola calon penyewa web gym yang mendaftar dari landing page')

@section('content')
<section id="registrations" class="mb-5">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="font-weight-bold text-black mb-0">Daftar Pengajuan Calon Penyewa</h4>
  </div>

  @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-radius: 10px;">
      <ul class="mb-0 pl-3">
        @foreach($errors->all() as $err)
          <li class="font-weight-bold small">{{ $err }}</li>
        @endforeach
      </ul>
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
  @endif

  <div class="table-custom p-4">
    <!-- Filter Bar -->
    <form action="{{ route('superadmin.registrations') }}" method="GET" class="row mb-4">
      <div class="col-md-6 mb-2 mb-md-0">
        <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama, email, no WA, atau paket..." value="{{ request('search') }}">
      </div>
      <div class="col-md-4 mb-2 mb-md-0">
        <select name="status" class="form-control form-control-sm" onchange="this.form.submit()">
          <option value="">Semua Pendaftaran</option>
          <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Belum Dibuatkan Akun</option>
          <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Sudah Dibuatkan Akun</option>
        </select>
      </div>
      <div class="col-md-2">
        <button type="submit" class="btn btn-sm btn-outline-primary px-3 btn-block">Filter</button>
      </div>
    </form>

    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead>
          <tr>
            <th class="text-black font-weight-bold" style="width: 140px;">Tanggal</th>
            <th class="text-black font-weight-bold">Nama Penyewa</th>
            <th class="text-black font-weight-bold">Kontak</th>
            <th class="text-black font-weight-bold">Paket</th>
            <th class="text-black font-weight-bold" style="min-width: 200px;">Deskripsi</th>
            <th class="text-black font-weight-bold text-right" style="min-width: 220px;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($registrations as $reg)
          <tr>
            <!-- 1. Tanggal -->
            <td>
              <span class="text-dark font-weight-bold" style="font-size: 13px;">{{ $reg->created_at->format('d M Y') }}</span><br>
              <small class="text-muted">{{ $reg->created_at->format('H:i') }} WIB</small>
            </td>

            <!-- 2. Nama Penyewa -->
            <td>
              <strong class="text-dark" style="font-size: 14px;">{{ $reg->name }}</strong>
            </td>

            <!-- 3. Kontak (WA & Email) -->
            <td>
              <div class="d-flex flex-column" style="gap: 3px;">
                <span class="text-dark font-weight-bold" style="font-size: 12px;">
                  <i class="icon-phone mr-1 text-success"></i> {{ $reg->phone }}
                </span>
                <span class="text-muted small">
                  <i class="icon-envelope mr-1 text-primary"></i> {{ $reg->email }}
                </span>
              </div>
            </td>

            <!-- 4. Paket -->
            <td>
              @if(stripos($reg->plan_name, 'Enterprise') !== false)
                <span class="badge badge-primary px-2 py-1 font-weight-bold">{{ $reg->plan_name }}</span>
              @elseif(stripos($reg->plan_name, 'Pro') !== false)
                <span class="badge badge-info px-2 py-1 font-weight-bold">{{ $reg->plan_name }}</span>
              @else
                <span class="badge badge-secondary px-2 py-1 font-weight-bold">{{ $reg->plan_name }}</span>
              @endif
            </td>

            <!-- 5. Deskripsi (Input dari Penyewa) -->
            <td>
              @if(!empty($reg->notes))
                <div class="text-dark small" style="line-height: 1.4; max-width: 280px; word-break: break-word;">
                  {{ $reg->notes }}
                </div>
              @else
                <span class="text-muted small font-italic">- Tidak ada deskripsi -</span>
              @endif
            </td>

            <!-- 6. Aksi (Hubungi -> WA / Email dan Buat Akun) -->
            <td class="text-right">
              <div class="d-inline-flex align-items-center" style="gap: 6px;">
                <!-- Dropdown Hubungi -->
                <div class="dropdown">
                  <button class="btn btn-sm btn-outline-success dropdown-toggle py-1 px-2 font-weight-bold" type="button" id="hubungiMenu{{ $reg->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="font-size: 12px;">
                    <span class="icon-phone"></span> Hubungi
                  </button>
                  <div class="dropdown-menu dropdown-menu-right shadow-sm border-0" aria-labelledby="hubungiMenu{{ $reg->id }}" style="border-radius: 8px;">
                    <a class="dropdown-item py-2 text-success font-weight-bold small" href="{{ $reg->whatsapp_url }}" target="_blank">
                      <span class="icon-phone mr-2"></span> Chat WhatsApp ({{ $reg->phone }})
                    </a>
                    <a class="dropdown-item py-2 text-primary font-weight-bold small" href="mailto:{{ $reg->email }}?subject=Konfirmasi%20Penyewaan%20Web%20Gym%20-%20PetGym%20SaaS">
                      <span class="icon-envelope mr-2"></span> Kirim Email ({{ $reg->email }})
                    </a>
                  </div>
                </div>

                <!-- Tombol Buat Akun -->
                @if($reg->status !== 'approved')
                  <button type="button" class="btn btn-sm btn-primary py-1 px-3 font-weight-bold" data-toggle="modal" data-target="#approveModal{{ $reg->id }}" style="font-size: 12px;">
                    <span class="icon-check mr-1"></span> Buat Akun
                  </button>
                @else
                  <span class="badge badge-light border border-success px-2 py-2 text-success font-weight-bold small">
                    <span class="icon-check mr-1"></span> Akun Dibuat
                  </span>
                @endif

                <!-- Tombol Hapus -->
                <div class="dropdown">
                  <button class="btn btn-sm btn-link text-muted p-1" type="button" data-toggle="dropdown" title="Opsi Lainnya">
                    <span class="icon-more_vert"></span>
                  </button>
                  <div class="dropdown-menu dropdown-menu-right shadow-sm border-0" style="border-radius: 8px;">
                    <form action="{{ route('superadmin.registrations.destroy', $reg->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data calon penyewa ini?');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="dropdown-item small text-danger">
                        <span class="icon-trash mr-1"></span> Hapus Data
                      </button>
                    </form>
                  </div>
                </div>
              </div>

              <!-- MODAL BUAT AKUN PENYEWA -->
              <div class="modal fade text-left" id="approveModal{{ $reg->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                  <form action="{{ route('superadmin.registrations.approve', $reg->id) }}" method="POST" class="modal-content shadow-lg border-0" style="border-radius: 14px; overflow: hidden;">
                    @csrf
                    <div class="modal-header bg-white border-bottom py-3 px-4 text-dark">
                      <div>
                        <h5 class="modal-title font-weight-bold text-dark mb-0">Buat Akun Penyewa Gym</h5>
                        <small class="text-muted">Setujui pendaftaran dan aktifkan akses admin penyewa</small>
                      </div>
                      <button type="button" class="close text-dark" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body p-4" style="max-height: 75vh; overflow-y: auto;">
                      <!-- Info Notifikasi -->
                      <div class="alert alert-info border-0 p-3 mb-4 rounded d-flex align-items-start" style="font-size: 13px; background-color: #f0f9ff; color: #0369a1; border-radius: 8px;">
                        <span class="icon-info mr-2 mt-1 font-weight-bold" style="font-size: 16px;"></span>
                        <div>
                          Akun akan otomatis masuk ke <strong>Kelola Penyewa</strong> dengan masa aktif 30 hari. Penyewa dapat langsung login dengan kredensial ini.
                        </div>
                      </div>

                      <!-- Data Calon Penyewa -->
                      <div class="row">
                        <div class="col-md-6 form-group mb-3">
                          <label class="font-weight-bold text-dark small">Nama Calon Penyewa</label>
                          <input type="text" class="form-control" value="{{ $reg->name }}" readonly style="background-color: #f8fafc; font-weight: 600;">
                        </div>
                        <div class="col-md-6 form-group mb-3">
                          <label class="font-weight-bold text-dark small">Nomor WhatsApp</label>
                          <input type="text" class="form-control" value="{{ $reg->phone }}" readonly style="background-color: #f8fafc; font-weight: 600;">
                        </div>
                      </div>

                      <!-- Kredensial Login -->
                      <div class="row">
                        <div class="col-md-6 form-group mb-3">
                          <label class="font-weight-bold text-dark small">Email Login Penyewa <span class="text-danger">*</span></label>
                          <input type="email" name="email" class="form-control" value="{{ $reg->email }}" required>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                          <label class="font-weight-bold text-dark small">Kata Sandi Masuk Awal <span class="text-danger">*</span></label>
                          <div class="input-group">
                            <input type="text" name="password" id="passInput{{ $reg->id }}" class="form-control font-weight-bold" value="1234" required>
                            <div class="input-group-append">
                              <button type="button" class="btn btn-outline-secondary btn-sm" onclick="document.getElementById('passInput{{ $reg->id }}').value = Math.random().toString(36).slice(-8);">Acak</button>
                            </div>
                          </div>
                          <small class="text-muted">Kata sandi awal: <strong>1234</strong></small>
                        </div>
                      </div>

                      <!-- PILIHAN PAKET SEWA DENGAN SPESIFIKASI LENGKAP -->
                      <div class="form-group mb-4">
                        <label class="font-weight-bold text-dark small d-block mb-2">Pilih Paket Sewa Gym:</label>
                        <div class="row">
                          <!-- 1. Paket Basic -->
                          <div class="col-md-4 mb-3">
                            <label class="plan-card-option d-block h-100 p-3 border rounded cursor-pointer position-relative {{ stripos($reg->plan_name, 'Basic') !== false ? 'border-primary bg-light-primary' : 'border-light-gray' }}" style="cursor: pointer; transition: all 0.2s;" for="plan_basic_{{ $reg->id }}">
                              <div class="custom-control custom-radio mb-2">
                                <input type="radio" id="plan_basic_{{ $reg->id }}" name="plan_id" value="{{ $plans->firstWhere('name', 'Paket Basic')->id ?? 1 }}" class="custom-control-input plan-radio" {{ stripos($reg->plan_name, 'Basic') !== false || (!stripos($reg->plan_name, 'Pro') && !stripos($reg->plan_name, 'Enterprise')) ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-bold text-dark" for="plan_basic_{{ $reg->id }}" style="font-size: 14px;">Paket Basic</label>
                              </div>
                              <p class="text-muted small mb-2" style="font-size: 11px; line-height: 1.3;">Cocok untuk gym skala kecil atau baru buka.</p>
                              <div class="text-primary font-weight-bold mb-2" style="font-size: 15px;">
                                Rp 500.000 <small class="text-muted font-weight-normal">/ bulan</small>
                              </div>
                              <ul class="list-unstyled text-dark mb-0 small" style="font-size: 11px; line-height: 1.6;">
                                <li><i class="icon-check mr-1 text-primary"></i> Kapasitas maks 150 Member Aktif</li>
                                <li><i class="icon-check mr-1 text-primary"></i> Maks 5 Akun Karyawan (Admin & Resepsionis)</li>
                                <li><i class="icon-check mr-1 text-primary"></i> Termasuk Modul POS, Kasir & Check-in</li>
                              </ul>
                            </label>
                          </div>

                          <!-- 2. Paket Pro (Paling Direkomendasikan) -->
                          <div class="col-md-4 mb-3">
                            <label class="plan-card-option d-block h-100 p-3 border rounded cursor-pointer position-relative {{ stripos($reg->plan_name, 'Pro') !== false ? 'border-primary bg-light-primary' : 'border-light-gray' }}" style="cursor: pointer; transition: all 0.2s; border-width: 2px;" for="plan_pro_{{ $reg->id }}">
                              <span class="badge badge-warning text-dark font-weight-bold px-2 py-1 position-absolute" style="top: -10px; right: 10px; font-size: 10px;">Paling Direkomendasikan</span>
                              <div class="custom-control custom-radio mb-2">
                                <input type="radio" id="plan_pro_{{ $reg->id }}" name="plan_id" value="{{ $plans->firstWhere('name', 'Paket Pro')->id ?? 2 }}" class="custom-control-input plan-radio" {{ stripos($reg->plan_name, 'Pro') !== false ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-bold text-dark" for="plan_pro_{{ $reg->id }}" style="font-size: 14px;">Paket Pro</label>
                              </div>
                              <p class="text-muted small mb-2" style="font-size: 11px; line-height: 1.3;">Ideal untuk gym berkembang dengan operasional penuh.</p>
                              <div class="text-primary font-weight-bold mb-2" style="font-size: 15px;">
                                Rp 1.200.000 <small class="text-muted font-weight-normal">/ bulan</small>
                              </div>
                              <ul class="list-unstyled text-dark mb-0 small" style="font-size: 11px; line-height: 1.6;">
                                <li><i class="icon-check mr-1 text-primary"></i> Kapasitas maks 500 Member Aktif</li>
                                <li><i class="icon-check mr-1 text-primary"></i> Maks 15 Akun Karyawan (Manager & PT)</li>
                                <li><i class="icon-check mr-1 text-primary"></i> Semua fitur Basic + Inventaris Ritel, Retensi & Analitik Kelas</li>
                              </ul>
                            </label>
                          </div>

                          <!-- 3. Paket Enterprise -->
                          <div class="col-md-4 mb-3">
                            <label class="plan-card-option d-block h-100 p-3 border rounded cursor-pointer position-relative {{ stripos($reg->plan_name, 'Enterprise') !== false ? 'border-primary bg-light-primary' : 'border-light-gray' }}" style="cursor: pointer; transition: all 0.2s;" for="plan_enterprise_{{ $reg->id }}">
                              <div class="custom-control custom-radio mb-2">
                                <input type="radio" id="plan_enterprise_{{ $reg->id }}" name="plan_id" value="{{ $plans->firstWhere('name', 'Paket Enterprise')->id ?? 3 }}" class="custom-control-input plan-radio" {{ stripos($reg->plan_name, 'Enterprise') !== false ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-bold text-dark" for="plan_enterprise_{{ $reg->id }}" style="font-size: 14px;">Paket Enterprise</label>
                              </div>
                              <p class="text-muted small mb-2" style="font-size: 11px; line-height: 1.3;">Solusi terlengkap untuk mega gym & multi-cabang.</p>
                              <div class="text-primary font-weight-bold mb-2" style="font-size: 15px;">
                                Rp 2.500.000 <small class="text-muted font-weight-normal">/ bulan</small>
                              </div>
                              <ul class="list-unstyled text-dark mb-0 small" style="font-size: 11px; line-height: 1.6;">
                                <li><i class="icon-check mr-1 text-primary"></i> Kapasitas Member & Karyawan Unlimited</li>
                                <li><i class="icon-check mr-1 text-primary"></i> Semua fitur Pro + Custom Domain & Cabang</li>
                                <li><i class="icon-check mr-1 text-primary"></i> Prioritas Support 24/7 & Dedicated Server</li>
                              </ul>
                            </label>
                          </div>
                        </div>
                      </div>

                      <!-- FITUR AKTIF TENANT (CHECKBOX DAPAT DIATUR SUPERADMIN) -->
                      <div class="form-group mb-3 bg-light p-3 rounded border">
                        <label class="font-weight-bold text-dark small d-block mb-2">Kustomisasi Fitur Aktif Tenant (Dapat Diatur):</label>
                        <div class="row px-2">
                          <div class="col-md-4 mb-2 custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="featMember{{ $reg->id }}" name="features[]" value="members" checked>
                            <label class="custom-control-label small font-weight-bold" for="featMember{{ $reg->id }}">Manajemen Member</label>
                          </div>
                          <div class="col-md-4 mb-2 custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="featPos{{ $reg->id }}" name="features[]" value="pos" checked>
                            <label class="custom-control-label small font-weight-bold" for="featPos{{ $reg->id }}">Kasir / POS & Produk</label>
                          </div>
                          <div class="col-md-4 mb-2 custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="featClasses{{ $reg->id }}" name="features[]" value="classes" checked>
                            <label class="custom-control-label small font-weight-bold" for="featClasses{{ $reg->id }}">Jadwal Kelas & Trainer</label>
                          </div>
                          <div class="col-md-4 mb-2 custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="featLockers{{ $reg->id }}" name="features[]" value="lockers" checked>
                            <label class="custom-control-label small font-weight-bold" for="featLockers{{ $reg->id }}">Master Loker</label>
                          </div>
                          <div class="col-md-4 mb-2 custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="featStaff{{ $reg->id }}" name="features[]" value="staff" checked>
                            <label class="custom-control-label small font-weight-bold" for="featStaff{{ $reg->id }}">Akun Staf (Manager/PT)</label>
                          </div>
                          <div class="col-md-4 mb-2 custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="featReports{{ $reg->id }}" name="features[]" value="reports" checked>
                            <label class="custom-control-label small font-weight-bold" for="featReports{{ $reg->id }}">Laporan & Ekspor CSV</label>
                          </div>
                          <div class="col-md-4 mb-2 custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="featInventory{{ $reg->id }}" name="features[]" value="inventory" {{ stripos($reg->plan_name, 'Pro') !== false || stripos($reg->plan_name, 'Enterprise') !== false ? 'checked' : '' }}>
                            <label class="custom-control-label small font-weight-bold" for="featInventory{{ $reg->id }}">Inventaris Ritel</label>
                          </div>
                          <div class="col-md-4 mb-2 custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="featRetention{{ $reg->id }}" name="features[]" value="retention" {{ stripos($reg->plan_name, 'Pro') !== false || stripos($reg->plan_name, 'Enterprise') !== false ? 'checked' : '' }}>
                            <label class="custom-control-label small font-weight-bold" for="featRetention{{ $reg->id }}">Retensi & Analitik</label>
                          </div>
                          <div class="col-md-4 mb-2 custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="featDomain{{ $reg->id }}" name="features[]" value="custom_domain" {{ stripos($reg->plan_name, 'Enterprise') !== false ? 'checked' : '' }}>
                            <label class="custom-control-label small font-weight-bold" for="featDomain{{ $reg->id }}">Custom Domain & Cabang</label>
                          </div>
                        </div>
                      </div>

                      <!-- Deskripsi / Catatan Prospek -->
                      <div class="form-group mb-0">
                        <label class="font-weight-bold text-dark small">Catatan Tambahan Superadmin (Opsional)</label>
                        <textarea name="notes" rows="2" class="form-control" placeholder="Catatan pembayaran, konfirmasi WA, dsb...">{{ $reg->notes }}</textarea>
                      </div>
                    </div>

                    <div class="modal-footer bg-light py-3 px-4">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                      <button type="submit" class="btn btn-primary font-weight-bold px-4">
                        <span class="icon-check mr-1"></span> Buat Akun & Aktifkan Penyewa
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="6" class="text-center py-5 text-muted">
              <span class="icon-inbox h3 d-block mb-2 text-muted"></span>
              Belum ada data calon penyewa yang mendaftar.
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-3">
      <small class="text-muted">Menampilkan {{ $registrations->count() }} dari {{ $registrations->total() }} data pendaftaran</small>
      {{ $registrations->links('pagination::bootstrap-4') }}
    </div>
  </div>
</section>

<style>
  .cursor-pointer { cursor: pointer; }
  .bg-light-primary { background-color: #f0fdf4 !important; border-color: #22c55e !important; }
  .border-light-gray { border-color: #e2e8f0 !important; }
  .plan-card-option:hover { border-color: #0ea5e9 !important; }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Interactive card border when radio changes
    document.querySelectorAll('.plan-radio').forEach(function(radio) {
      radio.addEventListener('change', function() {
        var modal = this.closest('.modal');
        if (modal) {
          modal.querySelectorAll('.plan-card-option').forEach(function(card) {
            card.classList.remove('border-primary', 'bg-light-primary');
            card.classList.add('border-light-gray');
          });
          var parentLabel = this.closest('.plan-card-option');
          if (parentLabel) {
            parentLabel.classList.add('border-primary', 'bg-light-primary');
            parentLabel.classList.remove('border-light-gray');
          }
        }
      });
    });
  });
</script>
@endsection
