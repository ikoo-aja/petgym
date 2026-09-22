@extends('layouts.layout')

@section('title', 'Data Staf Operasional — PetGym')
@section('page_title', 'Manajemen Staf Operasional')
@section('page_subtitle', 'Pengelolaan akun tim Resepsionis / Kasir dan Personal Trainer')

@section('content')
<div class="card border-0 shadow-sm bg-white" style="border-radius: 12px;">
  <div class="card-header bg-white border-bottom pt-4 px-4 pb-3 d-flex justify-content-between align-items-center">
    <div>
      <h6 class="font-weight-bold text-dark mb-0">Daftar Akun Staf Operasional</h6>
      <small class="text-muted">Kelola akun Resepsionis/Kasir dan Personal Trainer gym</small>
    </div>
    <button type="button" class="btn btn-sm btn-success font-weight-bold px-3 py-2" data-toggle="modal" data-target="#createStaffModal" style="border-radius: 8px;">
      + Tambah Staf Baru
    </button>
  </div>

  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
          <tr>
            <th class="px-4 py-3">Nama Staf</th>
            <th class="py-3">Email Login</th>
            <th class="py-3">Role / Jabatan</th>
            <th class="py-3">Tanggal Dibuat</th>
            <th class="px-4 py-3 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($staffs as $st)
            <tr>
              <td class="px-4 py-3 font-weight-bold text-dark">{{ $st->name }}</td>
              <td class="py-3 font-weight-semibold text-dark" style="font-size: 13.5px;">
                {{ $st->email }}
                @if($st->hasVerifiedEmail())
                  <span class="badge badge-success ml-1 font-weight-bold px-2 py-1" style="font-size: 9.5px; border-radius: 4px;">Terverifikasi</span>
                @else
                  <span class="badge badge-warning text-dark ml-1 font-weight-bold px-2 py-1" style="font-size: 9.5px; border-radius: 4px;">Belum Verifikasi</span>
                @endif
              </td>
              <td class="py-3">
                @if($st->role === 'supervisor')
                  <span class="badge badge-primary px-3 py-1 font-weight-bold" style="border-radius: 6px;">Supervisor</span>
                @elseif($st->role === 'receptionist')
                  <span class="badge badge-info px-3 py-1 font-weight-bold" style="border-radius: 6px;">Resepsionis / Kasir</span>
                @elseif($st->role === 'trainer')
                  <span class="badge badge-success px-3 py-1 font-weight-bold" style="border-radius: 6px;">Personal Trainer</span>
                @else
                  <span class="badge badge-secondary px-3 py-1 font-weight-bold" style="border-radius: 6px;">{{ ucfirst($st->role) }}</span>
                @endif
              </td>
              <td class="py-3 text-muted small">{{ $st->created_at ? $st->created_at->format('d M Y H:i') : '-' }}</td>
              <td class="px-4 py-3 text-right">
                @if(!$st->hasVerifiedEmail())
                  <form action="{{ route('manager.staff.send-verification', $st->id) }}" method="POST" class="d-inline mr-1">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-info font-weight-bold px-2 py-1" style="border-radius: 6px; font-size: 12px;">
                      Kirim Verifikasi
                    </button>
                  </form>
                @endif
                <form action="{{ route('manager.staff.destroy', $st->id) }}" method="POST" class="d-inline" data-confirm="Hapus akun staf operasional ini?">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger font-weight-bold px-2 py-1" style="border-radius: 6px; font-size: 12px;">
                    Hapus
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center py-4 text-muted small">
                <span class="icon-people h4 d-block mb-1 text-muted"></span>
                Belum ada data staf operasional terdaftar.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal Tambah Staf Operasional -->
<div class="modal fade" id="createStaffModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form action="{{ route('manager.staff.store') }}" method="POST" class="modal-content" style="border-radius: 12px;">
      @csrf
      <div class="modal-header bg-white border-bottom">
        <h5 class="modal-title font-weight-bold text-dark">Registrasi Staf Operasional Baru</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body p-4">
        @if(isset($errors) && $errors->any())
        <div class="alert alert-danger py-2 mb-3" style="font-size: 13px; border-radius: 8px;">
          <strong>Data tidak tersimpan:</strong>
          <ul class="mb-0 pl-3 mt-1">
            @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
          </ul>
        </div>
        @endif

        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark small mb-1">Nama Lengkap Staf <span class="text-danger">*</span></label>
          <input type="text" name="name" class="form-control" placeholder="Contoh: Rina Resepsionis / Dimas Trainer" required>
        </div>

        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark small mb-1">Email Login <span class="text-danger">*</span></label>
          <input type="email" name="email" class="form-control" placeholder="staf@domain.com" required>
        </div>

        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark small mb-1">Kata Sandi</label>
          <div class="input-group">
            <input type="password" name="password" id="staffPassword" class="form-control" placeholder="Minimal 4 karakter (atau gunakan auto-generate)">
            <div class="input-group-append">
              <span class="input-group-text bg-white border-left-0" style="cursor: pointer;" onclick="toggleStaffPw(this)">
                <i class="icon-eye text-muted"></i>
              </span>
            </div>
          </div>
          <div class="custom-control custom-checkbox mt-2">
            <input type="checkbox" class="custom-control-input" id="autoGenPw" onchange="toggleAutoGen(this)">
            <label class="custom-control-label text-muted" style="font-size: 12px;" for="autoGenPw">Generate password default otomatis</label>
          </div>
        </div>

        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark small mb-1">Role / Peran Akun <span class="text-danger">*</span></label>
          <select name="role" id="staffRole" class="form-control" required>
            <option value="supervisor">Supervisor (Pengawas Operasional)</option>
            <option value="receptionist">Resepsionis / Kasir</option>
            <option value="trainer">Personal Trainer (PT)</option>
          </select>
        </div>

        <div class="form-group mb-0" id="staffPhoneGroup" style="display: none;">
          <label class="font-weight-bold text-dark small mb-1">Nomor WhatsApp Trainer</label>
          <input type="text" name="phone" id="staffPhone" class="form-control" placeholder="081234567890" maxlength="20">
          <small class="text-muted" style="font-size: 11px;">Kontak ini akan tampil di jadwal kelas &amp; trainer.</small>
        </div>
      </div>
      <div class="modal-footer bg-light border-top">
        <button type="button" class="btn btn-secondary font-weight-bold px-3" data-dismiss="modal" style="border-radius: 8px;">Batal</button>
        <button type="submit" class="btn btn-success font-weight-bold px-4" style="border-radius: 8px;">Simpan Akun Staf</button>
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
    }
  }

  $(document).ready(function() {
    const roleSel = document.getElementById('staffRole');
    const phoneGroup = document.getElementById('staffPhoneGroup');
    const phoneInput = document.getElementById('staffPhone');

    function syncPhoneField() {
      if (roleSel.value === 'trainer') {
        phoneGroup.style.display = 'block';
      } else {
        phoneGroup.style.display = 'none';
        phoneInput.value = '';
      }
    }

    roleSel.addEventListener('change', syncPhoneField);

    $('#createStaffModal').on('shown.bs.modal', function() {
      syncPhoneField();
    });

    @if(isset($errors) && $errors->any())
      $('#createStaffModal').modal('show');
    @endif
  });
</script>
@endsection
