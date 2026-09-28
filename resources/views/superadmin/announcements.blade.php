@extends('layouts.superadmin')

@section('title', 'Pengumuman Sistem &mdash; Superadmin Panel')

@section('page_title', 'Pengumuman')
@section('page_subtitle', 'Kirim pengumuman global ke seluruh dashboard tenant')

@section('styles')
<!-- Quill.js Snow theme CSS -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
  .ql-editor {
    min-height: 180px;
    font-family: 'Muli', sans-serif;
    font-size: 14px;
  }
</style>
@endsection

@section('content')
<div class="row">
  <div class="col-lg-12 mb-4" id="announcements">
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-radius: 10px;">
      <ul class="mb-0 pl-3">
        @foreach($errors->all() as $err)
          <li class="font-weight-bold small">{{ $err }}</li>
        @endforeach
      </ul>
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
    @endif

    <!-- Form Broadcast Baru -->
    <div class="bg-white p-4 rounded shadow-sm">
      <h4 class="font-weight-bold text-black mb-3">Kirim Pengumuman Baru</h4>
      <form action="{{ route('superadmin.announcements.store') }}" method="POST" id="broadcastForm">
        @csrf
        <div class="form-group mb-3">
          <label class="text-black font-weight-bold">Judul Pengumuman <span class="text-danger">*</span></label>
          <input type="text" name="title" class="form-control" placeholder="Contoh: Pembaruan Sistem Harian & Fitur Baru" required value="{{ old('title') }}">
        </div>
        <div class="form-group mb-3">
          <label class="text-black font-weight-bold">Pesan Broadcast ke Tenant <span class="text-danger">*</span></label>
          <!-- Quill Editor -->
          <div id="editor-container" style="background: #fff; border-radius: 4px;">{!! old('message') !!}</div>
          <!-- Hidden Input for HTML Message -->
          <input type="hidden" name="message" id="message-input">
        </div>
        <button type="submit" class="btn btn-primary btn-sm px-4 font-weight-bold">
          <span class="icon-send mr-1"></span> Kirim Broadcast
        </button>
      </form>
    </div>

    <!-- Riwayat Pengumuman -->
    <div class="bg-white p-4 rounded shadow-sm mt-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="font-weight-bold text-black mb-0">Riwayat Pengumuman (Announcement History)</h4>
        <span class="badge badge-light border text-muted px-2 py-1">Total: {{ $announcements->total() }}</span>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead>
            <tr>
              <th class="text-black font-weight-bold" style="width: 140px;">Tanggal</th>
              <th class="text-black font-weight-bold">Judul</th>
              <th class="text-black font-weight-bold">Isi Pesan</th>
              <th class="text-black font-weight-bold">Status</th>
              <th class="text-black font-weight-bold text-right" style="min-width: 180px;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($announcements as $announcement)
            @php
              $createdAtStr = is_object($announcement->created_at) ? $announcement->created_at->format('d M Y H:i') : ($announcement['created_at'] ?? 'Hari ini');
              $annTitle = $announcement->title ?? $announcement['title'];
              $annMsg = $announcement->message ?? $announcement['message'];
              $annStatus = $announcement->status ?? $announcement['status'];
              $isActive = (strcasecmp($annStatus, 'active') === 0);
            @endphp
            <tr id="row-announcement-{{ $announcement->id }}">
              <td style="white-space: nowrap;"><small class="text-muted">{{ $createdAtStr }}</small></td>
              <td class="font-weight-bold text-black">{{ $annTitle }}</td>
              <td style="max-width: 350px;" class="text-black">{!! \Illuminate\Support\Str::limit(strip_tags($annMsg), 90) !!}</td>
              <td>
                @if($isActive)
                  <span class="badge badge-success px-2 py-1 text-white badge-status">Aktif</span>
                @else
                  <span class="badge badge-secondary px-2 py-1 badge-status">Ditarik (Nonaktif)</span>
                @endif
              </td>
              <td style="white-space: nowrap;" class="text-right">
                <button class="btn btn-sm btn-outline-secondary px-2 py-1 mr-1 btn-edit-announcement"
                        data-id="{{ $announcement->id }}"
                        data-title="{{ $annTitle }}"
                        data-message="{{ htmlspecialchars($annMsg, ENT_QUOTES) }}"
                        title="Ubah Konten">
                  <span class="icon-pencil"></span> Ubah
                </button>

                <button class="btn btn-sm {{ $isActive ? 'btn-outline-warning' : 'btn-outline-success' }} px-2 py-1 mr-1 btn-toggle-announcement"
                        data-id="{{ $announcement->id }}"
                        data-title="{{ $annTitle }}"
                        title="{{ $isActive ? 'Tarik Kembali' : 'Aktifkan Kembali' }}">
                  <span class="{{ $isActive ? 'icon-pause' : 'icon-play_arrow' }}"></span>
                  <span class="btn-toggle-text">{{ $isActive ? 'Tarik' : 'Aktifkan' }}</span>
                </button>

                <button class="btn btn-sm btn-outline-danger px-2 py-1 btn-delete-announcement"
                        data-id="{{ $announcement->id }}"
                        data-title="{{ $annTitle }}"
                        title="Hapus">
                  <span class="icon-close"></span> Hapus
                </button>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="5" class="text-center py-4 text-muted">Belum ada pengumuman disiarkan.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <!-- Pagination Footer -->
      <div class="d-flex justify-content-between align-items-center mt-3">
        <small class="text-muted">Menampilkan {{ $announcements->firstItem() ?? 0 }} sampai {{ $announcements->lastItem() ?? 0 }} dari {{ $announcements->total() }} pengumuman</small>
        {{ $announcements->links() }}
      </div>
    </div>
  </div>
