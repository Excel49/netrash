@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Riwayat Transaksi Sampah</h2>
            <div>
                <a href="{{ route('warga.dashboard') }}" class="btn btn-netra-outline">
                    <i class="bi bi-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
        <p class="text-muted">Daftar semua transaksi setoran sampah Anda</p>
    </div>
</div>

<!-- Stats -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6 class="card-title">Total Transaksi</h6>
                <h3>{{ $transaksi->total() }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6 class="card-title">Total Poin</h6>
                <h3>{{ number_format(auth()->user()->total_points, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h6 class="card-title">Total Berat</h6>
                <h3>{{ number_format($transaksi->sum('total_berat'), 1) }} kg</h3>
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
                <label class="form-label">Petugas</label>
                <select name="petugas_id" class="form-select">
                    <option value="">Semua Petugas</option>
                    @foreach($transaksi->pluck('petugas')->unique() as $petugas)
                    @if($petugas)
                    <option value="{{ $petugas->id }}" {{ request('petugas_id') == $petugas->id ? 'selected' : '' }}>
                        {{ $petugas->name }}
                    </option>
                    @endif
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-netra me-2">
                    <i class="bi bi-filter me-2"></i>Filter
                </button>
                <a href="{{ route('warga.transaksi.index') }}" class="btn btn-outline-secondary">
                    Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Table -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Kode Transaksi</th>
                        <th>Petugas</th>
                        <th>Berat (kg)</th>
                        <th>Total Poin</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksi as $trx)
                    <tr>
                        <td>{{ $trx->tanggal_transaksi->format('d/m/Y H:i') }}</td>
                        <td>{{ $trx->kode_transaksi }}</td>
                        <td>{{ $trx->petugas->name ?? 'Sistem' }}</td>
                        <td>{{ number_format($trx->total_berat, 1) }}</td>
                        <td class="text-success fw-bold">+{{ number_format($trx->total_poin, 0, ',', '.') }}</td>
                        <td>
                            @if($trx->status == 'completed')
                            <span class="badge bg-success">Selesai</span>
                            @elseif($trx->status == 'pending')
                            <span class="badge bg-warning">Pending</span>
                            @else
                            <span class="badge bg-danger">Dibatalkan</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('warga.transaksi.show', $trx->id) }}" 
                                   class="btn btn-outline-primary" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
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