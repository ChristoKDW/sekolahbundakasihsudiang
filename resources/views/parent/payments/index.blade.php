@extends('layouts.app')

@section('title', 'Tagihan Saya')

@section('content')
<div class="page-header">
    <h1 class="page-title">Tagihan Saya</h1>
    <p class="page-subtitle">Daftar semua tagihan yang harus dibayar</p>
</div>

<!-- Summary -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <h6><i class="fas fa-exclamation-circle me-2"></i>Total Tagihan</h6>
                <h3 class="mb-0">Rp {{ number_format($total_unpaid, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h6><i class="fas fa-clock me-2"></i>Menunggu Pembayaran</h6>
                <h3 class="mb-0">{{ $pending_count }} tagihan</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h6><i class="fas fa-hourglass-half me-2"></i>Dalam Proses</h6>
                <h3 class="mb-0">{{ $processing_count }} pembayaran</h3>
            </div>
        </div>
    </div>
</div>

<!-- Student Tabs -->
@if($students->count() > 1)
<ul class="nav nav-tabs mb-4" role="tablist">
    @foreach($students as $index => $student)
    <li class="nav-item">
        <a class="nav-link {{ $index == 0 ? 'active' : '' }}" data-bs-toggle="tab" href="#student-{{ $student->id }}">
            {{ $student->name }} <span class="badge bg-danger">{{ $student->unpaid_bills_count }}</span>
        </a>
    </li>
    @endforeach
</ul>
@endif

<div class="tab-content">
    @foreach($students as $index => $student)
    <div class="tab-pane fade {{ $index == 0 ? 'show active' : '' }}" id="student-{{ $student->id }}">
        <!-- Student Info -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div class="user-avatar" style="width: 60px; height: 60px; font-size: 1.5rem;">
                            {{ substr($student->name, 0, 1) }}
                        </div>
                    </div>
                    <div class="col">
                        <h5 class="mb-1">{{ $student->name }}</h5>
                        <p class="text-muted mb-0">NIS: {{ $student->nis }} | Kelas: {{ $student->class }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bills List -->
        @forelse($student->bills->where('status', '!=', 'paid') as $bill)
        <div class="card mb-3 {{ $bill->isOverdue() ? 'border-danger' : '' }}">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <span class="badge bg-{{ $bill->billType->color ?? 'primary' }} p-3 rounded-3">
                                    <i class="fas fa-{{ $bill->billType->icon ?? 'file-invoice' }} fa-2x"></i>
                                </span>
                            </div>
                            <div>
                                <h5 class="mb-1">{{ $bill->billType->name }}</h5>
                                <p class="text-muted mb-0">
                                    @if($bill->month)
                                    {{ $bill->month }} |
                                    @endif
                                    Invoice: <code>{{ $bill->invoice_number }}</code>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 text-md-center">
                        <small class="text-muted d-block">Sisa Tagihan</small>
                        <h4 class="text-danger mb-0">{{ $bill->formatted_remaining }}</h4>
                        @if($bill->paid_amount > 0)
                        <small class="text-success">Terbayar: {{ $bill->formatted_paid }}</small>
                        @endif
                    </div>
                    <div class="col-md-3 text-md-end mt-3 mt-md-0">
                        <div class="mb-2">
                            {!! $bill->status_badge !!}
                            @if($bill->isOverdue())
                            <span class="badge bg-danger">
                                <i class="fas fa-exclamation-triangle"></i> Terlambat {{ $bill->due_date->diffInDays(now()) }} hari
                            </span>
                            @endif
                        </div>
                        <p class="small text-muted mb-2">
                            Jatuh tempo: {{ $bill->due_date->format('d M Y') }}
                        </p>
                        <a href="{{ route('parent.payments.checkout', $bill) }}" class="btn btn-primary">
                            <i class="fas fa-credit-card me-2"></i>Bayar Sekarang
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-check-circle text-success fa-4x mb-3"></i>
                <h4>Tidak Ada Tagihan</h4>
                <p class="text-muted">Semua tagihan untuk {{ $student->name }} sudah lunas.</p>
            </div>
        </div>
        @endforelse
    </div>
    @endforeach
</div>

<!-- Payment History Link -->
<div class="text-center mt-4">
    <a href="{{ route('parent.payments.history') }}" class="btn btn-outline-primary">
        <i class="fas fa-history me-2"></i>Lihat Riwayat Pembayaran
    </a>
</div>
@endsection
