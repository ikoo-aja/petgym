@extends('layouts.admin')

@section('title', 'Manajemen Member &mdash; PetGym')
@section('page_title', 'Manajemen Member Gym')
@section('page_subtitle', 'Kelola pendaftaran member baru, kode akses statis, dan riwayat transaksi')

@section('content')
<div class="card-custom">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <form action="{{ route('manager.members.index') }}" method="GET" class="form-inline">
      <input type="text" name="search" class="form-control form-control-sm mr-2" placeholder="Cari nama / nomor HP..." value="{{ request('search') }}" style="width: 260px; border-radius: 8px;">
      <select name="status" class="form-control form-control-sm mr-2" style="border-radius: 8px;">
        <option value="">-- Semua Status --</option>
        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Non-Aktif</option>
      </select>
      <button type="submit" class="btn btn-sm btn-primary font-weight-bold" style="border-radius: 8px;">Cari</button>
    </form>

    @if(Auth::user() && Auth::user()->isReceptionist())
    <button type="button" class="btn btn-sm btn-success font-weight-bold" data-toggle="modal" data-target="#registerMemberModal" style="border-radius: 8px;">
      + Register Member Baru
    </button>
    @endif
  </div>

  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
        <tr>
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
              @if(Auth::user() && (Auth::user()->isManager() || Auth::user()->isSuperadmin() || Auth::user()->isAdmin()))
                @if($m->status !== 'active')
                  <form action="{{ route('manager.members.approve', $m->id) }}" method="POST" class="d-inline mr-1">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-success font-weight-bold" style="border-radius: 6px;">
                      Setujui
                    </button>
                  </form>
                @endif
                <button class="btn btn-sm btn-outline-primary mr-1 btn-edit-member"
                  data-id="{{ $m->id }}"
                  data-name="{{ $m->name }}"
                  data-email="{{ $m->email }}"
                  data-phone="{{ $m->phone }}"
                  data-gender="{{ $m->gender }}"
                  data-address="{{ $m->address }}"
                  data-status="{{ $m->status }}"
                  data-expired_at="{{ $m->expired_at ? $m->expired_at->format('Y-m-d') : '' }}"
                  style="border-radius: 6px;">Ubah</button>
                <form action="{{ route('manager.members.destroy', $m->id) }}" method="POST" class="d-inline" data-confirm="Hapus data member ini?">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius: 6px;">Hapus</button>
                </form>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center py-4 text-muted">Belum ada data member. Silakan daftarkan member baru.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-3">
    {{ $members->appends(request()->query())->links() }}
  </div>
</div>

