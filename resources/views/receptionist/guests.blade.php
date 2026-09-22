@extends('layouts.admin')

@section('title', 'Buku Tamu &mdash; PetGym')
@section('page_title', 'Buku Tamu / Walk-in Leads')
@section('page_subtitle', 'Pencatatan data pengunjung harian, prospek calon member, dan konversi ke member aktif')

@section('content')
<div class="card-custom">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h6 class="font-weight-bold text-dark mb-0">Daftar Buku Tamu & Walk-in Prospek</h6>
      <small class="text-muted">Total: {{ count($guests) }} data tamu tercatat</small>
    </div>
    <button class="btn btn-sm btn-primary font-weight-bold" data-toggle="modal" data-target="#addGuestModal" style="border-radius: 8px;">
      + Tambah Tamu Baru
    </button>
  </div>

  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
        <tr>
          <th>Nama Tamu</th>
          <th>No. WhatsApp / HP</th>
          <th>Email</th>
          <th>Catatan / Minat</th>
          <th>Status Lead</th>
          <th class="text-right">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($guests as $g)
        <tr>
          <td class="font-weight-bold text-dark">{{ $g->name }}</td>
          <td style="font-size:13px;" class="text-dark">{{ $g->phone ? \App\Helpers\PrivacyHelper::maskPhone($g->phone) : '-' }}</td>
          <td style="font-size:12px;">{{ $g->email ? \App\Helpers\PrivacyHelper::maskEmail($g->email) : '-' }}</td>
          <td style="font-size:12.5px; max-width: 250px;">{{ $g->notes ?? '-' }}</td>
          <td>
            @if($g->converted_to_member_id)
              <span class="badge badge-success px-2 py-1">Sudah Jadi Member</span>
            @else
              <span class="badge badge-warning text-dark px-2 py-1">Prospek Baru</span>
            @endif
          </td>
          <td class="text-right">
            @if(!$g->converted_to_member_id)
              <form action="{{ route('receptionist.guests.convert', $g->id) }}" method="POST" style="display:inline;" data-confirm="Konversi tamu ini menjadi calon member?">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-success font-weight-bold" style="border-radius:6px;">Jadikan Member</button>
              </form>
            @else
              <span class="text-success font-weight-bold" style="font-size:12px;">Terkonversi</span>
            @endif
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6" class="text-center py-4 text-muted">Belum ada data tamu tercatat. Silakan klik "+ Tambah Tamu Baru".</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Tambah Tamu -->
<div class="modal fade" id="addGuestModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form action="{{ route('receptionist.guests.store') }}" method="POST" class="modal-content" style="border-radius: 12px;">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title font-weight-bold">Form Catat Tamu Baru</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body p-4">
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark mb-1" style="font-size: 13px;">Nama Lengkap Tamu *</label>
          <input type="text" name="name" class="form-control" placeholder="Contoh: Andi Pratama" required style="border-radius: 8px;">
        </div>
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark mb-1" style="font-size: 13px;">Nomor Telepon / WhatsApp *</label>
          <input type="text" name="phone" class="form-control" placeholder="08123456789" required style="border-radius: 8px;">
        </div>
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark mb-1" style="font-size: 13px;">Email (Opsional)</label>
          <input type="email" name="email" class="form-control" placeholder="andi@example.com" style="border-radius: 8px;">
        </div>
        <div class="form-group mb-0">
          <label class="font-weight-bold text-dark mb-1" style="font-size: 13px;">Catatan / Minat</label>
          <textarea name="notes" class="form-control" rows="3" placeholder="Misal: Tertarik program fitness 3 bulan, tanya instruktur yoga..." style="border-radius: 8px;"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 8px;">Batal</button>
        <button type="submit" class="btn btn-primary font-weight-bold px-4" style="border-radius: 8px;">Simpan ke Buku Tamu</button>
      </div>
    </form>
  </div>
</div>
@endsection
