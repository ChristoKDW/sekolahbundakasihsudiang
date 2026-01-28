@extends('layouts.app')

@section('title', 'Laporan Pembayaran')
@section('page-title', 'Laporan Pembayaran')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0"><i class="fas fa-receipt me-2 text-primary"></i>Laporan Pembayaran</h5>
            <div class="d-flex gap-2">
                <a href="{{ route('treasurer.reports.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Kembali
                </a>
                <a href="{{ route('treasurer.reports.export-pdf') }}?type=payments" class="btn btn-danger btn-sm">
                    <i class="fas fa-file-pdf me-1"></i>Export PDF
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <!-- Filter -->
        <form action="" method="GET" class="mb-4">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal Akhir</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Sukses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Gagal</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                </div>
            </div>
        </form>
        
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="paymentsReportTable">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>Transaction ID</th>
                        <th>Siswa</th>
                        <th>Jenis Tagihan</th>
                        <th>Metode</th>
                        <th class="text-end">Jumlah</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments ?? [] as $payment)
                    <tr>
                        <td>{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                        <td><code>{{ $payment->transaction_id }}</code></td>
                        <td>{{ $payment->bill->student->name ?? '-' }}</td>
                        <td>{{ $payment->bill->billType->name ?? '-' }}</td>
                        <td>{{ $payment->payment_method ?? '-' }}</td>
                        <td class="text-end"><strong>Rp {{ number_format($payment->amount, 0, ',', '.') }}</strong></td>
                        <td>
                            @if($payment->status == 'success')
                                <span class="badge bg-success">Sukses</span>
                            @elseif($payment->status == 'pending')
                                <span class="badge bg-warning">Pending</span>
                            @else
                                <span class="badge bg-danger">{{ ucfirst($payment->status) }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Tidak ada data pembayaran</td>
                    </tr>
                    @endforelse
                </tbody>
                @if(isset($payments) && $payments->count() > 0)
                <tfoot class="table-light">
                    <tr>
                        <th colspan="5">Total</th>
                        <th class="text-end">Rp {{ number_format($payments->sum('amount'), 0, ',', '.') }}</th>
                        <th></th>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
        
        @if(isset($payments) && method_exists($payments, 'links'))
        <div class="d-flex justify-content-center mt-3">
            {{ $payments->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#paymentsReportTable').DataTable({
        paging: true,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]],
        info: true,
        searching: true,
        ordering: true,
        order: [[0, 'desc']],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        }
    });
});
</script>
@endpush
