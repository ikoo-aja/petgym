@extends('layouts.superadmin')

@section('title', 'Pendaftaran Penyewa — Superadmin Panel')

@section('page_title', 'Pendaftaran Penyewa')
@section('page_subtitle', 'Kelola calon penyewa web gym yang mendaftar dari landing page atau pendaftaran manual')

@section('content')
<section id="registrations" class="mb-5">
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
    <div>
      <h4 class="font-weight-bold text-black mb-0">Daftar Pengajuan Calon Penyewa</h4>
      <small class="text-muted">Prospek gym yang siap ditindaklanjuti dan dibuatkan akses admin sistem</small>
    </div>
    <div class="mt-2 mt-md-0">
      <button class="btn btn-primary btn-sm px-3 font-weight-bold shadow-sm" data-toggle="modal" data-target="#createRegistrationModal">
        <span class="icon-plus mr-1"></span> Tambah Calon Penyewa
      </button>
    </div>
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
    <!-- Filter & Search Bar -->
    <form action="{{ route('superadmin.registrations') }}" method="GET" class="row mb-4">
      <div class="col-md-6 mb-2 mb-md-0">
        <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama, email, no WA, paket, atau catatan..." value="{{ request('search') }}">
      </div>
      <div class="col-md-4 mb-2 mb-md-0">
        <select name="status" class="form-control form-control-sm" onchange="this.form.submit()">
          <option value="">Semua Status Pendaftaran</option>
          <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu Tindak Lanjut (Pending)</option>
          <option value="contacted" {{ request('status') == 'contacted' ? 'selected' : '' }}>Sudah Dihubungi (Contacted)</option>
          <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Sudah Dibuatkan Akun (Approved)</option>
          <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak / Batal (Rejected)</option>
        </select>
      </div>
      <div class="col-md-2">
        <button type="submit" class="btn btn-sm btn-outline-primary px-3 btn-block font-weight-bold">Filter</button>
      </div>
    </form>

    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead>
          <tr>
            <th class="text-black font-weight-bold" style="width: 130px;">Tanggal</th>
            <th class="text-black font-weight-bold">Nama Calon Penyewa</th>
            <th class="text-black font-weight-bold">Kontak</th>
            <th class="text-black font-weight-bold">Paket Diminati</th>
            <th class="text-black font-weight-bold">Status</th>
            <th class="text-black font-weight-bold" style="min-width: 180px;">Catatan</th>
            <th class="text-black font-weight-bold text-right" style="min-width: 220px;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($registrations as $reg)
          @php
            $regStatus = strtolower($reg->status ?? 'pending');
          @endphp
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
              <div class="d-flex flex-column" style="gap: 2px;">
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
              @php
                $pName = $reg->plan_name ?? 'Basic';
                $badgePlan = 'badge-secondary';
                if (stripos($pName, 'Enterprise') !== false) $badgePlan = 'badge-primary';
                elseif (stripos($pName, 'Pro') !== false) $badgePlan = 'badge-info';
                elseif (stripos($pName, 'Basic') !== false) $badgePlan = 'badge-success';
              @endphp
              <span class="badge {{ $badgePlan }} px-2 py-1 font-weight-bold">{{ $pName }}</span>
            </td>

            <!-- 5. Status Prospek -->
            <td>
              @if($regStatus === 'approved')
                <span class="badge badge-success px-2 py-1 font-weight-bold">
                  <i class="icon-check mr-1"></i> Akun Aktif
                </span>
              @elseif($regStatus === 'contacted')
                <span class="badge badge-info px-2 py-1 font-weight-bold">
                  <i class="icon-phone mr-1"></i> Dihubungi
                </span>
              @elseif($regStatus === 'rejected')
                <span class="badge badge-danger px-2 py-1 font-weight-bold">
                  <i class="icon-close mr-1"></i> Ditolak
                </span>
              @else
                <span class="badge badge-warning text-dark px-2 py-1 font-weight-bold">
                  <i class="icon-clock-o mr-1"></i> Pending
                </span>
              @endif
            </td>

            <!-- 6. Deskripsi / Catatan Prospek -->
            <td>
              @if(!empty($reg->notes))
                <div class="text-dark small" style="line-height: 1.4; max-width: 240px; word-break: break-word;">
                  {{ $reg->notes }}
                </div>
              @else
                <span class="text-muted small font-italic">- Tidak ada catatan -</span>
              @endif
            </td>

            <!-- 7. Aksi Kontrol Lengkap -->
            <td class="text-right">
              <div class="d-inline-flex align-items-center" style="gap: 5px;">
                <!-- Tombol Hubungi WhatsApp / Email -->
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

                <!-- Tombol Buat Akun (Hanya jika belum approved) -->
                @if($regStatus !== 'approved')
                  <button type="button" class="btn btn-sm btn-primary py-1 px-3 font-weight-bold" data-toggle="modal" data-target="#approveModal{{ $reg->id }}" style="font-size: 12px;">
                    <span class="icon-check mr-1"></span> Buat Akun
                  </button>
                @endif

                <!-- Dropdown Opsi: Reset Password, Ubah Status, Hapus -->
                <div class="dropdown">
                  <button class="btn btn-sm {{ !empty($reg->pending_reset) ? 'btn-warning text-dark border-warning' : 'btn-outline-secondary' }} dropdown-toggle py-1 px-2 font-weight-bold" type="button" id="moreActionMenu{{ $reg->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="font-size: 12px;" title="{{ !empty($reg->pending_reset) ? 'Ada permohonan reset password dari Admin!' : 'Menu Opsi' }}">
                    <span class="{{ !empty($reg->pending_reset) ? 'icon-lock' : 'icon-more_vert' }}"></span>
                    @if(!empty($reg->pending_reset))
                      <span class="badge badge-danger text-white ml-1" style="font-size: 9px; padding: 2px 4px;">!</span>
                    @endif
                  </button>
                  <div class="dropdown-menu dropdown-menu-right shadow-sm border-0" aria-labelledby="moreActionMenu{{ $reg->id }}" style="border-radius: 8px; min-width: 210px;">
                    <!-- 1. Reset Password Admin (Aktif jika ada request reset password, disabled jika tidak ada) -->
                    @if(!empty($reg->pending_reset))
                      <a class="dropdown-item small py-2 font-weight-bold text-dark" href="#" data-toggle="modal" data-target="#resetPasswordModal{{ $reg->id }}" style="background-color: #fffbeb;">
                        <span class="icon-lock mr-2 text-warning"></span> Reset Password
                        <span class="badge badge-warning text-dark ml-1 font-weight-bold" style="font-size: 10px;">Approval</span>
                      </a>
                    @else
                      <span class="dropdown-item small py-2 font-weight-bold text-muted disabled" style="cursor: not-allowed; opacity: 0.5;" title="Tidak ada permohonan reset password dari admin ini">
                        <span class="icon-lock mr-2 text-muted"></span> Reset Password
                      </span>
                    @endif
                    <div class="dropdown-divider my-1"></div>
                    <div class="dropdown-header py-1 text-muted small text-uppercase font-weight-bold">Ubah Status Cepat:</div>

                    <!-- 2. Set Status: Sudah Dihubungi -->
                    @if($regStatus !== 'contacted')
                    <form action="{{ route('superadmin.registrations.status', $reg->id) }}" method="POST">
                      @csrf
                      <input type="hidden" name="status" value="contacted">
                      <button type="submit" class="dropdown-item small py-1 text-info font-weight-bold">
                        <span class="icon-phone mr-2"></span> Tandai Sudah Dihubungi
                      </button>
                    </form>
                    @endif

                    <!-- 3. Set Status: Pending -->
                    @if($regStatus !== 'pending')
                    <form action="{{ route('superadmin.registrations.status', $reg->id) }}" method="POST">
                      @csrf
                      <input type="hidden" name="status" value="pending">
                      <button type="submit" class="dropdown-item small py-1 text-warning font-weight-bold">
                        <span class="icon-clock-o mr-2"></span> Kembalikan ke Pending
                      </button>
                    </form>
                    @endif

                    <!-- 4. Set Status: Ditolak -->
                    @if($regStatus !== 'rejected')
                    <form action="{{ route('superadmin.registrations.status', $reg->id) }}" method="POST">
                      @csrf
                      <input type="hidden" name="status" value="rejected">
                      <button type="submit" class="dropdown-item small py-1 text-danger font-weight-bold">
                        <span class="icon-close mr-2"></span> Tolak / Batal Sewa
                      </button>
                    </form>
                    @endif

                    <div class="dropdown-divider my-1"></div>

                    <!-- 5. Hapus Data -->
                    <form action="{{ route('superadmin.registrations.destroy', $reg->id) }}" method="POST" data-confirm="Hapus data calon penyewa \"{{ $reg->name }}\"? Data pendaftaran beserta riwayat prospeknya akan hilang permanen.">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="dropdown-item small py-1 text-danger">
                        <span class="icon-trash mr-2"></span> Hapus Data
                      </button>
                    </form>
                  </div>
                </div>
              </div>

              @if(!empty($reg->pending_reset))
              <!-- MODAL: APPROVAL RESET PASSWORD ADMIN -->
              <div class="modal fade text-left" id="resetPasswordModal{{ $reg->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                  <div class="modal-content shadow-lg border-0" style="border-radius: 12px; overflow: hidden;">
                    <div class="modal-header bg-warning py-3 px-4 text-dark d-flex justify-content-between align-items-center">
                      <h5 class="modal-title font-weight-bold mb-0" style="font-size: 16px;">
                        <span class="icon-lock mr-1"></span> Persetujuan Reset Password Admin
                      </h5>
                      <button type="button" class="close text-dark" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body p-4">
                      <div class="alert alert-warning border-0 p-3 mb-3 small" style="background-color: #fffbeb; color: #92400e; border-radius: 8px;">
                        <strong>Perhatian:</strong> Admin gym ini telah mengajukan permohonan reset password. Superadmin dapat menyetujui permohonan agar tautan pembuatan password baru dikirimkan ke email, atau menolak permohonan.
                      </div>

                      <table class="table table-sm table-borderless mb-3 small">
                        <tr>
                          <td class="text-muted font-weight-bold" style="width: 140px;">Penyewa / Gym:</td>
                          <td class="text-dark font-weight-bold">{{ $reg->name }}</td>
                        </tr>
                        <tr>
                          <td class="text-muted font-weight-bold">Nama Admin:</td>
                          <td class="text-dark">{{ $reg->pending_reset->admin_name }}</td>
                        </tr>
                        <tr>
                          <td class="text-muted font-weight-bold">Email Admin:</td>
                          <td class="text-dark font-weight-bold text-primary">{{ $reg->pending_reset->email }}</td>
                        </tr>
                        <tr>
                          <td class="text-muted font-weight-bold">Waktu Request:</td>
                          <td class="text-dark">{{ $reg->pending_reset->created_at ? $reg->pending_reset->created_at->format('d M Y, H:i') : '-' }}</td>
                        </tr>
                      </table>

                      <div class="collapse mt-2" id="rejectBox{{ $reg->id }}">
                        <form action="{{ route('superadmin.password-resets.reject', $reg->pending_reset->id) }}" method="POST" class="p-3 border rounded bg-light">
                          @csrf
                          <label class="font-weight-bold text-dark small mb-1">Alasan Penolakan (Opsional):</label>
                          <textarea name="notes" rows="2" class="form-control form-control-sm mb-2" placeholder="Tuliskan catatan alasan penolakan..."></textarea>
                          <button type="submit" class="btn btn-danger btn-sm font-weight-bold px-3">
                            <span class="icon-close mr-1"></span> Konfirmasi Tolak Permohonan
                          </button>
                        </form>
                      </div>
                    </div>
                    <div class="modal-footer bg-light py-2 px-4 d-flex justify-content-between align-items-center">
                      <div>
                        <button type="button" class="btn btn-outline-danger btn-sm font-weight-bold" data-toggle="collapse" data-target="#rejectBox{{ $reg->id }}">
                          <span class="icon-close mr-1"></span> Tolak Permohonan
                        </button>
                      </div>
                      <div class="d-flex" style="gap: 8px;">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
                        <form action="{{ route('superadmin.password-resets.approve', $reg->pending_reset->id) }}" method="POST" data-confirm="Setujui permohonan reset kata sandi {{ $reg->pending_reset->admin_name }}? Tautan verifikasi akan dikirimkan ke email {{ $reg->pending_reset->email }}.">
                          @csrf
                          <button type="submit" class="btn btn-success btn-sm font-weight-bold px-3">
                            <span class="icon-check mr-1"></span> Setujui &amp; Kirim Link
                          </button>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              @endif

              <!-- MODAL 2: BUAT AKUN & AKTIFKAN PENYEWA -->
              <div class="modal fade text-left" id="approveModal{{ $reg->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                  <form action="{{ route('superadmin.registrations.approve', $reg->id) }}" method="POST" class="modal-content shadow-lg border-0" style="border-radius: 14px; overflow: hidden;">
                    @csrf
                    <div class="modal-header bg-white border-bottom py-3 px-4 text-dark">
                      <div>
                        <h5 class="modal-title font-weight-bold text-dark mb-0">Buat Akun Penyewa Gym</h5>
                        <small class="text-muted">Setujui pendaftaran dan aktifkan akses admin tenant secara langsung</small>
                      </div>
                      <button type="button" class="close text-dark" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body p-4" style="max-height: 75vh; overflow-y: auto;">
                      <div class="alert alert-info border-0 p-3 mb-4 rounded d-flex align-items-start" style="font-size: 13px; background-color: #f0f9ff; color: #0369a1; border-radius: 8px;">
                        <span class="icon-info mr-2 mt-1 font-weight-bold" style="font-size: 16px;"></span>
                        <div>
                          Akun akan otomatis masuk ke <strong>Kelola Penyewa</strong> dengan masa aktif 30 hari. Akun <strong>Admin</strong> akan dibuat untuk email pendaftar. Akun Owner & Manager nantinya dibuat oleh Admin di dasbornya.
                        </div>
                      </div>

                      <div class="row">
                        <div class="col-md-6 form-group mb-3">
                          <label class="font-weight-bold text-dark small">Nama Pemohon / Calon Admin</label>
                          <input type="text" class="form-control" value="{{ $reg->name }}" readonly style="background-color: #f8fafc; font-weight: 600;">
                        </div>
                        <div class="col-md-6 form-group mb-3">
                          <label class="font-weight-bold text-dark small">Nomor WhatsApp</label>
                          <input type="text" class="form-control" value="{{ $reg->phone }}" readonly style="background-color: #f8fafc; font-weight: 600;">
                        </div>
                      </div>

                      <div class="row">
                        <div class="col-md-6 form-group mb-3">
                          <label class="font-weight-bold text-dark small">Email Login Admin Gym <span class="text-danger">*</span></label>
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
                          <small class="text-muted">Kata sandi default awal: <strong>1234</strong></small>
                        </div>
                      </div>

                      <!-- PILIHAN PAKET SEWA DINAMIS DARI DATABASE -->
                      <div class="form-group mb-4">
                        <label class="font-weight-bold text-dark small d-block mb-2">Pilih Paket Sewa Gym:</label>
                        <div class="row">
                          @forelse($plans as $p)
                            @php
                              $isCurrentPlan = ($reg->plan_id == $p->id || stripos($reg->plan_name, $p->name) !== false || ($loop->first && !$reg->plan_id));
                              $featPreview = is_array($p->features) ? array_slice($p->features, 0, 3) : [];
                            @endphp
                            <div class="col-md-4 mb-3">
                              <label class="plan-card-option d-block h-100 p-3 border rounded cursor-pointer position-relative {{ $isCurrentPlan ? 'border-primary bg-light-primary' : 'border-light-gray' }}" style="cursor: pointer; transition: all 0.2s;" for="plan_{{ $p->id }}_{{ $reg->id }}">
                                <div class="custom-control custom-radio mb-2">
                                  <input type="radio" id="plan_{{ $p->id }}_{{ $reg->id }}" name="plan_id" value="{{ $p->id }}" class="custom-control-input plan-radio" {{ $isCurrentPlan ? 'checked' : '' }}>
                                  <label class="custom-control-label font-weight-bold text-dark" for="plan_{{ $p->id }}_{{ $reg->id }}" style="font-size: 14px;">{{ $p->name }}</label>
                                </div>
                                <div class="text-primary font-weight-bold mb-2" style="font-size: 15px;">
                                  Rp {{ number_format($p->price, 0, ',', '.') }} <small class="text-muted font-weight-normal">/ bulan</small>
                                </div>
                                <ul class="list-unstyled text-dark mb-0 small" style="font-size: 11px; line-height: 1.6;">
                                  <li><i class="icon-check mr-1 text-primary"></i> {{ $p->max_members ? 'Maks ' . $p->max_members . ' Member' : 'Unlimited Member' }}</li>
                                  @foreach($featPreview as $f)
                                    <li><i class="icon-check mr-1 text-primary"></i> {{ $f }}</li>
                                  @endforeach
                                </ul>
                              </label>
                            </div>
                          @empty
                            <div class="col-12 text-muted small p-2">Belum ada paket sewa aktif.</div>
                          @endforelse
                        </div>
                      </div>

                      <div class="alert alert-light border py-2 px-3 mb-3 rounded" style="font-size: 12.5px;">
                        <i class="icon-info mr-1 text-primary"></i> <strong>Fitur & Modul:</strong> Fitur tenant otomatis disesuaikan secara penuh dengan paket sewa yang dipilih (fitur bawaan paket) tanpa perlu pengaturan manual.
                      </div>

                      <div class="form-group mb-0">
                        <label class="font-weight-bold text-dark small">Catatan Tambahan Superadmin (Opsional)</label>
                        <textarea name="notes" rows="2" class="form-control" placeholder="Catatan konfirmasi, pembayaran, dsb...">{{ $reg->notes }}</textarea>
                      </div>
                    </div>

                    <div class="modal-footer bg-light py-3 px-4">
                      <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                      <button type="submit" class="btn btn-primary btn-sm font-weight-bold px-4">
                        <span class="icon-check mr-1"></span> Buat Akun Admin & Aktifkan Penyewa
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="7" class="text-center py-5 text-muted">
              <span class="icon-inbox h3 d-block mb-2 text-muted"></span>
              Belum ada data calon penyewa yang mendaftar.
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
      <small class="text-muted">Menampilkan {{ $registrations->count() }} dari {{ $registrations->total() }} data pendaftaran</small>
      {{ $registrations->links('pagination::bootstrap-4') }}
    </div>
  </div>
