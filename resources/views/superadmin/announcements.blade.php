@extends('layouts.superadmin')

@section('title', 'Pengumuman Sistem &mdash; Superadmin Panel')

@section('page_title', 'Pengumuman')
@section('page_subtitle', 'Kirim pengumuman global ke semua tenant')

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
    <!-- Form Broadcast -->
    <div class="bg-white p-4 rounded shadow-sm">
      <h4 class="font-weight-bold text-black mb-3">Kirim Pengumuman Baru</h4>
      <form action="#" method="POST" id="broadcastForm">
        @csrf
        <div class="form-group">
          <label class="text-black font-weight-bold">Judul Pengumuman</label>
          <input type="text" name="title" class="form-control" placeholder="Contoh: Maintenance Sistem Harian" required>
        </div>
        <div class="form-group">
          <label class="text-black font-weight-bold">Pesan Broadcast ke Tenant</label>
          <!-- Quill Editor -->
          <div id="editor-container" style="background: #fff; border-radius: 4px;"></div>
          <!-- Hidden Input for HTML Message -->
          <input type="hidden" name="message" id="message-input">
        </div>
        <button type="submit" class="btn btn-primary btn-sm px-4">Kirim Broadcast</button>
      </form>
    </div>

    <!-- Riwayat Pengumuman -->
    <div class="bg-white p-4 rounded shadow-sm mt-4">
      <h4 class="font-weight-bold text-black mb-3">Riwayat Pengumuman (Announcement History)</h4>
      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th class="text-black font-weight-bold">Tanggal</th>
              <th class="text-black font-weight-bold">Judul</th>
              <th class="text-black font-weight-bold">Isi Pesan</th>
              <th class="text-black font-weight-bold">Status</th>
              <th class="text-black font-weight-bold">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($announcements as $announcement)
            @php
              $createdAtStr = is_object($announcement->created_at) ? $announcement->created_at->format('d M Y H:i') : ($announcement['created_at'] ?? 'Hari ini');
              $annTitle = $announcement->title ?? $announcement['title'];
              $annMsg = $announcement->message ?? $announcement['message'];
              $annStatus = $announcement->status ?? $announcement['status'];
            @endphp
            <tr>
              <td style="white-space: nowrap;"><small class="text-muted">{{ $createdAtStr }}</small></td>
              <td class="font-weight-bold text-black">{{ $annTitle }}</td>
              <td style="max-width: 350px;">{!! \Illuminate\Support\Str::limit(strip_tags($annMsg), 90) !!}</td>
              <td>
                @if($annStatus == 'Active')
                  <span class="badge badge-success px-2 py-1 text-white">Aktif</span>
                @else
                  <span class="badge badge-secondary px-2 py-1">Ditarik (Recalled)</span>
                @endif
              </td>
              <td style="white-space: nowrap;">
                <button class="btn btn-sm btn-outline-secondary px-2 py-1 mr-1 btn-edit-announcement" data-id="{{ $announcement->id ?? 0 }}" data-title="{{ $annTitle }}" data-message="{{ $annMsg }}" title="Edit">
                  <span class="icon-pencil"></span> Edit
                </button>
                @if($annStatus == 'Active')
                  <button class="btn btn-sm btn-outline-warning px-2 py-1 mr-1 btn-recall-announcement" title="Tarik Kembali">
                    <span class="icon-pause"></span> Tarik
                  </button>
                @else
                  <button class="btn btn-sm btn-outline-success px-2 py-1 mr-1 btn-activate-announcement" title="Aktifkan Kembali">
                    <span class="icon-play_arrow"></span> Aktifkan
                  </button>
                @endif
                <button class="btn btn-sm btn-outline-danger px-2 py-1 btn-delete-announcement" title="Hapus">
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
    </div>
  </div>
</div>
@endsection

