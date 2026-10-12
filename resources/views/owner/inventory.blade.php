@extends('layouts.admin')

@section('title', 'Pemantauan Inventaris & Loker (Owner) &mdash; PetGym')
@section('page_title', 'Pemantauan Inventaris Ritel & Loker Gym')
@section('page_subtitle', 'Monitoring valuasi aset 2-tier (Gudang & Etalase), vendor pemasok, dan okupansi fasilitas loker')

@section('content')

<!-- KPI Cards Valuasi Inventaris -->
<div class="row mb-4">
  <div class="col-md-4 mb-3 mb-md-0">
    <div class="card-custom p-3 border-left border-primary" style="border-left-width: 4px !important;">
      <span class="text-muted small font-weight-bold text-uppercase d-block mb-1">Valuasi Stok Gudang (Modal)</span>
      <h4 class="font-weight-bold text-primary mb-0">Rp {{ number_format($totalWarehouseValuation, 0, ',', '.') }}</h4>
      <small class="text-muted">{{ $products->sum('warehouse_stock') }} unit di ruang penyimpanan gudang</small>
    </div>
  </div>
  <div class="col-md-4 mb-3 mb-md-0">
    <div class="card-custom p-3 border-left border-info" style="border-left-width: 4px !important;">
      <span class="text-muted small font-weight-bold text-uppercase d-block mb-1">Valuasi Stok Etalase (Modal)</span>
      <h4 class="font-weight-bold text-info mb-0">Rp {{ number_format($totalDisplayValuation, 0, ',', '.') }}</h4>
      <small class="text-muted">{{ $products->sum('stock') }} unit dipajang siap jual di kasir</small>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card-custom p-3 border-left border-success" style="border-left-width: 4px !important;">
      <span class="text-muted small font-weight-bold text-uppercase d-block mb-1">Total Nilai Aset Inventaris</span>
      <h4 class="font-weight-bold text-success mb-0">Rp {{ number_format($totalInventoryValuation, 0, ',', '.') }}</h4>
      <small class="text-muted">Total {{ $products->count() }} jenis produk ritel aktif</small>
    </div>
  </div>
</div>

<!-- KPI Cards Loker -->
<div class="row mb-4">
  <div class="col-md-4 mb-3 mb-md-0">
    <div class="card-custom text-white" style="background: linear-gradient(135deg, #10b981, #059669);">
      <small class="text-white text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Loker Tersedia</small>
      <h3 class="font-weight-bold text-white mb-0 mt-1">{{ $availableLockers }} Loker</h3>
      <small class="text-white-50">Siap digunakan member</small>
    </div>
  </div>
  <div class="col-md-4 mb-3 mb-md-0">
    <div class="card-custom text-white" style="background: linear-gradient(135deg, #0ea5e9, #0284c7);">
      <small class="text-white text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Loker Sedang Terpakai</small>
      <h3 class="font-weight-bold text-white mb-0 mt-1">{{ $occupiedLockers }} Loker</h3>
      <small class="text-white-50">Sedang disewa / dipinjam</small>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card-custom text-white" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
      <small class="text-white text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Loker Rusak / Blokir</small>
      <h3 class="font-weight-bold text-white mb-0 mt-1">{{ $brokenLockers }} Loker</h3>
      <small class="text-white-50">Perlu perbaikan fisik</small>
    </div>
  </div>
</div>

