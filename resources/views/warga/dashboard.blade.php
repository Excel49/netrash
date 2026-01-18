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
            <div class="card border-start border-primary border-4 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Total Poin</h6>
                            <h4 class="mb-1">{{ number_format(auth()->user()->total_points, 0, ',', '.') }}</h4>
                            <small class="text-muted">
                                ≈ Rp {{ number_format(auth()->user()->total_points * 100, 0, ',', '.') }}
                            </small>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-coins fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-success border-4 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Total Transaksi</h6>
                            <h4 class="mb-1">{{ $totalTransaksi ?? 0 }}</h4>
                            <small class="text-muted">
                                {{ number_format($totalBerat ?? 0, 1) }} kg sampah
                            </small>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-exchange-alt fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-warning border-4 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Poin Bulan Ini</h6>
                            <h4 class="mb-1">{{ number_format($poinBulanIni ?? 0, 0, ',', '.') }}</h4>
                            <small class="text-muted">
                                @php
                                    $bulanIni = now()->month;
                                    $tahunIni = now()->year;
                                    $awalBulan = now()->startOfMonth();
                                    $akhirBulan = now()->endOfMonth();
                                @endphp
                                {{ $awalBulan->format('d M') }} - {{ $akhirBulan->format('d M') }}
                            </small>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-chart-line fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-info border-4 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Sampah Bulan Ini</h6>
                            <h4 class="mb-1">{{ number_format($beratBulanIni ?? 0, 1) }} kg</h4>
                            <small class="text-muted">
                                Rata-rata {{ $beratBulanIni && $totalTransaksi ? number_format($beratBulanIni / $totalTransaksi, 2) : 0 }} kg/transaksi
                            </small>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-weight-hanging fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3 col-6">
                            <a href="{{ route('warga.qrcode.index') }}" class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3 border rounded">
                                <i class="fas fa-qrcode fa-2x mb-2"></i>
                                <span class="fw-medium">QR Code</span>
                                <small class="text-muted">Tampilkan QR</small>
                            </a>
                        </div>
                        
                        <div class="col-md-3 col-6">
                            <a href="{{ route('warga.transaksi.index') }}" class="btn btn-outline-warning w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3 border rounded">
                                <i class="fas fa-history fa-2x mb-2"></i>
                                <span class="fw-medium">Riwayat</span>
                                <small class="text-muted">Transaksi</small>
                            </a>
                        </div>
                        
                        <div class="col-md-3 col-6">
                            @if(Route::has('warga.kategori.index'))
                            <a href="{{ route('warga.kategori.index') }}" class="btn btn-outline-info w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3 border rounded">
                            @else
                            <a href="#" class="btn btn-outline-info w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3 border rounded disabled">
                            @endif
                                <i class="fas fa-tags fa-2x mb-2"></i>
                                <span class="fw-medium">Kategori</span>
                                <small class="text-muted">Sampah</small>
                            </a>
                        </div>
                        
                        <div class="col-md-3 col-6">
                            @if(Route::has('warga.barang.index'))
                            <a href="{{ route('warga.barang.index') }}" class="btn btn-outline-success w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3 border rounded">
                            @else
                            <a href="#" class="btn btn-outline-success w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3 border rounded disabled">
                            @endif
                                <i class="fas fa-shopping-bag fa-2x mb-2"></i>
                                <span class="fw-medium">Barang</span>
                                <small class="text-muted">Tukar Poin</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <!-- Recent Transactions -->
        <div class="col-lg-7 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center">
                    <i class="fas fa-exchange-alt me-2"></i>
                    <h5 class="mb-0">Transaksi Terbaru</h5>
                    @if(Route::has('warga.transaksi.index') && ($recentTransactions->count() ?? 0) > 0)
                    <a href="{{ route('warga.transaksi.index') }}" class="ms-auto btn btn-sm btn-outline-netra">
                        Lihat Semua
                    </a>
                    @endif
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Tanggal</th>
                                    <th>Berat</th>
                                    <th>Poin</th>
                                    <th class="pe-3">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentTransactions ?? [] as $transaksi)
                                <tr>
                                    <td class="ps-3">
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse($transaksi->tanggal_transaksi ?? $transaksi->created_at)->format('d/m H:i') }}
                                        </small>
                                    </td>
                                    <td>
                                        <span class="fw-medium">{{ number_format($transaksi->total_berat ?? 0, 1) }} kg</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success-subtle text-success">
                                            +{{ number_format($transaksi->total_poin ?? 0, 0) }}
                                        </span>
                                    </td>
                                    <td class="pe-3">
                                        @php
                                            $statusColors = [
                                                'completed' => 'success',
                                                'pending' => 'warning',
                                                'cancelled' => 'danger'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$transaksi->status] ?? 'secondary' }}">
                                            {{ ucfirst($transaksi->status ?? 'N/A') }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <i class="fas fa-exchange-alt fa-2x d-block mb-2"></i>
                                        Belum ada transaksi
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Notifications -->
        <div class="col-lg-5 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center">
                    <i class="fas fa-bell me-2"></i>
                    <h5 class="mb-0">Notifikasi</h5>
                    @php
                        $unreadCount = \App\Models\Notifikasi::where('user_id', auth()->id())
                            ->where('dibaca', false)
                            ->count();
                    @endphp
                    @if($unreadCount > 0)
                    <span class="ms-2 badge bg-netra">{{ $unreadCount }}</span>
                    @endif
                </div>
                <div class="card-body p-0">
                    @php
                        $recentNotifications = \App\Models\Notifikasi::where('user_id', auth()->id())
                            ->orderBy('created_at', 'desc')
                            ->limit(4)
                            ->get();
                    @endphp
                    
                    @forelse($recentNotifications as $notif)
                    <div class="border-bottom p-3 {{ !$notif->dibaca  ? 'bg-light' : '' }}">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <div class="rounded-circle bg-{{ $notif->data['type'] ?? 'primary' }} p-2">
                                    <i class="fas fa-{{ $notif->data['icon'] ?? 'bell' }} text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="d-flex justify-content-between">
                                    <h6 class="mb-1 fw-medium">{{ $notif->data['title'] ?? 'Notifikasi' }}</h6>
                                    @if(!$notif->dibaca)
                                    <span class="badge bg-primary">Baru</span>
                                    @endif
                                </div>
                                <p class="text-muted mb-1 small">{{ Str::limit($notif->data['message'] ?? '', 60) }}</p>
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>{{ $notif->created_at->diffForHumans() }}
                                </small>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-bell-slash fa-2x d-block mb-2"></i>
                        Tidak ada notifikasi
                    </div>
                    @endforelse
                    
                    @if($recentNotifications->count() > 0 && Route::has('notifikasi.index'))
                    <div class="text-center p-3 border-top">
                        <a href="{{ route('notifikasi.index') }}" class="btn btn-outline-netra btn-sm">
                            Lihat Semua Notifikasi
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.card {
    border-radius: 0.5rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
}

.border-4 {
    border-width: 4px !important;
}

.card-header {
    background-color: rgba(0, 0, 0, 0.02);
    padding: 1rem 1.25rem;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.card-header h5 {
    font-weight: 600;
    margin: 0;
}

.table-hover tbody tr:hover {
    background-color: rgba(46, 139, 87, 0.03);
}

.table thead th {
    font-weight: 600;
    font-size: 0.875rem;
    border-top: none;
    padding: 0.75rem 0.5rem;
}

.table tbody td {
    padding: 0.75rem 0.5rem;
    vertical-align: middle;
}

.btn-outline-netra {
    color: var(--netra-primary);
    border-color: var(--netra-primary);
}

.btn-outline-netra:hover {
    background-color: var(--netra-primary);
    color: white;
}

.bg-netra {
    background-color: var(--netra-primary) !important;
}

.bg-success-subtle {
    background-color: rgba(25, 135, 84, 0.1) !important;
}

.quick-action-btn:hover {
    background-color: rgba(46, 139, 87, 0.05);
    border-color: var(--netra-primary) !important;
    transform: translateY(-2px);
}

@media (max-width: 768px) {
    .card-body .row > div {
        margin-bottom: 0.75rem;
    }
    
    .quick-action-btn {
        padding: 1rem !important;
    }
    
    .table-responsive {
        font-size: 0.875rem;
    }
}
</style>
@endpush
@endsection