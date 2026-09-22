@extends('layouts.admin')

@section('title', 'Manajemen Shift Kasir &mdash; PetGym')
@section('page_title', 'Manajemen Shift Kasir')
@section('page_subtitle', 'Buka shift kasir dengan modal awal, pantau penjualan kasir, dan lakukan setoran fisik penutupan shift')

@section('content')
<div class="row mb-4">
  <!-- Buka / Tutup Shift Kasir -->
  <div class="col-md-5">
    <div class="card-custom">
      @if($openShift)
        <h6 class="font-weight-bold text-dark mb-3">Status Shift Kasir: Aktif</h6>
        <div class="alert alert-success mb-3 border-0 p-3" style="background-color: #ecfdf5; border-radius: 10px;">
          <div class="font-weight-bold text-dark mb-2">Shift Sedang Berjalan</div>
          <div class="small text-muted mb-1">Dibuka sejak: <strong class="text-dark">{{ $openShift->opened_at ? \Carbon\Carbon::parse($openShift->opened_at)->format('d M Y, H:i') : '-' }} WIB</strong></div>
          <div class="small text-muted mb-1">Modal Kas Awal: <strong class="text-dark">Rp {{ number_format($openShift->start_cash, 0, ',', '.') }}</strong></div>
          <div class="small text-muted mb-1">Penjualan Tunai Shift Ini: <strong class="text-success">Rp {{ number_format($cashSalesDuringShift ?? 0, 0, ',', '.') }}</strong></div>
          <div class="small text-muted mb-2">Penjualan Digital / Non-Tunai: <strong class="text-info">Rp {{ number_format($digitalSalesDuringShift ?? 0, 0, ',', '.') }}</strong></div>
          <div class="border-top pt-2 mt-2 font-weight-bold text-dark" style="font-size: 13.5px;">
            Target Kas Fisik di Laci: <span class="text-primary font-weight-bold">Rp {{ number_format(($openShift->start_cash + ($cashSalesDuringShift ?? 0)), 0, ',', '.') }}</span>
          </div>
        </div>

        <form action="{{ route('receptionist.shifts.end', $openShift->id) }}" method="POST" data-confirm="Yakin ingin menutup shift kasir? Pastikan seluruh uang fisik laci kasir telah dihitung dengan benar.">
          @csrf
          <div class="form-group mb-3">
            <label class="font-weight-bold text-dark mb-1" style="font-size:13px;">Nominal Setoran Kas Fisik Akhir (Rp) *</label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text font-weight-bold">Rp</span>
              </div>
              <input type="number" name="end_cash" class="form-control font-weight-bold" placeholder="Hitung seluruh uang fisik di laci kasir" required min="0" style="border-radius: 0 8px 8px 0;">
            </div>
            <small class="text-muted mt-1 d-block">Hitung seluruh uang fisik di laci kasir untuk diserahterimakan ke shift berikutnya.</small>
          </div>
          <button type="submit" class="btn btn-danger btn-block font-weight-bold py-2" style="border-radius: 8px;">
            Tutup Shift & Setor Kas Akhir
          </button>
        </form>
      @else
        <h6 class="font-weight-bold text-dark mb-3">Buka Shift Kasir Baru</h6>
        <div class="alert alert-info mb-3 border-0 p-3" style="background-color: #eff6ff; color: #1e40af; border-radius: 10px;">
          <small><strong>Kewajiban Kas Awal:</strong> Anda belum memiliki shift kasir aktif. Masukkan uang modal kas awal dari serah terima untuk mengaktifkan seluruh fitur operasional gym.</small>
        </div>

        <form action="{{ route('receptionist.shifts.start') }}" method="POST">
          @csrf
          <div class="form-group mb-3">
            <label class="font-weight-bold text-dark mb-1" style="font-size:13px;">Nominal Modal Kas Awal (Rp) *</label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text font-weight-bold">Rp</span>
              </div>
              <input type="number" name="start_cash" class="form-control font-weight-bold" placeholder="Contoh: 200000" required min="0" style="border-radius: 0 8px 8px 0;">
            </div>
            <small class="text-muted mt-1 d-block">Hitung uang kas kembalian di laci saat serah terima dari shift sebelumnya.</small>
          </div>
          <button type="submit" class="btn btn-success btn-block font-weight-bold py-2" style="border-radius: 8px;">
            Buka Shift Kasir Sekarang
          </button>
        </form>
      @endif
    </div>
  </div>

  <!-- Riwayat Shift -->
  <div class="col-md-7">
    <div class="card-custom">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="font-weight-bold text-dark mb-0">Riwayat Shift Kasir Anda</h6>
        <span class="badge badge-primary font-weight-bold px-3 py-2" style="border-radius: 8px;">{{ count($shifts) }} Shift</span>
      </div>
      <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
            <tr>
              <th>Tanggal</th>
              <th>Buka</th>
              <th>Tutup</th>
              <th>Kas Awal</th>
              <th>Kas Akhir</th>
              <th>Selisih</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse($shifts as $s)
            <tr>
              <td style="font-size:13px;" class="text-dark">{{ $s->opened_at ? \Carbon\Carbon::parse($s->opened_at)->format('d M Y') : '-' }}</td>
              <td class="font-weight-bold text-dark" style="font-size:13px;">{{ $s->opened_at ? \Carbon\Carbon::parse($s->opened_at)->format('H:i') : '-' }}</td>
              <td style="font-size:13px;">{{ $s->closed_at ? \Carbon\Carbon::parse($s->closed_at)->format('H:i') : '-' }}</td>
              <td style="font-size:12.5px;">Rp {{ number_format($s->start_cash, 0, ',', '.') }}</td>
              <td style="font-size:12.5px;">{{ $s->end_cash !== null ? 'Rp ' . number_format($s->end_cash, 0, ',', '.') : '-' }}</td>
              <td>
                @if($s->end_cash !== null)
                  @php $diff = $s->end_cash - $s->start_cash; @endphp
                  <span class="badge {{ $diff >= 0 ? 'badge-success' : 'badge-danger' }}" style="font-size:11px;">
                    {{ $diff >= 0 ? '+' : '' }}Rp {{ number_format($diff, 0, ',', '.') }}
                  </span>
                @else
                  <span class="text-muted">-</span>
                @endif
              </td>
              <td>
                @if($s->status === 'open')
                  <span class="badge badge-success px-2 py-1">Aktif</span>
                @else
                  <span class="badge badge-secondary px-2 py-1">Selesai</span>
                @endif
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="7" class="text-center py-4 text-muted">Belum ada riwayat shift tercatat.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
