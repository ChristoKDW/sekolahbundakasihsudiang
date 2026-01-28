@extends('layouts.app')

@section('title', 'Edit Tagihan')
@section('page-title', 'Edit Tagihan')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <div class="d-flex align-items-center">
                    <a href="{{ route('treasurer.bills.index') }}" class="btn btn-outline-secondary btn-sm me-3">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h5 class="mb-0"><i class="fas fa-edit me-2 text-primary"></i>Edit Tagihan: {{ $bill->bill_number }}</h5>
                </div>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('treasurer.bills.update', $bill) }}" method="POST">
                    @csrf
                    @method('PUT')
                    @include('treasurer.bills._form')
                    
                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <a href="{{ route('treasurer.bills.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i>Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
