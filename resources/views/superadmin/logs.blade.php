@extends('layouts.superadmin')

@section('title', 'System Logs &mdash; Superadmin Panel')

@section('page_title', 'System Logs')
@section('page_subtitle', 'Riwayat aktivitas sistem')

@section('content')
<div class="row">
  <div class="col-lg-12 mb-4" id="logs">
    <div class="bg-white p-4 rounded shadow-sm">
      <h4 class="font-weight-bold text-black mb-3">System Audit Logs</h4>
      <ul class="list-group list-group-flush style-logs" style="font-size: 14px; line-height: 2;">
        @forelse($logs as $log)
        <li class="list-group-item px-0">
          <span class="text-muted">[{{ $log->created_at->format('d M Y H:i') }}]</span> 
          <strong class="text-black">{{ $log->user ? $log->user->name : 'System' }}</strong> 
          {{ $log->description }}
        </li>
        @empty
        <li class="list-group-item px-0 text-muted">Belum ada audit log sistem.</li>
        @endforelse
      </ul>
    </div>
  </div>
</div>
@endsection
