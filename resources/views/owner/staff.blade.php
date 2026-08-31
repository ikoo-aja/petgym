@extends('layouts.admin')

@section('title', 'Pemantauan Akun Staf (Owner) &mdash; PetGym')
@section('page_title', 'Pemantauan Akun Staf & Otentikasi Role')
@section('page_subtitle', 'Monitoring daftar akun karyawan gym dan hak akses Role-Based Access Control (RBAC) (Mode Pemantauan & Read-Only)')

@section('content')


<div class="card-custom">
  <h6 class="font-weight-bold text-dark mb-3">Daftar Akun Staf</h6>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
        <tr>
          <th>Nama Karyawan</th>
          <th>Email</th>
          <th>Peran</th>
          <th>Tanggal Masuk</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        @forelse($staffList as $s)
          <tr>
            <td class="font-weight-bold text-dark" style="font-size: 12.5px;">{{ $s->name }}</td>
            <td style="font-size: 12px;" class="text-muted">
              {{ $s->email ? \App\Helpers\PrivacyHelper::maskEmail($s->email) : '-' }}
            </td>
            <td>
              @if($s->role == 'admin')
                <span class="badge badge-primary px-2 py-1">Admin Gym</span>
              @elseif($s->role == 'manager')
                <span class="badge badge-info px-2 py-1">Manager Gym</span>
              @elseif($s->role == 'receptionist')
                <span class="badge badge-warning px-2 py-1">Resepsionis</span>
              @elseif($s->role == 'trainer')
                <span class="badge badge-secondary px-2 py-1">Personal Trainer</span>
              @else
                <span class="badge badge-dark px-2 py-1">{{ ucfirst($s->role) }}</span>
              @endif
            </td>
            <td style="font-size: 12px;" class="text-muted">{{ $s->created_at ? $s->created_at->format('d M Y') : '-' }}</td>
            <td>
              <span class="badge badge-success px-2 py-1">Aktif</span>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="text-center py-4 text-muted">Belum ada akun staf terdaftar.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
