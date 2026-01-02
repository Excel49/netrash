@extends('layouts.app')

@section('title', 'Riwayat Poin')

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="h3 mb-0">
        <i class="fas fa-history"></i> Riwayat Poin
    </h1>
    <div>
        <a href="{{ route('warga.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-3">
        <!-- Stats Card -->
        <div class="card mb-4">
            <div class="card-body text-center">
                <h6 class="card-title text-muted mb-2">
                    <i class="fas fa-coins me-1"></i> Poin Saat Ini
                </h6>
                <h2 class="text-netra mb-1" id="currentPoints">
                    {{ number_format(auth()->user()->total_points, 0, ',', '.') }}
                </h2>
                <small class="text-muted">pts</small>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-filter me-2"></i> Filter Periode
                </h6>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <a href="{{ route('warga.poin.history') }}" 
                       class="list-group-item list-group-item-action {{ !request()->has('period') ? 'active' : '' }}">
                        Semua
                    </a>
                    <a href="{{ route('warga.poin.history', ['period' => 'today']) }}" 
                       class="list-group-item list-group-item-action {{ request('period') == 'today' ? 'active' : '' }}">
                        Hari Ini
                    </a>
                    <a href="{{ route('warga.poin.history', ['period' => 'week']) }}" 
                       class="list-group-item list-group-item-action {{ request('period') == 'week' ? 'active' : '' }}">
                        7 Hari Terakhir
                    </a>
                    <a href="{{ route('warga.poin.history', ['period' => 'month']) }}" 
                       class="list-group-item list-group-item-action {{ request('period') == 'month' ? 'active' : '' }}">
                        Bulan Ini
                    </a>
                    <a href="{{ route('warga.poin.history', ['period' => 'year']) }}" 
                       class="list-group-item list-group-item-action {{ request('period') == 'year' ? 'active' : '' }}">
                        Tahun Ini
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-9">
        <div class="card">
            <div class="card-body">
                @if($transaksi->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-history fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Belum ada riwayat transaksi</h5>
                        <p class="text-muted">Transaksi penukaran sampah akan muncul di sini</p>
                    </div>
                @else
                    <!-- Summary Stats -->
                    @php
                        $totalPoin = $transaksi->sum('total_poin');
                        $totalBerat = $transaksi->sum('total_berat');
                    @endphp
                    
                    <div class="alert alert-info mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <small class="text-muted d-block">Total Transaksi</small>
                                <strong>{{ $transaksi->total() }}</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Total Poin</small>
                                <strong class="text-netra">{{ number_format($totalPoin) }} pts</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Total Berat</small>
                                <strong>{{ number_format($totalBerat, 1) }} kg</strong>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Kategori</th>
                                    <th>Berat</th>
                                    <th>Poin</th>
                                    <th>Petugas</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transaksi as $item)
                                <tr>
                                    <td>
                                        <small class="text-muted d-block">{{ $item->created_at->format('d/m/Y') }}</small>
                                        <small class="text-muted">{{ $item->created_at->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        @if($item->detailTransaksi->isNotEmpty())
                                            @php
                                                $categories = $item->detailTransaksi->pluck('kategori.nama_kategori')->unique()->toArray();
                                            @endphp
                                            <small>{{ implode(', ', $categories) }}</small>
                                        @else
                                            <small class="text-muted">-</small>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ number_format($item->total_berat, 1) }}</strong>
                                        <small class="text-muted d-block">kg</small>
                                    </td>
                                    <td>
                                        <strong class="text-netra">+{{ number_format($item->total_poin) }}</strong>
                                        <small class="text-muted d-block">pts</small>
                                    </td>
                                    <td>
                                        <small>{{ $item->petugas->name ?? '-' }}</small>
                                    </td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'pending' => 'warning',
                                                'completed' => 'success',
                                                'cancelled' => 'danger'
                                            ];
                                            $statusLabels = [
                                                'pending' => 'Menunggu',
                                                'completed' => 'Selesai',
                                                'cancelled' => 'Batal'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$item->status] ?? 'secondary' }}">
                                            {{ $statusLabels[$item->status] ?? $item->status }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $transaksi->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Chart Section (Optional) -->
        @if(!$transaksi->isEmpty())
        <div class="card mt-4">
            <div class="card-header bg-light">
                <h6 class="card-title mb-0">
                    <i class="fas fa-chart-line me-2"></i> Grafik Poin Bulanan
                </h6>
            </div>
            <div class="card-body">
                <div style="height: 300px;">
                    <canvas id="pointsChart"></canvas>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    .table tbody tr:hover {
        background-color: rgba(46, 139, 87, 0.05);
    }
    .badge {
        font-size: 0.8em;
        padding: 0.4em 0.8em;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Update current points with animation
        const currentPointsEl = document.getElementById('currentPoints');
        const currentPoints = parseInt(currentPointsEl.textContent.replace(/\./g, ''));
        
        // Initialize chart if data exists
        @if(!$transaksi->isEmpty())
            initPointsChart();
        @endif
    });
    
    function initPointsChart() {
        const ctx = document.getElementById('pointsChart').getContext('2d');
        
        // Sample data - in real app, fetch from API
        const data = {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            datasets: [{
                label: 'Poin Didapat',
                data: [1200, 1900, 3000, 5000, 2000, 3000, 2500, 1800, 2200, 2800, 3200, 4000],
                backgroundColor: 'rgba(46, 139, 87, 0.2)',
                borderColor: 'rgb(46, 139, 87)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        };
        
        const config = {
            type: 'line',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function(context) {
                                return `${context.dataset.label}: ${context.parsed.y.toLocaleString('id-ID')} pts`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('id-ID') + ' pts';
                            }
                        },
                        grid: {
                            drawBorder: false
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        };
        
        new Chart(ctx, config);
    }
</script>
@endpush