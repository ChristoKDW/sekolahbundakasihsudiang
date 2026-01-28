@extends('layouts.app')

@section('title', 'Kelola Tagihan')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1 class="page-title">Kelola Tagihan</h1>
        <p class="page-subtitle">Daftar semua tagihan siswa</p>
    </div>
    <div>
        <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#bulkBillModal">
            <i class="fas fa-layer-group me-2"></i>Tagihan Massal
        </button>
        <a href="{{ route('treasurer.bills.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Tambah Tagihan
        </a>
    </div>
</div>

<!-- Stats -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-file-invoice"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ number_format($stats['total']) }}</div>
                <div class="stat-label">Total Tagihan</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ number_format($stats['paid']) }}</div>
                <div class="stat-label">Lunas</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon warning">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ number_format($stats['pending']) }}</div>
                <div class="stat-label">Belum Lunas</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon danger">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ number_format($stats['overdue']) }}</div>
                <div class="stat-label">Terlambat</div>
            </div>
        </div>
    </div>
</div>

    </div>
</div>

<!-- Bills Table -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="billsTable">
                <thead>
                    <tr>
                        <th width="50">
                            <input type="checkbox" class="form-check-input" id="selectAll">
                        </th>
                        <th>Invoice</th>
                        <th>Siswa</th>
                        <th>Jenis</th>
                        <th>Total</th>
                        <th>Terbayar</th>
                        <th>Jatuh Tempo</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bills as $bill)
                    <tr>
                        <td>
                            <input type="checkbox" class="form-check-input bill-check" value="{{ $bill->id }}">
                        </td>
                        <td><code>{{ $bill->invoice_number }}</code></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="user-avatar me-2" style="width: 32px; height: 32px; font-size: 0.75rem;">
                                    {{ $bill->student ? substr($bill->student->name, 0, 1) : '?' }}
                                </div>
                                <div>
                                    <strong>{{ $bill->student->name ?? 'Siswa Dihapus' }}</strong>
                                    <small class="d-block text-muted">{{ $bill->student->class ?? '-' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            {{ $bill->billType->name ?? 'Tipe Dihapus' }}
                            @if($bill->month)
                            <small class="d-block text-muted">{{ $bill->month }}</small>
                            @endif
                        </td>
                        <td><strong>{{ $bill->formatted_total }}</strong></td>
                        <td class="text-success">{{ $bill->formatted_paid_amount }}</td>
                        <td>
                            <span class="{{ $bill->isOverdue() ? 'text-danger fw-bold' : '' }}">
                                {{ $bill->due_date->format('d M Y') }}
                            </span>
                        </td>
                        <td>{!! $bill->status_badge !!}</td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('treasurer.bills.show', $bill) }}" class="btn btn-sm btn-outline-info" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('treasurer.bills.edit', $bill) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete({{ $bill->id }})" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <form id="delete-form-{{ $bill->id }}" action="{{ route('treasurer.bills.destroy', $bill) }}" method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <div class="empty-state">
                                <i class="fas fa-file-invoice-dollar"></i>
                                <h5>Tidak ada tagihan</h5>
                                <p class="text-muted">Belum ada data tagihan atau tidak ada yang cocok dengan filter.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Bulk Bill Modal -->
<div class="modal fade" id="bulkBillModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('treasurer.bills.generate-bulk') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-layer-group me-2"></i>Buat Tagihan Massal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis Tagihan <span class="text-danger">*</span></label>
                            <select name="bill_type_id" id="bulkBillType" class="form-select" required>
                                @foreach($billTypes as $type)
                                <option value="{{ $type->id }}" data-amount="{{ $type->default_amount }}" data-flexible="{{ $type->is_flexible ? 1 : 0 }}">{{ $type->name }}{{ $type->is_flexible ? ' (Fleksibel)' : '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3" id="bulkAmountWrapper">
                            <label class="form-label">Total Tagihan <span class="text-danger" id="bulkAmountRequired">*</span></label>
                            <input type="number" name="total_amount" id="bulkAmountInput" class="form-control" required min="0">
                            <small class="text-muted d-none" id="bulkFlexibleHint">Nominal fleksibel akan diisi saat pembayaran</small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kelas Target <span class="text-danger">*</span></label>
                            <select name="classes[]" class="form-select select2" multiple required>
                                @foreach($classes as $class)
                                <option value="{{ $class }}">{{ $class }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jatuh Tempo <span class="text-danger">*</span></label>
                            <input type="date" name="due_date" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Bulan (untuk SPP)</label>
                            <input type="text" name="month" class="form-control" placeholder="Januari 2024">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tahun Ajaran</label>
                            <input type="text" name="academic_year" class="form-control" value="{{ date('Y') }}/{{ date('Y')+1 }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Tagihan akan dibuat untuk semua siswa aktif di kelas yang dipilih.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-layer-group me-2"></i>Buat Tagihan Massal
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
    $('#billsTable').DataTable({
        paging: true,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]],
        info: true,
        searching: true,
        ordering: true,
        columnDefs: [{ orderable: false, targets: [0, 8] }],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        }
    });
});

// Select all checkbox
document.getElementById('selectAll').addEventListener('change', function() {
    document.querySelectorAll('.bill-check').forEach(cb => cb.checked = this.checked);
});

// Toggle bulk amount field based on flexible bill type
function toggleBulkAmountField() {
    const select = document.getElementById('bulkBillType');
    const selected = select.options[select.selectedIndex];
    const isFlexible = selected.dataset.flexible == '1';
    const amount = selected.dataset.amount;
    
    const amountInput = document.getElementById('bulkAmountInput');
    const amountRequired = document.getElementById('bulkAmountRequired');
    const flexibleHint = document.getElementById('bulkFlexibleHint');
    
    if (isFlexible) {
        amountInput.required = false;
        amountInput.value = 0;
        amountInput.style.display = 'none';
        amountRequired.style.display = 'none';
        flexibleHint.classList.remove('d-none');
    } else {
        amountInput.required = true;
        amountInput.style.display = '';
        amountRequired.style.display = '';
        flexibleHint.classList.add('d-none');
        if (amount) {
            amountInput.value = amount;
        }
    }
}

// Auto-fill amount from bill type
document.getElementById('bulkBillType').addEventListener('change', toggleBulkAmountField);

// Apply toggle on modal show
document.getElementById('bulkBillModal').addEventListener('shown.bs.modal', toggleBulkAmountField);

function confirmDelete(id) {
    Swal.fire({
        title: 'Hapus Tagihan?',
        text: 'Tagihan dan data pembayaran terkait akan dihapus!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + id).submit();
        }
    });
}
</script>
@endpush
