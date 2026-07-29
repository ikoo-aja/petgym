@php
  $activeTab = request()->query('tab', 'class');
  $tabTitles = [
      'class' => ['title' => 'Master Kelas', 'desc' => 'Perencanaan kelas dasar, durasi, hari, dan jam mulai.'],
      'maintenance' => ['title' => 'Aset & Maintenance Alat Gym', 'desc' => 'Inventarisasi mesin, beban, alat gym, dan riwayat pemeliharaan berkala.'],
      'shift' => ['title' => 'Shift & Cuti Karyawan', 'desc' => 'Pengaturan jadwal shift kerja resepsionis/admin dan approval cuti.'],
      'approval' => ['title' => 'Sistem Otorisasi Kasir & Void', 'desc' => 'Pemantauan otorisasi void/refund transaksi kasir.'],
      'promo' => ['title' => 'Manajemen Promo & Harga', 'desc' => 'Pembuatan kode voucher dan program diskon untuk loket pendaftaran.'],
      'performance' => ['title' => 'Pantauan Kinerja Staf & Target', 'desc' => 'Evaluasi penjualan kasir resepsionis dan jam terbang personal trainer.'],
      'report' => ['title' => 'Laporan Operasional Taktis', 'desc' => 'Verifikasi rekap kas fisik harian dan analisis tren kepadatan kunjungan member.'],
      'stock' => ['title' => 'Manajemen Stok & Inventaris Ritel', 'desc' => 'Alert restock barang ritel dengan sisa stok kritis.'],
      'complaints' => ['title' => 'Manajemen Retensi Member (Complaints)', 'desc' => 'Penyelesaian masukan dan tiket keluhan member gym.'],
      'vendors' => ['title' => 'Database Vendor & Pihak Ketiga', 'desc' => 'Pusat data kontak teknisi, agen air, jasa kebersihan, dan supplier.'],
  ];
  $currentTabInfo = $tabTitles[$activeTab] ?? $tabTitles['class'];
@endphp

@extends('layouts.admin')

@section('title', 'Operasional Manager &mdash; PetGym')
@section('page_title', 'Operasional Manager: ' . $currentTabInfo['title'])
@section('page_subtitle', $currentTabInfo['desc'])

