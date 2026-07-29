@extends('layouts.admin')

@section('title', 'Ekspor Laporan &mdash; PetGym')
@section('page_title', 'Export Data & Generate Laporan Manual')
@section('page_subtitle', 'Unduh rekap data mentah member, absensi kunjungan, dan transaksi bulanan dalam format CSV/Excel')

@section('content')
<div class="row">
  <div class="col-md-4 mb-4">
    <div class="card-custom text-center py-4">
      <div class="mb-3 text-primary" style="font-size: 40px;">
        <span class="icon-person"></span>
      </div>
      <h5 class="font-weight-bold text-dark mb-2">Rekap Data Member</h5>
      <p class="text-muted mb-4" style="font-size: 13px;">Unduh seluruh data registrasi member gym, nomor kontak, kode PIN, dan status masa aktif.</p>

      <a href="{{ route('admin.reports.export-members') }}" class="btn btn-primary font-weight-bold btn-block py-2" style="border-radius: 8px;">
        Download Rekap Member (CSV)
      </a>
    </div>
  </div>

  <div class="col-md-4 mb-4">
    <div class="card-custom text-center py-4">
      <div class="mb-3 text-success" style="font-size: 40px;">
        <span class="icon-check"></span>
      </div>
      <h5 class="font-weight-bold text-dark mb-2">Rekap Absensi Kunjungan</h5>
      <p class="text-muted mb-4" style="font-size: 13px;">Unduh riwayat lengkap jam kedatangan dan metode check-in seluruh member gym.</p>

      <a href="{{ route('admin.reports.export-checkins') }}" class="btn btn-success font-weight-bold btn-block py-2" style="border-radius: 8px;">
        Download Log Absensi (CSV)
      </a>
    </div>
  </div>

  <div class="col-md-4 mb-4">
    <div class="card-custom text-center py-4">
      <div class="mb-3 text-info" style="font-size: 40px;">
        <span class="icon-shopping-cart"></span>
      </div>
      <h5 class="font-weight-bold text-dark mb-2">Rekap Transaksi Keuangan</h5>
      <p class="text-muted mb-4" style="font-size: 13px;">Unduh histori penjualan membership, ritel inventaris, metode pembayaran, dan nama kasir.</p>

      <a href="{{ route('admin.reports.export-transactions') }}" class="btn btn-info font-weight-bold btn-block py-2" style="border-radius: 8px;">
        Download Rekap Transaksi (CSV)
      </a>
    </div>
  </div>
</div>
@endsection
