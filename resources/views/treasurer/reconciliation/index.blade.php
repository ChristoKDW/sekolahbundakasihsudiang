@extends('layouts.app')

@section('title', 'Rekonsiliasi Pembayaran')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1 class="page-title">Rekonsiliasi Pembayaran</h1>
        <p class="page-subtitle">Pencocokan data pembayaran dengan laporan Midtrans</p>
    </div>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newReconciliationModal">
        <i class="fas fa-plus me-2"></i>Rekonsiliasi Baru
    </button>
</div>

<!-- Stats -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-sync-alt"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ number_format($stats['total']) }}</div>
                <div class="stat-label">Total Rekonsiliasi</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ number_format($stats['completed']) }}</div>
                <div class="stat-label">Selesai</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon warning">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ number_format($stats['with_issues']) }}</div>
                <div class="stat-label">Ada Masalah</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon info">
                <i class="fas fa-percentage"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $stats['match_rate'] }}%</div>
                <div class="stat-label">Tingkat Kecocokan</div>
            </div>
        </div>
    </div>
</div>

<!-- Reconciliation List -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-history me-2"></i>Riwayat Rekonsiliasi
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="reconciliationTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Periode</th>
                        <th>Total Transaksi</th>
                        <th>Cocok</th>
                        <th>Tidak Cocok</th>
                        <th>Status</th>
                        <th>Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reconciliations as $recon)
                    <tr>
                        <td><code>REC-{{ str_pad($recon->id, 5, '0', STR_PAD_LEFT) }}</code></td>
                        <td>
                            {{ $recon->start_date->format('d M') }} - {{ $recon->end_date->format('d M Y') }}
                        </td>
                        <td>{{ number_format($recon->total_transactions) }}</td>
                        <td class="text-success">
                            <i class="fas fa-check me-1"></i>{{ number_format($recon->matched_count) }}
                        </td>
                        <td class="text-danger">
                            <i class="fas fa-times me-1"></i>{{ number_format($recon->unmatched_count) }}
                        </td>
                        <td>
                            @if($recon->status == 'completed')
                            <span class="badge bg-success">Selesai</span>
                            @elseif($recon->status == 'processing')
                            <span class="badge bg-warning">Proses</span>
                            @else
                            <span class="badge bg-info">Menunggu Review</span>
                            @endif
                        </td>
                        <td>{{ $recon->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <a href="{{ route('treasurer.reconciliation.show', $recon) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="empty-state">
                                <i class="fas fa-balance-scale"></i>
                                <h5>Belum ada rekonsiliasi</h5>
                                <p class="text-muted">Buat rekonsiliasi baru untuk memulai pencocokan data.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- How it Works -->
<div class="card mt-4">
    <div class="card-header">
        <i class="fas fa-info-circle me-2"></i>Cara Kerja Algoritma Reconciliation Matching
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3 text-center mb-3">
                <div class="p-3 bg-primary bg-opacity-10 rounded-3">
                    <i class="fas fa-bullseye fa-2x text-primary mb-2"></i>
                    <h6>Level 1: Exact Match</h6>
                    <small class="text-muted">Pencocokan Order ID & Jumlah yang persis sama</small>
                </div>
            </div>
            <div class="col-md-3 text-center mb-3">
                <div class="p-3 bg-success bg-opacity-10 rounded-3">
                    <i class="fas fa-fingerprint fa-2x text-success mb-2"></i>
                    <h6>Level 2: ID Match</h6>
                    <small class="text-muted">Pencocokan berdasarkan Order ID saja</small>
                </div>
            </div>
            <div class="col-md-3 text-center mb-3">
                <div class="p-3 bg-warning bg-opacity-10 rounded-3">
                    <i class="fas fa-search fa-2x text-warning mb-2"></i>
                    <h6>Level 3: Transaction ID</h6>
                    <small class="text-muted">Pencocokan menggunakan Transaction ID Midtrans</small>
                </div>
            </div>
            <div class="col-md-3 text-center mb-3">
                <div class="p-3 bg-danger bg-opacity-10 rounded-3">
                    <i class="fas fa-clone fa-2x text-danger mb-2"></i>
                    <h6>Level 4: Duplicate Check</h6>
                    <small class="text-muted">Deteksi pembayaran duplikat</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- New Reconciliation Modal -->
<div class="modal fade" id="newReconciliationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('treasurer.reconciliation.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Rekonsiliasi Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" class="form-control" required value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Akhir <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" class="form-control" required value="{{ now()->format('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sumber Data</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="source" value="api" id="sourceApi" checked>
                            <label class="form-check-label" for="sourceApi">
                                Ambil dari Midtrans API (Otomatis)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="source" value="file" id="sourceFile">
                            <label class="form-check-label" for="sourceFile">
                                Upload File Laporan
                            </label>
                        </div>
                    </div>
                    <div class="mb-3 file-upload" style="display: none;">
                        <label class="form-label">File Laporan Midtrans</label>
                        <input type="file" name="report_file" class="form-control" accept=".csv,.xlsx">
                        <small class="text-muted">Format: CSV atau Excel dari dashboard Midtrans</small>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Sistem akan mencocokkan data pembayaran di database dengan laporan dari Midtrans menggunakan algoritma matching 4 level.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-play me-2"></i>Mulai Rekonsiliasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    if ($('#reconciliationTable tbody tr td').length > 1 || !$('#reconciliationTable tbody tr td[colspan]').length) {
        $('#reconciliationTable').DataTable({
            paging: true,
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]],
            info: true,
            searching: true,
            ordering: true,
            order: [[0, 'desc']],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
            }
        });
    }
});

document.querySelectorAll('input[name="source"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.querySelector('.file-upload').style.display = this.value === 'file' ? 'block' : 'none';
    });
});
</script>
@endpush
