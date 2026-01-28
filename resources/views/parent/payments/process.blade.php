@extends('layouts.app')

@section('title', 'Proses Pembayaran')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <div class="spinner-border text-primary" role="status" style="width: 4rem; height: 4rem;" id="loadingSpinner">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <div id="paymentIcon" style="display: none;">
                            <i class="fas fa-credit-card fa-4x text-primary"></i>
                        </div>
                    </div>
                    
                    <h3 class="mb-2" id="statusTitle">Memproses Pembayaran...</h3>
                    <p class="text-muted mb-4" id="statusMessage">Mohon tunggu, sedang menyiapkan halaman pembayaran.</p>

                    <!-- Payment Details -->
                    <div class="bg-light rounded-3 p-4 mb-4">
                        <div class="row text-start">
                            <div class="col-6">
                                <small class="text-muted">No. Pesanan</small>
                                <p class="mb-0"><code>{{ $payment->order_id }}</code></p>
                            </div>
                            <div class="col-6 text-end">
                                <small class="text-muted">Jumlah</small>
                                <p class="mb-0 fs-5 text-primary"><strong>{{ $payment->formatted_amount }}</strong></p>
                            </div>
                        </div>
                        <hr>
                        <div class="row text-start">
                            <div class="col-6">
                                <small class="text-muted">Siswa</small>
                                <p class="mb-0">{{ $payment->bill->student->name }}</p>
                            </div>
                            <div class="col-6 text-end">
                                <small class="text-muted">Tagihan</small>
                                <p class="mb-0">{{ $payment->bill->billType->name }}</p>
                            </div>
                        </div>
                    </div>

                    <div id="paymentButtons" style="display: none;">
                        <button type="button" class="btn btn-primary btn-lg px-5" id="payNowBtn">
                            <i class="fas fa-lock me-2"></i>Bayar Sekarang
                        </button>
                        <a href="{{ route('parent.payments.index') }}" class="btn btn-outline-secondary btn-lg ms-2">
                            Kembali
                        </a>
                    </div>

                    <div class="mt-4">
                        <small class="text-muted">
                            <i class="fas fa-shield-alt me-1"></i>Transaksi diamankan oleh Midtrans
                        </small>
                    </div>
                </div>
            </div>

            <!-- Instructions -->
            <div class="card mt-4">
                <div class="card-header">
                    <i class="fas fa-info-circle me-2"></i>Petunjuk Pembayaran
                </div>
                <div class="card-body">
                    <ol class="mb-0">
                        <li class="mb-2">Klik tombol <strong>"Bayar Sekarang"</strong> untuk membuka halaman pembayaran.</li>
                        <li class="mb-2">Pilih metode pembayaran yang diinginkan (Transfer Bank, E-Wallet, QRIS, dll).</li>
                        <li class="mb-2">Ikuti instruksi untuk menyelesaikan pembayaran.</li>
                        <li class="mb-2">Setelah pembayaran berhasil, Anda akan diarahkan kembali ke halaman ini.</li>
                        <li>Status pembayaran akan diperbarui secara otomatis dalam 1-5 menit.</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Midtrans Snap -->
<script src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" 
        data-client-key="{{ config('midtrans.client_key') }}"></script>

<script>
const snapToken = "{{ $snapToken }}";
const orderId = "{{ $payment->order_id }}";

// Show payment button after loading
setTimeout(function() {
    document.getElementById('loadingSpinner').style.display = 'none';
    document.getElementById('paymentIcon').style.display = 'block';
    document.getElementById('statusTitle').textContent = 'Siap Melakukan Pembayaran';
    document.getElementById('statusMessage').textContent = 'Klik tombol di bawah untuk melanjutkan ke halaman pembayaran.';
    document.getElementById('paymentButtons').style.display = 'block';
}, 1500);

// Handle payment button click
document.getElementById('payNowBtn').addEventListener('click', function() {
    snap.pay(snapToken, {
        onSuccess: function(result) {
            console.log('Payment Success:', result);
            Swal.fire({
                icon: 'success',
                title: 'Pembayaran Berhasil!',
                text: 'Terima kasih, pembayaran Anda telah diterima.',
                confirmButtonColor: '#4F46E5'
            }).then(() => {
                window.location.href = "{{ route('parent.payments.success', $payment) }}";
            });
        },
        onPending: function(result) {
            console.log('Payment Pending:', result);
            Swal.fire({
                icon: 'info',
                title: 'Menunggu Pembayaran',
                text: 'Silakan selesaikan pembayaran sesuai instruksi.',
                confirmButtonColor: '#4F46E5'
            }).then(() => {
                window.location.href = "{{ route('parent.payments.pending', $payment) }}";
            });
        },
        onError: function(result) {
            console.log('Payment Error:', result);
            Swal.fire({
                icon: 'error',
                title: 'Pembayaran Gagal',
                text: 'Terjadi kesalahan. Silakan coba lagi.',
                confirmButtonColor: '#4F46E5'
            });
        },
        onClose: function() {
            console.log('Payment popup closed');
        }
    });
});
</script>
@endpush
