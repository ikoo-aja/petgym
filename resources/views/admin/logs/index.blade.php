@extends('layouts.admin')

@section('title', 'Audit Trail Log &mdash; PetGym')
@section('page_title', 'Log Aktivitas Internal (Audit Trail Staff)')
@section('page_subtitle', 'Riwayat aktivitas staf internal untuk transparansi dan pencegahan kecurangan')

@section('content')
<div class="card-custom">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
        <tr>
          <th>Waktu Log</th>
          <th>Staf Pelaksana</th>
          <th>Nama Aksi</th>
          <th>Deskripsi Perubahan</th>
          <th>IP Address</th>
        </tr>
      </thead>
      <tbody>
        @forelse($logs as $lg)
          <tr>
            <td style="font-size: 12.5px;" class="text-muted">
              {{ $lg->created_at ? $lg->created_at->format('d M Y H:i:s') : '-' }}
            </td>
            <td>
              <div class="font-weight-bold text-dark" style="font-size: 13.5px;">{{ $lg->user ? $lg->user->name : 'System' }}</div>
              <small class="text-muted">{{ $lg->user ? ucfirst($lg->user->role) : 'System' }}</small>
            </td>
            <td><span class="badge badge-info px-2 py-1">{{ $lg->action }}</span></td>
            <td style="font-size: 13px;" class="text-dark">{{ $lg->description }}</td>
            <td style="font-size: 12px;" class="text-muted">{{ $lg->ip_address ?? '127.0.0.1' }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="text-center py-4 text-muted">Belum ada riwayat aktivitas staf yang tercatat.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-3">
    {{ $logs->links() }}
  </div>
</div>
@endsection
