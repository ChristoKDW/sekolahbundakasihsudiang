@extends('layouts.app')

@section('title', 'Detail Rekonsiliasi')
@section('page-title', 'Detail Hasil Rekonsiliasi')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <a href="{{ route('treasurer.reconciliation.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-calendar"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $reconciliation->reconciliation_date->format('d M Y') }}</div>
                <div class="stat-label">Tanggal Rekonsiliasi</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $reconciliation->matched_count }}</div>
                <div class="stat-label">Transaksi Cocok</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon warning">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $reconciliation->unmatched_count }}</div>
                <div class="stat-label">Tidak Cocok</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon info">
                <i class="fas fa-percentage"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ number_format($reconciliation->match_rate, 1) }}%</div>
                <div class="stat-label">Tingkat Kecocokan</div>
            </div>
        </div>
    </div>
</div>

<!-- Reconciliation Info -->
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-info-circle me-2"></i>Informasi Rekonsiliasi
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <table class="table table-borderless">
                    <tr>
                        <td class="text-muted">ID Rekonsiliasi</td>
                        <td><strong>#{{ $reconciliation->id }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Diproses Oleh</td>
                        <td><strong>{{ $reconciliation->processedBy->name ?? '-' }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tanggal Proses</td>
                        <td><strong>{{ $reconciliation->created_at->format('d M Y H:i') }}</strong></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-4">
                <table class="table table-borderless">
                    <tr>
                        <td class="text-muted">Total Transaksi</td>
                        <td><strong>{{ $reconciliation->total_transactions }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Transaksi Cocok</td>
                        <td><strong class="text-success">{{ $reconciliation->matched_transactions }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Transaksi Tidak Cocok</td>
                        <td><strong class="text-warning">{{ $reconciliation->unmatched_transactions }}</strong></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-4">
                <table class="table table-borderless">
                    <tr>
                        <td class="text-muted">Total Nominal</td>
                        <td><strong>Rp {{ number_format($reconciliation->total_amount, 0, ',', '.') }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Nominal Cocok</td>
                        <td><strong class="text-success">Rp {{ number_format($reconciliation->matched_amount, 0, ',', '.') }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Nominal Tidak Cocok</td>
                        <td><strong class="text-warning">Rp {{ number_format($reconciliation->unmatched_amount, 0, ',', '.') }}</strong></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Match Results Tabs -->
<div class="card">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#matched">
                    <i class="fas fa-check-circle text-success me-1"></i>
                    Cocok ({{ $matchedResults->count() }})
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#unmatched">
                    <i class="fas fa-exclamation-circle text-warning me-1"></i>
                    Tidak Cocok ({{ $unmatchedResults->count() }})
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#duplicates">
                    <i class="fas fa-copy text-danger me-1"></i>
                    Duplikat ({{ $duplicates->count() }})
                </a>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content">
            <!-- Matched Tab -->
            <div class="tab-pane fade show active" id="matched">
                <div class="table-responsive">
                    <table class="table table-hover" id="matchedTable">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Siswa</th>
                                <th>Tagihan</th>
                                <th>Nominal Sistem</th>
                                <th>Nominal Midtrans</th>
                                <th>Level Match</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($matchedResults as $result)
                            <tr>
                                <td><code>{{ $result->order_id }}</code></td>
                                <td>{{ $result->payment->bill->student->name ?? '-' }}</td>
                                <td>{{ $result->payment->bill->billType->name ?? '-' }}</td>
                                <td>Rp {{ number_format($result->system_amount, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($result->midtrans_amount, 0, ',', '.') }}</td>
                                <td>
                                    @switch($result->match_level)
                                        @case(1)
                                            <span class="badge bg-success">Level 1 - Exact</span>
                                            @break
                                        @case(2)
                                            <span class="badge bg-info">Level 2 - Order ID</span>
                                            @break
                                        @case(3)
                                            <span class="badge bg-warning">Level 3 - Trans ID</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">Unknown</span>
                                    @endswitch
                                </td>
                                <td><span class="badge bg-success">Cocok</span></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="fas fa-check-circle fa-2x mb-2"></i>
                                    <p>Tidak ada data transaksi yang cocok</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Unmatched Tab -->
            <div class="tab-pane fade" id="unmatched">
                <div class="table-responsive">
                    <table class="table table-hover" id="unmatchedTable">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Sumber</th>
                                <th>Nominal</th>
                                <th>Tanggal</th>
                                <th>Alasan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($unmatchedResults as $result)
                            <tr>
                                <td><code>{{ $result->order_id }}</code></td>
                                <td>
                                    @if($result->source === 'system')
                                    <span class="badge bg-primary">Sistem</span>
                                    @else
                                    <span class="badge bg-info">Midtrans</span>
                                    @endif
                                </td>
                                <td>Rp {{ number_format($result->amount, 0, ',', '.') }}</td>
                                <td>{{ $result->transaction_date->format('d M Y H:i') }}</td>
                                <td><span class="text-warning">{{ $result->reason ?? 'Tidak ditemukan pasangan' }}</span></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                            onclick="investigateTransaction('{{ $result->order_id }}')">
                                        <i class="fas fa-search"></i> Investigasi
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="fas fa-thumbs-up fa-2x mb-2"></i>
                                    <p>Semua transaksi cocok!</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Duplicates Tab -->
            <div class="tab-pane fade" id="duplicates">
                <div class="table-responsive">
                    <table class="table table-hover" id="duplicatesTable">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Jumlah Duplikat</th>
                                <th>Total Nominal</th>
                                <th>Tanggal Pertama</th>
                                <th>Tanggal Terakhir</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($duplicates as $dup)
                            <tr class="table-danger">
                                <td><code>{{ $dup->order_id }}</code></td>
                                <td><strong class="text-danger">{{ $dup->count }} kali</strong></td>
                                <td>Rp {{ number_format($dup->total_amount, 0, ',', '.') }}</td>
                                <td>{{ $dup->first_date }}</td>
                                <td>{{ $dup->last_date }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-danger" 
                                            onclick="reviewDuplicate('{{ $dup->order_id }}')">
                                        <i class="fas fa-exclamation-triangle"></i> Review
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="fas fa-smile fa-2x mb-2"></i>
                                    <p>Tidak ada transaksi duplikat</p>
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

<!-- Notes Section -->
@if($reconciliation->notes)
<div class="card mt-4">
    <div class="card-header">
        <i class="fas fa-sticky-note me-2"></i>Catatan
    </div>
    <div class="card-body">
        <p class="mb-0">{{ $reconciliation->notes }}</p>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#matchedTable, #unmatchedTable, #duplicatesTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        },
        pageLength: 25
    });
});

function investigateTransaction(orderId) {
    Swal.fire({
        title: 'Investigasi Transaksi',
        text: 'Membuka detail transaksi: ' + orderId,
        icon: 'info',
        confirmButtonText: 'Lihat Detail'
    });
}

function reviewDuplicate(orderId) {
    Swal.fire({
        title: 'Review Duplikat',
        html: `
            <p>Order ID: <strong>${orderId}</strong></p>
            <p>Transaksi ini terdeteksi duplikat. Tindakan yang disarankan:</p>
            <ol class="text-start">
                <li>Verifikasi status pembayaran di Midtrans Dashboard</li>
                <li>Cek apakah ada refund yang perlu diproses</li>
                <li>Hubungi tim support jika diperlukan</li>
            </ol>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Proses Refund',
        cancelButtonText: 'Tutup',
        confirmButtonColor: '#DC2626'
    });
}
</script>
@endpush
