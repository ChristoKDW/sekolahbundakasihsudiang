@extends('layouts.app')

@section('title', 'Laporan Pendapatan')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1 class="page-title">Laporan Pendapatan</h1>
        <p class="page-subtitle">Analisis pendapatan sekolah dari pembayaran</p>
    </div>
    <div>
        <button type="button" class="btn btn-success" onclick="window.print()">
            <i class="fas fa-print me-2"></i>Cetak
        </button>
    </div>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Tahun</label>
                <select name="year" class="form-select">
                    @for($y = date('Y'); $y >= 2020; $y--)
                    <option value="{{ $y }}" {{ request('year', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Jenis Tagihan</label>
                <select name="bill_type" class="form-select">
                    <option value="">Semua Jenis</option>
                    @foreach($billTypes as $type)
                    <option value="{{ $type->id }}" {{ request('bill_type') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-filter me-2"></i>Terapkan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white h-100">
            <div class="card-body">
                <h6><i class="fas fa-calendar-alt me-2"></i>Total Pendapatan Tahun {{ request('year', date('Y')) }}</h6>
                <h2 class="mb-0">Rp {{ number_format($yearly_income, 0, ',', '.') }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white h-100">
            <div class="card-body">
                <h6><i class="fas fa-chart-line me-2"></i>Rata-rata Bulanan</h6>
                <h2 class="mb-0">Rp {{ number_format($monthly_average, 0, ',', '.') }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white h-100">
            <div class="card-body">
                <h6><i class="fas fa-arrow-up me-2"></i>Bulan Tertinggi</h6>
                <h2 class="mb-0">{{ $highest_month['name'] }}</h2>
                <small>Rp {{ number_format($highest_month['amount'], 0, ',', '.') }}</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white h-100">
            <div class="card-body">
                <h6><i class="fas fa-receipt me-2"></i>Total Transaksi</h6>
                <h2 class="mb-0">{{ number_format($total_transactions) }}</h2>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header">
                <i class="fas fa-chart-bar me-2"></i>Tren Pendapatan Bulanan {{ request('year', date('Y')) }}
            </div>
            <div class="card-body">
                <canvas id="monthlyIncomeChart" height="300"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="fas fa-chart-pie me-2"></i>Distribusi per Jenis
            </div>
            <div class="card-body">
                <canvas id="incomeByTypeChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Monthly Detail Table -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-table me-2"></i>Detail Pendapatan Bulanan
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="monthlyIncomeTable">
                <thead>
                    <tr>
                        <th>Bulan</th>
                        <th class="text-end">Jumlah Transaksi</th>
                        <th class="text-end">Total Pendapatan</th>
                        <th class="text-end">Perubahan</th>
                        <th>Progress</th>
                    </tr>
                </thead>
                <tbody>
                    @php $prevAmount = 0; @endphp
                    @foreach($monthly_data as $month)
                    <tr>
                        <td><strong>{{ $month['name'] }}</strong></td>
                        <td class="text-end">{{ number_format($month['transactions']) }}</td>
                        <td class="text-end">
                            <strong>Rp {{ number_format($month['amount'], 0, ',', '.') }}</strong>
                        </td>
                        <td class="text-end">
                            @if($prevAmount > 0)
                                @php $change = (($month['amount'] - $prevAmount) / $prevAmount) * 100; @endphp
                                <span class="badge bg-{{ $change >= 0 ? 'success' : 'danger' }}">
                                    <i class="fas fa-arrow-{{ $change >= 0 ? 'up' : 'down' }}"></i>
                                    {{ number_format(abs($change), 1) }}%
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                            @php $prevAmount = $month['amount'] ?: $prevAmount; @endphp
                        </td>
                        <td>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-primary" style="width: {{ ($month['amount'] / max($highest_month['amount'], 1)) * 100 }}%"></div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-dark">
                    <tr>
                        <th>TOTAL</th>
                        <th class="text-end">{{ number_format($total_transactions) }}</th>
                        <th class="text-end">Rp {{ number_format($yearly_income, 0, ',', '.') }}</th>
                        <th></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Income by Type -->
<div class="card mt-4">
    <div class="card-header">
        <i class="fas fa-layer-group me-2"></i>Pendapatan per Jenis Tagihan
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="incomeByTypeTable">
                <thead>
                    <tr>
                        <th>Jenis Tagihan</th>
                        <th class="text-end">Jumlah Transaksi</th>
                        <th class="text-end">Total Pendapatan</th>
                        <th class="text-end">Persentase</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($income_by_type as $type)
                    <tr>
                        <td>
                            <i class="fas fa-circle text-primary me-2" style="font-size: 0.5rem; vertical-align: middle;"></i>
                            <strong>{{ $type['name'] }}</strong>
                        </td>
                        <td class="text-end">{{ number_format($type['transactions']) }}</td>
                        <td class="text-end">
                            <strong>Rp {{ number_format($type['amount'], 0, ',', '.') }}</strong>
                        </td>
                        <td class="text-end">
                            <span class="badge bg-primary">{{ number_format($type['percentage'], 1) }}%</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Monthly Income Chart
const monthlyCtx = document.getElementById('monthlyIncomeChart').getContext('2d');
new Chart(monthlyCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode(collect($monthly_data)->pluck('name')) !!},
        datasets: [{
            label: 'Pendapatan',
            data: {!! json_encode(collect($monthly_data)->pluck('amount')) !!},
            backgroundColor: 'rgba(79, 70, 229, 0.8)',
            borderColor: '#4F46E5',
            borderWidth: 1,
            borderRadius: 8
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

// Income by Type Chart
const typeCtx = document.getElementById('incomeByTypeChart').getContext('2d');
new Chart(typeCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode(collect($income_by_type)->pluck('name')) !!},
        datasets: [{
            data: {!! json_encode(collect($income_by_type)->pluck('amount')) !!},
            backgroundColor: ['#4F46E5', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899'],
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

// DataTables initialization
$('#monthlyIncomeTable').DataTable({
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

$('#incomeByTypeTable').DataTable({
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
</script>
@endpush
