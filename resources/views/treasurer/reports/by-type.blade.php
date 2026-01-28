@extends('layouts.app')

@section('title', 'Laporan per Jenis')
@section('page-title', 'Laporan per Jenis Tagihan')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0"><i class="fas fa-tags me-2 text-secondary"></i>Laporan per Jenis Tagihan</h5>
            <div class="d-flex gap-2">
                <a href="{{ route('treasurer.reports.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Kembali
                </a>
                <a href="{{ route('treasurer.reports.export-pdf') }}?type=by-type" class="btn btn-danger btn-sm">
                    <i class="fas fa-file-pdf me-1"></i>Export PDF
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <canvas id="typeChart" height="300"></canvas>
            </div>
            <div class="col-md-6">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="typeReportTable">
                        <thead class="table-light">
                            <tr>
                                <th>Jenis Tagihan</th>
                                <th class="text-end">Total</th>
                                <th class="text-end">Terbayar</th>
                                <th>%</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($typeData ?? [] as $type)
                            <tr>
                                <td>
                                    <span class="rounded-circle d-inline-block me-2" style="width: 12px; height: 12px; background: {{ $type['color'] ?? '#6c757d' }}"></span>
                                    {{ $type['name'] }}
                                </td>
                                <td class="text-end">Rp {{ number_format($type['total'] ?? 0, 0, ',', '.') }}</td>
                                <td class="text-end text-success">Rp {{ number_format($type['paid'] ?? 0, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge bg-{{ ($type['percentage'] ?? 0) >= 80 ? 'success' : (($type['percentage'] ?? 0) >= 50 ? 'warning' : 'danger') }}">
                                        {{ number_format($type['percentage'] ?? 0, 1) }}%
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">Tidak ada data</td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th>Total</th>
                                <th class="text-end">Rp {{ number_format(collect($typeData ?? [])->sum('total'), 0, ',', '.') }}</th>
                                <th class="text-end">Rp {{ number_format(collect($typeData ?? [])->sum('paid'), 0, ',', '.') }}</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('typeChart').getContext('2d');
new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode(collect($typeData ?? [])->pluck('name')->toArray()) !!},
        datasets: [{
            data: {!! json_encode(collect($typeData ?? [])->pluck('total')->toArray()) !!},
            backgroundColor: {!! json_encode(collect($typeData ?? [])->pluck('color')->toArray()) !!}
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// DataTables initialization
if ($('#typeReportTable tbody tr').length > 0 && !$('#typeReportTable tbody tr td').hasClass('text-center')) {
    $('#typeReportTable').DataTable({
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
