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

    @if(!Auth::user() || !Auth::user()->isOwner())
    <button type="button" class="btn btn-sm btn-success font-weight-bold" data-toggle="modal" data-target="#registerMemberModal" style="border-radius: 8px;">
      + Register Member Baru
    </button>
    @endif
  </div>

  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
        <tr>
          <th>Kode PIN Akses</th>
          <th>Nama Member</th>
          <th>Kontak (Masked for Owner)</th>
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
              <span class="badge badge-dark px-3 py-2" style="font-size: 13px; letter-spacing: 1.5px; border-radius: 6px;">
                {{ \App\Helpers\PrivacyHelper::maskCode($m->access_code) }}
              </span>
            </td>
            <td>
              <div class="font-weight-bold text-dark">{{ $m->name }}</div>
              <small class="text-muted">{{ $m->email ? \App\Helpers\PrivacyHelper::maskEmail($m->email) : 'No email' }}</small>
            </td>
            <td style="font-size: 13px;" class="text-dark">{{ $m->phone ? \App\Helpers\PrivacyHelper::maskPhone($m->phone) : '-' }}</td>
            <td style="font-size: 13px;" class="text-dark">{{ $m->gender }}</td>
            <td>
              @php
                $expiredAt = $m->expired_at ? \Carbon\Carbon::parse($m->expired_at)->startOfDay() : null;
                $today     = \Carbon\Carbon::today();
                $isExpired = $expiredAt && $expiredAt->lt($today);
                $daysLeft  = $expiredAt && !$isExpired ? $today->diffInDays($expiredAt) : 0;
                $expiringRealSoon = !$isExpired && $daysLeft > 0 && $daysLeft <= 5;
              @endphp
              @if(!$expiredAt)
                <span class="badge badge-secondary px-2 py-1">Belum Aktif</span>
              @elseif($isExpired)
                <span class="badge badge-danger px-2 py-1">Expired</span>
              @elseif($expiringRealSoon)
                <span class="badge badge-warning text-dark px-2 py-1">Segera Habis</span>
              @elseif($m->status === 'active')
                <span class="badge badge-success px-2 py-1">Aktif</span>
              @else
                <span class="badge badge-secondary px-2 py-1">Non-Aktif</span>
              @endif
            </td>
            <td style="font-size: 13px;">
              @if($expiredAt)
                <span class="font-weight-bold {{ $isExpired ? 'text-danger' : ($expiringRealSoon ? 'text-warning' : 'text-dark') }}">
                  {{ $expiredAt->format('d M Y') }}
                </span>
                <small class="text-muted d-block" style="font-size:10px;">
                  {{ $isExpired ? 'Habis ' . $expiredAt->diffForHumans() : 'Berakhir ' . $expiredAt->diffForHumans() }}
                </small>
              @else
                <span class="text-muted">-</span>
                <small class="text-muted d-block" style="font-size:10px;">Belum ada membership</small>
              @endif
            </td>
            <td class="text-right">
              <button class="btn btn-sm btn-outline-info btn-history mr-1" data-id="{{ $m->id }}" data-name="{{ $m->name }}" style="border-radius: 6px;">Histori</button>
              @if(!Auth::user() || !Auth::user()->isOwner())
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
                <form action="{{ route('admin.members.destroy', $m->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data member ini?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius: 6px;">Hapus</button>
                </form>
              @endif
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
        <div class="alert alert-warning py-2 mb-3" style="font-size: 12px;">
          <strong>⚠️ Perhatian:</strong> Member baru akan terdaftar dengan status <strong>Belum Aktif</strong>.
          Setelah pendaftaran, member perlu <strong>membeli paket membership di POS Kasir</strong> agar statusnya berubah menjadi Aktif.
          Kode Akses PIN 6-digit akan digenerate otomatis oleh sistem.
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

        <div class="form-group mb-0">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Alamat</label>
          <textarea name="address" class="form-control" rows="2"></textarea>
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
          <table class="table table-bordered align-middle" style="font-size: 13px;">
            <thead class="bg-light">
              <tr>
                <th>Invoice</th>
                <th>Paket / Item Membership</th>
                <th>Durasi</th>
                <th>Total (Rp)</th>
                <th>Metode</th>
                <th>Tanggal Beli</th>
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
      $('#historyMemberTitle').text('Riwayat Membership: ' + memberName);
      $('#historyLoading').show();
      $('#historyTableContainer').hide();
      $('#historyModal').modal('show');

      $.get('/admin/members/' + memberId + '/history', function(data) {
        $('#historyLoading').hide();
        $('#historyTableContainer').show();
        var html = '';
        if (data.transactions.length > 0) {
          $.each(data.transactions, function(i, t) {
            // Build paket label from items
            var paketLabel = '-';
            var durasiLabel = '-';

            if (t.type === 'membership' && t.items && t.items.length > 0) {
              var membershipItem = t.items.find(function(item) {
                return item.item_name && item.item_name.toLowerCase().indexOf('membership') !== -1 ||
                       item.item_name && item.item_name.toLowerCase().indexOf('paket') !== -1 ||
                       item.item_name && item.item_name.toLowerCase().indexOf('pass') !== -1;
              });
              if (membershipItem) {
                paketLabel = membershipItem.item_name;
              } else {
                paketLabel = t.items.map(function(it) { return it.item_name; }).join(', ');
              }

              // Hitung durasi dari nama item
              var itemName = paketLabel.toLowerCase();
              if (itemName.indexOf('1 tahun') !== -1 || itemName.indexOf('12 bulan') !== -1) {
                durasiLabel = '12 Bulan (1 Tahun)';
              } else if (itemName.indexOf('3 bulan') !== -1) {
                durasiLabel = '3 Bulan';
              } else if (itemName.indexOf('6 bulan') !== -1) {
                durasiLabel = '6 Bulan';
              } else if (itemName.indexOf('1 bulan') !== -1) {
                durasiLabel = '1 Bulan (30 Hari)';
              } else if (itemName.indexOf('daily') !== -1 || itemName.indexOf('harian') !== -1) {
                durasiLabel = '1 Hari (Pass Harian)';
              } else {
                durasiLabel = '-';
              }
            } else if (t.type === 'inventory' && t.items && t.items.length > 0) {
              paketLabel = t.items.map(function(it) { return it.item_name + ' x' + it.qty; }).join(', ');
              durasiLabel = '<span class="badge badge-secondary">Produk Ritel</span>';
            }

            var tipeLabel = t.type === 'membership'
              ? '<span class="badge badge-success">Membership</span>'
              : '<span class="badge badge-info">Inventaris</span>';

            html += '<tr>';
            html += '<td><strong class="text-primary">' + t.invoice_number + '</strong><br>';
            html += '<small class="text-muted">' + tipeLabel + '</small></td>';
            html += '<td>' + paketLabel + '</td>';
            html += '<td>' + durasiLabel + '</td>';
            html += '<td class="font-weight-bold text-success">Rp ' + parseInt(t.total_amount).toLocaleString('id-ID') + '</td>';
            html += '<td><span class="badge badge-info">' + t.payment_method.toUpperCase() + '</span></td>';
            html += '<td>' + new Date(t.created_at).toLocaleDateString('id-ID', {day:'2-digit',month:'short',year:'numeric'}) + '</td>';
            html += '</tr>';
          });
        } else {
          html = '<tr><td colspan="6" class="text-center text-muted py-3">Member ini belum pernah melakukan transaksi.</td></tr>';
        }
        $('#historyTableBody').html(html);
      });
    });
  });
</script>
@endsection
