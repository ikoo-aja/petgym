@extends('layouts.layout')

@section('title', 'Data Staf Operasional — PetGym')
@section('page_title', 'Data Staf')
@section('page_subtitle', 'Pengelolaan akun staf operasional lapangan')

@section('content')
<div class="card border-0 shadow-sm bg-white" style="border-radius: 12px;">
  <div class="card-header bg-white border-bottom pt-4 px-4 pb-3 d-flex justify-content-between align-items-center">
    <div>
      <h6 class="font-weight-bold text-dark mb-0">Daftar Akun Operasional</h6>
      <small class="text-muted">Kelola akun tim operasional gym</small>
    </div>
    <button type="button" class="btn btn-sm btn-success font-weight-bold px-3 py-2" data-toggle="modal" data-target="#createStaffModal" style="border-radius: 8px;">
      + Tambah Akun Baru
    </button>
  </div>

  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
          <tr>
            <th class="px-4 py-3">Nama</th>
            <th class="py-3">Email Login</th>
            <th class="py-3">Role / Jabatan</th>
            <th class="py-3">Status Akun</th>
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
              <td class="py-3">
                @if($st->isActive())
                  <span class="badge badge-success px-2 py-1 font-weight-bold" style="border-radius: 6px; font-size: 11px;">
                    <i class="icon-check mr-1"></i> Aktif
                  </span>
                @else
                  <span class="badge badge-danger px-2 py-1 font-weight-bold" style="border-radius: 6px; font-size: 11px;">
                    <i class="icon-close mr-1"></i> Non-Aktif
                  </span>
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
                @if(Auth::id() !== $st->id)
                  <form action="{{ route('manager.staff.toggle-status', $st->id) }}" method="POST" class="d-inline mr-1" data-confirm="{{ $st->isActive() ? 'Nonaktifkan akun staf ini? Pengguna tidak akan bisa login atau membuka shift.' : 'Aktifkan kembali akun staf ini?' }}">
                    @csrf
                    @if($st->isActive())
                      <button type="submit" class="btn btn-sm btn-outline-warning text-dark font-weight-bold px-2 py-1" style="border-radius: 6px; font-size: 12px;" title="Nonaktifkan Akun">
                        Kunci Akses
                      </button>
                    @else
                      <button type="submit" class="btn btn-sm btn-outline-success font-weight-bold px-2 py-1" style="border-radius: 6px; font-size: 12px;" title="Buka Kunci Akun">
                        Buka Akses
                      </button>
                    @endif
                  </form>
                @endif
                {{-- Tombol Approval Reset Password jika ada request pending --}}
                @if($st->latestPendingPasswordReset && Auth::id() !== $st->id)
                  <form action="{{ route('manager.staff.password-resets.approve', $st->latestPendingPasswordReset->id) }}" method="POST" class="d-inline mr-1" onsubmit="return confirm('Setujui permohonan reset kata sandi? Tautan verifikasi akan dikirimkan ke email terdaftar.');">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-warning text-dark font-weight-bold px-2 py-1" style="border-radius: 6px; font-size: 12px;" title="Setujui permohonan reset kata sandi">
                      <i class="icon-key mr-1"></i> Setujui Reset
                    </button>
                  </form>
                @endif
                @if(Auth::id() !== $st->id)
                <form action="{{ route('manager.staff.destroy', $st->id) }}" method="POST" class="d-inline" data-confirm="Hapus akun ini?">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger font-weight-bold px-2 py-1" style="border-radius: 6px; font-size: 12px;">
                    Hapus
                  </button>
                </form>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center py-4 text-muted small">
                <span class="icon-people h4 d-block mb-1 text-muted"></span>
                Belum ada data akun operasional terdaftar.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal Tambah Akun Baru -->
<div class="modal fade" id="createStaffModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form action="{{ route('manager.staff.store') }}" method="POST" class="modal-content" style="border-radius: 12px;">
      @csrf
      <div class="modal-header bg-white border-bottom">
        <h5 class="modal-title font-weight-bold text-dark">Registrasi Akun Baru</h5>
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
          <label class="font-weight-bold text-dark small mb-1">Nama Lengkap <span class="text-danger">*</span></label>
          <input type="text" name="name" class="form-control" placeholder="Contoh: Rina / Dimas" required>
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

        <div id="trainerExtraFields" style="display: none;">
          <div class="form-group mb-3">
            <label class="font-weight-bold text-dark small mb-1">Nomor WhatsApp / Kontak</label>
            <input type="text" name="phone" id="staffPhone" class="form-control" placeholder="081234567890" maxlength="20">
            <small class="text-muted" style="font-size: 11px;">Kontak ini akan tampil di jadwal kelas &amp; penugasan trainer.</small>
          </div>
          <div class="form-group mb-0">
            <label class="font-weight-bold text-dark small mb-1">Spesialisasi / Keahlian Trainer</label>
            <input type="text" name="specialization" id="staffSpecialization" class="form-control" placeholder="Contoh: Bodybuilding, Yoga, Zumba, HIIT, Fat Loss">
            <small class="text-muted" style="font-size: 11px;">Keahlian utama pelatih yang akan tampil di master data trainer.</small>
          </div>
        </div>
      </div>
      <div class="modal-footer bg-light border-top">
        <button type="button" class="btn btn-secondary font-weight-bold px-3" data-dismiss="modal" style="border-radius: 8px;">Batal</button>
        <button type="submit" class="btn btn-success font-weight-bold px-4" style="border-radius: 8px;">Simpan Data</button>
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
    const trainerExtra = document.getElementById('trainerExtraFields');
    const phoneInput = document.getElementById('staffPhone');
    const specInput = document.getElementById('staffSpecialization');

    function syncTrainerFields() {
      if (roleSel && roleSel.value === 'trainer') {
        trainerExtra.style.display = 'block';
      } else if (trainerExtra) {
        trainerExtra.style.display = 'none';
        if (phoneInput) phoneInput.value = '';
        if (specInput) specInput.value = '';
      }
    }

    if (roleSel) {
      roleSel.addEventListener('change', syncTrainerFields);
    }

    $('#createStaffModal').on('shown.bs.modal', function() {
      syncTrainerFields();
    });
    @if(isset($errors) && $errors->any())
      $('#createStaffModal').modal('show');
    @endif
  });
</script>
@endsection
