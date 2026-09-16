@extends('layouts.admin')

@section('title', 'Kelas & Trainer &mdash; PetGym')
@section('page_title', 'Manajemen Kelas & Trainer')
@section('page_subtitle', 'Pengaturan jadwal kelas olahraga dan entri data pelatih (Trainer)')

@section('content')
@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-radius: 8px;">
    <strong>Sukses!</strong> {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">&times;</button>
  </div>
@endif

<div class="row">
  <!-- Left Side: Jadwal Kelas -->
  <div class="col-md-7">
    <div class="card-custom">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="font-weight-bold text-dark mb-0">Jadwal Kelas Senam & Kebugaran</h6>
        @if(Auth::user() && Auth::user()->isManager())
        <button type="button" class="btn btn-sm btn-success font-weight-bold" data-toggle="modal" data-target="#addClassModal" style="border-radius: 8px;">
          + Tambah Kelas Baru
        </button>
        @endif
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
            <tr>
              <th>Hari</th>
              <th>Nama Kelas</th>
              <th>Ruangan & Jam</th>
              <th>Kuota Harian</th>
              <th>Trainer</th>
              @if(Auth::user() && Auth::user()->isManager())
              <th class="text-right">Aksi</th>
              @endif
            </tr>
          </thead>
          <tbody>
            @forelse($classes as $c)
              <tr>
                <td><span class="badge badge-info px-2 py-1">{{ $c->day }}</span></td>
                <td>
                  <div class="font-weight-bold text-dark">{{ $c->name }}</div>
                </td>
                <td style="font-size: 12.5px;" class="text-dark">
                  <div>{{ $c->room ?? 'Belum Set' }}</div>
                  <small class="text-muted">{{ substr($c->start_time, 0, 5) }} - {{ substr($c->end_time, 0, 5) }}</small>
                </td>
                <td style="font-size: 12.5px;" class="text-dark font-weight-bold">{{ $c->max_capacity }} Peserta</td>
                <td style="font-size: 12.5px;" class="text-dark font-weight-bold">{{ $c->trainer ? $c->trainer->name : 'Tanpa Trainer' }}</td>
                @if(Auth::user() && Auth::user()->isManager())
                <td class="text-right">
                  <button class="btn btn-sm btn-outline-primary btn-edit-class mr-1"
                    data-id="{{ $c->id }}"
                    data-name="{{ $c->name }}"
                    data-day="{{ $c->day }}"
                    data-start_time="{{ substr($c->start_time, 0, 5) }}"
                    data-end_time="{{ substr($c->end_time, 0, 5) }}"
                    data-room="{{ $c->room }}"
                    data-max_capacity="{{ $c->max_capacity }}"
                    data-trainer_id="{{ $c->trainer_id }}"
                    style="border-radius: 6px; font-weight: bold;">Edit</button>
                  <form action="{{ route('admin.classes.destroy', $c->id) }}" method="POST" class="d-inline" data-confirm="Hapus kelas ini?">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius: 6px;">Hapus</button>
                  </form>
                </td>
                @endif
              </tr>
            @empty
              <tr>
                <td colspan="{{ Auth::user() && Auth::user()->isManager() ? '6' : '5' }}" class="text-center py-4 text-muted">Belum ada jadwal kelas dari Manager.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Right Side: Trainer Management -->
  <div class="col-md-5">
    <div class="card-custom">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="font-weight-bold text-dark mb-0">Daftar Trainer / Pelatih</h6>
        @if(Auth::user() && Auth::user()->isManager())
        <button type="button" class="btn btn-sm btn-success font-weight-bold" data-toggle="modal" data-target="#addTrainerModal" style="border-radius: 8px;">
          + Tambah Trainer
        </button>
        @else
        <span class="badge badge-info px-2 py-1 font-weight-bold" style="border-radius: 6px; background: #e0f2fe; color: #0369a1;">
          Read-Only
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
                <td style="font-size: 13px;" class="text-dark">{{ $t->specialization ?? 'General' }}</td>
                @if(Auth::user() && Auth::user()->isManager())
                <td class="text-right">
                  <button class="btn btn-sm btn-outline-primary mr-1 btn-edit-trainer"
                    data-id="{{ $t->id }}"
                    data-name="{{ $t->name }}"
                    data-phone="{{ $t->phone }}"
                    data-specialization="{{ $t->specialization }}"
                    style="border-radius: 6px;">Edit</button>
                  <form action="{{ route('admin.classes.destroy-trainer', $t->id) }}" method="POST" class="d-inline" data-confirm="Hapus trainer ini?">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius: 6px;">Hapus</button>
                  </form>
                </td>
                @endif
              </tr>
            @empty
              <tr>
                <td colspan="3" class="text-center py-4 text-muted">Belum ada data trainer.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal Edit Kelas -->
<div class="modal fade" id="editClassModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="editClassForm" action="" method="POST" class="modal-content" style="border-radius: 12px;">
      @csrf
      @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title font-weight-bold">Edit Jadwal Kelas</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Nama Kelas *</label>
          <input type="text" name="name" id="editClassName" class="form-control" required>
        </div>
        <div class="form-group mb-3">
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
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Ruangan / Studio *</label>
          <input type="text" name="room" id="editClassRoom" class="form-control" placeholder="Contoh: Studio Yoga / Lantai 2" required>
        </div>
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Kuota Harian (Maksimal Peserta) *</label>
          <input type="number" name="max_capacity" id="editClassMaxCapacity" class="form-control" required min="1">
        </div>
        <div class="form-group mb-0">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Pilih Instruktur / Trainer</label>
          <select name="trainer_id" id="editClassTrainerId" class="form-control">
            <option value="">-- Tanpa Trainer / Opsional --</option>
            @foreach($trainers as $t)
              <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->specialization }})</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary font-weight-bold">Update Kelas</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Tambah Kelas Baru -->
