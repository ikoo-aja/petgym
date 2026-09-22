@extends('layouts.layout')

@php
  $featureMeta = [
    'classes' => [
      'title' => 'Master Kelas Rencana',
      'subtitle' => 'Pengaturan jadwal kelas kebugaran rutin dan instruktur pelatih',
    ],
    'promo' => [
      'title' => 'Promo dan Voucher',
      'subtitle' => 'Pengelolaan kode potongan harga untuk anggota dan kasir',
    ],
    'performance' => [
      'title' => 'Kinerja Karyawan',
      'subtitle' => 'Pemantauan pencapaian transaksi kasir dan aktivitas instruktur',
    ],
    'cash' => [
      'title' => 'Rekap Kas Keuangan',
      'subtitle' => 'Ringkasan penerimaan uang kas transaksi kasir harian',
    ],
    'vendor' => [
      'title' => 'Database Vendor Mitra',
      'subtitle' => 'Daftar kontak penyedia peralatan dan mitra operasional gym',
    ],
  ];

  $currentMeta = $featureMeta[$activeTab] ?? [
    'title' => 'Manajemen Strategis',
    'subtitle' => 'Pengelolaan operasional dan perencanaan manajerial gym',
  ];
@endphp

@section('title', $currentMeta['title'] . ' — PetGym')
@section('page_title', $currentMeta['title'])
@section('page_subtitle', $currentMeta['subtitle'])

