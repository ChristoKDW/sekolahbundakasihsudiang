@extends('layouts.app')

@section('title', 'Dashboard Bendahara')

@section('content')
<div class="page-header">
    <h1 class="page-title">Dashboard Bendahara</h1>
    <p class="page-subtitle">Selamat datang, {{ auth()->user()->name }}! Ringkasan keuangan hari ini.</p>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon success">
                <i class="fas fa-coins"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">Rp {{ number_format($today_income, 0, ',', '.') }}</div>
                <div class="stat-label">Pemasukan Hari Ini</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">Rp {{ number_format($monthly_income, 0, ',', '.') }}</div>
                <div class="stat-label">Pemasukan Bulan Ini</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon warning">
                <i class="fas fa-hourglass-half"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ number_format($pending_bills) }}</div>
                <div class="stat-label">Tagihan Pending</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon danger">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ number_format($overdue_bills) }}</div>
                <div class="stat-label">Tagihan Terlambat</div>
            </div>
        </div>
    </div>
</div>

<!-- Summary & Receivables -->
<div class="row g-4 mb-4">
    <div class="col-xl-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="fas fa-chart-pie me-2"></i>Ringkasan Tagihan
            </div>
            <div class="card-body">
                <canvas id="billStatusChart" height="220"></canvas>
                <div class="mt-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span><i class="fas fa-circle text-success me-2"></i>Lunas</span>
                        <strong>{{ number_format($paid_bills) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span><i class="fas fa-circle text-warning me-2"></i>Pending</span>
                        <strong>{{ number_format($pending_bills) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span><i class="fas fa-circle text-danger me-2"></i>Terlambat</span>
                        <strong>{{ number_format($overdue_bills) }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-exclamation-circle me-2"></i>Tagihan Terlambat</span>
                <a href="{{ route('treasurer.bills.index', ['status' => 'overdue']) }}" class="btn btn-sm btn-outline-danger">
                    Lihat Semua
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Siswa</th>
                                <th>Tagihan</th>
                                <th>Sisa</th>
                                <th>Jatuh Tempo</th>
                                <th>Keterlambatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($overdue_list as $bill)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar me-2" style="width: 32px; height: 32px; font-size: 0.75rem;">
                                            {{ substr($bill->student->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <strong>{{ $bill->student->name }}</strong>
                                            <small class="d-block text-muted">{{ $bill->student->class }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $bill->billType->name }}</td>
                                <td class="text-danger">
                                    <strong>{{ $bill->formatted_remaining }}</strong>
                                </td>
                                <td>{{ $bill->due_date->format('d M Y') }}</td>
                                <td>
                                    <span class="badge bg-danger">
                                        {{ $bill->due_date->diffInDays(now()) }} hari
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="fas fa-check-circle text-success fa-2x mb-2 d-block"></i>
                                    Tidak ada tagihan terlambat
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Payments -->
<div class="row g-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-history me-2"></i>Pembayaran Terbaru</span>
                <a href="{{ route('treasurer.reports.payments') }}" class="btn btn-sm btn-outline-primary">
                    Lihat Semua
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>No. Transaksi</th>
                                <th>Siswa</th>
                                <th>Tagihan</th>
                                <th>Jumlah</th>
                                <th>Metode</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recent_payments as $payment)
                            <tr>
                                <td>{{ $payment->paid_at?->format('d/m/Y H:i') ?? '-' }}</td>
                                <td><code>{{ $payment->order_id }}</code></td>
                                <td>{{ $payment->bill->student->name }}</td>
                                <td>{{ $payment->bill->billType->name }}</td>
                                <td><strong class="text-success">{{ $payment->formatted_amount }}</strong></td>
                                <td>{{ $payment->payment_method_label }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    Belum ada pembayaran hari ini
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row g-4 mt-2">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-bolt me-2"></i>Aksi Cepat
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3 col-sm-6">
                        <a href="{{ route('treasurer.bills.create') }}" class="btn btn-outline-primary w-100 py-3">
                            <i class="fas fa-plus-circle fa-2x mb-2 d-block"></i>
                            Buat Tagihan
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <button type="button" class="btn btn-outline-success w-100 py-3" data-bs-toggle="modal" data-bs-target="#bulkBillModal">
                            <i class="fas fa-layer-group fa-2x mb-2 d-block"></i>
                            Tagihan Massal
                        </button>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <a href="{{ route('treasurer.reconciliation.create') }}" class="btn btn-outline-info w-100 py-3">
                            <i class="fas fa-balance-scale fa-2x mb-2 d-block"></i>
                            Rekonsiliasi
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <a href="{{ route('treasurer.reports.index') }}" class="btn btn-outline-warning w-100 py-3">
                            <i class="fas fa-file-pdf fa-2x mb-2 d-block"></i>
                            Export Laporan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Bill Status Chart
    const ctx = document.getElementById('billStatusChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Lunas', 'Pending', 'Terlambat'],
            datasets: [{
                data: [{{ $paid_bills }}, {{ $pending_bills }}, {{ $overdue_bills }}],
                backgroundColor: ['#10B981', '#F59E0B', '#EF4444'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
</script>
@endpush
