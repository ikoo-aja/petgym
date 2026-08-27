@extends('layouts.member')

@section('title', 'Panduan Penggunaan - PetGym Member')
@section('page_title', 'Panduan & Langkah-Langkah Portal Member')
@section('page_subtitle', 'Petunjuk langkah demi langkah untuk menggunakan semua fitur pada portal member PetGym.')
@section('member_tier_badge', 'Tier ' . strtoupper($member->membership_tier ?? 'Basic'))

@section('content')
<!-- Overview Header Banner -->
<div class="card-custom border-left border-primary mb-4" style="border-left-width: 4px !important;">
  <h5 class="font-weight-bold text-dark mb-1">Selamat Datang di Portal Member PetGym</h5>
  <p class="text-muted mb-0 small">Gunakan panduan di bawah ini untuk memahami alur kerja transaksi, booking trainer, pendaftaran kelas, dan sewa loker digital.</p>
</div>

<!-- Step by Step Guides -->
<div class="row">
  <!-- Step 1: Sewa Loker -->
  <div class="col-md-6 mb-4">
    <div class="card-custom h-100">
      <div class="d-flex align-items-center mb-3">
        <div class="badge badge-primary font-weight-bold px-3 py-2 mr-2" style="font-size: 14px; border-radius: 8px;">Langkah 1</div>
        <h6 class="font-weight-bold text-dark mb-0">Menyewa Loker Digital Gym</h6>
      </div>
      <ol class="text-muted pl-3 mb-0" style="font-size: 13px; line-height: 1.8;">
        <li>Buka menu <strong>Loker Saya</strong> dari navigasi atas.</li>
        <li>Pilih nomor loker yang berstatus <strong>Tersedia (Kotak Hijau)</strong>.</li>
        <li>Pilih durasi sewa (<strong>Harian Rp 15rb</strong> atau <strong>Bulanan Rp 150rb</strong>). *Member Premium gratis bulanan!</li>
        <li>Pilih metode pembayaran (QRIS / Transfer Bank VA / Tunai).</li>
        <li>Klik <strong>Konfirmasi & Bayar Sekarang</strong>. Sistem akan menghasilkan **Kode Access PIN 6-Digit**.</li>
        <li>Tunjukkan PIN tersebut pada loker IoT atau petugas resepsionis untuk akses loker Anda.</li>
      </ol>
    </div>
  </div>

  <!-- Step 2: Personal Trainer -->
  <div class="col-md-6 mb-4">
    <div class="card-custom h-100">
      <div class="d-flex align-items-center mb-3">
        <div class="badge badge-primary font-weight-bold px-3 py-2 mr-2" style="font-size: 14px; border-radius: 8px;">Langkah 2</div>
        <h6 class="font-weight-bold text-dark mb-0">Membeli Kuota & Booking Personal Trainer (PT)</h6>
      </div>
      <ol class="text-muted pl-3 mb-0" style="font-size: 13px; line-height: 1.8;">
        <li>Buka menu <strong>Personal Trainer</strong> dari navigasi atas.</li>
        <li>Jika kuota Anda 0, pilih trainer favorit Anda lalu klik <strong>Beli Kuota</strong>.</li>
        <li>Pilih paket kuota sesi (5, 10, atau 20 sesi) & selesaikan pembayaran. Sisa kuota Anda akan langsung bertambah.</li>
        <li>Klik <strong>Booking Sesi</strong> pada trainer Anda, pilih tanggal dan slot jam (misal 08:00 - 09:00 WIB).</li>
        <li>Sistem akan memverifikasi jadwal anti-bentrok. Booking Anda langsung tercatat & muncul di Dashboard Trainer!</li>
      </ol>
    </div>
  </div>

  <!-- Step 3: Kelas Kebugaran -->
  <div class="col-md-6 mb-4">
    <div class="card-custom h-100">
      <div class="d-flex align-items-center mb-3">
        <div class="badge badge-primary font-weight-bold px-3 py-2 mr-2" style="font-size: 14px; border-radius: 8px;">Langkah 3</div>
        <h6 class="font-weight-bold text-dark mb-0">Mengikuti Kelas Kebugaran (RSVP & Waitlist)</h6>
      </div>
      <ol class="text-muted pl-3 mb-0" style="font-size: 13px; line-height: 1.8;">
        <li>Buka menu <strong>Kelas Kebugaran</strong> dari navigasi atas.</li>
        <li>Lihat jadwal kelas yang tersedia (Zumba, Yoga, HIIT, Pilates).</li>
        <li>Klik <strong>RSVP Ikuti Kelas</strong> dan pilih tanggal kelas (dapat dipesan hingga H-7 ke depan).</li>
        <li>Jika kuota kelas masih tersedia, status pendaftaran Anda adalah <strong>Dikonfirmasi</strong>.</li>
        <li>Jika kuota kelas sudah penuh, Anda otomatis ditempatkan pada <strong>Antrean Waitlist</strong>.</li>
        <li>Anda dapat membatalkan RSVP kapan saja sebelum kelas dimulai.</li>
      </ol>
    </div>
  </div>

  <!-- Step 4: Upgrade Keanggotaan -->
  <div class="col-md-6 mb-4">
    <div class="card-custom h-100">
      <div class="d-flex align-items-center mb-3">
        <div class="badge badge-primary font-weight-bold px-3 py-2 mr-2" style="font-size: 14px; border-radius: 8px;">Langkah 4</div>
        <h6 class="font-weight-bold text-dark mb-0">Upgrade Tier Keanggotaan</h6>
      </div>
      <ol class="text-muted pl-3 mb-0" style="font-size: 13px; line-height: 1.8;">
        <li>Buka menu <strong>Keanggotaan</strong> dari navigasi atas.</li>
        <li>Bandingkan benefit antara Tier Basic, Standard, dan Premium.</li>
        <li>Klik <strong>Pilih Standard</strong> atau <strong>Upgrade Ke Premium</strong>.</li>
        <li>Selesaikan pembayaran melalui checkout gateway. Masa aktif keanggotaan Anda akan otomatis bertambah 30 hari!</li>
        <li>Tier Premium mendapatkan benefit otomatis: <strong>Gratis Sewa Loker Bulanan</strong>!</li>
      </ol>
    </div>
  </div>
