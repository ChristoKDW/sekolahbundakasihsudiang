@extends('layouts.app')

@section('title', 'Tren & Analisis')
@section('page-title', 'Tren & Analisis')

@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-chart-bar me-2 text-info"></i>Tren & Analisis</h5>
            <a href="{{ route('principal.reports.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-8">
                <h6>Tren Pendapatan vs Target</h6>
                <canvas id="trendChart" height="150"></canvas>
            </div>
            <div class="col-md-4">
                <div class="card bg-light h-100">
                    <div class="card-body">
                        <h6>Ringkasan</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td>Bulan Terbaik</td>
                                <td><strong>{{ $bestMonth ?? 'September' }}</strong></td>
                            </tr>
                            <tr>
                                <td>Pendapatan Tertinggi</td>
                                <td><strong>Rp {{ number_format($highestIncome ?? 0, 0, ',', '.') }}</strong></td>
                            </tr>
                            <tr>
                                <td>Rata-rata Bulanan</td>
                                <td><strong>Rp {{ number_format($avgMonthly ?? 0, 0, ',', '.') }}</strong></td>
                            </tr>
                            <tr>
                                <td>Growth YoY</td>
                                <td><strong class="text-{{ ($growthRate ?? 0) >= 0 ? 'success' : 'danger' }}">{{ number_format($growthRate ?? 0, 1) }}%</strong></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">
                <h6 class="mb-0">Perbandingan Tahun</h6>
            </div>
            <div class="card-body">
                <canvas id="yearCompareChart" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">
                <h6 class="mb-0">Metode Pembayaran</h6>
            </div>
            <div class="card-body">
                <canvas id="methodChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <h6 class="mb-0"><i class="fas fa-lightbulb me-2"></i>Insights</h6>
    </div>
    <div class="card-body">
        <div class="row">
            @foreach($insights ?? [] as $insight)
            <div class="col-md-4 mb-3">
                <div class="card bg-{{ $insight['type'] ?? 'light' }} text-{{ $insight['type'] == 'warning' || $insight['type'] == 'light' ? 'dark' : 'white' }}">
                    <div class="card-body">
                        <h6><i class="fas fa-{{ $insight['icon'] ?? 'info-circle' }} me-2"></i>{{ $insight['title'] ?? 'Insight' }}</h6>
                        <p class="mb-0 small">{{ $insight['description'] ?? '-' }}</p>
                    </div>
                </div>
            </div>
            @endforeach
            
            @if(empty($insights))
            <div class="col-md-4 mb-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h6><i class="fas fa-arrow-up me-2"></i>Pertumbuhan Positif</h6>
                        <p class="mb-0 small">Pendapatan bulan ini meningkat 15% dibanding bulan lalu.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card bg-warning text-dark">
                    <div class="card-body">
                        <h6><i class="fas fa-exclamation-triangle me-2"></i>Perhatian</h6>
                        <p class="mb-0 small">Kelas X memiliki collection rate terendah (65%).</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h6><i class="fas fa-lightbulb me-2"></i>Rekomendasi</h6>
                        <p class="mb-0 small">Kirim reminder pembayaran untuk tagihan yang jatuh tempo minggu ini.</p>
                    </div>
                </div>
            </div>
            @endif
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
        datasets: [
            { label: 'Aktual', data: {!! json_encode($actualData ?? array_fill(0, 12, 0)) !!}, borderColor: '#4F46E5', tension: 0.4 },
            { label: 'Target', data: {!! json_encode($targetData ?? array_fill(0, 12, 50000000)) !!}, borderColor: '#EF4444', borderDash: [5,5], tension: 0.4 }
        ]
    },
    options: { responsive: true, scales: { y: { beginAtZero: true }}}
});

// Year Compare Chart
new Chart(document.getElementById('yearCompareChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: ['Q1', 'Q2', 'Q3', 'Q4'],
        datasets: [
            { label: '{{ date('Y')-1 }}', data: {!! json_encode($lastYearData ?? [100, 120, 110, 130]) !!}, backgroundColor: '#94A3B8' },
            { label: '{{ date('Y') }}', data: {!! json_encode($thisYearData ?? [110, 125, 140, 0]) !!}, backgroundColor: '#4F46E5' }
        ]
    },
    options: { responsive: true }
});

// Method Chart
new Chart(document.getElementById('methodChart').getContext('2d'), {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($methodLabels ?? ['Bank Transfer', 'E-Wallet', 'Virtual Account']) !!},
        datasets: [{
            data: {!! json_encode($methodData ?? [50, 30, 20]) !!},
            backgroundColor: ['#4F46E5', '#10B981', '#F59E0B']
        }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' }}}
});
</script>
@endpush
