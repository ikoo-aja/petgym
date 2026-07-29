@extends('layouts.admin')

@section('title', 'Buku Tamu & Lost Found &mdash; PetGym')
@section('page_title', 'Buku Tamu & Lost Found')
@section('page_subtitle', 'Pencatatan walk-in tamu (leads), konversi ke member, dan log barang tertinggal')

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
  <strong>✅ Berhasil!</strong> {{ session('success') }}
  <button type="button" class="close" data-dismiss="alert">&times;</button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
  <strong>❌ Gagal!</strong> {{ session('error') }}
  <button type="button" class="close" data-dismiss="alert">&times;</button>
</div>
@endif

<div class="row">
  <!-- LEFT: Buku Tamu -->
  <div class="col-md-7">
    <div class="card-custom mb-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="font-weight-bold text-dark mb-0">📋 Buku Tamu / Walk-in Leads</h6>
        <button class="btn btn-sm btn-primary font-weight-bold" data-toggle="modal" data-target="#addGuestModal" style="border-radius: 8px;">+ Tambah Tamu</button>
      </div>

      <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
            <tr>
              <th>Nama</th>
              <th>No. HP</th>
              <th>Email</th>
              <th>Catatan</th>
              <th>Status</th>
              <th class="text-right">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($guests as $g)
            <tr>
              <td class="font-weight-bold text-dark">{{ $g->name }}</td>
              <td style="font-size:13px;">{{ $g->phone }}</td>
              <td style="font-size:12px;">{{ $g->email ?? '-' }}</td>
              <td style="font-size:12px; max-width:120px;" class="text-truncate">{{ $g->notes ?? '-' }}</td>
              <td>
                @if($g->converted_to_member_id)
                  <span class="badge badge-success">Sudah Member</span>
                @else
                  <span class="badge badge-warning text-dark">Lead Baru</span>
                @endif
              </td>
              <td class="text-right">
                @if(!$g->converted_to_member_id)
                  <form action="{{ route('receptionist.guests.convert', $g->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Konversi tamu ini menjadi member aktif?')">
                    @csrf
                    <button type="submit" class="btn btn-xs btn-outline-success font-weight-bold" style="border-radius:6px;">Jadikan Member</button>
                  </form>
                @else
                  <span class="text-success font-weight-bold" style="font-size:12px;">✓ Converted</span>
                @endif
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="6" class="text-center py-4 text-muted">Belum ada data tamu tercatat.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- RIGHT: Form Tambah Tamu Singkat -->
  <div class="col-md-5">
    <div class="card-custom mb-4">
      <h6 class="font-weight-bold text-dark mb-3">✏️ Form Catat Tamu Baru (Quick Add)</h6>
      <form action="{{ route('receptionist.guests.store') }}" method="POST">
        @csrf
        <div class="form-group mb-2">
          <label class="font-weight-bold mb-1" style="font-size:12px;">Nama Tamu *</label>
          <input type="text" name="name" class="form-control form-control-sm" placeholder="Nama lengkap tamu" required>
        </div>
        <div class="form-group mb-2">
          <label class="font-weight-bold mb-1" style="font-size:12px;">No. HP *</label>
          <input type="text" name="phone" class="form-control form-control-sm" placeholder="08xxxxxxxxxx" required>
        </div>
        <div class="form-group mb-2">
          <label class="font-weight-bold mb-1" style="font-size:12px;">Email (Opsional)</label>
          <input type="email" name="email" class="form-control form-control-sm" placeholder="email@contoh.com">
        </div>
        <div class="form-group mb-3">
          <label class="font-weight-bold mb-1" style="font-size:12px;">Catatan / Minat</label>
          <textarea name="notes" class="form-control form-control-sm" rows="2" placeholder="Misal: Tertarik paket bulanan, tanya kelas yoga..."></textarea>
        </div>
        <button type="submit" class="btn btn-primary btn-block btn-sm font-weight-bold">Simpan ke Buku Tamu</button>
      </form>
    </div>
  </div>
</div>

