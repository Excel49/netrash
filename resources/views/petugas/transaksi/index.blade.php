@extends('layouts.app')

@section('title', 'Riwayat Transaksi Sampah')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-1">Riwayat Transaksi Sampah</h4>
                    <p class="text-muted mb-0">Daftar transaksi setor sampah yang Anda lakukan</p>
                </div>
                <div>
                    <a href="{{ route('petugas.transaksi.create') }}" class="btn btn-netra me-2">
                        <i class="fas fa-plus-circle me-2"></i>Transaksi Baru
                    </a>
                    <a href="{{ route('petugas.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal Akhir</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-netra me-2">
                        <i class="fas fa-filter me-2"></i>Filter
                    </button>
                    <a href="{{ route('petugas.transaksi.index') }}" class="btn btn-outline-secondary">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Stats -->
    @php
        $totalTransaksi = $transaksi->total() ?? 0;
        $selesai = $transaksi->where('status', 'completed')->count() ?? 0;
        $pending = $transaksi->where('status', 'pending')->count() ?? 0;
        $batal = $transaksi->where('status', 'cancelled')->count() ?? 0;
    @endphp
    
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-start border-primary border-4 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Total Transaksi</h6>
                            <h4 class="mb-1">{{ $totalTransaksi }}</h4>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-exchange-alt fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-start border-success border-4 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Selesai</h6>
                            <h4 class="mb-1">{{ $selesai }}</h4>
                            <small class="text-muted">{{ $totalTransaksi > 0 ? round(($selesai/$totalTransaksi)*100, 1) : 0 }}%</small>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-check-circle fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-start border-warning border-4 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Pending</h6>
                            <h4 class="mb-1">{{ $pending }}</h4>
                            <small class="text-muted">{{ $totalTransaksi > 0 ? round(($pending/$totalTransaksi)*100, 1) : 0 }}%</small>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-clock fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-start border-danger border-4 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Dibatalkan</h6>
                            <h4 class="mb-1">{{ $batal }}</h4>
                            <small class="text-muted">{{ $totalTransaksi > 0 ? round(($batal/$totalTransaksi)*100, 1) : 0 }}%</small>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-times-circle fa-2x text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaksi Table -->
    <div class="card">
        <div class="card-header d-flex align-items-center">
            <i class="fas fa-list-alt me-2"></i>
            <h6 class="mb-0">Daftar Transaksi Setor Sampah</h6>
            <div class="ms-auto text-muted">
                Menampilkan {{ $transaksi->firstItem() ?? 0 }}-{{ $transaksi->lastItem() ?? 0 }} dari {{ $transaksi->total() }} transaksi
            </div>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Kode</th>
                            <th>Warga</th>
                            <th>Tanggal</th>
                            <th>Berat (kg)</th>
                            <th>Poin</th>
                            <th>Status</th>
                            <th class="pe-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transaksi as $trx)
                        <tr>
                            <td class="ps-3">
                                <span class="fw-medium">{{ $trx->kode_transaksi ?? 'N/A' }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($trx->warga && $trx->warga->profile_photo_url)
                                    <img src="{{ $trx->warga->profile_photo_url }}" 
                                         alt="Foto profil" 
                                         class="rounded-circle me-2" 
                                         width="32" height="32">
                                    @endif
                                    <div>
                                        <div class="fw-medium">{{ optional($trx->warga)->name ?? 'N/A' }}</div>
                                        @if($trx->warga && $trx->warga->phone)
                                        <small class="text-muted">{{ $trx->warga->phone }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <small class="text-muted">
                                        @if($trx->tanggal_transaksi instanceof \Carbon\Carbon)
                                            {{ $trx->tanggal_transaksi->format('d/m/Y') }}
                                        @else
                                            {{ \Carbon\Carbon::parse($trx->tanggal_transaksi ?? $trx->created_at)->format('d/m/Y') }}
                                        @endif
                                    </small>
                                    <div>
                                        <small class="text-muted">
                                            @if($trx->tanggal_transaksi instanceof \Carbon\Carbon)
                                                {{ $trx->tanggal_transaksi->format('H:i') }}
                                            @else
                                                {{ \Carbon\Carbon::parse($trx->tanggal_transaksi ?? $trx->created_at)->format('H:i') }}
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="fw-medium">{{ number_format($trx->total_berat ?? 0, 1) }} kg</span>
                            </td>
                            <td>
                                <span class="badge bg-success-subtle text-success">
                                    <i class="fas fa-plus-circle me-1"></i>{{ number_format($trx->total_poin ?? 0, 0, ',', '.') }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $statusColors = [
                                        'completed' => 'success',
                                        'pending' => 'warning',
                                        'cancelled' => 'danger'
                                    ];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$trx->status] ?? 'secondary' }}">
                                    {{ ucfirst($trx->status ?? 'N/A') }}
                                </span>
                            </td>
                            <td class="pe-3">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('petugas.transaksi.show', $trx->id) }}" 
                                       class="btn btn-outline-primary" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('petugas.transaksi.print', $trx->id) }}" 
                                       class="btn btn-outline-secondary" title="Print" target="_blank">
                                        <i class="fas fa-print"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="fas fa-exchange-alt fa-2x d-block mb-2"></i>
                                Belum ada transaksi
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Pagination -->
        @if($transaksi->hasPages())
        <div class="card-footer">
            {{ $transaksi->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

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

.bg-success-subtle {
    background-color: rgba(25, 135, 84, 0.1) !important;
}

@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.875rem;
    }
}
</style>
@endpush