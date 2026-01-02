@extends('layouts.app')

@section('title', 'Dashboard Warga')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-1">Dashboard Warga</h4>
                    <p class="text-muted mb-0">Selamat datang, {{ auth()->user()->name }}!</p>
                </div>
                <div class="d-flex align-items-center">
                    <span class="badge bg-{{ auth()->user()->role_id == 3 ? 'success' : 'primary' }} me-3 p-2">
                        {{ ucfirst(auth()->user()->role->name ?? 'Warga') }}
                    </span>
                    <span class="text-netra fw-bold">
                        <i class="fas fa-coins me-1"></i>{{ number_format(auth()->user()->total_points, 0, ',', '.') }} pts
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-primary border-start border-0 border-4 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Total Poin</h6>
                            <h4 class="mb-1 stat-number">{{ number_format(auth()->user()->total_points, 0, ',', '.') }}</h4>
                            <small class="text-muted">
                                ≈ Rp {{ number_format(auth()->user()->total_points * 100, 0, ',', '.') }}
                            </small>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-primary rounded-circle">
                                <i class="fas fa-coins fs-4"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-success border-start border-0 border-4 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Total Transaksi</h6>
                            <h4 class="mb-1 stat-number">{{ $totalTransaksi ?? 0 }}</h4>
                            <small class="text-muted">
                                {{ number_format($totalBerat ?? 0, 1) }} kg sampah
                            </small>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-success rounded-circle">
                                <i class="fas fa-exchange-alt fs-4"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-warning border-start border-0 border-4 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Penarikan Pending</h6>
                            <h4 class="mb-1 stat-number">{{ $pendingPenarikan ?? 0 }}</h4>
                            <small class="text-muted">
                                {{ number_format($totalPendingPoin ?? 0, 0, ',', '.') }} poin
                            </small>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-warning rounded-circle">
                                <i class="fas fa-clock fs-4"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-info border-start border-0 border-4 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Poin Ditarik</h6>
                            <h4 class="mb-1 stat-number">{{ number_format($totalPoinDitarik ?? 0, 0, ',', '.') }}</h4>
                            <small class="text-muted">
                                ≈ Rp {{ number_format(($totalPoinDitarik ?? 0) * 100, 0, ',', '.') }}
                            </small>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-info rounded-circle">
                                <i class="fas fa-money-bill-wave fs-4"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Aksi Cepat -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-bolt text-netra fs-5 me-2"></i>
                        <h5 class="mb-0">Aksi Cepat</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- QR Code -->
                        <div class="col-md-3 col-6 mb-3">
                            <a href="{{ route('warga.qrcode.index') }}" class="d-block p-3 text-center border rounded h-100 quick-action-item">
                                <div class="quick-action-icon mb-2">
                                    <i class="fas fa-qrcode text-primary fs-3"></i>
                                </div>
                                <h6 class="mb-1 fw-medium">QR Code Saya</h6>
                                <p class="text-muted mb-0 small">Tampilkan QR code</p>
                            </a>
                        </div>
                        
                        <!-- Tarik Poin -->
                        <div class="col-md-3 col-6 mb-3">
                            <a href="{{ route('warga.penarikan.create') }}" class="d-block p-3 text-center border rounded h-100 quick-action-item">
                                <div class="quick-action-icon mb-2">
                                    <i class="fas fa-hand-holding-usd text-success fs-3"></i>
                                </div>
                                <h6 class="mb-1 fw-medium">Tarik Poin</h6>
                                <p class="text-muted mb-0 small">Ajukan penarikan</p>
                            </a>
                        </div>
                        
                        <!-- Riwayat Transaksi -->
                        <div class="col-md-3 col-6 mb-3">
                            <a href="{{ route('warga.transaksi.index') }}" class="d-block p-3 text-center border rounded h-100 quick-action-item">
                                <div class="quick-action-icon mb-2">
                                    <i class="fas fa-history text-warning fs-3"></i>
                                </div>
                                <h6 class="mb-1 fw-medium">Riwayat Transaksi</h6>
                                <p class="text-muted mb-0 small">Lihat semua transaksi</p>
                            </a>
                        </div>
                        
                        <!-- Kategori Sampah -->
                        <div class="col-md-3 col-6 mb-3">
                            @if(Route::has('warga.kategori.index'))
                            <a href="{{ route('warga.kategori.index') }}" class="d-block p-3 text-center border rounded h-100 quick-action-item">
                            @else
                            <a href="#" class="d-block p-3 text-center border rounded h-100 quick-action-item">
                            @endif
                                <div class="quick-action-icon mb-2">
                                    <i class="fas fa-tags text-info fs-3"></i>
                                </div>
                                <h6 class="mb-1 fw-medium">Kategori Sampah</h6>
                                <p class="text-muted mb-0 small">Harga dan poin</p>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Notifikasi Terbaru -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-bell text-warning fs-5 me-2"></i>
                            <h5 class="mb-0">Notifikasi Terbaru</h5>
                        </div>
                        @php
                            $recentNotifications = \App\Models\Notifikasi::where('user_id', auth()->id())
                                ->orderBy('created_at', 'desc')
                                ->limit(3)
                                ->get();
                        @endphp
                        @if($recentNotifications->count() > 0)
                        <span class="badge bg-netra rounded-pill">{{ $recentNotifications->count() }}</span>
                        @endif
                    </div>
                </div>
                <div class="card-body p-0">
                    @forelse($recentNotifications as $notif)
                    <div class="border-bottom p-3 {{ !$notif->read_at ? 'bg-light' : '' }}">
                        <div class="d-flex align-items-start">
                            <div class="me-3">
                                <div class="rounded-circle bg-{{ $notif->data['type'] ?? 'primary' }} p-2">
                                    <i class="fas fa-{{ $notif->data['icon'] ?? 'bell' }} text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <h6 class="mb-0 fw-medium">{{ $notif->data['title'] ?? 'Notifikasi' }}</h6>
                                    @if(!$notif->read_at)
                                    <span class="badge bg-primary rounded-pill">Baru</span>
                                    @endif
                                </div>
                                <p class="text-muted mb-1 small">{{ Str::limit($notif->data['message'] ?? '', 70) }}</p>
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>{{ $notif->created_at->diffForHumans() }}
                                </small>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5">
                        <i class="fas fa-bell-slash text-muted fa-3x d-block mb-3"></i>
                        <p class="text-muted mb-2">Tidak ada notifikasi</p>
                        <small class="text-muted">Semua update akan muncul di sini</small>
                    </div>
                    @endforelse
                    
                    @if($recentNotifications->count() > 0 && Route::has('notifikasi.index'))
                    <div class="text-center p-3 border-top">
                        <a href="{{ route('notifikasi.index') }}" class="btn btn-outline-netra btn-sm">
                            <i class="fas fa-arrow-right me-1"></i> Lihat Semua
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Transaksi Terbaru -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exchange-alt text-primary fs-5 me-2"></i>
                            <h5 class="mb-0">Transaksi Terbaru</h5>
                        </div>
                        @if(Route::has('warga.transaksi.index'))
                        <a href="{{ route('warga.transaksi.index') }}" class="btn btn-netra btn-sm">
                            <i class="fas fa-eye me-1"></i> Lihat Semua
                        </a>
                        @endif
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Tanggal</th>
                                    <th>Berat</th>
                                    <th>Poin</th>
                                    <th class="pe-4">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentTransactions ?? [] as $transaksi)
                                <tr>
                                    <td class="ps-4">
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse($transaksi->tanggal_transaksi ?? $transaksi->created_at)->format('d/m/Y H:i') }}
                                        </small>
                                    </td>
                                    <td>
                                        <span class="fw-medium">{{ number_format($transaksi->total_berat ?? 0, 1) }} kg</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                                            +{{ number_format($transaksi->total_poin ?? 0, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="pe-4">
                                        @if(($transaksi->status ?? '') == 'completed')
                                        <span class="badge bg-success rounded-pill">
                                            Selesai
                                        </span>
                                        @else
                                        <span class="badge bg-warning rounded-pill">
                                            {{ ucfirst($transaksi->status ?? 'N/A') }}
                                        </span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-exchange-alt fa-2x d-block mb-2"></i>
                                            Belum ada transaksi
                                        </div>
                                        <a href="{{ route('warga.qrcode.index') }}" class="btn btn-netra btn-sm mt-2">
                                            <i class="fas fa-qrcode me-1"></i> Mulai Transaksi
                                        </a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Quick Action Styles */
.quick-action-item {
    color: inherit;
    text-decoration: none;
    transition: all 0.3s ease;
    display: block;
}
.quick-action-item:hover {
    background-color: rgba(46, 139, 87, 0.05);
    transform: translateY(-2px);
    border-color: var(--netra-primary) !important;
}
.quick-action-icon {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    border-radius: 12px;
    background-color: rgba(46, 139, 87, 0.08);
    transition: all 0.3s ease;
}
.quick-action-item:hover .quick-action-icon {
    background-color: rgba(46, 139, 87, 0.15);
    transform: scale(1.1);
}

/* Card Header Improvements */
.card-header {
    padding: 1rem 1.25rem;
    background-color: rgba(0, 0, 0, 0.02);
}
.card-header h5 {
    font-weight: 600;
    margin: 0;
}

/* Avatar Styles */
.avatar-sm {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.avatar-title {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}

/* Table Improvements */
.table-hover tbody tr:hover {
    background-color: rgba(46, 139, 87, 0.03);
}
.table thead th {
    font-weight: 600;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-top: 1px solid #dee2e6;
}

/* Badge Improvements */
.badge {
    font-weight: 500;
    padding: 0.35em 0.65em;
}
.badge.rounded-pill {
    padding: 0.35em 0.85em;
}

/* Stat Number */
.stat-number {
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--netra-primary);
    margin-bottom: 0.25rem;
}

/* Border Colors */
.border-primary { border-color: var(--netra-primary) !important; }
.border-success { border-color: var(--netra-success) !important; }
.border-warning { border-color: var(--netra-warning) !important; }
.border-info { border-color: var(--netra-info) !important; }

/* Background Subtle Colors */
.bg-success-subtle { background-color: rgba(25, 135, 84, 0.1) !important; }
.bg-warning-subtle { background-color: rgba(255, 193, 7, 0.1) !important; }

/* Responsive Adjustments */
@media (max-width: 768px) {
    .quick-action-item {
        padding: 1rem !important;
    }
    .quick-action-icon {
        width: 40px;
        height: 40px;
        font-size: 1.5rem !important;
    }
    .stat-number {
        font-size: 1.5rem;
    }
    .card-header h5 {
        font-size: 1.1rem;
    }
}

/* Status Badge Colors */
.bg-primary { background-color: var(--netra-primary) !important; }
.bg-success { background-color: var(--netra-success) !important; }
.bg-warning { background-color: var(--netra-warning) !important; }
.bg-info { background-color: var(--netra-info) !important; }
</style>
@endpush
@endsection