<!-- Lost & Found Section -->
<div class="row">
  <div class="col-md-8">
    <div class="card-custom">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="font-weight-bold text-dark mb-0">🔍 Log Barang Tertinggal (Lost & Found)</h6>
        <button class="btn btn-sm btn-warning text-dark font-weight-bold" data-toggle="modal" data-target="#addLostFoundModal" style="border-radius: 8px;">+ Catat Barang Temuan</button>
      </div>
      <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
            <tr>
              <th>Nama Barang</th>
              <th>Lokasi Ditemukan</th>
              <th>Tanggal</th>
              <th>Status</th>
              <th class="text-right">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($lostFounds as $lf)
            <tr>
              <td class="font-weight-bold text-dark">{{ $lf->item_name }}</td>
              <td style="font-size:13px;">{{ $lf->location_found }}</td>
              <td style="font-size:13px;">{{ $lf->found_at ? \Carbon\Carbon::parse($lf->found_at)->format('d M Y') : '-' }}</td>
              <td>
                @if($lf->status === 'diklaim')
                  <span class="badge badge-success">Diklaim oleh {{ $lf->claimed_by_name }}</span>
                @else
                  <span class="badge badge-danger">Belum Diklaim</span>
                @endif
              </td>
              <td class="text-right">
                @if($lf->status !== 'diklaim')
                  <button class="btn btn-xs btn-outline-success font-weight-bold btn-claim-lf" data-id="{{ $lf->id }}" data-name="{{ $lf->item_name }}" data-toggle="modal" data-target="#claimModal" style="border-radius:6px;">Klaim Barang</button>
                @else
                  <span class="text-muted font-italic" style="font-size:12px;">Selesai</span>
                @endif
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="5" class="text-center py-4 text-muted">Tidak ada barang tertinggal yang tercatat.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Form Catat Barang Temuan -->
  <div class="col-md-4">
    <div class="card-custom">
      <h6 class="font-weight-bold text-dark mb-3">📦 Catat Barang Temuan Baru</h6>
      <form action="{{ route('receptionist.lost-found.store') }}" method="POST">
        @csrf
        <div class="form-group mb-2">
          <label class="font-weight-bold mb-1" style="font-size:12px;">Nama Barang *</label>
          <input type="text" name="item_name" class="form-control form-control-sm" placeholder="Misal: Handuk biru, botol minum hitam" required>
        </div>
        <div class="form-group mb-2">
          <label class="font-weight-bold mb-1" style="font-size:12px;">Lokasi Ditemukan *</label>
          <input type="text" name="location_found" class="form-control form-control-sm" placeholder="Misal: Ruang Ganti Pria, Loker 12" required>
        </div>
        <div class="form-group mb-3">
          <label class="font-weight-bold mb-1" style="font-size:12px;">Tanggal Ditemukan *</label>
          <input type="date" name="found_at" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
        </div>
        <button type="submit" class="btn btn-warning text-dark btn-block btn-sm font-weight-bold">Simpan ke Log Lost & Found</button>
      </form>
    </div>
  </div>
</div>

<!-- Modal Klaim Barang -->
<div class="modal fade" id="claimModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="claimForm" action="" method="POST" class="modal-content" style="border-radius: 12px;">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title font-weight-bold">Klaim Barang: <span id="claimItemName" class="text-primary"></span></h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label class="font-weight-bold text-dark" style="font-size:13px;">Nama Pengklaim *</label>
          <input type="text" name="claimed_by_name" class="form-control" placeholder="Nama lengkap orang yang mengklaim barang" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-success font-weight-bold">Konfirmasi Klaim</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Tambah Tamu -->
<div class="modal fade" id="addGuestModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form action="{{ route('receptionist.guests.store') }}" method="POST" class="modal-content" style="border-radius: 12px;">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title font-weight-bold">Tambah Data Tamu Baru</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="form-group mb-2">
          <label class="font-weight-bold mb-1">Nama Tamu *</label>
          <input type="text" name="name" class="form-control" placeholder="Nama lengkap" required>
        </div>
        <div class="form-group mb-2">
          <label class="font-weight-bold mb-1">No. HP *</label>
          <input type="text" name="phone" class="form-control" placeholder="08xxxxxxxxxx" required>
        </div>
        <div class="form-group mb-2">
          <label class="font-weight-bold mb-1">Email</label>
          <input type="email" name="email" class="form-control" placeholder="Opsional">
        </div>
        <div class="form-group mb-0">
          <label class="font-weight-bold mb-1">Catatan / Minat</label>
          <textarea name="notes" class="form-control" rows="2" placeholder="Misal: Tertarik paket bulanan"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary font-weight-bold">Simpan Tamu</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Catat Barang Lost & Found -->
<div class="modal fade" id="addLostFoundModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form action="{{ route('receptionist.lost-found.store') }}" method="POST" class="modal-content" style="border-radius: 12px;">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title font-weight-bold">Catat Barang Temuan Baru</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="form-group mb-2">
          <label class="font-weight-bold mb-1">Nama Barang *</label>
          <input type="text" name="item_name" class="form-control" placeholder="Misal: Handuk biru" required>
        </div>
        <div class="form-group mb-2">
          <label class="font-weight-bold mb-1">Lokasi Ditemukan *</label>
          <input type="text" name="location_found" class="form-control" placeholder="Misal: Ruang Ganti Pria" required>
        </div>
        <div class="form-group mb-0">
          <label class="font-weight-bold mb-1">Tanggal Ditemukan *</label>
          <input type="date" name="found_at" class="form-control" value="{{ date('Y-m-d') }}" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-warning text-dark font-weight-bold">Simpan</button>
      </div>
    </form>
  </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
  // Claim modal
  $('.btn-claim-lf').on('click', function() {
    var id = $(this).data('id');
    var name = $(this).data('name');
    $('#claimItemName').text(name);
    $('#claimForm').attr('action', '/receptionist/lost-found/' + id + '/claim');
  });
});
</script>
@endsection
