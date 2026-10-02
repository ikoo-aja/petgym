@extends('layouts.admin')

@section('title', 'Manajemen Kelas & Trainer — PetGym')
@section('page_title', 'Manajemen Kelas & Trainer')
@section('page_subtitle', 'Pengaturan jadwal master kelas kebugaran rutin dan penugasan instruktur pelatih')

@section('content')
<div class="row">
  <!-- Left Side: Master Jadwal Kelas -->
  <div class="col-lg-7 mb-4">
    <div class="card-custom h-100">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
          <h6 class="font-weight-bold text-dark mb-1">Master Jadwal Kelas Gym</h6>
          <small class="text-muted">Daftar kelas kebugaran mingguan dan instruktur yang bertugas</small>
        </div>
        @if(Auth::user() && Auth::user()->isManager())
        <button type="button" class="btn btn-sm btn-success font-weight-bold" data-toggle="modal" data-target="#addClassModal" style="border-radius: 8px;">
          + Tambah Jadwal Kelas
        </button>
        @endif
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
            <tr>
              <th>Hari</th>
              <th>Nama Kelas & Ruangan</th>
              <th>Jam & Durasi</th>
              <th>Kuota</th>
              <th>Instruktur / Trainer</th>
              @if(Auth::user() && Auth::user()->isManager())
              <th class="text-right">Aksi</th>
              @endif
            </tr>
          </thead>
          <tbody>
            @forelse($classes as $c)
              <tr>
                <td><span class="badge badge-info px-2 py-1 font-weight-bold" style="border-radius: 6px;">{{ $c->day }}</span></td>
                <td>
                  <div class="font-weight-bold text-dark">{{ $c->name }}</div>
                  <small class="text-muted"><i class="icon-room mr-1"></i>{{ $c->room ?? 'Studio Utama' }}</small>
                </td>
                <td style="font-size: 12.5px;" class="text-dark">
                  <div class="font-weight-semibold">{{ substr($c->start_time, 0, 5) }} - {{ substr($c->end_time, 0, 5) }}</div>
                  <small class="text-muted">{{ $c->duration_minutes ?? 60 }} Menit</small>
                </td>
                <td style="font-size: 12.5px;" class="text-dark font-weight-bold">{{ $c->max_capacity }} Peserta</td>
                <td style="font-size: 12.5px;">
                  @if($c->trainer)
                    <span class="badge badge-primary font-weight-bold px-2 py-1" style="border-radius: 6px;">
                      {{ $c->trainer->name }}
                    </span>
                  @else
                    <span class="badge badge-light text-muted border px-2 py-1" style="border-radius: 6px;">
                      Tanpa Trainer
                    </span>
                  @endif
                </td>
                @if(Auth::user() && Auth::user()->isManager())
                <td class="text-right">
                  <button class="btn btn-sm btn-outline-primary btn-edit-class mr-1 font-weight-bold px-2 py-1"
                    data-id="{{ $c->id }}"
                    data-name="{{ $c->name }}"
                    data-day="{{ $c->day }}"
                    data-start_time="{{ substr($c->start_time, 0, 5) }}"
                    data-end_time="{{ substr($c->end_time, 0, 5) }}"
                    data-duration_minutes="{{ $c->duration_minutes ?? 60 }}"
                    data-room="{{ $c->room }}"
                    data-max_capacity="{{ $c->max_capacity }}"
                    data-trainer_id="{{ $c->trainer_id }}"
                    style="border-radius: 6px; font-size: 12px;">Ubah</button>
                  <form action="{{ route('manager.classes.destroy', $c->id) }}" method="POST" class="d-inline" data-confirm="Hapus jadwal kelas ini?">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger font-weight-bold px-2 py-1" style="border-radius: 6px; font-size: 12px;">Hapus</button>
                  </form>
                </td>
                @endif
              </tr>
            @empty
              <tr>
                <td colspan="{{ Auth::user() && Auth::user()->isManager() ? '6' : '5' }}" class="text-center py-4 text-muted">
                  <span class="icon-calendar h4 d-block mb-1 text-muted"></span>
                  Belum ada master jadwal kelas terdaftar.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Right Side: Trainer Management -->
  <div class="col-lg-5 mb-4">
    <div class="card-custom h-100">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
          <h6 class="font-weight-bold text-dark mb-1">Daftar Trainer / Pelatih</h6>
          <small class="text-muted">Master data instruktur kebugaran gym</small>
        </div>
        @if(Auth::user() && Auth::user()->isManager())
        <button type="button" class="btn btn-sm btn-success font-weight-bold" data-toggle="modal" data-target="#addTrainerModal" style="border-radius: 8px;">
          + Tambah Trainer
        </button>
        @else
        <span class="badge badge-info px-2 py-1 font-weight-bold" style="border-radius: 6px; background: #e0f2fe; color: #0369a1;">
          Hanya Lihat
        </span>
        @endif
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
            <tr>
              <th>Nama Trainer</th>
              <th>Spesialisasi</th>
              @if(Auth::user() && Auth::user()->isManager())
              <th class="text-right">Aksi</th>
              @endif
            </tr>
          </thead>
          <tbody>
            @forelse($trainers as $t)
              <tr>
                <td>
                  <div class="font-weight-bold text-dark">{{ $t->name }}</div>
                  <small class="text-muted">{{ $t->phone ? \App\Helpers\PrivacyHelper::maskPhone($t->phone) : '-' }}</small>
                </td>
                <td style="font-size: 13px;" class="text-dark">
                  <span class="badge badge-light border text-dark font-weight-semibold px-2 py-1" style="border-radius: 6px;">
                    {{ $t->specialization ?? 'Umum / Fitness' }}
                  </span>
                </td>
                @if(Auth::user() && Auth::user()->isManager())
                <td class="text-right">
                  <button class="btn btn-sm btn-outline-primary mr-1 btn-edit-trainer font-weight-bold px-2 py-1"
                    data-id="{{ $t->id }}"
                    data-name="{{ $t->name }}"
                    data-phone="{{ $t->phone }}"
                    data-specialization="{{ $t->specialization }}"
                    style="border-radius: 6px; font-size: 12px;">Ubah</button>
                  <form action="{{ route('manager.classes.destroy-trainer', $t->id) }}" method="POST" class="d-inline" data-confirm="Hapus data trainer ini?">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger font-weight-bold px-2 py-1" style="border-radius: 6px; font-size: 12px;">Hapus</button>
                  </form>
                </td>
                @endif
              </tr>
            @empty
              <tr>
                <td colspan="3" class="text-center py-4 text-muted">
                  <span class="icon-person h4 d-block mb-1 text-muted"></span>
                  Belum ada data trainer terdaftar.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal Tambah Kelas Baru -->
