@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-0">
                        <i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard
                    </h2>
                    <p class="text-muted mb-0">Overview sistem NetraTrash</p>
                </div>
                <div class="text-end">
                    <small class="text-muted">Update: {{ now()->format('d M Y, H:i') }}</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-start border-primary border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Total Users</h6>
                            <h3 class="mb-0">{{ $totalUsers }}</h3>
                            <small class="text-muted">
                                {{ $totalWarga }} warga • {{ $totalPetugas }} petugas
                            </small>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-users fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-start border-success border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Total Transaksi</h6>
                            <h3 class="mb-0">{{ $totalTransaksi }}</h3>
                            <small class="text-muted">
                                {{ number_format($totalSampah, 1) }} kg sampah
                            </small>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-exchange-alt fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-start border-info border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Total Revenue</h6>
                            <h3 class="mb-0">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                            <small class="text-muted">
                                Pendapatan total
                            </small>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-money-bill-wave fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Quick Actions -->
    <div class="row">
        <!-- Chart Section -->
        <div class="col-lg-8 mb-4">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>Statistik Transaksi 6 Bulan Terakhir
                    </h6>
                </div>
                <div class="card-body">
                    <div style="height: 300px;">
                        <canvas id="transaksiChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.users.create') }}" class="btn btn-netra">
                            <i class="fas fa-user-plus me-2"></i>Tambah User
                        </a>
                        <a href="{{ route('admin.kategori.create') }}" class="btn btn-outline-netra">
                            <i class="fas fa-tag me-2"></i>Tambah Kategori
                        </a>
                        <a href="{{ route('admin.barang.create') }}" class="btn btn-outline-netra">
                            <i class="fas fa-box me-2"></i>Tambah Barang
                        </a>
                        <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-netra">
                            <i class="fas fa-file-export me-2"></i>Generate Report
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Recent Notifications -->
            <div class="card mt-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-bell me-2"></i>Notifikasi Terbaru
                    </h6>
                    <a href="{{ route('notifikasi.index') }}" class="btn btn-sm btn-outline-netra">Lihat Semua</a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($recentNotifications as $notification)
                        <a href="{{ $notification->link ?: '#' }}" 
                           class="list-group-item list-group-item-action border-0 py-3 px-4">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0">
                                    <i class="fas {{ $notification->dibaca ? 'fa-envelope-open' : 'fa-envelope' }} 
                                       {{ $notification->dibaca ? 'text-muted' : 'text-primary' }} mt-1"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <h6 class="mb-0">{{ $notification->judul }}</h6>
                                        <small class="text-muted">{{ $notification->created_at->format('H:i') }}</small>
                                    </div>
                                    <p class="mb-0 text-muted small">{{ Str::limit($notification->pesan, 40) }}</p>
                                </div>
                            </div>
                        </a>
                        @empty
                        <div class="list-group-item text-center py-4">
                            <i class="fas fa-bell-slash fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">Tidak ada notifikasi</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-history me-2"></i>Transaksi Terbaru
                    </h6>
                    <a href="{{ route('admin.transaksi.index') }}" class="btn btn-sm btn-netra">Lihat Semua</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Kode</th>
                                    <th>Warga</th>
                                    <th>Petugas</th>
                                    <th class="text-end">Berat (kg)</th>
                                    <th class="text-end">Poin</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentTransactions as $transaksi)
                                <tr>
                                    <td>
                                        <span class="badge bg-secondary">#{{ $transaksi->kode_transaksi }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $transaksi->warga->name }}</div>
                                        @if($transaksi->warga->phone)
                                        <small class="text-muted">{{ $transaksi->warga->phone }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $transaksi->petugas->name ?? '-' }}</td>
                                    <td class="text-end fw-semibold">{{ number_format($transaksi->total_berat, 1) }}</td>
                                    <td class="text-end">
                                        <span class="badge bg-success rounded-pill px-2">
                                            {{ number_format($transaksi->total_poin, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted d-block">{{ $transaksi->tanggal_transaksi->format('d/m/Y') }}</small>
                                        <small class="text-muted">{{ $transaksi->tanggal_transaksi->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        @if($transaksi->status == 'completed')
                                        <span class="badge bg-success">
                                            <i class="fas fa-check me-1"></i>Selesai
                                        </span>
                                        @elseif($transaksi->status == 'pending')
                                        <span class="badge bg-warning">
                                            <i class="fas fa-clock me-1"></i>Pending
                                        </span>
                                        @else
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times me-1"></i>Dibatalkan
                                        </span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="fas fa-exchange-alt fa-2x text-muted mb-2"></i>
                                        <p class="text-muted mb-0">Belum ada transaksi</p>
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
    
    .bg-opacity-10 {
        background-color: rgba(var(--bs-primary-rgb), 0.1);
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Transaksi Chart
    const transaksiCtx = document.getElementById('transaksiChart').getContext('2d');
    const transaksiChart = new Chart(transaksiCtx, {
        type: 'line',
        data: {
            labels: @json($chartLabels),
            datasets: [{
                label: 'Jumlah Transaksi',
                data: @json($chartData),
                borderColor: '#2E8B57',
                backgroundColor: 'rgba(46, 139, 87, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#2E8B57',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.7)',
                    padding: 10,
                    titleFont: {
                        size: 12
                    },
                    bodyFont: {
                        size: 12
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        stepSize: 1,
                        callback: function(value) {
                            return value + ' transaksi';
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
@endsection