@extends('layouts.admin')

@section('title', 'Manajemen Member &mdash; PetGym')
@section('page_title', 'Manajemen Member Gym')
@section('page_subtitle', 'Kelola pendaftaran member baru, kode akses statis, dan riwayat transaksi')

@section('content')
<div class="card-custom">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <form action="{{ route('admin.members.index') }}" method="GET" class="form-inline">
      <input type="text" name="search" class="form-control form-control-sm mr-2" placeholder="Cari nama / HP / Kode PIN..." value="{{ request('search') }}" style="width: 260px; border-radius: 8px;">
      <select name="status" class="form-control form-control-sm mr-2" style="border-radius: 8px;">
        <option value="">-- Semua Status --</option>
        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Non-Aktif</option>
      </select>
      <button type="submit" class="btn btn-sm btn-primary font-weight-bold" style="border-radius: 8px;">Cari</button>
    </form>

    <button type="button" class="btn btn-sm btn-success font-weight-bold" data-toggle="modal" data-target="#registerMemberModal" style="border-radius: 8px;">
      + Register Member Baru
    </button>
  </div>

  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
        <tr>
          <th>Kode PIN Akses</th>
          <th>Nama Member</th>
          <th>Kontak</th>
          <th>Gender</th>
          <th>Status</th>
          <th>Masa Aktif</th>
          <th class="text-right">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($members as $m)
          <tr>
            <td>
              <span class="badge badge-dark px-3 py-2" style="font-size: 13px; letter-spacing: 1.5px; border-radius: 6px;">{{ $m->access_code }}</span>
            </td>
            <td>
              <div class="font-weight-bold text-dark">{{ $m->name }}</div>
              <small class="text-muted">{{ $m->email ?? 'No email' }}</small>
            </td>
            <td style="font-size: 13px;">{{ $m->phone ?? '-' }}</td>
            <td style="font-size: 13px;">{{ $m->gender }}</td>
            <td>
              @if($m->status === 'active')
                <span class="badge badge-success px-2 py-1">Aktif</span>
              @else
                <span class="badge badge-danger px-2 py-1">Non-Aktif</span>
              @endif
            </td>
            <td style="font-size: 13px;">
              {{ $m->expired_at ? $m->expired_at->format('d M Y') : '-' }}
              @if($m->is_expired)
                <small class="text-danger font-weight-bold d-block">Expired</small>
              @endif
            </td>
            <td class="text-right">
              <button class="btn btn-sm btn-outline-primary mr-1 btn-edit-member"
                data-id="{{ $m->id }}"
                data-name="{{ $m->name }}"
                data-email="{{ $m->email }}"
                data-phone="{{ $m->phone }}"
                data-gender="{{ $m->gender }}"
                data-address="{{ $m->address }}"
                data-status="{{ $m->status }}"
                data-expired_at="{{ $m->expired_at ? $m->expired_at->format('Y-m-d') : '' }}"
                style="border-radius: 6px;">Edit</button>
              <button class="btn btn-sm btn-outline-info btn-history mr-1" data-id="{{ $m->id }}" data-name="{{ $m->name }}" style="border-radius: 6px;">Histori</button>
              <form action="{{ route('admin.members.destroy', $m->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data member ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius: 6px;">Hapus</button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="text-center py-4 text-muted">Belum ada data member. Silakan daftarkan member baru.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-3">
    {{ $members->appends(request()->query())->links() }}
  </div>
</div>

<!-- Modal Register Member -->
<div class="modal fade" id="registerMemberModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form action="{{ route('admin.members.store') }}" method="POST" class="modal-content" style="border-radius: 12px;">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title font-weight-bold">Form Pendaftaran Member Baru</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="alert alert-info py-2" style="font-size: 12px;">
          Kode Akses PIN unik 6-digit akan dibuatkan secara otomatis oleh sistem saat pendaftaran awal.
        </div>

        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Nama Lengkap *</label>
          <input type="text" name="name" class="form-control" placeholder="Contoh: Budi Santoso" required>
        </div>

        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Nomor Telepon / WhatsApp</label>
          <input type="text" name="phone" class="form-control" placeholder="08123456789">
        </div>

        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Email</label>
          <input type="email" name="email" class="form-control" placeholder="budi@example.com">
        </div>

        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Jenis Kelamin *</label>
          <select name="gender" class="form-control" required>
            <option value="Laki-laki">Laki-laki</option>
            <option value="Perempuan">Perempuan</option>
          </select>
        </div>

        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Alamat</label>
          <textarea name="address" class="form-control" rows="2"></textarea>
        </div>

        <div class="form-group mb-0">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Masa Aktif Awal s/d</label>
          <input type="date" name="expired_at" class="form-control" value="{{ \Carbon\Carbon::today()->addMonth()->format('Y-m-d') }}">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-success font-weight-bold">Simpan & Generate PIN</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit Member -->
