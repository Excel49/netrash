@extends('layouts.app')

@section('title', 'Dashboard Petugas')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Minimalis -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-1">Dashboard Petugas</h4>
                    <p class="text-muted mb-0">Halo, {{ auth()->user()->name }}!</p>
                </div>
                <div class="text-muted">
                    <i class="fas fa-calendar-alt me-1"></i>
                    {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards Minimalis -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-primary border-4 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Transaksi Hari Ini</h6>
                            <h4 class="mb-1">{{ $todayTransactions ?? 0 }}</h4>
                            <small class="text-muted">{{ number_format($todayWeight ?? 0, 1) }} kg</small>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-exchange-alt fa-2x text-primary"></i>
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
                            <h6 class="text-muted mb-1">Poin Dibagikan</h6>
                            <h4 class="mb-1">{{ number_format($totalPointsDistributed ?? 0, 0, ',', '.') }}</h4>
                            <small class="text-muted">Bulan ini</small>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-coins fa-2x text-success"></i>
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
                            <h6 class="text-muted mb-1">Warga Terlayani</h6>
                            <h4 class="mb-1">{{ $totalWargaServed ?? 0 }}</h4>
                            <small class="text-muted">{{ $uniqueWargaToday ?? 0 }} hari ini</small>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-users fa-2x text-warning"></i>
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
                            <h6 class="text-muted mb-1">Transaksi Bulan Ini</h6>
                            <h4 class="mb-1">{{ $monthlyTransactions ?? 0 }}</h4>
                            <small class="text-muted">{{ number_format($monthlyWeight ?? 0, 1) }} kg</small>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-chart-line fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik Transaksi -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center">
                    <i class="fas fa-chart-line me-2"></i>
                    <h6 class="mb-0">Grafik Transaksi 7 Hari Terakhir</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="transactionsChart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Minimalis -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title mb-3">
                        <i class="fas fa-bolt me-2"></i>Aksi Cepat
                    </h6>
                    <div class="row g-2">
                        <div class="col-md-3 col-sm-6">
                            <a href="{{ route('petugas.scan.index') }}" 
                               class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3 border rounded quick-action-btn">
                                <i class="fas fa-qrcode fa-2x mb-2"></i>
                                <span class="fw-medium">Scan QR</span>
                                <small class="text-muted">Warga</small>
                            </a>
                        </div>
                        
                        <div class="col-md-3 col-sm-6">
                            <a href="{{ route('petugas.transaksi.create') }}" 
                               class="btn btn-outline-success w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3 border rounded quick-action-btn">
                                <i class="fas fa-plus-circle fa-2x mb-2"></i>
                                <span class="fw-medium">Transaksi</span>
                                <small class="text-muted">Baru</small>
                            </a>
                        </div>
                        
                        <div class="col-md-3 col-sm-6">
                            <a href="{{ route('petugas.warga.index') }}" 
                               class="btn btn-outline-warning w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3 border rounded quick-action-btn">
                                <i class="fas fa-user-friends fa-2x mb-2"></i>
                                <span class="fw-medium">Warga</span>
                                <small class="text-muted">Kelola</small>
                            </a>
                        </div>
                        
                        <div class="col-md-3 col-sm-6">
                            <a href="{{ route('petugas.transaksi.index') }}" 
                               class="btn btn-outline-info w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3 border rounded quick-action-btn">
                                <i class="fas fa-history fa-2x mb-2"></i>
                                <span class="fw-medium">Riwayat</span>
                                <small class="text-muted">Transaksi</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="row">
        <div class="col-12">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center">
                    <i class="fas fa-exchange-alt me-2"></i>
                    <h6 class="mb-0">Transaksi Terbaru</h6>
                    @if(($recentTransactions->count() ?? 0) > 0)
                        <a href="{{ route('petugas.transaksi.index') }}" class="ms-auto btn btn-sm btn-outline-netra">
                            Lihat Semua
                        </a>
                    @endif
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Kode</th>
                                    <th>Warga</th>
                                    <th>Berat</th>
                                    <th>Poin</th>
                                    <th>Waktu</th>
                                    <th class="pe-3">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentTransactions ?? [] as $transaksi)
                                    <tr>
                                        <td class="ps-3">
                                            <span class="badge bg-light text-dark">
                                                {{ $transaksi->kode_transaksi ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($transaksi->warga && $transaksi->warga->profile_photo_url)
                                                    <img src="{{ $transaksi->warga->profile_photo_url }}" 
                                                         alt="Foto profil" 
                                                         class="rounded-circle me-2" 
                                                         width="32" height="32">
                                                @endif
                                                <span class="fw-medium">
                                                    {{ optional($transaksi->warga)->name ?? 'N/A' }}
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-muted">
                                                {{ number_format($transaksi->total_berat ?? 0, 1) }} kg
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success-subtle text-success">
                                                +{{ number_format($transaksi->total_poin ?? 0, 0, ',', '.') }}
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ optional($transaksi->created_at)->format('H:i') ?? 'N/A' }}
                                            </small>
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
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            <i class="fas fa-exchange-alt fa-2x d-block mb-2"></i>
                                            Belum ada transaksi hari ini
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

@push('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Data untuk grafik transaksi
    const transactionLabels = <?php echo json_encode($chartData['labels'] ?? ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min']); ?>;
    const transactionValues = <?php echo json_encode($chartData['transactions'] ?? [0, 0, 0, 0, 0, 0, 0]); ?>;
    
    // Grafik transaksi line chart
    const transactionsCtx = document.getElementById('transactionsChart');
    if (transactionsCtx) {
        const transactionChart = new Chart(transactionsCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: transactionLabels,
                datasets: [{
                    label: 'Jumlah Transaksi',
                    data: transactionValues,
                    backgroundColor: 'rgba(46, 139, 87, 0.1)',
                    borderColor: 'rgba(46, 139, 87, 1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: 'rgba(46, 139, 87, 1)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            color: '#6c757d',
                            font: {
                                size: 12,
                                family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.7)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: 'rgba(46, 139, 87, 1)',
                        borderWidth: 1,
                        cornerRadius: 6,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return `${context.dataset.label}: ${context.raw} transaksi`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#6c757d',
                            stepSize: 1,
                            callback: function(value) {
                                return Number.isInteger(value) ? value : '';
                            }
                        },
                        title: {
                            display: true,
                            text: 'Jumlah Transaksi',
                            color: '#6c757d',
                            font: {
                                size: 12
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#6c757d'
                        },
                        title: {
                            display: true,
                            text: 'Hari',
                            color: '#6c757d',
                            font: {
                                size: 12
                            }
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                animation: {
                    duration: 1000,
                    easing: 'easeInOutQuart'
                }
            }
        });
    }
    
    // Debug: cek apakah data ada
    console.log('Transaction Labels:', transactionLabels);
    console.log('Transaction Values:', transactionValues);
});
</script>
@endpush

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

.card-header h6 {
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

.quick-action-btn {
    transition: all 0.3s ease;
}

.quick-action-btn:hover {
    background-color: rgba(46, 139, 87, 0.05);
    border-color: var(--netra-primary) !important;
    transform: translateY(-2px);
}

.badge {
    font-weight: 500;
    padding: 0.35em 0.65em;
    font-size: 0.75em;
}

/* Chart styling */
.chart-container {
    position: relative;
    min-height: 300px;
    width: 100%;
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
    
    .card-header h6 {
        font-size: 1rem;
    }
    
    .chart-container {
        min-height: 250px;
    }
}
</style>
@endpush
@endsection