@extends('layouts.app')

@section('title', 'Dashboard Kepala Sekolah')

@section('content')
<div class="page-header">
    <h1 class="page-title">Dashboard Kepala Sekolah</h1>
    <p class="page-subtitle">Selamat datang, {{ auth()->user()->name }}! Ringkasan performa keuangan sekolah.</p>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ number_format($total_students) }}</div>
                <div class="stat-label">Siswa Aktif</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon success">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">Rp {{ number_format($monthly_income / 1000000, 1) }}Jt</div>
                <div class="stat-label">Pendapatan Bulan Ini</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon info">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">Rp {{ number_format($yearly_income / 1000000, 1) }}Jt</div>
                <div class="stat-label">Pendapatan Tahun Ini</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon {{ $collection_rate >= 80 ? 'success' : ($collection_rate >= 60 ? 'warning' : 'danger') }}">
                <i class="fas fa-percentage"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $collection_rate }}%</div>
                <div class="stat-label">Tingkat Koleksi</div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-4">
    <div class="col-xl-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-chart-area me-2"></i>Tren Pendapatan Bulanan {{ date('Y') }}</span>
                <a href="{{ route('principal.reports.income') }}" class="btn btn-sm btn-outline-primary">
                    Detail
                </a>
            </div>
            <div class="card-body">
                <canvas id="monthlyIncomeChart" height="300"></canvas>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="fas fa-tags me-2"></i>Pendapatan per Jenis
            </div>
            <div class="card-body">
                <canvas id="paymentByTypeChart" height="280"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-4">
    <div class="col-md-4">
        <div class="card text-center h-100">
            <div class="card-body py-4">
                <div class="display-4 text-primary mb-3">
                    <i class="fas fa-clock"></i>
                </div>
                <h3 class="mb-1">{{ number_format($pending_bills) }}</h3>
                <p class="text-muted mb-0">Tagihan Menunggu Pembayaran</p>
            </div>
            <div class="card-footer bg-transparent">
                <a href="{{ route('principal.reports.outstanding') }}" class="btn btn-sm btn-outline-primary">
                    Lihat Detail
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card text-center h-100 border-success">
            <div class="card-body py-4">
                <div class="display-4 text-success mb-3">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <h3 class="mb-1">{{ $collection_rate }}%</h3>
                <p class="text-muted mb-0">Tingkat Koleksi Pembayaran</p>
                <div class="progress mt-3" style="height: 10px;">
                    <div class="progress-bar bg-success" style="width: {{ $collection_rate }}%"></div>
                </div>
            </div>
            <div class="card-footer bg-transparent">
                <a href="{{ route('principal.reports.collection') }}" class="btn btn-sm btn-outline-success">
                    Analisis Koleksi
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card text-center h-100">
            <div class="card-body py-4">
                <div class="display-4 text-info mb-3">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <h3 class="mb-1">Rp {{ number_format($yearly_income, 0, ',', '.') }}</h3>
                <p class="text-muted mb-0">Total Pendapatan Tahun {{ date('Y') }}</p>
            </div>
            <div class="card-footer bg-transparent">
                <a href="{{ route('principal.reports.income') }}" class="btn btn-sm btn-outline-info">
                    Laporan Lengkap
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Monthly Income Chart
    const monthlyCtx = document.getElementById('monthlyIncomeChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($monthly_chart_data['labels']) !!},
            datasets: [{
                label: 'Pendapatan',
                data: {!! json_encode($monthly_chart_data['data']) !!},
                borderColor: '#4F46E5',
                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#4F46E5',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
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
            labels: {!! json_encode($payment_by_type->pluck('name')) !!},
            datasets: [{
                data: {!! json_encode($payment_by_type->pluck('total')) !!},
                backgroundColor: [
                    '#4F46E5',
                    '#10B981',
                    '#F59E0B',
                    '#EF4444',
                    '#8B5CF6',
                    '#EC4899'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        usePointStyle: true
                    }
                }
            }
        }
    });
</script>
@endpush
