@extends('layouts.layout')

@php
  $featureMeta = [
    'void' => [
      'title' => 'Otorisasi Pembatalan Kasir',
      'subtitle' => 'Persetujuan pembatalan transaksi kasir dan pengembalian stok barang',
    ],
    'shift' => [
      'title' => 'Shift dan Cuti Staf',
      'subtitle' => 'Pengaturan jadwal kerja harian dan permohonan cuti staf operasional',
    ],
    'equipment' => [
      'title' => 'Aset dan Alat Gym',
      'subtitle' => 'Pencatatan inventaris peralatan dan riwayat pemeliharaan alat',
    ],
    'stock' => [
      'title' => 'Peringatan Stok Barang',
      'subtitle' => 'Pemantauan ketersediaan produk kasir dengan batas minimum stok',
    ],
    'complaint' => [
      'title' => 'Komplain Member',
      'subtitle' => 'Penanganan dan tindak lanjut keluhan fasilitas serta layanan pelanggan',
    ],
    'locker' => [
      'title' => 'Master Loker Gym',
      'subtitle' => 'Pemantauan status ketersediaan dan kondisi unit loker penyimpanan',
    ],
  ];

  $currentMeta = $featureMeta[$activeTab] ?? [
    'title' => 'Operasional Lapangan',
    'subtitle' => 'Pengawasan operasional harian dan fasilitas gym',
  ];
@endphp

@section('title', $currentMeta['title'] . ' — PetGym')
@section('page_title', $currentMeta['title'])
@section('page_subtitle', $currentMeta['subtitle'])