<div class="modal fade" id="addClassModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form action="{{ route('admin.classes.store') }}" method="POST" class="modal-content" style="border-radius: 12px;">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title font-weight-bold text-dark">Tambah Jadwal Kelas Baru</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
      </div>
      <div class="modal-body">
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Nama Kelas *</label>
          <input type="text" name="name" class="form-control" placeholder="Contoh: Yoga Evening / Pound Fit / Zumba" required>
        </div>
        <div class="form-group mb-3">
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
        <div class="row mb-3">
          <div class="col-md-6">
            <label class="font-weight-bold text-dark" style="font-size: 13px;">Jam Mulai *</label>
            <input type="time" name="start_time" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="font-weight-bold text-dark" style="font-size: 13px;">Jam Selesai *</label>
            <input type="time" name="end_time" class="form-control" required>
          </div>
        </div>
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Ruangan / Studio</label>
          <input type="text" name="room" class="form-control" placeholder="Contoh: Studio Utama / Lantai 2">
        </div>
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Kuota Maksimal Peserta *</label>
          <input type="number" name="max_capacity" class="form-control" placeholder="Contoh: 20" required min="1" value="20">
        </div>
        <div class="form-group mb-0">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Pilih Instruktur / Trainer</label>
          <select name="trainer_id" class="form-control">
            <option value="">-- Tanpa Trainer / Opsional --</option>
            @foreach($trainers as $t)
              <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->specialization }})</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-success font-weight-bold">Simpan Kelas Baru</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit Trainer -->
<div class="modal fade" id="editTrainerModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="editTrainerForm" action="" method="POST" class="modal-content" style="border-radius: 12px;">
      @csrf
      @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title font-weight-bold">Edit Data Trainer</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Nama Trainer *</label>
          <input type="text" name="name" id="editTrainerName" class="form-control" required>
        </div>
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Nomor Telepon</label>
          <input type="text" name="phone" id="editTrainerPhone" class="form-control">
        </div>
        <div class="form-group mb-0">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Spesialisasi</label>
          <input type="text" name="specialization" id="editTrainerSpecialization" class="form-control">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary font-weight-bold">Update Trainer</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Tambah Trainer Baru -->
<div class="modal fade" id="addTrainerModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form action="{{ route('admin.classes.store-trainer') }}" method="POST" class="modal-content" style="border-radius: 12px;">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title font-weight-bold text-dark">Tambah Trainer Baru</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
      </div>
      <div class="modal-body">
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Nama Trainer *</label>
          <input type="text" name="name" class="form-control" placeholder="Contoh: Budi Santoso" required>
        </div>
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Nomor Telepon / WhatsApp</label>
          <input type="text" name="phone" class="form-control" placeholder="081234567890">
        </div>
        <div class="form-group mb-0">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Spesialisasi / Keahlian</label>
          <input type="text" name="specialization" class="form-control" placeholder="Contoh: Bodybuilding, Yoga, Personal Trainer">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-success font-weight-bold">Simpan Trainer</button>
      </div>
    </form>
  </div>
</div>
@endsection

@section('scripts')
<script>
  $(document).ready(function() {
    $('.btn-edit-class').on('click', function() {
      var id = $(this).data('id');
      $('#editClassName').val($(this).data('name'));
      $('#editClassDay').val($(this).data('day'));
      $('#editClassStartTime').val($(this).data('start_time'));
      $('#editClassEndTime').val($(this).data('end_time'));
      $('#editClassRoom').val($(this).data('room'));
      $('#editClassMaxCapacity').val($(this).data('max_capacity'));
      $('#editClassTrainerId').val($(this).data('trainer_id'));

      $('#editClassForm').attr('action', '/admin/classes/' + id);
      $('#editClassModal').modal('show');
    });

    $('.btn-edit-trainer').on('click', function() {
      var id = $(this).data('id');
      $('#editTrainerName').val($(this).data('name'));
      $('#editTrainerPhone').val($(this).data('phone'));
      $('#editTrainerSpecialization').val($(this).data('specialization'));
      $('#editTrainerForm').attr('action', '/admin/trainers/' + id);
      $('#editTrainerModal').modal('show');
    });
  });
</script>
@endsection
