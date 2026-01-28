@extends('layouts.app')

@section('title', 'Laporan Bulanan')
@section('page-title', 'Laporan Bulanan')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0"><i class="fas fa-calendar-alt me-2 text-success"></i>Laporan Bulanan</h5>
            <div class="d-flex gap-2">
                <a href="{{ route('treasurer.reports.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Kembali
                </a>
                <a href="{{ route('treasurer.reports.export-pdf') }}?type=monthly" class="btn btn-danger btn-sm">
                    <i class="fas fa-file-pdf me-1"></i>Export PDF
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <!-- Filter -->
        <form action="" method="GET" class="mb-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Tahun</label>
                    <select name="year" class="form-select">
                        @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                            <option value="{{ $y }}" {{ request('year', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                </div>
            </div>
        </form>
        
        <div class="row mb-4">
            <div class="col-md-8">
                <canvas id="monthlyChart" height="300"></canvas>
            </div>
            <div class="col-md-4">
                <div class="card bg-light border-0 h-100">
                    <div class="card-body">
                        <h6 class="text-muted mb-3">Ringkasan Tahun {{ request('year', date('Y')) }}</h6>
                        <h3 class="text-primary">Rp {{ number_format($yearlyTotal ?? 0, 0, ',', '.') }}</h3>
                        <p class="text-muted mb-3">Total Pendapatan</p>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Rata-rata/bulan</span>
                            <strong>Rp {{ number_format(($yearlyTotal ?? 0) / 12, 0, ',', '.') }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Transaksi</span>
                            <strong>{{ $yearlyTransactions ?? 0 }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="monthlyTable">
                <thead class="table-light">
                    <tr>
                        <th>Bulan</th>
                        <th class="text-end">Total Tagihan</th>
                        <th class="text-end">Terbayar</th>
                        <th class="text-end">Piutang</th>
                        <th>% Collection</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($monthlyData ?? [] as $month)
                    <tr>
                        <td>{{ $month['name'] }}</td>
                        <td class="text-end">Rp {{ number_format($month['total'], 0, ',', '.') }}</td>
                        <td class="text-end text-success">Rp {{ number_format($month['paid'], 0, ',', '.') }}</td>
                        <td class="text-end text-danger">Rp {{ number_format($month['unpaid'], 0, ',', '.') }}</td>
                        <td>
                            <div class="progress" style="width: 100px; height: 20px;">
                                <div class="progress-bar bg-success" style="width: {{ $month['percentage'] ?? 0 }}%">
                                    {{ number_format($month['percentage'] ?? 0, 1) }}%
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">Tidak ada data</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('monthlyChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: {!! json_encode($chartLabels ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']) !!},
        datasets: [{
            label: 'Pendapatan',
            data: {!! json_encode($chartData ?? array_fill(0, 12, 0)) !!},
            borderColor: 'rgba(79, 70, 229, 1)',
            backgroundColor: 'rgba(79, 70, 229, 0.1)',
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                    }
                }
            }
        }
    }
});

// DataTables initialization
if ($('#monthlyTable tbody tr').length > 0 && !$('#monthlyTable tbody tr td').hasClass('text-center')) {
    $('#monthlyTable').DataTable({
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
</script>
@endpush
