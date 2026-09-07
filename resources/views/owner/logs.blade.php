@extends('layouts.admin')

@section('title', 'Audit Trail Log (Owner) &mdash; PetGym')
@section('page_title', 'Audit Trail & Stream Aktivitas Staf')
@section('page_subtitle', 'Pemantauan jejak audit operasional staf, transaksi kasir, perubahan data, dan otorisasi secara *real-time*')

@section('content')
<!-- Banner Mode Pemantauan Eksekutif -->


<div class="card-custom">
  <form action="{{ route('owner.logs') }}" method="GET" class="row mb-3">
    <div class="col-md-9">
      <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari Jenis Aksi atau Deskripsi Log Aktivitas..." value="{{ request('search') }}">
    </div>
    <div class="col-md-3">
      <button type="submit" class="btn btn-sm btn-outline-primary btn-block">Filter Aktivitas</button>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
        <tr>
          <th>Waktu</th>
          <th>Nama Staf </th>
          <th>Aktivitas</th>
          <th>Rincian Aktivitas</th>
        </tr>
      </thead>
      <tbody>
        @forelse($logs as $log)
          <tr>
            <td style="font-size: 12px;" class="text-muted font-weight-bold">{{ $log->created_at ? $log->created_at->format('d M Y H:i:s') : '-' }}</td>
            <td class="font-weight-bold text-dark" style="font-size: 12.5px;">{{ $log->user ? $log->user->name : 'System/Kasir' }}</td>
            <td>
              <span class="badge badge-secondary px-2 py-1" style="font-size: 11px;">{{ $log->action }}</span>
            </td>
            <td style="font-size: 12.5px;" class="text-dark">{{ $log->description }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="text-center py-4 text-muted">Belum ada log aktivitas staf tercatat di sistem.</td>
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
