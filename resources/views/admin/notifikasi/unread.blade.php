@extends('layouts.app')

@section('title', 'Notifikasi Belum Dibaca')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">Notifikasi Belum Dibaca</h2>
                <div>
                    <button type="button" class="btn btn-netra me-2" id="markAllReadBtn">
                        <i class="bi bi-check-all me-2"></i>Tandai Semua Dibaca
                    </button>
                    <button type="button" class="btn btn-outline-secondary" onclick="window.location.href='{{ route('notifikasi.index') }}'">
                        <i class="bi bi-arrow-left me-2"></i>Semua Notifikasi
                    </button>
                </div>
            </div>
            <p class="text-muted">Hanya menampilkan notifikasi yang belum dibaca</p>
        </div>
    </div>

    <!-- Notification List -->
    <div class="card">
        <div class="card-body p-0">
            @forelse($notifikasi as $notif)
            <div class="notification-item border-bottom bg-light">
                <div class="p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center mb-1">
                                <span class="badge bg-{{ $notif->color }} me-2">{{ ucfirst($notif->tipe) }}</span>
                                <small class="text-muted">{{ $notif->created_at->diffForHumans() }}</small>
                                <span class="badge bg-primary ms-2">Baru</span>
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
                                        class="btn btn-outline-primary mark-read-btn" 
                                        data-id="{{ $notif->id }}">
                                    <i class="bi bi-check"></i>
                                </button>
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
                <i class="bi bi-check-circle display-4 text-success mb-3"></i>
                <p class="text-muted mb-0">Tidak ada notifikasi yang belum dibaca</p>
                <a href="{{ route('notifikasi.index') }}" class="btn btn-netra-outline mt-3">
                    Lihat Semua Notifikasi
                </a>
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

@push('scripts')
<script>
$(document).ready(function() {
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
                    btn.closest('.notification-item').remove();
                    
                    // Check if list is empty
                    if ($('.notification-item').length === 0) {
                        $('.card-body').html(`
                            <div class="text-center py-5">
                                <i class="bi bi-check-circle display-4 text-success mb-3"></i>
                                <p class="text-muted mb-0">Tidak ada notifikasi yang belum dibaca</p>
                                <a href="{{ route('notifikasi.index') }}" class="btn btn-netra-outline mt-3">
                                    Lihat Semua Notifikasi
                                </a>
                            </div>
                        `);
                    }
                    
                    // Update badge count
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
                                    <i class="bi bi-check-circle display-4 text-success mb-3"></i>
                                    <p class="text-muted mb-0">Tidak ada notifikasi yang belum dibaca</p>
                                    <a href="{{ route('notifikasi.index') }}" class="btn btn-netra-outline mt-3">
                                        Lihat Semua Notifikasi
                                    </a>
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
                        window.location.href = '{{ route("notifikasi.index") }}';
                    }
                },
                error: function(xhr) {
                    alert('Gagal menandai semua notifikasi');
                }
            });
        }
    });
});
</script>
@endpush