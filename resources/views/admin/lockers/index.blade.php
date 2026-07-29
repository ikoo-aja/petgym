@extends('layouts.admin')

@section('title', 'Master Loker Gym &mdash; PetGym')
@section('page_title', 'Master Data Loker Gym')
@section('page_subtitle', 'Setup kapasitas loker, pendaftaran loker baru, dan blokir loker rusak oleh Admin')

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
  <!-- Form Tambah Loker Baru -->
  <div class="col-md-4">
    <div class="card-custom mb-4">
      <h6 class="font-weight-bold text-dark mb-3">➕ Daftarkan Loker Baru</h6>
      <form action="{{ route('admin.lockers.store') }}" method="POST">
        @csrf
        <div class="form-group mb-2">
          <label class="font-weight-bold mb-1" style="font-size:12px;">Nomor Loker *</label>
          <input type="text" name="locker_number" class="form-control form-control-sm" placeholder="Misal: 01, 02, A1, B3" required>
        </div>
        <div class="form-group mb-3">
          <label class="font-weight-bold mb-1" style="font-size:12px;">Status Awal *</label>
          <select name="status" class="form-control form-control-sm">
            <option value="tersedia">Tersedia</option>
            <option value="rusak">Rusak (Blokir)</option>
          </select>
        </div>
        <button type="submit" class="btn btn-primary btn-block btn-sm font-weight-bold">Tambah Loker</button>
      </form>

      <hr>

      <h6 class="font-weight-bold text-dark mb-3 mt-3">⚡ Tambah Loker Massal</h6>
      <form action="" method="POST" id="bulkLockerForm">
        @csrf
        <div class="form-group mb-2">
          <label class="font-weight-bold mb-1" style="font-size:12px;">Dari Nomor *</label>
          <input type="number" id="bulkStart" class="form-control form-control-sm" placeholder="1" min="1" required>
        </div>
        <div class="form-group mb-2">
          <label class="font-weight-bold mb-1" style="font-size:12px;">Sampai Nomor *</label>
          <input type="number" id="bulkEnd" class="form-control form-control-sm" placeholder="50" min="1" required>
        </div>
        <button type="button" class="btn btn-success btn-block btn-sm font-weight-bold" id="btnBulkAdd">
          Generate & Tambah Loker Massal
        </button>
        <small class="text-muted">Loker akan ditambah satu per satu secara otomatis.</small>
      </form>
    </div>
  </div>

  <!-- Daftar Master Loker -->
  <div class="col-md-8">
    <div class="card-custom">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="font-weight-bold text-dark mb-0">📦 Daftar Master Loker</h6>
        <span class="badge badge-info font-weight-bold px-3 py-2" style="border-radius:10px;">Total: {{ count($lockers) }} Loker</span>
      </div>

      <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase; position: sticky; top: 0; z-index: 1;">
            <tr>
              <th>No. Loker</th>
              <th>Status</th>
              <th class="text-right">Aksi Admin</th>
            </tr>
          </thead>
          <tbody>
            @forelse($lockers as $l)
            <tr>
              <td>
                <span class="font-weight-bold text-dark" style="font-size:15px;">{{ $l->locker_number }}</span>
              </td>
              <td>
                @if($l->status === 'tersedia')
                  <span class="badge badge-success px-2 py-1">Tersedia</span>
                @elseif($l->status === 'terpakai')
                  <span class="badge badge-primary px-2 py-1">Terpakai</span>
                @else
                  <span class="badge badge-danger px-2 py-1">Rusak / Diblokir</span>
                @endif
              </td>
              <td class="text-right">
                @if($l->status === 'tersedia')
                  <form action="{{ route('admin.lockers.update', $l->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Blokir loker ini? Status akan menjadi Rusak dan tidak bisa dipakai Resepsionis.')">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="locker_number" value="{{ $l->locker_number }}">
                    <input type="hidden" name="status" value="rusak">
                    <button type="submit" class="btn btn-xs btn-outline-danger font-weight-bold" style="border-radius:6px;">🔒 Blokir (Rusak)</button>
                  </form>
                @elseif($l->status === 'rusak')
                  <form action="{{ route('admin.lockers.update', $l->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Aktifkan kembali loker ini?')">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="locker_number" value="{{ $l->locker_number }}">
                    <input type="hidden" name="status" value="tersedia">
                    <button type="submit" class="btn btn-xs btn-outline-success font-weight-bold" style="border-radius:6px;">🔓 Aktifkan</button>
                  </form>
                @else
                  <span class="text-muted font-italic" style="font-size:12px;">Sedang dipakai member</span>
                @endif

                @if($l->status !== 'terpakai')
                <form action="{{ route('admin.lockers.destroy', $l->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus loker ini dari database secara permanen?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-xs btn-outline-dark font-weight-bold ml-1" style="border-radius:6px;">🗑 Hapus</button>
                </form>
                @endif
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="3" class="text-center py-4 text-muted">Belum ada data master loker. Silakan tambah loker baru di form sebelah kiri.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
  $('#btnBulkAdd').on('click', async function() {
    var start = parseInt($('#bulkStart').val());
    var end = parseInt($('#bulkEnd').val());

    if (isNaN(start) || isNaN(end) || start > end) {
      alert('Nomor awal harus lebih kecil dari nomor akhir.');
      return;
    }

    if ((end - start + 1) > 100) {
      alert('Maksimal 100 loker per batch.');
      return;
    }

    if (!confirm('Tambahkan loker nomor ' + start + ' sampai ' + end + '?')) return;

    var btn = $(this);
    btn.prop('disabled', true).text('Menambahkan...');
    var token = '{{ csrf_token() }}';
    var successCount = 0;
    var errors = [];

    for (var i = start; i <= end; i++) {
      try {
        var numStr = String(i).padStart(2, '0');
        var res = await fetch('{{ route("admin.lockers.store") }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
          },
          body: JSON.stringify({ locker_number: numStr, status: 'tersedia' })
        });
        if (res.ok || res.status === 302) {
          successCount++;
        } else {
          errors.push('Loker ' + numStr + ': gagal');
        }
      } catch(e) {
        errors.push('Loker ' + i + ': error');
      }
    }

    alert('Selesai! ' + successCount + ' loker berhasil ditambahkan.' + (errors.length > 0 ? '\nGagal: ' + errors.join(', ') : ''));
    location.reload();
  });
});
</script>
@endsection
