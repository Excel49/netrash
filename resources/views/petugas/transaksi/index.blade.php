@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Riwayat Transaksi</h2>
            <div>
                <a href="{{ route('petugas.transaksi.create') }}" class="btn btn-netra me-2">
                    <i class="bi bi-plus-circle me-2"></i>Transaksi Baru
                </a>
                <a href="{{ route('petugas.dashboard') }}" class="btn btn-netra-outline">
                    <i class="bi bi-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
        <p class="text-muted">Daftar semua transaksi yang Anda lakukan</p>
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
                    <option value="">Semua</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-netra me-2">
                    <i class="bi bi-filter me-2"></i>Filter
                </button>
                <a href="{{ route('petugas.transaksi.index') }}" class="btn btn-outline-secondary">
                    Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Stats -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6 class="card-title">Total Transaksi</h6>
                <h3>{{ $transaksi->total() ?? 0 }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6 class="card-title">Selesai</h6>
                <h3>{{ $transaksi->where('status', 'completed')->count() ?? 0 }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h6 class="card-title">Pending</h6>
                <h3>{{ $transaksi->where('status', 'pending')->count() ?? 0 }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <h6 class="card-title">Dibatalkan</h6>
                <h3>{{ $transaksi->where('status', 'cancelled')->count() ?? 0 }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Table -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Warga</th>
                        <th>Tanggal</th>
                        <th>Berat (kg)</th>
                        <th>Harga</th>
                        <th>Poin</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksi as $trx)
                    <tr>
                        <td>{{ $trx->kode_transaksi ?? 'N/A' }}</td>
                        <td>{{ $trx->warga->name ?? 'N/A' }}</td>
                        <td>
                            @php
                                // Cek jika tanggal_transaksi adalah objek Carbon atau string
                                if ($trx->tanggal_transaksi instanceof \Carbon\Carbon) {
                                    echo $trx->tanggal_transaksi->format('d/m/Y H:i');
                                } else {
                                    echo \Carbon\Carbon::parse($trx->tanggal_transaksi ?? $trx->created_at)->format('d/m/Y H:i');
                                }
                            @endphp
                        </td>
                        <td>{{ number_format($trx->total_berat ?? 0, 1) }}</td>
                        <td>Rp {{ number_format($trx->total_harga ?? 0, 0, ',', '.') }}</td>
                        <td>{{ number_format($trx->total_poin ?? 0, 0, ',', '.') }}</td>
                        <td>
                            @if(($trx->status ?? '') == 'completed')
                            <span class="badge bg-success">Selesai</span>
                            @elseif(($trx->status ?? '') == 'pending')
                            <span class="badge bg-warning">Pending</span>
                            @else
                            <span class="badge bg-danger">Dibatalkan</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('petugas.transaksi.show', $trx->id) }}" 
                                   class="btn btn-outline-primary" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('petugas.transaksi.print', $trx->id) }}" 
                                   class="btn btn-outline-secondary" title="Print" target="_blank">
                                    <i class="bi bi-printer"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="bi bi-receipt display-4 d-block mb-2"></i>
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
@endsection