@extends('layouts.app')

@section('title', 'Pembayaran - ' . $bill->billType->name)

@section('content')
<div class="page-header">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('parent.payments.index') }}">Tagihan</a></li>
            <li class="breadcrumb-item active">Pembayaran</li>
        </ol>
    </nav>
    <h1 class="page-title">Pembayaran Tagihan</h1>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Bill Detail -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-file-invoice me-2"></i>Detail Tagihan
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="text-muted" width="40%">No. Invoice</td>
                                <td><code>{{ $bill->invoice_number }}</code></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Nama Siswa</td>
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
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="text-muted" width="40%">Jenis Tagihan</td>
                                <td><strong>{{ $bill->billType->name }}</strong></td>
                            </tr>
                            @if($bill->month)
                            <tr>
                                <td class="text-muted">Bulan</td>
                                <td>{{ $bill->month }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td class="text-muted">Jatuh Tempo</td>
                                <td class="{{ $bill->isOverdue() ? 'text-danger' : '' }}">
                                    {{ $bill->due_date->format('d F Y') }}
                                    @if($bill->isOverdue())
                                    <span class="badge bg-danger ms-2">Terlambat</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Status</td>
                                <td>{!! $bill->status_badge !!}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($bill->description)
                <div class="alert alert-light mt-3">
                    <i class="fas fa-info-circle me-2"></i>{{ $bill->description }}
                </div>
                @endif
            </div>
        </div>

        <!-- Payment Form -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-credit-card me-2"></i>Formulir Pembayaran
            </div>
            <div class="card-body">
                <form id="paymentForm" action="{{ route('parent.payments.process', $bill) }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="form-label">Jumlah Pembayaran</label>
                        @if($bill->billType->is_flexible)
                        {{-- Tagihan Fleksibel: Bayar sesuai kemampuan --}}
                        <div class="alert alert-success mb-3">
                            <i class="fas fa-hand-holding-usd me-2"></i>
                            <strong>Tagihan Fleksibel</strong> - Anda dapat membayar sesuai kemampuan
                        </div>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <div class="form-check p-3 border rounded payment-option" onclick="setAmount({{ $bill->remaining_amount }})">
                                    <input class="form-check-input" type="radio" name="amount_option" value="full" id="amountFull">
                                    <label class="form-check-label w-100" for="amountFull">
                                        <strong>Bayar Penuh</strong>
                                        <span class="d-block text-primary">{{ $bill->formatted_remaining }}</span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check p-3 border rounded payment-option active">
                                    <input class="form-check-input" type="radio" name="amount_option" value="custom" id="amountCustom" checked>
                                    <label class="form-check-label w-100" for="amountCustom">
                                        <strong>Sesuai Kemampuan</strong>
                                        <span class="d-block text-muted small">Minimum Rp 10.000</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        @else
                        {{-- Tagihan Tetap: Nominal Fixed --}}
                        <div class="row g-2">
                            <div class="col-md-4">
                                <div class="form-check p-3 border rounded payment-option" onclick="setAmount({{ $bill->remaining_amount }})">
                                    <input class="form-check-input" type="radio" name="amount_option" value="full" id="amountFull" checked>
                                    <label class="form-check-label w-100" for="amountFull">
                                        <strong>Bayar Penuh</strong>
                                        <span class="d-block text-primary">{{ $bill->formatted_remaining }}</span>
                                    </label>
                                </div>
                            </div>
                            @if($bill->billType->allow_partial ?? false)
                            <div class="col-md-4">
                                <div class="form-check p-3 border rounded payment-option" onclick="setAmount({{ $bill->remaining_amount / 2 }})">
                                    <input class="form-check-input" type="radio" name="amount_option" value="half" id="amountHalf">
                                    <label class="form-check-label w-100" for="amountHalf">
                                        <strong>Setengah</strong>
                                        <span class="d-block text-primary">Rp {{ number_format($bill->remaining_amount / 2, 0, ',', '.') }}</span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check p-3 border rounded payment-option">
                                    <input class="form-check-input" type="radio" name="amount_option" value="custom" id="amountCustom">
                                    <label class="form-check-label w-100" for="amountCustom">
                                        <strong>Jumlah Lain</strong>
                                        <span class="d-block text-muted small">Minimum Rp 10.000</span>
                                    </label>
                                </div>
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>

                    <div class="mb-4" id="customAmountDiv" style="{{ $bill->billType->is_flexible ? '' : 'display: none;' }}">
                        <label class="form-label">Masukkan Jumlah</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="custom_amount" id="customAmount" class="form-control" 
                                   min="10000" {{ $bill->billType->is_flexible ? '' : 'max="' . $bill->remaining_amount . '"' }} placeholder="0">
                        </div>
                        @if(!$bill->billType->is_flexible)
                        <small class="text-muted">Maksimal: {{ $bill->formatted_remaining }}</small>
                        @else
                        <small class="text-success">Bayar sesuai kemampuan Anda</small>
                        @endif
                    </div>

                    <input type="hidden" name="amount" id="paymentAmount" value="{{ $bill->billType->is_flexible ? '' : $bill->remaining_amount }}">

                    <div class="alert alert-info">
                        <div class="d-flex">
                            <i class="fas fa-shield-alt fa-2x me-3 text-primary"></i>
                            <div>
                                <strong>Pembayaran Aman dengan Xendit</strong>
                                <p class="mb-0 small">Pembayaran diproses melalui Xendit dengan berbagai metode: 
                                Virtual Account (BCA, BNI, BRI, Mandiri, Permata), E-Wallet, dan lainnya.</p>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="payment_gateway" value="xendit">

                    <button type="submit" class="btn btn-primary btn-lg w-100" id="payButton">
                        <i class="fas fa-lock me-2"></i>Lanjutkan ke Pembayaran
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Order Summary -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-receipt me-2"></i>Ringkasan
            </div>
            <div class="card-body">
                @if($bill->billType->is_flexible)
                <div class="alert alert-success py-2 mb-3">
                    <small><i class="fas fa-hand-holding-usd me-1"></i>Bayar sesuai kemampuan</small>
                </div>
                @endif
                <div class="d-flex justify-content-between mb-2">
                    <span>Total Tagihan</span>
                    <span>{{ $bill->formatted_total }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2 text-success">
                    <span>Sudah Dibayar</span>
                    <span>- {{ $bill->formatted_paid_amount }}</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-3">
                    <strong>Sisa Tagihan</strong>
                    <strong class="text-danger">{{ $bill->formatted_remaining }}</strong>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <strong>Yang Dibayar</strong>
                    <strong class="text-primary fs-4" id="amountDisplay">{{ $bill->billType->is_flexible ? 'Rp -' : $bill->formatted_remaining }}</strong>
                </div>
            </div>
        </div>

        <!-- Payment Methods Info -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-wallet me-2"></i>Metode Pembayaran Xendit
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6><i class="fas fa-university me-2 text-primary"></i>Virtual Account (VA)</h6>
                    <small class="text-muted">Transfer melalui ATM, Internet/Mobile Banking</small>
                </div>
                <div class="row g-2">
                    <div class="col-4">
                        <div class="text-center p-2 border rounded">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/5/5c/Bank_Central_Asia.svg" alt="BCA" height="20" class="mb-1">
                            <small class="d-block text-muted">BCA VA</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="text-center p-2 border rounded">
                            <img src="https://upload.wikimedia.org/wikipedia/id/5/55/BNI_logo.svg" alt="BNI" height="20" class="mb-1">
                            <small class="d-block text-muted">BNI VA</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="text-center p-2 border rounded">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/6/68/BANK_BRI_logo.svg" alt="BRI" height="20" class="mb-1">
                            <small class="d-block text-muted">BRI VA</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center p-2 border rounded">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/a/ad/Bank_Mandiri_logo_2016.svg" alt="Mandiri" height="20" class="mb-1">
                            <small class="d-block text-muted">Mandiri VA</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center p-2 border rounded">
                            <img src="https://upload.wikimedia.org/wikipedia/id/4/46/Bank_Permata.svg" alt="Permata" height="20" class="mb-1">
                            <small class="d-block text-muted">Permata VA</small>
                        </div>
                    </div>
                </div>
                <hr class="my-3">
                <div class="mb-2">
                    <h6><i class="fas fa-wallet me-2 text-success"></i>E-Wallet & Lainnya</h6>
                    <small class="text-muted">OVO, DANA, ShopeePay, LinkAja, dll</small>
                </div>
                <div class="alert alert-light py-2 mb-0">
                    <small><i class="fas fa-info-circle me-1"></i>Metode pembayaran lengkap tersedia di halaman Xendit</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const remainingAmount = {{ $bill->remaining_amount }};
const isFlexible = {{ $bill->billType->is_flexible ? 'true' : 'false' }};

function setAmount(amount) {
    document.getElementById('paymentAmount').value = amount;
    document.getElementById('amountDisplay').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
}

document.querySelectorAll('input[name="amount_option"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const customDiv = document.getElementById('customAmountDiv');
        if (this.value === 'custom') {
            customDiv.style.display = 'block';
            // Reset untuk tagihan fleksibel
            if (isFlexible) {
                document.getElementById('paymentAmount').value = '';
                document.getElementById('amountDisplay').textContent = 'Rp -';
            }
        } else {
            customDiv.style.display = 'none';
        }
    });
});

