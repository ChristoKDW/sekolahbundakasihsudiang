@extends('layouts.app')

@section('title', 'Dashboard Orang Tua')

@section('content')
<div class="page-header">
    <h1 class="page-title">Dashboard Orang Tua</h1>
    <p class="page-subtitle">Selamat datang, {{ auth()->user()->name }}!</p>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-xl-4 col-md-6">
        <div class="stat-card">
            <div class="stat-icon danger">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">Rp {{ number_format($total_unpaid, 0, ',', '.') }}</div>
                <div class="stat-label">Total Tagihan Belum Lunas</div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6">
        <div class="stat-card">
            <div class="stat-icon warning">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ count($unpaid_bills) }}</div>
                <div class="stat-label">Jumlah Tagihan Pending</div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6">
        <div class="stat-card">
            <div class="stat-icon success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">Rp {{ number_format($total_paid_this_month, 0, ',', '.') }}</div>
                <div class="stat-label">Pembayaran Bulan Ini</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Unpaid Bills -->
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-file-invoice-dollar me-2"></i>Tagihan Belum Lunas</span>
                <a href="{{ route('parent.payments.index') }}" class="btn btn-sm btn-primary">
                    Lihat Semua
                </a>
            </div>
            <div class="card-body p-0">
                @if($unpaid_bills->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Siswa</th>
                                <th>Jenis Tagihan</th>
                                <th>Total</th>
                                <th>Jatuh Tempo</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($unpaid_bills as $bill)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar me-2" style="width: 36px; height: 36px; font-size: 0.8rem;">
                                            {{ substr($bill->student->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <strong>{{ $bill->student->name }}</strong>
                                            <small class="d-block text-muted">{{ $bill->student->class }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    {{ $bill->billType->name }}
                                    @if($bill->month)
                                    <small class="d-block text-muted">{{ $bill->month }}</small>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $bill->formatted_remaining }}</strong>
                                    @if($bill->paid_amount > 0)
                                    <small class="d-block text-success">Terbayar: {{ $bill->formatted_paid }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="{{ $bill->isOverdue() ? 'text-danger' : '' }}">
                                        {{ $bill->due_date->format('d M Y') }}
                                    </span>
                                    @if($bill->isOverdue())
                                    <small class="d-block text-danger">
                                        <i class="fas fa-exclamation-triangle"></i> Terlambat
                                    </small>
                                    @endif
                                </td>
                                <td>{!! $bill->status_badge !!}</td>
                                <td>
                                    <a href="{{ route('parent.payments.checkout', $bill) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-credit-card"></i> Bayar
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="empty-state">
                    <i class="fas fa-check-circle text-success"></i>
                    <h5>Tidak ada tagihan</h5>
                    <p>Semua tagihan sudah lunas. Terima kasih!</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Students List -->
    <div class="col-xl-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="fas fa-child me-2"></i>Data Anak Saya
            </div>
            <div class="card-body">
                @forelse($students as $student)
                <div class="d-flex align-items-center p-3 mb-3 bg-light rounded-3">
                    <div class="user-avatar me-3" style="width: 50px; height: 50px;">
                        {{ substr($student->name, 0, 1) }}
                    </div>
                    <div class="flex-grow-1">
                        <strong>{{ $student->name }}</strong>
                        <small class="d-block text-muted">
                            NIS: {{ $student->nis }} | {{ $student->class }}
                        </small>
                    </div>
                    <a href="{{ route('parent.students.show', $student) }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-eye"></i>
                    </a>
                </div>
                @empty
                <div class="empty-state">
                    <i class="fas fa-user-slash"></i>
                    <h6>Belum ada data anak</h6>
                    <p class="small">Hubungi admin untuk menghubungkan data siswa.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Recent Payments -->
<div class="row g-4 mt-2">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-history me-2"></i>Riwayat Pembayaran Terakhir</span>
                <a href="{{ route('parent.payments.history') }}" class="btn btn-sm btn-outline-primary">
                    Lihat Semua
                </a>
            </div>
            <div class="card-body p-0">
                @if($recent_payments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>No. Transaksi</th>
                                <th>Tagihan</th>
                                <th>Jumlah</th>
                                <th>Metode</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recent_payments as $payment)
                            <tr>
                                <td>{{ $payment->created_at->format('d M Y H:i') }}</td>
                                <td><code>{{ $payment->order_id }}</code></td>
                                <td>
                                    {{ $payment->bill->billType->name }}
                                    <small class="d-block text-muted">{{ $payment->bill->student->name }}</small>
                                </td>
                                <td><strong>{{ $payment->formatted_amount }}</strong></td>
                                <td>{{ $payment->payment_method_label }}</td>
                                <td>{!! $payment->status_badge !!}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="empty-state">
                    <i class="fas fa-receipt"></i>
                    <h6>Belum ada riwayat pembayaran</h6>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
