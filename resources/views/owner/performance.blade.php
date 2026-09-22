@extends('layouts.layout')

@section('title', 'Kinerja Karyawan — PetGym')
@section('page_title', 'Pemantauan Kinerja Karyawan')
@section('page_subtitle', 'Evaluasi performa penjualan kasir resepsionis dan penugasan kelas instruktur trainer')

@section('content')
<div class="container-fluid py-2">

    <div class="row">
        <!-- Evaluasi Penjualan Resepsionis -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm bg-white h-100" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom pt-4 px-4 pb-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="font-weight-bold text-dark mb-0">Evaluasi Penjualan Resepsionis</h6>
                        <small class="text-muted">Total transaksi kasir dan nominal penjualan bulan ini</small>
                    </div>
                    <span class="badge badge-light border text-primary font-weight-bold px-2 py-1" style="font-size: 11px; border-radius: 6px;">
                        <i class="icon-shopping-cart mr-1"></i> Bulan Ini
                    </span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
                                <tr>
                                    <th class="px-4 py-3">Nama Resepsionis / Kasir</th>
                                    <th class="py-3 text-center">Transaksi</th>
                                    <th class="px-4 py-3 text-right">Total Omset</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($receptionistPerformance as $rp)
                                    <tr>
                                        <td class="px-4 py-3">
                                            <div class="font-weight-bold text-dark" style="font-size: 13.5px;">{{ $rp->user ? $rp->user->name : 'Sistem / Umum' }}</div>
                                            <small class="text-muted">{{ $rp->user ? $rp->user->email : '-' }}</small>
                                        </td>
                                        <td class="py-3 text-center font-weight-bold text-dark" style="font-size: 13px;">
                                            {{ $rp->total_transactions }}
                                        </td>
                                        <td class="px-4 py-3 text-right font-weight-bold text-success" style="font-size: 14px;">
                                            Rp {{ number_format($rp->total_sales, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted small">
                                            <span class="icon-inbox h4 d-block mb-1 text-muted"></span>
                                            Belum ada data transaksi kasir pada bulan ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Jam Terbang & Penugasan Kelas Personal Trainer -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm bg-white h-100" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom pt-4 px-4 pb-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="font-weight-bold text-dark mb-0">Jam Terbang &amp; Penugasan Kelas Trainer</h6>
                        <small class="text-muted">Distribusi jadwal dan jumlah kelas kebugaran aktif</small>
                    </div>
                    <span class="badge badge-light border text-info font-weight-bold px-2 py-1" style="font-size: 11px; border-radius: 6px;">
                        <i class="icon-calendar mr-1"></i> Instruktur
                    </span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
                                <tr>
                                    <th class="px-4 py-3">Nama Personal Trainer</th>
                                    <th class="py-3">Spesialisasi</th>
                                    <th class="px-4 py-3 text-right">Kelas Mengajar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($trainerPerformance as $tp)
                                    <tr>
                                        <td class="px-4 py-3">
                                            <div class="font-weight-bold text-dark" style="font-size: 13.5px;">{{ $tp->trainer ? $tp->trainer->name : 'N/A' }}</div>
                                            <small class="text-muted">{{ $tp->trainer ? $tp->trainer->phone : '-' }}</small>
                                        </td>
                                        <td class="py-3 text-muted small">
                                            {{ $tp->trainer ? ($tp->trainer->specialization ?? 'General Fitness') : '-' }}
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <span class="badge badge-primary font-weight-bold px-2 py-1" style="font-size: 12px; border-radius: 6px;">
                                                {{ $tp->total_classes }} Kelas
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted small">
                                            <span class="icon-inbox h4 d-block mb-1 text-muted"></span>
                                            Belum ada penugasan kelas untuk trainer.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
