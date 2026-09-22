@extends('layouts.layout')

@php
  $featureMeta = [
    'void' => [
      'title' => 'Otorisasi Void Kasir',
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

    <!-- 1. TAB OTORISASI VOID KASIR -->
    <div class="tab-pane fade {{ $activeTab === 'void' ? 'show active' : '' }}" id="void-sec" role="tabpanel">
      <div class="card-custom p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <h5 class="font-weight-bold text-dark mb-1">Daftar Pengajuan Void Kasir</h5>
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
                  <td>{{ $vt->created_at->format('d M Y, H:i') }}</td>
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
                  <td colspan="8" class="text-center py-4 text-muted">Belum ada riwayat permohonan void kasir.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- 2. TAB SHIFT & CUTI STAF -->
    <div class="tab-pane fade {{ $activeTab === 'shift' ? 'show active' : '' }}" id="shift-sec" role="tabpanel">
      <div class="row">
        <!-- Kolom Kiri: Jadwal Shift Kerja -->
        <div class="col-lg-7 mb-4">
          <div class="card-custom p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div>
                <h5 class="font-weight-bold text-dark mb-1">Jadwal Shift Staf</h5>
                <p class="text-muted small mb-0">Atur pembagian jam kerja resepsionis dan instruktur pelatih.</p>
              </div>
              <button type="button" class="btn btn-sm btn-success font-weight-bold" data-toggle="modal" data-target="#addShiftModal">
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
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($staffShifts as $sh)
                    <tr>
                      <td>{{ $sh->shift_date->format('d M Y') }}</td>
                      <td class="font-weight-bold text-dark">{{ $sh->user->name ?? '-' }}</td>
                      <td><span class="badge badge-info">{{ $sh->shift_name }}</span></td>
                      <td>{{ substr($sh->start_time, 0, 5) }} - {{ substr($sh->end_time, 0, 5) }}</td>
                      <td>
                        <button type="button" class="btn btn-xs btn-outline-primary btn-edit-shift"
                          data-id="{{ $sh->id }}"
                          data-user_id="{{ $sh->user_id }}"
                          data-shift_date="{{ $sh->shift_date->format('Y-m-d') }}"
                          data-shift_name="{{ $sh->shift_name }}"
                          data-start_time="{{ substr($sh->start_time, 0, 5) }}"
                          data-end_time="{{ substr($sh->end_time, 0, 5) }}"
                          data-notes="{{ $sh->notes }}">Ubah</button>
                        <form action="{{ route('supervisor.shifts.destroy', $sh->id) }}" method="POST" class="d-inline" data-confirm="Hapus shift kerja ini?">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-xs btn-outline-danger">Hapus</button>
                        </form>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="5" class="text-center py-4 text-muted">Belum ada plotting shift staf tercatat.</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Kolom Kanan: Pengajuan Cuti Staf -->
        <div class="col-lg-5 mb-4">
          <div class="card-custom p-4">
            <h5 class="font-weight-bold text-dark mb-1">Pencatatan Cuti Staf</h5>
            <p class="text-muted small mb-3">Input dan persetujuan permohonan cuti karyawan lapangan.</p>

            <form action="{{ route('supervisor.leave.store') }}" method="POST" class="mb-4 p-3 bg-light rounded border">
              @csrf
              <div class="form-group mb-2">
                <label class="font-weight-bold mb-1" style="font-size:12px;">Pilih Staf *</label>
                <select name="user_id" class="form-control form-control-sm" required>
                  <option value="">-- Pilih Staf --</option>
                  @foreach($shiftStaffUsers as $su)
                    <option value="{{ $su->id }}">{{ $su->name }} ({{ ucfirst($su->role) }})</option>
                  @endforeach
                </select>
              </div>
              <div class="row">
                <div class="col-6 form-group mb-2">
                  <label class="font-weight-bold mb-1" style="font-size:12px;">Mulai Cuti *</label>
                  <input type="date" name="start_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-6 form-group mb-2">
                  <label class="font-weight-bold mb-1" style="font-size:12px;">Selesai Cuti *</label>
                  <input type="date" name="end_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
                </div>
              </div>
              <div class="form-group mb-3">
                <label class="font-weight-bold mb-1" style="font-size:12px;">Alasan Cuti *</label>
                <textarea name="reason" class="form-control form-control-sm" rows="2" placeholder="Contoh: Sakit, urusan keluarga..." required></textarea>
              </div>
              <button type="submit" class="btn btn-sm btn-primary btn-block font-weight-bold">Simpan & Setujui Cuti</button>
            </form>

            <h6 class="font-weight-bold text-dark mb-2">Riwayat Pengajuan Cuti</h6>
            <div style="max-height: 250px; overflow-y: auto;">
              @forelse($leaveRequests as $lr)
                <div class="p-2 border-bottom bg-white d-flex justify-content-between align-items-center">
                  <div>
                    <strong class="text-dark d-block" style="font-size: 13px;">{{ $lr->user->name ?? '-' }}</strong>
                    <small class="text-muted">{{ $lr->start_date->format('d M') }} s/d {{ $lr->end_date->format('d M Y') }} &bull; {{ $lr->reason }}</small>
                  </div>
                  <div>
                    @if($lr->status === 'approved')
                      <span class="badge badge-success">Disetujui</span>
                    @elseif($lr->status === 'rejected')
                      <span class="badge badge-danger">Ditolak</span>
                    @else
                      <div class="btn-group">
                        <form action="{{ route('supervisor.leave.approve', $lr->id) }}" method="POST" class="d-inline">
                          @csrf
                          <button type="submit" class="btn btn-xs btn-success">Setujui</button>
                        </form>
                        <form action="{{ route('supervisor.leave.reject', $lr->id) }}" method="POST" class="d-inline ml-1">
                          @csrf
                          <button type="submit" class="btn btn-xs btn-outline-danger">Tolak</button>
                        </form>
                      </div>
                    @endif
                  </div>
                </div>
              @empty
                <p class="text-muted text-center py-3">Belum ada data cuti staf.</p>
              @endforelse
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- 3. TAB ASET & PERAWATAN ALAT GYM -->
    <div class="tab-pane fade {{ $activeTab === 'equipment' ? 'show active' : '' }}" id="equipment-sec" role="tabpanel">
      <div class="row">
        <!-- List Alat Gym -->
        <div class="col-lg-7 mb-4">
          <div class="card-custom p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div>
                <h5 class="font-weight-bold text-dark mb-1">Daftar Peralatan Gym</h5>
                <p class="text-muted small mb-0">Pantau kondisi fisik mesin beban, alat kardio, dan perlengkapan.</p>
              </div>
              <button type="button" class="btn btn-sm btn-primary font-weight-bold" data-toggle="modal" data-target="#addEquipmentModal">
                + Tambah Alat Baru
              </button>
            </div>

            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
                  <tr>
                    <th>Nama Alat</th>
                    <th>Kategori</th>
                    <th>Status</th>
                    <th>Jadwal Servis</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($equipments as $eq)
                    <tr>
                      <td>
                        <strong class="text-dark">{{ $eq->name }}</strong><br>
                        <small class="text-muted">{{ $eq->brand ?? 'Tanpa Merk' }}</small>
                      </td>
                      <td><span class="badge badge-secondary">{{ ucfirst($eq->category) }}</span></td>
                      <td>
                        @if($eq->status === 'berfungsi')
                          <span class="badge badge-success">Berfungsi</span>
                        @elseif($eq->status === 'perlu_perbaikan')
                          <span class="badge badge-warning text-dark">Perlu Servis</span>
                        @else
                          <span class="badge badge-danger">Rusak Berat</span>
                        @endif
                      </td>
                      <td>{{ $eq->next_service_date ? $eq->next_service_date->format('d M Y') : '-' }}</td>
                      <td>
                        <button type="button" class="btn btn-xs btn-outline-primary btn-edit-eq"
                          data-id="{{ $eq->id }}"
                          data-name="{{ $eq->name }}"
                          data-category="{{ $eq->category }}"
                          data-brand="{{ $eq->brand }}"
                          data-status="{{ $eq->status }}"
                          data-purchase_date="{{ $eq->purchase_date ? $eq->purchase_date->format('Y-m-d') : '' }}"
                          data-next_service_date="{{ $eq->next_service_date ? $eq->next_service_date->format('Y-m-d') : '' }}"
                          data-notes="{{ $eq->notes }}">Ubah</button>
                        <form action="{{ route('supervisor.equipment.destroy', $eq->id) }}" method="POST" class="d-inline" data-confirm="Hapus alat ini dari inventaris?">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-xs btn-outline-danger">Hapus</button>
                        </form>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="5" class="text-center py-4 text-muted">Belum ada data inventaris alat gym.</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Pencatatan Servis Alat -->
        <div class="col-lg-5 mb-4">
          <div class="card-custom p-4">
            <h5 class="font-weight-bold text-dark mb-1">Pencatatan Servis Alat</h5>
            <p class="text-muted small mb-3">Catat biaya perbaikan dan perbarui status kondisi alat.</p>

            <form action="{{ route('supervisor.maintenance.store') }}" method="POST" class="p-3 bg-light rounded border mb-4">
              @csrf
              <div class="form-group mb-2">
                <label class="font-weight-bold mb-1" style="font-size:12px;">Pilih Alat Gym *</label>
                <select name="gym_equipment_id" class="form-control form-control-sm" required>
                  <option value="">-- Pilih Alat --</option>
                  @foreach($equipments as $eq)
                    <option value="{{ $eq->id }}">{{ $eq->name }} ({{ $eq->brand }}) - Status: {{ $eq->status }}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group mb-2">
                <label class="font-weight-bold mb-1" style="font-size:12px;">Tindakan Servis *</label>
                <input type="text" name="action" class="form-control form-control-sm" placeholder="Contoh: Ganti kabel sling, pelumasan bearing" required>
              </div>
              <div class="form-group mb-2">
                <label class="font-weight-bold mb-1" style="font-size:12px;">Deskripsi / Vendor Teknisi</label>
                <textarea name="description" class="form-control form-control-sm" rows="2" placeholder="Nama teknisi atau rincian sparepart..."></textarea>
              </div>
              <div class="row">
                <div class="col-6 form-group mb-2">
                  <label class="font-weight-bold mb-1" style="font-size:12px;">Biaya Servis (Rp) *</label>
                  <input type="number" name="cost" class="form-control form-control-sm" value="0" required>
                </div>
                <div class="col-6 form-group mb-2">
                  <label class="font-weight-bold mb-1" style="font-size:12px;">Tanggal Servis *</label>
                  <input type="date" name="serviced_at" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
                </div>
              </div>
              <div class="form-group mb-3">
                <label class="font-weight-bold mb-1" style="font-size:12px;">Jadwal Servis Berikutnya</label>
                <input type="date" name="next_service_date" class="form-control form-control-sm">
              </div>
              <button type="submit" class="btn btn-sm btn-primary btn-block font-weight-bold">Simpan & Reset Status Alat</button>
            </form>

            <h6 class="font-weight-bold text-dark mb-2">Riwayat Pemeliharaan Terbaru</h6>
            <div style="max-height: 250px; overflow-y: auto;">
              @forelse($maintenanceLogs as $ml)
                <div class="p-2 border-bottom bg-white">
                  <div class="d-flex justify-content-between">
                    <strong>{{ $ml->equipment->name ?? 'Alat Dihapus' }}</strong>
                    <span class="text-success font-weight-bold">Rp {{ number_format($ml->cost, 0, ',', '.') }}</span>
                  </div>
                  <small class="text-muted d-block">{{ $ml->action }} &bull; {{ $ml->serviced_at->format('d M Y') }}</small>
                  @if($ml->description)
                    <small class="text-dark">{{ $ml->description }}</small>
                  @endif
                </div>
              @empty
                <p class="text-muted text-center py-3">Belum ada riwayat servis.</p>
              @endforelse
            </div>
          </div>
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
            <h5 class="font-weight-bold text-dark mb-1">Daftar Keluhan Member</h5>
            <p class="text-muted small mb-0">Tindak lanjut keluhan fasilitas, kebersihan, dan pelayanan staf.</p>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="bg-light text-muted" style="font-size: 11.5px; text-transform: uppercase;">
              <tr>
                <th>ID</th>
                <th>Tanggal</th>
                <th>Member</th>
                <th>Judul Keluhan</th>
                <th>Deskripsi Lengkap</th>
                <th>Status</th>
                <th>Solusi / Resolusi</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($complaints as $c)
                <tr>
                  <td>#{{ $c->id }}</td>
                  <td>{{ $c->created_at->format('d M Y') }}</td>
                  <td class="font-weight-bold text-dark">{{ $c->member->name ?? 'Anonim' }}</td>
                  <td class="font-weight-bold">{{ $c->title }}</td>
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
                  <td>
                    <button type="button" class="btn btn-xs btn-primary btn-edit-complaint"
                      data-id="{{ $c->id }}"
                      data-title="{{ $c->title }}"
                      data-status="{{ $c->status }}"
                      data-resolution="{{ $c->resolution }}">Ubah Status</button>
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
          <a href="{{ route('receptionist.lockers.index') }}" class="btn btn-sm btn-primary font-weight-bold">
            <i class="icon-settings mr-1"></i> Buka Pengaturan Loker Lengkap
          </a>
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
    <form action="{{ route('supervisor.equipment.store') }}" method="POST" class="modal-content shadow border-0">
      @csrf
      <div class="modal-header bg-light">
        <h5 class="modal-title font-weight-bold text-dark mb-0">Tambah Inventaris Alat Gym</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body p-4">
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark small mb-1">Nama Alat *</label>
          <input type="text" name="name" class="form-control" placeholder="Contoh: Treadmill Pro, Barbell Olympic" required>
        </div>
        <div class="row">
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Kategori *</label>
            <select name="category" class="form-control" required>
              <option value="kardio">Kardio</option>
              <option value="beban">Beban / Machine</option>
              <option value="free_weight">Free Weight</option>
              <option value="aksesoris">Aksesoris & Matras</option>
            </select>
          </div>
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Merk / Brand</label>
            <input type="text" name="brand" class="form-control" placeholder="Contoh: LifeFitness, Matrix">
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Status Awal *</label>
            <select name="status" class="form-control" required>
              <option value="berfungsi">Berfungsi Normal</option>
              <option value="perlu_perbaikan">Perlu Servis</option>
              <option value="rusak_berat">Rusak Berat</option>
            </select>
          </div>
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Tanggal Beli</label>
            <input type="date" name="purchase_date" class="form-control">
          </div>
        </div>
        <div class="form-group mb-0">
          <label class="font-weight-bold text-dark small mb-1">Jadwal Servis Berikutnya</label>
          <input type="date" name="next_service_date" class="form-control">
        </div>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary font-weight-bold">Simpan Alat</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Ubah Alat Gym -->
<div class="modal fade" id="editEquipmentModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <form id="editEquipmentForm" method="POST" class="modal-content shadow border-0">
      @csrf
      @method('PUT')
      <div class="modal-header bg-light">
        <h5 class="modal-title font-weight-bold text-dark mb-0">Ubah Inventaris Alat Gym</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body p-4">
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark small mb-1">Nama Alat *</label>
          <input type="text" name="name" id="editEqName" class="form-control" required>
        </div>
        <div class="row">
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Kategori *</label>
            <select name="category" id="editEqCategory" class="form-control" required>
              <option value="kardio">Kardio</option>
              <option value="beban">Beban / Machine</option>
              <option value="free_weight">Free Weight</option>
              <option value="aksesoris">Aksesoris & Matras</option>
            </select>
          </div>
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Merk / Brand</label>
            <input type="text" name="brand" id="editEqBrand" class="form-control">
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Status Alat *</label>
            <select name="status" id="editEqStatus" class="form-control" required>
              <option value="berfungsi">Berfungsi Normal</option>
              <option value="perlu_perbaikan">Perlu Servis</option>
              <option value="rusak_berat">Rusak Berat</option>
            </select>
          </div>
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Tanggal Beli</label>
            <input type="date" name="purchase_date" id="editEqPurchaseDate" class="form-control">
          </div>
        </div>
        <div class="form-group mb-0">
          <label class="font-weight-bold text-dark small mb-1">Jadwal Servis Berikutnya</label>
          <input type="date" name="next_service_date" id="editEqNextService" class="form-control">
        </div>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary font-weight-bold">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Tambah Shift Staf -->
<div class="modal fade" id="addShiftModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <form action="{{ route('supervisor.shifts.store') }}" method="POST" class="modal-content shadow border-0">
      @csrf
      <div class="modal-header bg-light">
        <h5 class="modal-title font-weight-bold text-dark mb-0">Buat Jadwal Shift Kerja Staf</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body p-4">
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark small mb-1">Pilih Staf Operasional *</label>
          <select name="user_id" class="form-control" required>
            <option value="">-- Pilih Staf --</option>
            @foreach($shiftStaffUsers as $su)
              <option value="{{ $su->id }}">{{ $su->name }} ({{ ucfirst($su->role) }})</option>
            @endforeach
          </select>
        </div>
        <div class="row">
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Tanggal Shift *</label>
            <input type="date" name="shift_date" class="form-control" value="{{ date('Y-m-d') }}" required>
          </div>
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Nama Shift *</label>
            <select name="shift_name" class="form-control" required>
              <option value="Pagi">Shift Pagi</option>
              <option value="Siang">Shift Siang</option>
              <option value="Malam">Shift Malam</option>
              <option value="Full Day">Full Day</option>
            </select>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Jam Masuk *</label>
            <input type="time" name="start_time" class="form-control" value="06:00" required>
          </div>
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Jam Pulang *</label>
            <input type="time" name="end_time" class="form-control" value="14:00" required>
          </div>
        </div>
        <div class="form-group mb-0">
          <label class="font-weight-bold text-dark small mb-1">Catatan Khusus</label>
          <input type="text" name="notes" class="form-control" placeholder="Contoh: Bertugas di frontdesk utama">
        </div>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary font-weight-bold">Simpan Jadwal Shift</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Ubah Shift Staf -->
<div class="modal fade" id="editShiftModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <form id="editShiftForm" method="POST" class="modal-content shadow border-0">
      @csrf
      @method('PUT')
      <div class="modal-header bg-light">
        <h5 class="modal-title font-weight-bold text-dark mb-0">Ubah Plotting Shift</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body p-4">
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark small mb-1">Pilih Staf Operasional *</label>
          <select name="user_id" id="editShiftUserId" class="form-control" required>
            @foreach($shiftStaffUsers as $su)
              <option value="{{ $su->id }}">{{ $su->name }} ({{ ucfirst($su->role) }})</option>
            @endforeach
          </select>
        </div>
        <div class="row">
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Tanggal Shift *</label>
            <input type="date" name="shift_date" id="editShiftDate" class="form-control" required>
          </div>
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Nama Shift *</label>
            <select name="shift_name" id="editShiftName" class="form-control" required>
              <option value="Pagi">Shift Pagi</option>
              <option value="Siang">Shift Siang</option>
              <option value="Malam">Shift Malam</option>
              <option value="Full Day">Full Day</option>
            </select>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Jam Masuk *</label>
            <input type="time" name="start_time" id="editShiftStart" class="form-control" required>
          </div>
          <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Jam Pulang *</label>
            <input type="time" name="end_time" id="editShiftEnd" class="form-control" required>
          </div>
        </div>
        <div class="form-group mb-0">
          <label class="font-weight-bold text-dark small mb-1">Catatan Khusus</label>
          <input type="text" name="notes" id="editShiftNotes" class="form-control">
        </div>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary font-weight-bold">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Ubah Tiket Komplain -->
<div class="modal fade" id="editComplaintModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <form id="editComplaintForm" method="POST" class="modal-content shadow border-0">
      @csrf
      @method('PUT')
      <div class="modal-header bg-light">
        <h5 class="modal-title font-weight-bold text-dark mb-0">Tindak Lanjuti Komplain Member</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body p-4">
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark small mb-1">Judul Keluhan</label>
          <input type="text" id="complaintTitleDisplay" class="form-control bg-light" readonly disabled>
        </div>
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark small mb-1">Status Penanganan *</label>
          <select name="status" id="editComplaintStatus" class="form-control" required>
            <option value="open">Menunggu Penanganan</option>
            <option value="in_progress">Sedang Diproses</option>
            <option value="resolved">Terselesaikan</option>
            <option value="closed">Selesai / Ditutup</option>
          </select>
        </div>
        <div class="form-group mb-0">
          <label class="font-weight-bold text-dark small mb-1">Catatan Solusi / Tindakan Supervisor</label>
          <textarea name="resolution" id="editComplaintResolution" class="form-control" rows="3" placeholder="Jelaskan tindakan yang telah dilakukan untuk menyelesaikan keluhan ini..."></textarea>
        </div>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary font-weight-bold">Simpan Resolusi</button>
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
      $('#complaintTitleDisplay').val($(this).data('title'));
      $('#editComplaintStatus').val($(this).data('status'));
      $('#editComplaintResolution').val($(this).data('resolution'));
      $('#editComplaintModal').modal('show');
    });
  });
</script>
@endpush
