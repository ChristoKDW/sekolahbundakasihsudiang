@extends('layouts.app')

@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')

@section('content')
<div class="row">
    <!-- Profile Card -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="user-avatar mx-auto mb-3" style="width: 120px; height: 120px; font-size: 3rem;">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <h4>{{ auth()->user()->name }}</h4>
                <p class="text-muted mb-2">{{ auth()->user()->email }}</p>
                <span class="badge bg-primary fs-6">{{ ucfirst(auth()->user()->role->name ?? 'User') }}</span>
                
                <hr class="my-4">
                
                <div class="text-start">
                    <div class="mb-3">
                        <small class="text-muted d-block">Bergabung Sejak</small>
                        <strong>{{ auth()->user()->created_at->format('d F Y') }}</strong>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Login Terakhir</small>
                        <strong>{{ auth()->user()->last_login_at ? auth()->user()->last_login_at->diffForHumans() : 'Baru saja' }}</strong>
                    </div>
                    @if(auth()->user()->role->name === 'orangtua')
                    <div class="mb-3">
                        <small class="text-muted d-block">Anak Terdaftar</small>
                        <strong>{{ auth()->user()->students->count() }} Siswa</strong>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Quick Stats -->
        @if(auth()->user()->role->name === 'orangtua')
        <div class="card mt-4">
            <div class="card-header">
                <i class="fas fa-chart-pie me-2"></i>Ringkasan Pembayaran
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Total Tagihan</span>
                    <strong>{{ $totalBills ?? 0 }}</strong>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Sudah Dibayar</span>
                    <strong class="text-success">{{ $paidBills ?? 0 }}</strong>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted">Belum Dibayar</span>
                    <strong class="text-danger">{{ $unpaidBills ?? 0 }}</strong>
                </div>
            </div>
        </div>
        @endif
    </div>
    
    <!-- Edit Profile Form -->
    <div class="col-lg-8">
        <!-- Personal Information -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-user-edit me-2"></i>Informasi Pribadi
            </div>
            <div class="card-body">
                @if(session('profile-success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle me-2"></i>{{ session('profile-success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif
                
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', auth()->user()->name) }}" required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email', auth()->user()->email) }}" required>
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nomor Telepon</label>
                            <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                                   value="{{ old('phone', auth()->user()->phone) }}" placeholder="08xxxxxxxxxx">
                            @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Role</label>
                            <input type="text" class="form-control" value="{{ ucfirst(auth()->user()->role->name ?? 'User') }}" readonly>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="address" class="form-control @error('address') is-invalid @enderror" 
                                  rows="3" placeholder="Alamat lengkap">{{ old('address', auth()->user()->address) }}</textarea>
                        @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Change Password -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-lock me-2"></i>Ubah Password
            </div>
            <div class="card-body">
                @if(session('password-success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle me-2"></i>{{ session('password-success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif
                
                @if(session('password-error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('password-error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif
                
                <form action="{{ route('profile.password') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label">Password Saat Ini <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" 
                                   required id="current_password">
                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="current_password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('current_password')
                        <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password Baru <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                                       required id="new_password" minlength="8">
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="new_password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('password')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Minimal 8 karakter</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" name="password_confirmation" class="form-control" 
                                       required id="confirm_password">
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="confirm_password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="password-strength mb-3" id="passwordStrength" style="display: none;">
                        <small class="d-block mb-1">Kekuatan Password:</small>
                        <div class="progress" style="height: 5px;">
                            <div class="progress-bar" id="passwordStrengthBar" role="progressbar"></div>
                        </div>
                        <small id="passwordStrengthText" class="text-muted"></small>
                    </div>
                    
                    <div class="text-end">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-key me-2"></i>Ubah Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Toggle password visibility
document.querySelectorAll('.toggle-password').forEach(function(button) {
    button.addEventListener('click', function() {
        const targetId = this.getAttribute('data-target');
        const input = document.getElementById(targetId);
        const icon = this.querySelector('i');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
});

// Password strength indicator
const newPassword = document.getElementById('new_password');
const strengthDiv = document.getElementById('passwordStrength');
const strengthBar = document.getElementById('passwordStrengthBar');
const strengthText = document.getElementById('passwordStrengthText');

newPassword.addEventListener('input', function() {
    const password = this.value;
    
    if (password.length === 0) {
        strengthDiv.style.display = 'none';
        return;
    }
    
    strengthDiv.style.display = 'block';
    let strength = 0;
    
    if (password.length >= 8) strength += 25;
    if (password.match(/[a-z]/)) strength += 25;
    if (password.match(/[A-Z]/)) strength += 25;
    if (password.match(/[0-9]/)) strength += 15;
    if (password.match(/[^a-zA-Z0-9]/)) strength += 10;
    
    strengthBar.style.width = strength + '%';
    
    if (strength < 25) {
        strengthBar.className = 'progress-bar bg-danger';
        strengthText.textContent = 'Sangat Lemah';
    } else if (strength < 50) {
        strengthBar.className = 'progress-bar bg-warning';
        strengthText.textContent = 'Lemah';
    } else if (strength < 75) {
        strengthBar.className = 'progress-bar bg-info';
        strengthText.textContent = 'Cukup';
    } else {
        strengthBar.className = 'progress-bar bg-success';
        strengthText.textContent = 'Kuat';
    }
});
</script>
@endpush