<!-- Tabel Master Inventaris 2-Tier -->
<div class="card-custom mb-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h6 class="font-weight-bold text-dark mb-0">Inventaris Produk 2-Tier (Gudang &amp; Etalase Kasir)</h6>
      <small class="text-muted">Rincian stok pasokan vendor di gudang dan barang yang siap dijual di meja kasir</small>
    </div>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
        <tr>
          <th>Nama Produk</th>
          <th>Kategori</th>
          <th>Vendor Mitra</th>
          <th>Harga Modal (HPP)</th>
          <th>Harga Jual (Kasir)</th>
          <th class="text-center">Stok Gudang</th>
          <th class="text-center">Stok Etalase</th>
          <th class="text-center">Total Fisik</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        @forelse($products as $p)
          <tr>
            <td class="font-weight-bold text-dark" style="font-size: 12.5px;">{{ $p->name }}</td>
            <td><span class="badge badge-light border">{{ ucfirst($p->category ?? 'Ritel') }}</span></td>
            <td style="font-size: 12px;" class="text-dark">{{ $p->vendor_name ?: '-' }}</td>
            <td class="text-muted" style="font-size: 12.5px;">Rp {{ number_format($p->cost_price ?: 0, 0, ',', '.') }}</td>
            <td class="font-weight-bold text-success" style="font-size: 12.5px;">Rp {{ number_format($p->price, 0, ',', '.') }}</td>
            <td class="text-center">
              <span class="badge {{ $p->warehouse_stock <= 5 ? 'badge-danger' : 'badge-primary' }} px-2 py-1">
                {{ $p->warehouse_stock }} pcs
              </span>
            </td>
            <td class="text-center">
              <span class="badge {{ $p->stock <= 10 ? 'badge-warning text-dark' : 'badge-success' }} px-2 py-1">
                {{ $p->stock }} pcs
              </span>
            </td>
            <td class="text-center font-weight-bold text-dark" style="font-size: 13px;">
              {{ $p->warehouse_stock + $p->stock }} pcs
            </td>
            <td>
              @if(($p->warehouse_stock + $p->stock) == 0)
                <span class="badge badge-danger">Habis Total</span>
              @elseif($p->warehouse_stock == 0 && $p->stock > 0)
                <span class="badge badge-warning text-dark">Gudang Kosong</span>
              @elseif($p->stock == 0 && $p->warehouse_stock > 0)
                <span class="badge badge-info">Belum Dipajang</span>
              @else
                <span class="badge badge-success">Tersedia</span>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="9" class="text-center py-4 text-muted">Belum ada data produk ritel kasir.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div class="row">
  <!-- Mutasi & Aliran Stok (Audit Trail) -->
  <div class="col-md-7 mb-4">
    <div class="card-custom h-100">
      <h6 class="font-weight-bold text-dark mb-3">Log Aliran &amp; Mutasi Stok Terkini</h6>
      <div class="table-responsive" style="max-height: 360px; overflow-y: auto;">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
            <tr>
              <th>Waktu</th>
              <th>Produk</th>
              <th>Jenis Aliran</th>
              <th class="text-center">Qty</th>
              <th>Operator</th>
            </tr>
          </thead>
          <tbody>
            @forelse($stockMutations as $sm)
              <tr>
                <td class="small text-muted text-nowrap">{{ $sm->created_at ? $sm->created_at->format('d/m H:i') : '-' }}</td>
                <td class="font-weight-bold text-dark" style="font-size: 12.5px;">{{ $sm->product ? $sm->product->name : 'Produk Dihapus' }}</td>
                <td>
                  @if($sm->type === 'in_vendor')
                    <span class="badge badge-success">Masuk Vendor</span>
                  @elseif($sm->type === 'transfer_to_display')
                    <span class="badge badge-info">Pajang Etalase</span>
                  @elseif($sm->type === 'sale_pos')
                    <span class="badge badge-primary">Penjualan POS</span>
                  @else
                    <span class="badge badge-secondary">{{ $sm->type }}</span>
                  @endif
                </td>
                <td class="text-center font-weight-bold">
                  @if($sm->type === 'in_vendor')
                    <span class="text-success">+{{ $sm->qty }}</span>
                  @elseif($sm->type === 'sale_pos')
                    <span class="text-danger">-{{ $sm->qty }}</span>
                  @else
                    <span>{{ $sm->qty }}</span>
                  @endif
                </td>
                <td class="small text-dark">{{ $sm->user ? $sm->user->name : 'Sistem' }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center py-4 text-muted">Belum ada riwayat mutasi stok.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Master Loker Gym -->
  <div class="col-md-5 mb-4">
    <div class="card-custom h-100">
      <h6 class="font-weight-bold text-dark mb-3">Master Loker Gym</h6>
      <div class="table-responsive" style="max-height: 360px; overflow-y: auto;">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
            <tr>
              <th>No. Loker</th>
              <th>Area/Kategori</th>
              <th>Status Okupansi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($lockers as $l)
              <tr>
                <td class="font-weight-bold text-dark" style="font-size: 12.5px;">Loker #{{ $l->locker_number }}</td>
                <td style="font-size: 12px;" class="text-muted">{{ $l->gender_type ?? 'Umum' }}</td>
                <td>
                  @if($l->status == 'tersedia')
                    <span class="badge badge-success px-2 py-1">Tersedia</span>
                  @elseif($l->status == 'terpakai')
                    <span class="badge badge-primary px-2 py-1">Terpakai</span>
                  @else
                    <span class="badge badge-danger px-2 py-1">Rusak/Blokir</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="3" class="text-center py-4 text-muted">Belum ada data master loker.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
