@extends('layouts.app')

@section('title', 'Pembayaran Berhasil')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card text-center">
                <div class="card-body py-5">
                    <div class="mb-4">
                        <div class="success-checkmark">
                            <div class="check-icon">
                                <span class="icon-line line-tip"></span>
                                <span class="icon-line line-long"></span>
                                <div class="icon-circle"></div>
                                <div class="icon-fix"></div>
                            </div>
                        </div>
                    </div>

                    <h2 class="text-success mb-3">Pembayaran Berhasil!</h2>
                    <p class="text-muted mb-4">Terima kasih, pembayaran Anda telah berhasil diproses.</p>

                    <!-- Payment Details -->
                    <div class="bg-light rounded-3 p-4 mb-4 text-start">
                        <div class="row mb-3">
                            <div class="col-6 text-muted">No. Transaksi</div>
                            <div class="col-6 text-end"><code>{{ $payment->order_id }}</code></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6 text-muted">Tanggal Bayar</div>
                            <div class="col-6 text-end">{{ $payment->paid_at?->format('d M Y H:i') }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6 text-muted">Siswa</div>
                            <div class="col-6 text-end">{{ $payment->bill->student->name }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6 text-muted">Tagihan</div>
                            <div class="col-6 text-end">{{ $payment->bill->billType->name }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6 text-muted">Metode Pembayaran</div>
                            <div class="col-6 text-end">{{ $payment->payment_method_label }}</div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-6"><strong>Jumlah Dibayar</strong></div>
                            <div class="col-6 text-end fs-4 text-success"><strong>{{ $payment->formatted_amount }}</strong></div>
                        </div>
                    </div>

                    <!-- Bill Status -->
                    @if($payment->bill->status == 'paid')
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        Tagihan sudah <strong>LUNAS</strong>
                    </div>
                    @else
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle me-2"></i>
                        Sisa tagihan: <strong>{{ $payment->bill->formatted_remaining }}</strong>
                    </div>
                    @endif

                    <div class="d-grid gap-2">
                        <a href="{{ route('parent.payments.receipt', $payment) }}" class="btn btn-outline-primary">
                            <i class="fas fa-download me-2"></i>Download Bukti Pembayaran
                        </a>
                        <a href="{{ route('parent.payments.index') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar Tagihan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.success-checkmark {
    width: 80px;
    height: 80px;
    margin: 0 auto;
}

.check-icon {
    width: 80px;
    height: 80px;
    position: relative;
    border-radius: 50%;
    box-sizing: content-box;
    border: 4px solid #10B981;
}

.check-icon::before {
    top: 3px;
    left: -2px;
    width: 30px;
    transform-origin: 100% 50%;
    border-radius: 100px 0 0 100px;
}

.check-icon::after {
    top: 0;
    left: 30px;
    width: 60px;
    transform-origin: 0 50%;
    border-radius: 0 100px 100px 0;
    animation: rotate-circle 4.25s ease-in;
}

.check-icon::before, .check-icon::after {
    content: '';
    height: 100px;
    position: absolute;
    background: #FFFFFF;
    transform: rotate(-45deg);
}

.icon-line {
    height: 5px;
    background-color: #10B981;
    display: block;
    border-radius: 2px;
    position: absolute;
    z-index: 10;
}

.icon-line.line-tip {
    top: 46px;
    left: 14px;
    width: 25px;
    transform: rotate(45deg);
    animation: icon-line-tip 0.75s;
}

.icon-line.line-long {
    top: 38px;
    right: 8px;
    width: 47px;
    transform: rotate(-45deg);
    animation: icon-line-long 0.75s;
}

.icon-circle {
    top: -4px;
    left: -4px;
    z-index: 10;
    width: 80px;
    height: 80px;
    border-radius: 50%;
    position: absolute;
    box-sizing: content-box;
    border: 4px solid rgba(16, 185, 129, .5);
}

.icon-fix {
    top: 8px;
    width: 5px;
    left: 26px;
    z-index: 1;
    height: 85px;
    position: absolute;
    transform: rotate(-45deg);
    background-color: #FFFFFF;
}

@keyframes rotate-circle {
    0% { transform: rotate(-45deg); }
    5% { transform: rotate(-45deg); }
    12% { transform: rotate(-405deg); }
    100% { transform: rotate(-405deg); }
}

@keyframes icon-line-tip {
    0% { width: 0; left: 1px; top: 19px; }
    54% { width: 0; left: 1px; top: 19px; }
    70% { width: 50px; left: -8px; top: 37px; }
    84% { width: 17px; left: 21px; top: 48px; }
    100% { width: 25px; left: 14px; top: 46px; }
}

@keyframes icon-line-long {
    0% { width: 0; right: 46px; top: 54px; }
    65% { width: 0; right: 46px; top: 54px; }
    84% { width: 55px; right: 0px; top: 35px; }
    100% { width: 47px; right: 8px; top: 38px; }
}
</style>
@endsection
