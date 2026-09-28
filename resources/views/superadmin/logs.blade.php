@extends('layouts.superadmin')

@section('title', 'System Logs &mdash; Superadmin Panel')

@section('page_title', 'Log Sistem')
@section('page_subtitle', 'Riwayat audit trail dan rekam aktivitas sistem platform')

@section('content')
<div class="row">
  <div class="col-lg-12 mb-4" id="logs">
    <div class="bg-white p-4 rounded shadow-sm">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="font-weight-bold text-black mb-0">System Audit Logs</h4>
        <span class="badge badge-light border text-muted px-2 py-1 font-weight-bold">Total: {{ $logs->total() }} Log</span>
      </div>

      <!-- Filter & Search -->
      <form action="{{ route('superadmin.logs') }}" method="GET" class="row mb-4">
        <div class="col-md-7 mb-2 mb-md-0">
          <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari aktivitas, pengguna, atau IP address..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3 mb-2 mb-md-0">
          <select name="action" class="form-control form-control-sm" onchange="this.form.submit()">
            <option value="">Semua Kategori Aktivitas</option>
            <option value="Tenant" {{ request('action') == 'Tenant' ? 'selected' : '' }}>Tenant / Penyewa</option>
            <option value="Paket" {{ request('action') == 'Paket' ? 'selected' : '' }}>Paket Sewa</option>
            <option value="Pengumuman" {{ request('action') == 'Pengumuman' ? 'selected' : '' }}>Pengumuman</option>
            <option value="Perpanjangan" {{ request('action') == 'Perpanjangan' ? 'selected' : '' }}>Perpanjangan & Tagihan</option>
            <option value="Pendaftaran" {{ request('action') == 'Pendaftaran' ? 'selected' : '' }}>Pendaftaran Leads</option>
          </select>
        </div>
        <div class="col-md-2">
          <button type="submit" class="btn btn-sm btn-outline-primary btn-block px-3">Filter</button>
        </div>
      </form>

      <!-- Table of Logs -->
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead>
            <tr>
              <th class="text-black font-weight-bold" style="width: 160px;">Waktu</th>
              <th class="text-black font-weight-bold" style="width: 140px;">Pengguna</th>
              <th class="text-black font-weight-bold" style="width: 160px;">Kategori</th>
              <th class="text-black font-weight-bold">Deskripsi Aktivitas</th>
              <th class="text-black font-weight-bold text-right" style="width: 130px;">IP Address</th>
            </tr>
          </thead>
          <tbody>
            @forelse($logs as $log)
            <tr>
              <td class="text-muted small" style="white-space: nowrap;">
                <i class="icon-clock-o mr-1"></i> {{ $log->created_at->format('d M Y, H:i') }}
              </td>
              <td>
                <strong class="text-dark small">{{ $log->user ? $log->user->name : 'Sistem' }}</strong>
              </td>
              <td>
                @php
                  $act = strtolower($log->action ?? '');
                  $badgeCls = 'badge-secondary';
                  if (str_contains($act, 'tenant') || str_contains($act, 'penyewa')) $badgeCls = 'badge-primary';
                  elseif (str_contains($act, 'paket') || str_contains($act, 'plan')) $badgeCls = 'badge-info';
                  elseif (str_contains($act, 'pengumuman')) $badgeCls = 'badge-warning text-dark';
                  elseif (str_contains($act, 'perpanjangan') || str_contains($act, 'bayar') || str_contains($act, 'invoice')) $badgeCls = 'badge-success';
                  elseif (str_contains($act, 'hapus') || str_contains($act, 'suspend')) $badgeCls = 'badge-danger';
                @endphp
                <span class="badge {{ $badgeCls }} px-2 py-1 font-weight-bold" style="font-size: 11px;">
                  {{ $log->action }}
                </span>
              </td>
              <td class="text-dark small" style="line-height: 1.5;">
                {{ $log->description }}
              </td>
              <td class="text-right text-muted small" style="font-family: monospace;">
                {{ $log->ip_address ?: '-' }}
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="5" class="text-center py-4 text-muted">Belum ada audit log sistem tercatat.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <!-- Pagination Links -->
      <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
        <small class="text-muted">Menampilkan {{ $logs->firstItem() ?? 0 }} sampai {{ $logs->lastItem() ?? 0 }} dari {{ $logs->total() }} catatan log</small>
        {{ $logs->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
