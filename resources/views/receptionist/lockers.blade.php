@extends('layouts.admin')

@section('title', 'Manajemen Loker &mdash; PetGym')
@section('page_title', 'Manajemen Loker & Peminjaman Kunci')
@section('page_subtitle', 'Visual status loker, peminjaman kunci ke member, dan pengembalian otomatis')

@section('content')

<!-- Legend Status Warna Loker -->
<div class="card-custom mb-4">
  <div class="d-flex align-items-center flex-wrap" style="gap: 20px;">
    <h6 class="font-weight-bold text-dark mb-0 mr-3">Status Loker:</h6>
    <div class="d-flex align-items-center"><span style="width:16px;height:16px;border-radius:4px;background:#28a745;display:inline-block;margin-right:6px;"></span> <span style="font-size:13px;">Tersedia</span></div>
    <div class="d-flex align-items-center"><span style="width:16px;height:16px;border-radius:4px;background:#007bff;display:inline-block;margin-right:6px;"></span> <span style="font-size:13px;">Terpakai</span></div>
    <div class="d-flex align-items-center"><span style="width:16px;height:16px;border-radius:4px;background:#dc3545;display:inline-block;margin-right:6px;"></span> <span style="font-size:13px;">Rusak / Tidak Aktif</span></div>
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
        <p>Belum ada data loker terdaftar dalam sistem.</p>
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
          <th>Kontak</th>
          <th>Tipe Sewa</th>
          <th>Waktu Pinjam</th>
          <th>Berakhir</th>
          <th class="text-right">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($activeRentals as $r)
        <tr>
          <td><span class="badge badge-primary font-weight-bold px-2 py-1" style="font-size:13px;">#{{ $r->locker ? $r->locker->locker_number : '-' }}</span></td>
          <td class="font-weight-bold text-dark">{{ $r->member ? $r->member->name : '-' }}</td>
          <td style="font-size:13px;" class="text-dark">{{ $r->member && $r->member->phone ? \App\Helpers\PrivacyHelper::maskPhone($r->member->phone) : '-' }}</td>
          <td>
            <span class="badge badge-info font-weight-bold text-uppercase">{{ $r->rental_type ?? 'Harian' }}</span>
          </td>
          <td style="font-size:13px;">{{ $r->rented_at ? \Carbon\Carbon::parse($r->rented_at)->format('d M H:i') : '-' }}</td>
          <td style="font-size:13px;">
            @if($r->end_date)
              <span class="text-dark font-weight-bold">{{ \Carbon\Carbon::parse($r->end_date)->format('d M Y') }}</span>
            @else
              -
            @endif
          </td>
          <td class="text-right">
            <form action="{{ route('receptionist.lockers.return', $r->locker_id) }}" method="POST" style="display:inline;" data-confirm="Konfirmasi pengembalian kunci loker ini?">
              @csrf
              <button type="submit" class="btn btn-sm btn-outline-success font-weight-bold" style="border-radius: 8px;">
                Kembalikan Loker
              </button>
            </form>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7" class="text-center py-4 text-muted">Semua loker telah dikembalikan. Tidak ada penyewaan aktif saat ini.</td>
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
        <div class="form-group position-relative" id="assignMemberSearchWrapper">
          <label class="font-weight-bold text-dark" style="font-size:13px;">Pilih Member Aktif *</label>
          <input type="hidden" name="member_id" id="assignLockerMemberId" required>
          <div class="input-group">
            <input type="text" id="assignMemberSearchInput" class="form-control" placeholder="Ketik nama atau no. HP member..." autocomplete="off" style="border-radius: 8px;">
            <div class="input-group-append" id="assignClearMemberBtnGroup" style="display: none;">
              <button class="btn btn-outline-secondary font-weight-bold" type="button" id="assignBtnClearMember" title="Hapus Pilihan">&times;</button>
            </div>
          </div>
          <div id="assignMemberSearchResults" class="list-group shadow position-absolute w-100 mt-1" style="display: none; max-height: 200px; overflow-y: auto; z-index: 1050; border-radius: 8px; text-align: left; left: 0;">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" id="btnAssignLockerSubmit" class="btn btn-primary font-weight-bold" disabled>Berikan Kunci Loker</button>
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
// Data member aktif untuk pencarian peminjaman loker
var lockerMemberList = [
  @foreach($members as $m)
    {
      id: {{ $m->id }},
      name: "{{ addslashes($m->name) }}",
      phone: "{{ $m->phone ?? '' }}",
      status: "Aktif"
    },
  @endforeach
];

$(document).ready(function() {
  // Assign modal
  $('#assignModal').on('show.bs.modal', function(e) {
    var btn = $(e.relatedTarget);
    $('#assignLockerId').val(btn.data('locker-id'));
    $('#assignLockerNum').text('#' + btn.data('locker-number'));
    
    // Reset member search state
    $('#assignLockerMemberId').val('');
    $('#assignMemberSearchInput').val('').prop('readonly', false);
    $('#assignClearMemberBtnGroup').hide();
    $('#btnAssignLockerSubmit').prop('disabled', true);
    $('#assignMemberSearchResults').hide();
  });

  var $assignInput = $('#assignMemberSearchInput');
  var $assignResults = $('#assignMemberSearchResults');
  var $assignId = $('#assignLockerMemberId');
  var $assignClearBtn = $('#assignClearMemberBtnGroup');
  var $assignSubmitBtn = $('#btnAssignLockerSubmit');

  function renderAssignSearchResults(query) {
    $assignResults.empty();
    query = (query || '').trim().toLowerCase();

    var matches = lockerMemberList.filter(function(item) {
      if (!query) return true;
      return item.name.toLowerCase().indexOf(query) !== -1 || item.phone.toLowerCase().indexOf(query) !== -1;
    });

    if (matches.length > 0) {
      $.each(matches.slice(0, 15), function(i, item) {
        var el = $('<a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2">')
          .html('<div><strong class="text-dark">' + item.name + '</strong></div><span class="badge badge-success">' + item.status + '</span>')
          .on('click', function(e) {
            e.preventDefault();
            $assignId.val(item.id);
            $assignInput.val(item.name + ' — ' + item.status).prop('readonly', true);
            $assignClearBtn.show();
            $assignSubmitBtn.prop('disabled', false);
            $assignResults.hide();
          });
        $assignResults.append(el);
      });
    } else {
      $assignResults.append('<div class="list-group-item text-muted py-2 text-center" style="font-size: 13px;">Member tidak ditemukan</div>');
    }

    $assignResults.show();
  }

  $assignInput.on('focus input', function() {
    if (!$assignInput.prop('readonly')) {
      renderAssignSearchResults($(this).val());
    }
  });

  $('#assignBtnClearMember').on('click', function() {
    $assignId.val('');
    $assignInput.val('').prop('readonly', false).focus();
    $assignClearBtn.hide();
    $assignSubmitBtn.prop('disabled', true);
    renderAssignSearchResults('');
  });

  $(document).on('click', function(e) {
    if (!$(e.target).closest('#assignMemberSearchWrapper').length) {
      $assignResults.hide();
    }
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