<div class="modal fade" id="addClassModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <form action="{{ route('manager.classes.store') }}" method="POST" class="modal-content" style="border-radius: 12px;">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title font-weight-bold text-dark">Tambah Jadwal Master Kelas Baru</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
      </div>
      <div class="modal-body p-4">
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Nama Kelas Kebugaran *</label>
          <input type="text" name="name" class="form-control" placeholder="Contoh: Yoga Morning / Pound Fit / Zumba Party" required>
        </div>
        <div class="row mb-3">
          <div class="col-md-6">
            <label class="font-weight-bold text-dark" style="font-size: 13px;">Hari Pelaksanaan *</label>
            <select name="day" class="form-control" required>
              <option value="Senin">Senin</option>
              <option value="Selasa">Selasa</option>
              <option value="Rabu">Rabu</option>
              <option value="Kamis">Kamis</option>
              <option value="Jumat">Jumat</option>
              <option value="Sabtu">Sabtu</option>
              <option value="Minggu">Minggu</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="font-weight-bold text-dark" style="font-size: 13px;">Durasi (Menit) *</label>
            <input type="number" name="duration_minutes" id="addDurationMinutes" class="form-control" value="60" min="15" required>
          </div>
        </div>
        <div class="row mb-3">
          <div class="col-md-6">
            <label class="font-weight-bold text-dark" style="font-size: 13px;">Jam Mulai *</label>
            <input type="time" name="start_time" id="addStartTime" class="form-control" value="08:00" required>
          </div>
          <div class="col-md-6">
            <label class="font-weight-bold text-dark" style="font-size: 13px;">Jam Selesai *</label>
            <input type="time" name="end_time" id="addEndTime" class="form-control" value="09:00" required>
          </div>
        </div>
        <div class="row mb-3">
          <div class="col-md-6">
            <label class="font-weight-bold text-dark" style="font-size: 13px;">Ruangan / Studio</label>
            <input type="text" name="room" class="form-control" placeholder="Studio Utama / Lantai 2" value="Studio Utama">
          </div>
          <div class="col-md-6">
            <label class="font-weight-bold text-dark" style="font-size: 13px;">Kuota Maksimal Peserta *</label>
            <input type="number" name="max_capacity" class="form-control" placeholder="Contoh: 20" required min="1" value="20">
          </div>
        </div>
        <div class="form-group mb-0">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Pilih Instruktur / Trainer</label>
          <select name="trainer_id" class="form-control">
            <option value="">-- Tanpa Trainer / Belum Ditugaskan --</option>
            @foreach($trainers as $t)
              <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->specialization ?? 'Umum' }})</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-success font-weight-bold px-4">Simpan Data</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Ubah Kelas -->
