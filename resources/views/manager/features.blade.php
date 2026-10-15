@extends('layouts.admin')

@php
  $featureMeta = [
    'promo' => [
      'title' => 'Promo & Voucher',
      'subtitle' => 'Pengelolaan voucher dan potongan harga pendaftaran',
    ],
    'performance' => [
      'title' => 'Aktivitas Karyawan',
      'subtitle' => 'Pemantauan aktivitas transaksi kasir dan jadwal kelas',
    ],
    'cash' => [
      'title' => 'Laporan Keuangan',
      'subtitle' => 'Rekapitulasi penerimaan kas harian, mingguan, dan bulanan',
    ],
    'vendor' => [
      'title' => 'Vendor & Mitra',
      'subtitle' => 'Daftar rekanan penyedia perlengkapan dan operasional gym',
    ],
    'locker' => [
      'title' => 'Pengaturan Loker',
      'subtitle' => 'Pengelolaan kapasitas dan pemantauan kondisi loker gym',
    ],
  ];
  $currentMeta = $featureMeta[$activeTab] ?? $featureMeta['promo'];
@endphp

@section('title', $currentMeta['title'] . ' — PetGym')
@section('page_title', $currentMeta['title'])
@section('page_subtitle', $currentMeta['subtitle'])

@section('content')
<div class="container-fluid p-0">

  <!-- ============================================================== -->
  <!-- 1. TAB PROMO & VOUCHER -->
  <!-- ============================================================== -->
  @if($activeTab === 'promo')
  <div class="card-custom p-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3" style="gap: 12px;">
      <div>
        <h5 class="font-weight-bold text-dark mb-1">Daftar Voucher Promo</h5>
        <p class="text-muted small mb-0">Kelola voucher potongan harga untuk pendaftaran member dan transaksi kasir.</p>
      </div>
      <button type="button" class="btn btn-sm btn-primary font-weight-bold px-3 py-2" data-toggle="modal" data-target="#addPromoModal" style="border-radius: 8px;">
        <i class="icon-plus mr-1"></i> Tambah Voucher Promo
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
            <th class="text-right">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($promoCodes as $promo)
            <tr>
              <td>
                <span class="badge badge-secondary px-2 py-1 font-weight-bold text-dark" style="letter-spacing: 0.8px; font-size: 12.5px;">{{ $promo->code }}</span>
                @if($promo->description)
                  <small class="d-block text-muted mt-1">{{ $promo->description }}</small>
                @endif
              </td>
              <td class="font-weight-bold text-success">
                @if($promo->discount_type === 'percentage')
                  {{ (float)$promo->discount_value }}%
                @else
                  Rp {{ number_format($promo->discount_value, 0, ',', '.') }}
                @endif
              </td>
              <td class="text-dark">Rp {{ number_format($promo->min_purchase, 0, ',', '.') }}</td>
              <td class="text-dark">{{ $promo->used_count }} / {{ $promo->max_uses }} kali</td>
              <td><small class="text-muted">{{ $promo->valid_from->format('d M Y') }} s/d {{ $promo->valid_until->format('d M Y') }}</small></td>
              <td>
                @if($promo->is_active && $promo->valid_until->isFuture() && $promo->used_count < $promo->max_uses)
                  <span class="badge badge-success px-2 py-1 font-weight-bold">Aktif</span>
                @else
                  <span class="badge badge-secondary px-2 py-1 font-weight-bold">Kedaluwarsa / Nonaktif</span>
                @endif
              </td>
              <td class="text-right">
                <button type="button" class="btn btn-sm btn-outline-primary btn-edit-promo mr-1 font-weight-bold px-2 py-1"
                  data-id="{{ $promo->id }}"
                  data-code="{{ $promo->code }}"
                  data-description="{{ $promo->description }}"
                  data-discount_type="{{ $promo->discount_type }}"
                  data-discount_value="{{ (float)$promo->discount_value }}"
                  data-min_purchase="{{ (float)$promo->min_purchase }}"
                  data-max_uses="{{ $promo->max_uses }}"
                  data-valid_from="{{ $promo->valid_from->format('Y-m-d') }}"
                  data-valid_until="{{ $promo->valid_until->format('Y-m-d') }}"
                  data-is_active="{{ $promo->is_active ? '1' : '0' }}"
                  style="border-radius: 6px; font-size: 12px;">Ubah</button>
                <form action="{{ route('manager.promo.destroy', $promo->id) }}" method="POST" class="d-inline" data-confirm="Hapus voucher \"{{ $promo->code }}\" secara permanen? Voucher tidak dapat digunakan lagi untuk transaksi.">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger font-weight-bold px-2 py-1" style="border-radius: 6px; font-size: 12px;">Hapus</button>
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
  @endif

  <!-- ============================================================== -->
  <!-- 2. TAB KINERJA KARYAWAN -->
  <!-- ============================================================== -->
  @if($activeTab === 'performance')
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
                  <td><span class="badge badge-info font-weight-bold px-2 py-1">{{ $rp->total_transactions }} Transaksi</span></td>
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

    <!-- Evaluasi Trainer -->
    <div class="col-lg-6 mb-4">
      <div class="card-custom p-4 h-100">
        <h5 class="font-weight-bold text-dark mb-1"><i class="icon-people text-success mr-1"></i> Keaktifan Trainer Kelas</h5>
        <p class="text-muted small mb-3">Jumlah kelas kebugaran yang dipimpin oleh instruktur pelatih.</p>

        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
              <tr>
                <th>Nama Trainer</th>
                <th>Spesialisasi</th>
                <th>Kelas Dipimpin</th>
              </tr>
            </thead>
            <tbody>
              @forelse($trainerPerformance as $tp)
                <tr>
                  <td><strong class="text-dark">{{ $tp->trainer->name ?? 'Trainer #' . $tp->trainer_id }}</strong></td>
                  <td>{{ $tp->trainer->specialization ?? 'Trainer Kebugaran' }}</td>
                  <td><span class="badge badge-success font-weight-bold px-2 py-1">{{ $tp->total_classes }} Kelas Aktif</span></td>
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
  @endif

  <!-- ============================================================== -->
  <!-- ============================================================== -->
  <!-- 3. TAB REKAP KAS KEUANGAN -->
  <!-- ============================================================== -->
  @if($activeTab === 'cash')
  @php
    $periodLabels = [
      'daily' => 'Harian (Hari Ini)',
      'weekly' => 'Mingguan (Minggu Ini)',
      'monthly' => 'Bulanan (Bulan Ini)'
    ];
    $currentPeriodLabel = $periodLabels[$cashPeriod ?? 'daily'] ?? 'Harian';
  @endphp
  <div class="card-custom p-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3" style="gap: 12px;">
      <div>
        <h5 class="font-weight-bold text-dark mb-1">Rekap Transaksi Kas Keuangan</h5>
        <p class="text-muted small mb-0">Rincian pendapatan kasir berdasarkan metode pembayaran untuk periode: <strong class="text-primary">{{ $currentPeriodLabel }}</strong>.</p>
      </div>
      <div class="d-flex flex-wrap align-items-center" style="gap: 8px;">
        <div class="btn-group shadow-sm" role="group" aria-label="Filter Periode Keuangan">
          <a href="{{ url('/manager/features?tab=cash&period=daily') }}" 
             class="btn btn-sm {{ ($cashPeriod ?? 'daily') === 'daily' ? 'btn-primary font-weight-bold' : 'btn-outline-secondary' }}" 
             style="border-radius: 8px 0 0 8px; font-size: 12px; padding: 6px 14px;">
            <i class="icon-calendar mr-1"></i> Harian
          </a>
          <a href="{{ url('/manager/features?tab=cash&period=weekly') }}" 
             class="btn btn-sm {{ ($cashPeriod ?? 'daily') === 'weekly' ? 'btn-primary font-weight-bold' : 'btn-outline-secondary' }}" 
             style="font-size: 12px; padding: 6px 14px;">
            <i class="icon-calendar mr-1"></i> Mingguan
          </a>
          <a href="{{ url('/manager/features?tab=cash&period=monthly') }}" 
             class="btn btn-sm {{ ($cashPeriod ?? 'daily') === 'monthly' ? 'btn-primary font-weight-bold' : 'btn-outline-secondary' }}" 
             style="border-radius: 0 8px 8px 0; font-size: 12px; padding: 6px 14px;">
            <i class="icon-calendar mr-1"></i> Bulanan
          </a>
        </div>
        <span class="badge badge-info px-3 py-2 font-weight-bold" style="border-radius: 8px; font-size: 12px;">
          Total Transaksi: {{ $dailyCashRecap->count() }}
        </span>
      </div>
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
          <small class="text-muted font-weight-bold text-uppercase" style="font-size: 11px;">Total Omset ({{ $currentPeriodLabel }})</small>
          <h4 class="font-weight-bold text-primary mt-1 mb-0">Rp {{ number_format($totalToday, 0, ',', '.') }}</h4>
        </div>
      </div>
      <div class="col-md-3 mb-2 mb-md-0">
        <div class="p-3 bg-light rounded border text-center">
          <small class="text-muted font-weight-bold text-uppercase" style="font-size: 11px;">Uang Tunai (Cash)</small>
          <h4 class="font-weight-bold text-success mt-1 mb-0">Rp {{ number_format($cashTotal, 0, ',', '.') }}</h4>
        </div>
      </div>
      <div class="col-md-3 mb-2 mb-md-0">
        <div class="p-3 bg-light rounded border text-center">
          <small class="text-muted font-weight-bold text-uppercase" style="font-size: 11px;">Pembayaran QRIS</small>
          <h4 class="font-weight-bold text-info mt-1 mb-0">Rp {{ number_format($qrisTotal, 0, ',', '.') }}</h4>
        </div>
      </div>
      <div class="col-md-3">
        <div class="p-3 bg-light rounded border text-center">
          <small class="text-muted font-weight-bold text-uppercase" style="font-size: 11px;">Transfer &amp; Kartu</small>
          <h4 class="font-weight-bold text-dark mt-1 mb-0">Rp {{ number_format($transferTotal + $cardTotal, 0, ',', '.') }}</h4>
        </div>
      </div>
    </div>

    <h6 class="font-weight-bold text-dark mb-3 border-top pt-3">Rincian Transaksi Kasir ({{ $currentPeriodLabel }})</h6>
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
          <tr>
            <th>Invoice</th>
            <th>Waktu</th>
            <th>Kasir</th>
            <th>Pelanggan</th>
            <th>Metode Bayar</th>
            <th class="text-right">Nominal</th>
          </tr>
        </thead>
        <tbody>
          @forelse($dailyCashRecap as $tx)
            <tr>
              <td class="font-weight-bold text-dark">{{ $tx->invoice_number }}</td>
              <td>{{ $tx->created_at->format('H:i:s') }} WIB</td>
              <td>{{ $tx->user->name ?? 'Kasir Staf' }}</td>
              <td>{{ $tx->member->name ?? 'Pelanggan Umum' }}</td>
              <td><span class="badge badge-secondary font-weight-bold px-2 py-1" style="text-transform: uppercase;">{{ $tx->payment_method }}</span></td>
              <td class="font-weight-bold text-success text-right">Rp {{ number_format($tx->total_amount, 0, ',', '.') }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center py-4 text-muted">Belum ada transaksi kasir yang tercatat untuk periode {{ strtolower($currentPeriodLabel) }}.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  @endif

  <!-- ============================================================== -->
  <!-- 4. TAB DATABASE VENDOR MITRA -->
  <!-- ============================================================== -->
  @if($activeTab === 'vendor')
  <div class="card-custom p-4 mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3" style="gap: 12px;">
      <div>
        <h5 class="font-weight-bold text-dark mb-1">Daftar Rekanan Vendor &amp; Pemasok</h5>
        <p class="text-muted small mb-0">Informasi kontak penyedia alat kebugaran, suplemen, kebersihan, dan teknisi servis.</p>
      </div>
      <button type="button" class="btn btn-sm btn-primary font-weight-bold px-3 py-2" data-toggle="modal" data-target="#addVendorModal" style="border-radius: 8px;">
        <i class="icon-plus mr-1"></i> Tambah Kontak Vendor
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
            <th class="text-right">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($vendors as $v)
            <tr>
              <td>
                <strong class="text-dark">{{ $v->name }}</strong>
                @if($v->notes)
                  <small class="d-block text-muted mt-1">{{ $v->notes }}</small>
                @endif
              </td>
              <td><span class="badge badge-secondary px-2 py-1 font-weight-semibold">{{ $v->category }}</span></td>
              <td class="font-weight-bold text-dark">{{ $v->phone ?? '-' }}</td>
              <td>{{ $v->email ?? '-' }}</td>
              <td style="max-width: 220px;" class="small text-muted">{{ $v->address ?? '-' }}</td>
              <td class="text-right">
                <button type="button" class="btn btn-sm btn-outline-primary btn-edit-vendor mr-1 font-weight-bold px-2 py-1"
                  data-id="{{ $v->id }}"
                  data-name="{{ $v->name }}"
                  data-category="{{ $v->category }}"
                  data-phone="{{ $v->phone }}"
                  data-email="{{ $v->email }}"
                  data-address="{{ $v->address }}"
                  data-notes="{{ $v->notes }}"
                  style="border-radius: 6px; font-size: 12px;">Ubah</button>
                <form action="{{ route('manager.vendors.destroy', $v->id) }}" method="POST" class="d-inline" data-confirm="Hapus kontak vendor \"{{ $v->name }}\" secara permanen?">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger font-weight-bold px-2 py-1" style="border-radius: 6px; font-size: 12px;">Hapus</button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center py-4 text-muted">Belum ada buku kontak vendor mitra terdaftar.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  @endif

  <!-- ============================================================== -->
  <!-- 5. TAB MASTER LOKER GYM -->
  <!-- ============================================================== -->
  @if($activeTab === 'locker')
  <div class="row mb-4">
    <div class="col-sm-6 col-md-3 mb-3 mb-md-0">
      <div class="card-custom p-3 border-left" style="border-left: 4px solid #4f46e5 !important;">
        <small class="text-muted font-weight-bold text-uppercase" style="font-size: 11px;">Total Unit Loker</small>
        <h3 class="font-weight-bold text-dark mt-1 mb-0">{{ $totalLockers }}</h3>
        <small class="text-muted">Kapasitas penyimpanan gym</small>
      </div>
    </div>
    <div class="col-sm-6 col-md-3 mb-3 mb-md-0">
      <div class="card-custom p-3 border-left" style="border-left: 4px solid #10b981 !important;">
        <small class="text-muted font-weight-bold text-uppercase" style="font-size: 11px;">Loker Tersedia</small>
        <h3 class="font-weight-bold text-success mt-1 mb-0">{{ $availableLockers }}</h3>
        <small class="text-muted">Siap dipinjamkan resepsionis</small>
      </div>
    </div>
    <div class="col-sm-6 col-md-3 mb-3 mb-md-0">
      <div class="card-custom p-3 border-left" style="border-left: 4px solid #3b82f6 !important;">
        <small class="text-muted font-weight-bold text-uppercase" style="font-size: 11px;">Sedang Digunakan</small>
        <h3 class="font-weight-bold text-primary mt-1 mb-0">{{ $occupiedLockers }}</h3>
        <small class="text-muted">Kunci sedang dipinjam member</small>
      </div>
    </div>
    <div class="col-sm-6 col-md-3">
      <div class="card-custom p-3 border-left" style="border-left: 4px solid #ef4444 !important;">
        <small class="text-muted font-weight-bold text-uppercase" style="font-size: 11px;">Rusak / Perlu Perbaikan</small>
        <h3 class="font-weight-bold text-danger mt-1 mb-0">{{ $brokenLockers }}</h3>
        <small class="text-muted">Ditangani oleh Supervisor</small>
      </div>
    </div>
  </div>

  <div class="card-custom p-4 mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3" style="gap: 12px;">
      <div>
        <h5 class="font-weight-bold text-dark mb-1">Master Loker Gym & Pengelolaan Kapasitas</h5>
        <p class="text-muted small mb-0">Tambah unit loker gym secara satuan atau langsung tentukan jumlah loker massal.</p>
      </div>
      @if(!Auth::user() || !Auth::user()->isOwner())
      <div class="d-flex flex-wrap gap-2">
        <button type="button" class="btn btn-sm btn-outline-primary font-weight-bold px-3 py-2 mr-2" data-toggle="modal" data-target="#addSingleLockerModal" style="border-radius: 8px;">
          <i class="icon-plus mr-1"></i> Tambah Loker Satuan
        </button>
        <button type="button" class="btn btn-sm btn-primary font-weight-bold px-3 py-2" data-toggle="modal" data-target="#addBulkLockerModal" style="border-radius: 8px;">
          <i class="icon-settings mr-1"></i> Buat Jumlah Loker (Massal)
        </button>
      </div>
      @endif
    </div>

    <!-- Visual Grid Peta Loker -->
    <div class="p-3 bg-light rounded mb-4 border">
      <div class="d-flex align-items-center justify-content-between flex-wrap mb-3">
        <span class="font-weight-bold text-dark small text-uppercase" style="letter-spacing: 0.5px;">Visualisasi Grid Loker</span>
        <div class="d-flex align-items-center flex-wrap gap-3" style="font-size: 12px;">
          <div class="d-flex align-items-center mr-3"><span style="width:12px;height:12px;border-radius:3px;background:#10b981;display:inline-block;margin-right:6px;"></span> Tersedia</div>
          <div class="d-flex align-items-center mr-3"><span style="width:12px;height:12px;border-radius:3px;background:#3b82f6;display:inline-block;margin-right:6px;"></span> Terpakai</div>
          <div class="d-flex align-items-center"><span style="width:12px;height:12px;border-radius:3px;background:#ef4444;display:inline-block;margin-right:6px;"></span> Rusak</div>
        </div>
      </div>
      <div class="d-flex flex-wrap" style="gap: 8px; max-height: 280px; overflow-y: auto;">
        @forelse($lockers as $l)
          @php
            $bg = '#10b981';
            if ($l->status === 'terpakai') $bg = '#3b82f6';
            elseif ($l->status === 'rusak') $bg = '#ef4444';
          @endphp
          <div class="text-center rounded p-2 text-white shadow-sm" style="width: 62px; height: 62px; background: {{ $bg }}; display: flex; flex-direction: column; justify-content: center; align-items: center;" title="Loker #{{ $l->locker_number }} - {{ ucfirst($l->status) }}{{ $l->notes ? ' (' . $l->notes . ')' : '' }}">
            <span class="font-weight-bold" style="font-size: 15px; line-height: 1.1;">{{ $l->locker_number }}</span>
            <small style="font-size: 9px; opacity: 0.9;">{{ ucfirst($l->status) }}</small>
          </div>
        @empty
          <div class="text-center py-4 text-muted w-100">Belum ada unit loker terdaftar. Gunakan tombol di atas untuk membuat jumlah loker gym.</div>
        @endforelse
      </div>
    </div>

    <!-- Tabel Daftar Unit Loker -->
    <div class="table-responsive" style="max-height: 450px; overflow-y: auto;">
      <table class="table table-hover align-middle mb-0">
        <thead class="bg-light text-muted" style="font-size: 11.5px; text-transform: uppercase; position: sticky; top: 0; z-index: 1;">
          <tr>
            <th>No. Loker</th>
            <th>Status Okupansi</th>
            <th>Catatan Kerusakan / Keterangan</th>
            <th>Terakhir Diperbarui</th>
            @if(!Auth::user() || !Auth::user()->isOwner())
            <th class="text-right">Aksi</th>
            @endif
          </tr>
        </thead>
        <tbody>
          @forelse($lockers as $l)
            <tr>
              <td>
                <span class="font-weight-bold text-dark" style="font-size: 14px;">#{{ $l->locker_number }}</span>
              </td>
              <td>
                @if($l->status === 'tersedia')
                  <span class="badge badge-success px-2 py-1">Tersedia</span>
                @elseif($l->status === 'terpakai')
                  <span class="badge badge-primary px-2 py-1">Sedang Digunakan</span>
                @else
                  <span class="badge badge-danger px-2 py-1"><i class="icon-exclamation-triangle mr-1"></i> Rusak (Perlu Maintenance)</span>
                @endif
              </td>
              <td>
                @if($l->status === 'rusak' && $l->notes)
                  <span class="text-danger small font-weight-bold"><i class="icon-wrench mr-1"></i> {{ $l->notes }}</span>
                @elseif($l->status === 'rusak')
                  <span class="text-danger small font-italic">Dilaporkan rusak tanpa keterangan tambahan</span>
                @else
                  <span class="text-muted small">-</span>
                @endif
              </td>
              <td class="text-muted small">
                {{ $l->updated_at ? $l->updated_at->format('d M Y H:i') : '-' }}
              </td>
              @if(!Auth::user() || !Auth::user()->isOwner())
              <td class="text-right">
                @if($l->status !== 'terpakai')
                  <form action="{{ route('manager.lockers.destroy', $l->id) }}" method="POST" class="d-inline" data-confirm="Hapus unit loker #{{ $l->locker_number }} secara permanen? Nomor loker ini tidak dapat digunakan lagi.">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-xs btn-outline-danger font-weight-bold" style="border-radius: 6px;">
                      <i class="icon-trash"></i> Hapus
                    </button>
                  </form>
                @else
                  <span class="badge badge-light border text-muted small" title="Loker sedang dipakai member, tidak dapat dihapus">Sedang Digunakan</span>
                @endif
              </td>
              @endif
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center py-4 text-muted">Belum ada data loker. Silakan tambah jumlah loker gym.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  @endif

</div>

<!-- ========================================== -->
<!-- MODALS -->
<!-- ========================================== -->

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
        <button type="submit" class="btn btn-primary font-weight-bold px-4">Simpan Data</button>
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
        <button type="submit" class="btn btn-primary font-weight-bold px-4">Simpan Perubahan</button>
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
        <button type="submit" class="btn btn-primary font-weight-bold px-4">Simpan Data</button>
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
        <button type="submit" class="btn btn-primary font-weight-bold px-4">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Tambah Loker Satuan -->
<div class="modal fade" id="addSingleLockerModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <form action="{{ route('manager.lockers.store') }}" method="POST" class="modal-content shadow border-0">
      @csrf
      <div class="modal-header bg-light">
        <h5 class="modal-title font-weight-bold text-dark mb-0">Tambah Unit Loker Baru</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body p-4">
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark small mb-1">Nomor / Kode Loker *</label>
          <input type="text" name="locker_number" class="form-control font-weight-bold" placeholder="Contoh: 01, 02, A-01, B-10" required>
          <small class="text-muted">Nomor atau kode identifikasi loker fisik di gym.</small>
        </div>
        <div class="form-group mb-0">
          <label class="font-weight-bold text-dark small mb-1">Status Awal</label>
          <select name="status" class="form-control">
            <option value="tersedia">Tersedia (Siap Digunakan)</option>
            <option value="rusak">Rusak (Perlu Maintenance)</option>
          </select>
        </div>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary font-weight-bold px-4">Daftarkan Loker</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Tambah Loker Massal -->
<div class="modal fade" id="addBulkLockerModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <form action="{{ route('manager.lockers.bulk') }}" method="POST" class="modal-content shadow border-0">
      @csrf
      <div class="modal-header bg-light">
        <h5 class="modal-title font-weight-bold text-dark mb-0">Buat Jumlah Loker Gym (Massal)</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body p-4">
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark small mb-2 d-block">Metode Pembuatan Loker:</label>
          <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="modeQuantity" name="mode" value="quantity" class="custom-control-input" checked>
            <label class="custom-control-label font-weight-bold text-dark" for="modeQuantity">Berdasarkan Jumlah Unit</label>
          </div>
          <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="modeRange" name="mode" value="range" class="custom-control-input">
            <label class="custom-control-label font-weight-bold text-dark" for="modeRange">Rentang Nomor Tertentu</label>
          </div>
        </div>

        <div id="sectionQuantity">
          <div class="form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Berapa Banyak Loker yang Ingin Dibuat? *</label>
            <input type="number" name="quantity" id="inputQuantity" class="form-control font-weight-bold" min="1" max="100" value="10" placeholder="Contoh: 10, 20, 50">
            <small class="text-muted">Sistem akan otomatis membuat nomor urut loker yang belum terdaftar.</small>
          </div>
        </div>

        <div id="sectionRange" style="display: none;">
          <div class="row">
            <div class="col-6 form-group mb-3">
              <label class="font-weight-bold text-dark small mb-1">Dari Nomor *</label>
              <input type="number" name="start_number" id="inputStartNumber" class="form-control" min="1" value="1" placeholder="1">
            </div>
            <div class="col-6 form-group mb-3">
              <label class="font-weight-bold text-dark small mb-1">Sampai Nomor *</label>
              <input type="number" name="end_number" id="inputEndNumber" class="form-control" min="1" value="30" placeholder="30">
            </div>
          </div>
          <small class="text-muted d-block mb-3">Maksimal 100 nomor loker per batch.</small>
        </div>

        <div class="form-group mb-0">
          <label class="font-weight-bold text-dark small mb-1">Awalan / Prefix Huruf (Opsional)</label>
          <input type="text" name="prefix" class="form-control" placeholder="Kosongkan atau contoh: A, B, VIP">
          <small class="text-muted">Misal diisi <code>A</code>, nomor loker menjadi <code>A-01</code>, <code>A-02</code>, dst.</small>
        </div>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary font-weight-bold px-4">Generate & Buat Loker</button>
      </div>
    </form>
  </div>
</div>
@endsection
@section('scripts')
<script>
  $(document).ready(function() {
    // 1. Edit Promo Modal
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

    // 2. Edit Vendor Modal
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

    // 3. Toggle Mode Bulk Loker
    $('input[name="mode"]').on('change', function() {
      if ($(this).val() === 'quantity') {
        $('#sectionQuantity').show();
        $('#sectionRange').hide();
        $('#inputQuantity').prop('required', true);
        $('#inputStartNumber, #inputEndNumber').prop('required', false);
      } else {
        $('#sectionQuantity').hide();
        $('#sectionRange').show();
        $('#inputQuantity').prop('required', false);
        $('#inputStartNumber, #inputEndNumber').prop('required', true);
      }
    });
  });
</script>
@endsection
