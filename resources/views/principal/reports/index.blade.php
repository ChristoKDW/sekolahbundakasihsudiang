@extends('layouts.app')

@section('title', 'Laporan Kepala Sekolah')
@section('page-title', 'Dashboard Laporan')

@section('content')
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-primary text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <i class="fas fa-money-bill-wave fa-2x me-3 opacity-75"></i>
                    <div>
                        <h4 class="mb-0">Rp {{ number_format($totalIncome ?? 0, 0, ',', '.') }}</h4>
                        <small>Total Pendapatan</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-success text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle fa-2x me-3 opacity-75"></i>
                    <div>
                        <h4 class="mb-0">{{ number_format($collectionRate ?? 0, 1) }}%</h4>
                        <small>Collection Rate</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-warning text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <i class="fas fa-users fa-2x me-3 opacity-75"></i>
                    <div>
                        <h4 class="mb-0">{{ $totalStudents ?? 0 }}</h4>
                        <small>Total Siswa</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-danger text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle fa-2x me-3 opacity-75"></i>
                    <div>
                        <h4 class="mb-0">Rp {{ number_format($totalReceivables ?? 0, 0, ',', '.') }}</h4>
                        <small>Total Piutang</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-chart-line me-2"></i>Tren Pendapatan 12 Bulan Terakhir</h6>
            </div>
            <div class="card-body">
                <canvas id="trendChart" height="300"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-list me-2"></i>Menu Laporan</h6>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <a href="{{ route('principal.reports.income') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-money-bill-wave text-success me-2"></i>Laporan Pendapatan
                    </a>
                    <a href="{{ route('principal.reports.collection') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-percentage text-primary me-2"></i>Collection Rate
                    </a>
                    <a href="{{ route('principal.reports.outstanding') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-file-invoice-dollar text-warning me-2"></i>Outstanding Balance
                    </a>
                    <a href="{{ route('principal.reports.trends') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-chart-bar text-info me-2"></i>Tren & Analisis
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Distribusi per Jenis Tagihan</h6>
            </div>
            <div class="card-body">
                <canvas id="typeChart" height="250"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Status per Kelas</h6>
            </div>
            <div class="card-body">
                <canvas id="classChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Trend Chart
new Chart(document.getElementById('trendChart').getContext('2d'), {
    type: 'line',
    data: {
        labels: {!! json_encode($trendLabels ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']) !!},
        datasets: [{
            label: 'Pendapatan',
            data: {!! json_encode($trendData ?? array_fill(0, 12, 0)) !!},
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
            y: { beginAtZero: true, ticks: { callback: function(v) { return 'Rp ' + (v/1000000).toFixed(0) + 'jt'; }}}
        }
    }
});

// Type Chart
new Chart(document.getElementById('typeChart').getContext('2d'), {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($typeLabels ?? ['SPP', 'Uang Gedung', 'Lainnya']) !!},
        datasets: [{
            data: {!! json_encode($typeData ?? [60, 30, 10]) !!},
            backgroundColor: ['#4F46E5', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6']
        }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' }}}
});

// Class Chart
new Chart(document.getElementById('classChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($classLabels ?? ['X', 'XI', 'XII']) !!},
        datasets: [
            { label: 'Lunas', data: {!! json_encode($classPaid ?? [80, 75, 90]) !!}, backgroundColor: '#10B981' },
            { label: 'Belum', data: {!! json_encode($classUnpaid ?? [20, 25, 10]) !!}, backgroundColor: '#EF4444' }
        ]
    },
    options: {
        responsive: true,
        scales: { x: { stacked: true }, y: { stacked: true, beginAtZero: true }}
    }
});
</script>
@endpush
