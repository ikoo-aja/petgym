@extends('layouts.admin')

@section('title', 'Pemantauan Inventaris & Loker (Owner) &mdash; PetGym')
@section('page_title', 'Pemantauan Inventaris Ritel & Loker Gym')
@section('page_subtitle', 'Monitoring stok produk suplemen/merchandise dan okupansi loker gym (Mode Pemantauan & Read-Only)')

@section('content')


<!-- KPI Cards Loker -->
<div class="row mb-4">
  <div class="col-md-4">
    <div class="card-custom text-white" style="background: linear-gradient(135deg, #10b981, #059669);">
      <small class="text-white-50 text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Loker Tersedia</small>
      <h3 class="font-weight-bold text-white mb-0 mt-1">{{ $availableLockers }} Loker</h3>
      <small class="text-white-50">Siap digunakan oleh member gym</small>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card-custom text-white" style="background: linear-gradient(135deg, #0ea5e9, #0284c7);">
      <small class="text-white-50 text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Loker Sedang Terpakai</small>
      <h3 class="font-weight-bold text-white mb-0 mt-1">{{ $occupiedLockers }} Loker</h3>
      <small class="text-white-50">Sedang disewa/dipakai saat ini</small>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card-custom text-white" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
      <small class="text-white-50 text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Loker Rusak / Blokir</small>
      <h3 class="font-weight-bold text-white mb-0 mt-1">{{ $brokenLockers }} Loker</h3>
      <small class="text-white-50">Perlu perbaikan atau perawatan</small>
    </div>
  </div>
</div>

<div class="row">
  <!-- Stok Produk Ritel Kasir -->
  <div class="col-md-7">
    <div class="card-custom">
      <h6 class="font-weight-bold text-dark mb-3">Inventaris Stok Produk Ritel Kasir</h6>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
            <tr>
              <th>Nama Produk</th>
              <th>Kategori</th>
              <th>Harga Jual</th>
              <th>Sisa Stok</th>
              <th>Status Stok</th>
            </tr>
          </thead>
          <tbody>
            @forelse($products as $p)
              <tr>
                <td class="font-weight-bold text-dark" style="font-size: 12.5px;">{{ $p->name }}</td>
                <td style="font-size: 12px;" class="text-muted">{{ $p->category ?? 'Ritel' }}</td>
                <td class="font-weight-bold text-success" style="font-size: 12.5px;">Rp {{ number_format($p->price, 0, ',', '.') }}</td>
                <td class="font-weight-bold text-dark" style="font-size: 13px;">{{ $p->stock }} {{ $p->unit ?? 'pcs' }}</td>
                <td>
                  @if($p->stock <= 5)
                    <span class="badge badge-danger px-2 py-1">Stok Kritis</span>
                  @elseif($p->stock <= 15)
                    <span class="badge badge-warning px-2 py-1">Menipis</span>
                  @else
                    <span class="badge badge-success px-2 py-1">Aman</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center py-4 text-muted">Belum ada data produk ritel kasir.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Master Loker Gym -->
  <div class="col-md-5">
    <div class="card-custom">
      <h6 class="font-weight-bold text-dark mb-3">Master Loker Gym</h6>
      <div class="table-responsive">
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
