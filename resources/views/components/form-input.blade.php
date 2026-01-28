@props([
    'label' => '',
    'name' => '',
    'type' => 'text',
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'readonly' => false,
    'disabled' => false,
    'help' => null,
    'prefix' => null,
    'suffix' => null,
    'rows' => 3
])

<div class="mb-3">
    @if($label)
    <label for="{{ $name }}" class="form-label">
        {{ $label }}
        @if($required)<span class="text-danger">*</span>@endif
    </label>
    @endif
    
    @if($prefix || $suffix)
    <div class="input-group">
        @if($prefix)
        <span class="input-group-text">{{ $prefix }}</span>
        @endif
    @endif
    
    @if($type === 'textarea')
    <textarea 
        name="{{ $name }}" 
        id="{{ $name }}" 
        rows="{{ $rows }}"
        class="form-control @error($name) is-invalid @enderror"
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        {{ $readonly ? 'readonly' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes }}
    >{{ old($name, $value) }}</textarea>
    @elseif($type === 'select')
    <select 
        name="{{ $name }}" 
        id="{{ $name }}" 
        class="form-select @error($name) is-invalid @enderror"
        {{ $required ? 'required' : '' }}
        {{ $readonly ? 'readonly' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes }}
    >
        {{ $slot }}
    </select>
    @else
    <input 
        type="{{ $type }}" 
        name="{{ $name }}" 
        id="{{ $name }}" 
        class="form-control @error($name) is-invalid @enderror"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        {{ $readonly ? 'readonly' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes }}
    >
    @endif
    
    @if($prefix || $suffix)
        @if($suffix)
        <span class="input-group-text">{{ $suffix }}</span>
        @endif
    </div>
    @endif
    
    @error($name)
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    
    @if($help)
    <small class="text-muted">{{ $help }}</small>
    @endif
</div>
