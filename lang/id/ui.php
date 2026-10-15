<?php

return [
    /*
    |--------------------------------------------------------------------------
    | UI Elements Language Lines (Bahasa Indonesia)
    |--------------------------------------------------------------------------
    | Teks umum tombol, label, navigasi, dan antarmuka PetGym.
    */

    'app_name' => 'PetGym',
    'tagline' => 'Platform Manajemen Gym Berbasis Cloud',

    // Tombol Aksi Umum
    'actions' => [
        'save' => 'Simpan',
        'save_changes' => 'Simpan Perubahan',
        'cancel' => 'Batal',
        'delete' => 'Hapus',
        'edit' => 'Ubah',
        'detail' => 'Lihat Detail',
        'search' => 'Cari',
        'filter' => 'Filter',
        'close' => 'Tutup',
        'back' => 'Kembali',
        'login' => 'Masuk',
        'logout' => 'Keluar',
        'register' => 'Daftar',
        'approve' => 'Setujui',
        'reject' => 'Tolak',
        'confirm' => 'Konfirmasi',
        'download' => 'Unduh',
        'upload' => 'Unggah',
        'print' => 'Cetak',
        'refresh' => 'Muat Ulang',
        'create_new' => 'Tambah Baru',
    ],

    // Peran & Jabatan Baku
    'roles' => [
        'superadmin' => 'Superadmin Platform',
        'admin' => 'Admin Gym',
        'owner' => 'Pemilik (Owner)',
        'manager' => 'Manager Gym',
        'supervisor' => 'Supervisor Operasional',
        'receptionist' => 'Resepsionis',
        'trainer' => 'Personal Trainer (PT)',
        'member' => 'Member Gym',
    ],

    // Istilah Modul & Fitur Baku
    'modules' => [
        'dashboard' => 'Beranda',
        'staff' => 'Data Staf',
        'members' => 'Data Member',
        'lockers' => 'Sewa Loker',
        'classes' => 'Jadwal Kelas',
        'trainers' => 'Personal Trainer',
        'pos' => 'Kasir (POS)',
        'shifts' => 'Shift Kasir',
        'attendance' => 'Presensi Member',
        'inventory' => 'Inventaris & Stok',
        'reports' => 'Laporan Keuangan',
        'promos' => 'Voucher Promo',
        'vendors' => 'Kontak Vendor',
        'equipment' => 'Aset & Alat Gym',
        'lost_found' => 'Barang Tertinggal (Lost & Found)',
        'complaints' => 'Keluhan Member',
        'settings' => 'Pengaturan',
        'subscriptions' => 'Langganan Web',
        'announcements' => 'Pengumuman Sistem',
        'tenants' => 'Kelola Penyewa',
    ],

    // Status Baku
    'status' => [
        'active' => 'Aktif',
        'inactive' => 'Non-Aktif',
        'pending' => 'Menunggu',
        'approved' => 'Disetujui',
        'rejected' => 'Ditolak',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
        'paid' => 'Lunas',
        'unpaid' => 'Belum Lunas',
        'available' => 'Tersedia',
        'occupied' => 'Terpakai',
        'damaged' => 'Rusak',
        'in_repair' => 'Perlu Servis',
        'functional' => 'Berfungsi Normal',
    ],

    // Placeholders Umum
    'placeholders' => [
        'search' => 'Ketik kata kunci pencarian...',
        'search_member' => 'Cari nama atau nomor WhatsApp...',
        'search_invoice' => 'Cari nomor invoice...',
        'email' => 'nama@email.com',
        'phone' => '081234567890',
        'password' => 'Masukkan kata sandi',
        'password_min' => 'Minimal 8 karakter',
        'select_option' => '-- Pilih Opsi --',
        'notes_optional' => 'Catatan tambahan (opsional)...',
    ],

    // Konfirmasi & Dialog
    'dialogs' => [
        'confirm_title' => 'Konfirmasi Tindakan',
        'confirm_delete_title' => 'Konfirmasi Hapus Data',
        'confirm_action_question' => 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
        'destructive_warning' => 'Tindakan ini permanen dan tidak dapat dibatalkan.',
        'yes_delete' => 'Ya, Hapus',
        'yes_proceed' => 'Ya, Lanjutkan',
        'yes_approve' => 'Ya, Setujui',
        'yes_reject' => 'Ya, Tolak',
        'cancel' => 'Batal',
    ],

    // Empty States Standar
    'empty' => [
        'no_data' => 'Belum ada data tersedia.',
        'no_records_found' => 'Tidak ditemukan data yang sesuai dengan filter pencarian.',
        'no_transactions' => 'Belum ada transaksi tercatat pada periode ini.',
        'no_classes' => 'Belum ada jadwal kelas yang terdaftar.',
        'no_members' => 'Belum ada member gym terdaftar.',
        'no_staff' => 'Belum ada akun staf operasional terdaftar.',
        'no_lockers' => 'Belum ada unit loker gym terdaftar.',
        'no_attendance' => 'Belum ada kunjungan presensi tercatat hari ini.',
        'no_complaints' => 'Tidak ada keluhan terbuka saat ini.',
    ],
];