<!-- Modal Register Member via Kasir POS -->
<div class="modal fade" id="registerMemberModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <form action="{{ route('manager.members.store') }}" method="POST" class="modal-content" style="border-radius: 12px;">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title font-weight-bold">Pendaftaran & Pembayaran Member Baru</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body p-4">
        <div class="alert alert-info py-2 mb-4" style="font-size: 13px;">
          Pendaftaran member baru terintegrasi langsung dengan Kasir POS. Akun member akan langsung aktif dengan kode akses PIN unik setelah pembayaran paket diselesaikan.
        </div>

        <div class="row">
          <!-- Kolom Data Calon Member -->
          <div class="col-md-6 border-right">
            <h6 class="font-weight-bold text-dark mb-3">1. Data Calon Member</h6>
            <div class="form-group mb-3">
              <label class="font-weight-bold text-dark" style="font-size: 13px;">Nama Lengkap *</label>
              <input type="text" name="name" class="form-control" placeholder="Nama lengkap member" required style="border-radius: 8px;">
            </div>

            <div class="form-group mb-3">
              <label class="font-weight-bold text-dark" style="font-size: 13px;">Nomor Telepon / WhatsApp *</label>
              <input type="text" name="phone" class="form-control" placeholder="08123456789" required style="border-radius: 8px;">
            </div>

            <div class="form-group mb-3">
              <label class="font-weight-bold text-dark" style="font-size: 13px;">Email (Opsional untuk login akun portal)</label>
              <input type="email" name="email" class="form-control" placeholder="member@example.com" style="border-radius: 8px;">
            </div>

            <div class="form-group mb-3">
              <label class="font-weight-bold text-dark" style="font-size: 13px;">Jenis Kelamin *</label>
              <select name="gender" class="form-control" required style="border-radius: 8px;">
                <option value="Laki-laki">Laki-laki</option>
                <option value="Perempuan">Perempuan</option>
              </select>
            </div>

            <div class="form-group mb-0">
              <label class="font-weight-bold text-dark" style="font-size: 13px;">Alamat</label>
              <textarea name="address" class="form-control" rows="2" placeholder="Alamat domisili" style="border-radius: 8px;"></textarea>
            </div>
          </div>

          <!-- Kolom Paket & Pembayaran Kasir -->
          <div class="col-md-6">
            <h6 class="font-weight-bold text-dark mb-3">2. Paket Keanggotaan & Pembayaran</h6>
            
            <div class="form-group mb-3">
              <label class="font-weight-bold text-dark" style="font-size: 13px;">Pilihan Paket Membership *</label>
              <select name="package_duration" id="regPackageSelect" class="form-control" required style="border-radius: 8px;">
                <option value="1" data-price="500000" data-days="30">Paket 1 Bulan — Rp 500.000 (Tier Basic)</option>
                <option value="3" data-price="1350000" data-days="90">Paket 3 Bulan — Rp 1.350.000 (Tier Standard)</option>
                <option value="12" data-price="4500000" data-days="365">Paket 1 Tahun — Rp 4.500.000 (Tier Premium)</option>
              </select>
            </div>

            <div class="card bg-light border p-3 mb-3" style="border-radius: 10px;">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="text-muted" style="font-size: 13px;">Total Tagihan Kasir:</span>
                <span class="font-weight-bold text-dark" id="regPackagePriceText" style="font-size: 16px;">Rp 500.000</span>
              </div>
              <div class="d-flex justify-content-between align-items-center">
                <span class="text-muted" style="font-size: 13px;">Masa Berlaku:</span>
                <span class="text-dark font-weight-bold" id="regPackageDurationText" style="font-size: 13px;">30 Hari</span>
              </div>
            </div>

            <div class="form-group mb-3">
              <label class="font-weight-bold text-dark" style="font-size: 13px;">Metode Pembayaran *</label>
              <select name="payment_method" id="regPaymentMethod" class="form-control" required style="border-radius: 8px;">
                <option value="cash">Tunai (Cash)</option>
                <option value="qris">QRIS Standar</option>
                <option value="transfer">Bank Transfer</option>
              </select>
            </div>

            <div class="form-group mb-3" id="regCashGroup">
              <label class="font-weight-bold text-dark" style="font-size: 13px;">Nominal Uang Tunai Diterima (Rp) *</label>
              <input type="number" name="cash_paid" id="regCashPaid" class="form-control" placeholder="500000" min="0" style="border-radius: 8px;" value="500000">
              <div class="d-flex justify-content-between align-items-center mt-2 p-2 rounded bg-white border" style="font-size: 13px;">
                <span class="text-muted">Kembalian:</span>
                <strong class="text-success" id="regCashChangeText">Rp 0</strong>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 8px;">Batal</button>
        <button type="submit" class="btn btn-success font-weight-bold px-4" style="border-radius: 8px;">Bayar di Kasir & Buat Akun</button>
      </div>
    </form>
  </div>
</div>

