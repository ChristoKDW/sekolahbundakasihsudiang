@extends('layouts.app')

@section('title', 'Laporan Piutang')
@section('page-title', 'Laporan Piutang')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0"><i class="fas fa-file-invoice-dollar me-2 text-warning"></i>Laporan Piutang</h5>
            <div class="d-flex gap-2">
                <a href="{{ route('treasurer.reports.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Kembali
                </a>
                <a href="{{ route('treasurer.reports.export-pdf') }}?type=receivables" class="btn btn-danger btn-sm">
                    <i class="fas fa-file-pdf me-1"></i>Export PDF
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <!-- Filter -->
        <form action="" method="GET" class="mb-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Kelas</label>
                    <select name="class" class="form-select">
                        <option value="">Semua Kelas</option>
                        @foreach($classes ?? [] as $class)
                            <option value="{{ $class }}" {{ request('class') == $class ? 'selected' : '' }}>{{ $class }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Jenis Tagihan</label>
                    <select name="bill_type" class="form-select">
                        <option value="">Semua Jenis</option>
                        @foreach($billTypes ?? [] as $type)
                            <option value="{{ $type->id }}" {{ request('bill_type') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                </div>
            </div>
        </form>
        
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="receivablesTable">
                <thead class="table-light">
                    <tr>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Jenis Tagihan</th>
                        <th>Periode</th>
                        <th>Jatuh Tempo</th>
                        <th class="text-end">Sisa Tagihan</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bills ?? [] as $bill)
                    <tr>
                        <td>{{ $bill->student->nis }}</td>
                        <td>{{ $bill->student->name }}</td>
                        <td>{{ $bill->student->class }}</td>
                        <td>{{ $bill->billType->name }}</td>
                        <td>{{ $bill->month }}/{{ $bill->year }}</td>
                        <td>{{ $bill->due_date->format('d/m/Y') }}</td>
                        <td class="text-end"><strong class="text-danger">Rp {{ number_format($bill->amount - $bill->paid_amount, 0, ',', '.') }}</strong></td>
                        <td>
                            @if($bill->status == 'overdue')
                                <span class="badge bg-danger">Terlambat</span>
                            @elseif($bill->status == 'partial')
                                <span class="badge bg-warning">Sebagian</span>
                            @else
                                <span class="badge bg-secondary">Belum Bayar</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">Tidak ada piutang</td>
                    </tr>
                    @endforelse
                </tbody>
                @if(isset($bills) && $bills->count() > 0)
                <tfoot class="table-light">
                    <tr>
                        <th colspan="6">Total Piutang</th>
                        <th class="text-end text-danger">Rp {{ number_format($bills->sum(fn($b) => $b->amount - $b->paid_amount), 0, ',', '.') }}</th>
                        <th></th>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
        
        @if(isset($bills) && method_exists($bills, 'links'))
        <div class="d-flex justify-content-center mt-3">
            {{ $bills->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#receivablesTable').DataTable({
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
});
</script>
@endpush
