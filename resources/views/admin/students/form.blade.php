@extends('layouts.app')

@section('title', isset($student) ? 'Edit Siswa' : 'Tambah Siswa')

@section('content')
<div class="page-header">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.students.index') }}">Siswa</a></li>
            <li class="breadcrumb-item active">{{ isset($student) ? 'Edit' : 'Tambah' }}</li>
        </ol>
    </nav>
    <h1 class="page-title">{{ isset($student) ? 'Edit Data Siswa' : 'Tambah Siswa Baru' }}</h1>
</div>

<form action="{{ isset($student) ? route('admin.students.update', $student) : route('admin.students.store') }}" method="POST">
    @csrf
    @if(isset($student))
        @method('PUT')
    @endif

    <div class="row">
        <div class="col-lg-8">
            <!-- Data Siswa -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-user me-2"></i>Data Siswa
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">NIS <span class="text-danger">*</span></label>
                            <input type="text" name="nis" class="form-control @error('nis') is-invalid @enderror" 
                                   value="{{ old('nis', $student->nis ?? '') }}" required>
                            @error('nis')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">NISN</label>
                            <input type="text" name="nisn" class="form-control @error('nisn') is-invalid @enderror" 
                                   value="{{ old('nisn', $student->nisn ?? '') }}">
                            @error('nisn')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name', $student->name ?? '') }}" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                                <option value="">Pilih</option>
                                <option value="L" {{ old('gender', $student->gender ?? '') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('gender', $student->gender ?? '') == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                            @error('gender')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Kelas <span class="text-danger">*</span></label>
                            <select name="class" class="form-select @error('class') is-invalid @enderror" required>
                                <option value="">Pilih Kelas</option>
                                @foreach(['VII-A', 'VII-B', 'VII-C', 'VIII-A', 'VIII-B', 'VIII-C', 'IX-A', 'IX-B', 'IX-C'] as $class)
                                <option value="{{ $class }}" {{ old('class', $student->class ?? '') == $class ? 'selected' : '' }}>{{ $class }}</option>
                                @endforeach
                            </select>
                            @error('class')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tahun Masuk <span class="text-danger">*</span></label>
                            <select name="entry_year" class="form-select @error('entry_year') is-invalid @enderror" required>
                                @for($year = date('Y'); $year >= 2015; $year--)
                                <option value="{{ $year }}" {{ old('entry_year', $student->entry_year ?? date('Y')) == $year ? 'selected' : '' }}>{{ $year }}</option>
                                @endfor
                            </select>
                            @error('entry_year')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tempat Lahir</label>
                            <input type="text" name="birth_place" class="form-control @error('birth_place') is-invalid @enderror" 
                                   value="{{ old('birth_place', $student->birth_place ?? '') }}">
                            @error('birth_place')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" name="birth_date" class="form-control @error('birth_date') is-invalid @enderror" 
                                   value="{{ old('birth_date', isset($student) && $student->birth_date ? $student->birth_date->format('Y-m-d') : '') }}">
                            @error('birth_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="address" rows="2" class="form-control @error('address') is-invalid @enderror">{{ old('address', $student->address ?? '') }}</textarea>
                        @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Data Orang Tua -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-users me-2"></i>Hubungkan Orang Tua
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Pilih Orang Tua</label>
                        <select name="parent_ids[]" class="form-select select2" multiple>
                            @foreach($parents as $parent)
                            <option value="{{ $parent->id }}" 
                                    {{ in_array($parent->id, old('parent_ids', isset($student) ? $student->parents->pluck('id')->toArray() : [])) ? 'selected' : '' }}>
                                {{ $parent->user->name }} ({{ $parent->user->email }})
                            </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Bisa memilih lebih dari satu orang tua/wali</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Status -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-toggle-on me-2"></i>Status
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Status Siswa <span class="text-danger">*</span></label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="active" {{ old('status', $student->status ?? 'active') == 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ old('status', $student->status ?? '') == 'inactive' ? 'selected' : '' }}>Non-Aktif</option>
                            <option value="graduated" {{ old('status', $student->status ?? '') == 'graduated' ? 'selected' : '' }}>Lulus</option>
                        </select>
                        @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Catatan -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-sticky-note me-2"></i>Catatan
                </div>
                <div class="card-body">
                    <textarea name="notes" rows="4" class="form-control" placeholder="Catatan tambahan...">{{ old('notes', $student->notes ?? '') }}</textarea>
                </div>
            </div>

            <!-- Aksi -->
            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100 mb-2">
                        <i class="fas fa-save me-2"></i>{{ isset($student) ? 'Simpan Perubahan' : 'Simpan Siswa' }}
                    </button>
                    <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
