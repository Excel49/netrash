@extends('layouts.app')

@section('title', 'Reports Dashboard')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Reports Dashboard</h2>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-netra-outline">
                <i class="bi bi-arrow-left me-2"></i>Kembali
            </a>
        </div>
        <p class="text-muted">Analytics dan laporan sistem</p>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-primary h-100">
            <div class="card-body stat-card">
                <div class="row align-items-center">
                    <div class="col-8">
                        <h6 class="text-uppercase mb-0">Total Transaksi</h6>
                        <div class="stat-number">{{ number_format($stats['total_transaksi']) }}</div>
                        <small class="text-muted">Rp {{ number_format($stats['total_pendapatan'], 0, ',', '.') }}</small>
                    </div>
                    <div class="col-4 text-end">
                        <i class="bi bi-receipt stat-icon text-primary"></i>
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
                        <h6 class="text-uppercase mb-0">Total Penarikan</h6>
                        <div class="stat-number">{{ number_format($stats['total_penarikan']) }}</div>
                        <small class="text-muted">{{ number_format($stats['total_poin_ditarik']) }} poin</small>
                    </div>
                    <div class="col-4 text-end">
                        <i class="bi bi-cash-coin stat-icon text-success"></i>
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
                        <h6 class="text-uppercase mb-0">Total Users</h6>
                        <div class="stat-number">{{ number_format($stats['total_users']) }}</div>
                        <small class="text-muted">{{ number_format($stats['total_poin_dikeluarkan']) }} poin</small>
                    </div>
                    <div class="col-4 text-end">
                        <i class="bi bi-people stat-icon text-warning"></i>
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
                        <h6 class="text-uppercase mb-0">Kategori Sampah</h6>
                        <div class="stat-number">{{ number_format($stats['total_kategori']) }}</div>
                        <small class="text-muted">Jenis sampah</small>
                    </div>
                    <div class="col-4 text-end">
                        <i class="bi bi-tags stat-icon text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Aktivitas 7 Hari Terakhir</h6>
            </div>
            <div class="card-body">
                <canvas id="activityChart" height="250"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Quick Reports</h6>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <a href="{{ route('admin.reports.transaksi') }}" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">Transaksi Reports</h6>
                            <i class="bi bi-chevron-right"></i>
                        </div>
                        <small class="text-muted">Laporan semua transaksi</small>
                    </a>
                    
                    <a href="{{ route('admin.reports.penarikan') }}" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">Penarikan Reports</h6>
                            <i class="bi bi-chevron-right"></i>
                        </div>
                        <small class="text-muted">Laporan penarikan poin</small>
                    </a>
                    
                    <a href="{{ route('admin.reports.users') }}" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">Users Reports</h6>
                            <i class="bi bi-chevron-right"></i>
                        </div>
                        <small class="text-muted">Laporan semua user</small>
                    </a>
                    
                    <a href="{{ route('admin.reports.kategori') }}" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">Kategori Reports</h6>
                            <i class="bi bi-chevron-right"></i>
                        </div>
                        <small class="text-muted">Statistik per kategori</small>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Report Cards -->
<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Generate Custom Report</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.reports.export') }}" method="POST" target="_blank">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Report Type</label>
                        <select name="report" class="form-select" required>
                            <option value="transaksi">Transaksi</option>
                            <option value="penarikan">Penarikan Poin</option>
                            <option value="users">Users</option>
                        </select>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" class="form-control">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Export Format</label>
                        <select name="type" class="form-select" required>
                            <option value="excel">Excel</option>
                            <option value="pdf">PDF</option>
                        </select>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-netra">
                            <i class="bi bi-download me-2"></i>Generate Report
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Quick Stats</h6>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Transaksi Hari Ini</span>
                        <span class="badge bg-primary">0</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Penarikan Pending</span>
                        <span class="badge bg-warning">0</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Warga Baru (Bulan Ini)</span>
                        <span class="badge bg-success">0</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Poin Dikeluarkan (Bulan Ini)</span>
                        <span class="badge bg-info">0</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">System Information</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td>Laravel Version</td>
                        <td class="text-end">{{ app()->version() }}</td>
                    </tr>
                    <tr>
                        <td>PHP Version</td>
                        <td class="text-end">{{ PHP_VERSION }}</td>
                    </tr>
                    <tr>
                        <td>Database</td>
                        <td class="text-end">MySQL</td>
                    </tr>
                    <tr>
                        <td>Server Time</td>
                        <td class="text-end">{{ now()->format('d/m/Y H:i:s') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fetch chart data
    fetch('/admin/reports/dashboard-stats')
        .then(response => response.json())
        .then(data => {
            // Activity Chart
            const ctx = document.getElementById('activityChart').getContext('2d');
            const activityChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.dates,
                    datasets: [
                        {
                            label: 'Transaksi',
                            data: data.transaksi,
                            borderColor: '#2E8B57',
                            backgroundColor: 'rgba(46, 139, 87, 0.1)',
                            borderWidth: 2,
                            tension: 0.4
                        },
                        {
                            label: 'Penarikan',
                            data: data.penarikan,
                            borderColor: '#FFC107',
                            backgroundColor: 'rgba(255, 193, 7, 0.1)',
                            borderWidth: 2,
                            tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
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
        })
        .catch(error => {
            console.error('Error fetching chart data:', error);
        });
});
</script>
@endpush
@endsection