@props([
    'status' => 'pending',
    'size' => 'md'
])

@php
    $badges = [
        'unpaid' => ['class' => 'warning', 'label' => 'Belum Bayar'],
        'partial' => ['class' => 'info', 'label' => 'Sebagian'],
        'paid' => ['class' => 'success', 'label' => 'Lunas'],
        'pending' => ['class' => 'warning', 'label' => 'Pending'],
        'processing' => ['class' => 'info', 'label' => 'Proses'],
        'success' => ['class' => 'success', 'label' => 'Berhasil'],
        'failed' => ['class' => 'danger', 'label' => 'Gagal'],
        'expired' => ['class' => 'secondary', 'label' => 'Kadaluarsa'],
        'active' => ['class' => 'success', 'label' => 'Aktif'],
        'inactive' => ['class' => 'secondary', 'label' => 'Non-Aktif'],
        'graduated' => ['class' => 'info', 'label' => 'Lulus'],
    ];
    
    $badge = $badges[$status] ?? ['class' => 'secondary', 'label' => ucfirst($status)];
    $sizeClass = $size === 'lg' ? 'fs-6' : ($size === 'sm' ? 'small' : '');
@endphp

<span class="badge bg-{{ $badge['class'] }} {{ $sizeClass }}">{{ $badge['label'] }}</span>
