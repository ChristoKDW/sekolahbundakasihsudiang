@extends('layouts.app')

@section('title', 'Kelola Users')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1 class="page-title">Kelola Pengguna</h1>
        <p class="page-subtitle">Manajemen akun pengguna dan hak akses</p>
    </div>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Tambah User
    </a>
</div>

<!-- Stats -->
<div class="row g-4 mb-4">
    @foreach($roles as $role)
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon {{ $role->name == 'admin' ? 'primary' : ($role->name == 'orangtua' ? 'success' : ($role->name == 'bendahara' ? 'warning' : 'info')) }}">
                <i class="fas fa-{{ $role->name == 'admin' ? 'user-shield' : ($role->name == 'orangtua' ? 'users' : ($role->name == 'bendahara' ? 'calculator' : 'graduation-cap')) }}"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $role->users_count }}</div>
                <div class="stat-label">{{ $role->display_name }}</div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Users Table -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="usersTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Pengguna</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Terakhir Login</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $index => $user)
                    <tr>
                        <td>{{ $users->firstItem() + $index }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="user-avatar me-3" style="width: 40px; height: 40px;">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div>
                                    <strong>{{ $user->name }}</strong>
                                    <small class="d-block text-muted">Dibuat {{ $user->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @foreach($user->roles as $role)
                            <span class="badge bg-{{ $role->name == 'admin' ? 'primary' : ($role->name == 'orangtua' ? 'success' : ($role->name == 'bendahara' ? 'warning' : 'info')) }}">
                                {{ $role->display_name }}
                            </span>
                            @endforeach
                        </td>
                        <td>
                            @if($user->is_active)
                            <span class="badge bg-success">Aktif</span>
                            @else
                            <span class="badge bg-secondary">Non-Aktif</span>
                            @endif
                        </td>
                        <td>
                            @if($user->last_login_at)
                            {{ $user->last_login_at->diffForHumans() }}
                            @else
                            <span class="text-muted">Belum pernah</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($user->id != auth()->id())
                                <button type="button" class="btn btn-sm btn-outline-{{ $user->is_active ? 'warning' : 'success' }}" 
                                        onclick="toggleStatus({{ $user->id }}, {{ $user->is_active ? 0 : 1 }})" 
                                        title="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                    <i class="fas fa-{{ $user->is_active ? 'ban' : 'check' }}"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete({{ $user->id }}, '{{ $user->name }}')" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @endif
                            </div>
                            <form id="delete-form-{{ $user->id }}" action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                            <form id="toggle-form-{{ $user->id }}" action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="d-none">
                                @csrf
                                @method('PATCH')
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="empty-state">
                                <i class="fas fa-users"></i>
                                <h5>Tidak ada pengguna</h5>
                                <p class="text-muted">Tidak ada data yang cocok dengan filter.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($users->hasPages())
    <div class="card-footer">
        {{ $users->links() }}
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#usersTable').DataTable({
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

function confirmDelete(id, name) {
    Swal.fire({
        title: 'Hapus Pengguna?',
        html: `Anda yakin ingin menghapus <strong>${name}</strong>?`,
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

function toggleStatus(id, status) {
    const action = status ? 'mengaktifkan' : 'menonaktifkan';
    Swal.fire({
        title: 'Konfirmasi',
        text: `Anda yakin ingin ${action} pengguna ini?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#4F46E5',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('toggle-form-' + id).submit();
        }
    });
}
</script>
@endpush
