@props([
    'type' => 'primary',
    'value' => 0,
    'label' => '',
    'icon' => null,
    'prefix' => '',
    'suffix' => '',
    'trend' => null,
    'trendValue' => null
])

<div class="stat-card">
    <div class="stat-icon {{ $type }}">
        <i class="fas fa-{{ $icon ?? 'chart-bar' }}"></i>
    </div>
    <div class="stat-content">
        <div class="stat-value">{{ $prefix }}{{ is_numeric($value) ? number_format($value) : $value }}{{ $suffix }}</div>
        <div class="stat-label">{{ $label }}</div>
        @if($trend !== null)
        <div class="stat-trend text-{{ $trend >= 0 ? 'success' : 'danger' }} small">
            <i class="fas fa-arrow-{{ $trend >= 0 ? 'up' : 'down' }}"></i>
            {{ abs($trend) }}% dari bulan lalu
        </div>
        @endif
    </div>
    {{ $slot }}
</div>
