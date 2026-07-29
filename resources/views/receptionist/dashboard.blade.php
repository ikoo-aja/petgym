@extends('layouts.admin')

@section('title', 'Dashboard Resepsionis &mdash; PetGym')
@section('page_title', 'Dashboard Resepsionis / Frontdesk')
@section('page_subtitle', 'Antarmuka cepat layanan frontdesk, PIN check-in, registrasi member, dan POS kasir')

@section('content')
<!-- Shift Status Banner -->
@if(!$activeShift)
<div class="alert alert-danger shadow-sm mb-4 d-flex justify-content-between align-items-center">
  <div>
    <strong>⚠️ Shift Belum Dibuka!</strong> Harap buka shift kasir terlebih dahulu untuk mencatat nominal kas laci meja depan sebelum memproses transaksi kasir.
  </div>
  <a href="{{ route('receptionist.shifts') }}" class="btn btn-sm btn-danger font-weight-bold">Buka Shift Sekarang</a>
</div>
@else
<div class="alert alert-success shadow-sm mb-4 d-flex justify-content-between align-items-center">
  <div>
    <strong>🟢 Shift Aktif:</strong> Terbuka sejak {{ $activeShift->opened_at->format('H:i') }} WIB dengan kas awal sebesar <strong>Rp {{ number_format($activeShift->start_cash, 0, ',', '.') }}</strong>.
  </div>
  <a href="{{ route('receptionist.shifts') }}" class="btn btn-sm btn-outline-success font-weight-bold">Kelola Shift</a>
</div>
@endif

<!-- Quick Action Buttons for Receptionist -->
<div class="row mb-4">
  <div class="col-md-3">
    <a href="{{ route('admin.checkin.index') }}" class="card-custom d-block text-decoration-none text-center py-4 bg-primary text-white" style="border-radius: 12px; transition: transform 0.2s ease;">
      <h4 class="font-weight-bold mb-1"><span class="icon-check"></span> Check-In Absensi</h4>
      <p class="mb-0 text-white-50" style="font-size: 12px;">Antarmuka PIN & Manual</p>
    </a>
  </div>
  <div class="col-md-3">
    <a href="{{ route('admin.members.index') }}" class="card-custom d-block text-decoration-none text-center py-4 bg-success text-white" style="border-radius: 12px; transition: transform 0.2s ease;">
      <h4 class="font-weight-bold mb-1"><span class="icon-person"></span> Register Member</h4>
      <p class="mb-0 text-white-50" style="font-size: 12px;">Pendaftaran & Generate PIN</p>
    </a>
  </div>
  <div class="col-md-3">
    <a href="{{ route('admin.pos.index') }}" class="card-custom d-block text-decoration-none text-center py-4 bg-info text-white" style="border-radius: 12px; transition: transform 0.2s ease;">
      <h4 class="font-weight-bold mb-1"><span class="icon-shopping-cart"></span> POS Kasir & Struk</h4>
      <p class="mb-0 text-white-50" style="font-size: 12px;">Transaksi Kasir & Produk</p>
    </a>
  </div>
  <div class="col-md-3">
    <a href="{{ route('receptionist.lockers') }}" class="card-custom d-block text-decoration-none text-center py-4 bg-secondary text-white" style="border-radius: 12px; transition: transform 0.2s ease;">
      <h4 class="font-weight-bold mb-1"><span class="icon-settings"></span> Manajemen Loker</h4>
      <p class="mb-0 text-white-50" style="font-size: 12px;">Peminjaman & Pengembalian</p>
    </a>
  </div>
</div>

<div class="row mb-4">
  <!-- Today Check-Ins -->
  <div class="col-md-6">
    <div class="card-custom">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="font-weight-bold text-dark mb-0">Member Checked-In Hari Ini</h6>
        <span class="badge badge-primary font-weight-bold px-3 py-2" style="border-radius: 12px;">{{ count($todayCheckIns) }} Kunjungan</span>
      </div>

      <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
            <tr>
              <th>Jam</th>
              <th>Member</th>
              <th>PIN Akses</th>
              <th>Metode</th>
            </tr>
          </thead>
          <tbody>
            @forelse($todayCheckIns as $ci)
              <tr>
                <td class="font-weight-bold text-dark" style="font-size: 13px;">{{ $ci->checked_in_at ? $ci->checked_in_at->format('H:i:s') : '-' }}</td>
                <td class="font-weight-bold text-dark">{{ $ci->member ? $ci->member->name : 'Member' }}</td>
                <td><span class="badge badge-secondary" style="font-size: 12px;">{{ $ci->access_code }}</span></td>
                <td><span class="badge badge-success">{{ $ci->check_in_method === 'code' ? 'PIN Numpad' : 'Manual' }}</span></td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center py-4 text-muted">Belum ada kunjungan hari ini.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- PT checkin & Class Booking Execution -->
  <div class="col-md-6">
    <div class="card-custom">
      <h6 class="font-weight-bold text-dark mb-3">Check-In Sesi Personal Trainer (PT)</h6>
      
      <form action="{{ route('receptionist.pt.checkin') }}" method="POST" class="p-3 bg-light rounded border mb-2">
        @csrf
        <div class="row">
          <div class="col-md-6 form-group mb-2">
            <label class="font-weight-bold mb-1" style="font-size:12px;">Pilih Member *</label>
            <select name="member_id" class="form-control form-control-sm" required>
              <option value="">-- Pilih Member --</option>
              @foreach($activeMembers as $m)
                <option value="{{ $m->id }}">{{ $m->name }} (PIN: {{ $m->access_code }})</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6 form-group mb-2">
            <label class="font-weight-bold mb-1" style="font-size:12px;">Pilih Trainer *</label>
            <select name="trainer_id" class="form-control form-control-sm" required>
              <option value="">-- Pilih Trainer --</option>
              @foreach($standbyTrainers as $t)
                <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->specialization }})</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="form-group mb-2">
          <label class="font-weight-bold mb-1" style="font-size:12px;">Tanggal Pertemuan *</label>
          <input type="date" name="session_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
        </div>
        <button type="submit" class="btn btn-sm btn-success btn-block font-weight-bold">Verifikasi & Konfirmasi Sesi PT</button>
      </form>
    </div>
  </div>
