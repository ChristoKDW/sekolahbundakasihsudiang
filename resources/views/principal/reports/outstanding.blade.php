@extends('layouts.app')

@section('title', 'Laporan Piutang')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1 class="page-title">Laporan Piutang (Outstanding)</h1>
        <p class="page-subtitle">Daftar tagihan yang belum terbayar</p>
    </div>
    <div>
        <button type="button" class="btn btn-success" onclick="window.print()">
            <i class="fas fa-print me-2"></i>Cetak
        </button>
    </div>
</div>

<!-- Summary -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <h6><i class="fas fa-exclamation-circle me-2"></i>Total Piutang</h6>
                <h2 class="mb-0">Rp {{ number_format($total_outstanding, 0, ',', '.') }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h6><i class="fas fa-clock me-2"></i>Belum Jatuh Tempo</h6>
                <h2 class="mb-0">Rp {{ number_format($not_due_amount, 0, ',', '.') }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-orange text-white" style="background-color: #F97316;">
            <div class="card-body">
                <h6><i class="fas fa-hourglass-half me-2"></i>Terlambat 1-30 Hari</h6>
                <h2 class="mb-0">Rp {{ number_format($overdue_1_30, 0, ',', '.') }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card" style="background-color: #DC2626; color: white;">
            <div class="card-body">
                <h6><i class="fas fa-calendar-times me-2"></i>Terlambat > 30 Hari</h6>
                <h2 class="mb-0">Rp {{ number_format($overdue_30_plus, 0, ',', '.') }}</h2>
            </div>
        </div>
    </div>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-2">
                <label class="form-label">Kelas</label>
                <select name="class" class="form-select">
                    <option value="">Semua Kelas</option>
                    @foreach($classes as $class)
                    <option value="{{ $class }}" {{ request('class') == $class ? 'selected' : '' }}>{{ $class }}</option>
                    @endforeach
                </select>
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
            <div class="col-md-2">
                <label class="form-label">Status Keterlambatan</label>
                <select name="overdue_status" class="form-select">
                    <option value="">Semua</option>
                    <option value="not_due" {{ request('overdue_status') == 'not_due' ? 'selected' : '' }}>Belum Jatuh Tempo</option>
                    <option value="1-30" {{ request('overdue_status') == '1-30' ? 'selected' : '' }}>1-30 Hari</option>
                    <option value="31-60" {{ request('overdue_status') == '31-60' ? 'selected' : '' }}>31-60 Hari</option>
                    <option value="60+" {{ request('overdue_status') == '60+' ? 'selected' : '' }}>> 60 Hari</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Urutkan</label>
                <select name="sort" class="form-select">
                    <option value="amount_desc" {{ request('sort', 'amount_desc') == 'amount_desc' ? 'selected' : '' }}>Nominal (Besar-Kecil)</option>
                    <option value="amount_asc" {{ request('sort') == 'amount_asc' ? 'selected' : '' }}>Nominal (Kecil-Besar)</option>
                    <option value="days_desc" {{ request('sort') == 'days_desc' ? 'selected' : '' }}>Keterlambatan (Terlama)</option>
                    <option value="due_date" {{ request('sort') == 'due_date' ? 'selected' : '' }}>Jatuh Tempo</option>
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

<!-- Outstanding by Class Chart -->
<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <i class="fas fa-chart-bar me-2"></i>Piutang per Kelas
            </div>
            <div class="card-body">
                <canvas id="outstandingByClassChart" height="250"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <i class="fas fa-chart-pie me-2"></i>Aging Analysis
            </div>
            <div class="card-body">
                <canvas id="agingChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Outstanding List -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-list me-2"></i>Daftar Piutang</span>
        <span class="badge bg-danger">{{ $bills->total() }} tagihan</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="outstandingTable">
                <thead>
                    <tr>
                        <th>Siswa</th>
                        <th>Kelas</th>
                        <th>Jenis Tagihan</th>
                        <th class="text-end">Sisa Tagihan</th>
                        <th>Jatuh Tempo</th>
                        <th>Keterlambatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bills as $bill)
                    @php
                        $remaining = $bill->total_amount - $bill->paid_amount;
                        $daysOverdue = $bill->due_date->isPast() ? $bill->due_date->diffInDays(now()) : 0;
                    @endphp
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="user-avatar me-2" style="width: 32px; height: 32px; font-size: 0.75rem;">
                                    {{ substr($bill->student->name, 0, 1) }}
                                </div>
                                <div>
                                    <strong>{{ $bill->student->name }}</strong>
                                    <small class="d-block text-muted">{{ $bill->student->nis }}</small>
                                </div>
                            </div>
                        </td>
                        <td>{{ $bill->student->class }}</td>
                        <td>
                            {{ $bill->billType->name }}
                            @if($bill->month)
                            <small class="d-block text-muted">{{ $bill->month }}</small>
                            @endif
                        </td>
                        <td class="text-end">
                            <strong class="text-danger">Rp {{ number_format($remaining, 0, ',', '.') }}</strong>
                        </td>
                        <td>{{ $bill->due_date->format('d M Y') }}</td>
                        <td>
                            @if($daysOverdue == 0)
                            <span class="badge bg-secondary">Belum jatuh tempo</span>
                            @elseif($daysOverdue <= 30)
                            <span class="badge bg-warning">{{ $daysOverdue }} hari</span>
                            @elseif($daysOverdue <= 60)
                            <span class="badge bg-orange text-white" style="background-color: #F97316;">{{ $daysOverdue }} hari</span>
                            @else
                            <span class="badge bg-danger">{{ $daysOverdue }} hari</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="empty-state">
                                <i class="fas fa-check-circle text-success"></i>
                                <h5>Tidak ada piutang</h5>
                                <p class="text-muted">Semua tagihan sudah lunas.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($bills->hasPages())
    <div class="card-footer">
        {{ $bills->links() }}
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
// Outstanding by Class Chart
const classCtx = document.getElementById('outstandingByClassChart').getContext('2d');
new Chart(classCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($by_class->pluck('class')) !!},
        datasets: [{
            label: 'Piutang',
            data: {!! json_encode($by_class->pluck('amount')) !!},
            backgroundColor: '#EF4444',
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

// Aging Chart
const agingCtx = document.getElementById('agingChart').getContext('2d');
new Chart(agingCtx, {
    type: 'doughnut',
    data: {
        labels: ['Belum Jatuh Tempo', '1-30 Hari', '31-60 Hari', '> 60 Hari'],
        datasets: [{
            data: [{{ $not_due_amount }}, {{ $overdue_1_30 }}, {{ $overdue_31_60 ?? 0 }}, {{ $overdue_60_plus ?? 0 }}],
            backgroundColor: ['#6B7280', '#F59E0B', '#F97316', '#DC2626'],
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
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                    }
                }
            }
        }
    }
});

// DataTables initialization
if ($('#outstandingTable tbody tr').length > 0 && !$('#outstandingTable tbody tr td[colspan]').length) {
    $('#outstandingTable').DataTable({
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
