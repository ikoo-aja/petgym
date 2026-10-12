<script>
  /**
   * Helper Universal Format Rupiah
   * Menformat angka ke format Rupiah (cth: 50000 -> 50.000)
   */
  function formatRupiahNumber(val) {
    if (val === null || val === undefined || val === '') return '';
    var number_string = val.toString().replace(/[^0-9]/g, '');
    var split = number_string.split(',');
    var sisa = split[0].length % 3;
    var rupiah = split[0].substr(0, sisa);
    var ribuan = split[0].substr(sisa).match(/\d{3}/gi);

    if (ribuan) {
      var separator = sisa ? '.' : '';
      rupiah += separator + ribuan.join('.');
    }

    rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
    return rupiah;
  }

  function unformatRupiahNumber(val) {
    if (!val) return 0;
    return parseInt(val.toString().replace(/[^0-9]/g, ''), 10) || 0;
  }

  $(document).ready(function() {
    // 1. Format realtime saat user mengetik pada input dengan class .rupiah-input / .format-rupiah
    $(document).on('input', '.rupiah-input, .format-rupiah', function() {
      var rawVal = $(this).val();
      var formatted = formatRupiahNumber(rawVal);
      $(this).val(formatted);
    });

    // 2. Format awal untuk input yang sudah memiliki value saat halaman dimuat
    $('.rupiah-input, .format-rupiah').each(function() {
      var initialVal = $(this).val();
      if (initialVal) {
        $(this).val(formatRupiahNumber(initialVal));
      }
    });

    // 3. Bersihkan titik pemisah sebelum form disubmit agar backend menerima angka murni (integer)
    $(document).on('submit', 'form', function() {
      $(this).find('.rupiah-input, .format-rupiah').each(function() {
        var cleanVal = $(this).val().replace(/[^0-9]/g, '');
        $(this).val(cleanVal);
      });
    });
  });
</script>
