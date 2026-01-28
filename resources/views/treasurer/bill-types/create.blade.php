@extends('layouts.app')

@section('title', 'Tambah Jenis Tagihan')

@push('styles')
<style>
    .form-switch .form-check-input {
        width: 3em;
        height: 1.5em;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('treasurer.bill-types.index') }}" class="btn btn-outline-secondary me-3">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="h3 mb-0 fw-bold text-primary">
                        <i class="fas fa-plus-circle me-2"></i>Tambah Jenis Tagihan
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('treasurer.bill-types.index') }}">Jenis Tagihan</a></li>
                            <li class="breadcrumb-item active">Tambah</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <!-- Form Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('treasurer.bill-types.store') }}" method="POST">
                        @csrf

                        <!-- Basic Information -->
                        <h5 class="mb-3 fw-bold">
                            <i class="fas fa-info-circle text-primary me-2"></i>Informasi Dasar
                        </h5>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-12">
                                <label for="name" class="form-label">Nama Jenis Tagihan <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name') }}"
                                       placeholder="Contoh: SPP Bulanan, Uang Gedung, dll"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="description" class="form-label">Deskripsi</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" 
                                          name="description" 
                                          rows="3"
                                          placeholder="Deskripsi jenis tagihan...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="amount" class="form-label">Nominal Default <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" 
                                           class="form-control @error('amount') is-invalid @enderror" 
                                           id="amount" 
                                           name="amount" 
                                           value="{{ old('amount', 0) }}"
                                           min="0"
                                           step="1000"
                                           required>
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-text">Nominal dapat diubah saat membuat tagihan</div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check form-switch mt-4">
                                    <input type="checkbox" 
                                           class="form-check-input" 
                                           id="is_flexible" 
                                           name="is_flexible" 
                                           value="1"
                                           {{ old('is_flexible') ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="is_flexible">
                                        <i class="fas fa-hand-holding-usd text-success me-1"></i>Nominal Fleksibel
                                    </label>
                                </div>
                                <div class="form-text">Jika aktif, orang tua dapat membayar sesuai kemampuan (SPP, Uang Pangkal)</div>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-exclamation-circle text-warning me-1"></i>Jenis Pembayaran
                                </label>
                                <div class="mt-2">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="is_mandatory" id="mandatory_yes" value="1" {{ old('is_mandatory', '1') == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="mandatory_yes">
                                            <span class="badge bg-danger">Wajib</span>
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="is_mandatory" id="mandatory_no" value="0" {{ old('is_mandatory') == '0' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="mandatory_no">
                                            <span class="badge bg-info">Sukarela</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="form-text">
                                    <strong>Wajib:</strong> Tagihan harus dibayar (SPP, Uang Gedung)<br>
                                    <strong>Sukarela:</strong> Pembayaran opsional (Infaq, Donasi)
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Recurring Settings -->
                        <h5 class="mb-3 fw-bold">
                            <i class="fas fa-sync-alt text-primary me-2"></i>Pengaturan Berulang
                        </h5>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input type="checkbox" 
                                           class="form-check-input" 
                                           id="is_recurring" 
                                           name="is_recurring" 
                                           value="1"
                                           {{ old('is_recurring') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_recurring">Tagihan Berulang</label>
                                </div>
                                <div class="form-text">Aktifkan jika tagihan ini bersifat rutin/berulang</div>
                            </div>

                            <div class="col-md-6" id="recurring-period-wrapper" style="display: none;">
                                <label for="recurring_period" class="form-label">Periode Berulang</label>
                                <select class="form-select @error('recurring_period') is-invalid @enderror" 
                                        id="recurring_period" 
                                        name="recurring_period">
                                    <option value="">-- Pilih Periode --</option>
                                    <option value="monthly" {{ old('recurring_period') == 'monthly' ? 'selected' : '' }}>Bulanan</option>
                                    <option value="quarterly" {{ old('recurring_period') == 'quarterly' ? 'selected' : '' }}>Triwulan</option>
                                    <option value="semester" {{ old('recurring_period') == 'semester' ? 'selected' : '' }}>Semester</option>
                                    <option value="yearly" {{ old('recurring_period') == 'yearly' ? 'selected' : '' }}>Tahunan</option>
                                </select>
                                @error('recurring_period')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Status -->
                        <h5 class="mb-3 fw-bold">
                            <i class="fas fa-toggle-on text-primary me-2"></i>Status
                        </h5>

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input type="checkbox" 
                                       class="form-check-input" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1"
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Aktif</label>
                            </div>
                            <div class="form-text">Jenis tagihan aktif dapat digunakan untuk membuat tagihan baru</div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('treasurer.bill-types.index') }}" class="btn btn-light">
                                <i class="fas fa-times me-1"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const isRecurring = document.getElementById('is_recurring');
    const recurringPeriodWrapper = document.getElementById('recurring-period-wrapper');

    function toggleRecurringPeriod() {
        if (isRecurring.checked) {
            recurringPeriodWrapper.style.display = 'block';
        } else {
            recurringPeriodWrapper.style.display = 'none';
        }
    }

    isRecurring.addEventListener('change', toggleRecurringPeriod);
    toggleRecurringPeriod();
});
</script>
@endpush
