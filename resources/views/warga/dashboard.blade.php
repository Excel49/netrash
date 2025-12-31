@extends('layouts.app')

@section('title', 'Dashboard Warga')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2 class="mb-0">Dashboard Warga</h2>
        <p class="text-muted">Selamat datang, {{ auth()->user()->name }}!</p>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-primary h-100">
            <div class="card-body stat-card">
                <div class="row align-items-center">
                    <div class="col-8">
                        <h6 class="text-uppercase mb-0">Total Poin</h6>
                        <div class="stat-number">{{ number_format(auth()->user()->total_points, 0, ',', '.') }}</div>
                        <small class="text-muted">Rp {{ number_format(auth()->user()->total_points * 100, 0, ',', '.') }}</small>
                    </div>
                    <div class="col-4 text-end">
                        <i class="bi bi-coin stat-icon text-primary"></i>
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
                        <small class="text-muted">{{ number_format($totalBerat, 1) }} kg sampah</small>
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
                        <h6 class="text-uppercase mb-0">Poin Ditarik</h6>
                        <div class="stat-number">{{ number_format($totalPoinDitarik, 0, ',', '.') }}</div>
                        <small class="text-muted">Rp {{ number_format($totalPoinDitarik * 100, 0, ',', '.') }}</small>
                    </div>
                    <div class="col-4 text-end">
                        <i class="bi bi-cash-stack stat-icon text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- QR Code Card -->
    <div class="col-lg-5 mb-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">QR Code Saya</h6>
            </div>
            <div class="card-body text-center">
                @if(auth()->user()->qr_code)
                    <img src="{{ asset('storage/' . auth()->user()->qr_code) }}" alt="QR Code" class="img-fluid mb-3" width="200">
                @else
                    <div class="mb-4" style="background-color: #f8f9fa; padding: 20px; border-radius: 10px;">
                        <div class="text-muted mb-2">QR Code belum tersedia</div>
                        <div class="small text-muted">Hubungi admin untuk generate QR Code</div>
                    </div>
                @endif
                <p class="text-muted">Tunjukkan QR Code ini ke petugas untuk transaksi</p>
                <button onclick="alert('Fitur download akan segera tersedia')" class="btn btn-netra">
                    <i class="bi bi-download me-2"></i>Download QR Code
                </button>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">Aksi Cepat</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('warga.penarikan.create') }}" class="btn btn-netra">
                        <i class="bi bi-cash-coin me-2"></i>Ajukan Penarikan Poin
                    </a>
                    <a href="{{ route('warga.transaksi.index') }}" class="btn btn-netra-outline">
                        <i class="bi bi-history me-2"></i>Lihat Riwayat Transaksi
                    </a>
                    <a href="{{ route('warga.kategori.index') }}" class="btn btn-netra-outline">
                        <i class="bi bi-info-circle me-2"></i>Informasi Kategori
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Transactions -->
    <div class="col-lg-7 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Transaksi Terbaru</h6>
                <a href="{{ route('warga.transaksi.index') }}" class="btn btn-sm btn-netra">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Kode Transaksi</th>
                                <th>Berat (kg)</th>
                                <th>Poin</th>
                                <th>Petugas</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTransactions as $transaksi)
                            <tr>
                                <td>{{ $transaksi->tanggal_transaksi->format('d/m/Y') }}</td>
                                <td>{{ $transaksi->kode_transaksi }}</td>
                                <td>{{ number_format($transaksi->total_berat, 1) }}</td>
                                <td class="text-success fw-bold">+{{ number_format($transaksi->total_poin, 0, ',', '.') }}</td>
                                <td>{{ $transaksi->petugas->name }}</td>
                                <td>
                                    @if($transaksi->status == 'completed')
                                    <span class="badge bg-success">Selesai</span>
                                    @else
                                    <span class="badge bg-warning">{{ $transaksi->status }}</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    Belum ada transaksi
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Poin History Chart -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">Riwayat Poin 30 Hari Terakhir</h6>
            </div>
            <div class="card-body">
                <canvas id="poinChart" height="150"></canvas>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Poin History Chart
    const poinCtx = document.getElementById('poinChart').getContext('2d');
    const poinChart = new Chart(poinCtx, {
        type: 'line',
        data: {
            labels: @json($chartLabels),
            datasets: [{
                label: 'Poin',
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
                        callback: function(value) {
                            return value.toLocaleString('id-ID');
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