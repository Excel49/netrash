@extends('layouts.app')

@section('title', 'Dashboard Petugas')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">Dashboard Petugas</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('petugas.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
            </div>
            <p class="text-muted">Halo, {{ auth()->user()->name }}! - {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
        </div>
    </div>

    <!-- Stats Cards untuk Petugas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-primary border-start border-0 border-4 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-0">Transaksi Hari Ini</h6>
                            <h4 class="mb-0">{{ $todayTransactions ?? 0 }}</h4>
                            <small class="text-muted">{{ number_format($todayWeight ?? 0, 1) }} kg</small>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-primary rounded-circle">
                                <i class="bi bi-receipt fs-4"></i>
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
                            <h6 class="text-muted mb-0">Total Poin Dibagikan</h6>
                            <h4 class="mb-0">{{ number_format($totalPointsDistributed ?? 0, 0, ',', '.') }}</h4>
                            <small class="text-muted">Bulan ini</small>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-success rounded-circle">
                                <i class="bi bi-star-fill fs-4"></i>
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
                            <h6 class="text-muted mb-0">Warga Terlayani</h6>
                            <h4 class="mb-0">{{ $totalWargaServed ?? 0 }}</h4>
                            <small class="text-muted">{{ $uniqueWargaToday ?? 0 }} hari ini</small>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-warning rounded-circle">
                                <i class="bi bi-people-fill fs-4"></i>
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
                            <h6 class="text-muted mb-0">Rating Petugas</h6>
                            <h4 class="mb-0">{{ number_format($averageRating ?? 0, 1) }}/5.0</h4>
                            <small class="text-muted">{{ $totalRatings ?? 0 }} penilaian</small>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-info rounded-circle">
                                <i class="bi bi-star-half fs-4"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions untuk Petugas -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Aksi Cepat</h5>
                    <p class="text-muted mb-0 small">Fitur-fitur penting untuk aktivitas harian Anda</p>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Scan QR Code - Ikon Scanner Modern -->
                        <div class="col-md-3 col-sm-6">
                            <a href="{{ route('petugas.scan.index') }}" class="card card-hover text-center h-100">
                                <div class="card-body py-5">
                                    <div class="icon-wrapper mb-3">
                                        <i class="bi bi-qr-code-scan quick-action-icon text-primary"></i>
                                        <div class="icon-pulse"></div>
                                    </div>
                                    <h5 class="mb-2">Scan QR Code</h5>
                                    <p class="text-muted mb-0">Scan QR code warga untuk transaksi cepat</p>
                                </div>
                            </a>
                        </div>
                        
                        <!-- Transaksi Manual - Ikon Input Data -->
                        <div class="col-md-3 col-sm-6">
                            <a href="{{ route('petugas.transaksi.create') }}" class="card card-hover text-center h-100">
                                <div class="card-body py-5">
                                    <div class="icon-wrapper mb-3">
                                        <i class="bi bi-keyboard quick-action-icon text-success"></i>
                                    </div>
                                    <h5 class="mb-2">Input Transaksi</h5>
                                    <p class="text-muted mb-0">Input transaksi secara manual</p>
                                </div>
                            </a>
                        </div>
                        
                        <!-- Data Warga - Ikon Kelola Data -->
                        <div class="col-md-3 col-sm-6">
                            <a href="{{ route('petugas.warga.index') }}" class="card card-hover text-center h-100">
                                <div class="card-body py-5">
                                    <div class="icon-wrapper mb-3">
                                        <i class="bi bi-person-lines-fill quick-action-icon text-warning"></i>
                                    </div>
                                    <h5 class="mb-2">Kelola Warga</h5>
                                    <p class="text-muted mb-0">Kelola data warga dan anggota</p>
                                </div>
                            </a>
                        </div>
                        
                        <!-- Riwayat Transaksi - Ikon History -->
                        <div class="col-md-3 col-sm-6">
                            <a href="{{ route('petugas.transaksi.index') }}" class="card card-hover text-center h-100">
                                <div class="card-body py-5">
                                    <div class="icon-wrapper mb-3">
                                        <i class="bi bi-archive quick-action-icon text-info"></i>
                                    </div>
                                    <h5 class="mb-2">Riwayat</h5>
                                    <p class="text-muted mb-0">Lihat semua riwayat transaksi</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tambahan Aksi Cepat Baris Kedua -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Statistik - Sudah tersedia -->
                        <div class="col-md-4 col-sm-6">
                            <a href="{{ route('petugas.statistik.index') }}" class="card card-hover text-center h-100">
                                <div class="card-body py-4">
                                    <div class="icon-wrapper mb-3">
                                        <i class="bi bi-bar-chart quick-action-icon text-purple"></i>
                                    </div>
                                    <h6 class="mb-2">Statistik</h6>
                                    <p class="text-muted mb-0 small">Analisis performa petugas</p>
                                </div>
                            </a>
                        </div>
                        
                        <!-- Profil - Pasti tersedia -->
                        <div class="col-md-4 col-sm-6">
                            <a href="{{ route('profile.edit') }}" class="card card-hover text-center h-100">
                                <div class="card-body py-4">
                                    <div class="icon-wrapper mb-3">
                                        <i class="bi bi-person-circle quick-action-icon text-info"></i>
                                    </div>
                                    <h6 class="mb-2">Profil</h6>
                                    <p class="text-muted mb-0 small">Kelola akun Anda</p>
                                </div>
                            </a>
                        </div>
                        
                        <!-- Cetak Laporan -->
                        <div class="col-md-4 col-sm-6">
                            @if(Route::has('petugas.transaksi.harian'))
                            <a href="{{ route('petugas.transaksi.harian') }}" class="card card-hover text-center h-100">
                            @else
                            <a href="{{ route('petugas.transaksi.index') }}" class="card card-hover text-center h-100">
                            @endif
                                <div class="card-body py-4">
                                    <div class="icon-wrapper mb-3">
                                        <i class="bi bi-printer quick-action-icon text-orange"></i>
                                    </div>
                                    <h6 class="mb-2">Cetak Laporan</h6>
                                    <p class="text-muted mb-0 small">Cetak laporan transaksi</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Transaksi Terbaru -->
        <div class="col-xl-8 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Transaksi Terbaru</h5>
                    <a href="{{ route('petugas.transaksi.index') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-arrow-right me-1"></i> Lihat Semua
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th><i class="bi bi-hash me-1"></i> Kode</th>
                                    <th><i class="bi bi-person me-1"></i> Warga</th>
                                    <th><i class="bi bi-scale me-1"></i> Berat</th>
                                    <th><i class="bi bi-star me-1"></i> Poin</th>
                                    <th><i class="bi bi-clock me-1"></i> Waktu</th>
                                    <th><i class="bi bi-circle me-1"></i> Status</th>
                                    <th><i class="bi bi-gear me-1"></i> Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentTransactions ?? [] as $transaksi)
                                <tr>
                                    <td>
                                        <span class="fw-semibold">{{ $transaksi->kode_transaksi ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($transaksi->warga && $transaksi->warga->profile_photo_url)
                                            <img src="{{ $transaksi->warga->profile_photo_url }}" 
                                                 alt="Foto profil" 
                                                 class="rounded-circle me-2" 
                                                 width="32" height="32">
                                            @endif
                                            <span>{{ optional($transaksi->warga)->name ?? 'N/A' }}</span>
                                        </div>
                                    </td>
                                    <td>{{ number_format($transaksi->total_berat ?? 0, 1) }} kg</td>
                                    <td>
                                        <span class="badge bg-success-subtle text-success">
                                            <i class="bi bi-plus-circle me-1"></i>{{ number_format($transaksi->total_poin ?? 0, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ optional($transaksi->created_at)->format('H:i') ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        @if(($transaksi->status ?? '') == 'completed')
                                        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Selesai</span>
                                        @elseif(($transaksi->status ?? '') == 'pending')
                                        <span class="badge bg-warning"><i class="bi bi-clock me-1"></i>Pending</span>
                                        @else
                                        <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Dibatalkan</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('petugas.transaksi.show', $transaksi->id ?? '') }}" 
                                               class="btn btn-outline-primary" title="Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @if(Route::has('petugas.transaksi.print'))
                                            <a href="{{ route('petugas.transaksi.print', $transaksi->id ?? '') }}" 
                                               class="btn btn-outline-secondary" title="Print">
                                                <i class="bi bi-printer"></i>
                                            </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="bi bi-receipt fs-4 mb-2 d-block"></i>
                                            Belum ada transaksi hari ini
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Statistik & Notifikasi -->
        <div class="col-xl-4 mb-4">
            <!-- Statistik Hari Ini -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-graph-up me-2"></i>Statistik Hari Ini</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item border-0 px-0 py-2 d-flex justify-content-between align-items-center">
                            <span class="fw-medium"><i class="bi bi-receipt text-primary me-2"></i>Total Transaksi:</span>
                            <span class="badge bg-primary">{{ $todayTransactions ?? 0 }}</span>
                        </div>
                        <div class="list-group-item border-0 px-0 py-2 d-flex justify-content-between align-items-center">
                            <span class="fw-medium"><i class="bi bi-scale text-success me-2"></i>Total Berat:</span>
                            <span class="badge bg-success">{{ number_format($todayWeight ?? 0, 1) }} kg</span>
                        </div>
                        <div class="list-group-item border-0 px-0 py-2 d-flex justify-content-between align-items-center">
                            <span class="fw-medium"><i class="bi bi-star text-warning me-2"></i>Total Poin:</span>
                            <span class="badge bg-warning">{{ number_format($todayPoints ?? 0, 0, ',', '.') }}</span>
                        </div>
                        <div class="list-group-item border-0 px-0 py-2 d-flex justify-content-between align-items-center">
                            <span class="fw-medium"><i class="bi bi-people text-info me-2"></i>Warga Terlayani:</span>
                            <span class="badge bg-info">{{ $uniqueWargaToday ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Warga Teraktif -->
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-trophy me-2"></i>Warga Teraktif</h5>
                    <span class="badge bg-primary">Top {{ count($topWarga ?? []) }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($topWarga ?? [] as $index => $warga)
                        <div class="list-group-item border-0 py-3">
                            <div class="d-flex align-items-center">
                                <div class="position-relative me-3">
                                    @if($warga->profile_photo_url)
                                    <img src="{{ $warga->profile_photo_url }}" 
                                         alt="Foto profil" 
                                         class="rounded-circle" 
                                         width="40" height="40">
                                    @else
                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" 
                                         style="width: 40px; height: 40px;">
                                        <i class="bi bi-person text-muted"></i>
                                    </div>
                                    @endif
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">
                                        {{ $index + 1 }}
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">{{ $warga->name ?? 'N/A' }}</h6>
                                    <small class="text-muted">
                                        <i class="bi bi-receipt me-1"></i>{{ number_format($warga->total_transactions ?? 0) }} transaksi
                                    </small>
                                </div>
                                <div class="flex-shrink-0">
                                    <span class="badge bg-success">
                                        <i class="bi bi-star-fill me-1"></i>{{ number_format($warga->total_points ?? 0, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="list-group-item text-center py-4 text-muted">
                            <i class="bi bi-people fs-4 mb-2 d-block"></i>
                            Belum ada data warga
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Performa Petugas -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-graph-up-arrow me-2"></i>Performa 7 Hari Terakhir</h5>
                    <div class="dropdown">
                        <button class="btn btn-link p-0 text-muted" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('petugas.statistik.index') }}">
                                <i class="bi bi-eye me-2"></i>Lihat Detail
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#">
                                <i class="bi bi-download me-2"></i>Export Data
                            </a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    @if(!empty($performanceData) && !empty($performanceWeight))
                    <canvas id="performanceChart" height="250"></canvas>
                    @else
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-graph-up display-4 mb-3 d-block"></i>
                        <p class="mb-1">Belum ada data performa</p>
                        <small class="text-muted">Mulai dengan melakukan transaksi</small>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
@if(!empty($performanceData) && !empty($performanceWeight))
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Performance Chart
    const performanceCtx = document.getElementById('performanceChart');
    if (performanceCtx) {
        // Data untuk chart - gunakan pendekatan yang lebih aman
        const performanceLabels = {!! json_encode($performanceLabels ?? ["Sen", "Sel", "Rab", "Kam", "Jum", "Sab", "Min"]) !!};
        const performanceData = {!! json_encode($performanceData ?? [0, 0, 0, 0, 0, 0, 0]) !!};
        const performanceWeight = {!! json_encode($performanceWeight ?? [0, 0, 0, 0, 0, 0, 0]) !!};
        
        const performanceChart = new Chart(performanceCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: performanceLabels,
                datasets: [
                    {
                        label: 'Jumlah Transaksi',
                        data: performanceData,
                        backgroundColor: '#2E8B57',
                        borderColor: '#2E8B57',
                        borderWidth: 1,
                        borderRadius: 4,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Total Berat (kg)',
                        data: performanceWeight,
                        backgroundColor: '#FFC107',
                        borderColor: '#FFC107',
                        borderWidth: 1,
                        borderRadius: 4,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Jumlah Transaksi'
                        },
                        beginAtZero: true
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Berat (kg)'
                        },
                        beginAtZero: true,
                        grid: {
                            drawOnChartArea: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                }
            }
        });
    }
});
</script>
@endif
@endpush

@push('styles')
<style>
/* Card Hover Effects */
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    transition: all 0.3s ease;
}
.card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}
.card-hover {
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}
.card-hover:hover {
    transform: translateY(-8px);
    box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.15);
    border-color: rgba(46, 139, 87, 0.2);
}