@section('content')
<div class="container-fluid p-0">

  <!-- Tab Contents -->
  <div class="tab-content" id="supervisorTabContent">

    <!-- 1. TAB OTORISASI PEMBATALAN KASIR -->
    <div class="tab-pane fade {{ $activeTab === 'void' ? 'show active' : '' }}" id="void-sec" role="tabpanel">
      <div class="card-custom p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <h5 class="font-weight-bold text-dark mb-1">Daftar Pengajuan Pembatalan Transaksi Kasir</h5>
            <p class="text-muted small mb-0">Persetujuan atau penolakan pembatalan transaksi kasir secara langsung.</p>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="bg-light text-muted" style="font-size: 11.5px; text-transform: uppercase;">
              <tr>
                <th>Invoice</th>
                <th>Tanggal & Jam</th>
                <th>Kasir Pemohon</th>
                <th>Member / Pelanggan</th>
                <th>Total Nominal</th>
                <th>Alasan Pembatalan</th>
                <th>Status</th>
                <th>Tindakan Supervisor</th>
              </tr>
            </thead>
            <tbody>
              @forelse($voidTransactions as $vt)
                <tr>
                  <td class="font-weight-bold text-dark">{{ $vt->invoice_number }}</td>
                  <td>{{ $vt->created_at->format('d M Y, H:i') }} WIB</td>
                  <td>{{ $vt->user->name ?? 'Kasir Staf' }}</td>
                  <td>{{ $vt->member->name ?? 'Pelanggan Umum' }}</td>
                  <td class="font-weight-bold text-danger">Rp {{ number_format($vt->total_amount, 0, ',', '.') }}</td>
                  <td style="max-width: 200px;" class="small text-muted">{{ $vt->void_reason ?? 'Kesalahan input kasir' }}</td>
                  <td>
                    @if($vt->void_status === 'pending')
                      <span class="badge badge-warning text-dark font-weight-bold">Menunggu Persetujuan</span>
                    @elseif($vt->void_status === 'approved')
                      <span class="badge badge-success font-weight-bold">Disetujui (Dibatalkan)</span>
                    @elseif($vt->void_status === 'rejected')
                      <span class="badge badge-danger font-weight-bold">Ditolak</span>
                    @else
                      <span class="badge badge-secondary">{{ ucfirst($vt->void_status) }}</span>
                    @endif
                  </td>
                  <td>
                    @if($vt->void_status === 'pending')
                      <div class="d-flex gap-1">
                        <form action="{{ route('supervisor.void.approve', $vt->id) }}" method="POST" class="d-inline" data-confirm="Setujui pembatalan invoice {{ $vt->invoice_number }}? Stok produk akan dikembalikan secara otomatis.">
                          @csrf
                          <button type="submit" class="btn btn-sm btn-success font-weight-bold mr-1">
                            <i class="icon-check mr-1"></i> Setujui
                          </button>
                        </form>
                        <form action="{{ route('supervisor.void.reject', $vt->id) }}" method="POST" class="d-inline" data-confirm="Tolak permohonan pembatalan invoice {{ $vt->invoice_number }}?">
                          @csrf
                          <button type="submit" class="btn btn-sm btn-outline-danger font-weight-bold">
                            <i class="icon-close mr-1"></i> Tolak
                          </button>
                        </form>
                      </div>
                    @else
                      <span class="text-muted small">Selesai diproses</span>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="8" class="text-center py-4 text-muted">Belum ada riwayat permohonan pembatalan kasir.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- 2. TAB SHIFT & CUTI STAF -->
    <div class="tab-pane fade {{ $activeTab === 'shift' ? 'show active' : '' }}" id="shift-sec" role="tabpanel">
      <!-- Tabel 1: Jadwal Shift Kerja Staf -->
      <div class="card-custom p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <h5 class="font-weight-bold text-dark mb-1">Jadwal Shift Staf Operasional</h5>
            <p class="text-muted small mb-0">Pengaturan jam kerja harian resepsionis dan personal trainer di gym.</p>
          </div>
          <button type="button" class="btn btn-sm btn-success font-weight-bold px-3 py-2" data-toggle="modal" data-target="#addShiftModal" style="border-radius: 8px;">
            + Buat Jadwal Shift
          </button>
        </div>

        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
              <tr>
                <th>Tanggal</th>
                <th>Nama Staf</th>
                <th>Nama Shift</th>
                <th>Jam Kerja</th>
                <th>Catatan Khusus</th>
                <th class="text-right">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($staffShifts as $sh)
                <tr>
                  <td>{{ $sh->shift_date->format('d M Y') }}</td>
                  <td class="font-weight-bold text-dark">{{ $sh->user->name ?? '-' }}</td>
                  <td><span class="badge badge-info">{{ $sh->shift_name }}</span></td>
                  <td>{{ substr($sh->start_time, 0, 5) }} - {{ substr($sh->end_time, 0, 5) }} WIB</td>
                  <td class="text-muted small">{{ $sh->notes ?? '-' }}</td>
                  <td class="text-right">
                    <button type="button" class="btn btn-xs btn-outline-primary btn-edit-shift mr-1"
                      data-id="{{ $sh->id }}"
                      data-user_id="{{ $sh->user_id }}"
                      data-shift_date="{{ $sh->shift_date->format('Y-m-d') }}"
                      data-shift_name="{{ $sh->shift_name }}"
                      data-start_time="{{ substr($sh->start_time, 0, 5) }}"
                      data-end_time="{{ substr($sh->end_time, 0, 5) }}"
                      data-notes="{{ $sh->notes }}" style="border-radius: 6px;">Ubah</button>
                    <form action="{{ route('supervisor.shifts.destroy', $sh->id) }}" method="POST" class="d-inline" data-confirm="Hapus shift kerja ini?">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-xs btn-outline-danger" style="border-radius: 6px;">Hapus</button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center py-4 text-muted">Belum ada plotting shift staf operasional tercatat.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <!-- Tabel 2: Daftar & Riwayat Pengajuan Cuti Staf -->
      <div class="card-custom p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <h5 class="font-weight-bold text-dark mb-1">Daftar Pengajuan Cuti Staf</h5>
            <p class="text-muted small mb-0">Pencatatan dan persetujuan permohonan izin cuti staf operasional gym.</p>
          </div>
          <button type="button" class="btn btn-sm btn-primary font-weight-bold px-3 py-2" data-toggle="modal" data-target="#addLeaveModal" style="border-radius: 8px;">
            + Ajukan / Catat Cuti Staf
          </button>
        </div>

        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
              <tr>
                <th>Nama Staf</th>
                <th>Mulai Cuti</th>
                <th>Selesai Cuti</th>
                <th>Alasan Permohonan</th>
                <th>Status</th>
                <th class="text-right">Aksi Persetujuan</th>
              </tr>
            </thead>
            <tbody>
              @forelse($leaveRequests as $lr)
                <tr>
                  <td class="font-weight-bold text-dark">{{ $lr->user->name ?? '-' }}</td>
                  <td>{{ $lr->start_date ? $lr->start_date->format('d M Y') : '-' }}</td>
                  <td>{{ $lr->end_date ? $lr->end_date->format('d M Y') : '-' }}</td>
                  <td class="small text-muted">{{ $lr->reason }}</td>
                  <td>
                    @if($lr->status === 'approved')
                      <span class="badge badge-success font-weight-bold">Disetujui</span>
                    @elseif($lr->status === 'rejected')
                      <span class="badge badge-danger font-weight-bold">Ditolak</span>
                    @else
                      <span class="badge badge-warning text-dark font-weight-bold">Menunggu Persetujuan</span>
                    @endif
                  </td>
                  <td class="text-right">
                    @if($lr->status === 'pending')
                      <form action="{{ route('supervisor.leave.approve', $lr->id) }}" method="POST" class="d-inline mr-1" data-confirm="Setujui permohonan cuti untuk {{ $lr->user->name ?? 'staf' }}?">
                        @csrf
                        <button type="submit" class="btn btn-xs btn-success font-weight-bold" style="border-radius: 6px;">Setujui</button>
                      </form>
                      <form action="{{ route('supervisor.leave.reject', $lr->id) }}" method="POST" class="d-inline" data-confirm="Tolak permohonan cuti ini?">
                        @csrf
                        <button type="submit" class="btn btn-xs btn-outline-danger font-weight-bold" style="border-radius: 6px;">Tolak</button>
                      </form>
                    @else
                      <span class="text-muted small">Selesai diproses</span>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center py-4 text-muted">Belum ada riwayat pengajuan cuti staf.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- 3. TAB ASET & PEMELIHARAAN ALAT GYM -->
    <div class="tab-pane fade {{ $activeTab === 'equipment' ? 'show active' : '' }}" id="equipment-sec" role="tabpanel">
      <!-- Tabel 1: Inventaris Peralatan Gym -->
      <div class="card-custom p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <h5 class="font-weight-bold text-dark mb-1">Daftar Inventaris Peralatan Gym</h5>
            <p class="text-muted small mb-0">Pantau kondisi fisik mesin beban, alat kardio, dan perlengkapan gym.</p>
          </div>
          <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-success font-weight-bold px-3 py-2 mr-2" data-toggle="modal" data-target="#addEquipmentModal" style="border-radius: 8px;">
              + Tambah Alat Baru
            </button>
            <button type="button" class="btn btn-sm btn-primary font-weight-bold px-3 py-2" data-toggle="modal" data-target="#addMaintenanceModal" style="border-radius: 8px;">
              + Catat Servis / Perbaikan Alat
            </button>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
              <tr>
                <th>Nama Alat</th>
                <th>Kategori</th>
                <th>Merk / Brand</th>
                <th>Status Kondisi</th>
                <th>Tanggal Beli</th>
                <th>Jadwal Servis Berikutnya</th>
                <th class="text-right">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($equipments as $eq)
                <tr>
                  <td class="font-weight-bold text-dark">{{ $eq->name }}</td>
                  <td><span class="badge badge-secondary">{{ ucfirst($eq->category) }}</span></td>
                  <td>{{ $eq->brand ?? '-' }}</td>
                  <td>
                    @if($eq->status === 'berfungsi')
                      <span class="badge badge-success font-weight-bold">Berfungsi Normal</span>
                    @elseif($eq->status === 'perlu_perbaikan')
                      <span class="badge badge-warning text-dark font-weight-bold">Perlu Servis</span>
                    @else
                      <span class="badge badge-danger font-weight-bold">Rusak Berat</span>
                    @endif
                  </td>
                  <td class="small text-muted">{{ $eq->purchase_date ? $eq->purchase_date->format('d M Y') : '-' }}</td>
                  <td class="small">{{ $eq->next_service_date ? $eq->next_service_date->format('d M Y') : '-' }}</td>
                  <td class="text-right">
                    <button type="button" class="btn btn-xs btn-outline-primary btn-edit-eq mr-1"
                      data-id="{{ $eq->id }}"
                      data-name="{{ $eq->name }}"
                      data-category="{{ $eq->category }}"
                      data-brand="{{ $eq->brand }}"
                      data-status="{{ $eq->status }}"
                      data-purchase_date="{{ $eq->purchase_date ? $eq->purchase_date->format('Y-m-d') : '' }}"
                      data-next_service_date="{{ $eq->next_service_date ? $eq->next_service_date->format('Y-m-d') : '' }}"
                      data-notes="{{ $eq->notes }}" style="border-radius: 6px;">Ubah</button>
                    <form action="{{ route('supervisor.equipment.destroy', $eq->id) }}" method="POST" class="d-inline" data-confirm="Hapus alat ini dari inventaris?">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-xs btn-outline-danger" style="border-radius: 6px;">Hapus</button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="text-center py-4 text-muted">Belum ada data inventaris alat gym terdaftar.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <!-- Tabel 2: Riwayat Pemeliharaan & Servis Alat -->
      <div class="card-custom p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <h5 class="font-weight-bold text-dark mb-1">Riwayat Pemeliharaan &amp; Servis Alat Gym</h5>
            <p class="text-muted small mb-0">Catatan riwayat perbaikan fisik, biaya teknisi, dan pembaruan kondisi alat.</p>
          </div>
          <button type="button" class="btn btn-sm btn-primary font-weight-bold px-3 py-2" data-toggle="modal" data-target="#addMaintenanceModal" style="border-radius: 8px;">
            + Catat Servis / Perbaikan Alat
          </button>
        </div>

        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
              <tr>
                <th>Tanggal Servis</th>
                <th>Nama Alat</th>
                <th>Tindakan Pemeliharaan</th>
                <th>Biaya Perbaikan</th>
                <th>Deskripsi / Teknisi</th>
                <th>Jadwal Servis Berikutnya</th>
              </tr>
            </thead>
            <tbody>
              @forelse($maintenanceLogs as $ml)
                <tr>
                  <td>{{ $ml->serviced_at ? $ml->serviced_at->format('d M Y') : '-' }}</td>
                  <td class="font-weight-bold text-dark">{{ $ml->equipment->name ?? 'Alat Dihapus' }}</td>
                  <td><span class="badge badge-info">{{ $ml->action }}</span></td>
                  <td class="font-weight-bold text-success">Rp {{ number_format($ml->cost, 0, ',', '.') }}</td>
                  <td class="small text-muted">{{ $ml->description ?? '-' }}</td>
                  <td class="small text-muted">{{ $ml->next_service_date ? $ml->next_service_date->format('d M Y') : '-' }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center py-4 text-muted">Belum ada riwayat servis atau pemeliharaan alat.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- 4. TAB STOK RITEL MENIPIS -->
    <div class="tab-pane fade {{ $activeTab === 'stock' ? 'show active' : '' }}" id="stock-sec" role="tabpanel">
      <div class="card-custom p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <h5 class="font-weight-bold text-dark mb-1">Peringatan Stok Menipis</h5>
            <p class="text-muted small mb-0">Daftar produk jualan kasir dengan sisa stok sepuluh unit atau kurang.</p>
          </div>
        </div>

        <div class="table-responsive mb-4">
          <table class="table table-hover align-middle mb-0">
            <thead class="bg-light text-muted" style="font-size: 11.5px; text-transform: uppercase;">
              <tr>
                <th>Nama Produk</th>
                <th>Kategori</th>
                <th>Harga Jual</th>
                <th>Sisa Stok Fisik</th>
                <th>Status Ketersediaan</th>
              </tr>
            </thead>
            <tbody>
              @forelse($lowStockProducts as $lsp)
                <tr>
                  <td><strong class="text-dark">{{ $lsp->name }}</strong></td>
                  <td><span class="badge badge-secondary">{{ $lsp->category ?? 'Umum' }}</span></td>
                  <td>Rp {{ number_format($lsp->price, 0, ',', '.') }}</td>
                  <td><strong class="text-danger" style="font-size: 15px;">{{ $lsp->stock }}</strong> unit</td>
                  <td>
                    @if($lsp->stock == 0)
                      <span class="badge badge-danger font-weight-bold">Habis Total</span>
                    @else
                      <span class="badge badge-warning text-dark font-weight-bold">Menipis (Perlu Restock)</span>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center py-4 text-muted">
                    <span class="icon-check-circle text-success d-block mb-1" style="font-size: 24px;"></span>
                    Semua stok produk ritel dalam kondisi aman (di atas 10 unit).
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <h6 class="font-weight-bold text-dark mb-3 border-top pt-3">Katalog Produk Kasir</h6>
        <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
          <table class="table table-hover align-middle mb-0">
            <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
              <tr>
                <th>Produk</th>
                <th>Kategori</th>
                <th>Harga Jual</th>
                <th>Stok</th>
              </tr>
            </thead>
            <tbody>
              @forelse($allProducts as $p)
                <tr>
                  <td class="font-weight-bold text-dark">{{ $p->name }}</td>
                  <td>{{ $p->category ?? 'Umum' }}</td>
                  <td>Rp {{ number_format($p->price, 0, ',', '.') }}</td>
                  <td>
                    <span class="badge {{ $p->stock <= 10 ? 'badge-warning text-dark' : 'badge-success' }} font-weight-bold">
                      {{ $p->stock }} unit
                    </span>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="text-center py-3 text-muted">Belum ada produk jualan kasir.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- 5. TAB KOMPLAIN MEMBER -->
    <div class="tab-pane fade {{ $activeTab === 'complaint' ? 'show active' : '' }}" id="complaint-sec" role="tabpanel">
      <div class="card-custom p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <h5 class="font-weight-bold text-dark mb-1">Daftar Keluhan &amp; Komplain Member</h5>
            <p class="text-muted small mb-0">Tindak lanjut laporan keluhan fasilitas, kebersihan, dan pelayanan staf operasional.</p>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="bg-light text-muted" style="font-size: 11.5px; text-transform: uppercase;">
              <tr>
                <th>ID</th>
                <th>Tanggal Laporan</th>
                <th>Member &amp; Kontak</th>
                <th>Judul Keluhan</th>
                <th>Detail Deskripsi</th>
                <th>Status Penanganan</th>
                <th>Solusi / Resolusi</th>
                <th class="text-right">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($complaints as $c)
                <tr>
                  <td>#{{ $c->id }}</td>
                  <td>{{ $c->created_at->format('d M Y, H:i') }} WIB</td>
                  <td>
                    <strong class="text-dark d-block">{{ $c->member->name ?? 'Anonim' }}</strong>
                    <small class="text-muted">{{ $c->member && $c->member->phone ? $c->member->phone : '-' }}</small>
                  </td>
                  <td class="font-weight-bold text-dark">{{ $c->title }}</td>
                  <td style="max-width: 250px;" class="small text-muted">{{ $c->description }}</td>
                  <td>
                    @if($c->status === 'open')
                      <span class="badge badge-danger font-weight-bold">Menunggu Penanganan</span>
                    @elseif($c->status === 'in_progress')
                      <span class="badge badge-warning text-dark font-weight-bold">Sedang Diproses</span>
                    @elseif($c->status === 'resolved')
                      <span class="badge badge-success font-weight-bold">Terselesaikan</span>
                    @else
                      <span class="badge badge-secondary font-weight-bold">Selesai / Ditutup</span>
                    @endif
                  </td>
                  <td style="max-width: 200px;" class="small text-muted">{{ $c->resolution ?? 'Belum ada resolusi' }}</td>
                  <td class="text-right">
                    <button type="button" class="btn btn-sm btn-primary font-weight-bold btn-edit-complaint"
                      data-id="{{ $c->id }}"
                      data-title="{{ $c->title }}"
                      data-member="{{ $c->member ? $c->member->name : 'Anonim' }}"
                      data-contact="{{ $c->member && $c->member->phone ? $c->member->phone : '-' }}"
                      data-date="{{ $c->created_at->format('d M Y, H:i') }} WIB"
                      data-description="{{ $c->description }}"
                      data-status="{{ $c->status }}"
                      data-resolution="{{ $c->resolution }}" style="border-radius: 6px; padding: 4px 10px;">
                      <i class="icon-pencil mr-1"></i> Tindak Lanjuti
                    </button>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="8" class="text-center py-4 text-muted">Belum ada tiket keluhan member yang masuk.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- 6. TAB MASTER LOKER GYM -->
    <div class="tab-pane fade {{ $activeTab === 'locker' ? 'show active' : '' }}" id="locker-sec" role="tabpanel">
      <div class="card-custom p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <h5 class="font-weight-bold text-dark mb-1">Status Unit Loker Gym</h5>
            <p class="text-muted small mb-0">Pantau ketersediaan fisik loker penyimpanan barang member di area gym.</p>
          </div>
        </div>

        <div class="row mb-4">
          <div class="col-md-4 mb-2 mb-md-0">
            <div class="p-3 bg-light rounded text-center border">
              <span class="badge badge-success px-2 py-1 font-weight-bold mb-1">Tersedia</span>
              <h3 class="font-weight-bold text-dark mb-0">{{ $availableLockers }}</h3>
              <small class="text-muted">Loker siap dipinjamkan</small>
            </div>
          </div>
          <div class="col-md-4 mb-2 mb-md-0">
            <div class="p-3 bg-light rounded text-center border">
              <span class="badge badge-warning text-dark px-2 py-1 font-weight-bold mb-1">Sedang Terpakai</span>
              <h3 class="font-weight-bold text-dark mb-0">{{ $occupiedLockers }}</h3>
              <small class="text-muted">Sedang digunakan member</small>
            </div>
          </div>
          <div class="col-md-4">
            <div class="p-3 bg-light rounded text-center border">
              <span class="badge badge-danger px-2 py-1 font-weight-bold mb-1">Rusak</span>
              <h3 class="font-weight-bold text-dark mb-0">{{ $brokenLockers }}</h3>
              <small class="text-muted">Perlu perbaikan kunci/pintu</small>
            </div>
          </div>
        </div>

        <div class="row">
          @forelse($lockers as $l)
            <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-3">
              <div class="p-3 rounded text-center border {{ $l->status === 'tersedia' ? 'border-success bg-light-success' : ($l->status === 'terpakai' ? 'border-warning bg-light-warning' : 'border-danger bg-light-danger') }}">
                <div class="font-weight-bold text-dark" style="font-size: 16px;">#{{ $l->locker_number }}</div>
                <small class="d-block font-weight-bold {{ $l->status === 'tersedia' ? 'text-success' : ($l->status === 'terpakai' ? 'text-warning' : 'text-danger') }}">
                  {{ ucfirst($l->status) }}
                </small>
              </div>
            </div>
          @empty
            <div class="col-12 text-center py-4 text-muted">
              Belum ada data unit loker yang didaftarkan.
            </div>
          @endforelse
        </div>
      </div>
    </div>

  </div>

</div>

<!-- ========================================== -->
<!-- MODALS -->
<!-- ========================================== -->

<!-- Modal Tambah Alat Gym -->
<div class="modal fade" id="addEquipmentModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <form action="{{ route('supervisor.equipment.store') }}" method="POST" class="modal-content shadow border-0" style="border-radius: 12px;">
      @csrf
      <div class="modal-header bg-light">
        <h5 class="modal-title font-weight-bold text-dark mb-0">Tambah Inventaris Alat Gym</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body p-4">
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark small mb-1">Nama Alat *</label>
          <input type="text" name="name" class="form-control" placeholder="Contoh: Treadmill Pro, Barbell Olympic" required style="border-radius: 8px;">
        </div>
        <div class="row">
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Kategori *</label>
            <select name="category" class="form-control" required style="border-radius: 8px;">
              <option value="kardio">Kardio</option>
              <option value="beban">Beban / Mesin</option>
              <option value="free_weight">Beban Bebas (Free Weight)</option>
              <option value="aksesoris">Aksesoris &amp; Matras</option>
            </select>
          </div>
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Merk / Brand</label>
            <input type="text" name="brand" class="form-control" placeholder="Contoh: LifeFitness, Matrix" style="border-radius: 8px;">
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Status Awal *</label>
            <select name="status" class="form-control" required style="border-radius: 8px;">
              <option value="berfungsi">Berfungsi Normal</option>
              <option value="perlu_perbaikan">Perlu Servis</option>
              <option value="rusak_berat">Rusak Berat</option>
            </select>
          </div>
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Tanggal Beli</label>
            <input type="date" name="purchase_date" class="form-control" style="border-radius: 8px;">
          </div>
        </div>
        <div class="form-group mb-0">
          <label class="font-weight-bold text-dark small mb-1">Jadwal Servis Berikutnya</label>
          <input type="date" name="next_service_date" class="form-control" style="border-radius: 8px;">
        </div>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary font-weight-bold px-3" data-dismiss="modal" style="border-radius: 8px;">Batal</button>
        <button type="submit" class="btn btn-success font-weight-bold px-4" style="border-radius: 8px;">Simpan Alat</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Ubah Alat Gym -->
<div class="modal fade" id="editEquipmentModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <form id="editEquipmentForm" method="POST" class="modal-content shadow border-0" style="border-radius: 12px;">
      @csrf
      @method('PUT')
      <div class="modal-header bg-light">
        <h5 class="modal-title font-weight-bold text-dark mb-0">Ubah Inventaris Alat Gym</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body p-4">
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark small mb-1">Nama Alat *</label>
          <input type="text" name="name" id="editEqName" class="form-control" required style="border-radius: 8px;">
        </div>
        <div class="row">
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Kategori *</label>
            <select name="category" id="editEqCategory" class="form-control" required style="border-radius: 8px;">
              <option value="kardio">Kardio</option>
              <option value="beban">Beban / Mesin</option>
              <option value="free_weight">Beban Bebas (Free Weight)</option>
              <option value="aksesoris">Aksesoris &amp; Matras</option>
            </select>
          </div>
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Merk / Brand</label>
            <input type="text" name="brand" id="editEqBrand" class="form-control" style="border-radius: 8px;">
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Status Alat *</label>
            <select name="status" id="editEqStatus" class="form-control" required style="border-radius: 8px;">
              <option value="berfungsi">Berfungsi Normal</option>
              <option value="perlu_perbaikan">Perlu Servis</option>
              <option value="rusak_berat">Rusak Berat</option>
            </select>
          </div>
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Tanggal Beli</label>
            <input type="date" name="purchase_date" id="editEqPurchaseDate" class="form-control" style="border-radius: 8px;">
          </div>
        </div>
        <div class="form-group mb-0">
          <label class="font-weight-bold text-dark small mb-1">Jadwal Servis Berikutnya</label>
          <input type="date" name="next_service_date" id="editEqNextService" class="form-control" style="border-radius: 8px;">
        </div>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary font-weight-bold px-3" data-dismiss="modal" style="border-radius: 8px;">Batal</button>
        <button type="submit" class="btn btn-primary font-weight-bold px-4" style="border-radius: 8px;">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Pencatatan Servis Alat (Baru) -->
<div class="modal fade" id="addMaintenanceModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <form action="{{ route('supervisor.maintenance.store') }}" method="POST" class="modal-content shadow border-0" style="border-radius: 12px;">
      @csrf
      <div class="modal-header bg-light">
        <h5 class="modal-title font-weight-bold text-dark mb-0">Pencatatan Servis / Perbaikan Alat Gym</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body p-4">
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark small mb-1">Pilih Alat Gym yang Diservis *</label>
          <select name="gym_equipment_id" class="form-control" required style="border-radius: 8px;">
            <option value="">-- Pilih Alat Gym --</option>
            @foreach($equipments as $eq)
              <option value="{{ $eq->id }}">{{ $eq->name }} ({{ $eq->brand ?? 'Tanpa Merk' }}) - Kondisi: {{ ucfirst($eq->status) }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark small mb-1">Tindakan Servis / Perbaikan *</label>
          <input type="text" name="action" class="form-control" placeholder="Contoh: Ganti kabel sling, pelumasan bearing, ganti dinamo" required style="border-radius: 8px;">
        </div>
        <div class="row">
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Biaya Perbaikan (Rp) *</label>
            <input type="number" name="cost" class="form-control" value="0" min="0" required style="border-radius: 8px;">
          </div>
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Tanggal Servis *</label>
            <input type="date" name="serviced_at" class="form-control" value="{{ date('Y-m-d') }}" required style="border-radius: 8px;">
          </div>
        </div>
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark small mb-1">Nama Teknisi / Rincian Vendor</label>
          <textarea name="description" class="form-control" rows="2" placeholder="Nama teknisi panggilan atau rincian sparepart yang diganti..." style="border-radius: 8px;"></textarea>
        </div>
        <div class="form-group mb-0">
          <label class="font-weight-bold text-dark small mb-1">Jadwal Servis Berkala Berikutnya</label>
          <input type="date" name="next_service_date" class="form-control" style="border-radius: 8px;">
          <small class="text-muted" style="font-size: 11px;">Status alat akan otomatis di-reset menjadi Berfungsi Normal.</small>
        </div>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary font-weight-bold px-3" data-dismiss="modal" style="border-radius: 8px;">Batal</button>
        <button type="submit" class="btn btn-primary font-weight-bold px-4" style="border-radius: 8px;">Simpan &amp; Reset Status Alat</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Tambah Shift Staf -->
<div class="modal fade" id="addShiftModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <form action="{{ route('supervisor.shifts.store') }}" method="POST" class="modal-content shadow border-0" style="border-radius: 12px;">
      @csrf
      <div class="modal-header bg-light">
        <h5 class="modal-title font-weight-bold text-dark mb-0">Buat Jadwal Shift Kerja Staf</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body p-4">
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark small mb-1">Pilih Staf Operasional *</label>
          <select name="user_id" class="form-control" required style="border-radius: 8px;">
            <option value="">-- Pilih Staf --</option>
            @foreach($shiftStaffUsers as $su)
              <option value="{{ $su->id }}">{{ $su->name }} ({{ ucfirst($su->role) }})</option>
            @endforeach
          </select>
        </div>
        <div class="row">
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Tanggal Shift *</label>
            <input type="date" name="shift_date" class="form-control" value="{{ date('Y-m-d') }}" required style="border-radius: 8px;">
          </div>
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Nama Shift *</label>
            <select name="shift_name" class="form-control" required style="border-radius: 8px;">
              <option value="Pagi">Shift Pagi</option>
              <option value="Siang">Shift Siang</option>
              <option value="Malam">Shift Malam</option>
              <option value="Full Day">Seharian (Full Day)</option>
            </select>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Jam Masuk *</label>
            <input type="time" name="start_time" class="form-control" value="06:00" required style="border-radius: 8px;">
          </div>
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Jam Pulang *</label>
            <input type="time" name="end_time" class="form-control" value="14:00" required style="border-radius: 8px;">
          </div>
        </div>
        <div class="form-group mb-0">
          <label class="font-weight-bold text-dark small mb-1">Catatan Khusus</label>
          <input type="text" name="notes" class="form-control" placeholder="Contoh: Bertugas di frontdesk utama" style="border-radius: 8px;">
        </div>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary font-weight-bold px-3" data-dismiss="modal" style="border-radius: 8px;">Batal</button>
        <button type="submit" class="btn btn-success font-weight-bold px-4" style="border-radius: 8px;">Simpan Jadwal Shift</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Ubah Shift Staf -->
<div class="modal fade" id="editShiftModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <form id="editShiftForm" method="POST" class="modal-content shadow border-0" style="border-radius: 12px;">
      @csrf
      @method('PUT')
      <div class="modal-header bg-light">
        <h5 class="modal-title font-weight-bold text-dark mb-0">Ubah Plotting Shift</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body p-4">
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark small mb-1">Pilih Staf Operasional *</label>
          <select name="user_id" id="editShiftUserId" class="form-control" required style="border-radius: 8px;">
            @foreach($shiftStaffUsers as $su)
              <option value="{{ $su->id }}">{{ $su->name }} ({{ ucfirst($su->role) }})</option>
            @endforeach
          </select>
        </div>
        <div class="row">
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Tanggal Shift *</label>
            <input type="date" name="shift_date" id="editShiftDate" class="form-control" required style="border-radius: 8px;">
          </div>
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Nama Shift *</label>
            <select name="shift_name" id="editShiftName" class="form-control" required style="border-radius: 8px;">
              <option value="Pagi">Shift Pagi</option>
              <option value="Siang">Shift Siang</option>
              <option value="Malam">Shift Malam</option>
              <option value="Full Day">Seharian (Full Day)</option>
            </select>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Jam Masuk *</label>
            <input type="time" name="start_time" id="editShiftStart" class="form-control" required style="border-radius: 8px;">
          </div>
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Jam Pulang *</label>
            <input type="time" name="end_time" id="editShiftEnd" class="form-control" required style="border-radius: 8px;">
          </div>
        </div>
        <div class="form-group mb-0">
          <label class="font-weight-bold text-dark small mb-1">Catatan Khusus</label>
          <input type="text" name="notes" id="editShiftNotes" class="form-control" style="border-radius: 8px;">
        </div>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary font-weight-bold px-3" data-dismiss="modal" style="border-radius: 8px;">Batal</button>
        <button type="submit" class="btn btn-primary font-weight-bold px-4" style="border-radius: 8px;">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Ajukan / Catat Cuti Staf (Baru) -->
<div class="modal fade" id="addLeaveModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <form action="{{ route('supervisor.leave.store') }}" method="POST" class="modal-content shadow border-0" style="border-radius: 12px;">
      @csrf
      <div class="modal-header bg-light">
        <h5 class="modal-title font-weight-bold text-dark mb-0">Pencatatan Permohonan Cuti Staf</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body p-4">
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark small mb-1">Pilih Staf Operasional *</label>
          <select name="user_id" class="form-control" required style="border-radius: 8px;">
            <option value="">-- Pilih Staf --</option>
            @foreach($shiftStaffUsers as $su)
              <option value="{{ $su->id }}">{{ $su->name }} ({{ ucfirst($su->role) }})</option>
            @endforeach
          </select>
        </div>
        <div class="row">
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Mulai Cuti *</label>
            <input type="date" name="start_date" class="form-control" value="{{ date('Y-m-d') }}" required style="border-radius: 8px;">
          </div>
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Selesai Cuti *</label>
            <input type="date" name="end_date" class="form-control" value="{{ date('Y-m-d') }}" required style="border-radius: 8px;">
          </div>
        </div>
        <div class="form-group mb-0">
          <label class="font-weight-bold text-dark small mb-1">Alasan Permohonan Cuti *</label>
          <textarea name="reason" class="form-control" rows="3" placeholder="Contoh: Sakit, keperluan keluarga mendesak..." required style="border-radius: 8px;"></textarea>
        </div>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary font-weight-bold px-3" data-dismiss="modal" style="border-radius: 8px;">Batal</button>
        <button type="submit" class="btn btn-primary font-weight-bold px-4" style="border-radius: 8px;">Simpan &amp; Setujui Cuti</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Tindak Lanjut Komplain Member -->
<div class="modal fade" id="editComplaintModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <form id="editComplaintForm" method="POST" class="modal-content shadow border-0" style="border-radius: 12px;">
      @csrf
      @method('PUT')
      <div class="modal-header bg-light">
        <h5 class="modal-title font-weight-bold text-dark mb-0">Tindak Lanjut Keluhan Member</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body p-4">
        <!-- Info Ringkas Member & Laporan -->
        <div class="p-3 bg-light rounded border mb-3" style="border-radius: 8px;">
          <div class="d-flex justify-content-between mb-1">
            <span class="text-muted small">Pelapor:</span>
            <strong class="text-dark small" id="complaintMemberDisplay">-</strong>
          </div>
          <div class="d-flex justify-content-between mb-1">
            <span class="text-muted small">No. Kontak:</span>
            <span class="text-dark small font-weight-bold" id="complaintContactDisplay">-</span>
          </div>
          <div class="d-flex justify-content-between mb-1">
            <span class="text-muted small">Waktu Laporan:</span>
            <span class="text-muted small" id="complaintDateDisplay">-</span>
          </div>
          <div class="border-top pt-2 mt-2">
            <span class="text-muted small d-block mb-1">Judul Keluhan:</span>
            <strong class="text-dark d-block mb-1" id="complaintTitleDisplay">-</strong>
            <span class="text-muted small d-block mb-1">Isi Keluhan:</span>
            <p class="text-dark small mb-0 font-italic" id="complaintDescDisplay">-</p>
          </div>
        </div>

        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark small mb-1">Status Penanganan Keluhan *</label>
          <select name="status" id="editComplaintStatus" class="form-control" required style="border-radius: 8px;">
            <option value="open">Menunggu Penanganan</option>
            <option value="in_progress">Sedang Diproses</option>
            <option value="resolved">Terselesaikan</option>
            <option value="closed">Selesai / Ditutup</option>
          </select>
        </div>
        <div class="form-group mb-0">
          <label class="font-weight-bold text-dark small mb-1">Catatan Solusi / Tindakan Supervisor</label>
          <textarea name="resolution" id="editComplaintResolution" class="form-control" rows="3" placeholder="Jelaskan tindakan yang telah dilakukan untuk menyelesaikan keluhan ini..." style="border-radius: 8px;"></textarea>
        </div>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary font-weight-bold px-3" data-dismiss="modal" style="border-radius: 8px;">Batal</button>
        <button type="submit" class="btn btn-primary font-weight-bold px-4" style="border-radius: 8px;">Simpan Tanggapan</button>
      </div>
    </form>
  </div>
</div>

@endsection

@push('scripts')
<script>
  $(document).ready(function() {
    // 1. Edit Equipment Modal
    $('.btn-edit-eq').on('click', function() {
      const id = $(this).data('id');
      $('#editEquipmentForm').attr('action', '/supervisor/equipment/' + id);
      $('#editEqName').val($(this).data('name'));
      $('#editEqCategory').val($(this).data('category'));
      $('#editEqBrand').val($(this).data('brand'));
      $('#editEqStatus').val($(this).data('status'));
      $('#editEqPurchaseDate').val($(this).data('purchase_date'));
      $('#editEqNextService').val($(this).data('next_service_date'));
      $('#editEquipmentModal').modal('show');
    });

    // 2. Edit Shift Modal
    $('.btn-edit-shift').on('click', function() {
      const id = $(this).data('id');
      $('#editShiftForm').attr('action', '/supervisor/shifts/' + id);
      $('#editShiftUserId').val($(this).data('user_id'));
      $('#editShiftDate').val($(this).data('shift_date'));
      $('#editShiftName').val($(this).data('shift_name'));
      $('#editShiftStart').val($(this).data('start_time'));
      $('#editShiftEnd').val($(this).data('end_time'));
      $('#editShiftNotes').val($(this).data('notes'));
      $('#editShiftModal').modal('show');
    });

    // 3. Edit Complaint Modal
    $('.btn-edit-complaint').on('click', function() {
      const id = $(this).data('id');
      $('#editComplaintForm').attr('action', '/supervisor/complaints/' + id);
      $('#complaintMemberDisplay').text($(this).data('member'));
      $('#complaintContactDisplay').text($(this).data('contact'));
      $('#complaintDateDisplay').text($(this).data('date'));
      $('#complaintTitleDisplay').text($(this).data('title'));
      $('#complaintDescDisplay').text($(this).data('description'));
      $('#editComplaintStatus').val($(this).data('status'));
      $('#editComplaintResolution').val($(this).data('resolution'));
      $('#editComplaintModal').modal('show');
    });
  });
</script>
@endpush
