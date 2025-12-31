@extends('layouts.app')

@section('title', 'Notifikasi')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Notifikasi</h2>
            <div>
                @if($notifikasi->where('dibaca', false)->count() > 0)
                <form action="{{ route('notifikasi.read-all') }}" method="POST" class="d-inline me-2">
                    @csrf
                    <button type="submit" class="btn btn-netra-outline btn-sm">
                        <i class="bi bi-check-all me-2"></i>Tandai Semua Dibaca
                    </button>
                </form>
                @endif
                <a href="{{ route('warga.dashboard') }}" class="btn btn-netra-outline">
                    <i class="bi bi-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
        <p class="text-muted">Daftar semua notifikasi Anda</p>
    </div>
</div>

<!-- Stats -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6 class="card-title">Total Notifikasi</h6>
                <h3>{{ $notifikasi->total() }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h6 class="card-title">Belum Dibaca</h6>
                <h3>{{ $notifikasi->where('dibaca', false)->count() }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6 class="card-title">Sudah Dibaca</h6>
                <h3>{{ $notifikasi->where('dibaca', true)->count() }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Notifications List -->
<div class="card">
    <div class="card-body p-0">
        <div class="list-group list-group-flush">
            @forelse($notifikasi as $notif)
            <a href="{{ $notif->link ?: '#' }}" 
               class="list-group-item list-group-item-action border-0 py-3 {{ !$notif->dibaca ? 'bg-light' : '' }}">
                <div class="d-flex w-100 justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            @if($notif->tipe == 'success')
                            <div class="bg-success bg-opacity-10 p-2 rounded">
                                <i class="bi bi-check-circle text-success"></i>
                            </div>
                            @elseif($notif->tipe == 'warning')
                            <div class="bg-warning bg-opacity-10 p-2 rounded">
                                <i class="bi bi-exclamation-triangle text-warning"></i>
                            </div>
                            @elseif($notif->tipe == 'error')
                            <div class="bg-danger bg-opacity-10 p-2 rounded">
                                <i class="bi bi-x-circle text-danger"></i>
                            </div>
                            @else
                            <div class="bg-info bg-opacity-10 p-2 rounded">
                                <i class="bi bi-info-circle text-info"></i>
                            </div>
                            @endif
                        </div>
                        <div>
                            <h6 class="mb-1">{{ $notif->judul }}</h6>
                            <p class="mb-1">{{ $notif->pesan }}</p>
                        </div>
                    </div>
                    <div class="text-end">
                        <small class="text-muted">{{ $notif->created_at->diffForHumans() }}</small>
                        <br>
                        @if(!$notif->dibaca)
                        <span class="badge bg-primary mt-1">Baru</span>
                        @endif
                        <div class="mt-2">
                            <form action="{{ route('notifikasi.read', $notif->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-check"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </a>
            @empty
            <div class="list-group-item text-center py-4 text-muted">
                <i class="bi bi-bell display-4 d-block mb-2"></i>
                Tidak ada notifikasi
            </div>
            @endforelse
        </div>
    </div>
    
    <!-- Pagination -->
    @if($notifikasi->hasPages())
    <div class="card-footer">
        {{ $notifikasi->links() }}
    </div>
    @endif
</div>
@endsection