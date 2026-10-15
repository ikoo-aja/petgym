<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Notification & Flash Messages Language Lines (Bahasa Indonesia)
    |--------------------------------------------------------------------------
    | Pesan notifikasi sukses, peringatan, dan kesalahan terpusat.
    */

    // Autentikasi & Akun
    'auth' => [
        'login_success' => 'Berhasil masuk ke sistem. Selamat beraktivitas!',
        'logout_success' => 'Anda telah berhasil keluar.',
        'password_changed' => 'Kata sandi akun Anda berhasil diperbarui.',
        'profile_updated' => 'Informasi profil akun Anda berhasil diperbarui.',
        'email_verified' => 'Email berhasil diverifikasi! Silakan masuk dengan email dan kata sandi Anda.',
        'email_already_verified' => 'Email Anda sudah terverifikasi.',
        'verification_sent' => 'Tautan verifikasi baru telah dikirim ke email Anda.',
        'shift_still_open' => 'Shift kasir Anda masih terbuka. Tutup shift terlebih dahulu sebelum keluar.',
        'must_change_default_password' => 'Silakan ubah kata sandi bawaan Anda sebelum melanjutkan.',
        'unauthorized_monitoring' => 'Anda tidak dapat melakukan perubahan ini karena akun Anda hanya memiliki akses pantau (baca saja). Hubungi pemilik akun utama untuk melakukan perubahan.',
    ],

    // Data Tidak Ditemukan
    'not_found' => [
        'gym' => 'Data gym Anda tidak ditemukan. Silakan muat ulang halaman, atau hubungi dukungan teknis jika masalah berlanjut.',
        'gym_unregistered' => 'Data gym Anda belum terdaftar. Hubungi Superadmin untuk melengkapi data gym.',
        'user' => 'Data pengguna tidak ditemukan. Silakan muat ulang halaman dan coba lagi.',
        'admin' => 'Akun admin tidak ditemukan. Silakan muat ulang halaman dan coba lagi.',
        'tenant' => 'Data penyewa tidak ditemukan. Silakan muat ulang halaman dan coba lagi.',
        'pin' => 'Kode akses PIN tidak dikenali atau belum terdaftar. Periksa kembali PIN member, atau gunakan menu Pendaftaran Member untuk mendaftarkan PIN baru.',
    ],

    // Pembatasan Akses (RBAC)
    'access' => [
        'admin_no_member' => 'Akses Ditolak: Data member hanya dapat dikelola oleh Manager Gym.',
        'trainer_no_member' => 'Akses Ditolak: Personal Trainer tidak memiliki izin untuk mengakses data member.',
        'member_inactive_locker' => 'Akses Terkunci: Layanan sewa loker hanya dapat digunakan oleh member yang memiliki paket langganan aktif. Silakan lakukan aktivasi paket di Meja Resepsionis.',
        'member_inactive_pt' => 'Akses Terkunci: Layanan Personal Trainer (PT) hanya dapat digunakan oleh member yang memiliki paket langganan aktif. Silakan lakukan aktivasi paket di Meja Resepsionis.',
        'member_inactive_classes' => 'Akses Terkunci: Jadwal dan reservasi kelas kebugaran hanya dapat diakses oleh member yang memiliki paket langganan aktif. Silakan lakukan aktivasi paket di Meja Resepsionis.',
        'pos_receptionist_only' => 'Fungsi POS Kasir hanya dapat diakses oleh Resepsionis. Silakan gunakan menu Langganan Website untuk Pengaturan Sewa Web.',
    ],

    // Kuota & Upgrade Paket
    'quota' => [
        'staff_limit' => 'Gagal menambahkan akun staf: Kuota maksimal karyawan untuk :plan (:max Akun) telah tercapai. Silakan naikkan paket langganan Anda.',
        'role_disallowed' => 'Paket :plan tidak mengizinkan penambahan peran :role. Silakan naikkan paket langganan Anda.',
        'pt_unsupported' => 'Paket :plan belum mendukung pengelolaan akun Personal Trainer (PT). Silakan naikkan paket ke Pro atau Enterprise.',
        'member_limit' => 'Pendaftaran member saat ini tidak dapat diproses: Batas kuota member aktif untuk gym ini (:max Member) telah penuh. Silakan hubungi Customer Service gym untuk informasi lebih lanjut.',
    ],

    // Operasional Kasir & Loker
    'pos' => [
        'shift_required' => 'Transaksi POS diblokir: Shift kasir Anda belum dibuka. Silakan buka shift kasir terlebih dahulu di menu Shift & Kasir.',
        'shift_opened' => 'Shift kasir meja depan berhasil dibuka. Seluruh fitur operasional kini dapat digunakan.',
        'shift_closed' => 'Shift kasir berhasil ditutup dan setoran kas akhir telah tercatat.',
        'stock_insufficient' => 'Transaksi gagal: Stok untuk :product tidak mencukupi (Tersedia: :available pcs, Diminta: :requested pcs).',
        'single_membership_only' => 'Transaksi dibatalkan: Hanya satu paket membership yang boleh dipilih per transaksi.',
    ],

    'lockers' => [
        'created' => 'Loker nomor :number berhasil didaftarkan.',
        'updated' => 'Loker nomor :number berhasil diperbarui.',
        'deleted' => 'Loker nomor :number berhasil dihapus.',
        'returned' => 'Kunci Loker :number berhasil dikembalikan dan loker siap digunakan kembali.',
        'returned_damaged' => 'Kunci Loker :number dikembalikan dalam kondisi rusak. Laporan telah otomatis diteruskan ke Supervisor untuk perbaikan.',
        'repaired' => 'Loker nomor :number berhasil diperbaiki dan status dikembalikan menjadi Tersedia.',
        'occupied_cannot_delete' => 'Loker nomor :number sedang terpakai oleh member dan tidak dapat dihapus.',
    ],
];
