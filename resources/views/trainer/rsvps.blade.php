@extends('layouts.layout')

@section('title', 'Daftar Peserta RSVP Kelas &mdash; PetGym')
@section('page_title', 'Daftar Peserta RSVP Kelas')
@section('page_subtitle', 'Pantau dan catat kehadiran member yang mendaftar pada kelas latihan yang Anda bimbing')

@section('content')
<div class="card-custom">
  <!-- Filter & Search Bar -->
  <div class="d-flex flex-wrap justify-content-between align-items-center mb-4" style="gap: 12px;">
    <form action="{{ route('trainer.rsvps') }}" method="GET" class="form-inline flex-wrap" style="gap: 8px;">
      <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama member..." value="{{ request('search') }}" style="width: 200px; border-radius: 8px;">
      
      <select name="gym_class_id" class="form-control form-control-sm" style="border-radius: 8px; max-width: 200px;">
        <option value="">-- Semua Kelas --</option>
        @foreach($myClasses as $c)
          <option value="{{ $c->id }}" {{ request('gym_class_id') == $c->id ? 'selected' : '' }}>
            {{ $c->name }} ({{ $c->day }})
          </option>
        @endforeach
      </select>

      <select name="status" class="form-control form-control-sm" style="border-radius: 8px;">
        <option value="">-- Status Kehadiran --</option>
        <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Terdaftar</option>
        <option value="attended" {{ request('status') === 'attended' ? 'selected' : '' }}>Hadir</option>
        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Batal</option>
      </select>

      <input type="date" name="date" class="form-control form-control-sm" value="{{ request('date') }}" style="border-radius: 8px;">

      <button type="submit" class="btn btn-sm btn-primary font-weight-bold" style="border-radius: 8px;">Filter</button>
      @if(request()->hasAny(['search', 'gym_class_id', 'status', 'date']))
        <a href="{{ route('trainer.rsvps') }}" class="btn btn-sm btn-outline-secondary font-weight-bold" style="border-radius: 8px;">Reset</a>
      @endif
    </form>

    <div>
      <span class="badge badge-primary font-weight-bold px-3 py-2" style="border-radius: 8px;">
        Total: {{ $rsvps->total() }} Peserta
      </span>
    </div>
  </div>

  <!-- Tabel Peserta RSVP -->
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
        <tr>
          <th>Nama Member</th>
          <th>Kelas Gym</th>
          <th>Tanggal & Jam Kelas</th>
          <th>Status Kehadiran</th>
          <th class="text-right">Aksi Penanganan</th>
        </tr>
      </thead>
      <tbody>
        @forelse($rsvps as $r)
          <tr>
            <td>
              <div class="font-weight-bold text-dark">{{ $r->member ? $r->member->name : '-' }}</div>
              <small class="text-muted">Status: {{ ucfirst($r->member->status ?? 'Aktif') }}</small>
            </td>
            <td>
              <div class="font-weight-bold text-dark">{{ $r->gymClass ? $r->gymClass->name : '-' }}</div>
              <small class="text-muted">{{ $r->gymClass->room ?? 'Studio' }} | Hari {{ $r->gymClass->day ?? '-' }}</small>
            </td>
            <td>
              <div class="font-weight-bold text-dark" style="font-size: 13.5px;">
                {{ $r->class_date ? \Carbon\Carbon::parse($r->class_date)->format('d M Y') : '-' }}
              </div>
              <div class="text-primary font-weight-bold" style="font-size: 12px;">
                {{ $r->gymClass->time ?? '-' }} WIB
              </div>
            </td>
            <td>
              @if($r->status === 'confirmed')
                <span class="badge badge-warning px-3 py-1 font-weight-bold text-dark">Terdaftar</span>
              @elseif($r->status === 'attended')
                <span class="badge badge-success px-3 py-1 font-weight-bold">Hadir</span>
              @else
                <span class="badge badge-danger px-3 py-1 font-weight-bold">Batal</span>
              @endif
            </td>
            <td class="text-right">
              @if($r->status === 'confirmed')
                <form action="{{ route('trainer.rsvps.status', $r->id) }}" method="POST" class="d-inline mr-1" data-confirm="Tandai peserta ini hadir mengikuti kelas?">
                  @csrf
                  <input type="hidden" name="status" value="attended">
                  <button type="submit" class="btn btn-sm btn-success font-weight-bold" style="border-radius: 6px;">
                    Tandai Hadir
                  </button>
                </form>

                <form action="{{ route('trainer.rsvps.status', $r->id) }}" method="POST" class="d-inline" data-confirm="Batalkan pendaftaran RSVP untuk peserta ini?">
                  @csrf
                  <input type="hidden" name="status" value="cancelled">
                  <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius: 6px;">
                    Batalkan
                  </button>
                </form>
              @elseif($r->status === 'attended')
                <span class="text-success font-weight-bold" style="font-size: 12px;">Sudah Hadir</span>
              @else
                <span class="text-muted" style="font-size: 12px;">Pendaftaran Dibatalkan</span>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="text-center py-4 text-muted">Belum ada peserta RSVP kelas yang sesuai dengan filter pencarian.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-3">
    {{ $rsvps->appends(request()->query())->links() }}
  </div>
</div>
@endsection