</div>

<div class="row">
  <!-- 10. Pusat Informasi Cepat (Daily Info Hub - Read Only) -->
  <div class="col-md-7">
    <div class="card-custom">
      <h6 class="font-weight-bold text-dark mb-3"><span class="icon-calendar"></span> Daily Info Hub: Jadwal Kelas Dasar Gym</h6>
      <div class="table-responsive">
        <table class="table table-bordered table-striped">
          <thead class="bg-light text-muted">
            <tr>
              <th>Hari</th>
              <th>Nama Kelas</th>
              <th>Waktu & Jam</th>
              <th>Ruangan</th>
              <th>Trainer</th>
            </tr>
          </thead>
          <tbody>
            @forelse($dailyClasses as $dc)
              <tr>
                <td><span class="badge badge-info">{{ $dc->day }}</span></td>
                <td class="font-weight-bold text-dark">{{ $dc->name }}</td>
                <td style="font-size: 13px;">{{ substr($dc->start_time, 0, 5) }} - {{ substr($dc->end_time, 0, 5) }} WIB ({{ $dc->duration_minutes ?? 60 }}m)</td>
                <td>{{ $dc->room ?? 'N/A' }}</td>
                <td>{{ $dc->trainer ? $dc->trainer->name : 'N/A' }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center text-muted">Belum ada jadwal kelas terdaftar.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Recent Transactions processed by Receptionist -->
  <div class="col-md-5">
    <div class="card-custom">
      <h6 class="font-weight-bold text-dark mb-3">Transaksi Kasir Anda Hari Ini</h6>
      <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
            <tr>
              <th>Invoice</th>
              <th>Total</th>
              <th>Status Void</th>
              <th class="text-right">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($recentTransactions as $tx)
              <tr>
                <td>
                  <div class="font-weight-bold text-primary" style="font-size: 12.5px;">{{ $tx->invoice_number }}</div>
                  <small class="text-muted">{{ $tx->member ? $tx->member->name : 'Umum' }}</small>
                </td>
                <td class="font-weight-bold text-success" style="font-size: 13px;">Rp {{ number_format($tx->total_amount, 0, ',', '.') }}</td>
                <td>
                  @if($tx->void_status === 'pending')
                    <span class="badge badge-warning text-dark">Pending Void</span>
                  @elseif($tx->void_status === 'approved')
                    <span class="badge badge-danger">Voided</span>
                  @elseif($tx->void_status === 'rejected')
                    <span class="badge badge-secondary">Void Rejected</span>
                  @else
                    <span class="badge badge-success">Selesai</span>
                  @endif
                </td>
                <td class="text-right">
                  @if($tx->void_status === 'none')
                    <button class="btn btn-xs btn-outline-danger btn-void-req"
                      data-id="{{ $tx->id }}"
                      data-invoice="{{ $tx->invoice_number }}"
                      style="border-radius: 6px;">Void</button>
                  @else
                    <span class="text-muted font-italic" style="font-size:12px;">No Action</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center py-4 text-muted">Belum ada transaksi kasir.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal Request Void -->
<div class="modal fade" id="voidRequestModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="voidRequestForm" action="" method="POST" class="modal-content" style="border-radius: 12px;">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title font-weight-bold">Ajukan Pembatalan (Void) Transaksi</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="alert alert-warning" style="font-size:13px;">
          <strong>Pemberitahuan:</strong> Pembatalan transaksi memerlukan persetujuan otorisasi dari Manager. Sisa stok produk ritel akan otomatis dikembalikan setelah disetujui.
        </div>
        <p class="font-weight-bold text-dark">Invoice: <span id="voidInvoiceNum"></span></p>
        <div class="form-group mb-0">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Alasan Void Transaksi *</label>
          <textarea name="void_reason" class="form-control" rows="3" placeholder="Tuliskan alasan pembatalan, misal: Salah input nominal kasir / double click" required></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-danger font-weight-bold">Kirim Pengajuan Void</button>
      </div>
    </form>
  </div>
</div>
@endsection

@section('scripts')
<script>
  $(document).ready(function() {
    $('.btn-void-req').on('click', function() {
      var id = $(this).data('id');
      var inv = $(this).data('invoice');
      $('#voidInvoiceNum').text(inv);
      $('#voidRequestForm').attr('action', '/admin/pos/void/' + id);
      $('#voidRequestModal').modal('show');
    });
  });
</script>
@endsection
