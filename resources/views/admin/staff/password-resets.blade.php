@extends('layouts.layout')

@section('title', 'Persetujuan Reset Password — Admin Portal')

@section('page_title', 'Persetujuan Reset Password Pengguna')
@section('page_subtitle', 'Kelola permohonan reset password dari Staf dan Member gym Anda')

@section('content')
<div class="container-fluid py-4">
  <div class="card border shadow-sm rounded-lg overflow-hidden bg-white mb-4" style="border-radius: 14px;">
    <div class="card-body p-4">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4" style="gap: 15px;">
        <div>
          <h4 class="font-weight-bold text-dark mb-1">Permohonan Reset Kata Sandi</h4>
          <p class="text-muted small mb-0">Role Owner, Manager, Supervisor, PT, Resepsionis, dan Member yang lupa kata sandi memerlukan persetujuan Admin sebelum tautan pembuatan kata sandi baru diterbitkan.</p>
        </div>
        <div>
          <span class="badge badge-warning text-dark px-3 py-2 font-weight-bold" style="border-radius: 8px; font-size: 13px;">
            <i class="icon-clock-o mr-1"></i> {{ $pendingCount }} Menunggu Persetujuan
          </span>
        </div>
      </div>

      <!-- Filter & Search Bar -->
      <form action="{{ route('admin.password-resets') }}" method="GET" class="row mb-4">
        <div class="col-md-7 mb-2 mb-md-0">
          <input type="text" name="search" class="form-control" placeholder="Cari nama, email, atau jabatan / role..." value="{{ request('search') }}" style="border-radius: 8px;">
        </div>
        <div class="col-md-3 mb-2 mb-md-0">
          <select name="status" class="form-control" onchange="this.form.submit()" style="border-radius: 8px;">
            <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>Semua Status</option>
            <option value="pending" {{ request('status', 'pending') === 'pending' ? 'selected' : '' }}>Menunggu (Pending)</option>
            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui (Approved)</option>
            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak (Rejected)</option>
          </select>
        </div>
        <div class="col-md-2">
          <button type="submit" class="btn btn-secondary btn-block font-weight-bold" style="border-radius: 8px;">Filter</button>
        </div>
      </form>

      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: 13.5px;">
          <thead class="thead-light">
            <tr>
              <th>Waktu Pengajuan</th>
              <th>Nama Pengguna & Email</th>
              <th>Peran / Role</th>
              <th>Status</th>
              <th>Catatan / Verifikator</th>
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
                <strong class="text-dark">{{ $req->admin_name }}</strong><br>
                <span class="text-muted small">{{ $req->email }}</span>
              </td>
              <td>
                @php
                  $roleBadges = [
                    'owner'        => 'badge-primary',
                    'manager'      => 'badge-info',
                    'supervisor'   => 'badge-secondary',
                    'trainer'      => 'badge-success',
                    'receptionist' => 'badge-dark',
                    'member'       => 'badge-warning text-dark',
                  ];
                  $badgeClass = $roleBadges[$req->user_role] ?? 'badge-secondary';
                @endphp
                <span class="badge {{ $badgeClass }} px-2 py-1 font-weight-bold text-uppercase" style="border-radius: 6px; font-size: 11px;">
                  {{ $req->user_role ?? 'Pengguna' }}
                </span>
              </td>
              <td>
                @if($req->status === 'pending')
                  <span class="badge badge-warning text-dark font-weight-bold px-2 py-1" style="border-radius: 6px;">
                    <i class="icon-clock-o mr-1"></i> Menunggu Persetujuan
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
                  <small class="d-block text-muted">Diproses: <strong>{{ $req->approver->name ?? 'Admin Gym' }}</strong></small>
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
                    <form action="{{ route('admin.password-resets.approve', $req->id) }}" method="POST" data-confirm="Setujui permohonan reset kata sandi {{ $req->admin_name }}? Sistem akan mengirimkan tautan verifikasi ke email {{ $req->email }}.">
                      @csrf
                      <button type="submit" class="btn btn-sm btn-success font-weight-bold px-3 py-1" style="border-radius: 6px; font-size: 12px;">
                        <i class="icon-check mr-1"></i> Setujui & Kirim Link
                      </button>
                    </form>

                    <!-- Tombol Tolak dengan Modal -->
                    <button type="button" class="btn btn-sm btn-outline-danger font-weight-bold px-2 py-1" data-toggle="modal" data-target="#rejectStaffModal{{ $req->id }}" style="border-radius: 6px; font-size: 12px;" title="Tolak Permohonan">
                      <i class="icon-close"></i> Tolak
                    </button>
                  </div>

                  <!-- Modal Tolak -->
                  <div class="modal fade" id="rejectStaffModal{{ $req->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                      <form action="{{ route('admin.password-resets.reject', $req->id) }}" method="POST" class="modal-content shadow border-0 text-left" style="border-radius: 12px;">
                        @csrf
                        <div class="modal-header bg-light">
                          <h5 class="modal-title font-weight-bold text-dark">Tolak Permohonan Reset Password</h5>
                          <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body p-4">
                          <p class="text-muted small mb-3">Tolak permohonan reset password untuk pengguna <strong>{{ $req->admin_name }}</strong> ({{ $req->email }}).</p>
                          <div class="form-group mb-0">
                            <label class="font-weight-bold small text-dark">Alasan Penolakan (Opsional)</label>
                            <textarea name="notes" rows="3" class="form-control" placeholder="Contoh: Akun tidak aktif / verifikasi identitas tidak sesuai..."></textarea>
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
              <td colspan="6" class="text-center py-4 text-muted">Tidak ada permohonan reset password pengguna yang ditemukan.</td>
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
  </div>
</div>
@endsection
