@extends('layouts.app')

@section('title', 'Detail Tagihan')
@section('page-title', 'Detail Tagihan')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <a href="{{ route('treasurer.bills.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
        <a href="{{ route('treasurer.bills.edit', $bill->id) }}" class="btn btn-warning">
            <i class="fas fa-edit me-2"></i>Edit
        </a>
        @if($bill->status === 'unpaid')
        <button type="button" class="btn btn-danger" onclick="confirmDelete()">
            <i class="fas fa-trash me-2"></i>Hapus
        </button>
        @endif
    </div>
</div>

<div class="row">
    <!-- Bill Info -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-file-invoice me-2"></i>Informasi Tagihan</span>
                @if($bill->status === 'paid')
                <span class="badge bg-success fs-6">LUNAS</span>
                @elseif($bill->status === 'partial')
                <span class="badge bg-info fs-6">SEBAGIAN</span>
                @else
                <span class="badge bg-warning fs-6">BELUM BAYAR</span>
                @endif
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="40%" class="text-muted">No. Tagihan</td>
                                <td><strong>#{{ $bill->invoice_number }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Jenis Tagihan</td>
                                <td><strong>{{ $bill->billType->name ?? '-' }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Periode</td>
                                <td><strong>{{ $bill->period_month }}/{{ $bill->period_year }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tanggal Jatuh Tempo</td>
                                <td>
                                    <strong>{{ $bill->due_date->format('d F Y') }}</strong>
                                    @if($bill->due_date->isPast() && $bill->status !== 'paid')
                                    <span class="badge bg-danger ms-2">Terlambat</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="40%" class="text-muted">Nominal Tagihan</td>
                                <td><strong class="text-primary fs-5">Rp {{ number_format($bill->amount, 0, ',', '.') }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Sudah Dibayar</td>
                                <td><strong class="text-success">Rp {{ number_format($bill->paid_amount, 0, ',', '.') }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Sisa Tagihan</td>
                                <td>
                                    @php $remaining = $bill->amount - $bill->paid_amount @endphp
                                    <strong class="{{ $remaining > 0 ? 'text-danger' : 'text-success' }}">
                                        Rp {{ number_format($remaining, 0, ',', '.') }}
                                    </strong>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Dibuat Pada</td>
                                <td>{{ $bill->created_at->format('d M Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                @if($bill->description)
                <hr>
                <div class="mb-0">
                    <strong class="text-muted">Keterangan:</strong>
                    <p class="mb-0">{{ $bill->description }}</p>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Payment History -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-history me-2"></i>Riwayat Pembayaran
            </div>
            <div class="card-body">
                @if($bill->payments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Order ID</th>
                                <th>Metode</th>
                                <th>Nominal</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bill->payments as $payment)
                            <tr>
                                <td>{{ $payment->created_at->format('d M Y H:i') }}</td>
                                <td><code>{{ $payment->order_id }}</code></td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        {{ strtoupper($payment->payment_type ?? '-') }}
                                    </span>
                                </td>
                                <td>Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                                <td>
                                    @if($payment->status === 'success')
                                    <span class="badge bg-success">Berhasil</span>
                                    @elseif($payment->status === 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                    @elseif($payment->status === 'failed')
                                    <span class="badge bg-danger">Gagal</span>
                                    @else
                                    <span class="badge bg-secondary">{{ ucfirst($payment->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada pembayaran</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Student Info -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-user-graduate me-2"></i>Informasi Siswa
            </div>
            <div class="card-body text-center">
                <div class="user-avatar mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                    {{ $bill->student ? substr($bill->student->name, 0, 1) : '?' }}
                </div>
                <h5>{{ $bill->student->name ?? 'Siswa Dihapus' }}</h5>
                <p class="text-muted mb-2">NIS: {{ $bill->student->nis ?? '-' }}</p>
                <span class="badge bg-primary">{{ $bill->student->class ?? '-' }}</span>
            </div>
            <div class="card-footer bg-transparent">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted">Orang Tua</td>
                        <td class="text-end">{{ $bill->student->parent->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Telepon</td>
                        <td class="text-end">{{ $bill->student->parent->phone ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Email</td>
                        <td class="text-end">{{ $bill->student->parent->email ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-bolt me-2"></i>Aksi Cepat
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if($bill->status !== 'paid' && $bill->status !== 'cancelled')
                    <form action="{{ route('treasurer.bills.send-reminder', $bill) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success w-100" {{ ($bill->student->parent->email ?? null) ? '' : 'disabled' }}>
                            <i class="fas fa-envelope me-2"></i>Kirim Pengingat Email
                        </button>
                    </form>
                    @if(!($bill->student->parent->email ?? null))
                    <small class="text-muted text-center">Email orang tua belum diisi</small>
                    @endif
                    @endif
                    <button type="button" class="btn btn-outline-primary" onclick="printInvoice()">
                        <i class="fas fa-print me-2"></i>Cetak Invoice
                    </button>
                    <a href="{{ route('treasurer.bills.index', ['student_id' => $bill->student_id]) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-list me-2"></i>Lihat Semua Tagihan Siswa
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Form (Hidden) -->
<form id="deleteForm" action="{{ route('treasurer.bills.destroy', $bill->id) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
function confirmDelete() {
    Swal.fire({
        title: 'Hapus Tagihan?',
        text: 'Tagihan ini akan dihapus secara permanen!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DC2626',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('deleteForm').submit();
        }
    });
}

function sendReminder() {
    Swal.fire({
        title: 'Kirim Pengingat?',
        text: 'Notifikasi akan dikirim ke orang tua siswa',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Kirim',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // AJAX call to send reminder
            Swal.fire('Terkirim!', 'Pengingat berhasil dikirim.', 'success');
        }
    });
}

function printInvoice() {
    window.print();
}
</script>

<style>
@media print {
    .btn, .card-header, nav, .sidebar {
        display: none !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
}
</style>
@endpush
