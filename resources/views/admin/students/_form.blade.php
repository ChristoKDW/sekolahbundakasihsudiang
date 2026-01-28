{{-- Form Partial for Student Create/Edit --}}
<div class="row">
    <div class="col-lg-8">
        <!-- Data Siswa -->
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-user me-2 text-primary"></i>Data Siswa</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">NIS <span class="text-danger">*</span></label>
                        <input type="text" name="nis" class="form-control @error('nis') is-invalid @enderror" 
                               value="{{ old('nis', $student->nis ?? '') }}" required>
                        @error('nis')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">NISN</label>
                        <input type="text" name="nisn" class="form-control @error('nisn') is-invalid @enderror" 
                               value="{{ old('nisn', $student->nisn ?? '') }}">
                        @error('nisn')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Jenjang Pendidikan <span class="text-danger">*</span></label>
                        <select name="education_level" id="educationLevel" class="form-select @error('education_level') is-invalid @enderror" required>
                            <option value="">Pilih Jenjang</option>
                            @foreach(\App\Models\Student::getEducationLevels() as $key => $label)
                            <option value="{{ $key }}" {{ old('education_level', $student->education_level ?? '') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('education_level')
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
                        <select name="class" id="classSelect" class="form-select @error('class') is-invalid @enderror" required>
                            <option value="">Pilih Kelas</option>
                        </select>
                        @error('class')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Jurusan</label>
                        <input type="text" name="major" class="form-control @error('major') is-invalid @enderror" 
                               value="{{ old('major', $student->major ?? '') }}" placeholder="Contoh: Teknik Komputer">
                        @error('major')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tempat Lahir <span class="text-danger">*</span></label>
                        <input type="text" name="place_of_birth" class="form-control @error('place_of_birth') is-invalid @enderror" 
                               value="{{ old('place_of_birth', $student->place_of_birth ?? '') }}" required>
                        @error('place_of_birth')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                        <input type="date" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror" 
                               value="{{ old('date_of_birth', isset($student) && $student->date_of_birth ? $student->date_of_birth->format('Y-m-d') : '') }}" required>
                        @error('date_of_birth')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Alamat <span class="text-danger">*</span></label>
                    <textarea name="address" rows="2" class="form-control @error('address') is-invalid @enderror" required>{{ old('address', $student->address ?? '') }}</textarea>
                    @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">No. Telepon</label>
                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                           value="{{ old('phone', $student->phone ?? '') }}" placeholder="08xxxxxxxxxx">
                    @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Hubungkan Orang Tua -->
        @if(isset($parents) && $parents->count() > 0)
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-users me-2 text-primary"></i>Hubungkan Orang Tua</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Pilih Orang Tua</label>
                    <select name="parent_ids[]" class="form-select" multiple>
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
        @endif
    </div>

    <div class="col-lg-4">
        <!-- Status -->
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-toggle-on me-2 text-primary"></i>Status</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Status Siswa <span class="text-danger">*</span></label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="active" {{ old('status', $student->status ?? 'active') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ old('status', $student->status ?? '') == 'inactive' ? 'selected' : '' }}>Non-Aktif</option>
                        <option value="graduated" {{ old('status', $student->status ?? '') == 'graduated' ? 'selected' : '' }}>Lulus</option>
                        <option value="dropout" {{ old('status', $student->status ?? '') == 'dropout' ? 'selected' : '' }}>Keluar</option>
                    </select>
                    @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Foto -->
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-camera me-2 text-primary"></i>Foto Siswa</h6>
            </div>
            <div class="card-body text-center">
                @if(isset($student) && $student->photo)
                <img src="{{ asset('storage/' . $student->photo) }}" class="img-thumbnail mb-3" style="max-width: 150px;">
                @else
                <div class="bg-light rounded p-4 mb-3">
                    <i class="fas fa-user fa-3x text-muted"></i>
                </div>
                @endif
                <input type="file" name="photo" class="form-control @error('photo') is-invalid @enderror" accept="image/*">
                <small class="text-muted">Format: JPG, PNG. Max: 2MB</small>
                @error('photo')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Class options by education level
const classOptions = {
    'TK': ['TK-A', 'TK-B'],
    'SD': ['1', '2', '3', '4', '5', '6'],
    'SMP': ['VII', 'VIII', 'IX'],
    'SMA': ['X-IPA', 'X-IPS', 'XI-IPA', 'XI-IPS', 'XII-IPA', 'XII-IPS', 'X-TKJ', 'X-RPL', 'X-AKL', 'XI-TKJ', 'XI-RPL', 'XI-AKL', 'XII-TKJ', 'XII-RPL', 'XII-AKL']
};

const educationLevelSelect = document.getElementById('educationLevel');
const classSelect = document.getElementById('classSelect');
const currentClass = '{{ old('class', $student->class ?? '') }}';

function updateClassOptions() {
    const level = educationLevelSelect.value;
    classSelect.innerHTML = '<option value="">Pilih Kelas</option>';
    
    if (level && classOptions[level]) {
        classOptions[level].forEach(cls => {
            const option = document.createElement('option');
            option.value = cls;
            option.textContent = cls;
            if (cls === currentClass) {
                option.selected = true;
            }
            classSelect.appendChild(option);
        });
    }
}

educationLevelSelect.addEventListener('change', updateClassOptions);

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateClassOptions();
});
</script>
@endpush