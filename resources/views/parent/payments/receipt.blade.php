@extends('layouts.app')

@section('title', 'Kwitansi Pembayaran')
@section('page-title', 'Kwitansi Pembayaran')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <!-- Header -->
                <div class="text-center mb-4">
                    <h4 class="mb-1">SMKS Bunda Kasih Sudiang</h4>
                    <p class="text-muted mb-0">Jl. Sudiang Raya, Makassar</p>
                    <p class="text-muted">Telp: (0411) 123-4567</p>
                    <hr>
                    <h5 class="text-primary">KWITANSI PEMBAYARAN</h5>
                    <p class="mb-0">No: {{ $payment->transaction_id }}</p>
                </div>

                <!-- Info -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm mb-0">
                            <tr>
                                <td class="text-muted" width="140">Nama Siswa</td>
                                <td><strong>{{ $payment->bill->student->name }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">NIS</td>
                                <td>{{ $payment->bill->student->nis }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Kelas</td>
                                <td>{{ $payment->bill->student->class }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm mb-0">
                            <tr>
                                <td class="text-muted" width="140">Tanggal Bayar</td>
                                <td>{{ $payment->paid_at?->format('d M Y H:i') ?? $payment->created_at->format('d M Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Metode</td>
                                <td>{{ $payment->payment_method ?? 'Online Payment' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Status</td>
                                <td><span class="badge bg-success">Lunas</span></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Detail -->
                <table class="table">
                    <thead class="table-light">
                        <tr>
                            <th>Keterangan</th>
                            <th>Periode</th>
                            <th class="text-end">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $payment->bill->billType->name }}</td>
                            <td>{{ $payment->bill->month }}/{{ $payment->bill->year }}</td>
                            <td class="text-end">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="2">Total Pembayaran</th>
                            <th class="text-end">Rp {{ number_format($payment->amount, 0, ',', '.') }}</th>
                        </tr>
                    </tfoot>
                </table>

                <div class="row mt-5">
                    <div class="col-6">
                        <p class="text-muted small">Kwitansi ini sah dan diterbitkan secara elektronik.</p>
                    </div>
                    <div class="col-6 text-end">
                        <p class="mb-4">Makassar, {{ now()->format('d M Y') }}</p>
                        <p class="mb-0"><strong>Bendahara</strong></p>
                    </div>
                </div>

                <!-- Actions -->
                <hr>
                <div class="d-flex justify-content-between">
                    <a href="{{ route('parent.payments.history') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Kembali
                    </a>
                    <button onclick="window.print()" class="btn btn-primary">
                        <i class="fas fa-print me-1"></i>Cetak Kwitansi
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .sidebar, .navbar, .btn, hr:last-of-type, .d-flex.justify-content-between:last-child { display: none !important; }
    .card { border: none !important; box-shadow: none !important; }
}
</style>
@endsection
