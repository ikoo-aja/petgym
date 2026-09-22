@extends('layouts.admin')

@section('title', 'Keluhan Member &mdash; PetGym')
@section('page_title', 'Keluhan & Komplain Member')
@section('page_subtitle', 'Pencatatan laporan keluhan fasilitas/layanan dari member untuk ditindaklanjuti oleh Supervisor dan Manager')

@section('content')
<div class="card-custom">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h6 class="font-weight-bold text-dark mb-0">Daftar Tiket Keluhan Member</h6>
      <small class="text-muted">Total: {{ count($complaints) }} tiket tercatat</small>
    </div>
    <button class="btn btn-sm btn-danger font-weight-bold" data-toggle="modal" data-target="#addComplaintModal" style="border-radius: 8px;">
      + Catat Keluhan Baru
    </button>
  </div>

  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
        <tr>
          <th>Tanggal</th>
          <th>Nama Member</th>
          <th>Judul Keluhan</th>
          <th>Status Tiket</th>
          <th>Deskripsi & Tindak Lanjut</th>
        </tr>
      </thead>
      <tbody>
        @forelse($complaints as $c)
        <tr>
          <td style="font-size:13px;" class="text-dark">{{ $c->created_at ? $c->created_at->format('d M Y H:i') : '-' }}</td>
          <td>
            <div class="font-weight-bold text-dark">{{ $c->member ? $c->member->name : '-' }}</div>
            <small class="text-muted">{{ $c->member && $c->member->phone ? \App\Helpers\PrivacyHelper::maskPhone($c->member->phone) : '' }}</small>
          </td>
          <td style="font-size:13px;" class="font-weight-bold text-dark">{{ $c->title }}</td>
          <td>
            @if($c->status === 'open')
              <span class="badge badge-danger px-2 py-1">Menunggu Penanganan</span>
            @elseif($c->status === 'in_progress')
              <span class="badge badge-warning text-dark px-2 py-1">Sedang Diproses</span>
            @elseif($c->status === 'resolved')
              <span class="badge badge-success px-2 py-1">Terselesaikan</span>
            @else
              <span class="badge badge-secondary px-2 py-1">Selesai</span>
            @endif
          </td>
          <td style="font-size:12.5px; max-width: 300px;">
            <div class="text-dark">{{ $c->description }}</div>
            @if($c->resolution)
              <div class="mt-1 p-1 px-2 rounded bg-light border text-success" style="font-size:11.5px;">
                <strong>Resolusi:</strong> {{ $c->resolution }}
              </div>
            @endif
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="5" class="text-center py-4 text-muted">Belum ada keluhan member yang tercatat saat ini.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Catat Keluhan Baru -->
<div class="modal fade" id="addComplaintModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form action="{{ route('receptionist.complaints.store') }}" method="POST" class="modal-content" style="border-radius: 12px;">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title font-weight-bold">Form Catat Keluhan Member</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body p-4">
        <div class="form-group mb-3 position-relative" id="complaintMemberSearchWrapper">
          <label class="font-weight-bold text-dark mb-1" style="font-size: 13px;">Pilih Member yang Mengeluh *</label>
          <input type="hidden" name="member_id" id="complaintMemberId" required>
          <div class="input-group">
            <input type="text" id="complaintMemberSearchInput" class="form-control" placeholder="Ketik nama atau no. HP member..." autocomplete="off" style="border-radius: 8px;">
            <div class="input-group-append" id="complaintClearMemberBtnGroup" style="display: none;">
              <button class="btn btn-outline-secondary font-weight-bold" type="button" id="complaintBtnClearMember" title="Hapus Pilihan">&times;</button>
            </div>
          </div>
          <div id="complaintMemberSearchResults" class="list-group shadow position-absolute w-100 mt-1" style="display: none; max-height: 200px; overflow-y: auto; z-index: 1050; border-radius: 8px; text-align: left; left: 0;">
          </div>
        </div>

        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark mb-1" style="font-size: 13px;">Judul / Topik Keluhan *</label>
          <input type="text" name="title" class="form-control" placeholder="Contoh: AC ruang ganti mati / Alat treadmill error" required style="border-radius: 8px;">
        </div>

        <div class="form-group mb-0">
          <label class="font-weight-bold text-dark mb-1" style="font-size: 13px;">Deskripsi Lengkap Keluhan *</label>
          <textarea name="description" class="form-control" rows="3" placeholder="Jelaskan kronologi atau detail keluhan yang disampaikan member..." required style="border-radius: 8px;"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 8px;">Batal</button>
        <button type="submit" id="btnSubmitComplaint" class="btn btn-danger font-weight-bold px-4" style="border-radius: 8px;" disabled>Kirim Tiket Keluhan</button>
      </div>
    </form>
  </div>
</div>
@endsection

@section('scripts')
<script>
var complaintMemberList = [
  @foreach($members as $m)
    @php
      $expiredAt = $m->expired_at ? \Carbon\Carbon::parse($m->expired_at) : null;
      $mStatus = !$expiredAt ? 'Belum Aktif' : ($expiredAt->isPast() ? 'Expired' : 'Aktif');
      $mBadge = !$expiredAt ? 'badge-secondary' : ($expiredAt->isPast() ? 'badge-danger' : 'badge-success');
    @endphp
    {
      id: {{ $m->id }},
      name: "{{ addslashes($m->name) }}",
      phone: "{{ $m->phone ?? '' }}",
      status: "{{ $mStatus }}",
      badge: "{{ $mBadge }}"
    },
  @endforeach
];

$(document).ready(function() {
  var $cInput = $('#complaintMemberSearchInput');
  var $cResults = $('#complaintMemberSearchResults');
  var $cId = $('#complaintMemberId');
  var $cClearBtn = $('#complaintClearMemberBtnGroup');
  var $cSubmitBtn = $('#btnSubmitComplaint');

  function renderComplaintSearchResults(query) {
    $cResults.empty();
    query = (query || '').trim().toLowerCase();

    var matches = complaintMemberList.filter(function(item) {
      if (!query) return true;
      return item.name.toLowerCase().indexOf(query) !== -1 || item.phone.toLowerCase().indexOf(query) !== -1;
    });

    if (matches.length > 0) {
      $.each(matches.slice(0, 15), function(i, item) {
        var el = $('<a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2">')
          .html('<div><strong class="text-dark">' + item.name + '</strong></div><span class="badge ' + item.badge + '">' + item.status + '</span>')
          .on('click', function(e) {
            e.preventDefault();
            $cId.val(item.id);
            $cInput.val(item.name + ' — ' + item.status).prop('readonly', true);
            $cClearBtn.show();
            $cSubmitBtn.prop('disabled', false);
            $cResults.hide();
          });
        $cResults.append(el);
      });
    } else {
      $cResults.append('<div class="list-group-item text-muted py-2 text-center" style="font-size: 13px;">Member tidak ditemukan</div>');
    }

    $cResults.show();
  }

  $cInput.on('focus input', function() {
    if (!$cInput.prop('readonly')) {
      renderComplaintSearchResults($(this).val());
    }
  });

  $('#complaintBtnClearMember').on('click', function() {
    $cId.val('');
    $cInput.val('').prop('readonly', false).focus();
    $cClearBtn.hide();
    $cSubmitBtn.prop('disabled', true);
    renderComplaintSearchResults('');
  });

  $(document).on('click', function(e) {
    if (!$(e.target).closest('#complaintMemberSearchWrapper').length) {
      $cResults.hide();
    }
  });
});
</script>
@endsection
