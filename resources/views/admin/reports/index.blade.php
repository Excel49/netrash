@extends('layouts.app')

@section('title', 'Reports Dashboard')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Reports Dashboard</h2>
            <div class="btn-group">
                <a href="{{ route('admin.reports.transaksi') }}" class="btn btn-netra">
                    <i class="bi bi-arrow-clockwise me-2"></i>Transaksi
                </a>
                <a href="{{ route('admin.reports.penarikan') }}" class="btn btn-netra-outline">
                    <i class="bi bi-wallet me-2"></i>Penarikan
                </a>
            </div>
        </div>
        <p class="text-muted">Statistik dan laporan sistem NetraTrash</p>
    </div>
</div>

<!-- Summary Statistics -->
<div class="row mb-4">
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Total Transaksi</h6>
                        <h3 class="mb-0">{{ number_format($stats['total_transaksi']) }}</h3>
                    </div>
                    <i class="bi bi-arrow-clockwise fs-1"></i>
                </div>
                <div class="mt-2">
                    <small>Riwayat semua transaksi</small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Total Penarikan</h6>
                        <h3 class="mb-0">{{ number_format($stats['total_penarikan']) }}</h3>
                    </div>
                    <i class="bi bi-wallet fs-1"></i>
                </div>
                <div class="mt-2">
                    <small>Total pengajuan penarikan</small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Total Pendapatan</h6>
                        <h3 class="mb-0">Rp {{ number_format($stats['total_pendapatan'], 0, ',', '.') }}</h3>
                    </div>
                    <i class="bi bi-cash-coin fs-1"></i>
                </div>
                <div class="mt-2">
                    <small>Total nilai transaksi</small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Total Poin</h6>
                        <h3 class="mb-0">{{ number_format($stats['total_poin_dikeluarkan'] - $stats['total_poin_ditarik'], 0, ',', '.') }}</h3>
                    </div>
                    <i class="bi bi-star fs-1"></i>
                </div>
                <div class="mt-2">
                    <small>Poin aktif dalam sistem</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Reports -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Quick Reports</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 border">
                            <div class="card-body text-center">
                                <i class="bi bi-arrow-clockwise fs-1 text-primary mb-3"></i>
                                <h5>Transaksi Reports</h5>
                                <p class="text-muted">Laporan semua transaksi sampah</p>
                                <a href="{{ route('admin.reports.transaksi') }}" class="btn btn-primary btn-sm">
                                    Lihat Laporan <i class="bi bi-arrow-right ms-2"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 border">
                            <div class="card-body text-center">
                                <i class="bi bi-wallet fs-1 text-success mb-3"></i>
                                <h5>Penarikan Reports</h5>
                                <p class="text-muted">Laporan penarikan poin warga</p>
                                <a href="{{ route('admin.reports.penarikan') }}" class="btn btn-success btn-sm">
                                    Lihat Laporan <i class="bi bi-arrow-right ms-2"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 border">
                            <div class="card-body text-center">
                                <i class="bi bi-people fs-1 text-info mb-3"></i>
                                <h5>Users Reports</h5>
                                <p class="text-muted">Laporan data pengguna</p>
                                <a href="{{ route('admin.reports.users') }}" class="btn btn-info btn-sm">
                                    Lihat Laporan <i class="bi bi-arrow-right ms-2"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Recent Transactions</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Warga</th>
                                <th>Berat</th>
                                <th>Poin</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(\App\Models\Transaksi::with('warga')->latest()->take(5)->get() as $transaksi)
                            <tr>
                                <td>{{ $transaksi->kode_transaksi }}</td>
                                <td>{{ $transaksi->warga->name }}</td>
                                <td>{{ number_format($transaksi->total_berat, 1) }} kg</td>
                                <td><span class="badge bg-success">{{ number_format($transaksi->total_poin) }}</span></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">No transactions yet</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Recent Withdrawals</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Warga</th>
                                <th>Poin</th>
                                <th>Nilai</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(\App\Models\PenarikanPoin::with('warga')->latest()->take(5)->get() as $penarikan)
                            <tr>
                                <td>{{ $penarikan->warga->name }}</td>
                                <td>{{ number_format($penarikan->jumlah_poin) }}</td>
                                <td>Rp {{ number_format($penarikan->jumlah_rupiah, 0, ',', '.') }}</td>
                                <td>
                                    @if($penarikan->status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                    @elseif($penarikan->status == 'approved')
                                    <span class="badge bg-info">Approved</span>
                                    @elseif($penarikan->status == 'completed')
                                    <span class="badge bg-success">Completed</span>
                                    @else
                                    <span class="badge bg-danger">Rejected</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">No withdrawals yet</td>
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

@section('scripts')
<script>
$(document).ready(function() {
    // Load chart data for dashboard
    loadChartData();
    
    function loadChartData() {
        $.ajax({
            url: '{{ route("admin.reports.dashboard-stats") }}',
            method: 'GET',
            success: function(response) {
                // You can add chart.js here if needed
                console.log('Chart data loaded');
            }
        });
    }
});
</script>
@endsection