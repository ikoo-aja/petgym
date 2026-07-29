@extends('layouts.admin')

@section('title', 'Manajemen Staf (RBAC) &mdash; PetGym')
@section('page_title', 'Manajemen Akun Staf & Hak Akses (RBAC)')
@section('page_subtitle', 'Pendaftaran akun Resepsionis, Manager, Trainer, serta reset password staf')

@section('content')
<div class="card-custom">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h6 class="font-weight-bold text-dark mb-0">Daftar Akun Pengelola & Staf Internal</h6>
    <button type="button" class="btn btn-sm btn-success font-weight-bold" data-toggle="modal" data-target="#createStaffModal" style="border-radius: 8px;">
      + Tambah Akun Staf Baru
    </button>
  </div>

  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
        <tr>
          <th>Nama Staf</th>
          <th>Email Login</th>
          <th>Role / Jabatan</th>
          <th>Tanggal Dibuat</th>
          <th class="text-right">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($staffs as $st)
          <tr>
            <td class="font-weight-bold text-dark">{{ $st->name }}</td>
            <td style="font-size: 13.5px;">{{ $st->email }}</td>
            <td>
              @if($st->role === 'admin')
                <span class="badge badge-primary px-3 py-1">Admin</span>
              @elseif($st->role === 'manager')
                <span class="badge badge-warning text-dark px-3 py-1">Manager Gym</span>
              @elseif($st->role === 'receptionist')
                <span class="badge badge-info px-3 py-1">Resepsionis</span>
              @elseif($st->role === 'trainer')
                <span class="badge badge-success px-3 py-1">Personal Trainer</span>
              @else
                <span class="badge badge-secondary px-3 py-1">{{ ucfirst($st->role) }}</span>
              @endif
            </td>
            <td style="font-size: 13px;">{{ $st->created_at ? $st->created_at->format('d M Y H:i') : '-' }}</td>
            <td class="text-right">
              <button class="btn btn-sm btn-outline-primary mr-1 btn-edit-staff" data-id="{{ $st->id }}" data-name="{{ $st->name }}" data-email="{{ $st->email }}" data-role="{{ $st->role }}" style="border-radius: 6px;">Edit</button>
              <button class="btn btn-sm btn-outline-warning mr-1 btn-reset" data-id="{{ $st->id }}" data-name="{{ $st->name }}" style="border-radius: 6px;">Reset Password</button>
              @if(Auth::id() !== $st->id)
                <form action="{{ route('admin.staff.destroy', $st->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus akun staf ini?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius: 6px;">Hapus</button>
                </form>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="text-center py-4 text-muted">Belum ada akun staf tambahan.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Tambah Staf -->
<div class="modal fade" id="createStaffModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form action="{{ route('admin.staff.store') }}" method="POST" class="modal-content" style="border-radius: 12px;">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title font-weight-bold">Form Registrasi Akun Staf Baru</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Nama Lengkap Staf *</label>
          <input type="text" name="name" class="form-control" placeholder="Contoh: Rina Resepsionis / Joko Manager" required>
        </div>
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Email Login *</label>
          <input type="email" name="email" class="form-control" placeholder="rina@fitlife.com" required>
        </div>
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Password *</label>
          <input type="password" name="password" class="form-control" required minlength="4">
        </div>
        <div class="form-group mb-0">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Role / Hak Akses Staf *</label>
          <select name="role" class="form-control" required>
            <option value="receptionist">Resepsionis / Frontdesk</option>
            <option value="manager">Manager Gym</option>
            <option value="admin">Admin / Pemilik Gym</option>
            <option value="trainer">Personal Trainer</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-success font-weight-bold">Simpan Akun Staf</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit Staf -->
<div class="modal fade" id="editStaffModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="editStaffForm" action="" method="POST" class="modal-content" style="border-radius: 12px;">
      @csrf
      @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title font-weight-bold">Edit Data Akun Staf</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Nama Lengkap Staf *</label>
          <input type="text" name="name" id="editStaffName" class="form-control" required>
        </div>
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Email Login *</label>
          <input type="email" name="email" id="editStaffEmail" class="form-control" required>
        </div>
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Role / Hak Akses Staf *</label>
          <select name="role" id="editStaffRole" class="form-control" required>
            <option value="receptionist">Resepsionis / Frontdesk</option>
            <option value="manager">Manager Gym</option>
            <option value="admin">Admin / Pemilik Gym</option>
            <option value="trainer">Personal Trainer</option>
          </select>
        </div>
        <div class="form-group mb-0">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Password Baru (Opsional, kosongkan jika tidak diubah)</label>
          <input type="password" name="password" class="form-control" minlength="4">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary font-weight-bold">Update Data Staf</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Reset Password -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="resetPasswordForm" action="" method="POST" class="modal-content" style="border-radius: 12px;">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title font-weight-bold" id="resetStaffTitle">Reset Password Staf</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="form-group mb-0">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Password Baru Sementara *</label>
          <input type="password" name="new_password" class="form-control" placeholder="Masukkan password baru..." required minlength="4">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-warning font-weight-bold text-dark">Simpan Password Baru</button>
      </div>
    </form>
  </div>
</div>
@endsection

@section('scripts')
<script>
  $(document).ready(function() {
    $('.btn-edit-staff').on('click', function() {
      var id = $(this).data('id');
      var name = $(this).data('name');
      var email = $(this).data('email');
      var role = $(this).data('role');

      $('#editStaffName').val(name);
      $('#editStaffEmail').val(email);
      $('#editStaffRole').val(role);
      $('#editStaffForm').attr('action', '/admin/staff/' + id);
      $('#editStaffModal').modal('show');
    });

    $('.btn-reset').on('click', function() {
      var staffId = $(this).data('id');
      var staffName = $(this).data('name');
      $('#resetStaffTitle').text('Reset Password: ' + staffName);
      $('#resetPasswordForm').attr('action', '/admin/staff/' + staffId + '/reset-password');
      $('#resetPasswordModal').modal('show');
    });
  });
</script>
@endsection
