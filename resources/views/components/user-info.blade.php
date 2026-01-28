@props([
    'name' => '',
    'avatar' => null,
    'subtitle' => null,
    'size' => 'md'
])

@php
    $sizes = [
        'sm' => ['avatar' => '32px', 'font' => '0.75rem'],
        'md' => ['avatar' => '40px', 'font' => '0.875rem'],
        'lg' => ['avatar' => '60px', 'font' => '1.25rem'],
    ];
    $sizeConfig = $sizes[$size] ?? $sizes['md'];
@endphp

<div class="d-flex align-items-center">
    <div class="user-avatar me-{{ $size === 'lg' ? '3' : '2' }}" 
         style="width: {{ $sizeConfig['avatar'] }}; height: {{ $sizeConfig['avatar'] }}; font-size: {{ $sizeConfig['font'] }};">
        @if($avatar)
        <img src="{{ $avatar }}" alt="{{ $name }}" class="rounded-circle w-100 h-100">
        @else
        {{ substr($name, 0, 1) }}
        @endif
    </div>
    <div>
        <strong>{{ $name }}</strong>
        @if($subtitle)
        <small class="d-block text-muted">{{ $subtitle }}</small>
        @endif
        {{ $slot }}
    </div>
</div>
