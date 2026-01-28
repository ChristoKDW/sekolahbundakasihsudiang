@extends('layouts.app')

@section('title', 'Anak Saya')
@section('page-title', 'Data Anak')

@section('content')
<div class="row">
    @forelse($students as $student)
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-4">
                @if($student->photo)
                    <img src="{{ Storage::url($student->photo) }}" alt="{{ $student->name }}" class="rounded-circle mb-3" style="width: 100px; height: 100px; object-fit: cover;">
                @else
                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 100px; height: 100px;">
                        <i class="fas fa-user-graduate fa-2x text-white"></i>
                    </div>
                @endif
                <h5 class="mb-1">{{ $student->name }}</h5>
                <p class="text-muted mb-3">NIS: {{ $student->nis }}</p>
                
                <div class="d-flex justify-content-center gap-2 mb-3">
                    <span class="badge bg-primary">{{ $student->class }}</span>
                    @if($student->status == 'active')
                        <span class="badge bg-success">Aktif</span>
                    @else
                        <span class="badge bg-secondary">{{ ucfirst($student->status) }}</span>
                    @endif
                </div>
                
                <a href="{{ route('parent.students.show', $student) }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-eye me-1"></i>Lihat Detail
                </a>
            </div>
            <div class="card-footer bg-light">
                <div class="row text-center">
                    <div class="col-6 border-end">
                        <h5 class="mb-0 text-danger">{{ $student->bills->where('status', '!=', 'paid')->count() }}</h5>
                        <small class="text-muted">Tagihan Aktif</small>
                    </div>
                    <div class="col-6">
                        <h5 class="mb-0 text-success">{{ $student->bills->where('status', 'paid')->count() }}</h5>
                        <small class="text-muted">Lunas</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-users fa-4x text-muted mb-3"></i>
                <h5>Belum Ada Data Anak</h5>
                <p class="text-muted">Silakan hubungi admin sekolah untuk menghubungkan data anak Anda.</p>
            </div>
        </div>
    </div>
    @endforelse
</div>
@endsection
