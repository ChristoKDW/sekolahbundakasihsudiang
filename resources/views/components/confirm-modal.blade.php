@props([
    'id' => 'deleteModal',
    'title' => 'Konfirmasi Hapus',
    'message' => 'Apakah Anda yakin ingin menghapus item ini?',
    'action' => '',
    'method' => 'DELETE',
    'buttonText' => 'Hapus',
    'buttonClass' => 'btn-danger'
])

<div class="modal fade" id="{{ $id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ $action }}" method="POST" id="{{ $id }}-form">
                @csrf
                @method($method)
                <div class="modal-body">
                    <p>{{ $message }}</p>
                    {{ $slot }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn {{ $buttonClass }}">
                        <i class="fas fa-trash me-2"></i>{{ $buttonText }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
