{{-- Form Partial for Bill Create/Edit --}}
<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-file-invoice me-2 text-primary"></i>Detail Tagihan</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Siswa <span class="text-danger">*</span></label>
                        <select name="student_id" class="form-select @error('student_id') is-invalid @enderror" required {{ isset($bill) ? 'disabled' : '' }}>
                            <option value="">Pilih Siswa</option>
                            @foreach($students as $student)
                            <option value="{{ $student->id }}" 
                                    {{ old('student_id', $bill->student_id ?? request('student_id')) == $student->id ? 'selected' : '' }}>
                                {{ $student->nis }} - {{ $student->name }} ({{ $student->class }})
                            </option>
                            @endforeach
                        </select>
                        @if(isset($bill))
                        <input type="hidden" name="student_id" value="{{ $bill->student_id }}">
                        @endif
                        @error('student_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Jenis Tagihan <span class="text-danger">*</span></label>
                        <select name="bill_type_id" id="billType" class="form-select @error('bill_type_id') is-invalid @enderror" required {{ isset($bill) ? 'disabled' : '' }}>
                            <option value="">Pilih Jenis</option>
                            @foreach($billTypes as $type)
                            <option value="{{ $type->id }}" 
                                    data-amount="{{ $type->amount }}"
                                    data-flexible="{{ $type->is_flexible ? 1 : 0 }}"
                                    {{ old('bill_type_id', $bill->bill_type_id ?? '') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}{{ !$type->is_flexible ? ' - Rp ' . number_format($type->amount, 0, ',', '.') : ' (Fleksibel)' }}
                            </option>
                            @endforeach
                        </select>
                        @if(isset($bill))
                        <input type="hidden" name="bill_type_id" value="{{ $bill->bill_type_id }}">
                        @endif
                        @error('bill_type_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3" id="amountWrapper">
                        <label class="form-label">Jumlah Tagihan <span class="text-danger" id="amountRequired">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="amount" id="amountInput" 
                                   class="form-control @error('amount') is-invalid @enderror" 
                                   value="{{ old('amount', $bill->amount ?? '') }}" 
                                   required min="0">
                        </div>
                        <small class="text-muted d-none" id="flexibleHint">Nominal fleksibel akan diisi saat pembayaran</small>
                        @error('amount')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Diskon</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="discount" id="discountInput" 
                                   class="form-control @error('discount') is-invalid @enderror" 
                                   value="{{ old('discount', $bill->discount ?? 0) }}" 
                                   min="0">
                        </div>
                        @error('discount')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Denda</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="fine" id="fineInput" 
                                   class="form-control @error('fine') is-invalid @enderror" 
                                   value="{{ old('fine', $bill->fine ?? 0) }}" 
                                   min="0">
                        </div>
                        @error('fine')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Jatuh Tempo <span class="text-danger">*</span></label>
                        <input type="date" name="due_date" class="form-control @error('due_date') is-invalid @enderror" 
                               value="{{ old('due_date', isset($bill) && $bill->due_date ? $bill->due_date->format('Y-m-d') : '') }}" required>
                        @error('due_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tahun Ajaran <span class="text-danger">*</span></label>
                        <input type="text" name="academic_year" class="form-control @error('academic_year') is-invalid @enderror" 
                               value="{{ old('academic_year', $bill->academic_year ?? date('Y').'/'.(date('Y')+1)) }}" required
                               placeholder="2024/2025">
                        @error('academic_year')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Bulan (untuk SPP)</label>
                    <input type="text" name="month" class="form-control @error('month') is-invalid @enderror" 
                           value="{{ old('month', $bill->month ?? '') }}" placeholder="Januari 2024">
                    @error('month')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Catatan</label>
                    <textarea name="notes" rows="3" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $bill->notes ?? '') }}</textarea>
                    @error('notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Summary -->
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-calculator me-2 text-primary"></i>Ringkasan</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td>Jumlah:</td>
                        <td class="text-end fw-bold" id="summaryAmount">Rp 0</td>
                    </tr>
                    <tr>
                        <td>Diskon:</td>
                        <td class="text-end text-danger" id="summaryDiscount">- Rp 0</td>
                    </tr>
                    <tr>
                        <td>Denda:</td>
                        <td class="text-end text-warning" id="summaryFine">+ Rp 0</td>
                    </tr>
                    <tr class="border-top">
                        <td class="fw-bold">Total:</td>
                        <td class="text-end fw-bold text-primary fs-5" id="summaryTotal">Rp 0</td>
                    </tr>
                </table>
            </div>
        </div>

        @if(isset($bill))
        <!-- Status -->
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2 text-primary"></i>Status Tagihan</h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span>Status:</span>
                    @php
                        $statusColors = [
                            'pending' => 'warning',
                            'partial' => 'info',
                            'paid' => 'success',
                            'overdue' => 'danger',
                            'cancelled' => 'secondary'
                        ];
                        $statusLabels = [
                            'pending' => 'Belum Bayar',
                            'partial' => 'Sebagian',
                            'paid' => 'Lunas',
                            'overdue' => 'Jatuh Tempo',
                            'cancelled' => 'Dibatalkan'
                        ];
                    @endphp
                    <span class="badge bg-{{ $statusColors[$bill->status] ?? 'secondary' }}">
                        {{ $statusLabels[$bill->status] ?? $bill->status }}
                    </span>
                </div>
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span>Sudah Dibayar:</span>
                    <strong>Rp {{ number_format($bill->paid_amount, 0, ',', '.') }}</strong>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <span>Sisa:</span>
                    <strong class="text-danger">Rp {{ number_format($bill->total_amount - $bill->paid_amount, 0, ',', '.') }}</strong>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Toggle amount field based on flexible bill type
    function toggleAmountField() {
        const selected = $('#billType').find(':selected');
        const isFlexible = selected.data('flexible') == 1;
        const amount = selected.data('amount');
        
        if (isFlexible) {
            // Hide amount for flexible bill type
            $('#amountWrapper').find('input').prop('required', false);
            $('#amountWrapper').find('input').val(0);
            $('#amountWrapper').find('.input-group').hide();
            $('#amountRequired').hide();
            $('#flexibleHint').removeClass('d-none');
        } else {
            // Show amount and auto-fill for non-flexible bill type
            $('#amountWrapper').find('input').prop('required', true);
            $('#amountWrapper').find('.input-group').show();
            $('#amountRequired').show();
            $('#flexibleHint').addClass('d-none');
            if (amount) {
                $('#amountInput').val(amount);
            }
        }
        updateSummary();
    }

    // Auto-fill amount when bill type is selected
    $('#billType').on('change', toggleAmountField);

    // Update summary on input change
    $('#amountInput, #discountInput, #fineInput').on('input', updateSummary);

    function updateSummary() {
        const amount = parseFloat($('#amountInput').val()) || 0;
        const discount = parseFloat($('#discountInput').val()) || 0;
        const fine = parseFloat($('#fineInput').val()) || 0;
        const total = amount - discount + fine;

        $('#summaryAmount').text('Rp ' + amount.toLocaleString('id-ID'));
        $('#summaryDiscount').text('- Rp ' + discount.toLocaleString('id-ID'));
        $('#summaryFine').text('+ Rp ' + fine.toLocaleString('id-ID'));
        $('#summaryTotal').text('Rp ' + total.toLocaleString('id-ID'));
    }

    // Initial calculation
    updateSummary();
    
    // Apply toggle on page load (for edit mode)
    if ($('#billType').val()) {
        toggleAmountField();
    }
});
</script>
@endpush
