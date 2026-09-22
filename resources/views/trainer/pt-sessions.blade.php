@extends('layouts.layout')

@section('title', 'Booking Sesi PT &mdash; PetGym')
@section('page_title', 'Booking Sesi Personal Trainer')
@section('page_subtitle', 'Kelola jadwal latihan privat bersama member dan konfirmasi penyelesaian sesi latihan')

@section('content')
<div class="card-custom">
  <!-- Filter & Search Bar -->
  <div class="d-flex flex-wrap justify-content-between align-items-center mb-4" style="gap: 12px;">
    <form action="{{ route('trainer.pt-sessions') }}" method="GET" class="form-inline flex-wrap" style="gap: 8px;">
      <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama member..." value="{{ request('search') }}" style="width: 220px; border-radius: 8px;">
      
      <select name="status" class="form-control form-control-sm" style="border-radius: 8px;">
        <option value="">-- Semua Status --</option>
        <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Terjadwal</option>
        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Batal</option>
      </select>

      <input type="date" name="date" class="form-control form-control-sm" value="{{ request('date') }}" style="border-radius: 8px;">

      <button type="submit" class="btn btn-sm btn-primary font-weight-bold" style="border-radius: 8px;">Filter</button>
      @if(request()->hasAny(['search', 'status', 'date']))
        <a href="{{ route('trainer.pt-sessions') }}" class="btn btn-sm btn-outline-secondary font-weight-bold" style="border-radius: 8px;">Reset</a>
      @endif
    </form>

    <div>
      <span class="badge badge-primary font-weight-bold px-3 py-2" style="border-radius: 8px;">
        Total: {{ $ptBookings->total() }} Sesi Terdaftar
      </span>
    </div>
  </div>

  <!-- Tabel Booking Sesi PT -->
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
        <tr>
          <th>Nama Member</th>
          <th>Tanggal Sesi</th>
          <th>Jam Pertemuan</th>
          <th>Status Sesi</th>
          <th class="text-right">Aksi Penanganan</th>
        </tr>
      </thead>
      <tbody>
        @forelse($ptBookings as $b)
          <tr>
            <td>
              <div class="font-weight-bold text-dark">{{ $b->member ? $b->member->name : '-' }}</div>
              <small class="text-muted">Status Member: {{ ucfirst($b->member->status ?? 'Aktif') }}</small>
            </td>
            <td style="font-size: 13.5px;" class="text-dark font-weight-bold">
              {{ $b->booking_date ? \Carbon\Carbon::parse($b->booking_date)->format('d M Y') : '-' }}
            </td>
            <td style="font-size: 13.5px;" class="font-weight-bold text-primary">
              {{ $b->booking_time ?? '09:00 WIB' }}
            </td>
            <td>
              @if($b->status === 'scheduled')
                <span class="badge badge-success px-3 py-1 font-weight-bold">Terjadwal</span>
              @elseif($b->status === 'completed')
                <span class="badge badge-secondary px-3 py-1 font-weight-bold">Selesai</span>
              @else
                <span class="badge badge-danger px-3 py-1 font-weight-bold">Batal</span>
              @endif
            </td>
            <td class="text-right">
              @if($b->status === 'scheduled')
                <form action="{{ route('trainer.pt-sessions.status', $b->id) }}" method="POST" class="d-inline mr-1" data-confirm="Konfirmasi bahwa sesi latihan PT ini telah selesai dilaksanakan?">
                  @csrf
                  <input type="hidden" name="status" value="completed">
                  <button type="submit" class="btn btn-sm btn-success font-weight-bold" style="border-radius: 6px;">
                    Tandai Selesai
                  </button>
                </form>

                <form action="{{ route('trainer.pt-sessions.status', $b->id) }}" method="POST" class="d-inline" data-confirm="Batalkan jadwal sesi latihan PT ini?">
                  @csrf
                  <input type="hidden" name="status" value="cancelled">
                  <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius: 6px;">
                    Batalkan
                  </button>
                </form>
              @elseif($b->status === 'completed')
                <span class="text-success font-weight-bold" style="font-size: 12px;">Sesi Lunas & Selesai</span>
              @else
                <span class="text-muted" style="font-size: 12px;">Dibatalkan</span>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="text-center py-4 text-muted">Belum ada booking sesi PT yang sesuai dengan pencarian.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-3">
    {{ $ptBookings->appends(request()->query())->links() }}
  </div>
</div>
@endsection
