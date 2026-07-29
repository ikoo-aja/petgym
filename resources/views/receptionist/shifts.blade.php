@extends('layouts.admin')

@section('title', 'Shift Kasir & Keluhan &mdash; PetGym')
@section('page_title', 'Manajemen Shift Kasir & Pencatatan Keluhan')
@section('page_subtitle', 'Buka/tutup shift harian, input setoran laci kas fisik, dan form tiket keluhan member')

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

<div class="row mb-4">
  <!-- Buka Shift Kasir -->
  <div class="col-md-5">
    <div class="card-custom">
      <h6 class="font-weight-bold text-dark mb-3">🟢 Buka Shift Kasir Baru</h6>

      @php
        $openShift = $shifts->where('status', 'open')->first();
      @endphp

      @if($openShift)
        <div class="alert alert-info mb-3">
          <strong>Shift Aktif</strong> — Dibuka sejak <strong>{{ $openShift->opened_at ? \Carbon\Carbon::parse($openShift->opened_at)->format('d M Y, H:i') : '-' }}</strong> WIB.
          <br>Kas Awal: <strong>Rp {{ number_format($openShift->start_cash, 0, ',', '.') }}</strong>
        </div>

        <form action="{{ route('receptionist.shifts.end', $openShift->id) }}" method="POST">
          @csrf
          <div class="form-group mb-3">
            <label class="font-weight-bold mb-1" style="font-size:13px;">Nominal Kas Laci Akhir Shift (Fisik) *</label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text font-weight-bold">Rp</span>
              </div>
              <input type="number" name="end_cash" class="form-control" placeholder="Hitung fisik nominal laci kasir" required min="0">
            </div>
            <small class="text-muted">Hitung secara manual uang fisik yang ada di laci kasir saat ini.</small>
          </div>
          <button type="submit" class="btn btn-danger btn-block font-weight-bold" onclick="return confirm('Yakin ingin menutup shift kasir? Pastikan nominal kas laci sudah benar.')">
            🔴 Tutup Shift & Setor Kas
          </button>
        </form>
      @else
        <form action="{{ route('receptionist.shifts.start') }}" method="POST">
          @csrf
          <div class="form-group mb-3">
            <label class="font-weight-bold mb-1" style="font-size:13px;">Nominal Kas Laci Awal Shift (Fisik) *</label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text font-weight-bold">Rp</span>
              </div>
              <input type="number" name="start_cash" class="form-control" placeholder="Masukkan kas awal dari serah terima" required min="0">
            </div>
            <small class="text-muted">Hitung kas yang ada di laci saat serah terima dari shift sebelumnya.</small>
          </div>
          <button type="submit" class="btn btn-success btn-block font-weight-bold">
            🟢 Buka Shift Kasir Sekarang
          </button>
        </form>
      @endif
    </div>
  </div>

  <!-- Riwayat Shift -->
  <div class="col-md-7">
    <div class="card-custom">
      <h6 class="font-weight-bold text-dark mb-3">📅 Riwayat Shift Kasir Anda</h6>
      <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
            <tr>
              <th>Tanggal</th>
              <th>Buka</th>
              <th>Tutup</th>
              <th>Kas Awal</th>
              <th>Kas Akhir</th>
              <th>Selisih</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse($shifts as $s)
            <tr>
              <td style="font-size:13px;">{{ $s->opened_at ? \Carbon\Carbon::parse($s->opened_at)->format('d M Y') : '-' }}</td>
              <td class="font-weight-bold text-dark" style="font-size:13px;">{{ $s->opened_at ? \Carbon\Carbon::parse($s->opened_at)->format('H:i') : '-' }}</td>
              <td style="font-size:13px;">{{ $s->closed_at ? \Carbon\Carbon::parse($s->closed_at)->format('H:i') : '-' }}</td>
              <td style="font-size:12px;">Rp {{ number_format($s->start_cash, 0, ',', '.') }}</td>
              <td style="font-size:12px;">{{ $s->end_cash !== null ? 'Rp ' . number_format($s->end_cash, 0, ',', '.') : '-' }}</td>
              <td>
                @if($s->end_cash !== null)
                  @php $diff = $s->end_cash - $s->start_cash; @endphp
                  <span class="badge {{ $diff >= 0 ? 'badge-success' : 'badge-danger' }}" style="font-size:11px;">
                    {{ $diff >= 0 ? '+' : '' }}Rp {{ number_format($diff, 0, ',', '.') }}
                  </span>
                @else
                  <span class="text-muted">-</span>
                @endif
              </td>
              <td>
                @if($s->status === 'open')
                  <span class="badge badge-success">Aktif</span>
                @else
                  <span class="badge badge-secondary">Closed</span>
                @endif
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="7" class="text-center py-4 text-muted">Belum ada riwayat shift tercatat.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Pencatatan Keluhan Member -->
<div class="row">
  <div class="col-md-5">
    <div class="card-custom">
      <h6 class="font-weight-bold text-dark mb-3">📝 Form Input Keluhan Member Baru</h6>
      <form action="{{ route('receptionist.complaints.store') }}" method="POST">
        @csrf
        <div class="form-group mb-2">
          <label class="font-weight-bold mb-1" style="font-size:12px;">Pilih Member yang Mengeluh *</label>
          <select name="member_id" class="form-control form-control-sm" required>
            <option value="">-- Pilih Member --</option>
            @foreach($members as $m)
              <option value="{{ $m->id }}">{{ $m->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group mb-2">
          <label class="font-weight-bold mb-1" style="font-size:12px;">Judul Keluhan *</label>
          <input type="text" name="title" class="form-control form-control-sm" placeholder="Misal: AC mati di ruang ganti" required>
        </div>
        <div class="form-group mb-3">
          <label class="font-weight-bold mb-1" style="font-size:12px;">Deskripsi Lengkap *</label>
          <textarea name="description" class="form-control form-control-sm" rows="3" placeholder="Jelaskan detail keluhan member secara lengkap..." required></textarea>
        </div>
        <button type="submit" class="btn btn-danger btn-block btn-sm font-weight-bold">
          Kirim Tiket Keluhan ke Manager
        </button>
      </form>
    </div>
  </div>

  <!-- Daftar Keluhan Tercatat -->
  <div class="col-md-7">
    <div class="card-custom">
      <h6 class="font-weight-bold text-dark mb-3">📋 Riwayat Tiket Keluhan Member</h6>
      <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
            <tr>
              <th>Tanggal</th>
              <th>Member</th>
              <th>Judul</th>
              <th>Status</th>
              <th>Resolusi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($complaints as $c)
            <tr>
              <td style="font-size:12px;">{{ $c->created_at->format('d M Y') }}</td>
              <td class="font-weight-bold text-dark">{{ $c->member ? $c->member->name : '-' }}</td>
              <td style="font-size:13px;">{{ $c->title }}</td>
              <td>
                @if($c->status === 'open')
                  <span class="badge badge-danger">Open</span>
                @elseif($c->status === 'in_progress')
                  <span class="badge badge-warning text-dark">In Progress</span>
                @elseif($c->status === 'resolved')
                  <span class="badge badge-success">Resolved</span>
                @else
                  <span class="badge badge-secondary">Closed</span>
                @endif
              </td>
              <td style="font-size:12px; max-width:150px;" class="text-truncate">{{ $c->resolution ?? 'Belum ada resolusi' }}</td>
            </tr>
            @empty
            <tr>
              <td colspan="5" class="text-center py-4 text-muted">Belum ada keluhan yang tercatat.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
