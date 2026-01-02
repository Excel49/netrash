@extends('layouts.app')

@section('title', 'Notifikasi')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">Notifikasi</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ Auth::user()->isPetugas() ? route('petugas.dashboard') : route('warga.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Notifikasi</li>
                    </ol>
                </div>
            </div>
            <p class="text-muted">Riwayat notifikasi Anda</p>
        </div>
    </div>

    <!-- Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    @if(Auth::user()->isPetugas())
                    <a href="{{ route('notifikasi.create') }}" class="btn btn-primary me-2">
                        <i class="bi bi-send me-2"></i>Kirim Notifikasi
                    </a>
                    <a href="{{ route('notifikasi.sent') }}" class="btn btn-outline-primary me-2">
                        <i class="bi bi-envelope-check me-2"></i>Terkirim
                    </a>
                    @endif
                </div>
                <div>
                    @if($notifikasi->where('is_read', false)->count() > 0)
                    <button class="btn btn-outline-success me-2" onclick="markAllAsRead()">
                        <i class="bi bi-check-all me-2"></i>Tandai Semua Dibaca
                    </button>
                    @endif
                    <button class="btn btn-outline-danger" onclick="clearAllNotifications()">
                        <i class="bi bi-trash me-2"></i>Hapus Semua
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($notifikasi as $notif)
                        <div class="list-group-item list-group-item-action border-0 py-3 {{ !$notif->is_read ? 'bg-light' : '' }}">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar-sm bg-{{ $notif->color }} rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi {{ $notif->icon }} text-white"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <h6 class="mb-0">{{ $notif->judul }}</h6>
                                        <div class="d-flex align-items-center">
                                            @if(!$notif->is_read)
                                            <span class="badge bg-primary me-2">Baru</span>
                                            @endif
                                            <small class="text-muted">{{ $notif->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                    <p class="text-muted mb-2">{{ $notif->pesan }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="bi bi-person me-1"></i>Dari: {{ $notif->sender_name }} ({{ $notif->sender_role }})
                                        </small>
                                        <div class="btn-group btn-group-sm">
                                            @if(!$notif->is_read)
                                            <button class="btn btn-outline-primary btn-sm" onclick="markAsRead({{ $notif->id }})">
                                                <i class="bi bi-check"></i> Tandai Dibaca
                                            </button>
                                            @endif
                                            <button class="btn btn-outline-danger btn-sm" onclick="deleteNotification({{ $notif->id }})">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="list-group-item text-center py-5">
                            <i class="bi bi-bell display-4 text-muted mb-3"></i>
                            <h5 class="text-muted">Tidak ada notifikasi</h5>
                            <p class="text-muted mb-0">Notifikasi baru akan muncul di sini</p>
                        </div>
                        @endforelse
                    </div>
                </div>
                
                @if($notifikasi->hasPages())
                <div class="card-footer">
                    {{ $notifikasi->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Mark as read
function markAsRead(id) {
    fetch(`/notifikasi/${id}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

// Mark all as read
function markAllAsRead() {
    if (confirm('Tandai semua notifikasi sebagai sudah dibaca?')) {
        fetch('/notifikasi/read-all', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}

// Delete notification
function deleteNotification(id) {
    if (confirm('Hapus notifikasi ini?')) {
        fetch(`/notifikasi/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}

// Clear all notifications
function clearAllNotifications() {
    if (confirm('Hapus semua notifikasi? Tindakan ini tidak dapat dibatalkan.')) {
        fetch('/notifikasi/clear-all', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}
</script>
@endpush
@endsection