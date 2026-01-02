@extends('layouts.app')

@section('title', 'Statistik Petugas')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">Statistik Petugas</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('petugas.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Statistik</li>
                    </ol>
                </div>
            </div>
            <p class="text-muted">Halo, {{ auth()->user()->name }}! - {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
        </div>
    </div>

    <!-- Statistik Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-primary border-start border-0 border-4 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-0">Transaksi Bulan Ini</h6>
                            <h4 class="mb-0">{{ $stats['transaksi_bulan_ini'] ?? 0 }}</h4>
                            <small class="text-muted">{{ $stats['transaksi_hari_ini'] ?? 0 }} hari ini</small>
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
                            <h6 class="text-muted mb-0">Total Warga</h6>
                            <h4 class="mb-0">{{ $stats['total_warga'] ?? 0 }}</h4>
                            <small class="text-muted">Warga terdaftar</small>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-success rounded-circle">
                                <i class="bi bi-people-fill fs-4"></i>
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
                            <h6 class="text-muted mb-0">Total Berat Sampah</h6>
                            <h4 class="mb-0">{{ number_format($stats['berat_bulan_ini'] ?? 0, 1) }} kg</h4>
                            <small class="text-muted">{{ number_format($stats['berat_hari_ini'] ?? 0, 1) }} kg hari ini</small>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-warning rounded-circle">
                                <i class="bi bi-trash fs-4"></i>
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
                            <h6 class="text-muted mb-0">Total Poin Diberikan</h6>
                            <h4 class="mb-0">{{ number_format($stats['poin_bulan_ini'] ?? 0, 0, ',', '.') }}</h4>
                            <small class="text-muted">{{ number_format($stats['poin_hari_ini'] ?? 0, 0, ',', '.') }} hari ini</small>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-info rounded-circle">
                                <i class="bi bi-star-fill fs-4"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Section -->
    <div class="row mb-4">
        <div class="col-xl-8 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Statistik Transaksi 30 Hari Terakhir</h5>
                    <div class="dropdown">
                        <button class="btn btn-link p-0 text-muted" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('petugas.statistik.daily') }}">Lihat Detail Harian</a></li>
                            <li><a class="dropdown-item" href="{{ route('petugas.statistik.monthly') }}">Lihat Detail Bulanan</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="transaksiChart" height="300"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4 mb-4">
            <!-- Top Warga -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Top 5 Warga</h5>
                    <a href="{{ route('petugas.statistik.top-warga') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($topWarga ?? [] as $index => $warga)
                        <div class="list-group-item border-0 py-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center">
                                        <span class="fw-bold text-primary">{{ $index + 1 }}</span>
                                    </div>
                                </div>
                                @if($warga->profile_photo_url)
                                <img src="{{ $warga->profile_photo_url }}" 
                                     alt="Foto profil" 
                                     class="rounded-circle me-3" 
                                     width="40" height="40">
                                @else
                                <div class="avatar-sm bg-secondary rounded-circle me-3 d-flex align-items-center justify-content-center">
                                    <i class="bi bi-person text-white"></i>
                                </div>
                                @endif
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">{{ $warga->name ?? 'N/A' }}</h6>
                                    <small class="text-muted">{{ $warga->total_transactions ?? 0 }} transaksi</small>
                                </div>
                                <div class="flex-shrink-0">
                                    <span class="badge bg-primary">
                                        {{ number_format($warga->total_points ?? 0, 0, ',', '.') }} poin
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
            
            <!-- Quick Actions -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Ekspor Data</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('petugas.statistik.export') }}" class="btn btn-outline-success">
                            <i class="bi bi-file-earmark-excel me-2"></i>Ekspor ke Excel
                        </a>
                        <a href="{{ route('petugas.statistik.performance') }}" class="btn btn-outline-primary">
                            <i class="bi bi-graph-up me-2"></i>Lihat Performa Saya
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Ringkasan Bulanan</h5>
                    <div class="dropdown">
                        <select class="form-select form-select-sm" style="width: auto;" id="monthSelector">
                            <option value="1">Januari</option>
                            <option value="2">Februari</option>
                            <option value="3">Maret</option>
                            <option value="4">April</option>
                            <option value="5">Mei</option>
                            <option value="6">Juni</option>
                            <option value="7">Juli</option>
                            <option value="8">Agustus</option>
                            <option value="9">September</option>
                            <option value="10">Oktober</option>
                            <option value="11">November</option>
                            <option value="12" selected>Desember</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Minggu</th>
                                    <th>Jumlah Transaksi</th>
                                    <th>Total Berat (kg)</th>
                                    <th>Total Poin</th>
                                    <th>Warga Terlayani</th>
                                    <th>Rata-rata/Hari</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for($i = 1; $i <= 4; $i++)
                                <tr>
                                    <td>Minggu {{ $i }}</td>
                                    <td>{{ rand(10, 50) }}</td>
                                    <td>{{ number_format(rand(50, 200), 1) }}</td>
                                    <td>{{ number_format(rand(500, 2500), 0, ',', '.') }}</td>
                                    <td>{{ rand(5, 20) }}</td>
                                    <td>{{ number_format(rand(1, 7), 1) }}</td>
                                </tr>
                                @endfor
                                <tr class="table-active">
                                    <td><strong>Total</strong></td>
                                    <td><strong>{{ $stats['transaksi_bulan_ini'] ?? 0 }}</strong></td>
                                    <td><strong>{{ number_format($stats['berat_bulan_ini'] ?? 0, 1) }} kg</strong></td>
                                    <td><strong>{{ number_format($stats['poin_bulan_ini'] ?? 0, 0, ',', '.') }}</strong></td>
                                    <td><strong>{{ rand(20, 50) }}</strong></td>
                                    <td><strong>{{ number_format(($stats['transaksi_bulan_ini'] ?? 0) / 30, 1) }}</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Transaksi Chart
    const transaksiCtx = document.getElementById('transaksiChart').getContext('2d');
    
    // Data contoh untuk 30 hari terakhir
    const labels = [];
    const data = [];
    
    for (let i = 29; i >= 0; i--) {
        const date = new Date();
        date.setDate(date.getDate() - i);
        labels.push(date.getDate() + '/' + (date.getMonth() + 1));
        data.push(Math.floor(Math.random() * 10) + 1); // Data contoh
    }
    
    const transaksiChart = new Chart(transaksiCtx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Jumlah Transaksi',
                data: data,
                borderColor: '#2E8B57',
                backgroundColor: 'rgba(46, 139, 87, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#2E8B57',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4
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
                    mode: 'index',
                    intersect: false
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
                    ticks: {
                        stepSize: 1
                    },
                    grid: {
                        borderDash: [2, 2]
                    }
                }
            }
        }
    });

    // Month selector
    const monthSelector = document.getElementById('monthSelector');
    if (monthSelector) {
        monthSelector.addEventListener('change', function() {
            // Logic untuk mengubah data berdasarkan bulan
            console.log('Mengubah data untuk bulan:', this.value);
            // Di sini Anda bisa menambahkan AJAX request untuk mengambil data bulan yang dipilih
        });
    }
});
</script>
@endpush
@endsection