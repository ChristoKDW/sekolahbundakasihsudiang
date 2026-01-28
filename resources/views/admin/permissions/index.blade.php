@extends('layouts.app')

@section('title', 'Permissions')
@section('page-title', 'Kelola Permissions')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-key me-2 text-primary"></i>Daftar Permissions</h5>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="fas fa-plus me-1"></i>Tambah Permission
            </button>
        </div>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover align-middle" id="permissionsTable">
                <thead class="table-light">
                    <tr>
                        <th>Nama Permission</th>
                        <th>Nama Tampilan</th>
                        <th>Modul</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($permissions as $permission)
                    <tr>
                        <td><code>{{ $permission->name }}</code></td>
                        <td>{{ $permission->display_name }}</td>
                        <td><span class="badge bg-secondary">{{ $permission->module }}</span></td>
                        <td>
                            <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $permission->id }}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('admin.permissions.destroy', $permission) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus permission ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    
                    <!-- Edit Modal -->
                    <div class="modal fade" id="editModal{{ $permission->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('admin.permissions.update', $permission) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Permission</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Nama Permission</label>
                                            <input type="text" name="name" class="form-control" value="{{ $permission->name }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Nama Tampilan</label>
                                            <input type="text" name="display_name" class="form-control" value="{{ $permission->display_name }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Modul</label>
                                            <input type="text" name="module" class="form-control" value="{{ $permission->module }}" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary">Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted">Belum ada permission</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-center mt-3">
            {{ $permissions->links() }}
        </div>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.permissions.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Permission Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Permission <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="contoh: create_reports" required>
                        <small class="text-muted">Gunakan huruf kecil dan underscore</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Tampilan <span class="text-danger">*</span></label>
                        <input type="text" name="display_name" class="form-control" placeholder="contoh: Buat Laporan" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Modul <span class="text-danger">*</span></label>
                        <input type="text" name="module" class="form-control" placeholder="contoh: reports" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#permissionsTable').DataTable({
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