<div class="modal fade" id="editMemberModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="editMemberForm" action="" method="POST" class="modal-content" style="border-radius: 12px;">
      @csrf
      @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title font-weight-bold">Edit Data Member</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Nama Lengkap *</label>
          <input type="text" name="name" id="editMemberName" class="form-control" required>
        </div>

        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Nomor Telepon / WhatsApp</label>
          <input type="text" name="phone" id="editMemberPhone" class="form-control">
        </div>

        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Email</label>
          <input type="email" name="email" id="editMemberEmail" class="form-control">
        </div>

        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Jenis Kelamin *</label>
          <select name="gender" id="editMemberGender" class="form-control" required>
            <option value="Laki-laki">Laki-laki</option>
            <option value="Perempuan">Perempuan</option>
          </select>
        </div>

        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Status Membership *</label>
          <select name="status" id="editMemberStatus" class="form-control" required>
            <option value="active">Aktif</option>
            <option value="inactive">Non-Aktif</option>
            <option value="expiring_soon">Expiring Soon</option>
          </select>
        </div>

        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Alamat</label>
          <textarea name="address" id="editMemberAddress" class="form-control" rows="2"></textarea>
        </div>

        <div class="form-group mb-0">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Tanggal Expired</label>
          <input type="date" name="expired_at" id="editMemberExpiredAt" class="form-control">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary font-weight-bold">Update Data Member</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Histori Transaksi Member -->
<div class="modal fade" id="historyModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content" style="border-radius: 12px;">
      <div class="modal-header">
        <h5 class="modal-title font-weight-bold" id="historyMemberTitle">Riwayat Transaksi Member</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div id="historyLoading" class="text-center py-4 text-muted">Memuat data histori...</div>
        <div id="historyTableContainer" style="display: none;">
          <table class="table table-bordered align-middle">
            <thead class="bg-light">
              <tr>
                <th>Invoice</th>
                <th>Tipe</th>
                <th>Total (Rp)</th>
                <th>Metode Bayar</th>
                <th>Tanggal</th>
              </tr>
            </thead>
            <tbody id="historyTableBody"></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  $(document).ready(function() {
    $('.btn-edit-member').on('click', function() {
      var id = $(this).data('id');
      $('#editMemberName').val($(this).data('name'));
      $('#editMemberEmail').val($(this).data('email'));
      $('#editMemberPhone').val($(this).data('phone'));
      $('#editMemberGender').val($(this).data('gender'));
      $('#editMemberAddress').val($(this).data('address'));
      $('#editMemberStatus').val($(this).data('status'));
      $('#editMemberExpiredAt').val($(this).data('expired_at'));
      $('#editMemberForm').attr('action', '/admin/members/' + id);
      $('#editMemberModal').modal('show');
    });

    $('.btn-history').on('click', function() {
      var memberId = $(this).data('id');
      var memberName = $(this).data('name');
      $('#historyMemberTitle').text('Riwayat Transaksi: ' + memberName);
      $('#historyLoading').show();
      $('#historyTableContainer').hide();
      $('#historyModal').modal('show');

      $.get('/admin/members/' + memberId + '/history', function(data) {
        $('#historyLoading').hide();
        $('#historyTableContainer').show();
        var html = '';
        if (data.transactions.length > 0) {
          $.each(data.transactions, function(i, t) {
            html += '<tr>';
            html += '<td><strong class="text-primary">' + t.invoice_number + '</strong></td>';
            html += '<td>' + t.type + '</td>';
            html += '<td>Rp ' + parseInt(t.total_amount).toLocaleString('id-ID') + '</td>';
            html += '<td><span class="badge badge-info">' + t.payment_method + '</span></td>';
            html += '<td>' + new Date(t.created_at).toLocaleDateString('id-ID') + '</td>';
            html += '</tr>';
          });
        } else {
          html = '<tr><td colspan="5" class="text-center text-muted">Belum ada riwayat transaksi.</td></tr>';
        }
        $('#historyTableBody').html(html);
      });
    });
  });
</script>
@endsection
