@extends('layouts.member')

@section('title', 'Panduan Penggunaan')
@section('page_title', 'Panduan Penggunaan')
@section('page_subtitle', 'Petunjuk langkah demi langkah untuk menggunakan seluruh fasilitas portal anggota.')

@section('content')
<!-- Overview Header Banner -->
<div class="card-custom border-left border-primary mb-3 mb-md-4" style="border-left-width: 4px !important;">
  <h5 class="font-weight-bold text-dark mb-1" style="font-size: 16px;">Selamat Datang di Portal Anggota {{ $tenantName }}</h5>
  <p class="text-muted mb-0 small">Gunakan panduan berikut untuk memahami tata cara transaksi, penjadwalan pelatih pribadi, reservasi kelas, dan penyewaan loker digital.</p>
</div>

<!-- Step by Step Guides -->
<div class="row">
  <!-- Step 1: Sewa Loker -->
  <div class="col-12 col-md-6 mb-3 mb-md-4">
    <div class="card-custom h-100">
      <div class="d-flex align-items-center mb-3">
        <div class="badge badge-primary font-weight-bold px-3 py-2 mr-2" style="font-size: 13px; border-radius: 8px;">Langkah 1</div>
        <h6 class="font-weight-bold text-dark mb-0">Menyewa Loker Digital</h6>
      </div>
      <ol class="text-muted pl-3 mb-0 small" style="line-height: 1.8;">
        <li>Buka menu <strong>Penyewaan Loker</strong> dari bilah navigasi.</li>
        <li>Pilih nomor loker yang berstatus <strong>Tersedia (Kotak Hijau)</strong>.</li>
        <li>Pilih durasi sewa (<strong>Harian Rp 15.000</strong> atau <strong>Bulanan Rp 150.000</strong>). *Member Tingkat Premium gratis bulanan.</li>
        <li>Pilih metode pembayaran (QRIS / Transfer Bank / Tunai di Kasir).</li>
        <li>Klik <strong>Konfirmasi & Bayar Sekarang</strong>. Sistem akan menghasilkan <strong>Kode PIN Akses 6 Digit</strong>.</li>
        <li>Gunakan PIN tersebut pada unit loker atau tunjukkan kepada petugas resepsionis.</li>
      </ol>
    </div>
  </div>

  <!-- Step 2: Personal Trainer -->
  <div class="col-12 col-md-6 mb-3 mb-md-4">
    <div class="card-custom h-100">
      <div class="d-flex align-items-center mb-3">
        <div class="badge badge-primary font-weight-bold px-3 py-2 mr-2" style="font-size: 13px; border-radius: 8px;">Langkah 2</div>
        <h6 class="font-weight-bold text-dark mb-0">Beli Kuota & Jadwal Pelatih Pribadi (PT)</h6>
      </div>
      <ol class="text-muted pl-3 mb-0 small" style="line-height: 1.8;">
        <li>Buka menu <strong>Pelatih Pribadi</strong> dari navigasi portal.</li>
        <li>Jika kuota Anda kosong, pilih pelatih yang diinginkan lalu klik <strong>Beli Kuota</strong>.</li>
        <li>Pilih paket kuota sesi (5, 10, atau 20 sesi) dan selesaikan pembayaran. Kuota akan langsung bertambah.</li>
        <li>Klik <strong>Jadwalkan</strong> pada profil pelatih, pilih tanggal dan slot jam sesi latihan.</li>
        <li>Sistem otomatis memverifikasi ketersediaan jadwal tanpa bentrok.</li>
      </ol>
    </div>
  </div>

  <!-- Step 3: Kelas Kebugaran -->
  <div class="col-12 col-md-6 mb-3 mb-md-4">
    <div class="card-custom h-100">
      <div class="d-flex align-items-center mb-3">
        <div class="badge badge-primary font-weight-bold px-3 py-2 mr-2" style="font-size: 13px; border-radius: 8px;">Langkah 3</div>
        <h6 class="font-weight-bold text-dark mb-0">Reservasi Kelas Kebugaran</h6>
      </div>
      <ol class="text-muted pl-3 mb-0 small" style="line-height: 1.8;">
        <li>Buka menu <strong>Kelas Kebugaran</strong> dari bilah navigasi.</li>
        <li>Lihat daftar kelas yang tersedia (Zumba, Yoga, HIIT, Spinning, dll).</li>
        <li>Klik <strong>Reservasi Kelas</strong> dan tentukan tanggal pelaksanaan kelas.</li>
        <li>Jika kuota masih ada, status langsung <strong>Dikonfirmasi</strong>.</li>
        <li>Jika kuota telah penuh, Anda otomatis ditempatkan pada <strong>Daftar Antrean Tunggu (Waitlist)</strong>.</li>
        <li>Harap batalkan reservasi jika berhalangan hadir untuk menghindari sanksi ketidakhadiran (No-Show).</li>
      </ol>
    </div>
  </div>

  <!-- Step 4: Upgrade Keanggotaan -->
  <div class="col-12 col-md-6 mb-3 mb-md-4">
    <div class="card-custom h-100">
      <div class="d-flex align-items-center mb-3">
        <div class="badge badge-primary font-weight-bold px-3 py-2 mr-2" style="font-size: 13px; border-radius: 8px;">Langkah 4</div>
        <h6 class="font-weight-bold text-dark mb-0">Aktivasi & Perpanjang Langganan di Kasir</h6>
      </div>
      <ol class="text-muted pl-3 mb-0 small" style="line-height: 1.8;">
        <li>Buka menu <strong>Keanggotaan</strong> atau <strong>Beranda</strong> pada portal.</li>
        <li>Lihat daftar pilihan paket (Basic, Standard, atau Premium) beserta rincian fasilitasnya.</li>
        <li>Kunjungi <strong>Meja Kasir Gym</strong> dan tunjukkan <strong>PIN / Kode Akses</strong> Anda.</li>
        <li>Selesaikan pembayaran di kasir (Tunai, QRIS, atau Transfer). Akun dan seluruh fitur kebugaran akan aktif seketika.</li>
      </ol>
    </div>
  </div>
</div>

<!-- FAQ Section -->
<div class="card-custom">
  <h6 class="font-weight-bold text-dark mb-3">Pertanyaan yang Sering Diajukan (FAQ)</h6>
  <div class="accordion" id="faqAccordion">
    <div class="border-bottom py-2">
      <strong class="text-dark d-block mb-1" style="font-size: 13.5px;">Bagaimana cara mencetak struk bukti pembayaran?</strong>
      <small class="text-muted">Buka menu <strong>Riwayat & Tagihan</strong>, klik tombol <strong>Bukti Struk</strong> pada transaksi yang diinginkan, lalu pilih <strong>Cetak Struk</strong>.</small>
    </div>
    <div class="border-bottom py-2">
      <strong class="text-dark d-block mb-1" style="font-size: 13.5px;">Apa yang terjadi jika saya tidak hadir di kelas tanpa pembatalan (No-Show)?</strong>
      <small class="text-muted">Pelanggaran No-Show sebanyak 3 kali akan menyebabkan penangguhan hak reservasi kelas selama 7 hari kalender. Selalu batalkan pendaftaran jika berhalangan hadir.</small>
    </div>
    <div class="py-2">
      <strong class="text-dark d-block mb-1" style="font-size: 13.5px;">Bagaimana cara memperbarui informasi kontak atau kata sandi?</strong>
      <small class="text-muted">Buka menu <strong>Profil & Pengaturan</strong> di pojok kanan atas untuk memperbarui nomor telepon, data diri, atau mengubah kata sandi akun Anda.</small>
    </div>
  </div>
</div>
@endsection