</section>

<!-- MODAL: TAMBAH CALON PENYEWA BARU (MANUAL) -->
<div class="modal fade" id="createRegistrationModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <form action="{{ route('superadmin.registrations.store') }}" method="POST" class="modal-content shadow-lg border-0" style="border-radius: 12px; overflow: hidden;">
      @csrf
      <div class="modal-header bg-white border-bottom py-3 px-4 text-dark">
        <h5 class="modal-title font-weight-bold text-dark mb-0">Tambah Calon Penyewa Baru</h5>
        <button type="button" class="close text-dark" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body p-4">
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark small">Nama Calon Penyewa <span class="text-danger">*</span></label>
          <input type="text" name="name" class="form-control" placeholder="Contoh: Budi Santoso" required>
        </div>
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark small">Nomor WhatsApp <span class="text-danger">*</span></label>
          <input type="text" name="phone" class="form-control" placeholder="Contoh: 08123456789" required>
        </div>
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark small">Alamat Email <span class="text-danger">*</span></label>
          <input type="email" name="email" class="form-control" placeholder="Contoh: budi@fitness.com" required>
        </div>
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark small">Pilihan Paket Sewa</label>
          <select name="plan_id" class="form-control">
            <option value="">Pilih Paket...</option>
            @foreach($plans as $p)
              <option value="{{ $p->id }}">{{ $p->name }} (Rp {{ number_format($p->price, 0, ',', '.') }}/bln)</option>
            @endforeach
          </select>
        </div>
        <div class="form-group mb-0">
          <label class="font-weight-bold text-dark small">Catatan / Kebutuhan Awal</label>
          <textarea name="notes" rows="2" class="form-control" placeholder="Catatan mengenai gym, lokasi, atau kebutuhan..."></textarea>
        </div>
      </div>
      <div class="modal-footer bg-light py-2 px-4">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary btn-sm font-weight-bold px-3">Simpan Calon Penyewa</button>
      </div>
    </form>
  </div>
</div>

<style>
  .cursor-pointer { cursor: pointer; }
  .bg-light-primary { background-color: #f0fdf4 !important; border-color: #22c55e !important; }
  .border-light-gray { border-color: #e2e8f0 !important; }
  .plan-card-option:hover { border-color: #0ea5e9 !important; }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Interaktivitas border kartu paket ketika radio dipilih
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