</div>
@endsection

@section('modals')
<!-- MODAL: UBAH PENGUMUMAN -->
<div class="modal fade" id="editAnnouncementModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-header-title font-weight-bold text-black">Ubah Pengumuman</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="" method="POST" id="editAnnouncementForm">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="form-group mb-3">
            <label class="text-black font-weight-bold">Judul Pengumuman <span class="text-danger">*</span></label>
            <input type="text" name="title" id="edit_title" class="form-control" required>
          </div>
          <div class="form-group mb-3">
            <label class="text-black font-weight-bold">Pesan Broadcast <span class="text-danger">*</span></label>
            <div id="edit-editor-container" style="background: #fff; border-radius: 4px;"></div>
            <input type="hidden" name="message" id="edit-message-input">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary btn-sm font-weight-bold">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<!-- Quill.js JS library -->
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
  $(document).ready(function() {
    // 1. Inisialisasi Quill Editor untuk Create
    var quillCreate = new Quill('#editor-container', {
      theme: 'snow',
      placeholder: 'Tuliskan pesan broadcast lengkap dengan link info, promo, list bullet, dsb...',
      modules: {
        toolbar: [
          ['bold', 'italic', 'underline', 'strike'],
          [{ 'list': 'ordered'}, { 'list': 'bullet' }],
          ['link', 'clean']
        ]
      }
    });

    // 2. Inisialisasi Quill Editor untuk Edit Modal
    var quillEdit = new Quill('#edit-editor-container', {
      theme: 'snow',
      placeholder: 'Tuliskan pembaruan pesan broadcast...',
      modules: {
        toolbar: [
          ['bold', 'italic', 'underline', 'strike'],
          [{ 'list': 'ordered'}, { 'list': 'bullet' }],
          ['link', 'clean']
        ]
      }
    });

    // Validasi & Sync Form Broadcast Baru
    $('#broadcastForm').on('submit', function(e) {
      const editorHtml = quillCreate.root.innerHTML;
      if (quillCreate.getText().trim().length === 0) {
        e.preventDefault();
        showToast('Validasi Gagal', 'Isi pesan broadcast tidak boleh kosong!', 'error');
        return false;
      }
      $('#message-input').val(editorHtml);
      return true;
    });

    // Modal Edit Pengumuman
    $(document).on('click', '.btn-edit-announcement', function(e) {
      e.preventDefault();
      const id = $(this).data('id');
      const title = $(this).data('title');
      const message = $(this).data('message');

      $('#editAnnouncementForm').attr('action', "{{ url('/superadmin/announcements') }}/" + id);
      $('#edit_title').val(title);
      quillEdit.root.innerHTML = message || '';

      $('#editAnnouncementModal').modal('show');
    });

    // Sync Form Edit
    $('#editAnnouncementForm').on('submit', function(e) {
      const editorHtml = quillEdit.root.innerHTML;
      if (quillEdit.getText().trim().length === 0) {
        e.preventDefault();
        showToast('Validasi Gagal', 'Isi pesan broadcast tidak boleh kosong!', 'error');
        return false;
      }
      $('#edit-message-input').val(editorHtml);
      return true;
    });

    // Toggle Aktif / Tarik Status via Server
    $(document).on('click', '.btn-toggle-announcement', function(e) {
      e.preventDefault();
      const btn = $(this);
      const id = btn.data('id');
      const title = btn.data('title');

      fetch("{{ url('/superadmin/announcements') }}/" + id + "/toggle", {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        }
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === 'success') {
          showToast('Status Pengumuman', data.message, 'success');
          setTimeout(() => window.location.reload(), 700);
        } else {
          showToast('Gagal', 'Tidak dapat mengubah status pengumuman.', 'error');
        }
      })
      .catch(() => {
        showToast('Error', 'Terjadi kesalahan jaringan saat memperbarui status.', 'error');
      });
    });

    // Hapus Pengumuman via Server
    $(document).on('click', '.btn-delete-announcement', function(e) {
      e.preventDefault();
      const id = $(this).data('id');
      const title = $(this).data('title');

      if (!confirm(`Apakah Anda yakin ingin menghapus pengumuman "${title}"?`)) {
        return;
      }

      fetch("{{ url('/superadmin/announcements') }}/" + id, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        }
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === 'success') {
          showToast('Berhasil', data.message, 'success');
          $('#row-announcement-' + id).fadeOut(400, function() { $(this).remove(); });
        } else {
          showToast('Gagal', 'Tidak dapat menghapus pengumuman.', 'error');
        }
      })
      .catch(() => {
        showToast('Error', 'Terjadi kesalahan jaringan saat menghapus data.', 'error');
      });
    });
  });
</script>
@endsection
