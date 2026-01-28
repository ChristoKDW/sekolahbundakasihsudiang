@extends('layouts.app')

@section('title', 'Laporan Koleksi')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1 class="page-title">Laporan Tingkat Koleksi</h1>
        <p class="page-subtitle">Analisis efektivitas pengumpulan pembayaran</p>
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
                <label class="form-label">Periode</label>
                <select name="period" class="form-select">
                    <option value="month" {{ request('period', 'month') == 'month' ? 'selected' : '' }}>Bulan Ini</option>
                    <option value="quarter" {{ request('period') == 'quarter' ? 'selected' : '' }}>Kuartal Ini</option>
                    <option value="semester" {{ request('period') == 'semester' ? 'selected' : '' }}>Semester Ini</option>
                    <option value="year" {{ request('period') == 'year' ? 'selected' : '' }}>Tahun Ini</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Tahun Ajaran</label>
                <select name="academic_year" class="form-select">
                    @foreach($academic_years as $year)
                    <option value="{{ $year }}" {{ request('academic_year', $current_year) == $year ? 'selected' : '' }}>{{ $year }}</option>
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

<!-- Main Stats -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="position-relative d-inline-block mb-3">
                    <canvas id="collectionGauge" width="180" height="180"></canvas>
                    <div class="position-absolute top-50 start-50 translate-middle">
                        <h2 class="mb-0 {{ $collection_rate >= 80 ? 'text-success' : ($collection_rate >= 60 ? 'text-warning' : 'text-danger') }}">
                            {{ $collection_rate }}%
                        </h2>
                        <small class="text-muted">Tingkat Koleksi</small>
                    </div>
                </div>
                <h5>Status: 
                    @if($collection_rate >= 80)
                    <span class="badge bg-success">Sangat Baik</span>
                    @elseif($collection_rate >= 60)
                    <span class="badge bg-warning">Cukup</span>
                    @else
                    <span class="badge bg-danger">Perlu Perhatian</span>
                    @endif
                </h5>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card bg-primary text-white h-100">
                    <div class="card-body">
                        <h6><i class="fas fa-file-invoice me-2"></i>Total Tagihan</h6>
                        <h2>Rp {{ number_format($total_billed, 0, ',', '.') }}</h2>
                        <small>{{ number_format($total_bills) }} tagihan</small>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-success text-white h-100">
                    <div class="card-body">
                        <h6><i class="fas fa-check-circle me-2"></i>Terkumpul</h6>
                        <h2>Rp {{ number_format($total_collected, 0, ',', '.') }}</h2>
                        <small>{{ number_format($bills_paid) }} tagihan lunas</small>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-warning text-white h-100">
                    <div class="card-body">
                        <h6><i class="fas fa-clock me-2"></i>Belum Terkumpul</h6>
                        <h2>Rp {{ number_format($total_outstanding, 0, ',', '.') }}</h2>
                        <small>{{ number_format($bills_pending) }} tagihan pending</small>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-danger text-white h-100">
                    <div class="card-body">
                        <h6><i class="fas fa-exclamation-triangle me-2"></i>Terlambat</h6>
                        <h2>Rp {{ number_format($total_overdue, 0, ',', '.') }}</h2>
                        <small>{{ number_format($bills_overdue) }} tagihan terlambat</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Collection by Class -->
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-graduation-cap me-2"></i>Tingkat Koleksi per Kelas
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="collectionByClassTable">
                <thead>
                    <tr>
                        <th>Kelas</th>
                        <th>Siswa</th>
                        <th class="text-end">Total Tagihan</th>
                        <th class="text-end">Terkumpul</th>
                        <th class="text-end">Outstanding</th>
                        <th>Tingkat Koleksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($collection_by_class as $class)
                    <tr>
                        <td><strong>{{ $class['name'] }}</strong></td>
                        <td>{{ $class['students'] }}</td>
                        <td class="text-end">Rp {{ number_format($class['billed'], 0, ',', '.') }}</td>
                        <td class="text-end text-success">Rp {{ number_format($class['collected'], 0, ',', '.') }}</td>
                        <td class="text-end text-danger">Rp {{ number_format($class['outstanding'], 0, ',', '.') }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="progress flex-grow-1" style="height: 8px;">
                                    <div class="progress-bar bg-{{ $class['rate'] >= 80 ? 'success' : ($class['rate'] >= 60 ? 'warning' : 'danger') }}" 
                                         style="width: {{ $class['rate'] }}%"></div>
                                </div>
                                <span class="ms-2 fw-bold">{{ $class['rate'] }}%</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Collection by Bill Type -->
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header">
                <i class="fas fa-chart-bar me-2"></i>Koleksi per Jenis Tagihan
            </div>
            <div class="card-body">
                <canvas id="collectionByTypeChart" height="300"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="fas fa-list-ol me-2"></i>Ranking Koleksi
            </div>
            <div class="card-body">
                @foreach($collection_by_type->sortByDesc('rate')->take(5) as $index => $type)
                <div class="d-flex align-items-center mb-3">
                    <div class="me-3">
                        <span class="badge bg-{{ $index == 0 ? 'warning' : ($index == 1 ? 'secondary' : 'light text-dark') }} fs-6">
                            #{{ $index + 1 }}
                        </span>
                    </div>
                    <div class="flex-grow-1">
                        <strong>{{ $type['name'] }}</strong>
                        <div class="progress mt-1" style="height: 6px;">
                            <div class="progress-bar bg-primary" style="width: {{ $type['rate'] }}%"></div>
                        </div>
                    </div>
                    <div class="ms-3">
                        <strong>{{ $type['rate'] }}%</strong>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Collection Gauge
const gaugeCtx = document.getElementById('collectionGauge').getContext('2d');
new Chart(gaugeCtx, {
    type: 'doughnut',
    data: {
        datasets: [{
            data: [{{ $collection_rate }}, {{ 100 - $collection_rate }}],
            backgroundColor: [
                '{{ $collection_rate >= 80 ? "#10B981" : ($collection_rate >= 60 ? "#F59E0B" : "#EF4444") }}',
                '#E5E7EB'
            ],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        cutout: '80%',
        rotation: -90,
        circumference: 180,
        plugins: {
            legend: { display: false },
            tooltip: { enabled: false }
        }
    }
});

// Collection by Type Chart
const typeCtx = document.getElementById('collectionByTypeChart').getContext('2d');
new Chart(typeCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($collection_by_type->pluck('name')) !!},
        datasets: [
            {
                label: 'Terkumpul',
                data: {!! json_encode($collection_by_type->pluck('collected')) !!},
                backgroundColor: '#10B981'
            },
            {
                label: 'Outstanding',
                data: {!! json_encode($collection_by_type->pluck('outstanding')) !!},
                backgroundColor: '#EF4444'
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom' },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                    }
                }
            }
        },
        scales: {
            x: { stacked: true },
            y: {
                stacked: true,
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

// DataTables initialization
$('#collectionByClassTable').DataTable({
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
