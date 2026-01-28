@extends('layouts.app')

@section('title', 'Jenis Tagihan')
@section('page-title', 'Manajemen Jenis Tagihan')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-tags me-2"></i>Daftar Jenis Tagihan</span>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#billTypeModal">
            <i class="fas fa-plus me-2"></i>Tambah Jenis
        </button>
    </div>
    <div class="card-body">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        
        <div class="table-responsive">
            <table class="table table-hover" id="billTypesTable">
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>Nama Jenis Tagihan</th>
                        <th>Deskripsi</th>
                        <th>Nominal</th>
                        <th>Jenis</th>
                        <th>Tipe</th>
                        <th>Status</th>
                        <th>Jumlah Tagihan</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($billTypes as $index => $type)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $type->name }}</strong>
                        </td>
                        <td>{{ $type->description ?? '-' }}</td>
                        <td>
                            @if($type->amount)
                            Rp {{ number_format($type->amount, 0, ',', '.') }}
                            @if($type->is_flexible)
                            <br><span class="badge bg-success"><i class="fas fa-hand-holding-usd me-1"></i>Fleksibel</span>
                            @endif
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($type->is_mandatory)
                            <span class="badge bg-danger">Wajib</span>
                            @else
                            <span class="badge bg-info">Sukarela</span>
                            @endif
                        </td>
                        <td>
                            @if($type->is_recurring)
                            <span class="badge bg-primary">Berulang</span>
                            @else
                            <span class="badge bg-secondary">Sekali Bayar</span>
                            @endif
                        </td>
                        <td>
                            @if($type->is_active)
                            <span class="badge bg-success">Aktif</span>
                            @else
                            <span class="badge bg-danger">Non-Aktif</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-light text-dark">{{ $type->bills_count ?? 0 }} Tagihan</span>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-warning edit-btn"
                                    data-id="{{ $type->id }}"
                                    data-name="{{ $type->name }}"
                                    data-description="{{ $type->description }}"
                                    data-amount="{{ $type->amount }}"
                                    data-mandatory="{{ $type->is_mandatory ? '1' : '0' }}"
                                    data-flexible="{{ $type->is_flexible ? '1' : '0' }}"
                                    data-recurring="{{ $type->is_recurring ? '1' : '0' }}"
                                    data-active="{{ $type->is_active ? '1' : '0' }}"
                                    data-bs-toggle="modal" data-bs-target="#billTypeModal">
                                <i class="fas fa-edit"></i>
                            </button>
                            @if(($type->bills_count ?? 0) == 0)
                            <form action="{{ route('treasurer.bill-types.destroy', $type->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" 
                                        onclick="return confirm('Hapus jenis tagihan ini?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Belum ada jenis tagihan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Bill Type Modal -->
