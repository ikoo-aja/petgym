@extends('layouts.admin')

@section('title', 'Manajemen Loker &mdash; PetGym')
@section('page_title', 'Manajemen Loker & Peminjaman Kunci')
@section('page_subtitle', 'Visual status loker, peminjaman kunci ke member, dan pengembalian otomatis')

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

<!-- Legend Status Warna Loker -->
<div class="card-custom mb-4">
  <div class="d-flex align-items-center flex-wrap" style="gap: 20px;">
    <h6 class="font-weight-bold text-dark mb-0 mr-3">Status Loker:</h6>
    <div class="d-flex align-items-center"><span style="width:16px;height:16px;border-radius:4px;background:#28a745;display:inline-block;margin-right:6px;"></span> <span style="font-size:13px;">Tersedia</span></div>
    <div class="d-flex align-items-center"><span style="width:16px;height:16px;border-radius:4px;background:#007bff;display:inline-block;margin-right:6px;"></span> <span style="font-size:13px;">Terpakai</span></div>
    <div class="d-flex align-items-center"><span style="width:16px;height:16px;border-radius:4px;background:#dc3545;display:inline-block;margin-right:6px;"></span> <span style="font-size:13px;">Rusak (Diblokir Admin)</span></div>
    <div class="ml-auto">
      <span class="badge badge-info font-weight-bold px-3 py-2" style="border-radius:10px;">Total: {{ count($lockers) }} Loker</span>
    </div>
  </div>
</div>

<!-- Grid Visual Loker -->
<div class="card-custom mb-4">
  <h6 class="font-weight-bold text-dark mb-3">Peta Loker Gym</h6>
  <div class="d-flex flex-wrap" style="gap: 10px;">
    @forelse($lockers as $locker)
      @php
        $bgColor = '#28a745';
        $textColor = '#fff';
        $cursor = 'pointer';
        $label = 'Tersedia';
        if ($locker->status === 'terpakai') {
          $bgColor = '#007bff';
          $label = 'Terpakai';
        } elseif ($locker->status === 'rusak') {
          $bgColor = '#dc3545';
          $label = 'Rusak';
          $cursor = 'not-allowed';
        }
      @endphp
      <div class="locker-box text-center"
        style="width: 72px; height: 72px; background: {{ $bgColor }}; color: {{ $textColor }};
               border-radius: 10px; display: flex; flex-direction: column; align-items: center; justify-content: center;
               cursor: {{ $cursor }}; transition: transform 0.15s ease, box-shadow 0.15s ease; box-shadow: 0 2px 6px rgba(0,0,0,0.15);"
        @if($locker->status === 'tersedia')
          data-toggle="modal" data-target="#assignModal" data-locker-id="{{ $locker->id }}" data-locker-number="{{ $locker->locker_number }}"
        @elseif($locker->status === 'terpakai')
          data-toggle="modal" data-target="#returnModal" data-locker-id="{{ $locker->id }}" data-locker-number="{{ $locker->locker_number }}"
        @endif
        onmouseover="this.style.transform='scale(1.08)';this.style.boxShadow='0 4px 12px rgba(0,0,0,0.25)'"
        onmouseout="this.style.transform='scale(1)';this.style.boxShadow='0 2px 6px rgba(0,0,0,0.15)'">
        <span style="font-weight:800; font-size: 16px;">{{ $locker->locker_number }}</span>
        <span style="font-size: 9px; opacity: 0.85;">{{ $label }}</span>
      </div>
    @empty
      <div class="text-center text-muted w-100 py-5">
        <p>Belum ada data loker. Admin perlu menambahkan master loker di menu <strong>"Master Loker Gym"</strong>.</p>
      </div>
    @endforelse
  </div>
</div>

<!-- Tabel Peminjaman Aktif -->
<div class="card-custom">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="font-weight-bold text-dark mb-0">Peminjaman Kunci Loker Aktif</h6>
    <span class="badge badge-warning text-dark font-weight-bold px-2 py-1" style="font-size:11px;">{{ count($activeRentals) }} kunci belum dikembalikan</span>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
        <tr>
          <th>Loker</th>
          <th>Member</th>
          <th>Jam Pinjam</th>
          <th>Durasi</th>
          <th class="text-right">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($activeRentals as $r)
        <tr>
          <td><span class="badge badge-primary font-weight-bold px-2 py-1" style="font-size:13px;">{{ $r->locker ? $r->locker->locker_number : '-' }}</span></td>
          <td class="font-weight-bold text-dark">{{ $r->member ? $r->member->name : '-' }}</td>
          <td style="font-size:13px;">{{ $r->rented_at ? \Carbon\Carbon::parse($r->rented_at)->format('H:i:s') : '-' }}</td>
          <td>
            @if($r->rented_at)
              @php $dur = \Carbon\Carbon::parse($r->rented_at)->diffForHumans(null, true) @endphp
              <span class="badge badge-warning text-dark">{{ $dur }}</span>
            @else
              -
            @endif
          </td>
          <td class="text-right">
            <form action="{{ route('receptionist.lockers.return', $r->locker_id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Konfirmasi pengembalian kunci loker ini?')">
              @csrf
              <button type="submit" class="btn btn-sm btn-outline-success font-weight-bold" style="border-radius: 8px;">
                <span class="icon-check"></span> Kembalikan Kunci
              </button>
            </form>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="5" class="text-center py-4 text-muted">Semua kunci loker telah dikembalikan — tidak ada peminjaman aktif saat ini.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Assign Loker ke Member -->
<div class="modal fade" id="assignModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form action="{{ route('receptionist.lockers.assign') }}" method="POST" class="modal-content" style="border-radius: 12px;">
      @csrf
      <input type="hidden" name="locker_id" id="assignLockerId">
      <div class="modal-header">
        <h5 class="modal-title font-weight-bold">Pinjamkan Kunci Loker <span id="assignLockerNum" class="text-primary"></span></h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label class="font-weight-bold text-dark" style="font-size:13px;">Pilih Member Aktif *</label>
          <select name="member_id" class="form-control" required>
            <option value="">-- Pilih Member --</option>
            @foreach($members as $m)
              <option value="{{ $m->id }}">{{ $m->name }} (PIN: {{ $m->access_code }})</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary font-weight-bold">Berikan Kunci Loker</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Konfirmasi Return Loker -->
<div class="modal fade" id="returnModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="returnForm" action="" method="POST" class="modal-content" style="border-radius: 12px;">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title font-weight-bold">Kembalikan Kunci Loker <span id="returnLockerNum" class="text-primary"></span></h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <p>Apakah Anda yakin ingin mengkonfirmasi pengembalian kunci loker ini? Status loker akan kembali menjadi <strong class="text-success">"Tersedia"</strong>.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-success font-weight-bold">Ya, Kembalikan Kunci</button>
      </div>
    </form>
  </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
  // Assign modal
  $('#assignModal').on('show.bs.modal', function(e) {
    var btn = $(e.relatedTarget);
    $('#assignLockerId').val(btn.data('locker-id'));
    $('#assignLockerNum').text('#' + btn.data('locker-number'));
  });

  // Return modal
  $('#returnModal').on('show.bs.modal', function(e) {
    var btn = $(e.relatedTarget);
    var lockerId = btn.data('locker-id');
    $('#returnLockerNum').text('#' + btn.data('locker-number'));
    $('#returnForm').attr('action', '/receptionist/lockers/' + lockerId + '/return');
  });
});
</script>
@endsection
