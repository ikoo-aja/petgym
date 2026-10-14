@extends('layouts.admin')

@section('title', 'Manajemen Loker &mdash; PetGym')
@section('page_title', 'Manajemen Loker')
@section('page_subtitle', 'Peminjaman kunci dan pemantauan status loker anggota')

@section('content')

<!-- Legend Status Warna Loker -->
<div class="card-custom mb-4">
  <div class="d-flex align-items-center flex-wrap" style="gap: 20px;">
    <h6 class="font-weight-bold text-dark mb-0 mr-3">Status Loker:</h6>
    <div class="d-flex align-items-center"><span style="width:16px;height:16px;border-radius:4px;background:#28a745;display:inline-block;margin-right:6px;"></span> <span style="font-size:13px;">Tersedia</span></div>
    <div class="d-flex align-items-center"><span style="width:16px;height:16px;border-radius:4px;background:#007bff;display:inline-block;margin-right:6px;"></span> <span style="font-size:13px;">Terpakai</span></div>
    <div class="d-flex align-items-center"><span style="width:16px;height:16px;border-radius:4px;background:#dc3545;display:inline-block;margin-right:6px;"></span> <span style="font-size:13px;">Rusak / Tidak Aktif</span></div>
    <div class="ml-auto d-flex align-items-center">
      <button type="button" class="btn btn-sm btn-outline-danger font-weight-bold mr-2" data-toggle="modal" data-target="#reportDamageModal" style="border-radius: 8px;">
        <i class="icon-wrench mr-1"></i> Laporkan Loker Rusak
      </button>
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
          $cursor = 'pointer';
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
        @elseif($locker->status === 'rusak')
          data-toggle="modal" data-target="#brokenModal" data-locker-id="{{ $locker->id }}" data-locker-number="{{ $locker->locker_number }}" data-notes="{{ $locker->notes }}"
        @endif
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
        <div class="mt-3 pt-2 border-top text-right">
          <button type="button" class="btn btn-link text-danger p-0 small font-weight-bold" id="btnSwitchToDamage">
            <i class="icon-wrench mr-1"></i> Loker ini fisik rusak? Laporkan ke Supervisor &raquo;
          </button>
        </div>
      </div>
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
        <p class="mb-3">Konfirmasi pengembalian kunci fisik loker dari member:</p>

        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark small mb-2 d-block">Kondisi Fisik Loker Saat Pengembalian *</label>
          <div class="custom-control custom-radio mb-2">
            <input type="radio" id="condGood" name="condition" value="tersedia" class="custom-control-input" checked>
            <label class="custom-control-label font-weight-bold text-success" for="condGood">
              <i class="icon-check-circle mr-1"></i> Kondisi Baik / Normal (Loker kembali Tersedia)
            </label>
          </div>
          <div class="custom-control custom-radio">
            <input type="radio" id="condBroken" name="condition" value="rusak" class="custom-control-input">
            <label class="custom-control-label font-weight-bold text-danger" for="condBroken">
              <i class="icon-exclamation-triangle mr-1"></i> Kondisi Rusak (Kunci macet/pintu rusak & kirim tiket ke Supervisor)
            </label>
          </div>
        </div>

        <div id="returnDamageNotesGroup" style="display: none;" class="form-group mb-0">
          <label class="font-weight-bold text-dark small mb-1">Catatan Kerusakan (akan diteruskan ke Supervisor) *</label>
          <textarea name="notes" id="returnDamageNotes" class="form-control" rows="2" placeholder="Contoh: Kunci bengkok, engsel pintu lepas, slot kunci macet..."></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Batal</button>
        <button type="submit" id="btnReturnLockerSubmit" class="btn btn-success font-weight-bold">Ya, Kembalikan Kunci</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Laporkan Loker Rusak (Manual) -->
