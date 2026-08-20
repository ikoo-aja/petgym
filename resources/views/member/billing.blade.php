@extends('layouts.admin')

@section('title', 'Riwayat Tagihan (Member) &mdash; PetGym')
@section('page_title', 'Riwayat Tagihan & Transaksi Invoicing')
@section('page_subtitle', 'Rekapitulasi bukti pembayaran paket keanggotaan, penyewaan loker digital, dan kuota sesi Personal Trainer.')

@section('content')
<div class="card-custom">
  <h6 class="font-weight-bold text-dark mb-3">🧾 Daftar Invoice Pembayaran Saya</h6>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
        <tr>
          <th>No. Invoice</th>
          <th>Tanggal & Jam</th>
          <th>Metode Bayar</th>
          <th>Total Nominal</th>
          <th>Status Pembayaran</th>
        </tr>
      </thead>
      <tbody>
        @forelse($transactions as $t)
          <tr>
            <td class="font-weight-bold text-dark" style="font-size: 12.5px;">{{ $t->invoice_number }}</td>
            <td style="font-size: 12px;" class="text-muted">{{ $t->created_at ? $t->created_at->format('d M Y H:i') : '-' }}</td>
            <td>
              <span class="badge badge-light border text-uppercase px-2 py-1" style="font-size: 10px;">{{ $t->payment_method ?? 'QRIS' }}</span>
            </td>
            <td class="font-weight-bold text-success" style="font-size: 13px;">Rp {{ number_format($t->total_amount, 0, ',', '.') }}</td>
            <td>
              <span class="badge badge-success px-2 py-1">Lunas (Paid)</span>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="text-center py-4 text-muted">Belum ada riwayat transaksi tagihan tercatat.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
