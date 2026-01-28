@extends('layouts.app')

@section('title', 'Detail Anak')
@section('page-title', 'Detail Anak')

@section('content')
<div class="row">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body text-center py-4">
                @if($student->photo)
                    <img src="{{ Storage::url($student->photo) }}" alt="{{ $student->name }}" class="rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                @else
                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 120px; height: 120px;">
                        <i class="fas fa-user-graduate fa-3x text-white"></i>
                    </div>
                @endif
                <h4 class="mb-1">{{ $student->name }}</h4>
                <p class="text-muted mb-3">NIS: {{ $student->nis }}</p>
                <span class="badge bg-primary me-1">{{ $student->class }}</span>
                @if($student->status == 'active')
                    <span class="badge bg-success">Aktif</span>
                @else
                    <span class="badge bg-secondary">{{ ucfirst($student->status) }}</span>
                @endif
            </div>
        </div>
        
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Siswa</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm mb-0">
                    <tr>
                        <td class="text-muted">NISN</td>
                        <td>{{ $student->nisn ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Jenis Kelamin</td>
                        <td>{{ $student->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tempat Lahir</td>
                        <td>{{ $student->place_of_birth }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tanggal Lahir</td>
                        <td>{{ $student->date_of_birth->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Alamat</td>
                        <td>{{ $student->address }}</td>
                    </tr>
                    @if($student->major)
                    <tr>
                        <td class="text-muted">Jurusan</td>
                        <td>{{ $student->major }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-file-invoice-dollar me-2"></i>Tagihan Aktif</h6>
                <a href="{{ route('parent.payments.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body">
                @if($student->bills->where('status', '!=', 'paid')->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Jenis</th>
                                    <th>Periode</th>
                                    <th>Jumlah</th>
                                    <th>Jatuh Tempo</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($student->bills->where('status', '!=', 'paid')->take(5) as $bill)
                                <tr>
                                    <td>{{ $bill->billType->name }}</td>
                                    <td>{{ $bill->month }}/{{ $bill->year }}</td>
                                    <td><strong>Rp {{ number_format($bill->amount - $bill->paid_amount, 0, ',', '.') }}</strong></td>
                                    <td>{{ $bill->due_date->format('d M Y') }}</td>
                                    <td>
                                        @if($bill->status == 'overdue')
                                            <span class="badge bg-danger">Terlambat</span>
                                        @elseif($bill->status == 'partial')
                                            <span class="badge bg-warning">Sebagian</span>
                                        @else
                                            <span class="badge bg-secondary">Belum Bayar</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('parent.payments.checkout', $bill) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-credit-card me-1"></i>Bayar
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <h5>Tidak Ada Tagihan</h5>
                        <p class="text-muted mb-0">Semua tagihan sudah lunas.</p>
                    </div>
                @endif
            </div>
        </div>
        
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-history me-2"></i>Riwayat Pembayaran Terakhir</h6>
            </div>
            <div class="card-body">
                @php
                    $recentPayments = $student->bills->flatMap->payments->sortByDesc('created_at')->take(5);
                @endphp
                
                @if($recentPayments->count() > 0)
                    @foreach($recentPayments as $payment)
                    <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                        <div class="rounded-circle bg-success p-2 me-3">
                            <i class="fas fa-check text-white"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <strong>{{ $payment->bill->billType->name }}</strong>
                                <span class="text-success">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                            </div>
                            <small class="text-muted">{{ $payment->created_at->format('d M Y H:i') }}</small>
                        </div>
                    </div>
                    @endforeach
                @else
                    <p class="text-muted text-center mb-0">Belum ada riwayat pembayaran.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('parent.students.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>Kembali
    </a>
</div>
@endsection
