@extends('layouts.admin')

@section('title', 'Manajemen Staf (RBAC) &mdash; PetGym')
@section('page_title', 'Manajemen Akun Staf & Hak Akses')
@section('page_subtitle', 'Pendaftaran akun Resepsionis, Manager, serta Personal Trainer')

@section('content')
<div class="card-custom">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h6 class="font-weight-bold text-dark mb-0">Daftar Akun Pengelola & Staf Internal</h6>
    @if(!Auth::user() || !Auth::user()->isOwner())
    <button type="button" class="btn btn-sm btn-success font-weight-bold" data-toggle="modal" data-target="#createStaffModal" style="border-radius: 8px;">
      + Tambah Akun Staf Baru
    </button>
    @endif
  </div>

  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
        <tr>
          <th>Nama Staf</th>
          <th>Email Login (Masked for Owner)</th>
          <th>Role / Jabatan</th>
          <th>Tanggal Dibuat</th>
          @if(!Auth::user() || !Auth::user()->isOwner())
          <th class="text-right">Aksi</th>
          @endif
        </tr>
      </thead>
      <tbody>
        @forelse($staffs as $st)
          <tr>
            <td class="font-weight-bold text-dark">{{ $st->name }}</td>
            <td style="font-size: 13.5px;" class="text-dark font-weight-semibold">
              {{ \App\Helpers\PrivacyHelper::maskEmail($st->email) }}
              @if($st->hasVerifiedEmail())
                <span class="badge badge-success ml-1" style="font-size: 9.5px;">Email Terverifikasi</span>
              @else
                <span class="badge badge-warning text-dark ml-1" style="font-size: 9.5px;">Belum Verifikasi Email</span>
              @endif
            </td>
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
            <td style="font-size: 13px;" class="text-dark">{{ $st->created_at ? $st->created_at->format('d M Y H:i') : '-' }}</td>
            @if(!Auth::user() || !Auth::user()->isOwner())
            <td class="text-right">
              @if(!$st->hasVerifiedEmail())
                <form action="{{ route('admin.staff.send-verification', $st->id) }}" method="POST" class="d-inline">
                  @csrf
                  <button type="submit" class="btn btn-sm btn-outline-info" style="border-radius: 6px;">Kirim Verifikasi</button>
                </form>
              @endif
              @if(Auth::id() !== $st->id)
                <form action="{{ route('admin.staff.destroy', $st->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus akun staf ini?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius: 6px;">Hapus</button>
                </form>
              @endif
            </td>
            @endif
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
          <div class="input-group">
            <input type="password" name="password" id="staffPassword" class="form-control" required minlength="4">
            <div class="input-group-append">
              <span class="input-group-text bg-white border-left-0" style="cursor: pointer;" onclick="toggleStaffPw(this)">
                <i class="icon-eye text-muted"></i>
              </span>
            </div>
          </div>
          <div class="custom-control custom-checkbox mt-2">
            <input type="checkbox" class="custom-control-input" id="autoGenPw" onchange="toggleAutoGen(this)">
            <label class="custom-control-label text-muted" style="font-size: 12px;" for="autoGenPw">Generate password default otomatis (staf wajib ganti saat login)</label>
          </div>
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

@endsection

@section('scripts')
<script>
  function toggleStaffPw(btn) {
    const input = btn.closest('.input-group').querySelector('input[type="password"]');
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
      input.type = 'text';
      icon.classList.remove('icon-eye');
      icon.classList.add('icon-eye-slash');
      icon.classList.remove('text-muted');
      icon.classList.add('text-primary');
    } else {
      input.type = 'password';
      icon.classList.remove('icon-eye-slash');
      icon.classList.add('icon-eye');
      icon.classList.remove('text-primary');
      icon.classList.add('text-muted');
    }
  }

  function toggleAutoGen(cb) {
    const pw = document.getElementById('staffPassword');
    if (cb.checked) {
      pw.value = '';
      pw.disabled = true;
      pw.removeAttribute('required');
    } else {
      pw.disabled = false;
      pw.setAttribute('required', '');
    }
  }
</script>
@endsection