@section('content')
<div class="card-custom">
  <div class="tab-content" id="managerTabContent">
    
    <!-- 1. PERENCANAAN MASTER KELAS -->
    <div class="tab-pane fade {{ $activeTab === 'class' ? 'show active' : '' }}" id="class-sec" role="tabpanel">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="font-weight-bold text-dark">Rencana Master Kelas Dasar</h5>
        <button type="button" class="btn btn-primary font-weight-bold" data-toggle="modal" data-target="#addMasterClassModal">
          + Rencanakan Kelas Baru
        </button>
      </div>
      <small class="text-muted d-block mb-3">Tanggung jawab Manager adalah mendefinisikan kelas dasar, hari, jam mulai, dan durasi. Admin akan melengkapi alokasi instruktur (trainer), ruangan, dan kuota harian peserta.</small>

      <div class="table-responsive">
        <table class="table table-hover table-bordered align-middle">
          <thead class="bg-light text-muted">
            <tr>
              <th>Nama Kelas</th>
              <th>Hari</th>
              <th>Jam Mulai</th>
              <th>Durasi Dasar</th>
              <th>Ruangan (Oleh Admin)</th>
              <th>Trainer (Oleh Admin)</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($masterClasses as $mc)
              <tr>
                <td class="font-weight-bold text-dark">{{ $mc->name }}</td>
                <td><span class="badge badge-info px-2 py-1">{{ $mc->day }}</span></td>
                <td>{{ substr($mc->start_time, 0, 5) }} WIB</td>
                <td>{{ $mc->duration_minutes ?? 60 }} Menit</td>
                <td><span class="text-muted font-italic">{{ $mc->room ?? 'Belum dialokasikan' }}</span></td>
                <td>{{ $mc->trainer ? $mc->trainer->name : 'N/A' }}</td>
                <td>
                  <button type="button" class="btn btn-sm btn-outline-primary btn-edit-mclass"
                    data-id="{{ $mc->id }}"
                    data-name="{{ $mc->name }}"
                    data-day="{{ $mc->day }}"
                    data-start_time="{{ substr($mc->start_time, 0, 5) }}"
                    data-duration_minutes="{{ $mc->duration_minutes ?? 60 }}">Edit</button>
                  <form action="{{ route('manager.classes.destroy', $mc->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus master kelas ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center py-4 text-muted">Belum ada master kelas yang direncanakan.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <!-- 2. MANAJEMEN ALAT & MAINTENANCE -->
    <div class="tab-pane fade" id="maintenance-sec" role="tabpanel">
      <div class="row">
        <!-- List Alat Gym -->
        <div class="col-md-7">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="font-weight-bold text-dark mb-0">Daftar Inventaris Alat Gym</h6>
            <button type="button" class="btn btn-sm btn-success font-weight-bold" data-toggle="modal" data-target="#addEquipmentModal">+ Tambah Alat</button>
          </div>
          <div class="table-responsive">
            <table class="table table-hover table-bordered">
              <thead class="bg-light">
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
                      <div class="font-weight-bold text-dark">{{ $eq->name }}</div>
                      <small class="text-muted">{{ $eq->brand ?? '-' }}</small>
                    </td>
                    <td>{{ $eq->category }}</td>
                    <td>
                      @if($eq->status === 'berfungsi')
                        <span class="badge badge-success">Berfungsi</span>
                      @elseif($eq->status === 'perlu_servis')
                        <span class="badge badge-warning">Perlu Servis</span>
                      @else
                        <span class="badge badge-danger">Rusak</span>
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
                        data-notes="{{ $eq->notes }}">Edit</button>
                      <form action="{{ route('manager.equipment.destroy', $eq->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus alat ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-xs btn-outline-danger">Hapus</button>
                      </form>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="5" class="text-center text-muted">Belum ada inventaris alat terdaftar.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>

        <!-- Servis Log -->
        <div class="col-md-5">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="font-weight-bold text-dark mb-0">Catat Pemeliharaan / Servis Alat</h6>
          </div>
          <form action="{{ route('manager.maintenance.store') }}" method="POST" class="p-3 bg-light rounded border mb-3">
            @csrf
            <div class="form-group mb-2">
              <label class="font-weight-bold mb-1" style="font-size:12px;">Pilih Alat *</label>
              <select name="gym_equipment_id" class="form-control form-control-sm" required>
                @foreach($equipments as $eq)
                  <option value="{{ $eq->id }}">{{ $eq->name }} ({{ ucfirst($eq->status) }})</option>
                @endforeach
              </select>
            </div>
            <div class="form-group mb-2">
              <label class="font-weight-bold mb-1" style="font-size:12px;">Tindakan Servis *</label>
              <input type="text" name="action" class="form-control form-control-sm" placeholder="Contoh: Kalibrasi Motor, Ganti Vanbelt" required>
            </div>
            <div class="form-group mb-2">
              <label class="font-weight-bold mb-1" style="font-size:12px;">Keterangan & Sparepart</label>
              <textarea name="description" class="form-control form-control-sm" rows="2"></textarea>
            </div>
            <div class="row">
              <div class="col-6 form-group mb-2">
                <label class="font-weight-bold mb-1" style="font-size:12px;">Biaya (Rp) *</label>
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

          <h6 class="font-weight-bold text-dark mt-4 mb-2">Riwayat Pemeliharaan Terbaru</h6>
          <div style="max-height: 250px; overflow-y: auto;">
            @forelse($maintenanceLogs as $ml)
              <div class="p-2 border-bottom bg-white">
                <div class="d-flex justify-content-between">
                  <strong>{{ $ml->equipment->name }}</strong>
                  <span class="text-success font-weight-bold">Rp {{ number_format($ml->cost, 0, ',', '.') }}</span>
                </div>
                <small class="text-muted d-block">{{ $ml->action }} - {{ $ml->serviced_at->format('d M Y') }}</small>
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

    <!-- 3. JADWAL & SHIFT KARYAWAN -->
    <div class="tab-pane fade" id="shift-sec" role="tabpanel">
      <div class="row">
        <!-- Plotting Shift Staf -->
        <div class="col-md-7">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="font-weight-bold text-dark mb-0">Plotting Shift Staf (Resepsionis & Admin)</h6>
            <button type="button" class="btn btn-sm btn-primary font-weight-bold" data-toggle="modal" data-target="#addShiftModal">+ Tambah Shift</button>
          </div>
          <div class="table-responsive">
            <table class="table table-hover table-bordered">
              <thead class="bg-light">
                <tr>
                  <th>Tanggal</th>
                  <th>Staf</th>
                  <th>Shift</th>
                  <th>Jam Kerja</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                @forelse($staffShifts as $sh)
                  <tr>
                    <td>{{ $sh->shift_date->format('d M Y') }}</td>
                    <td class="font-weight-bold">{{ $sh->user->name }}</td>
                    <td><span class="badge badge-info px-2">{{ $sh->shift_name }}</span></td>
                    <td style="font-size:12.5px;">{{ substr($sh->start_time, 0, 5) }} - {{ substr($sh->end_time, 0, 5) }}</td>
                    <td>
                      <button type="button" class="btn btn-xs btn-outline-primary btn-edit-shift"
                        data-id="{{ $sh->id }}"
                        data-user_id="{{ $sh->user_id }}"
                        data-shift_date="{{ $sh->shift_date->format('Y-m-d') }}"
                        data-shift_name="{{ $sh->shift_name }}"
                        data-start_time="{{ substr($sh->start_time, 0, 5) }}"
                        data-end_time="{{ substr($sh->end_time, 0, 5) }}"
                        data-notes="{{ $sh->notes }}">Edit</button>
                      <form action="{{ route('manager.shifts.destroy', $sh->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus shift ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-xs btn-outline-danger">Hapus</button>
                      </form>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="5" class="text-center text-muted">Belum ada plotting shift kerja.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>

        <!-- Approval Cuti/Izin -->
        <div class="col-md-5">
          <h6 class="font-weight-bold text-dark mb-3">Pengajuan Cuti / Izin Karyawan</h6>
          <div style="max-height: 500px; overflow-y: auto;">
            @forelse($leaveRequests as $lr)
              <div class="p-3 border rounded bg-white mb-2 shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <strong class="text-dark">{{ $lr->user->name }}</strong>
                  @if($lr->status === 'pending')
                    <span class="badge badge-warning">Pending</span>
                  @elseif($lr->status === 'approved')
                    <span class="badge badge-success">Approved</span>
                  @else
                    <span class="badge badge-danger">Rejected</span>
                  @endif
                </div>
                <div class="text-muted" style="font-size:12.5px;">Tanggal: {{ $lr->start_date->format('d M') }} s/d {{ $lr->end_date->format('d M Y') }}</div>
                <p class="my-2 bg-light p-2 rounded" style="font-size: 13px;">"{{ $lr->reason }}"</p>
                
                @if($lr->status === 'pending')
                  <div class="d-flex justify-content-end">
                    <form action="{{ route('manager.leave.reject', $lr->id) }}" method="POST" class="mr-1">
                      @csrf
                      <button type="submit" class="btn btn-xs btn-danger font-weight-bold px-3">Tolak</button>
                    </form>
                    <form action="{{ route('manager.leave.approve', $lr->id) }}" method="POST">
                      @csrf
                      <button type="submit" class="btn btn-xs btn-success font-weight-bold px-3">Setujui</button>
                    </form>
                  </div>
                @else
                  <small class="text-muted d-block text-right">Diproses oleh: {{ $lr->approver ? $lr->approver->name : 'Manager' }}</small>
                @endif
              </div>
            @empty
              <p class="text-muted text-center py-4 bg-light rounded">Tidak ada pengajuan cuti masuk.</p>
            @endforelse
          </div>
        </div>
      </div>
    </div>

    <!-- 4. SISTEM OTORISASI & OTORITAS TRANSAKSI (VOID / KASIR) -->
    <div class="tab-pane fade" id="approval-sec" role="tabpanel">
      <h5 class="font-weight-bold text-dark mb-2">Otorisasi & Persetujuan Void (Pembatalan Transaksi)</h5>
      <p class="text-muted" style="font-size:13.5px;">Gunakan panel ini untuk menyetujui atau menolak permohonan pembatalan transaksi kasir (void) yang diajukan oleh Resepsionis.</p>

      <div class="table-responsive">
        <table class="table table-bordered table-striped">
          <thead class="bg-light">
            <tr>
              <th>No. Invoice</th>
              <th>Tanggal Pengajuan</th>
              <th>Kasir</th>
              <th>Member / Pelanggan</th>
              <th>Total Transaksi</th>
              <th>Alasan Void</th>
              <th>Status Otorisasi</th>
              <th class="text-center">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($voidTransactions as $vt)
              <tr>
                <td class="font-weight-bold text-dark">{{ $vt->invoice_number }}</td>
                <td style="font-size:12px;">{{ $vt->updated_at->format('d M Y H:i') }}</td>
                <td>{{ $vt->user ? $vt->user->name : 'System' }}</td>
                <td>{{ $vt->member ? $vt->member->name : 'Pelanggan Umum' }}</td>
                <td class="font-weight-bold text-dark">Rp {{ number_format($vt->total_amount, 0, ',', '.') }}</td>
                <td style="font-size:12.5px;" class="text-muted">"{{ $vt->void_reason ?? '-' }}"</td>
                <td>
                  @if($vt->void_status === 'pending')
                    <span class="badge badge-warning text-dark font-weight-bold">Menunggu Persetujuan</span>
                  @elseif($vt->void_status === 'approved')
                    <span class="badge badge-success font-weight-bold">Disetujui (Voided)</span>
                  @else
                    <span class="badge badge-danger font-weight-bold">Ditolak</span>
                  @endif
                </td>
                <td class="text-center">
                  @if($vt->void_status === 'pending')
                    <div class="d-flex justify-content-center">
                      <form action="{{ route('manager.leave.reject', $vt->id) }}" method="POST" class="mr-1" onsubmit="return confirm('Tolak permohonan void ini?')">
                        @csrf
                        <!-- We will define manager.void.reject route soon -->
                      </form>
                      <form action="/manager/void/{{ $vt->id }}/approve" method="POST" class="d-inline mr-1" onsubmit="return confirm('Setujui pembatalan transaksi ini?')">
                        @csrf
                        <button type="submit" class="btn btn-xs btn-success font-weight-bold px-2 py-1">Setujui</button>
                      </form>
                      <form action="/manager/void/{{ $vt->id }}/reject" method="POST" class="d-inline" onsubmit="return confirm('Tolak pembatalan transaksi ini?')">
                        @csrf
                        <button type="submit" class="btn btn-xs btn-danger font-weight-bold px-2 py-1">Tolak</button>
                      </form>
                    </div>
                  @else
                    <span class="text-muted font-italic" style="font-size:12px;">Selesai</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="text-center py-4 text-muted">Belum ada pengajuan pembatalan void transaksi dari kasir.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <!-- 5. MANAJEMEN PROMO & HARGA -->
    <div class="tab-pane fade" id="promo-sec" role="tabpanel">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="font-weight-bold text-dark mb-0">Manajemen Kode Promo & Voucher Diskon</h5>
        <button type="button" class="btn btn-primary font-weight-bold" data-toggle="modal" data-target="#addPromoModal">+ Tambah Voucher Promo</button>
      </div>

      <div class="table-responsive">
        <table class="table table-hover table-bordered">
          <thead class="bg-light text-muted">
            <tr>
              <th>Kode Promo</th>
              <th>Deskripsi / Syarat</th>
              <th>Tipe Diskon</th>
              <th>Nilai Potongan</th>
              <th>Masa Berlaku</th>
              <th>Pemakaian</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($promoCodes as $promo)
              <tr>
                <td class="font-weight-bold text-success" style="font-size: 15px; letter-spacing: 0.5px;">{{ $promo->code }}</td>
                <td>
                  <div>{{ $promo->description ?? '-' }}</div>
                  <small class="text-muted">Min. Pembelian: Rp {{ number_format($promo->min_purchase, 0, ',', '.') }}</small>
                </td>
                <td>{{ $promo->discount_type === 'percentage' ? 'Persentase (%)' : 'Nominal Tetap (Rp)' }}</td>
                <td class="font-weight-bold">
                  {{ $promo->discount_type === 'percentage' ? $promo->discount_value . '%' : 'Rp ' . number_format($promo->discount_value, 0, ',', '.') }}
                </td>
                <td style="font-size:12px;">{{ $promo->valid_from->format('d M Y') }} s/d {{ $promo->valid_until->format('d M Y') }}</td>
                <td>{{ $promo->used_count }} / {{ $promo->max_uses }} kali</td>
                <td>
                  @if($promo->isValid())
                    <span class="badge badge-success">Aktif / Valid</span>
                  @else
                    <span class="badge badge-secondary">Tidak Valid / Expired</span>
                  @endif
                </td>
                <td>
                  <button type="button" class="btn btn-sm btn-outline-primary btn-edit-promo"
                    data-id="{{ $promo->id }}"
                    data-code="{{ $promo->code }}"
                    data-description="{{ $promo->description }}"
                    data-discount_type="{{ $promo->discount_type }}"
                    data-discount_value="{{ $promo->discount_value }}"
                    data-min_purchase="{{ $promo->min_purchase }}"
                    data-max_uses="{{ $promo->max_uses }}"
                    data-valid_from="{{ $promo->valid_from->format('Y-m-d') }}"
                    data-valid_until="{{ $promo->valid_until->format('Y-m-d') }}"
                    data-is_active="{{ $promo->is_active ? 1 : 0 }}">Edit</button>
                  <form action="{{ route('manager.promo.destroy', $promo->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus voucher promo ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="text-center py-4 text-muted">Belum ada promo terdaftar.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <!-- 6. PANTAUAN KINERJA STAF & TARGET -->
    <div class="tab-pane fade" id="performance-sec" role="tabpanel">
      <div class="row">
        <!-- Resepsionis Leaderboard -->
        <div class="col-md-6 mb-3">
          <div class="p-3 bg-light rounded border">
            <h6 class="font-weight-bold text-dark mb-3"><span class="icon-people"></span> Evaluasi Penjualan Resepsionis (Bulan Ini)</h6>
            <div class="table-responsive">
              <table class="table table-hover table-striped">
                <thead class="thead-dark">
                  <tr>
                    <th>Resepsionis</th>
                    <th class="text-center">Trx</th>
                    <th class="text-right">Total Penjualan</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($receptionistPerformance as $rp)
                    <tr>
                      <td class="font-weight-bold text-dark">{{ $rp->user ? $rp->user->name : 'Sistem/Unknown' }}</td>
                      <td class="text-center">{{ $rp->total_transactions }}</td>
                      <td class="text-right font-weight-bold text-success">Rp {{ number_format($rp->total_sales, 0, ',', '.') }}</td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="3" class="text-center text-muted py-3">Belum ada transaksi bulan ini.</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- PT Leaderboard -->
        <div class="col-md-6 mb-3">
          <div class="p-3 bg-light rounded border">
            <h6 class="font-weight-bold text-dark mb-3"><span class="icon-calendar"></span> Jam Terbang & Jumlah Kelas Trainer</h6>
            <div class="table-responsive">
              <table class="table table-hover table-striped">
                <thead class="thead-dark">
                  <tr>
                    <th>Personal Trainer</th>
                    <th class="text-center">Kelas Mengajar</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($trainerPerformance as $tp)
                    <tr>
                      <td class="font-weight-bold text-dark">{{ $tp->trainer ? $tp->trainer->name : 'N/A' }}</td>
                      <td class="text-center font-weight-bold text-primary" style="font-size:15px;">{{ $tp->total_classes }} Kelas</td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="2" class="text-center text-muted py-3">Belum ada penugasan kelas trainer.</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- 7. LAPORAN OPERASIONAL TAKTIS (REKAP KAS & TREN KEHADIRAN) -->
    <div class="tab-pane fade" id="report-sec" role="tabpanel">
      <div class="row">
        <!-- Rekap Kas Fisik Harian -->
        <div class="col-md-6 mb-4">
          <div class="p-3 bg-light rounded border h-100">
            <h6 class="font-weight-bold text-dark mb-3">Verifikasi Rekap Kas & Laci Kasir Hari Ini</h6>
            <div class="alert alert-info py-2" style="font-size:12.5px;">
              <strong>Info:</strong> Verifikasi kesesuaian angka laci kasir di sistem dengan setoran fisik resepsionis.
            </div>
            
            @php
              $totalSystemCash = $dailyCashRecap->where('payment_method', 'cash')->sum('total_amount');
              $totalSystemNonCash = $dailyCashRecap->where('payment_method', '!=', 'cash')->sum('total_amount');
            @endphp

            <div class="p-3 bg-white border rounded">
              <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Total Transaksi Hari Ini:</span>
                <span class="font-weight-bold text-dark">{{ count($dailyCashRecap) }} Trx</span>
              </div>
              <div class="d-flex justify-content-between mb-2 border-top pt-2">
                <span class="text-muted">Setoran Tunai (Sistem):</span>
                <span class="font-weight-bold text-success">Rp {{ number_format($totalSystemCash, 0, ',', '.') }}</span>
              </div>
              <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Transaksi Non-Tunai (Sistem):</span>
                <span class="font-weight-bold text-primary">Rp {{ number_format($totalSystemNonCash, 0, ',', '.') }}</span>
              </div>
              <div class="d-flex justify-content-between mb-0 border-top pt-2" style="font-size:16px;">
                <span class="font-weight-bold text-dark">Total Omset Harian:</span>
                <span class="font-weight-bold text-dark">Rp {{ number_format($totalSystemCash + $totalSystemNonCash, 0, ',', '.') }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Tren Kehadiran Member -->
        <div class="col-md-6 mb-4">
          <div class="p-3 bg-light rounded border h-100">
            <h6 class="font-weight-bold text-dark mb-3">Tren Jam Sibuk Kunjungan Member</h6>
            <p class="text-muted" style="font-size:12.5px;">Statistik jam sibuk kunjungan member untuk efisiensi pendingin AC dan plotting tambahan staf jaga resepsionis.</p>
            <div class="p-3 bg-white border rounded text-center">
              <h5 class="text-dark font-weight-bold mb-2">Estimasi Jam Terpadat</h5>
              <div class="display-4 font-weight-bold text-warning mb-2" style="font-size: 28px;">17:00 - 20:00 WIB</div>
              <small class="text-muted">Kepadatan meningkat 65% pada sore/malam hari selepas jam kantor.</small>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- 8. MANAJEMEN STOK & INVENTARIS RITEL (STOCK OPNAME) -->
    <div class="tab-pane fade" id="stock-sec" role="tabpanel">
      <h5 class="font-weight-bold text-dark mb-2">Sistem Peringatan Stok Menipis (Alert Stock Opname)</h5>
      <p class="text-muted" style="font-size:13.5px;">Peringatan otomatis untuk produk retail ritel (suplemen, minuman, merchandise) dengan sisa stok di bawah atau sama dengan 10 unit.</p>

      @if(count($lowStockProducts) > 0)
        <div class="alert alert-warning mb-3">
          <strong>⚠️ Peringatan Restock!</strong> Terdapat {{ count($lowStockProducts) }} barang retail yang persediaannya hampir habis. Harap segera restock ke supplier.
        </div>
      @else
        <div class="alert alert-success mb-3">
          <strong>✅ Stok Aman!</strong> Seluruh barang inventaris retail saat ini dalam kondisi stok aman (di atas 10 unit).
        </div>
      @endif

      <div class="table-responsive">
        <table class="table table-bordered table-striped">
          <thead class="thead-dark">
            <tr>
              <th>Nama Produk</th>
              <th>Kategori</th>
              <th>Harga Ritel</th>
              <th class="text-center">Sisa Stok</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse($lowStockProducts as $prod)
              <tr>
                <td class="font-weight-bold text-dark">{{ $prod->name }}</td>
                <td>{{ ucfirst($prod->category) }}</td>
                <td>Rp {{ number_format($prod->price, 0, ',', '.') }}</td>
                <td class="text-center font-weight-bold text-danger" style="font-size:15px;">{{ $prod->stock }} unit</td>
                <td><span class="badge badge-danger">Segera Restock</span></td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center py-4 text-muted">Tidak ada barang inventaris ritel dengan stok menipis.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <!-- 9. RETENSI MEMBER (COMPLAINTS TICKETING) -->
    <div class="tab-pane fade" id="complaints-sec" role="tabpanel">
      <h5 class="font-weight-bold text-dark mb-3">Tiket Komplain & Masukan Member</h5>
      <div class="table-responsive">
        <table class="table table-bordered table-striped">
          <thead class="bg-light">
            <tr>
              <th>Tanggal Masuk</th>
              <th>Member</th>
              <th>Keluhan / Tiket</th>
              <th>Status Tiket</th>
              <th>Solusi / Resolusi</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($complaints as $comp)
              <tr>
                <td>{{ $comp->created_at->format('d M Y H:i') }}</td>
                <td>
                  <div class="font-weight-bold">{{ $comp->member ? $comp->member->name : 'N/A' }}</div>
                  <small class="text-muted">HP: {{ $comp->member ? $comp->member->phone : '-' }}</small>
                </td>
                <td>
                  <div class="font-weight-bold text-dark">{{ $comp->title }}</div>
                  <p class="mb-0 text-muted" style="font-size:12.5px;">{{ $comp->description }}</p>
                </td>
                <td>
                  @if($comp->status === 'open')
                    <span class="badge badge-danger">Open</span>
                  @elseif($comp->status === 'in_progress')
                    <span class="badge badge-warning">In Progress</span>
                  @elseif($comp->status === 'resolved')
                    <span class="badge badge-success">Resolved</span>
                  @else
                    <span class="badge badge-secondary">Closed</span>
                  @endif
                </td>
                <td style="font-size: 13px;">{{ $comp->resolution ?? 'Belum ada tanggapan.' }}</td>
                <td>
                  <button type="button" class="btn btn-sm btn-outline-primary btn-edit-complaint"
                    data-id="{{ $comp->id }}"
                    data-title="{{ $comp->title }}"
                    data-status="{{ $comp->status }}"
                    data-resolution="{{ $comp->resolution }}">Proses Tiket</button>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center py-4 text-muted">Belum ada keluhan atau masukan member terdaftar.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <!-- 10. DATABASE VENDOR & PIHAK KETIGA -->
    <div class="tab-pane fade" id="vendors-sec" role="tabpanel">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="font-weight-bold text-dark mb-0">Buku Kontak Vendor & Pihak Ketiga</h5>
        <button type="button" class="btn btn-primary font-weight-bold" data-toggle="modal" data-target="#addVendorModal">+ Tambah Kontak Vendor</button>
      </div>

      <div class="table-responsive">
        <table class="table table-hover table-bordered">
          <thead class="bg-light text-muted">
            <tr>
              <th>Nama Vendor / Kontak</th>
              <th>Kategori Layanan</th>
              <th>Kontak (HP/Email)</th>
              <th>Alamat</th>
              <th>Keterangan / Notes</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($vendors as $ven)
              <tr>
                <td class="font-weight-bold text-dark">{{ $ven->name }}</td>
                <td><span class="badge badge-secondary">{{ $ven->category }}</span></td>
                <td>
                  <div>{{ $ven->phone ?? '-' }}</div>
                  <small class="text-muted">{{ $ven->email ?? '-' }}</small>
                </td>
                <td style="font-size:12.5px;">{{ $ven->address ?? '-' }}</td>
                <td style="font-size:12.5px;">{{ $ven->notes ?? '-' }}</td>
                <td>
                  <button type="button" class="btn btn-sm btn-outline-primary btn-edit-vendor"
                    data-id="{{ $ven->id }}"
                    data-name="{{ $ven->name }}"
                    data-phone="{{ $ven->phone }}"
                    data-email="{{ $ven->email }}"
                    data-category="{{ $ven->category }}"
                    data-address="{{ $ven->address }}"
                    data-notes="{{ $ven->notes }}">Edit</button>
                  <form action="{{ route('manager.vendors.destroy', $ven->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus kontak vendor ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center py-4 text-muted">Belum ada kontak vendor tersimpan.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

  </div>
</div>

<!-- ==================== MODALS IN MANAGER FEATURES ==================== -->

<!-- Modal Tambah Master Class -->
<div class="modal fade" id="addMasterClassModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form action="{{ route('manager.classes.store') }}" method="POST" class="modal-content" style="border-radius: 12px;">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title font-weight-bold">Rencanakan Master Kelas Baru</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Nama Kelas *</label>
          <input type="text" name="name" class="form-control" placeholder="Contoh: Yoga Vinyasa, Zumba Blast, HIIT" required>
        </div>
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Hari *</label>
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
        <div class="row">
          <div class="col-md-6 form-group mb-0">
            <label class="font-weight-bold text-dark" style="font-size: 13px;">Jam Mulai *</label>
            <input type="time" name="start_time" class="form-control" value="08:00" required>
          </div>
          <div class="col-md-6 form-group mb-0">
            <label class="font-weight-bold text-dark" style="font-size: 13px;">Durasi (Menit) *</label>
            <input type="number" name="duration_minutes" class="form-control" value="60" required min="1">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary font-weight-bold">Rencanakan Kelas</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit Master Class -->
<div class="modal fade" id="editMasterClassModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="editMasterClassForm" action="" method="POST" class="modal-content" style="border-radius: 12px;">
      @csrf
      @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title font-weight-bold">Edit Rencana Master Kelas</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Nama Kelas *</label>
          <input type="text" name="name" id="editMClassName" class="form-control" required>
        </div>
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Hari *</label>
          <select name="day" id="editMClassDay" class="form-control" required>
            <option value="Senin">Senin</option>
            <option value="Selasa">Selasa</option>
            <option value="Rabu">Rabu</option>
            <option value="Kamis">Kamis</option>
            <option value="Jumat">Jumat</option>
            <option value="Sabtu">Sabtu</option>
            <option value="Minggu">Minggu</option>
          </select>
        </div>
        <div class="row">
          <div class="col-md-6 form-group mb-0">
            <label class="font-weight-bold text-dark" style="font-size: 13px;">Jam Mulai *</label>
            <input type="time" name="start_time" id="editMClassStartTime" class="form-control" required>
          </div>
          <div class="col-md-6 form-group mb-0">
            <label class="font-weight-bold text-dark" style="font-size: 13px;">Durasi (Menit) *</label>
            <input type="number" name="duration_minutes" id="editMClassDuration" class="form-control" required min="1">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary font-weight-bold">Update Rencana</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Tambah Alat Gym -->
<div class="modal fade" id="addEquipmentModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form action="{{ route('manager.equipment.store') }}" method="POST" class="modal-content" style="border-radius: 12px;">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title font-weight-bold">Tambah Inventaris Alat Gym</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Nama Alat *</label>
          <input type="text" name="name" class="form-control" placeholder="Contoh: Treadmill Lifesport A, Dumbbell Set 5kg" required>
        </div>
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Kategori *</label>
          <select name="category" class="form-control" required>
            <option value="Alat Berat">Alat Berat (Mesin/Beban)</option>
            <option value="Cardio">Alat Kardio</option>
            <option value="Aksesoris">Aksesoris & Matras</option>
          </select>
        </div>
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Merek / Brand</label>
          <input type="text" name="brand" class="form-control" placeholder="Contoh: Lifefitness, Kettler">
        </div>
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Status Awal *</label>
          <select name="status" class="form-control" required>
            <option value="berfungsi">Berfungsi Penuh</option>
            <option value="perlu_servis">Perlu Servis</option>
            <option value="rusak">Rusak</option>
          </select>
        </div>
        <div class="row">
          <div class="col-6 form-group mb-0">
            <label class="font-weight-bold text-dark" style="font-size: 13px;">Tanggal Beli</label>
            <input type="date" name="purchase_date" class="form-control">
          </div>
          <div class="col-6 form-group mb-0">
            <label class="font-weight-bold text-dark" style="font-size: 13px;">Rencana Servis</label>
            <input type="date" name="next_service_date" class="form-control">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-success font-weight-bold">Simpan Alat</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit Alat Gym -->
<div class="modal fade" id="editEquipmentModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="editEquipmentForm" action="" method="POST" class="modal-content" style="border-radius: 12px;">
      @csrf
      @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title font-weight-bold">Edit Inventaris Alat Gym</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Nama Alat *</label>
          <input type="text" name="name" id="editEqName" class="form-control" required>
        </div>
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Kategori *</label>
          <select name="category" id="editEqCategory" class="form-control" required>
            <option value="Alat Berat">Alat Berat (Mesin/Beban)</option>
            <option value="Cardio">Alat Kardio</option>
            <option value="Aksesoris">Aksesoris & Matras</option>
          </select>
        </div>
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Merek / Brand</label>
          <input type="text" name="brand" id="editEqBrand" class="form-control">
        </div>
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Status *</label>
          <select name="status" id="editEqStatus" class="form-control" required>
            <option value="berfungsi">Berfungsi Penuh</option>
            <option value="perlu_servis">Perlu Servis</option>
            <option value="rusak">Rusak</option>
          </select>
        </div>
        <div class="row">
          <div class="col-6 form-group mb-0">
            <label class="font-weight-bold text-dark" style="font-size: 13px;">Tanggal Beli</label>
            <input type="date" name="purchase_date" id="editEqPurchaseDate" class="form-control">
          </div>
          <div class="col-6 form-group mb-0">
            <label class="font-weight-bold text-dark" style="font-size: 13px;">Rencana Servis</label>
            <input type="date" name="next_service_date" id="editEqNextService" class="form-control">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary font-weight-bold">Update Alat</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Tambah Shift Staf -->
<div class="modal fade" id="addShiftModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form action="{{ route('manager.shifts.store') }}" method="POST" class="modal-content" style="border-radius: 12px;">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title font-weight-bold">Plotting Shift Staf</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Pilih Karyawan / Staf *</label>
          <select name="user_id" class="form-control" required>
            @foreach($staffUsers as $st)
              <option value="{{ $st->id }}">{{ $st->name }} ({{ ucfirst($st->role) }})</option>
            @endforeach
          </select>
        </div>
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Tanggal Shift *</label>
          <input type="date" name="shift_date" class="form-control" value="{{ date('Y-m-d') }}" required>
        </div>
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Nama Shift *</label>
          <select name="shift_name" class="form-control" required>
            <option value="Pagi">Shift Pagi</option>
            <option value="Siang">Shift Siang</option>
            <option value="Malam">Shift Malam</option>
          </select>
        </div>
        <div class="row">
          <div class="col-6 form-group mb-3">
            <label class="font-weight-bold text-dark" style="font-size: 13px;">Jam Mulai *</label>
            <input type="time" name="start_time" class="form-control" value="08:00" required>
          </div>
          <div class="col-6 form-group mb-3">
            <label class="font-weight-bold text-dark" style="font-size: 13px;">Jam Selesai *</label>
            <input type="time" name="end_time" class="form-control" value="16:00" required>
          </div>
        </div>
        <div class="form-group mb-0">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Catatan Shift</label>
          <input type="text" name="notes" class="form-control" placeholder="Contoh: Mengisi laci kasir awal">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary font-weight-bold">Simpan Shift</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit Shift Staf -->
<div class="modal fade" id="editShiftModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="editShiftForm" action="" method="POST" class="modal-content" style="border-radius: 12px;">
      @csrf
      @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title font-weight-bold">Edit Plotting Shift</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Pilih Karyawan / Staf *</label>
          <select name="user_id" id="editShUserId" class="form-control" required>
            @foreach($staffUsers as $st)
              <option value="{{ $st->id }}">{{ $st->name }} ({{ ucfirst($st->role) }})</option>
            @endforeach
          </select>
        </div>
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Tanggal Shift *</label>
          <input type="date" name="shift_date" id="editShDate" class="form-control" required>
        </div>
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Nama Shift *</label>
          <select name="shift_name" id="editShName" class="form-control" required>
            <option value="Pagi">Shift Pagi</option>
            <option value="Siang">Shift Siang</option>
            <option value="Malam">Shift Malam</option>
          </select>
        </div>
        <div class="row">
          <div class="col-6 form-group mb-3">
            <label class="font-weight-bold text-dark" style="font-size: 13px;">Jam Mulai *</label>
            <input type="time" name="start_time" id="editShStart" class="form-control" required>
          </div>
          <div class="col-6 form-group mb-3">
            <label class="font-weight-bold text-dark" style="font-size: 13px;">Jam Selesai *</label>
            <input type="time" name="end_time" id="editShEnd" class="form-control" required>
          </div>
        </div>
        <div class="form-group mb-0">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Catatan Shift</label>
          <input type="text" name="notes" id="editShNotes" class="form-control">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary font-weight-bold">Update Shift</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Tambah Promo -->
<div class="modal fade" id="addPromoModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form action="{{ route('manager.promo.store') }}" method="POST" class="modal-content" style="border-radius: 12px;">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title font-weight-bold">Buat Voucher Promo Baru</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Kode Promo *</label>
          <input type="text" name="code" class="form-control" placeholder="Contoh: FITAGUSTUS" required style="text-transform: uppercase;">
        </div>
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Deskripsi / Keterangan</label>
          <input type="text" name="description" class="form-control" placeholder="Contoh: Potongan 15% khusus paket tahunan">
        </div>
        <div class="row">
          <div class="col-6 form-group mb-3">
            <label class="font-weight-bold text-dark" style="font-size: 13px;">Tipe Diskon *</label>
            <select name="discount_type" class="form-control" required>
              <option value="percentage">Persentase (%)</option>
              <option value="fixed">Nominal Tetap (Rp)</option>
            </select>
          </div>
          <div class="col-6 form-group mb-3">
            <label class="font-weight-bold text-dark" style="font-size: 13px;">Nilai Diskon *</label>
            <input type="number" name="discount_value" class="form-control" placeholder="Contoh: 15 / 50000" required>
          </div>
        </div>
        <div class="row">
          <div class="col-6 form-group mb-3">
            <label class="font-weight-bold text-dark" style="font-size: 13px;">Min. Belanja (Rp) *</label>
            <input type="number" name="min_purchase" class="form-control" value="0" required>
          </div>
          <div class="col-6 form-group mb-3">
            <label class="font-weight-bold text-dark" style="font-size: 13px;">Maksimal Pemakaian *</label>
            <input type="number" name="max_uses" class="form-control" value="100" required>
          </div>
        </div>
        <div class="row">
          <div class="col-6 form-group mb-0">
            <label class="font-weight-bold text-dark" style="font-size: 13px;">Berlaku Dari *</label>
            <input type="date" name="valid_from" class="form-control" value="{{ date('Y-m-d') }}" required>
          </div>
          <div class="col-6 form-group mb-0">
            <label class="font-weight-bold text-dark" style="font-size: 13px;">Berlaku Sampai *</label>
            <input type="date" name="valid_until" class="form-control" value="{{ date('Y-m-d', strtotime('+30 days')) }}" required>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-success font-weight-bold">Simpan Promo</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit Promo -->
<div class="modal fade" id="editPromoModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="editPromoForm" action="" method="POST" class="modal-content" style="border-radius: 12px;">
      @csrf
      @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title font-weight-bold">Edit Voucher Promo</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Kode Promo *</label>
          <input type="text" name="code" id="editPromoCode" class="form-control" required style="text-transform: uppercase;">
        </div>
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Deskripsi / Keterangan</label>
          <input type="text" name="description" id="editPromoDesc" class="form-control">
        </div>
        <div class="row">
          <div class="col-6 form-group mb-3">
            <label class="font-weight-bold text-dark" style="font-size: 13px;">Tipe Diskon *</label>
            <select name="discount_type" id="editPromoType" class="form-control" required>
              <option value="percentage">Persentase (%)</option>
              <option value="fixed">Nominal Tetap (Rp)</option>
            </select>
          </div>
          <div class="col-6 form-group mb-3">
            <label class="font-weight-bold text-dark" style="font-size: 13px;">Nilai Diskon *</label>
            <input type="number" name="discount_value" id="editPromoVal" class="form-control" required>
          </div>
        </div>
        <div class="row">
          <div class="col-6 form-group mb-3">
            <label class="font-weight-bold text-dark" style="font-size: 13px;">Min. Belanja (Rp) *</label>
            <input type="number" name="min_purchase" id="editPromoMin" class="form-control" required>
          </div>
          <div class="col-6 form-group mb-3">
            <label class="font-weight-bold text-dark" style="font-size: 13px;">Maksimal Pemakaian *</label>
            <input type="number" name="max_uses" id="editPromoMax" class="form-control" required>
          </div>
        </div>
        <div class="row mb-3">
          <div class="col-6 form-group mb-0">
            <label class="font-weight-bold text-dark" style="font-size: 13px;">Berlaku Dari *</label>
            <input type="date" name="valid_from" id="editPromoFrom" class="form-control" required>
          </div>
          <div class="col-6 form-group mb-0">
            <label class="font-weight-bold text-dark" style="font-size: 13px;">Berlaku Sampai *</label>
            <input type="date" name="valid_until" id="editPromoUntil" class="form-control" required>
          </div>
        </div>
        <div class="form-group mb-0 border-top pt-2">
          <div class="custom-control custom-checkbox">
            <input type="checkbox" name="is_active" value="1" class="custom-control-input" id="editPromoActive">
            <label class="custom-control-label font-weight-bold text-dark" for="editPromoActive">Voucher Aktif & Dapat Digunakan</label>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary font-weight-bold">Update Promo</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Proses Complaint -->
<div class="modal fade" id="editComplaintModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="editComplaintForm" action="" method="POST" class="modal-content" style="border-radius: 12px;">
      @csrf
      @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title font-weight-bold">Proses Keluhan Member</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <p class="font-weight-bold text-dark mb-1" id="complaintTitleLabel"></p>
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Status Keluhan *</label>
          <select name="status" id="editCompStatus" class="form-control" required>
            <option value="open">Open</option>
            <option value="in_progress">In Progress</option>
            <option value="resolved">Resolved (Terselesaikan)</option>
            <option value="closed">Closed (Ditutup)</option>
          </select>
        </div>
        <div class="form-group mb-0">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Solusi / Resolusi Keluhan *</label>
          <textarea name="resolution" id="editCompResolution" class="form-control" rows="4" placeholder="Tuliskan tindakan/solusi pemecahan masalah di sini..." required></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary font-weight-bold">Simpan Solusi</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Tambah Vendor -->
<div class="modal fade" id="addVendorModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form action="{{ route('manager.vendors.store') }}" method="POST" class="modal-content" style="border-radius: 12px;">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title font-weight-bold">Tambah Kontak Vendor Baru</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Nama Vendor / Kontak Person *</label>
          <input type="text" name="name" class="form-control" placeholder="Contoh: Bpk Budi (Teknisi Treadmill)" required>
        </div>
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Kategori Layanan *</label>
          <input type="text" name="category" class="form-control" placeholder="Contoh: Teknisi Alat / Supplier Suplemen / Kebersihan" required>
        </div>
        <div class="row">
          <div class="col-6 form-group mb-3">
            <label class="font-weight-bold text-dark" style="font-size: 13px;">No. Telepon / HP</label>
            <input type="text" name="phone" class="form-control" placeholder="0812...">
          </div>
          <div class="col-6 form-group mb-3">
            <label class="font-weight-bold text-dark" style="font-size: 13px;">Email</label>
            <input type="email" name="email" class="form-control" placeholder="vendor@mail.com">
          </div>
        </div>
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Alamat Kantor</label>
          <input type="text" name="address" class="form-control" placeholder="Alamat lengkap vendor">
        </div>
        <div class="form-group mb-0">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Catatan Tambahan</label>
          <textarea name="notes" class="form-control" rows="2" placeholder="Catatan tambahan kontak"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-success font-weight-bold">Simpan Kontak</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit Vendor -->
<div class="modal fade" id="editVendorModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="editVendorForm" action="" method="POST" class="modal-content" style="border-radius: 12px;">
      @csrf
      @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title font-weight-bold">Edit Kontak Vendor</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Nama Vendor / Kontak Person *</label>
          <input type="text" name="name" id="editVenName" class="form-control" required>
        </div>
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Kategori Layanan *</label>
          <input type="text" name="category" id="editVenCategory" class="form-control" required>
        </div>
        <div class="row">
          <div class="col-6 form-group mb-3">
            <label class="font-weight-bold text-dark" style="font-size: 13px;">No. Telepon / HP</label>
            <input type="text" name="phone" id="editVenPhone" class="form-control">
          </div>
          <div class="col-6 form-group mb-3">
            <label class="font-weight-bold text-dark" style="font-size: 13px;">Email</label>
            <input type="email" name="email" id="editVenEmail" class="form-control">
          </div>
        </div>
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Alamat Kantor</label>
          <input type="text" name="address" id="editVenAddress" class="form-control">
        </div>
        <div class="form-group mb-0">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Catatan Tambahan</label>
          <textarea name="notes" id="editVenNotes" class="form-control" rows="2"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary font-weight-bold">Update Kontak</button>
      </div>
    </form>
  </div>
</div>

@endsection

@section('scripts')
<script>
  $(document).ready(function() {
    // 1. Edit Master Class
    $('.btn-edit-mclass').on('click', function() {
      var id = $(this).data('id');
      $('#editMClassName').val($(this).data('name'));
      $('#editMClassDay').val($(this).data('day'));
      $('#editMClassStartTime').val($(this).data('start_time'));
      $('#editMClassDuration').val($(this).data('duration_minutes'));
      $('#editMasterClassForm').attr('action', '/manager/classes/' + id);
      $('#editMasterClassModal').modal('show');
    });

    // 2. Edit Equipment
    $('.btn-edit-eq').on('click', function() {
      var id = $(this).data('id');
      $('#editEqName').val($(this).data('name'));
      $('#editEqCategory').val($(this).data('category'));
      $('#editEqBrand').val($(this).data('brand'));
      $('#editEqStatus').val($(this).data('status'));
      $('#editEqPurchaseDate').val($(this).data('purchase_date'));
      $('#editEqNextService').val($(this).data('next_service_date'));
      $('#editEquipmentForm').attr('action', '/manager/equipment/' + id);
      $('#editEquipmentModal').modal('show');
    });

    // 3. Edit Shift
    $('.btn-edit-shift').on('click', function() {
      var id = $(this).data('id');
      $('#editShUserId').val($(this).data('user_id'));
      $('#editShDate').val($(this).data('shift_date'));
      $('#editShName').val($(this).data('shift_name'));
      $('#editShStart').val($(this).data('start_time'));
      $('#editShEnd').val($(this).data('end_time'));
      $('#editShNotes').val($(this).data('notes'));
      $('#editShiftForm').attr('action', '/manager/shifts/' + id);
      $('#editShiftModal').modal('show');
    });

    // 4. Edit Promo
    $('.btn-edit-promo').on('click', function() {
      var id = $(this).data('id');
      $('#editPromoCode').val($(this).data('code'));
      $('#editPromoDesc').val($(this).data('description'));
      $('#editPromoType').val($(this).data('discount_type'));
      $('#editPromoVal').val($(this).data('discount_value'));
      $('#editPromoMin').val($(this).data('min_purchase'));
      $('#editPromoMax').val($(this).data('max_uses'));
      $('#editPromoFrom').val($(this).data('valid_from'));
      $('#editPromoUntil').val($(this).data('valid_until'));
      $('#editPromoActive').prop('checked', $(this).data('is_active') == 1);
      $('#editPromoForm').attr('action', '/manager/promo/' + id);
      $('#editPromoModal').modal('show');
    });

    // 8. Edit Complaint
    $('.btn-edit-complaint').on('click', function() {
      var id = $(this).data('id');
      $('#complaintTitleLabel').text('Judul: ' + $(this).data('title'));
      $('#editCompStatus').val($(this).data('status'));
      $('#editCompResolution').val($(this).data('resolution'));
      $('#editComplaintForm').attr('action', '/manager/complaints/' + id);
      $('#editComplaintModal').modal('show');
    });

    // 10. Edit Vendor
    $('.btn-edit-vendor').on('click', function() {
      var id = $(this).data('id');
      $('#editVenName').val($(this).data('name'));
      $('#editVenCategory').val($(this).data('category'));
      $('#editVenPhone').val($(this).data('phone'));
      $('#editVenEmail').val($(this).data('email'));
      $('#editVenAddress').val($(this).data('address'));
      $('#editVenNotes').val($(this).data('notes'));
      $('#editVendorForm').attr('action', '/manager/vendors/' + id);
      $('#editVendorModal').modal('show');
    });

    // Aktifkan tab-pane berdasarkan parameter URL query ?tab=...
    var activeTab = "{{ $activeTab }}";
    $('.tab-pane').removeClass('show active');
    if (activeTab === 'class') $('#class-sec').addClass('show active');
    else if (activeTab === 'maintenance') $('#maintenance-sec').addClass('show active');
    else if (activeTab === 'shift') $('#shift-sec').addClass('show active');
    else if (activeTab === 'approval') $('#approval-sec').addClass('show active');
    else if (activeTab === 'promo') $('#promo-sec').addClass('show active');
    else if (activeTab === 'performance') $('#performance-sec').addClass('show active');
    else if (activeTab === 'report') $('#report-sec').addClass('show active');
    else if (activeTab === 'stock') $('#stock-sec').addClass('show active');
    else if (activeTab === 'complaints') $('#complaints-sec').addClass('show active');
    else if (activeTab === 'vendors') $('#vendors-sec').addClass('show active');
  });
</script>
@endsection
