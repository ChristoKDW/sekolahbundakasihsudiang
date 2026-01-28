@extends('layouts.app')

@section('title', 'Detail Role')
@section('page-title', 'Detail Role')

@section('content')
<div class="row">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body text-center py-4">
                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                    <i class="fas fa-user-tag fa-2x text-white"></i>
                </div>
                <h4 class="mb-1">{{ $role->display_name }}</h4>
                <p class="text-muted">{{ $role->name }}</p>
            </div>
        </div>
        
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-cog me-2"></i>Aksi</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-1"></i>Edit Role
                    </a>
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Role</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td class="text-muted" width="200">Nama Role</td>
                        <td>{{ $role->name }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Nama Tampilan</td>
                        <td>{{ $role->display_name }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Deskripsi</td>
                        <td>{{ $role->description ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Jumlah User</td>
                        <td><span class="badge bg-primary">{{ $role->users->count() }} user</span></td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-key me-2"></i>Permissions ({{ $role->permissions->count() }})</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($role->permissions->groupBy('module') as $module => $perms)
                        <div class="col-md-4 mb-3">
                            <h6 class="text-capitalize text-primary mb-2">{{ $module }}</h6>
                            <ul class="list-unstyled mb-0">
                                @foreach($perms as $permission)
                                    <li class="mb-1">
                                        <i class="fas fa-check text-success me-1"></i>
                                        {{ $permission->display_name }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-users me-2"></i>User dengan Role Ini ({{ $role->users->count() }})</h6>
            </div>
            <div class="card-body">
                @if($role->users->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($role->users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if($user->is_active)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-danger">Nonaktif</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center mb-0">Belum ada user dengan role ini.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
