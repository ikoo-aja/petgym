@extends('layouts.admin')

@section('title', 'Barang Tertinggal (Lost & Found) &mdash; PetGym')
@section('page_title', 'Log Barang Tertinggal (Lost & Found)')
@section('page_subtitle', 'Pencatatan barang hilang/tertinggal di area gym dan verifikasi klaim pengembalian kepada pemilik')

@section('content')
<div class="card-custom">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h6 class="font-weight-bold text-dark mb-0">Daftar Barang Temuan & Tertinggal</h6>
      <small class="text-muted">Total: {{ count($lostFounds) }} barang tercatat</small>
    </div>
    <button class="btn btn-sm btn-warning text-dark font-weight-bold" data-toggle="modal" data-target="#addLostFoundModal" style="border-radius: 8px;">
      + Catat Barang Temuan Baru
    </button>
  </div>

  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
        <tr>
          <th>Nama Barang</th>
          <th>Lokasi Ditemukan</th>
          <th>Tanggal Ditemukan</th>
          <th>Status Barang</th>
          <th>Penerima Klaim</th>
          <th class="text-right">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($lostFounds as $lf)
        <tr>
          <td class="font-weight-bold text-dark">{{ $lf->item_name }}</td>
          <td style="font-size:13px;" class="text-dark">{{ $lf->location_found }}</td>
          <td style="font-size:13px;" class="text-dark">{{ $lf->found_at ? \Carbon\Carbon::parse($lf->found_at)->format('d M Y') : '-' }}</td>
          <td>
            @if($lf->status === 'diklaim')
              <span class="badge badge-success px-2 py-1">Telah Diklaim</span>
            @else
              <span class="badge badge-danger px-2 py-1">Belum Diklaim</span>
            @endif
          </td>
          <td style="font-size:13px;">
            @if($lf->status === 'diklaim')
              <span class="text-dark font-weight-bold">{{ $lf->claimed_by_name }}</span>
              <small class="text-muted d-block" style="font-size:11px;">{{ $lf->claimed_at ? \Carbon\Carbon::parse($lf->claimed_at)->format('d M Y H:i') : '' }}</small>
            @else
              <span class="text-muted">-</span>
            @endif
          </td>
          <td class="text-right">
            @if($lf->status !== 'diklaim')
              <button class="btn btn-sm btn-outline-success font-weight-bold btn-claim-lf" data-id="{{ $lf->id }}" data-name="{{ $lf->item_name }}" style="border-radius:6px;">Klaim Barang</button>
            @else
              <span class="badge badge-light border text-muted">Selesai</span>
            @endif
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6" class="text-center py-4 text-muted">Tidak ada barang tertinggal yang tercatat saat ini.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Catat Barang Temuan Baru -->
<div class="modal fade" id="addLostFoundModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form action="{{ route('receptionist.lost-found.store') }}" method="POST" class="modal-content" style="border-radius: 12px;">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title font-weight-bold">Form Catat Barang Temuan</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body p-4">
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark mb-1" style="font-size: 13px;">Nama / Ciri Barang *</label>
          <input type="text" name="item_name" class="form-control" placeholder="Contoh: Botol minum hitam merk Hydro, Handuk biru" required style="border-radius: 8px;">
        </div>
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark mb-1" style="font-size: 13px;">Lokasi Ditemukan *</label>
          <input type="text" name="location_found" class="form-control" placeholder="Contoh: Ruang Ganti Pria, Loker 14, Area Treadmill" required style="border-radius: 8px;">
        </div>
        <div class="form-group mb-0">
          <label class="font-weight-bold text-dark mb-1" style="font-size: 13px;">Tanggal Ditemukan *</label>
          <input type="date" name="found_at" class="form-control" value="{{ date('Y-m-d') }}" required style="border-radius: 8px;">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 8px;">Batal</button>
        <button type="submit" class="btn btn-warning text-dark font-weight-bold px-4" style="border-radius: 8px;">Simpan Barang Temuan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Klaim Barang -->
<div class="modal fade" id="claimModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="claimForm" action="" method="POST" class="modal-content" style="border-radius: 12px;">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title font-weight-bold">Klaim Pengambilan Barang: <span id="claimItemName" class="text-primary"></span></h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body p-4">
        <div class="form-group mb-0">
          <label class="font-weight-bold text-dark mb-1" style="font-size: 13px;">Nama Lengkap Pengklaim *</label>
          <input type="text" name="claimed_by_name" class="form-control" placeholder="Nama lengkap pemilik yang mengambil barang" required style="border-radius: 8px;">
          <small class="text-muted mt-1 d-block">Pastikan identitas atau bukti kepemilikan telah diperiksa dengan teliti.</small>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 8px;">Batal</button>
        <button type="submit" class="btn btn-success font-weight-bold px-4" style="border-radius: 8px;">Konfirmasi Penyerahan Barang</button>
      </div>
    </form>
  </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
  $('.btn-claim-lf').on('click', function() {
    var id = $(this).data('id');
    var name = $(this).data('name');
    $('#claimItemName').text(name);
    $('#claimForm').attr('action', '/receptionist/lost-found/' + id + '/claim');
    $('#claimModal').modal('show');
  });
});
</script>
@endsection
