@extends('layouts.app')

@section('title', 'Rekonsiliasi Baru')
@section('page-title', 'Buat Rekonsiliasi Baru')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <div class="d-flex align-items-center">
                    <a href="{{ route('treasurer.reconciliation.index') }}" class="btn btn-outline-secondary btn-sm me-3">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h5 class="mb-0"><i class="fas fa-sync-alt me-2 text-primary"></i>Rekonsiliasi Baru</h5>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Algoritma Rekonsiliasi 4 Level:</strong>
                    <ol class="mb-0 mt-2">
                        <li><strong>Level 1:</strong> Exact Match - Cocokkan berdasarkan Order ID dan jumlah</li>
                        <li><strong>Level 2:</strong> ID Match - Cocokkan berdasarkan Transaction ID Midtrans</li>
                        <li><strong>Level 3:</strong> Fuzzy Match - Cocokkan berdasarkan waktu dan nominal mendekati</li>
                        <li><strong>Level 4:</strong> Duplicate Detection - Deteksi transaksi duplikat</li>
                    </ol>
                </div>
                
                <form action="{{ route('treasurer.reconciliation.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date') }}" required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Akhir <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date') }}" required>
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">File Statement Bank (Opsional)</label>
                        <input type="file" name="bank_statement" class="form-control @error('bank_statement') is-invalid @enderror" accept=".csv,.xlsx,.xls">
                        <small class="text-muted">Upload file CSV atau Excel dari bank untuk dicocokkan</small>
                        @error('bank_statement')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Catatan tambahan (opsional)">{{ old('notes') }}</textarea>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <a href="{{ route('treasurer.reconciliation.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i>Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-play me-1"></i>Mulai Rekonsiliasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
