@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="page-header">
    <h1 class="page-title">Dashboard Administrator</h1>
    <p class="page-subtitle">Selamat datang, {{ auth()->user()->name }}! Berikut ringkasan sistem.</p>
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
                <div class="stat-label">Total Siswa Aktif</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon success">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ number_format($total_users) }}</div>
                <div class="stat-label">Total Pengguna</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon warning">
                <i class="fas fa-file-invoice"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ number_format($total_bills) }}</div>
                <div class="stat-label">Tagihan Belum Lunas</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon info">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">Rp {{ number_format($total_payments_today, 0, ',', '.') }}</div>
                <div class="stat-label">Pembayaran Hari Ini</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Users by Role -->
    <div class="col-xl-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-chart-pie me-2"></i>Pengguna per Role</span>
            </div>
            <div class="card-body">
                <canvas id="usersByRoleChart" height="250"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="col-xl-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-history me-2"></i>Aktivitas Terbaru</span>
                <a href="#" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>User</th>
                                <th>Aksi</th>
                                <th>Deskripsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recent_activities as $activity)
                            <tr>
                                <td>
                                    <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar me-2" style="width: 32px; height: 32px; font-size: 0.75rem;">
                                            {{ substr($activity->user?->name ?? 'S', 0, 1) }}
                                        </div>
                                        <span>{{ $activity->user?->name ?? 'System' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $activity->action === 'create' ? 'success' : ($activity->action === 'delete' ? 'danger' : 'info') }}">
                                        {{ ucfirst($activity->action) }}
                                    </span>
                                </td>
                                <td>{{ Str::limit($activity->description, 50) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    Belum ada aktivitas
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
                        <a href="{{ route('admin.students.create') }}" class="btn btn-outline-primary w-100 py-3">
                            <i class="fas fa-user-plus fa-2x mb-2 d-block"></i>
                            Tambah Siswa
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <a href="{{ route('admin.users.create') }}" class="btn btn-outline-success w-100 py-3">
                            <i class="fas fa-user-cog fa-2x mb-2 d-block"></i>
                            Tambah User
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <a href="{{ route('treasurer.bills.create') }}" class="btn btn-outline-warning w-100 py-3">
                            <i class="fas fa-file-invoice-dollar fa-2x mb-2 d-block"></i>
                            Buat Tagihan
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <a href="{{ route('treasurer.reports.index') }}" class="btn btn-outline-info w-100 py-3">
                            <i class="fas fa-chart-bar fa-2x mb-2 d-block"></i>
                            Lihat Laporan
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
    // Users by Role Chart
    const ctx = document.getElementById('usersByRoleChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($users_by_role->pluck('display_name')) !!},
            datasets: [{
                data: {!! json_encode($users_by_role->pluck('total')) !!},
                backgroundColor: [
                    '#4F46E5',
                    '#10B981',
                    '#F59E0B',
                    '#EF4444',
                    '#8B5CF6'
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
                        padding: 20,
                        usePointStyle: true
                    }
                }
            }
        }
    });
</script>
@endpush