<div class="modal fade" id="editClassModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <form id="editClassForm" action="" method="POST" class="modal-content" style="border-radius: 12px;">
      @csrf
      @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title font-weight-bold text-dark">Ubah Jadwal Master Kelas</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body p-4">
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Nama Kelas Kebugaran *</label>
          <input type="text" name="name" id="editClassName" class="form-control" required>
        </div>
        <div class="row mb-3">
          <div class="col-md-6">
            <label class="font-weight-bold text-dark" style="font-size: 13px;">Hari Pelaksanaan *</label>
            <select name="day" id="editClassDay" class="form-control" required>
              <option value="Senin">Senin</option>
              <option value="Selasa">Selasa</option>
              <option value="Rabu">Rabu</option>
              <option value="Kamis">Kamis</option>
              <option value="Jumat">Jumat</option>
              <option value="Sabtu">Sabtu</option>
              <option value="Minggu">Minggu</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="font-weight-bold text-dark" style="font-size: 13px;">Durasi (Menit) *</label>
            <input type="number" name="duration_minutes" id="editClassDuration" class="form-control" required min="15">
          </div>
        </div>
        <div class="row mb-3">
          <div class="col-md-6">
            <label class="font-weight-bold text-dark" style="font-size: 13px;">Jam Mulai *</label>
            <input type="time" name="start_time" id="editClassStartTime" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="font-weight-bold text-dark" style="font-size: 13px;">Jam Selesai *</label>
            <input type="time" name="end_time" id="editClassEndTime" class="form-control" required>
          </div>
        </div>
        <div class="row mb-3">
          <div class="col-md-6">
            <label class="font-weight-bold text-dark" style="font-size: 13px;">Ruangan / Studio</label>
            <input type="text" name="room" id="editClassRoom" class="form-control" placeholder="Studio Utama">
          </div>
          <div class="col-md-6">
            <label class="font-weight-bold text-dark" style="font-size: 13px;">Kuota Maksimal Peserta *</label>
            <input type="number" name="max_capacity" id="editClassMaxCapacity" class="form-control" required min="1">
          </div>
        </div>
        <div class="form-group mb-0">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Pilih Instruktur / Trainer</label>
          <select name="trainer_id" id="editClassTrainerId" class="form-control">
            <option value="">-- Tanpa Trainer / Belum Ditugaskan --</option>
            @foreach($trainers as $t)
              <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->specialization ?? 'Umum' }})</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary font-weight-bold px-4">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Tambah Trainer Baru -->
<div class="modal fade" id="addTrainerModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <form action="{{ route('manager.classes.store-trainer') }}" method="POST" class="modal-content" style="border-radius: 12px;">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title font-weight-bold text-dark">Tambah Data Trainer Baru</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
      </div>
      <div class="modal-body p-4">
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Nama Trainer / Instruktur *</label>
          <input type="text" name="name" class="form-control" placeholder="Contoh: Budi Santoso" required>
        </div>
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Nomor Telepon / WhatsApp</label>
          <input type="text" name="phone" class="form-control" placeholder="Contoh: 081234567890">
        </div>
        <div class="form-group mb-0">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Spesialisasi / Keahlian</label>
          <input type="text" name="specialization" class="form-control" placeholder="Contoh: Bodybuilding, Yoga, Zumba, HIIT">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-success font-weight-bold px-4">Simpan Data</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit Trainer -->
<div class="modal fade" id="editTrainerModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <form id="editTrainerForm" action="" method="POST" class="modal-content" style="border-radius: 12px;">
      @csrf
      @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title font-weight-bold text-dark">Ubah Data Trainer</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body p-4">
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Nama Trainer / Instruktur *</label>
          <input type="text" name="name" id="editTrainerName" class="form-control" required>
        </div>
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Nomor Telepon / WhatsApp</label>
          <input type="text" name="phone" id="editTrainerPhone" class="form-control">
        </div>
        <div class="form-group mb-0">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Spesialisasi / Keahlian</label>
          <input type="text" name="specialization" id="editTrainerSpecialization" class="form-control">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary font-weight-bold px-4">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>
@endsection

@section('scripts')
<script>
  $(document).ready(function() {
    // Edit Class Modal
    $('.btn-edit-class').on('click', function() {
      var id = $(this).data('id');
      $('#editClassName').val($(this).data('name'));
      $('#editClassDay').val($(this).data('day'));
      $('#editClassStartTime').val($(this).data('start_time'));
      $('#editClassEndTime').val($(this).data('end_time'));
      $('#editClassDuration').val($(this).data('duration_minutes') || 60);
      $('#editClassRoom').val($(this).data('room') || 'Studio Utama');
      $('#editClassMaxCapacity').val($(this).data('max_capacity') || 20);
      $('#editClassTrainerId').val($(this).data('trainer_id') || '');

      $('#editClassForm').attr('action', '/manager/classes/' + id);
      $('#editClassModal').modal('show');
    });

    // Edit Trainer Modal
    $('.btn-edit-trainer').on('click', function() {
      var id = $(this).data('id');
      $('#editTrainerName').val($(this).data('name'));
      $('#editTrainerPhone').val($(this).data('phone'));
      $('#editTrainerSpecialization').val($(this).data('specialization'));

      $('#editTrainerForm').attr('action', '/manager/trainers/' + id);
      $('#editTrainerModal').modal('show');
    });
  });
</script>
@endsection