@section('content')
<div class="container-fluid p-0">

  <!-- Tab Contents -->
  <div class="tab-content" id="managerTabContent">

    <!-- 1. TAB MASTER KELAS GYM -->
    <div class="tab-pane fade {{ $activeTab === 'classes' ? 'show active' : '' }}" id="classes-sec" role="tabpanel">
      <div class="card-custom p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <h5 class="font-weight-bold text-dark mb-1">Daftar Master Kelas</h5>
            <p class="text-muted small mb-0">Atur jadwal kelas kebugaran rutin dan instruktur pelatih yang bertugas.</p>
          </div>
          <button type="button" class="btn btn-sm btn-primary font-weight-bold" data-toggle="modal" data-target="#addMasterClassModal">
            + Tambah Master Kelas
          </button>
        </div>

        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="bg-light text-muted" style="font-size: 11.5px; text-transform: uppercase;">
              <tr>
                <th>Nama Kelas</th>
                <th>Hari Pelaksanaan</th>
                <th>Jam Mulai & Selesai</th>
                <th>Durasi Sesi</th>
                <th>Instruktur / Trainer</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($masterClasses as $mc)
                <tr>
                  <td><strong class="text-dark">{{ $mc->name }}</strong></td>
                  <td><span class="badge badge-info font-weight-bold">{{ $mc->day }}</span></td>
                  <td>{{ substr($mc->start_time, 0, 5) }} - {{ substr($mc->end_time, 0, 5) }}</td>
                  <td>{{ $mc->duration_minutes }} Menit</td>
                  <td>
                    @if($mc->trainer)
                      <span class="badge badge-primary font-weight-bold">{{ $mc->trainer->name }}</span>
                    @else
                      <span class="text-muted small">Belum Ditugaskan</span>
                    @endif
                  </td>
                  <td>
                    <button type="button" class="btn btn-xs btn-outline-primary btn-edit-mc"
                      data-id="{{ $mc->id }}"
                      data-name="{{ $mc->name }}"
                      data-day="{{ $mc->day }}"
                      data-start_time="{{ substr($mc->start_time, 0, 5) }}"
                      data-duration_minutes="{{ $mc->duration_minutes }}">Ubah</button>
                    <form action="{{ route('manager.master-classes.destroy', $mc->id) }}" method="POST" class="d-inline" data-confirm="Hapus master kelas ini?">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-xs btn-outline-danger">Hapus</button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center py-4 text-muted">Belum ada perencanaan master kelas.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- 2. TAB PROMO & VOUCHER -->
    <div class="tab-pane fade {{ $activeTab === 'promo' ? 'show active' : '' }}" id="promo-sec" role="tabpanel">
      <div class="card-custom p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <h5 class="font-weight-bold text-dark mb-1">Daftar Voucher Promo</h5>
            <p class="text-muted small mb-0">Kelola voucher potongan harga untuk pendaftaran anggota dan transaksi kasir.</p>
          </div>
          <button type="button" class="btn btn-sm btn-primary font-weight-bold" data-toggle="modal" data-target="#addPromoModal">
            + Tambah Voucher Promo
          </button>
        </div>

        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="bg-light text-muted" style="font-size: 11.5px; text-transform: uppercase;">
              <tr>
                <th>Kode Voucher</th>
                <th>Nilai Diskon</th>
                <th>Minimal Belanja</th>
                <th>Batas Penggunaan</th>
                <th>Masa Berlaku</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($promoCodes as $promo)
                <tr>
                  <td>
                    <span class="badge badge-secondary px-2 py-1 font-weight-bold" style="letter-spacing: 1px; font-size: 13px;">{{ $promo->code }}</span>
                    @if($promo->description)
                      <small class="d-block text-muted">{{ $promo->description }}</small>
                    @endif
                  </td>
                  <td class="font-weight-bold text-success">
                    @if($promo->discount_type === 'percentage')
                      {{ $promo->discount_value }}%
                    @else
                      Rp {{ number_format($promo->discount_value, 0, ',', '.') }}
                    @endif
                  </td>
                  <td>Rp {{ number_format($promo->min_purchase, 0, ',', '.') }}</td>
                  <td>{{ $promo->used_count }} / {{ $promo->max_uses }} kali</td>
                  <td><small>{{ $promo->valid_from->format('d M Y') }} s/d {{ $promo->valid_until->format('d M Y') }}</small></td>
                  <td>
                    @if($promo->is_active && $promo->valid_until->isFuture() && $promo->used_count < $promo->max_uses)
                      <span class="badge badge-success">Aktif</span>
                    @else
                      <span class="badge badge-secondary">Kedaluwarsa / Nonaktif</span>
                    @endif
                  </td>
                  <td>
                    <button type="button" class="btn btn-xs btn-outline-primary btn-edit-promo"
                      data-id="{{ $promo->id }}"
                      data-code="{{ $promo->code }}"
                      data-description="{{ $promo->description }}"
                      data-discount_type="{{ $promo->discount_type }}"
                      data-discount_value="{{ $promo->discount_value }}"
                      data-min_purchase="{{ $promo->min_purchase }}"
                      data-max_uses="{{ $promo->max_uses }}"
                      data-valid_from="{{ $promo->valid_from->format('Y-m-d') }}"
                      data-valid_until="{{ $promo->valid_until->format('Y-m-d') }}"
                      data-is_active="{{ $promo->is_active ? '1' : '0' }}">Ubah</button>
                    <form action="{{ route('manager.promo.destroy', $promo->id) }}" method="POST" class="d-inline" data-confirm="Hapus voucher promo ini?">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-xs btn-outline-danger">Hapus</button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="text-center py-4 text-muted">Belum ada voucher promo yang dibuat.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- 3. TAB KINERJA KARYAWAN -->
    <div class="tab-pane fade {{ $activeTab === 'performance' ? 'show active' : '' }}" id="performance-sec" role="tabpanel">
      <div class="row">
        <!-- Evaluasi Kasir Resepsionis -->
        <div class="col-lg-6 mb-4">
          <div class="card-custom p-4 h-100">
            <h5 class="font-weight-bold text-dark mb-1"><i class="icon-shopping-cart text-primary mr-1"></i> Penjualan Kasir Bulan Ini</h5>
            <p class="text-muted small mb-3">Total omset dan volume transaksi penjualan yang diproses staf kasir.</p>

            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
                  <tr>
                    <th>Staf Kasir</th>
                    <th>Jumlah Transaksi</th>
                    <th>Total Omset</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($receptionistPerformance as $rp)
                    <tr>
                      <td><strong class="text-dark">{{ $rp->user->name ?? 'Kasir #' . $rp->user_id }}</strong></td>
                      <td><span class="badge badge-info font-weight-bold">{{ $rp->total_transactions }} Transaksi</span></td>
                      <td><span class="font-weight-bold text-success">Rp {{ number_format($rp->total_sales, 0, ',', '.') }}</span></td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="3" class="text-center py-4 text-muted">Belum ada data transaksi kasir bulan ini.</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Evaluasi Trainer / Instruktur -->
        <div class="col-lg-6 mb-4">
          <div class="card-custom p-4 h-100">
            <h5 class="font-weight-bold text-dark mb-1"><i class="icon-people text-success mr-1"></i> Keaktifan Instruktur Kelas</h5>
            <p class="text-muted small mb-3">Jumlah kelas kebugaran yang dipimpin oleh instruktur pelatih.</p>

            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
                  <tr>
                    <th>Nama Instruktur</th>
                    <th>Spesialisasi</th>
                    <th>Kelas Dipimpin</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($trainerPerformance as $tp)
                    <tr>
                      <td><strong class="text-dark">{{ $tp->trainer->name ?? 'Trainer #' . $tp->trainer_id }}</strong></td>
                      <td>{{ $tp->trainer->specialization ?? 'Pelatih Kebugaran' }}</td>
                      <td><span class="badge badge-success font-weight-bold">{{ $tp->total_classes }} Kelas Aktif</span></td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="3" class="text-center py-4 text-muted">Belum ada alokasi kelas untuk instruktur.</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- 4. TAB REKAP KAS KEUANGAN -->
    <div class="tab-pane fade {{ $activeTab === 'cash' ? 'show active' : '' }}" id="cash-sec" role="tabpanel">
      <div class="card-custom p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <h5 class="font-weight-bold text-dark mb-1">Rekap Transaksi Kas Harian</h5>
            <p class="text-muted small mb-0">Rincian transaksi kasir hari ini berdasarkan metode pembayaran.</p>
          </div>
          <span class="badge badge-info px-3 py-2 font-weight-bold">
            Total Transaksi: {{ $dailyCashRecap->count() }}
          </span>
        </div>

        @php
          $totalToday = $dailyCashRecap->sum('total_amount');
          $cashTotal = $dailyCashRecap->where('payment_method', 'cash')->sum('total_amount');
          $qrisTotal = $dailyCashRecap->where('payment_method', 'qris')->sum('total_amount');
          $transferTotal = $dailyCashRecap->where('payment_method', 'transfer')->sum('total_amount');
          $cardTotal = $dailyCashRecap->where('payment_method', 'debit_card')->sum('total_amount');
        @endphp

        <div class="row mb-4">
          <div class="col-md-3 mb-2 mb-md-0">
            <div class="p-3 bg-light rounded border text-center">
              <small class="text-muted font-weight-bold text-uppercase" style="font-size: 11px;">Total Omset Hari Ini</small>
              <h4 class="font-weight-bold text-primary mt-1 mb-0">Rp {{ number_format($totalToday, 0, ',', '.') }}</h4>
            </div>
          </div>
          <div class="col-md-3 mb-2 mb-md-0">
            <div class="p-3 bg-light rounded border text-center">
              <small class="text-muted font-weight-bold text-uppercase" style="font-size: 11px;">Uang Tunai</small>
              <h4 class="font-weight-bold text-success mt-1 mb-0">Rp {{ number_format($cashTotal, 0, ',', '.') }}</h4>
            </div>
          </div>
          <div class="col-md-3 mb-2 mb-md-0">
            <div class="p-3 bg-light rounded border text-center">
              <small class="text-muted font-weight-bold text-uppercase" style="font-size: 11px;">Pembayaran Digital</small>
              <h4 class="font-weight-bold text-info mt-1 mb-0">Rp {{ number_format($qrisTotal, 0, ',', '.') }}</h4>
            </div>
          </div>
          <div class="col-md-3">
            <div class="p-3 bg-light rounded border text-center">
              <small class="text-muted font-weight-bold text-uppercase" style="font-size: 11px;">Transfer dan Debit</small>
              <h4 class="font-weight-bold text-dark mt-1 mb-0">Rp {{ number_format($transferTotal + $cardTotal, 0, ',', '.') }}</h4>
            </div>
          </div>
        </div>

        <h6 class="font-weight-bold text-dark mb-3 border-top pt-3">Rincian Transaksi Kasir Hari Ini</h6>
        <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
          <table class="table table-hover align-middle mb-0">
            <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
              <tr>
                <th>Invoice</th>
                <th>Waktu</th>
                <th>Kasir</th>
                <th>Pelanggan</th>
                <th>Metode Bayar</th>
                <th>Nominal</th>
              </tr>
            </thead>
            <tbody>
              @forelse($dailyCashRecap as $tx)
                <tr>
                  <td class="font-weight-bold text-dark">{{ $tx->invoice_number }}</td>
                  <td>{{ $tx->created_at->format('H:i:s') }}</td>
                  <td>{{ $tx->user->name ?? 'Kasir Staf' }}</td>
                  <td>{{ $tx->member->name ?? 'Pelanggan Umum' }}</td>
                  <td><span class="badge badge-secondary font-weight-bold" style="text-transform: uppercase;">{{ $tx->payment_method }}</span></td>
                  <td class="font-weight-bold text-success">Rp {{ number_format($tx->total_amount, 0, ',', '.') }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center py-3 text-muted">Belum ada transaksi kasir hari ini.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- 5. TAB DATABASE VENDOR -->
    <div class="tab-pane fade {{ $activeTab === 'vendor' ? 'show active' : '' }}" id="vendor-sec" role="tabpanel">
      <div class="card-custom p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <h5 class="font-weight-bold text-dark mb-1">Daftar Rekanan Vendor</h5>
            <p class="text-muted small mb-0">Informasi kontak penyedia alat kebugaran, suplemen, kebersihan, dan teknisi servis.</p>
          </div>
          <button type="button" class="btn btn-sm btn-primary font-weight-bold" data-toggle="modal" data-target="#addVendorModal">
            + Tambah Kontak Vendor
          </button>
        </div>

        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="bg-light text-muted" style="font-size: 11.5px; text-transform: uppercase;">
              <tr>
                <th>Nama Vendor / Perusahaan</th>
                <th>Kategori Layanan</th>
                <th>Nomor Telepon / WA</th>
                <th>Email</th>
                <th>Alamat</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($vendors as $v)
                <tr>
                  <td>
                    <strong class="text-dark">{{ $v->name }}</strong>
                    @if($v->notes)
                      <small class="d-block text-muted">{{ $v->notes }}</small>
                    @endif
                  </td>
                  <td><span class="badge badge-secondary">{{ $v->category }}</span></td>
                  <td class="font-weight-bold">{{ $v->phone ?? '-' }}</td>
                  <td>{{ $v->email ?? '-' }}</td>
                  <td style="max-width: 200px;" class="small text-muted">{{ $v->address ?? '-' }}</td>
                  <td>
                    <button type="button" class="btn btn-xs btn-outline-primary btn-edit-vendor"
                      data-id="{{ $v->id }}"
                      data-name="{{ $v->name }}"
                      data-category="{{ $v->category }}"
                      data-phone="{{ $v->phone }}"
                      data-email="{{ $v->email }}"
                      data-address="{{ $v->address }}"
                      data-notes="{{ $v->notes }}">Ubah</button>
                    <form action="{{ route('manager.vendors.destroy', $v->id) }}" method="POST" class="d-inline" data-confirm="Hapus vendor ini?">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-xs btn-outline-danger">Hapus</button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center py-4 text-muted">Belum ada buku kontak vendor mitra.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>

</div>

<!-- ========================================== -->
<!-- MODALS -->
<!-- ========================================== -->

<!-- Modal Tambah Master Class -->
<div class="modal fade" id="addMasterClassModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <form action="{{ route('manager.master-classes.store') }}" method="POST" class="modal-content shadow border-0">
      @csrf
      <div class="modal-header bg-light">
        <h5 class="modal-title font-weight-bold text-dark mb-0">Rencanakan Master Kelas Baru</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body p-4">
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark small mb-1">Nama Kelas Kebugaran *</label>
          <input type="text" name="name" class="form-control" placeholder="Contoh: Yoga Morning, Zumba Aerobic" required>
        </div>
        <div class="row">
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Hari Pelaksanaan *</label>
            <select name="day" class="form-control" required>
              <option value="Senin">Senin</option>
              <option value="Selasa">Selasa</option>
              <option value="Rabu">Rabu</option>
              <option value="Kamis">Kamis</option>
              <option value="Jumat">Jumat</option>
              <option value="Sabtu">Sabtu</option>
              <option value="Minggu">Minggu</option>
            </select>
          </div>
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Durasi (Menit) *</label>
            <input type="number" name="duration_minutes" class="form-control" value="60" min="15" required>
          </div>
        </div>
        <div class="form-group mb-0">
          <label class="font-weight-bold text-dark small mb-1">Jam Mulai *</label>
          <input type="time" name="start_time" class="form-control" value="08:00" required>
        </div>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary font-weight-bold">Simpan Rencana Kelas</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Ubah Master Class -->
<div class="modal fade" id="editMasterClassModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <form id="editMasterClassForm" method="POST" class="modal-content shadow border-0">
      @csrf
      @method('PUT')
      <div class="modal-header bg-light">
        <h5 class="modal-title font-weight-bold text-dark mb-0">Ubah Rencana Master Kelas</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body p-4">
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark small mb-1">Nama Kelas Kebugaran *</label>
          <input type="text" name="name" id="editMcName" class="form-control" required>
        </div>
        <div class="row">
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Hari Pelaksanaan *</label>
            <select name="day" id="editMcDay" class="form-control" required>
              <option value="Senin">Senin</option>
              <option value="Selasa">Selasa</option>
              <option value="Rabu">Rabu</option>
              <option value="Kamis">Kamis</option>
              <option value="Jumat">Jumat</option>
              <option value="Sabtu">Sabtu</option>
              <option value="Minggu">Minggu</option>
            </select>
          </div>
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Durasi (Menit) *</label>
            <input type="number" name="duration_minutes" id="editMcDuration" class="form-control" required>
          </div>
        </div>
        <div class="form-group mb-0">
          <label class="font-weight-bold text-dark small mb-1">Jam Mulai *</label>
          <input type="time" name="start_time" id="editMcStart" class="form-control" required>
        </div>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary font-weight-bold">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Tambah Promo -->
<div class="modal fade" id="addPromoModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <form action="{{ route('manager.promo.store') }}" method="POST" class="modal-content shadow border-0">
      @csrf
      <div class="modal-header bg-light">
        <h5 class="modal-title font-weight-bold text-dark mb-0">Buat Voucher Promo Baru</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body p-4">
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark small mb-1">Kode Voucher Promo (Huruf Kapital & Angka) *</label>
          <input type="text" name="code" class="form-control text-uppercase font-weight-bold" placeholder="Contoh: MERDEKA50, PROMOFIT" required>
        </div>
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark small mb-1">Keterangan / Judul Promo</label>
          <input type="text" name="description" class="form-control" placeholder="Contoh: Diskon pendaftaran member awal bulan">
        </div>
        <div class="row">
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Tipe Diskon *</label>
            <select name="discount_type" class="form-control" required>
              <option value="percentage">Persentase (%)</option>
              <option value="fixed">Nominal Tetap (Rp)</option>
            </select>
          </div>
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Nilai Diskon *</label>
            <input type="number" name="discount_value" class="form-control" placeholder="Contoh: 15 atau 20000" required>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Minimal Belanja (Rp) *</label>
            <input type="number" name="min_purchase" class="form-control" value="0" required>
          </div>
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Batas Maksimal Klaim *</label>
            <input type="number" name="max_uses" class="form-control" value="100" min="1" required>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 form-group mb-0">
            <label class="font-weight-bold text-dark small mb-1">Mulai Berlaku *</label>
            <input type="date" name="valid_from" class="form-control" value="{{ date('Y-m-d') }}" required>
          </div>
          <div class="col-md-6 form-group mb-0">
            <label class="font-weight-bold text-dark small mb-1">Berakhir Pada *</label>
            <input type="date" name="valid_until" class="form-control" value="{{ date('Y-m-d', strtotime('+30 days')) }}" required>
          </div>
        </div>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary font-weight-bold">Simpan Voucher</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Ubah Promo -->
<div class="modal fade" id="editPromoModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <form id="editPromoForm" method="POST" class="modal-content shadow border-0">
      @csrf
      @method('PUT')
      <div class="modal-header bg-light">
        <h5 class="modal-title font-weight-bold text-dark mb-0">Ubah Voucher Promo</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body p-4">
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark small mb-1">Kode Voucher Promo *</label>
          <input type="text" name="code" id="editPromoCode" class="form-control text-uppercase font-weight-bold" required>
        </div>
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark small mb-1">Keterangan / Judul Promo</label>
          <input type="text" name="description" id="editPromoDescription" class="form-control">
        </div>
        <div class="row">
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Tipe Diskon *</label>
            <select name="discount_type" id="editPromoType" class="form-control" required>
              <option value="percentage">Persentase (%)</option>
              <option value="fixed">Nominal Tetap (Rp)</option>
            </select>
          </div>
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Nilai Diskon *</label>
            <input type="number" name="discount_value" id="editPromoValue" class="form-control" required>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Minimal Belanja (Rp) *</label>
            <input type="number" name="min_purchase" id="editPromoMin" class="form-control" required>
          </div>
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Batas Maksimal Klaim *</label>
            <input type="number" name="max_uses" id="editPromoMax" class="form-control" required>
          </div>
        </div>
        <div class="row mb-3">
          <div class="col-md-6 form-group mb-0">
            <label class="font-weight-bold text-dark small mb-1">Mulai Berlaku *</label>
            <input type="date" name="valid_from" id="editPromoFrom" class="form-control" required>
          </div>
          <div class="col-md-6 form-group mb-0">
            <label class="font-weight-bold text-dark small mb-1">Berakhir Pada *</label>
            <input type="date" name="valid_until" id="editPromoUntil" class="form-control" required>
          </div>
        </div>
        <div class="custom-control custom-checkbox">
          <input type="checkbox" name="is_active" value="1" class="custom-control-input" id="editPromoActive">
          <label class="custom-control-label font-weight-bold text-dark" for="editPromoActive">Voucher Aktif & Dapat Digunakan</label>
        </div>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary font-weight-bold">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Tambah Vendor -->
<div class="modal fade" id="addVendorModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <form action="{{ route('manager.vendors.store') }}" method="POST" class="modal-content shadow border-0">
      @csrf
      <div class="modal-header bg-light">
        <h5 class="modal-title font-weight-bold text-dark mb-0">Tambah Buku Kontak Vendor</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body p-4">
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark small mb-1">Nama Vendor / Perusahaan *</label>
          <input type="text" name="name" class="form-control" placeholder="Contoh: PT Fitness Jaya Abadi" required>
        </div>
        <div class="row">
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Kategori Layanan *</label>
            <input type="text" name="category" class="form-control" placeholder="Contoh: Sparepart, Suplemen, Laundry" required>
          </div>
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Nomor Telepon / WhatsApp</label>
            <input type="text" name="phone" class="form-control" placeholder="08123456789">
          </div>
        </div>
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark small mb-1">Email Vendor</label>
          <input type="email" name="email" class="form-control" placeholder="kontak@vendor.com">
        </div>
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark small mb-1">Alamat Kantor / Toko</label>
          <textarea name="address" class="form-control" rows="2" placeholder="Alamat lengkap supplier..."></textarea>
        </div>
        <div class="form-group mb-0">
          <label class="font-weight-bold text-dark small mb-1">Catatan Tambahan</label>
          <input type="text" name="notes" class="form-control" placeholder="Contoh: PIC Pak Budi, Garansi 1 tahun">
        </div>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary font-weight-bold">Simpan Vendor</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Ubah Vendor -->
<div class="modal fade" id="editVendorModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <form id="editVendorForm" method="POST" class="modal-content shadow border-0">
      @csrf
      @method('PUT')
      <div class="modal-header bg-light">
        <h5 class="modal-title font-weight-bold text-dark mb-0">Ubah Kontak Vendor</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body p-4">
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark small mb-1">Nama Vendor / Perusahaan *</label>
          <input type="text" name="name" id="editVendorName" class="form-control" required>
        </div>
        <div class="row">
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Kategori Layanan *</label>
            <input type="text" name="category" id="editVendorCategory" class="form-control" required>
          </div>
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Nomor Telepon / WhatsApp</label>
            <input type="text" name="phone" id="editVendorPhone" class="form-control">
          </div>
        </div>
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark small mb-1">Email Vendor</label>
          <input type="email" name="email" id="editVendorEmail" class="form-control">
        </div>
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark small mb-1">Alamat Kantor / Toko</label>
          <textarea name="address" id="editVendorAddress" class="form-control" rows="2"></textarea>
        </div>
        <div class="form-group mb-0">
          <label class="font-weight-bold text-dark small mb-1">Catatan Tambahan</label>
          <input type="text" name="notes" id="editVendorNotes" class="form-control">
        </div>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary font-weight-bold">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

@endsection

@push('scripts')
<script>
  $(document).ready(function() {
    // 1. Edit Master Class Modal
    $('.btn-edit-mc').on('click', function() {
      const id = $(this).data('id');
      $('#editMasterClassForm').attr('action', '/manager/master-classes/' + id);
      $('#editMcName').val($(this).data('name'));
      $('#editMcDay').val($(this).data('day'));
      $('#editMcStart').val($(this).data('start_time'));
      $('#editMcDuration').val($(this).data('duration_minutes'));
      $('#editMasterClassModal').modal('show');
    });

    // 2. Edit Promo Modal
    $('.btn-edit-promo').on('click', function() {
      const id = $(this).data('id');
      $('#editPromoForm').attr('action', '/manager/promo/' + id);
      $('#editPromoCode').val($(this).data('code'));
      $('#editPromoDescription').val($(this).data('description'));
      $('#editPromoType').val($(this).data('discount_type'));
      $('#editPromoValue').val($(this).data('discount_value'));
      $('#editPromoMin').val($(this).data('min_purchase'));
      $('#editPromoMax').val($(this).data('max_uses'));
      $('#editPromoFrom').val($(this).data('valid_from'));
      $('#editPromoUntil').val($(this).data('valid_until'));
      $('#editPromoActive').prop('checked', $(this).data('is_active') == 1);
      $('#editPromoModal').modal('show');
    });

    // 3. Edit Vendor Modal
    $('.btn-edit-vendor').on('click', function() {
      const id = $(this).data('id');
      $('#editVendorForm').attr('action', '/manager/vendors/' + id);
      $('#editVendorName').val($(this).data('name'));
      $('#editVendorCategory').val($(this).data('category'));
      $('#editVendorPhone').val($(this).data('phone'));
      $('#editVendorEmail').val($(this).data('email'));
      $('#editVendorAddress').val($(this).data('address'));
      $('#editVendorNotes').val($(this).data('notes'));
      $('#editVendorModal').modal('show');
    });
  });
</script>
@endpush
