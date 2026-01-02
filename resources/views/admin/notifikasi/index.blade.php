@extends('layouts.app')

@section('title', 'Notifikasi')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">Notifikasi</h2>
                <div>
                    <button type="button" class="btn btn-netra-outline me-2" id="markAllReadBtn">
                        <i class="bi bi-check-all me-2"></i>Tandai Semua Dibaca
                    </button>
                    <button type="button" class="btn btn-outline-danger" id="clearAllBtn">
                        <i class="bi bi-trash me-2"></i>Hapus Semua
                    </button>
                </div>
            </div>
            <p class="text-muted">Daftar semua notifikasi Anda</p>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="card mb-4">
        <div class="card-body py-3">
            <div class="d-flex">
                <a href="{{ route('notifikasi.index') }}" 
                   class="text-decoration-none me-4 {{ request()->is('notifikasi') ? 'text-netra' : 'text-muted' }}">
                    <i class="bi bi-bell me-1"></i> Semua
                </a>
                <a href="{{ route('notifikasi.unread') }}" 
                   class="text-decoration-none me-4 {{ request()->is('notifikasi/unread') ? 'text-netra' : 'text-muted' }}">
                    <i class="bi bi-bell-fill me-1"></i> Belum Dibaca
                </a>
                @if(auth()->user()->isPetugas())
                <a href="{{ route('notifikasi.create') }}" 
                   class="text-decoration-none me-4 {{ request()->is('notifikasi/create') ? 'text-netra' : 'text-muted' }}">
                    <i class="bi bi-send me-1"></i> Kirim Notifikasi
                </a>
                <a href="{{ route('notifikasi.sent') }}" 
                   class="text-decoration-none {{ request()->is('notifikasi/sent') ? 'text-netra' : 'text-muted' }}">
                    <i class="bi bi-send-check me-1"></i> Terkirim
                </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Notification List -->
    <div class="card">
        <div class="card-body p-0">
            @forelse($notifikasi as $notif)
            <div class="notification-item border-bottom {{ !$notif->is_read ? 'bg-light' : '' }}">
                <div class="p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center mb-1">
                                <span class="badge bg-{{ $notif->color }} me-2">{{ ucfirst($notif->tipe) }}</span>
                                <small class="text-muted">{{ $notif->created_at->diffForHumans() }}</small>
                                @if(!$notif->is_read)
                                <span class="badge bg-primary ms-2">Baru</span>
                                @endif
                            </div>
                            <h6 class="mb-2">{{ $notif->judul }}</h6>
                            <p class="mb-0">{{ $notif->pesan }}</p>
                            
                            @if($notif->sender_name !== 'Sistem')
                            <small class="text-muted">
                                Dari: {{ $notif->sender_name }} ({{ $notif->sender_role }})
                            </small>
                            @endif
                        </div>
                        <div class="flex-shrink-0 ms-3">
                            <div class="btn-group btn-group-sm">
                                @if(!$notif->is_read)
                                <button type="button" 
                                        class="btn btn-outline-primary mark-read-btn" 
                                        data-id="{{ $notif->id }}">
                                    <i class="bi bi-check"></i>
                                </button>
                                @endif
                                <button type="button" 
                                        class="btn btn-outline-danger delete-btn" 
                                        data-id="{{ $notif->id }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-5">
                <i class="bi bi-bell-slash display-4 text-muted mb-3"></i>
                <p class="text-muted mb-0">Tidak ada notifikasi</p>
            </div>
            @endforelse
        </div>
        
        <!-- Pagination -->
        @if($notifikasi->hasPages())
        <div class="card-footer">
            {{ $notifikasi->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Mark as read
    $('.mark-read-btn').click(function() {
        const id = $(this).data('id');
        const btn = $(this);
        
        $.ajax({
            url: `/notifikasi/${id}/read`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    btn.closest('.notification-item').removeClass('bg-light');
                    btn.remove();
                    
                    // Update badge count if exists
                    const badge = $('#notificationBadge');
                    if (badge.length) {
                        let count = parseInt(badge.text());
                        if (count > 0) {
                            badge.text(count - 1);
                            if (count - 1 === 0) {
                                badge.remove();
                            }
                        }
                    }
                }
            },
            error: function(xhr) {
                alert('Gagal menandai notifikasi sebagai dibaca');
            }
        });
    });
    
    // Delete notification
    $('.delete-btn').click(function() {
        const id = $(this).data('id');
        const item = $(this).closest('.notification-item');
        
        if (confirm('Apakah Anda yakin ingin menghapus notifikasi ini?')) {
            $.ajax({
                url: `/notifikasi/${id}`,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        item.remove();
                        
                        // Check if list is empty
                        if ($('.notification-item').length === 0) {
                            $('.card-body').html(`
                                <div class="text-center py-5">
                                    <i class="bi bi-bell-slash display-4 text-muted mb-3"></i>
                                    <p class="text-muted mb-0">Tidak ada notifikasi</p>
                                </div>
                            `);
                        }
                    }
                },
                error: function(xhr) {
                    alert('Gagal menghapus notifikasi');
                }
            });
        }
    });
    
    // Mark all as read
    $('#markAllReadBtn').click(function() {
        if (confirm('Tandai semua notifikasi sebagai dibaca?')) {
            $.ajax({
                url: '/notifikasi/read-all',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        $('.notification-item').removeClass('bg-light');
                        $('.mark-read-btn').remove();
                        $('.badge.bg-primary').remove();
                        
                        // Remove badge from navbar
                        $('#notificationBadge').remove();
                        
                        alert('Semua notifikasi telah ditandai sebagai dibaca');
                    }
                },
                error: function(xhr) {
                    alert('Gagal menandai semua notifikasi');
                }
            });
        }
    });
    
    // Clear all notifications
    $('#clearAllBtn').click(function() {
        if (confirm('Hapus semua notifikasi? Tindakan ini tidak dapat dibatalkan.')) {
            $.ajax({
                url: '/notifikasi/clear-all',
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        $('.card-body').html(`
                            <div class="text-center py-5">
                                <i class="bi bi-bell-slash display-4 text-muted mb-3"></i>
                                <p class="text-muted mb-0">Tidak ada notifikasi</p>
                            </div>
                        `);
                        
                        // Remove badge from navbar
                        $('#notificationBadge').remove();
                    }
                },
                error: function(xhr) {
                    alert('Gagal menghapus semua notifikasi');
                }
            });
        }
    });
});
</script>
@endpush