/* Quick Action Icon Styles */
.quick-action-icon {
    font-size: 3rem;
    transition: all 0.3s ease;
}
.card-hover:hover .quick-action-icon {
    transform: scale(1.1);
}

.icon-wrapper {
    position: relative;
    display: inline-block;
}

.icon-pulse {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 70px;
    height: 70px;
    border-radius: 50%;
    background: rgba(13, 110, 253, 0.2);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        transform: translate(-50%, -50%) scale(0.8);
        opacity: 0.7;
    }
    50% {
        transform: translate(-50%, -50%) scale(1);
        opacity: 0.3;
    }
    100% {
        transform: translate(-50%, -50%) scale(0.8);
        opacity: 0.7;
    }
}

/* Badge Styling */
.badge {
    font-weight: 500;
    padding: 0.35em 0.65em;
}

/* Table Styling */
.table-hover tbody tr:hover {
    background-color: rgba(46, 139, 87, 0.05);
}

/* Stat Card Hover */
.stat-card {
    cursor: pointer;
    transition: transform 0.3s ease;
}
.stat-card:hover {
    transform: translateY(-5px);
}

/* Color Variables */
.text-purple { color: #6f42c1 !important; }
.text-teal { color: #20c997 !important; }
.text-orange { color: #fd7e14 !important; }

.bg-purple { background-color: #6f42c1 !important; }
.bg-teal { background-color: #20c997 !important; }
.bg-orange { background-color: #fd7e14 !important; }

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

/* List Group Item Hover */
.list-group-item:hover {
    background-color: rgba(0, 0, 0, 0.02);
}

/* Dropdown Styles */
.dropdown-menu {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    border: none;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .quick-action-icon {
        font-size: 2.5rem;
    }
    
    .card-hover:hover {
        transform: translateY(-5px);
    }
    
    .stat-number {
        font-size: 1.5rem;
    }
}
</style>
@endpush
@endsection