<div class="modal fade" id="billTypeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">
                    <i class="fas fa-plus me-2"></i>Tambah Jenis Tagihan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="billTypeForm" action="{{ route('treasurer.bill-types.store') }}" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Jenis Tagihan <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="typeName" class="form-control" required 
                               placeholder="Contoh: SPP Bulanan">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" id="typeDescription" class="form-control" rows="2"
                                  placeholder="Deskripsi singkat jenis tagihan"></textarea>
                    </div>
                    
                    <div class="mb-3" id="amountWrapper">
                        <label class="form-label">Nominal <span class="text-danger" id="amountRequired">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="amount" id="typeAmount" class="form-control" 
                                   min="0" placeholder="0" required>
                        </div>
                        <small class="text-muted" id="amountHint">Nominal yang akan otomatis terisi saat membuat tagihan</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            <i class="fas fa-exclamation-circle text-warning me-1"></i>Jenis Pembayaran <span class="text-danger">*</span>
                        </label>
                        <div class="mt-2">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="is_mandatory" id="typeMandatoryYes" value="1" checked>
                                <label class="form-check-label" for="typeMandatoryYes">
                                    <span class="badge bg-danger">Wajib</span>
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="is_mandatory" id="typeMandatoryNo" value="0">
                                <label class="form-check-label" for="typeMandatoryNo">
                                    <span class="badge bg-info">Sukarela</span>
                                </label>
                            </div>
                        </div>
                        <small class="text-muted d-block mt-1">
                            <strong>Wajib:</strong> Harus dibayar (SPP, Uang Gedung) | 
                            <strong>Sukarela:</strong> Opsional (Infaq, Donasi)
                        </small>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="is_flexible" id="typeFlexible" class="form-check-input" value="1">
                                <label class="form-check-label" for="typeFlexible">Nominal Fleksibel</label>
                            </div>
                            <small class="text-muted">Orang tua bayar sesuai kemampuan</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="is_recurring" id="typeRecurring" class="form-check-input" value="1">
                                <label class="form-check-label" for="typeRecurring">Tagihan Berulang</label>
                            </div>
                            <small class="text-muted">Tagihan berulang setiap bulan</small>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="is_active" id="typeActive" class="form-check-input" value="1" checked>
                            <label class="form-check-label" for="typeActive">Status Aktif</label>
                        </div>
                        <small class="text-muted">Jenis tagihan yang aktif dapat digunakan</small>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-save me-2"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#billTypesTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        },
        order: [[1, 'asc']]
    });

    // Toggle amount field based on flexible checkbox
    function toggleAmountField() {
        if ($('#typeFlexible').is(':checked')) {
            $('#amountWrapper').hide();
            $('#typeAmount').prop('required', false);
            $('#typeAmount').val(0);
        } else {
            $('#amountWrapper').show();
            $('#typeAmount').prop('required', true);
        }
    }

    // Listen to flexible checkbox change
    $('#typeFlexible').on('change', toggleAmountField);
    
    // Reset modal on close
    $('#billTypeModal').on('hidden.bs.modal', function() {
        $('#billTypeForm')[0].reset();
        $('#billTypeForm').attr('action', '{{ route("treasurer.bill-types.store") }}');
        $('#formMethod').val('POST');
        $('#modalTitle').html('<i class="fas fa-plus me-2"></i>Tambah Jenis Tagihan');
        $('#submitBtn').html('<i class="fas fa-save me-2"></i>Simpan');
        $('#typeActive').prop('checked', true);
        $('#typeMandatoryYes').prop('checked', true);
        $('#typeMandatoryNo').prop('checked', false);
        $('#typeFlexible').prop('checked', false);
        $('#amountWrapper').show();
        $('#typeAmount').prop('required', true);
    });
    
    // Edit button click
    $('.edit-btn').on('click', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const description = $(this).data('description');
        const amount = $(this).data('amount');
        const mandatory = $(this).data('mandatory');
        const flexible = $(this).data('flexible');
        const recurring = $(this).data('recurring');
        const active = $(this).data('active');
        
        $('#billTypeForm').attr('action', '{{ url("treasurer/bill-types") }}/' + id);
        $('#formMethod').val('PUT');
        $('#modalTitle').html('<i class="fas fa-edit me-2"></i>Edit Jenis Tagihan');
        $('#submitBtn').html('<i class="fas fa-save me-2"></i>Update');
        
        $('#typeName').val(name);
        $('#typeDescription').val(description);
        $('#typeAmount').val(amount);
        
        // Set mandatory radio
        if (mandatory == 1 || mandatory == true) {
            $('#typeMandatoryYes').prop('checked', true);
            $('#typeMandatoryNo').prop('checked', false);
        } else {
            $('#typeMandatoryYes').prop('checked', false);
            $('#typeMandatoryNo').prop('checked', true);
        }
        
        $('#typeFlexible').prop('checked', flexible == 1);
        $('#typeRecurring').prop('checked', recurring == 1);
        $('#typeActive').prop('checked', active == 1);

        // Toggle amount field after setting values
        toggleAmountField();
    });
});
</script>
@endpush