document.getElementById('customAmount').addEventListener('input', function() {
    let amount = parseInt(this.value) || 0;
    // Hanya batasi maksimal jika bukan tagihan fleksibel
    if (!isFlexible && amount > remainingAmount) {
        amount = remainingAmount;
        this.value = amount;
    }
    setAmount(amount);
});

document.querySelectorAll('.payment-option').forEach(option => {
    option.addEventListener('click', function() {
        document.querySelectorAll('.payment-option').forEach(o => o.classList.remove('border-primary'));
        this.classList.add('border-primary');
    });
});

// Handle form submission - Xendit or Midtrans
document.getElementById('paymentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const submitBtn = document.getElementById('payButton');
    const originalText = submitBtn.innerHTML;
    
    // Show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
    
    // Get form data
    const formData = new FormData(form);
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Check which gateway was used
            if (data.gateway === 'xendit' && data.invoice_url) {
                // Redirect to Xendit Invoice page
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'info',
                        title: 'Mengarahkan ke Pembayaran',
                        text: 'Anda akan dialihkan ke halaman pembayaran Xendit...',
                        timer: 2000,
                        showConfirmButton: false,
                        allowOutsideClick: false
                    }).then(() => {
                        window.location.href = data.invoice_url;
                    });
                } else {
                    window.location.href = data.invoice_url;
                }
            } else if (data.snap_token) {
                // Open Midtrans Snap popup (fallback)
                snap.pay(data.snap_token, {
                    onSuccess: function(result) {
                        console.log('Payment Success:', result);
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Pembayaran Berhasil!',
                                text: 'Terima kasih, pembayaran Anda telah diterima.',
                                confirmButtonColor: '#4F46E5'
                            }).then(() => {
                                window.location.href = "{{ route('parent.payments.history') }}";
                            });
                        } else {
                            alert('Pembayaran berhasil!');
                            window.location.href = "{{ route('parent.payments.history') }}";
                        }
                    },
                    onPending: function(result) {
                        console.log('Payment Pending:', result);
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'info',
                                title: 'Menunggu Pembayaran',
                                html: 'Silakan selesaikan pembayaran Anda melalui Virtual Account.<br><br>' +
                                      '<strong>Nomor VA:</strong> ' + (result.va_numbers ? result.va_numbers[0].va_number : '-') + '<br>' +
                                      '<strong>Bank:</strong> ' + (result.va_numbers ? result.va_numbers[0].bank.toUpperCase() : '-'),
                                confirmButtonColor: '#4F46E5'
                            }).then(() => {
                                window.location.href = "{{ route('parent.payments.history') }}";
                            });
                        } else {
                            alert('Pembayaran pending. Silakan selesaikan pembayaran melalui Virtual Account.');
                            window.location.href = "{{ route('parent.payments.history') }}";
                        }
                    },
                    onError: function(result) {
                        console.log('Payment Error:', result);
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Pembayaran Gagal',
                                text: 'Terjadi kesalahan saat memproses pembayaran. Silakan coba lagi.',
                                confirmButtonColor: '#4F46E5'
                            });
                        } else {
                            alert('Pembayaran gagal. Silakan coba lagi.');
                        }
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    },
                    onClose: function() {
                        console.log('Payment popup closed');
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }
                });
            } else {
                throw new Error('Response tidak valid dari server');
            }
        } else {
            throw new Error(data.message || 'Gagal memproses pembayaran');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message || 'Terjadi kesalahan. Silakan coba lagi.',
                confirmButtonColor: '#4F46E5'
            });
        } else {
            alert(error.message || 'Terjadi kesalahan. Silakan coba lagi.');
        }
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});
</script>
@endpush
