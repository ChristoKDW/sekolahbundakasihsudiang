@extends('layouts.app')

@section('title', 'Kelola Role')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1 class="page-title">Kelola Role</h1>
        <p class="page-subtitle">Manajemen role dan permission sistem</p>
    </div>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#roleModal">
        <i class="fas fa-plus me-2"></i>Tambah Role
    </button>
</div>

<div class="row g-4">
    @foreach($roles as $role)
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">{{ $role->display_name }}</h5>
                    <small class="text-muted">{{ $role->name }}</small>
                </div>
                <span class="badge bg-primary fs-6">{{ $role->users_count }} users</span>
            </div>
            <div class="card-body">
                @if($role->description)
                <p class="text-muted">{{ $role->description }}</p>
                @endif

                <h6 class="mb-3">Permissions:</h6>
                <div class="d-flex flex-wrap gap-2">
                    @forelse($role->permissions as $permission)
                    <span class="badge bg-light text-dark border">
                        <i class="fas fa-key me-1 text-primary"></i>{{ $permission->display_name }}
                    </span>
                    @empty
                    <span class="text-muted">Tidak ada permission</span>
                    @endforelse
                </div>
            </div>
            <div class="card-footer bg-transparent">
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="editRole({{ $role->id }})">
                    <i class="fas fa-edit me-1"></i>Edit
                </button>
                <button type="button" class="btn btn-sm btn-outline-info" onclick="managePermissions({{ $role->id }})">
                    <i class="fas fa-key me-1"></i>Kelola Permission
                </button>
                @if(!in_array($role->name, ['admin', 'orangtua', 'bendahara', 'kepala_sekolah']))
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteRole({{ $role->id }}, '{{ $role->name }}')">
                    <i class="fas fa-trash me-1"></i>Hapus
                </button>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Role Modal -->
<div class="modal fade" id="roleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="roleForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="roleModalTitle">Tambah Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Role (slug) <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="roleName" class="form-control" required 
                               pattern="[a-z_]+" title="Huruf kecil dan underscore saja">
                        <small class="text-muted">Contoh: admin, kepala_sekolah (huruf kecil, underscore)</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Tampilan <span class="text-danger">*</span></label>
                        <input type="text" name="display_name" id="roleDisplayName" class="form-control" required>
                        <small class="text-muted">Contoh: Administrator, Kepala Sekolah</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" id="roleDescription" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Permission Modal -->
<div class="modal fade" id="permissionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="permissionForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Kelola Permission: <span id="permRoleName"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row" id="permissionsList">
                        @foreach($permissions->groupBy(function($p) { return explode('.', $p->name)[0]; }) as $group => $perms)
                        <div class="col-md-6 mb-3">
                            <div class="card">
                                <div class="card-header py-2">
                                    <strong>{{ ucfirst($group) }}</strong>
                                </div>
                                <div class="card-body py-2">
                                    @foreach($perms as $permission)
                                    <div class="form-check">
                                        <input class="form-check-input permission-check" type="checkbox" 
                                               name="permissions[]" value="{{ $permission->id }}" 
                                               id="perm-{{ $permission->id }}">
                                        <label class="form-check-label" for="perm-{{ $permission->id }}">
                                            {{ $permission->display_name }}
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" onclick="selectAll()">Pilih Semua</button>
                    <button type="button" class="btn btn-outline-secondary" onclick="deselectAll()">Hapus Semua</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan Permission
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const roles = @json($roles);

function editRole(id) {
    const role = roles.find(r => r.id === id);
    document.getElementById('roleModalTitle').textContent = 'Edit Role';
    document.getElementById('roleName').value = role.name;
    document.getElementById('roleDisplayName').value = role.display_name;
    document.getElementById('roleDescription').value = role.description || '';
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('roleForm').action = `/admin/roles/${id}`;
    
    new bootstrap.Modal(document.getElementById('roleModal')).show();
}

function managePermissions(id) {
    const role = roles.find(r => r.id === id);
    document.getElementById('permRoleName').textContent = role.display_name;
    document.getElementById('permissionForm').action = `/admin/roles/${id}/permissions`;
    
    // Reset checkboxes
    document.querySelectorAll('.permission-check').forEach(cb => cb.checked = false);
    
    // Check role's permissions
    role.permissions.forEach(p => {
        const checkbox = document.getElementById(`perm-${p.id}`);
        if (checkbox) checkbox.checked = true;
    });
    
    new bootstrap.Modal(document.getElementById('permissionModal')).show();
}

function deleteRole(id, name) {
    Swal.fire({
        title: 'Hapus Role?',
        html: `Anda yakin ingin menghapus role <strong>${name}</strong>?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/roles/${id}`;
            form.innerHTML = `@csrf @method('DELETE')`;
            document.body.appendChild(form);
            form.submit();
        }
    });
}

function selectAll() {
    document.querySelectorAll('.permission-check').forEach(cb => cb.checked = true);
}

function deselectAll() {
    document.querySelectorAll('.permission-check').forEach(cb => cb.checked = false);
}

// Reset modal on close
document.getElementById('roleModal').addEventListener('hidden.bs.modal', function() {
    document.getElementById('roleModalTitle').textContent = 'Tambah Role';
    document.getElementById('roleForm').reset();
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('roleForm').action = '{{ route("admin.roles.store") }}';
});
</script>
@endpush
