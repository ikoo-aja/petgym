@extends('layouts.member')

@section('title', 'Riwayat Tagihan Member - PetGym')
@section('page_title', 'Riwayat Tagihan & Invoice')
@section('page_subtitle', 'Rekapitulasi bukti pembayaran paket keanggotaan, penyewaan loker digital, dan kuota sesi Personal Trainer.')
@section('member_tier_badge', 'Tier ' . strtoupper($member->membership_tier ?? 'Basic'))

@section('content')
<!-- Filter Navigation -->
<div class="d-flex justify-content-between align-items-center mb-3">
  <div class="btn-group" role="group">
    <a href="{{ route('member.billing') }}" class="btn btn-sm {{ !request()->has('type') ? 'btn-primary' : 'btn-outline-primary' }}">Semua Tagihan</a>
    <a href="{{ route('member.billing', ['type' => 'membership']) }}" class="btn btn-sm {{ request('type') == 'membership' ? 'btn-primary' : 'btn-outline-primary' }}">Keanggotaan & PT</a>
    <a href="{{ route('member.billing', ['type' => 'inventory']) }}" class="btn btn-sm {{ request('type') == 'inventory' ? 'btn-primary' : 'btn-outline-primary' }}">Loker & Ritel</a>
  </div>
  <div class="text-muted small">Total: <strong>{{ count($transactions) }} Transaksi</strong></div>
</div>

<div class="card-custom">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
        <tr>
          <th>No. Invoice</th>
          <th>Tanggal & Jam</th>
          <th>Kategori</th>
          <th>Metode Bayar</th>
          <th>Total Nominal</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($transactions as $t)
          <tr>
            <td class="font-weight-bold text-dark">{{ $t->invoice_number }}</td>
            <td class="text-muted" style="font-size: 12.5px;">{{ $t->created_at ? $t->created_at->format('d M Y H:i') : '-' }}</td>
            <td>
              <span class="badge badge-secondary text-uppercase">{{ $t->type ?? 'membership' }}</span>
            </td>
            <td>
              <span class="badge badge-light border text-uppercase" style="font-size: 10px;">{{ $t->payment_method ?? 'QRIS' }}</span>
            </td>
            <td class="font-weight-bold text-primary">
              @if($t->total_amount == 0)
                <span class="text-success">GRATIS (Bundling Premium)</span>
              @else
                Rp {{ number_format($t->total_amount, 0, ',', '.') }}
              @endif
            </td>
            <td>
              <span class="badge badge-success">Lunas</span>
            </td>
            <td>
              <button type="button" class="btn btn-sm btn-outline-primary py-1 btn-receipt" 
                      data-invoice="{{ $t->invoice_number }}"
                      data-date="{{ $t->created_at ? $t->created_at->format('d M Y H:i') : '-' }}"
                      data-method="{{ strtoupper($t->payment_method ?? 'QRIS') }}"
                      data-amount="{{ $t->total_amount == 0 ? 'GRATIS (Bundling Premium)' : 'Rp ' . number_format($t->total_amount, 0, ',', '.') }}"
                      data-type="{{ strtoupper($t->type ?? 'MEMBERSHIP') }}"
                      data-toggle="modal" 
                      data-target="#receiptModal">
                Detail Struk
              </button>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="text-center py-4 text-muted">Belum ada riwayat transaksi tagihan tercatat.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Struk Pembayaran -->
<div class="modal fade" id="receiptModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-light">
        <div>
          <h5 class="modal-title font-weight-bold text-dark mb-0" id="receiptInvoice">INV-XXXXX</h5>
          <small class="text-muted">Bukti Pembayaran Resmi Gym</small>
        </div>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body p-4">
        <div class="text-center mb-4">
          <span class="badge badge-success px-3 py-1 font-weight-bold text-uppercase mb-2">PEMBAYARAN LUNAS</span>
          <h2 class="font-weight-bold text-primary mb-0" id="receiptAmount">Rp 0</h2>
        </div>

        <div class="p-3 bg-light rounded mb-3" style="font-size: 13px;">
          <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">Tanggal Transaksi:</span>
            <strong class="text-dark" id="receiptDate">-</strong>
          </div>
          <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">Metode Pembayaran:</span>
            <strong class="text-dark" id="receiptMethod">QRIS</strong>
          </div>
          <div class="d-flex justify-content-between">
            <span class="text-muted">Kategori Transaksi:</span>
            <strong class="text-dark" id="receiptType">MEMBERSHIP</strong>
          </div>
        </div>

        <small class="text-muted d-block text-center">Struk digital ini sah sebagai bukti pembayaran di PetGym.</small>
      </div>

      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Tutup</button>
        <button type="button" class="btn btn-primary font-weight-bold" onclick="window.print()">Cetak Struk</button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script>
  $(document).ready(function() {
    $('.btn-receipt').on('click', function() {
      const invoice = $(this).data('invoice');
      const date = $(this).data('date');
      const method = $(this).data('method');
      const amount = $(this).data('amount');
      const type = $(this).data('type');

      $('#receiptInvoice').text(invoice);
      $('#receiptDate').text(date);
      $('#receiptMethod').text(method);
      $('#receiptAmount').text(amount);
      $('#receiptType').text(type);
    });
  });
</script>
@endsection
