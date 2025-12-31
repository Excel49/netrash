@extends('layouts.app')
@section('title', 'Dashboard Warga')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2 class="mb-0">Dashboard Warga</h2>
        <p class="text-muted">Selamat datang, {{ Auth::user()->nama_lengkap }}!</p>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('warga.qr') }}" class="btn btn-outline-success">
            <i class="bi bi-qr-code me-1"></i> QR Code Saya
        </a>
    </div>
</div>

<div class="row">
    <!-- Statistik Poin -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                            Total Poin
                        </div>
                        <div class="h5 mb-0 fw-bold text-gray-800">
                            {{ number_format(Auth::user()->total_poin) }}
                        </div>
                        <div class="small text-muted">
                            ≈ Rp {{ number_format(Auth::user()->total_poin * 100, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-star-fill fa-2x text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Transaksi -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-success text-uppercase mb-1">
                            Total Transaksi
                        </div>
                        <div class="h5 mb-0 fw-bold text-gray-800">
                            {{ Auth::user()->transaksiWarga()->count() }}
                        </div>
                        <div class="small text-muted">
                            {{ Auth::user()->transaksiWarga()->where('status', 'selesai')->count() }} selesai
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-receipt fa-2x text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Sampah -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-info text-uppercase mb-1">
                            Total Berat Sampah
                        </div>
                        <div class="h5 mb-0 fw-bold text-gray-800">
                            {{ number_format(Auth::user()->transaksiWarga()->sum('total_berat_kg'), 2) }} kg
                        </div>
                        <div class="small text-muted">
                            Berkontribusi pada lingkungan
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-trash fa-2x text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Penarikan -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-warning text-uppercase mb-1">
                            Penarikan Poin
                        </div>
                        <div class="h5 mb-0 fw-bold text-gray-800">
                            {{ Auth::user()->penarikanPoin()->count() }}
                        </div>
                        <div class="small text-muted">
                            {{ Auth::user()->penarikanPoin()->where('status_pengajuan', 'disetujui')->count() }} disetujui
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-wallet2 fa-2x text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Grafik dan Chart -->
<div class="row mt-4">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-white">
                <h6 class="m-0 fw-bold">Grafik Transaksi 6 Bulan Terakhir</h6>
            </div>
            <div class="card-body">
                <canvas id="transaksiChart" height="250"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-header bg-white">
                <h6 class="m-0 fw-bold">Kategori Sampah</h6>
            </div>
            <div class="card-body">
                <canvas id="kategoriChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Riwayat Transaksi Terbaru -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="m-0 fw-bold">Riwayat Transaksi Terbaru</h6>
                <a href="{{ route('warga.transaksi.history') }}" class="btn btn-sm btn-outline-success">
                    Lihat Semua
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Berat (kg)</th>
                                <th>Total Poin</th>
                                <th>Petugas</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transaksis as $transaksi)
                            <tr>
                                <td>{{ $transaksi->tgl_transaksi->format('d/m/Y H:i') }}</td>
                                <td>{{ number_format($transaksi->total_berat_kg, 2) }}</td>
                                <td>
                                    <span class="badge bg-warning text-dark">
                                        {{ number_format($transaksi->total_poin) }}
                                    </span>
                                </td>
                                <td>{{ $transaksi->petugas->nama_lengkap ?? '-' }}</td>
                                <td>
                                    @if($transaksi->status == 'selesai')
                                        <span class="badge bg-success">Selesai</span>
                                    @elseif($transaksi->status == 'menunggu_konfirmasi')
                                        <span class="badge bg-warning">Menunggu</span>
                                        <a href="{{ route('warga.transaksi.confirm', $transaksi->id) }}" 
                                           class="btn btn-sm btn-success ms-2"
                                           onclick="return confirm('Konfirmasi transaksi?')">
                                            Konfirmasi
                                        </a>
                                    @else
                                        <span class="badge bg-danger">Dibatalkan</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">Belum ada transaksi</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Data untuk chart (dari controller)
    const transaksiData = @json($chartData['transaksi'] ?? []);
    const kategoriData = @json($chartData['kategori'] ?? []);
    
    // Chart Transaksi
    const transaksiCtx = document.getElementById('transaksiChart').getContext('2d');
    new Chart(transaksiCtx, {
        type: 'line',
        data: {
            labels: transaksiData.labels || ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
            datasets: [{
                label: 'Berat Sampah (kg)',
                data: transaksiData.data || [12, 19, 3, 5, 2, 3],
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                borderWidth: 2,
                fill: true
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    
    // Chart Kategori
    const kategoriCtx = document.getElementById('kategoriChart').getContext('2d');
    new Chart(kategoriCtx, {
        type: 'doughnut',
        data: {
            labels: kategoriData.labels || ['Organik', 'Anorganik', 'B3', 'Campuran'],
            datasets: [{
                data: kategoriData.data || [30, 25, 15, 30],
                backgroundColor: [
                    '#28a745',
                    '#007bff',
                    '#dc3545',
                    '#ffc107'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>
@endpush