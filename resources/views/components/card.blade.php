@props([
    'title' => '',
    'icon' => null,
    'actions' => null,
    'footer' => null,
    'noPadding' => false
])

<div {{ $attributes->merge(['class' => 'card']) }}>
    @if($title || $icon || $actions)
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>
            @if($icon)<i class="fas fa-{{ $icon }} me-2"></i>@endif
            {{ $title }}
        </span>
        @if($actions)
        <div>{{ $actions }}</div>
        @endif
    </div>
    @endif
    
    <div class="card-body {{ $noPadding ? 'p-0' : '' }}">
        {{ $slot }}
    </div>
    
    @if($footer)
    <div class="card-footer">
        {{ $footer }}
    </div>
    @endif
</div>