<div class="modal fade" id="reportDamageModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <form id="reportDamageForm" action="" method="POST" class="modal-content shadow border-0" style="border-radius: 12px;">
      @csrf
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title font-weight-bold mb-0">
          <i class="icon-wrench mr-2"></i> Laporkan Loker Rusak ke Supervisor
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body p-4">
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark small mb-1">Pilih Unit Loker *</label>
          <select name="locker_select" id="reportDamageLockerSelect" class="form-control" required>
            <option value="">-- Pilih Loker yang Rusak --</option>
            @foreach($lockers as $l)
              @if($l->status !== 'rusak')
                <option value="{{ $l->id }}" data-num="{{ $l->locker_number }}" data-status="{{ $l->status }}">
                  Loker #{{ $l->locker_number }} (Status: {{ ucfirst($l->status) }})
                </option>
              @endif
            @endforeach
          </select>
          <small class="text-muted">Loker yang dilaporkan rusak akan otomatis dialihkan ke status <strong>Rusak</strong> dan dikirimkan ke modul perbaikan Supervisor.</small>
        </div>
        <div class="form-group mb-0">
          <label class="font-weight-bold text-dark small mb-1">Deskripsi Kerusakan Fisik *</label>
          <textarea name="notes" id="reportDamageNotesInput" class="form-control" rows="3" placeholder="Contoh: Kunci patah di dalam lubang, pintu macet tidak bisa tertutup, engsel lepas..." required></textarea>
        </div>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary font-weight-bold px-3" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-danger font-weight-bold px-4">
          <i class="icon-send mr-1"></i> Kirim Laporan ke Supervisor
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Info Loker Rusak (Klik Box Merah) -->
<div class="modal fade" id="brokenModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content shadow border-0" style="border-radius: 12px;">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title font-weight-bold mb-0">
          <i class="icon-exclamation-triangle mr-2"></i> Loker #<span id="brokenModalLockerNum"></span> (Kondisi Rusak)
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body p-4">
        <div class="alert alert-danger p-3 mb-3" style="border-radius: 8px;">
          <strong class="d-block mb-1 font-weight-bold text-danger">Keterangan Kerusakan yang Dilaporkan:</strong>
          <p id="brokenModalNotes" class="mb-0 text-dark font-weight-bold">-</p>
        </div>
        <div class="p-3 bg-light rounded border text-muted small">
          <i class="icon-info mr-1"></i> Loker ini telah masuk ke dalam antrean maintenance <strong>Supervisor</strong>. Setelah perbaikan fisik diselesaikan oleh Supervisor, loker akan otomatis kembali tersedia untuk dipinjamkan.
        </div>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary font-weight-bold px-4" data-dismiss="modal">Tutup</button>
      </div>
    </div>
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

    // Reset return form state
    $('#condGood').prop('checked', true);
    $('#returnDamageNotesGroup').hide();
    $('#returnDamageNotes').val('').prop('required', false);
    $('#btnReturnLockerSubmit')
      .removeClass('btn-danger')
      .addClass('btn-success')
      .text('Ya, Kembalikan Kunci');
  });

  $('input[name="condition"]').on('change', function() {
    if ($(this).val() === 'rusak') {
      $('#returnDamageNotesGroup').slideDown(150);
      $('#returnDamageNotes').prop('required', true).focus();
      $('#btnReturnLockerSubmit')
        .removeClass('btn-success')
        .addClass('btn-danger')
        .text('Kembalikan & Kirim Laporan Rusak ke Supervisor');
    } else {
      $('#returnDamageNotesGroup').slideUp(150);
      $('#returnDamageNotes').prop('required', false);
      $('#btnReturnLockerSubmit')
        .removeClass('btn-danger')
        .addClass('btn-success')
        .text('Ya, Kembalikan Kunci');
    }
  });

  // Broken Modal info (Box Merah)
  $('#brokenModal').on('show.bs.modal', function(e) {
    var btn = $(e.relatedTarget);
    $('#brokenModalLockerNum').text(btn.data('locker-number'));
    $('#brokenModalNotes').text(btn.data('notes') || 'Dilaporkan rusak tanpa keterangan detail');
  });

  // Report Damage Form action selector
  $('#reportDamageLockerSelect').on('change', function() {
    var id = $(this).val();
    if (id) {
      $('#reportDamageForm').attr('action', '/receptionist/lockers/' + id + '/report-damage');
    } else {
      $('#reportDamageForm').attr('action', '');
    }
  });

  $('#reportDamageForm').on('submit', function(e) {
    var action = $(this).attr('action');
    if (!action) {
      e.preventDefault();
      alert('Silakan pilih unit loker terlebih dahulu.');
    }
  });

  // Switch from assign modal to report damage
  $('#btnSwitchToDamage').on('click', function() {
    var currentLockerId = $('#assignLockerId').val();
    $('#assignModal').modal('hide');
    setTimeout(function() {
      $('#reportDamageLockerSelect').val(currentLockerId).trigger('change');
      $('#reportDamageModal').modal('show');
    }, 400);
  });
});
</script>
@endsection
