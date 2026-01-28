@extends('layouts.app')

@section('title', isset($user) ? 'Edit User' : 'Tambah User')

@section('content')
<div class="page-header">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
            <li class="breadcrumb-item active">{{ isset($user) ? 'Edit' : 'Tambah' }}</li>
        </ol>
    </nav>
    <h1 class="page-title">{{ isset($user) ? 'Edit Pengguna' : 'Tambah Pengguna Baru' }}</h1>
</div>

<form action="{{ isset($user) ? route('admin.users.update', $user) : route('admin.users.store') }}" method="POST">
    @csrf
    @if(isset($user))
        @method('PUT')
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-user me-2"></i>Informasi Pengguna
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name', $user->name ?? '') }}" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                               value="{{ old('email', $user->email ?? '') }}" required>
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password {{ isset($user) ? '' : '<span class="text-danger">*</span>' }}</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                                   {{ isset($user) ? '' : 'required' }}>
                            @if(isset($user))
                            <small class="text-muted">Kosongkan jika tidak ingin mengubah password</small>
                            @endif
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Role Selection -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-user-tag me-2"></i>Role & Hak Akses
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Pilih Role <span class="text-danger">*</span></label>
                        <div class="row">
                            @foreach($roles as $role)
                            <div class="col-md-6 mb-2">
                                <div class="form-check p-3 border rounded role-option">
                                    <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->id }}" 
                                           id="role-{{ $role->id }}"
                                           {{ in_array($role->id, old('roles', isset($user) ? $user->roles->pluck('id')->toArray() : [])) ? 'checked' : '' }}>
                                    <label class="form-check-label w-100" for="role-{{ $role->id }}">
                                        <strong>{{ $role->display_name }}</strong>
                                        @if($role->description)
                                        <small class="d-block text-muted">{{ $role->description }}</small>
                                        @endif
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @error('roles')
                        <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Parent Data (shown when orangtua role selected) -->
            <div class="card mb-4" id="parent-data" style="display: none;">
                <div class="card-header">
                    <i class="fas fa-child me-2"></i>Data Orang Tua
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No. Telepon</label>
                            <input type="text" name="phone" class="form-control" 
                                   value="{{ old('phone', isset($user) && $user->parentModel ? $user->parentModel->phone : '') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Pekerjaan</label>
                            <input type="text" name="occupation" class="form-control" 
                                   value="{{ old('occupation', isset($user) && $user->parentModel ? $user->parentModel->occupation : '') }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="parent_address" rows="2" class="form-control">{{ old('parent_address', isset($user) && $user->parentModel ? $user->parentModel->address : '') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Hubungkan dengan Siswa</label>
                        <select name="student_ids[]" class="form-select select2" multiple>
                            @foreach($students as $student)
                            <option value="{{ $student->id }}"
                                    {{ in_array($student->id, old('student_ids', isset($user) && $user->parentModel ? $user->parentModel->students->pluck('id')->toArray() : [])) ? 'selected' : '' }}>
                                {{ $student->nis }} - {{ $student->name }} ({{ $student->class }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Status -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-toggle-on me-2"></i>Status
                </div>
                <div class="card-body">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                               {{ old('is_active', isset($user) ? $user->is_active : true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Akun Aktif</label>
                    </div>
                    <small class="text-muted">Pengguna tidak aktif tidak dapat login ke sistem</small>
                </div>
            </div>

            <!-- Aksi -->
            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100 mb-2">
                        <i class="fas fa-save me-2"></i>{{ isset($user) ? 'Simpan Perubahan' : 'Simpan User' }}
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    function toggleParentData() {
        const orangtuaRoleId = @json($roles->where('name', 'orangtua')->first()?->id);
        const isOrangtuaChecked = $(`#role-${orangtuaRoleId}`).is(':checked');
        
        if (isOrangtuaChecked) {
            $('#parent-data').slideDown();
        } else {
            $('#parent-data').slideUp();
        }
    }

    $('input[name="roles[]"]').change(toggleParentData);
    toggleParentData(); // Initial check

    // Highlight selected roles
    $('.role-option input').change(function() {
        if ($(this).is(':checked')) {
            $(this).closest('.role-option').addClass('border-primary bg-light');
        } else {
            $(this).closest('.role-option').removeClass('border-primary bg-light');
        }
    }).trigger('change');
});
</script>
@endpush
