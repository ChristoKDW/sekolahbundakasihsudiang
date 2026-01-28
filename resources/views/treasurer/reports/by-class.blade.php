@extends('layouts.app')

@section('title', 'Laporan per Kelas')
@section('page-title', 'Laporan per Kelas')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0"><i class="fas fa-users me-2 text-info"></i>Laporan per Kelas</h5>
            <div class="d-flex gap-2">
                <a href="{{ route('treasurer.reports.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Kembali
                </a>
                <a href="{{ route('treasurer.reports.export-pdf') }}?type=by-class" class="btn btn-danger btn-sm">
                    <i class="fas fa-file-pdf me-1"></i>Export PDF
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            @foreach($classData ?? [] as $class)
            <div class="col-md-4 mb-3">
                <div class="card h-100 border">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="mb-0">{{ $class['name'] }}</h5>
                            <span class="badge bg-primary">{{ $class['students'] }} siswa</span>
                        </div>
                        
                        <div class="mb-2">
                            <small class="text-muted">Collection Rate</small>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-success" style="width: {{ $class['percentage'] ?? 0 }}%"></div>
                            </div>
                            <small class="text-success">{{ number_format($class['percentage'] ?? 0, 1) }}%</small>
                        </div>
                        
                        <div class="row text-center mt-3">
                            <div class="col-6 border-end">
                                <h6 class="text-success mb-0">{{ $class['paid'] ?? 0 }}</h6>
                                <small class="text-muted">Lunas</small>
                            </div>
                            <div class="col-6">
                                <h6 class="text-danger mb-0">{{ $class['unpaid'] ?? 0 }}</h6>
                                <small class="text-muted">Belum</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="classReportTable">
                <thead class="table-light">
                    <tr>
                        <th>Kelas</th>
                        <th class="text-center">Jumlah Siswa</th>
                        <th class="text-end">Total Tagihan</th>
                        <th class="text-end">Terbayar</th>
                        <th class="text-end">Piutang</th>
                        <th>% Lunas</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($classData ?? [] as $class)
                    <tr>
                        <td><strong>{{ $class['name'] }}</strong></td>
                        <td class="text-center">{{ $class['students'] }}</td>
                        <td class="text-end">Rp {{ number_format($class['total'] ?? 0, 0, ',', '.') }}</td>
                        <td class="text-end text-success">Rp {{ number_format($class['paid_amount'] ?? 0, 0, ',', '.') }}</td>
                        <td class="text-end text-danger">Rp {{ number_format($class['unpaid_amount'] ?? 0, 0, ',', '.') }}</td>
                        <td>
                            <div class="progress" style="width: 100px; height: 20px;">
                                <div class="progress-bar bg-success" style="width: {{ $class['percentage'] ?? 0 }}%">
                                    {{ number_format($class['percentage'] ?? 0, 1) }}%
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Tidak ada data</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    if ($('#classReportTable tbody tr').length > 0 && !$('#classReportTable tbody tr td').hasClass('text-center')) {
        $('#classReportTable').DataTable({
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
