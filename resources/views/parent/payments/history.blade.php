@extends('layouts.app')

@section('title', 'Riwayat Pembayaran')

@section('content')
<div class="page-header">
    <h1 class="page-title">Riwayat Pembayaran</h1>
    <p class="page-subtitle">Daftar semua transaksi pembayaran Anda</p>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Siswa</label>
                <select name="student" class="form-select">
                    <option value="">Semua Siswa</option>
                    @foreach($students as $student)
                    <option value="{{ $student->id }}" {{ request('student') == $student->id ? 'selected' : '' }}>
                        {{ $student->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Berhasil</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Gagal</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Periode</label>
                <input type="month" name="month" class="form-control" value="{{ request('month') }}">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-filter me-2"></i>Filter
                </button>
                <a href="{{ route('parent.payments.history') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-refresh"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Summary -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6><i class="fas fa-check-circle me-2"></i>Total Pembayaran Berhasil</h6>
                <h3 class="mb-0">Rp {{ number_format($total_success, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6><i class="fas fa-receipt me-2"></i>Jumlah Transaksi</h6>
                <h3 class="mb-0">{{ number_format($payments->total()) }} transaksi</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h6><i class="fas fa-calendar me-2"></i>Pembayaran Bulan Ini</h6>
                <h3 class="mb-0">Rp {{ number_format($this_month, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Payment History -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-history me-2"></i>Daftar Transaksi
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="paymentHistoryTable">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>No. Transaksi</th>
                        <th>Siswa</th>
                        <th>Tagihan</th>
                        <th>Jumlah</th>
                        <th>Metode</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    <tr>
                        <td>{{ $payment->created_at->format('d M Y H:i') }}</td>
                        <td><code>{{ $payment->order_id }}</code></td>
                        <td>
                            <strong>{{ $payment->bill->student->name }}</strong>
                            <small class="d-block text-muted">{{ $payment->bill->student->class }}</small>
                        </td>
                        <td>
                            {{ $payment->bill->billType->name }}
                            @if($payment->bill->month)
                            <small class="d-block text-muted">{{ $payment->bill->month }}</small>
                            @endif
                        </td>
                        <td><strong>{{ $payment->formatted_amount }}</strong></td>
                        <td>{{ $payment->payment_method_label }}</td>
                        <td>{!! $payment->status_badge !!}</td>
                        <td>
                            @if($payment->status == 'success')
                            <a href="{{ route('parent.payments.receipt', $payment) }}" class="btn btn-sm btn-outline-primary" title="Bukti Bayar">
                                <i class="fas fa-download"></i>
                            </a>
                            @elseif($payment->status == 'pending')
                            <div class="btn-group">
                                @if($payment->xendit_invoice_url)
                                <a href="{{ $payment->xendit_invoice_url }}" target="_blank" class="btn btn-sm btn-primary" title="Lanjutkan Pembayaran">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                                @endif
                                <form action="{{ route('parent.payments.sync-status', $payment) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" title="Cek Status dari Xendit">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </form>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="empty-state">
                                <i class="fas fa-receipt"></i>
                                <h5>Belum ada riwayat pembayaran</h5>
                                <p class="text-muted">Riwayat pembayaran akan muncul di sini.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($payments->hasPages())
    <div class="card-footer">
        {{ $payments->links() }}
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    if ($('#paymentHistoryTable tbody tr').length > 0 && !$('#paymentHistoryTable tbody tr td[colspan]').length) {
        $('#paymentHistoryTable').DataTable({
            paging: true,
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]],
            info: true,
            searching: true,
            ordering: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
            }
        });
    }
});
</script>
@endpush
