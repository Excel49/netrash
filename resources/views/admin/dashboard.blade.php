@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2 class="mb-0">Dashboard Admin</h2>
        <p class="text-muted">Overview sistem NetraTrash</p>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-primary h-100">
            <div class="card-body stat-card">
                <div class="row align-items-center">
                    <div class="col-8">
                        <h6 class="text-uppercase mb-0">Total Users</h6>
                        <div class="stat-number">{{ $totalUsers }}</div>
                        <small class="text-muted">{{ $totalWarga }} warga, {{ $totalPetugas }} petugas</small>
                    </div>
                    <div class="col-4 text-end">
                        <i class="bi bi-people-fill stat-icon text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-success h-100">
            <div class="card-body stat-card">
                <div class="row align-items-center">
                    <div class="col-8">
                        <h6 class="text-uppercase mb-0">Total Transaksi</h6>
                        <div class="stat-number">{{ $totalTransaksi }}</div>
                        <!-- PERBAIKAN DI SINI: $totalBerat diganti $totalSampah -->
                        <small class="text-muted">{{ number_format($totalSampah, 1) }} kg sampah</small>
                    </div>
                    <div class="col-4 text-end">
                        <i class="bi bi-receipt stat-icon text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-warning h-100">
            <div class="card-body stat-card">
                <div class="row align-items-center">
                    <div class="col-8">
                        <h6 class="text-uppercase mb-0">Penarikan Pending</h6>
                        <div class="stat-number">{{ $pendingPenarikan }}</div>
                        <small class="text-muted">{{ number_format($totalPendingPoin, 0, ',', '.') }} poin</small>
                    </div>
                    <div class="col-4 text-end">
                        <i class="bi bi-cash-coin stat-icon text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-info h-100">
            <div class="card-body stat-card">
                <div class="row align-items-center">
                    <div class="col-8">
                        <h6 class="text-uppercase mb-0">Total Revenue</h6>
                        <div class="stat-number">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
                        <small class="text-muted">Pendapatan total</small>
                    </div>
                    <div class="col-4 text-end">
                        <i class="bi bi-cash stat-icon text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Chart 1: Transaksi per Bulan -->
    <div class="col-xl-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Statistik Transaksi 6 Bulan Terakhir</h6>
            </div>
            <div class="card-body">
                <canvas id="transaksiChart" height="250"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="col-xl-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.users.create') }}" class="btn btn-netra">
                        <i class="bi bi-person-plus me-2"></i>Tambah User Baru
                    </a>
                    <a href="{{ route('admin.kategori.create') }}" class="btn btn-netra-outline">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Kategori
                    </a>
                    <a href="{{ route('admin.penarikan.index') }}" class="btn btn-netra-outline">
                        <i class="bi bi-cash-coin me-2"></i>Review Penarikan
                    </a>
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-netra-outline">
                        <i class="bi bi-file-earmark-bar-graph me-2"></i>Generate Report
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Recent Notifications -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">Notifikasi Terbaru</h6>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($recentNotifications as $notification)
                    <a href="{{ $notification->link ?: '#' }}" 
                       class="list-group-item list-group-item-action border-0 py-3">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">{{ $notification->judul }}</h6>
                            <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                        </div>
                        <p class="mb-1">{{ Str::limit($notification->pesan, 50) }}</p>
                        @if(!$notification->dibaca)
                        <span class="badge bg-primary">Baru</span>
                        @endif
                    </a>
                    @empty
                    <div class="list-group-item text-center py-4 text-muted">
                        Tidak ada notifikasi
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
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Transaksi Terbaru</h6>
                <a href="{{ route('admin.transaksi.index') }}" class="btn btn-sm btn-netra">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Warga</th>
                                <th>Petugas</th>
                                <th>Berat (kg)</th>
                                <th>Total Poin</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTransactions as $transaksi)
                            <tr>
                                <td>{{ $transaksi->kode_transaksi }}</td>
                                <td>{{ $transaksi->warga->name }}</td>
                                <td>{{ $transaksi->petugas->name }}</td>
                                <td>{{ number_format($transaksi->total_berat, 1) }}</td>
                                <td>{{ number_format($transaksi->total_poin, 0, ',', '.') }}</td>
                                <td>{{ $transaksi->tanggal_transaksi->format('d/m/Y') }}</td>
                                <td>
                                    @if($transaksi->status == 'completed')
                                    <span class="badge bg-success">Selesai</span>
                                    @elseif($transaksi->status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                    @else
                                    <span class="badge bg-danger">Dibatalkan</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
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
</div>

@push('scripts')
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
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
});
</script>
@endpush
@endsection