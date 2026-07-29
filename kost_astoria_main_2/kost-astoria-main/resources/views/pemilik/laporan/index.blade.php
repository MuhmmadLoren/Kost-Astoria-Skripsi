@extends('layouts.backend.app')

@section('title', 'Data Pembayaran')

@section('content')
<div class="row" id="table-hover-row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Data Pembayaran</h4>
                <a href="{{ route('laporan-pembayaran.export') }}" class="btn btn-md btn-primary">
                    <i class="fas fa-file-pdf"></i> Cetak Laporan
                </a>
            </div>
            <div class="card-content">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table zero-configuration">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Jumlah Bayar</th>
                                    <th>Tanggal</th>
                                    <th>Bukti Bayar</th>
                                    <th>Foto Selfie</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($payment as $pay)
                                    <tr>
                                        <td>{{ $pay->nama_pemilik }}</td>
                                        <td>Rp {{ number_format($pay->jumlah_bayar, 0, ',', '.') }}</td>
                                        <td>{{ $pay->tgl_transfer }}</td>
                                        <td>
                                            @if ($pay->bukti_bayar)
                                                <a href="{{ asset('storage/images/bukti_bayar/' . $pay->bukti_bayar) }}" target="_blank">
                                                    <img src="{{ asset('storage/images/bukti_bayar/' . $pay->bukti_bayar) }}" alt="Bukti Bayar" width="50" style="cursor: zoom-in;">
                                                </a>
                                            @else
                                                <span class="badge badge-light-secondary">No Bukti Bayar</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($pay->foto_selfie)
                                                <a href="{{ asset('storage/images/bukti_selfie/' . $pay->foto_selfie) }}" target="_blank">
                                                    <img src="{{ asset('storage/images/bukti_selfie/' . $pay->foto_selfie) }}" alt="Foto Selfie" width="50" style="cursor: zoom-in;">
                                                </a>
                                            @else
                                                <span class="badge badge-light-secondary">No Foto Selfie</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No payments found</td>
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

@push('styles')
<style>
    .table th,
    .table td {
        vertical-align: middle;
    }

    .badge {
        padding: 0.5em 1em;
    }
</style>
@endpush
