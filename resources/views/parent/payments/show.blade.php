@extends('layouts.app')

@section('title', 'Detail Pembayaran')
@section('page-title', 'Detail Pembayaran')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <div class="d-flex align-items-center">
                    <a href="{{ route('parent.payments.index') }}" class="btn btn-outline-secondary btn-sm me-3">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h5 class="mb-0"><i class="fas fa-file-invoice-dollar me-2 text-primary"></i>Detail Tagihan</h5>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm mb-0">
                            <tr>
                                <td class="text-muted" width="140">No. Tagihan</td>
                                <td><strong>{{ $bill->bill_number }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Jenis Tagihan</td>
                                <td>{{ $bill->billType->name }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Periode</td>
                                <td>{{ $bill->month }}/{{ $bill->year }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Jatuh Tempo</td>
                                <td>{{ $bill->due_date->format('d M Y') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm mb-0">
                            <tr>
                                <td class="text-muted" width="140">Nama Siswa</td>
                                <td><strong>{{ $bill->student->name }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">NIS</td>
                                <td>{{ $bill->student->nis }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Kelas</td>
                                <td>{{ $bill->student->class }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Status</td>
                                <td>
                                    @if($bill->status == 'paid')
                                        <span class="badge bg-success">Lunas</span>
                                    @elseif($bill->status == 'partial')
                                        <span class="badge bg-warning">Sebagian</span>
                                    @elseif($bill->status == 'overdue')
                                        <span class="badge bg-danger">Terlambat</span>
                                    @else
                                        <span class="badge bg-secondary">Belum Bayar</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Rincian Pembayaran</h6>
                        <table class="table table-sm">
                            <tr>
                                <td>Jumlah Tagihan</td>
                                <td class="text-end"><strong>Rp {{ number_format($bill->amount, 0, ',', '.') }}</strong></td>
                            </tr>
                            <tr>
                                <td>Sudah Dibayar</td>
                                <td class="text-end text-success">Rp {{ number_format($bill->paid_amount, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="table-light">
                                <td><strong>Sisa Tagihan</strong></td>
                                <td class="text-end"><strong class="text-danger">Rp {{ number_format($bill->amount - $bill->paid_amount, 0, ',', '.') }}</strong></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        @if($bill->status != 'paid' && $bill->status != 'cancelled')
                            <div class="card bg-light border-0">
                                <div class="card-body text-center">
                                    <h6>Sisa yang harus dibayar</h6>
                                    <h2 class="text-primary mb-3">Rp {{ number_format($bill->amount - $bill->paid_amount, 0, ',', '.') }}</h2>
                                    <a href="{{ route('parent.payments.checkout', $bill) }}" class="btn btn-primary btn-lg w-100">
                                        <i class="fas fa-credit-card me-2"></i>Bayar Sekarang
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="card bg-success text-white border-0">
                                <div class="card-body text-center">
                                    <i class="fas fa-check-circle fa-3x mb-3"></i>
                                    <h5>Tagihan Sudah Lunas</h5>
                                    <p class="mb-0">Dibayar pada {{ $bill->payments->last()?->created_at?->format('d M Y') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-history me-2"></i>Riwayat Pembayaran</h6>
            </div>
            <div class="card-body">
                @if($bill->payments->count() > 0)
                    @foreach($bill->payments as $payment)
                        <div class="d-flex align-items-start mb-3 pb-3 border-bottom">
                            <div class="rounded-circle bg-{{ $payment->status == 'success' ? 'success' : ($payment->status == 'pending' ? 'warning' : 'secondary') }} p-2 me-3">
                                <i class="fas fa-{{ $payment->status == 'success' ? 'check' : ($payment->status == 'pending' ? 'clock' : 'times') }} text-white"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between">
                                    <strong>Rp {{ number_format($payment->amount, 0, ',', '.') }}</strong>
                                    <small class="text-muted">{{ $payment->created_at->format('d/m/Y H:i') }}</small>
                                </div>
                                <small class="text-muted">{{ $payment->payment_method ?? 'Online Payment' }}</small>
                                @if($payment->status == 'success')
                                    <div><a href="{{ route('parent.payments.receipt', $payment) }}" class="btn btn-sm btn-outline-primary mt-1"><i class="fas fa-download me-1"></i>Kwitansi</a></div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-muted text-center mb-0">Belum ada pembayaran</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