@section('scripts')
<!-- Quill.js JS library -->
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
  $(document).ready(function() {
    // Inisialisasi Quill Editor
    var quill = new Quill('#editor-container', {
      theme: 'snow',
      placeholder: 'Tuliskan pesan broadcast lengkap dengan link promo, text bold, list bullet, dsb...',
      modules: {
        toolbar: [
          ['bold', 'italic', 'underline', 'strike'],
          [{ 'list': 'ordered'}, { 'list': 'bullet' }],
          ['link', 'clean']
        ]
      }
    });

    // 1. Submit form broadcast secara dinamis
    $('#broadcastForm').on('submit', function(e) {
      e.preventDefault();
      const title = $('input[name="title"]').val();
      const editorHtml = quill.root.innerHTML;
      
      if (quill.getText().trim().length === 0) {
        showToast('Validasi Gagal', 'Isi pesan broadcast tidak boleh kosong!', 'error');
        return false;
      }

      // Format Tanggal Hari Ini
      const now = new Date();
      const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
      const formattedDate = `${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()} ${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}`;

      // Tambahkan baris data dinamis
      const newRow = `
        <tr class="new-announcement-row" style="background-color: #f6fff6;">
          <td><small class="text-muted">${formattedDate}</small></td>
          <td class="font-weight-bold text-black">${title}</td>
          <td style="max-width: 350px;">${quill.getText().substring(0, 90)}...</td>
          <td><span class="badge badge-success px-2 py-1 text-white">Aktif</span></td>
          <td style="white-space: nowrap;">
            <button class="btn btn-sm btn-outline-secondary px-2 py-1 mr-1 btn-edit-announcement" data-title="${title}" data-message='${editorHtml}' title="Edit">
              <span class="icon-pencil"></span> Edit
            </button>
            <button class="btn btn-sm btn-outline-warning px-2 py-1 mr-1 btn-recall-announcement" title="Tarik Kembali">
              <span class="icon-pause"></span> Tarik
            </button>
            <button class="btn btn-sm btn-outline-danger px-2 py-1 btn-delete-announcement" title="Hapus">
              <span class="icon-close"></span> Hapus
            </button>
          </td>
        </tr>
      `;

      $('table tbody').prepend(newRow);
      
      // Reset form
      $('input[name="title"]').val('');
      quill.root.innerHTML = '';
      
      showToast('Pengumuman Disiarkan', `Pengumuman "${title}" berhasil disiarkan ke seluruh dashboard tenant!`, 'success');

      setTimeout(function() {
        $('.new-announcement-row').first().css('background-color', '');
      }, 2000);
    });

    // 2. Aksi Tarik Pengumuman (Recall)
    $(document).on('click', '.btn-recall-announcement', function(e) {
      e.preventDefault();
      const row = $(this).closest('tr');
      const statusBadge = row.find('.badge-success, .badge-secondary');
      const title = row.find('td:nth-child(2)').text();
      
      statusBadge.removeClass('badge-success').addClass('badge-secondary').text('Ditarik (Recalled)');
      $(this).removeClass('btn-outline-warning btn-recall-announcement').addClass('btn-outline-success btn-activate-announcement').html('<span class="icon-play_arrow"></span> Aktifkan').attr('title', 'Aktifkan Kembali');
      
      showToast('Pengumuman Ditarik', `Pengumuman "${title}" berhasil dinonaktifkan dari dashboard tenant.`, 'warning');
    });

    // 3. Aksi Aktifkan Pengumuman Kembali
    $(document).on('click', '.btn-activate-announcement', function(e) {
      e.preventDefault();
      const row = $(this).closest('tr');
      const statusBadge = row.find('.badge-success, .badge-secondary');
      const title = row.find('td:nth-child(2)').text();
      
      statusBadge.removeClass('badge-secondary').addClass('badge-success').text('Aktif');
      $(this).removeClass('btn-outline-success btn-activate-announcement').addClass('btn-outline-warning btn-recall-announcement').html('<span class="icon-pause"></span> Tarik').attr('title', 'Tarik Kembali');
      
      showToast('Pengumuman Aktif', `Pengumuman "${title}" disiarkan kembali ke dashboard tenant.`, 'success');
    });

    // 4. Aksi Hapus Pengumuman
    $(document).on('click', '.btn-delete-announcement', function(e) {
      e.preventDefault();
      const row = $(this).closest('tr');
      const title = row.find('td:nth-child(2)').text();
      
      row.css('background-color', '#fff3f3');
      setTimeout(function() {
        row.fadeOut(400, function() {
          row.remove();
          showToast('Pengumuman Dihapus', `Pengumuman "${title}" berhasil dibersihkan dari riwayat.`, 'error');
        });
      }, 100);
    });

    // 5. Aksi Edit Pengumuman
    $(document).on('click', '.btn-edit-announcement', function() {
      const title = $(this).data('title');
      const message = $(this).data('message');
      
      $('input[name="title"]').val(title);
      quill.root.innerHTML = message;
      
      $('html, body').animate({
        scrollTop: $("#broadcastForm").offset().top - 100
      }, 500);
      
      showToast('Memuat Pengumuman', 'Konten pengumuman dimuat ke editor untuk diedit.', 'info');
    });
  });
</script>
@endsection
