@extends('layouts.app')

@section('title', 'Notifikasi')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">Notifikasi</h2>
                <div>
                    <button type="button" class="btn btn-outline-danger" id="clearAllBtn">
                        <i class="bi bi-trash me-2"></i>Hapus Semua
                    </button>
                </div>
            </div>
            <p class="text-muted">Daftar semua notifikasi Anda</p>
        </div>
    </div>

    <!-- Notification List -->
    <div class="card">
        <div class="card-body p-0">
            @forelse($notifikasi as $notif)
            <div class="notification-item border-bottom">
                <div class="p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center mb-1">
                                <span class="badge bg-{{ $notif->color }} me-2">{{ ucfirst($notif->tipe) }}</span>
                                <small class="text-muted">{{ $notif->created_at->diffForHumans() }}</small>
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