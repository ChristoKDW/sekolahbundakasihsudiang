@extends('layouts.app')

@section('title', 'Detail User')
@section('page-title', 'Detail User')

@section('content')
<div class="row">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body text-center py-5">
                @if($user->avatar)
                    <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}" class="rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                @else
                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 120px; height: 120px;">
                        <span class="text-white" style="font-size: 3rem;">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    </div>
                @endif
                <h4 class="mb-1">{{ $user->name }}</h4>
                <p class="text-muted mb-3">{{ $user->email }}</p>
                
                @foreach($user->roles as $role)
                    <span class="badge bg-primary me-1">{{ $role->display_name }}</span>
                @endforeach
                
                <div class="mt-3">
                    @if($user->is_active)
                        <span class="badge bg-success"><i class="fas fa-check me-1"></i>Aktif</span>
                    @else
                        <span class="badge bg-danger"><i class="fas fa-times me-1"></i>Nonaktif</span>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-cog me-2"></i>Aksi</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-1"></i>Edit User
                    </a>
                    <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn {{ $user->is_active ? 'btn-secondary' : 'btn-success' }} w-100">
                            <i class="fas fa-power-off me-1"></i>{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                    </form>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi User</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td class="text-muted" width="200">Nama Lengkap</td>
                        <td>{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Email</td>
                        <td>{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">No. Telepon</td>
                        <td>{{ $user->phone ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Role</td>
                        <td>
                            @foreach($user->roles as $role)
                                <span class="badge bg-primary me-1">{{ $role->display_name }}</span>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status</td>
                        <td>
                            @if($user->is_active)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-danger">Nonaktif</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Terdaftar Sejak</td>
                        <td>{{ $user->created_at->format('d M Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Terakhir Update</td>
                        <td>{{ $user->updated_at->format('d M Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-history me-2"></i>Aktivitas Terakhir</h6>
            </div>
            <div class="card-body">
                @if($user->activityLogs->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Waktu</th>
                                    <th>Aktivitas</th>
                                    <th>Modul</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->activityLogs->take(10) as $log)
                                <tr>
                                    <td class="text-muted">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ $log->description }}</td>
                                    <td><span class="badge bg-secondary">{{ $log->module }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center mb-0">Belum ada aktivitas tercatat.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