@if(session('new_registered_member'))
@php $newMember = session('new_registered_member'); @endphp
<!-- Modal Bukti Pembayaran & Akun Member Baru -->
<div class="modal fade" id="newMemberSuccessModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content" style="border-radius: 14px;">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title font-weight-bold">Pendaftaran & Pembayaran Berhasil</h5>
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body p-4">
        <div class="text-center mb-3">
          <span class="badge badge-success px-3 py-2 font-weight-bold" style="font-size: 12px; border-radius: 6px;">LUNAS &bull; {{ $newMember['invoice_number'] }}</span>
          <h4 class="font-weight-bold text-dark mt-2 mb-0">{{ $newMember['name'] }}</h4>
          <p class="text-muted" style="font-size: 13px;">{{ $newMember['package_name'] }} &bull; Tier {{ $newMember['tier'] }}</p>
        </div>

        <div class="card bg-light border p-3 mb-3 text-center" style="border-radius: 10px;">
          <small class="text-muted font-weight-bold text-uppercase" style="letter-spacing: 1px;">Kode Akses PIN Member (6 Digit)</small>
          <div class="font-weight-bold text-primary mt-1" style="font-size: 28px; letter-spacing: 6px;">
            {{ $newMember['pin'] }}
          </div>
          <small class="text-muted mt-1">Berikan PIN ini kepada member untuk akses pintu masuk gym dan login portal.</small>
        </div>

        <table class="table table-sm table-borderless mb-0" style="font-size: 13px;">
          <tr>
            <td class="text-muted">Masa Aktif Hingga:</td>
            <td class="text-right font-weight-bold text-dark">{{ $newMember['expired_at'] }}</td>
          </tr>
          <tr>
            <td class="text-muted">Nomor WhatsApp / HP:</td>
            <td class="text-right text-dark">{{ $newMember['phone'] }}</td>
          </tr>
          <tr>
            <td class="text-muted">Metode Pembayaran:</td>
            <td class="text-right font-weight-bold text-dark">{{ $newMember['payment_method'] }}</td>
          </tr>
          <tr>
            <td class="text-muted">Total Tagihan:</td>
            <td class="text-right font-weight-bold text-success">Rp {{ number_format($newMember['total_amount'], 0, ',', '.') }}</td>
          </tr>
          @if($newMember['payment_method'] === 'CASH')
          <tr>
            <td class="text-muted">Uang Diterima:</td>
            <td class="text-right text-dark">Rp {{ number_format($newMember['cash_paid'], 0, ',', '.') }}</td>
          </tr>
          <tr>
            <td class="text-muted">Kembalian:</td>
            <td class="text-right font-weight-bold text-primary">Rp {{ number_format($newMember['cash_change'], 0, ',', '.') }}</td>
          </tr>
          @endif
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 8px;">Tutup</button>
        <button type="button" class="btn btn-primary font-weight-bold" onclick="window.print();" style="border-radius: 8px;">Cetak Bukti Pembayaran</button>
      </div>
    </div>
  </div>
</div>
@endif

<!-- Modal Edit Member -->
<div class="modal fade" id="editMemberModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="editMemberForm" action="" method="POST" class="modal-content" style="border-radius: 12px;">
      @csrf
      @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title font-weight-bold">Ubah Data Member</h5>
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
    @if(session('new_registered_member'))
      $('#newMemberSuccessModal').modal('show');
    @endif

    // Paket & Pembayaran Kasir POS untuk Register Member
    function updateRegisterPackagePreview() {
      var selected = $('#regPackageSelect option:selected');
      var price = parseInt(selected.data('price')) || 0;
      var days = selected.data('days') || 30;

      $('#regPackagePriceText').text('Rp ' + price.toLocaleString('id-ID'));
      $('#regPackageDurationText').text(days + ' Hari');

      var paymentMethod = $('#regPaymentMethod').val();
      if (paymentMethod === 'cash') {
        var currentCash = parseInt($('#regCashPaid').val()) || 0;
        if (currentCash < price) {
          $('#regCashPaid').val(price);
          currentCash = price;
        }
        var change = currentCash - price;
        $('#regCashChangeText').text('Rp ' + (change >= 0 ? change.toLocaleString('id-ID') : 0));
      }
    }

    $('#regPackageSelect').on('change', function() {
      updateRegisterPackagePreview();
    });

    $('#regPaymentMethod').on('change', function() {
      if ($(this).val() === 'cash') {
        $('#regCashGroup').show();
        updateRegisterPackagePreview();
      } else {
        $('#regCashGroup').hide();
      }
    });

    $('#regCashPaid').on('input', function() {
      var selected = $('#regPackageSelect option:selected');
      var price = parseInt(selected.data('price')) || 0;
      var paid = parseInt($(this).val()) || 0;
      var change = paid - price;
      if (change < 0) {
        $('#regCashChangeText').html('<span class="text-danger">Kurang Rp ' + Math.abs(change).toLocaleString('id-ID') + '</span>');
      } else {
        $('#regCashChangeText').html('<span class="text-success">Rp ' + change.toLocaleString('id-ID') + '</span>');
      }
    });

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
