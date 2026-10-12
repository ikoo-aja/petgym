@extends('layouts.superadmin')

@section('title', 'Approval Reset Password Admin — Superadmin Panel')

@section('page_title', 'Persetujuan Reset Password Admin')
@section('page_subtitle', 'Kelola permohonan reset password dari akun Admin tenant yang membutuhkan approval')

@section('content')
<div class="bg-white p-4 rounded shadow-sm mb-4">
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3" style="gap: 15px;">
    <div>
      <h5 class="font-weight-bold text-black mb-1">Daftar Permohonan Reset Password Admin</h5>
      <p class="text-muted small mb-0">Role Admin yang lupa kata sandi wajib disetujui Superadmin sebelum tautan reset dikirimkan ke email mereka.</p>
    </div>
    <div class="d-flex align-items-center" style="gap: 10px;">
      <span class="badge badge-warning text-dark px-3 py-2 font-weight-bold" style="border-radius: 8px; font-size: 12px;">
        <i class="icon-clock-o mr-1"></i> {{ $pendingCount }} Menunggu Persetujuan
      </span>
    </div>
  </div>

  <!-- Filter & Search Bar -->
  <form action="{{ route('superadmin.password-resets') }}" method="GET" class="row mb-3">
    <div class="col-md-7 mb-2 mb-md-0">
      <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama admin, email, atau nama gym tenant..." value="{{ request('search') }}">
    </div>
    <div class="col-md-3 mb-2 mb-md-0">
      <select name="status" class="form-control form-control-sm" onchange="this.form.submit()">
        <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>Semua Status</option>
        <option value="pending" {{ request('status', 'pending') === 'pending' ? 'selected' : '' }}>Menunggu (Pending)</option>
        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui (Approved)</option>
        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak (Rejected)</option>
      </select>
    </div>
    <div class="col-md-2">
      <button type="submit" class="btn btn-secondary btn-sm btn-block font-weight-bold">Filter</button>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table table-hover mb-0" style="font-size: 13.5px;">
      <thead class="thead-light">
        <tr>
          <th>Tanggal Pengajuan</th>
          <th>Nama Admin & Email</th>
          <th>Gym / Tenant</th>
          <th>Status</th>
          <th>Catatan / Approver</th>
          <th class="text-right">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($requests as $req)
        <tr>
          <td>
            <span class="text-dark font-weight-bold">{{ $req->created_at ? $req->created_at->format('d M Y') : '-' }}</span><br>
            <small class="text-muted">{{ $req->created_at ? $req->created_at->format('H:i') . ' WIB' : '' }}</small>
          </td>
          <td>
            <strong class="text-black">{{ $req->admin_name }}</strong><br>
            <span class="text-muted small">{{ $req->email }}</span>
          </td>
          <td>
            @if($req->tenant)
              <strong class="text-primary">{{ $req->tenant->name }}</strong><br>
              <small class="text-muted">{{ $req->tenant->subdomain }}</small>
            @else
              <span class="text-muted font-italic">Tenant Tidak Terhubung</span>
            @endif
          </td>
          <td>
            @if($req->status === 'pending')
              <span class="badge badge-warning text-dark font-weight-bold px-2 py-1" style="border-radius: 6px;">
                <i class="icon-clock-o mr-1"></i> Menunggu Approval
              </span>
            @elseif($req->status === 'approved')
              <span class="badge badge-success font-weight-bold px-2 py-1" style="border-radius: 6px;">
                <i class="icon-check mr-1"></i> Disetujui (Tautan Terkirim)
              </span>
            @elseif($req->status === 'rejected')
              <span class="badge badge-danger font-weight-bold px-2 py-1" style="border-radius: 6px;">
                <i class="icon-close mr-1"></i> Ditolak
              </span>
            @else
              <span class="badge badge-secondary px-2 py-1" style="border-radius: 6px;">{{ ucfirst($req->status) }}</span>
            @endif
          </td>
          <td>
            @if($req->approved_by)
              <small class="d-block text-muted">Diproses oleh: <strong>{{ $req->approver->name ?? 'Superadmin' }}</strong></small>
              <small class="text-muted">{{ $req->approved_at ? $req->approved_at->format('d/m/Y H:i') : '' }}</small>
            @endif
            @if($req->notes)
              <small class="d-block text-dark mt-1"><em>"{{ $req->notes }}"</em></small>
            @elseif(!$req->approved_by)
              <span class="text-muted small font-italic">-</span>
            @endif
          </td>
          <td class="text-right">
            @if($req->status === 'pending')
              <div class="d-inline-flex align-items-center" style="gap: 6px;">
                <!-- Form Setujui -->
                <form action="{{ route('superadmin.password-resets.approve', $req->id) }}" method="POST" onsubmit="return confirm('Setujui permohonan reset password Admin {{ $req->admin_name }}? Sistem akan otomatis mengirimkan tautan reset password baru ke email {{ $req->email }}.');">
                  @csrf
                  <button type="submit" class="btn btn-sm btn-success font-weight-bold px-3 py-1" style="border-radius: 6px; font-size: 12px;">
                    <i class="icon-check mr-1"></i> Setujui & Kirim Link
                  </button>
                </form>

                <!-- Tombol Tolak dengan Modal Catatan -->
                <button type="button" class="btn btn-sm btn-outline-danger font-weight-bold px-2 py-1" data-toggle="modal" data-target="#rejectModal{{ $req->id }}" style="border-radius: 6px; font-size: 12px;" title="Tolak Permohonan">
                  <i class="icon-close"></i> Tolak
                </button>
              </div>

              <!-- Modal Tolak Permohonan -->
              <div class="modal fade" id="rejectModal{{ $req->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                  <form action="{{ route('superadmin.password-resets.reject', $req->id) }}" method="POST" class="modal-content shadow border-0 text-left" style="border-radius: 12px;">
                    @csrf
                    <div class="modal-header bg-light">
                      <h5 class="modal-title font-weight-bold text-dark">Tolak Permohonan Reset Password</h5>
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body p-4">
                      <p class="text-muted small mb-3">Tolak permohonan reset password untuk Admin <strong>{{ $req->admin_name }}</strong> ({{ $req->email }}).</p>
                      <div class="form-group mb-0">
                        <label class="font-weight-bold small text-dark">Alasan Penolakan (Opsional)</label>
                        <textarea name="notes" rows="3" class="form-control" placeholder="Contoh: Akun sedang ditangguhkan / konfirmasi pemilik gym tidak cocok..."></textarea>
                      </div>
                    </div>
                    <div class="modal-footer bg-light">
                      <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                      <button type="submit" class="btn btn-danger btn-sm font-weight-bold px-3">Konfirmasi Tolak</button>
                    </div>
                  </form>
                </div>
              </div>
            @else
              <span class="text-muted small font-italic">Selesai</span>
            @endif
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6" class="text-center py-4 text-muted">Tidak ada permohonan reset password admin yang ditemukan.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($requests->hasPages())
  <div class="mt-4 d-flex justify-content-between align-items-center">
    <div class="text-muted small">
      Menampilkan {{ $requests->firstItem() ?? 0 }} sampai {{ $requests->lastItem() ?? 0 }} dari {{ $requests->total() }} data
    </div>
    <div>{{ $requests->links() }}</div>
  </div>
  @endif
</div>
@endsection
