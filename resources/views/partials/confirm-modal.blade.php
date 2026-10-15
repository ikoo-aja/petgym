<!-- Modal Konfirmasi Generik (Bootstrap) -->
<div class="modal fade" id="globalConfirmModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 420px;">
    <div class="modal-content" style="border-radius: 14px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.18);">
      <div class="modal-body text-center" style="padding: 32px 28px 18px;">
        <div id="globalConfirmIcon" style="
          width: 72px;
          height: 72px;
          margin: 0 auto 16px;
          border-radius: 50%;
          background: rgba(244, 63, 94, 0.1);
          color: #f43f5e;
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 36px;
          line-height: 1;
        ">
          <span style="font-weight: 900;">!</span>
        </div>
        <h5 id="globalConfirmTitle" class="font-weight-bold mb-2" style="color: #111827; font-size: 18px;">Konfirmasi Tindakan</h5>
        <p id="globalConfirmMessage" style="color: #4b5563; font-size: 14px; margin: 0; line-height: 1.5;">Apakah Anda yakin?</p>
      </div>
      <div class="modal-footer justify-content-center" style="border-top: none; padding: 0 28px 28px; gap: 10px;">
        <button type="button" id="globalConfirmCancel" class="btn btn-light font-weight-bold" data-dismiss="modal" style="min-width: 110px; border-radius: 8px; padding: 9px 18px;">Batal</button>
        <button type="button" id="globalConfirmOk" class="btn btn-danger font-weight-bold" style="min-width: 110px; border-radius: 8px; padding: 9px 18px; background-color: #f43f5e; border-color: #f43f5e;">Ya, Lanjutkan</button>
      </div>
    </div>
  </div>
</div>

<script>
  /**
   * Tampilkan modal konfirmasi custom (menggantikan window.confirm() bawaan browser
   * yang sering muncul sebagai popup localhost).
   *
   * @param {Object} options
   * @param {string} options.title       Judul modal (default: "Konfirmasi Tindakan").
   * @param {string} options.message     Pesan yang ditampilkan di body modal.
   * @param {string} options.confirmText Teks tombol konfirmasi (default: "Ya, Lanjutkan").
   * @param {string} options.cancelText  Teks tombol batal (default: "Batal").
   * @param {string} options.variant     'danger' | 'warning' | 'primary'.
   * @param {Function} onConfirm         Callback yang dijalankan saat user klik tombol konfirmasi.
   */
  window.showConfirm = function(options, onConfirm) {
    const opts = Object.assign({
      title: 'Konfirmasi Tindakan',
      message: 'Apakah Anda yakin?',
      confirmText: 'Ya, Lanjutkan',
      cancelText: 'Batal',
      variant: 'danger'
    }, options || {});

    const $modal = $('#globalConfirmModal');
    const $title = $('#globalConfirmTitle');
    const $msg   = $('#globalConfirmMessage');
    const $ok    = $('#globalConfirmOk');
    const $icon  = $('#globalConfirmIcon');
    const $cancel = $('#globalConfirmCancel');

    $title.text(opts.title);
    $msg.text(opts.message);
    $ok.text(opts.confirmText);
    $cancel.text(opts.cancelText);

    // Warna tombol sesuai variant
    let btnColor = '#f43f5e';
    let iconBg = 'rgba(244, 63, 94, 0.1)';
    let iconColor = '#f43f5e';
    if (opts.variant === 'warning') {
      btnColor = '#f59e0b';
      iconBg = 'rgba(245, 158, 11, 0.1)';
      iconColor = '#f59e0b';
    } else if (opts.variant === 'primary') {
      btnColor = '#2563eb';
      iconBg = 'rgba(37, 99, 235, 0.1)';
      iconColor = '#2563eb';
    }
    $ok.css({ 'background-color': btnColor, 'border-color': btnColor });
    $icon.css({ 'background': iconBg, 'color': iconColor });

    // Bind klik tombol (off dulu supaya tidak menumpuk handler)
    $ok.off('click').on('click', function() {
      $modal.modal('hide');
      if (typeof onConfirm === 'function') {
        onConfirm();
      }
    });

    $modal.modal('show');
  };

  /**
   * Intercept form submit yang memiliki data-confirm.
   * Menggantikan onsubmit="return confirm('...')" bawaan browser.
   */
  $(document).on('submit', 'form[data-confirm]', function(e) {
    const $form = $(this);
    if ($form.data('confirmed')) {
      return true;
    }

    e.preventDefault();
    e.stopImmediatePropagation();
    const msg = $form.attr('data-confirm');

    // Sesuaikan label tombol konfirmasi dengan jenis aksi pada pesan
    const lower = (msg || '').toLowerCase();
    let confirmText = 'Ya, Lanjutkan';
    let variant = 'danger';
    let title = 'Konfirmasi Tindakan';
    if (lower.indexOf('hapus') !== -1) {
      confirmText = 'Ya, Hapus';
      title = 'Konfirmasi Hapus Data';
    } else if (lower.indexOf('tolak') !== -1) {
      confirmText = 'Ya, Tolak';
      title = 'Konfirmasi Penolakan';
    } else if (lower.indexOf('batalkan') !== -1 || lower.indexOf('batal') !== -1) {
      confirmText = 'Ya, Batalkan';
      title = 'Konfirmasi Pembatalan';
    } else if (lower.indexOf('setujui') !== -1 || lower.indexOf('approve') !== -1) {
      confirmText = 'Ya, Setujui';
      variant = 'primary';
      title = 'Konfirmasi Persetujuan';
    } else if (lower.indexOf('blokir') !== -1) {
      confirmText = 'Ya, Blokir';
      variant = 'warning';
      title = 'Konfirmasi Blokir';
    } else if (lower.indexOf('nonaktifkan') !== -1) {
      confirmText = 'Ya, Nonaktifkan';
      variant = 'warning';
      title = 'Konfirmasi Nonaktifkan Akun';
    } else if (lower.indexOf('aktifkan') !== -1) {
      confirmText = 'Ya, Aktifkan';
      variant = 'primary';
      title = 'Konfirmasi Aktifkan Akun';
    } else if (lower.indexOf('tutup shift') !== -1) {
      confirmText = 'Ya, Tutup Shift';
      variant = 'warning';
      title = 'Konfirmasi Tutup Shift';
    } else if (lower.indexOf('tandai') !== -1 || lower.indexOf('selesai') !== -1) {
      confirmText = 'Ya, Tandai';
      variant = 'primary';
      title = 'Konfirmasi Perubahan Status';
    } else if (lower.indexOf('kembalikan') !== -1) {
      confirmText = 'Ya, Kembalikan';
      variant = 'warning';
      title = 'Konfirmasi Pengembalian';
    }

    if (typeof window.showConfirm !== 'function') {
      if (window.confirm(msg)) {
        $form.data('confirmed', true);
        $form.get(0).submit();
      }
      return;
    }

    window.showConfirm({
      title: title,
      message: msg,
      confirmText: confirmText,
      variant: variant
    }, function() {
      $form.data('confirmed', true);
      $form.get(0).submit();
    });
  });
</script>