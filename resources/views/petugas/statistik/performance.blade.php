@extends('layouts.app')

@section('title', 'Performa Petugas')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">Performa Petugas</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('petugas.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('petugas.statistik.index') }}">Statistik</a></li>
                        <li class="breadcrumb-item active">Performa</li>
                    </ol>
                </div>
            </div>
            <p class="text-muted">Halo, {{ auth()->user()->name }}! - Analisis performa Anda</p>
        </div>
    </div>

    <!-- Performance Stats -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-primary border-start border-0 border-4 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-0">Rating</h6>
                            <h4 class="mb-0">4.8/5.0</h4>
                            <small class="text-muted">Dari 24 penilaian</small>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-primary rounded-circle">
                                <i class="bi bi-star-fill fs-4"></i>
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
                            <h6 class="text-muted mb-0">Warga Puas</h6>
                            <h4 class="mb-0">92%</h4>
                            <small class="text-muted">Tingkat kepuasan</small>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-success rounded-circle">
                                <i class="bi bi-emoji-smile fs-4"></i>
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
                            <h6 class="text-muted mb-0">Rata-rata/Hari</h6>
                            <h4 class="mb-0">15.2</h4>
                            <small class="text-muted">Transaksi per hari</small>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-warning rounded-circle">
                                <i class="bi bi-speedometer2 fs-4"></i>
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
                            <h6 class="text-muted mb-0">Peringkat</h6>
                            <h4 class="mb-0">#3</h4>
                            <small class="text-muted">Dari 12 petugas</small>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-info rounded-circle">
                                <i class="bi bi-trophy fs-4"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Chart -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Performa 6 Bulan Terakhir</h5>
                </div>
                <div class="card-body">
                    <canvas id="performanceChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Details -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Detail Performa</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Bulan</th>
                                    <th>Transaksi</th>
                                    <th>Berat (kg)</th>
                                    <th>Poin</th>
                                    <th>Warga</th>
                                    <th>Rating</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                                    $currentMonth = date('n');
                                @endphp
                                
                                @for($i = 5; $i >= 0; $i--)
                                    @php
                                        $monthIndex = ($currentMonth - $i - 1 + 12) % 12;
                                        $monthName = $months[$monthIndex];
                                        $transactions = rand(100, 300);
                                        $weight = rand(500, 1500);
                                        $points = rand(10000, 30000);
                                        $warga = rand(20, 50);
                                        $rating = rand(40, 50) / 10;
                                        $status = $rating >= 4.5 ? 'Excellent' : ($rating >= 4.0 ? 'Good' : 'Average');
                                    @endphp
                                    <tr>
                                        <td>{{ $monthName }}</td>
                                        <td>{{ $transactions }}</td>
                                        <td>{{ number_format($weight, 0) }}</td>
                                        <td>{{ number_format($points, 0, ',', '.') }}</td>
                                        <td>{{ $warga }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                                    <div class="progress-bar" role="progressbar" 
                                                         style="width: {{ $rating * 20 }}%;"></div>
                                                </div>
                                                <span>{{ number_format($rating, 1) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            @if($status == 'Excellent')
                                                <span class="badge bg-success">Excellent</span>
                                            @elseif($status == 'Good')
                                                <span class="badge bg-primary">Good</span>
                                            @else
                                                <span class="badge bg-warning">Average</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endfor
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
    // Performance Chart
    const performanceCtx = document.getElementById('performanceChart').getContext('2d');
    
    const performanceChart = new Chart(performanceCtx, {
        type: 'line',
        data: {
            labels: ['Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            datasets: [
                {
                    label: 'Jumlah Transaksi',
                    data: [120, 180, 150, 200, 220, 250],
                    borderColor: '#2E8B57',
                    backgroundColor: 'rgba(46, 139, 87, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    yAxisID: 'y'
                },
                {
                    label: 'Rating',
                    data: [4.2, 4.5, 4.3, 4.7, 4.8, 4.9],
                    borderColor: '#FFC107',
                    backgroundColor: 'rgba(255, 193, 7, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
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
                        text: 'Rating'
                    },
                    beginAtZero: true,
                    min: 3,
                    max: 5,
                    grid: {
                        drawOnChartArea: false
                    }
                }
            }
        }
    });
});
</script>
@endpush
@endsection