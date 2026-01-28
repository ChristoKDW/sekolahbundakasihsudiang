@extends('layouts.app')

@section('title', 'Laporan Pembayaran')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1 class="page-title">Laporan Pembayaran</h1>
        <p class="page-subtitle">Ringkasan dan analisis pembayaran</p>
    </div>
    <div>
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exportModal">
            <i class="fas fa-file-excel me-2"></i>Export Excel
        </button>
        <button type="button" class="btn btn-danger" onclick="window.print()">
            <i class="fas fa-file-pdf me-2"></i>Cetak PDF
        </button>
    </div>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Periode</label>
                <select name="period" class="form-select" onchange="toggleDateInputs(this.value)">
                    <option value="today" {{ request('period') == 'today' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="week" {{ request('period') == 'week' ? 'selected' : '' }}>Minggu Ini</option>
                    <option value="month" {{ request('period', 'month') == 'month' ? 'selected' : '' }}>Bulan Ini</option>
                    <option value="year" {{ request('period') == 'year' ? 'selected' : '' }}>Tahun Ini</option>
                    <option value="custom" {{ request('period') == 'custom' ? 'selected' : '' }}>Custom</option>
                </select>
            </div>
            <div class="col-md-2 date-inputs" style="{{ request('period') != 'custom' ? 'display:none' : '' }}">
                <label class="form-label">Dari Tanggal</label>
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>
            <div class="col-md-2 date-inputs" style="{{ request('period') != 'custom' ? 'display:none' : '' }}">
                <label class="form-label">Sampai Tanggal</label>
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Jenis Tagihan</label>
                <select name="bill_type" class="form-select">
                    <option value="">Semua</option>
                    @foreach($billTypes as $type)
                    <option value="{{ $type->id }}" {{ request('bill_type') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-filter me-2"></i>Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Summary Stats -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6><i class="fas fa-money-bill-wave me-2"></i>Total Pemasukan</h6>
                <h3 class="mb-0">Rp {{ number_format($summary['total_income'], 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6><i class="fas fa-receipt me-2"></i>Jumlah Transaksi</h6>
                <h3 class="mb-0">{{ number_format($summary['total_transactions']) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h6><i class="fas fa-chart-bar me-2"></i>Rata-rata</h6>
                <h3 class="mb-0">Rp {{ number_format($summary['average'], 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h6><i class="fas fa-check-circle me-2"></i>Tagihan Lunas</h6>
                <h3 class="mb-0">{{ number_format($summary['bills_paid']) }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Chart -->
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header">
                <i class="fas fa-chart-line me-2"></i>Tren Pembayaran
            </div>
            <div class="card-body">
                <canvas id="paymentTrendChart" height="300"></canvas>
            </div>
        </div>
    </div>

    <!-- By Type -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="fas fa-chart-pie me-2"></i>Per Jenis Tagihan
            </div>
            <div class="card-body">
                <canvas id="paymentByTypeChart" height="280"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Payment Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-list me-2"></i>Daftar Pembayaran</span>
        <span class="badge bg-primary">{{ $payments->total() }} transaksi</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>No. Transaksi</th>
                        <th>Siswa</th>
                        <th>Jenis Tagihan</th>
                        <th>Jumlah</th>
                        <th>Metode</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    <tr>
                        <td>{{ $payment->paid_at?->format('d/m/Y H:i') ?? '-' }}</td>
                        <td><code>{{ $payment->order_id }}</code></td>
                        <td>
                            <strong>{{ $payment->bill->student->name }}</strong>
                            <small class="d-block text-muted">{{ $payment->bill->student->class }}</small>
                        </td>
                        <td>{{ $payment->bill->billType->name }}</td>
                        <td><strong class="text-success">{{ $payment->formatted_amount }}</strong></td>
                        <td>{{ $payment->payment_method_label }}</td>
                        <td>{!! $payment->status_badge !!}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            Tidak ada data pembayaran pada periode ini
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

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('treasurer.reports.export') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Export Laporan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Format</label>
                        <select name="format" class="form-select">
                            <option value="xlsx">Excel (.xlsx)</option>
                            <option value="pdf">PDF</option>
                            <option value="csv">CSV</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Dari Tanggal</label>
                            <input type="date" name="start_date" class="form-control" value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Sampai Tanggal</label>
                            <input type="date" name="end_date" class="form-control" value="{{ request('end_date', now()->format('Y-m-d')) }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-download me-2"></i>Download
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleDateInputs(value) {
    const inputs = document.querySelectorAll('.date-inputs');
    inputs.forEach(input => {
        input.style.display = value === 'custom' ? 'block' : 'none';
    });
}

// Payment Trend Chart
const trendCtx = document.getElementById('paymentTrendChart').getContext('2d');
new Chart(trendCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($chartData['labels']) !!},
        datasets: [{
            label: 'Pemasukan',
            data: {!! json_encode($chartData['data']) !!},
            borderColor: '#4F46E5',
            backgroundColor: 'rgba(79, 70, 229, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'Rp ' + (value / 1000000) + 'Jt';
                    }
                }
            }
        }
    }
});

// Payment by Type Chart
const typeCtx = document.getElementById('paymentByTypeChart').getContext('2d');
new Chart(typeCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($byType->pluck('name')) !!},
        datasets: [{
            data: {!! json_encode($byType->pluck('total')) !!},
            backgroundColor: ['#4F46E5', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6'],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: { padding: 15, usePointStyle: true }
            }
        }
    }
});
</script>
@endpush
