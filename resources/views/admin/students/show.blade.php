@extends('layouts.app')

@section('title', 'Detail Siswa - ' . $student->name)

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.students.index') }}">Siswa</a></li>
                <li class="breadcrumb-item active">{{ $student->name }}</li>
            </ol>
        </nav>
        <h1 class="page-title">Detail Siswa</h1>
    </div>
    <div>
        <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-primary">
            <i class="fas fa-edit me-2"></i>Edit
        </a>
        <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
            <i class="fas fa-trash me-2"></i>Hapus
        </button>
    </div>
</div>

<div class="row">
    <div class="col-lg-4">
        <!-- Profile Card -->
        <div class="card mb-4">
            <div class="card-body text-center">
                <div class="user-avatar mx-auto mb-3" style="width: 100px; height: 100px; font-size: 2.5rem;">
                    {{ substr($student->name, 0, 1) }}
                </div>
                <h4 class="mb-1">{{ $student->name }}</h4>
                <p class="text-muted mb-3">{{ $student->nis }}</p>
                
                @if($student->status == 'active')
                <span class="badge bg-success fs-6">Aktif</span>
                @elseif($student->status == 'inactive')
                <span class="badge bg-secondary fs-6">Non-Aktif</span>
                @else
                <span class="badge bg-info fs-6">Lulus</span>
                @endif
            </div>
            <div class="card-footer bg-transparent">
                <div class="row text-center">
                    <div class="col-4">
                        <h5 class="mb-0">{{ $student->bills->count() }}</h5>
                        <small class="text-muted">Tagihan</small>
                    </div>
                    <div class="col-4">
                        <h5 class="mb-0">{{ $student->bills->where('status', 'paid')->count() }}</h5>
                        <small class="text-muted">Lunas</small>
                    </div>
                    <div class="col-4">
                        <h5 class="mb-0">{{ $student->bills->whereIn('status', ['unpaid', 'partial'])->count() }}</h5>
                        <small class="text-muted">Pending</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Card -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-info-circle me-2"></i>Informasi
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td class="text-muted" width="40%">NISN</td>
                        <td><strong>{{ $student->nisn ?? '-' }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Kelas</td>
                        <td><strong>{{ $student->class }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Jenis Kelamin</td>
                        <td>
                            <strong>
                                <i class="fas fa-{{ $student->gender == 'L' ? 'mars text-primary' : 'venus text-danger' }} me-1"></i>
                                {{ $student->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}
                            </strong>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">TTL</td>
                        <td>
                            <strong>
                                {{ $student->birth_place ?? '-' }}
                                @if($student->birth_date)
                                , {{ $student->birth_date->format('d M Y') }}
                                @endif
                            </strong>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tahun Masuk</td>
                        <td><strong>{{ $student->entry_year }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Alamat</td>
                        <td><strong>{{ $student->address ?? '-' }}</strong></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Orang Tua -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-users me-2"></i>Orang Tua / Wali
            </div>
            <div class="card-body">
                @forelse($student->parents as $parent)
                <div class="d-flex align-items-center p-2 mb-2 bg-light rounded">
                    <div class="user-avatar me-3" style="width: 40px; height: 40px;">
                        {{ substr($parent->user->name, 0, 1) }}
                    </div>
                    <div class="flex-grow-1">
                        <strong>{{ $parent->user->name }}</strong>
                        <small class="d-block text-muted">{{ $parent->user->email }}</small>
                        @if($parent->phone)
                        <small class="text-muted"><i class="fas fa-phone me-1"></i>{{ $parent->phone }}</small>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-3">
                    <i class="fas fa-user-slash d-block mb-2"></i>
                    Belum ada data orang tua
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <!-- Tagihan -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-file-invoice-dollar me-2"></i>Riwayat Tagihan</span>
                <a href="{{ route('treasurer.bills.create', ['student_id' => $student->id]) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus me-1"></i>Tambah Tagihan
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>No. Invoice</th>
                                <th>Jenis</th>
                                <th>Total</th>
                                <th>Terbayar</th>
                                <th>Jatuh Tempo</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($student->bills as $bill)
                            <tr>
                                <td><code>{{ $bill->invoice_number }}</code></td>
                                <td>
                                    {{ $bill->billType->name }}
                                    @if($bill->month)
                                    <small class="d-block text-muted">{{ $bill->month }}</small>
                                    @endif
                                </td>
                                <td>{{ $bill->formatted_amount }}</td>
                                <td class="text-success">{{ $bill->formatted_paid }}</td>
                                <td>
                                    <span class="{{ $bill->isOverdue() ? 'text-danger' : '' }}">
                                        {{ $bill->due_date->format('d M Y') }}
                                    </span>
                                </td>
                                <td>{!! $bill->status_badge !!}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    Belum ada tagihan untuk siswa ini
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Ringkasan Keuangan -->
        <div class="row">
            <div class="col-md-6">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h6><i class="fas fa-check-circle me-2"></i>Total Terbayar</h6>
                        <h3 class="mb-0">Rp {{ number_format($student->bills->sum('paid_amount'), 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <h6><i class="fas fa-exclamation-circle me-2"></i>Sisa Tagihan</h6>
                        @php
                            $remaining = $student->bills->sum('total_amount') - $student->bills->sum('paid_amount');
                        @endphp
                        <h3 class="mb-0">Rp {{ number_format($remaining, 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<form id="delete-form" action="{{ route('admin.students.destroy', $student) }}" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
function confirmDelete() {
    Swal.fire({
        title: 'Hapus Siswa?',
        html: `Anda yakin ingin menghapus siswa <strong>{{ $student->name }}</strong>?<br><small class="text-muted">Data tagihan terkait juga akan dihapus.</small>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form').submit();
        }
    });
}
</script>
@endpush
