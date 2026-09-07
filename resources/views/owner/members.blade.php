@extends('layouts.admin')

@section('title', 'Pemantauan Data Member (Owner) &mdash; PetGym')
@section('page_title', 'Pemantauan Data Member Gym')
@section('page_subtitle', 'Monitoring daftar anggota keanggotaan gym, tanggal kadaluarsa, dan status aktif')

@section('content')
<!-- Banner Mode Pemantauan Eksekutif -->


<!-- KPI Cards -->
<div class="row mb-4">
  <div class="col-md-6">
    <div class="card-custom text-white" style="background: linear-gradient(135deg, #0ea5e9, #0284c7);">
      <small class="text-white  text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Total Member</small>
      <h3 class="font-weight-bold text-white mb-0 mt-1">{{ $totalMembers }} Member</h3>
    </div>
  </div>

  <div class="col-md-6">
    <div class="card-custom text-white" style="background: linear-gradient(135deg, #10b981, #059669);">
      <small class="text-white-50 text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Member Aktif</small>
      <h3 class="font-weight-bold text-white mb-0 mt-1">{{ $activeMembers }} Member</h3>
    </div>
  </div>
</div>

<!-- Table Section -->
<div class="card-custom">
  <form action="{{ route('owner.members') }}" method="GET" class="row mb-3">
    <div class="col-md-6">
      <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari Nama Member, Kode Akses, No. HP..." value="{{ request('search') }}">
    </div>
    <div class="col-md-4">
      <select name="status" class="form-control form-control-sm" onchange="this.form.submit()">
        <option value="">Semua Status Keanggotaan</option>
        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired / Kadaluarsa</option>
      </select>
    </div>
    <div class="col-md-2">
      <button type="submit" class="btn btn-sm btn-outline-primary btn-block">Filter</button>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
        <tr>
          <th>Nama Member</th>
          <th>Kontak</th>
          <th>Masa Berlaku</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        @forelse($members as $m)
          <tr>
            <td class="font-weight-bold text-dark" style="font-size: 12.5px;">{{ $m->name }}</td>
            <td style="font-size: 12px;" class="text-muted">
              {{ $m->phone ? \App\Helpers\PrivacyHelper::maskPhone($m->phone) : '-' }}
            </td>
            <td style="font-size: 12px;" class="text-dark font-weight-bold">
              {{ $m->expired_at ? \Carbon\Carbon::parse($m->expired_at)->format('d M Y') : '-' }}
            </td>
            <td>
              @if($m->status == 'active')
                <span class="badge badge-success px-2 py-1">Aktif</span>
              @else
                <span class="badge badge-danger px-2 py-1">Expired</span>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="text-center py-4 text-muted">Belum ada data member keanggotaan ditemukan.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-3">
    {{ $members->links() }}
  </div>
</div>
@endsection
