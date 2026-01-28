@extends('layouts.app')

@section('title', isset($bill) ? 'Edit Tagihan' : 'Tambah Tagihan')

@section('content')
<div class="page-header">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('treasurer.bills.index') }}">Tagihan</a></li>
            <li class="breadcrumb-item active">{{ isset($bill) ? 'Edit' : 'Tambah' }}</li>
        </ol>
    </nav>
    <h1 class="page-title">{{ isset($bill) ? 'Edit Tagihan' : 'Buat Tagihan Baru' }}</h1>
</div>

<form action="{{ isset($bill) ? route('treasurer.bills.update', $bill) : route('treasurer.bills.store') }}" method="POST">
    @csrf
    @if(isset($bill))
        @method('PUT')
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-file-invoice me-2"></i>Detail Tagihan
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Siswa <span class="text-danger">*</span></label>
                            <select name="student_id" class="form-select select2 @error('student_id') is-invalid @enderror" required>
                                <option value="">Pilih Siswa</option>
                                @foreach($students as $student)
                                <option value="{{ $student->id }}" 
                                        {{ old('student_id', $bill->student_id ?? request('student_id')) == $student->id ? 'selected' : '' }}>
                                    {{ $student->nis }} - {{ $student->name }} ({{ $student->class }})
                                </option>
                                @endforeach
                            </select>
                            @error('student_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis Tagihan <span class="text-danger">*</span></label>
                            <select name="bill_type_id" id="billType" class="form-select @error('bill_type_id') is-invalid @enderror" required>
                                @foreach($billTypes as $type)
                                <option value="{{ $type->id }}" 
                                        data-amount="{{ $type->amount }}"
                                        {{ old('bill_type_id', $bill->bill_type_id ?? '') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('bill_type_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jumlah Tagihan <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="amount" id="totalAmount" 
                                       class="form-control @error('amount') is-invalid @enderror" 
                                       value="{{ old('amount', $bill->amount ?? '') }}" 
                                       required min="1000">
                            </div>
                            @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jatuh Tempo <span class="text-danger">*</span></label>
                            <input type="date" name="due_date" class="form-control @error('due_date') is-invalid @enderror" 
                                   value="{{ old('due_date', isset($bill) ? $bill->due_date->format('Y-m-d') : '') }}" required>
                            @error('due_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Bulan (untuk SPP)</label>
                            <input type="text" name="month" class="form-control" 
                                   value="{{ old('month', $bill->month ?? '') }}" placeholder="Januari 2024">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tahun Ajaran</label>
                            <input type="text" name="academic_year" class="form-control" 
                                   value="{{ old('academic_year', $bill->academic_year ?? date('Y').'/'.(date('Y')+1)) }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="notes" rows="3" class="form-control">{{ old('notes', $bill->notes ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            @if(isset($bill) && $bill->payments->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-history me-2"></i>Riwayat Pembayaran
                </div>
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>No. Transaksi</th>
                                <th>Jumlah</th>
                                <th>Metode</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bill->payments as $payment)
                            <tr>
                                <td>{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                                <td><code>{{ $payment->order_id }}</code></td>
                                <td>{{ $payment->formatted_amount }}</td>
                                <td>{{ $payment->payment_method_label }}</td>
                                <td>{!! $payment->status_badge !!}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <!-- Status -->
            @if(isset($bill))
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-info-circle me-2"></i>Status Tagihan
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="unpaid" {{ $bill->status == 'unpaid' ? 'selected' : '' }}>Belum Bayar</option>
                            <option value="partial" {{ $bill->status == 'partial' ? 'selected' : '' }}>Sebagian</option>
                            <option value="paid" {{ $bill->status == 'paid' ? 'selected' : '' }}>Lunas</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Terbayar</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" class="form-control" value="{{ number_format($bill->paid_amount, 0, ',', '.') }}" readonly>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sisa Tagihan</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" class="form-control text-danger" value="{{ number_format($bill->total_amount - $bill->paid_amount, 0, ',', '.') }}" readonly>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Invoice Number -->
            @if(isset($bill))
            <div class="card mb-4">
                <div class="card-body text-center">
                    <small class="text-muted">No. Invoice</small>
                    <h5 class="mb-0"><code>{{ $bill->invoice_number }}</code></h5>
                </div>
            </div>
            @endif

            <!-- Actions -->
            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100 mb-2">
                        <i class="fas fa-save me-2"></i>{{ isset($bill) ? 'Simpan Perubahan' : 'Simpan Tagihan' }}
                    </button>
                    <a href="{{ route('treasurer.bills.index') }}" class="btn btn-outline-secondary w-100">
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
// Auto-fill amount from bill type
document.getElementById('billType').addEventListener('change', function() {
    const amount = this.options[this.selectedIndex].dataset.amount;
    if (amount && !document.getElementById('totalAmount').value) {
        document.getElementById('totalAmount').value = amount;
    }
});
</script>
@endpush
