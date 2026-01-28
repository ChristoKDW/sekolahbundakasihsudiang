@extends('layouts.app')

@section('title', 'Notifikasi')
@section('page-title', 'Notifikasi')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-bell me-2"></i>Semua Notifikasi</span>
        @if($notifications->where('read_at', null)->count() > 0)
        <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-check-double me-1"></i>Tandai Semua Dibaca
            </button>
        </form>
        @endif
    </div>
    <div class="card-body p-0">
        @if($notifications->count() > 0)
        <div class="list-group list-group-flush">
            @foreach($notifications as $notification)
            <div class="list-group-item list-group-item-action {{ $notification->read_at ? '' : 'bg-light' }}">
                <div class="d-flex w-100 justify-content-between align-items-start">
                    <div class="d-flex align-items-start">
                        <div class="notification-icon me-3">
                            @switch($notification->data['type'] ?? 'info')
                                @case('payment')
                                    <div class="rounded-circle bg-success text-white p-2">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </div>
                                    @break
                                @case('bill')
                                    <div class="rounded-circle bg-warning text-white p-2">
                                        <i class="fas fa-file-invoice"></i>
                                    </div>
                                    @break
                                @case('reminder')
                                    <div class="rounded-circle bg-danger text-white p-2">
                                        <i class="fas fa-bell"></i>
                                    </div>
                                    @break
                                @default
                                    <div class="rounded-circle bg-info text-white p-2">
                                        <i class="fas fa-info"></i>
                                    </div>
                            @endswitch
                        </div>
                        <div>
                            <h6 class="mb-1">{{ $notification->data['title'] ?? 'Notifikasi' }}</h6>
                            <p class="mb-1 text-muted">{{ $notification->data['message'] ?? '' }}</p>
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>
                                {{ $notification->created_at->diffForHumans() }}
                            </small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        @if(!$notification->read_at)
                        <span class="badge bg-primary me-2">Baru</span>
                        @endif
                        <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-link text-danger" 
                                    onclick="return confirm('Hapus notifikasi ini?')">
                                <i class="fas fa-times"></i>
                            </button>
                        </form>
                    </div>
                </div>
                @if(isset($notification->data['action_url']))
                <a href="{{ $notification->data['action_url'] }}" class="btn btn-sm btn-outline-primary mt-2">
                    {{ $notification->data['action_text'] ?? 'Lihat Detail' }}
                </a>
                @endif
            </div>
            @endforeach
        </div>
        
        <div class="p-3">
            {{ $notifications->links() }}
        </div>
        @else
        <div class="text-center py-5">
            <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
            <h5>Tidak ada notifikasi</h5>
            <p class="text-muted">Anda akan menerima notifikasi terkait pembayaran dan tagihan di sini.</p>
        </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
.notification-icon .rounded-circle {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.list-group-item.bg-light {
    border-left: 3px solid var(--primary-color);
}
</style>
@endpush
