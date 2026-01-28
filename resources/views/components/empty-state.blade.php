@props([
    'icon' => 'folder-open',
    'title' => 'Tidak ada data',
    'message' => '',
    'action' => null,
    'actionUrl' => '#',
    'actionText' => 'Tambah Baru'
])

<div class="empty-state text-center py-5">
    <i class="fas fa-{{ $icon }} fa-3x mb-3 text-muted"></i>
    <h5>{{ $title }}</h5>
    @if($message)
    <p class="text-muted">{{ $message }}</p>
    @endif
    @if($action)
    <a href="{{ $actionUrl }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>{{ $actionText }}
    </a>
    @endif
    {{ $slot }}
</div>
