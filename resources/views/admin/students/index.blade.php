@extends('layouts.app')

@section('title', 'Kelola Siswa')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1 class="page-title">Kelola Data Siswa</h1>
        <p class="page-subtitle">Daftar semua siswa yang terdaftar di sistem</p>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="fas fa-file-import me-2"></i>Import
        </button>
        <a href="{{ route('admin.students.export') }}" class="btn btn-outline-primary">
            <i class="fas fa-file-excel me-2"></i>Export
        </a>
        <a href="{{ route('admin.students.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Tambah Siswa
        </a>
    </div>
</div>

<!-- Students Table -->
<div class="card">
    <div class="card-header bg-white">
        <form action="" method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small">Cari</label>
                <input type="text" name="search" class="form-control" placeholder="Nama, NIS, NISN..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small">Jenjang</label>
                <select name="education_level" class="form-select">
                    <option value="">Semua Jenjang</option>
                    @foreach(\App\Models\Student::getEducationLevels() as $key => $label)
                    <option value="{{ $key }}" {{ request('education_level') == $key ? 'selected' : '' }}>{{ $key }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small">Kelas</label>
                <select name="class" class="form-select">
                    <option value="">Semua Kelas</option>
                    @foreach($classes as $cls)
                    <option value="{{ $cls }}" {{ request('class') == $cls ? 'selected' : '' }}>{{ $cls }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Non-Aktif</option>
                    <option value="graduated" {{ request('status') == 'graduated' ? 'selected' : '' }}>Lulus</option>
                    <option value="dropout" {{ request('status') == 'dropout' ? 'selected' : '' }}>Keluar</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary me-1"><i class="fas fa-search"></i></button>
                <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary"><i class="fas fa-sync"></i></a>
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="studentsTable">
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th>Siswa</th>
                        <th>NIS / NISN</th>
                        <th>Jenjang</th>
                        <th>Kelas</th>
                        <th>Orang Tua</th>
                        <th>Status</th>
                        <th>Tagihan</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $index => $student)
                    <tr>
                        <td>{{ $students->firstItem() + $index }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="user-avatar me-3" style="width: 40px; height: 40px;">
                                    {{ substr($student->name, 0, 1) }}
                                </div>
                                <div>
                                    <strong>{{ $student->name }}</strong>
                                    @if($student->gender)
                                    <small class="d-block text-muted">
                                        <i class="fas fa-{{ $student->gender == 'L' ? 'mars' : 'venus' }}"></i>
                                        {{ $student->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                    </small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <strong>{{ $student->nis }}</strong>
                            @if($student->nisn)
                            <small class="d-block text-muted">{{ $student->nisn }}</small>
                            @endif
                        </td>
                        <td><span class="badge bg-info">{{ $student->education_level }}</span></td>
                        <td>{{ $student->class }}</td>
                        <td>
                            @forelse($student->parents as $parent)
                            <span class="badge bg-light text-dark">
                                {{ $parent->user->name }}
                            </span>
                            @empty
                            <span class="text-muted">-</span>
                            @endforelse
                        </td>
                        <td>
                            @if($student->status == 'active')
                            <span class="badge bg-success">Aktif</span>
                            @elseif($student->status == 'inactive')
                            <span class="badge bg-secondary">Non-Aktif</span>
                            @elseif($student->status == 'graduated')
                            <span class="badge bg-info">Lulus</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $unpaid = $student->bills()->where('status', '!=', 'paid')->sum('total_amount') - $student->bills()->where('status', '!=', 'paid')->sum('paid_amount');
                            @endphp
                            @if($unpaid > 0)
                            <span class="text-danger">
                                <strong>Rp {{ number_format($unpaid, 0, ',', '.') }}</strong>
                            </span>
                            @else
                            <span class="text-success">
                                <i class="fas fa-check-circle"></i> Lunas
                            </span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('admin.students.show', $student) }}" class="btn btn-sm btn-outline-info" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete({{ $student->id }}, '{{ $student->name }}')" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <form id="delete-form-{{ $student->id }}" action="{{ route('admin.students.destroy', $student) }}" method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="empty-state">
                                <i class="fas fa-user-graduate"></i>
                                <h5>Tidak ada data siswa</h5>
                                <p class="text-muted">Belum ada siswa yang terdaftar atau tidak ada yang cocok dengan filter.</p>
                                <a href="{{ route('admin.students.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Tambah Siswa Pertama
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($students->hasPages())
    <div class="card-footer">
        {{ $students->links() }}
    </div>
    @endif
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Data Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.students.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">File Excel (.xlsx)</label>
                        <input type="file" name="file" class="form-control" accept=".xlsx,.xls" required>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Format:</strong> NIS, NISN, Nama, Kelas, Jenis Kelamin, Tanggal Lahir, Alamat, Email Orang Tua
                    </div>
                    <a href="{{ route('admin.students.template') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-download me-2"></i>Download Template
                    </a>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-upload me-2"></i>Import
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
    $('#studentsTable').DataTable({
        paging: true,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
        info: true,
        searching: true,
        ordering: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
            emptyTable: "Tidak ada data siswa",
            zeroRecords: "Tidak ditemukan data yang cocok"
        }
    });
});

function confirmDelete(id, name) {
    Swal.fire({
        title: 'Hapus Siswa?',
        html: `Anda yakin ingin menghapus siswa <strong>${name}</strong>?<br><small class="text-muted">Data tagihan terkait juga akan dihapus.</small>`,
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