</div>

<!-- FAQ Section -->
<div class="card-custom">
  <h6 class="font-weight-bold text-dark mb-3">Pertanyaan Sering Diajukan (FAQ)</h6>
  <div class="accordion" id="faqAccordion">
    <div class="border-bottom py-2">
      <strong class="text-dark d-block mb-1" style="font-size: 14px;">Bagaimana cara mengunduh atau mencetak kwitansi pembayaran?</strong>
      <small class="text-muted">Buka menu <strong>Riwayat Tagihan</strong>, klik tombol <strong>Detail Struk</strong> pada transaksi yang diinginkan, lalu klik <strong>Cetak Struk</strong>.</small>
    </div>
    <div class="border-bottom py-2">
      <strong class="text-dark d-block mb-1" style="font-size: 14px;">Apa yang terjadi jika saya tidak hadir kelas tanpa pembatalan (No-Show)?</strong>
      <small class="text-muted">Pelanggaran No-Show sebanyak 3 kali akan memicu pemblokiran pendaftaran kelas otomatis selama 7 hari. Selalu batalkan RSVP jika berhalangan hadir.</small>
    </div>
    <div class="py-2">
      <strong class="text-dark d-block mb-1" style="font-size: 14px;">Bagaimana cara memperbarui nomor telepon atau kata sandi akun?</strong>
      <small class="text-muted">Buka menu <strong>Pengaturan</strong> di pojok kanan atas navigasi untuk memperbarui informasi profil dan kata sandi Anda.</small>
    </div>
  </div>
</div>
@endsection
