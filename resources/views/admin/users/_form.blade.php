{{-- Form Partial for User Create/Edit --}}
<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-user me-2 text-primary"></i>Informasi Pengguna</h6>
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
                        <label class="form-label">Password @if(!isset($user))<span class="text-danger">*</span>@endif</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                               {{ !isset($user) ? 'required' : '' }}>
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

                <div class="mb-3">
                    <label class="form-label">No. Telepon</label>
                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                           value="{{ old('phone', $user->phone ?? '') }}" placeholder="08xxxxxxxxxx">
                    @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Role Selection -->
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-user-tag me-2 text-primary"></i>Role & Hak Akses</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Pilih Role <span class="text-danger">*</span></label>
                    <div class="row">
                        @foreach($roles as $role)
                        <div class="col-md-6 mb-2">
                            <div class="form-check p-3 border rounded role-option {{ in_array($role->id, old('roles', isset($user) ? $user->roles->pluck('id')->toArray() : [])) ? 'border-primary bg-light' : '' }}">
                                <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->id }}" 
                                       id="role-{{ $role->id }}"
                                       {{ in_array($role->id, old('roles', isset($user) ? $user->roles->pluck('id')->toArray() : [])) ? 'checked' : '' }}>
                                <label class="form-check-label w-100" for="role-{{ $role->id }}">
                                    <strong>{{ $role->display_name ?? ucfirst($role->name) }}</strong>
                                    @if($role->description)
                                    <small class="d-block text-muted">{{ $role->description }}</small>
                                    @endif
                                </label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @error('roles')
                    <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Parent Data (shown when orangtua role selected) -->
        <div class="card mb-4 border-0 shadow-sm" id="parent-data" style="display: none;">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-child me-2 text-primary"></i>Data Orang Tua</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Pekerjaan</label>
                        <input type="text" name="occupation" class="form-control" 
                               value="{{ old('occupation', isset($user) && $user->parentProfile ? $user->parentProfile->occupation : '') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Hubungan</label>
                        <select name="relationship" class="form-select">
                            <option value="ayah" {{ old('relationship', isset($user) && $user->parentProfile ? $user->parentProfile->relationship : '') == 'ayah' ? 'selected' : '' }}>Ayah</option>
                            <option value="ibu" {{ old('relationship', isset($user) && $user->parentProfile ? $user->parentProfile->relationship : '') == 'ibu' ? 'selected' : '' }}>Ibu</option>
                            <option value="wali" {{ old('relationship', isset($user) && $user->parentProfile ? $user->parentProfile->relationship : '') == 'wali' ? 'selected' : '' }}>Wali</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Alamat</label>
                    <textarea name="parent_address" rows="2" class="form-control">{{ old('parent_address', isset($user) && $user->parentProfile ? $user->parentProfile->address : '') }}</textarea>
                </div>
                @if(isset($students) && $students->count() > 0)
                <div class="mb-3">
                    <label class="form-label">Hubungkan dengan Siswa</label>
                    <select name="student_ids[]" class="form-select" multiple>
                        @foreach($students as $student)
                        <option value="{{ $student->id }}"
                                {{ in_array($student->id, old('student_ids', isset($user) && $user->parentProfile ? $user->parentProfile->students->pluck('id')->toArray() : [])) ? 'selected' : '' }}>
                            {{ $student->nis }} - {{ $student->name }} ({{ $student->class }})
                        </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Tahan Ctrl untuk memilih lebih dari satu</small>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Status -->
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-toggle-on me-2 text-primary"></i>Status</h6>
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

        @if(isset($user))
        <!-- Info -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2 text-primary"></i>Informasi</h6>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <small class="text-muted">Dibuat:</small>
                    <div>{{ $user->created_at->format('d/m/Y H:i') }}</div>
                </div>
                <div class="mb-2">
                    <small class="text-muted">Terakhir diupdate:</small>
                    <div>{{ $user->updated_at->format('d/m/Y H:i') }}</div>
                </div>
                @if($user->email_verified_at)
                <div>
                    <small class="text-muted">Email verified:</small>
                    <div class="text-success"><i class="fas fa-check-circle me-1"></i>Terverifikasi</div>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    function toggleParentData() {
        const orangtuaRole = @json($roles->where('name', 'orangtua')->first());
        if (!orangtuaRole) return;
        
        const isOrangtuaChecked = $(`#role-${orangtuaRole.id}`).is(':checked');
        
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
    });
});
</script>
@endpush
