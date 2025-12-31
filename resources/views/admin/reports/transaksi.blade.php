@extends('layouts.app')

@section('title', 'Transaksi Reports')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Transaksi Reports</h2>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-netra-outline">
                <i class="bi bi-arrow-left me-2"></i>Kembali
            </a>
        </div>
        <p class="text-muted">Laporan semua transaksi</p>
    </div>
</div>

<!-- Filter Card -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Start Date</label>
                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">End Date</label>
                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Petugas</label>
                <select name="petugas_id" class="form-select">
                    <option value="">All Petugas</option>
                    @foreach($petugasList as $petugas)
                    <option value="{{ $petugas->id }}" {{ request('petugas_id') == $petugas->id ? 'selected' : '' }}>
                        {{ $petugas->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-12">
                <button type="submit" class="btn btn-netra">
                    <i class="bi bi-filter me-2"></i>Filter
                </button>
                <a href="{{ route('admin.reports.transaksi') }}" class="btn btn-outline-secondary">
                    Reset
                </a>
                
                <!-- Export Buttons -->
                <div class="btn-group ms-2">
                    <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="bi bi-download me-2"></i>Export
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <form action="{{ route('admin.reports.export') }}" method="POST" target="_blank">
                                @csrf
                                <input type="hidden" name="report" value="transaksi">
                                <input type="hidden" name="start_date" value="{{ $startDate }}">
                                <input type="hidden" name="end_date" value="{{ $endDate }}">
                                <input type="hidden" name="type" value="excel">
                                <button type="submit" class="dropdown-item">
                                    <i class="bi bi-file-earmark-excel me-2"></i>Excel
                                </button>
                            </form>
                        </li>
                        <li>
                            <form action="{{ route('admin.reports.export') }}" method="POST" target="_blank">
                                @csrf
                                <input type="hidden" name="report" value="transaksi">
                                <input type="hidden" name="start_date" value="{{ $startDate }}">
                                <input type="hidden" name="end_date" value="{{ $endDate }}">
                                <input type="hidden" name="type" value="pdf">
                                <button type="submit" class="dropdown-item">
                                    <i class="bi bi-file-earmark-pdf me-2"></i>PDF
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6 class="card-title">Total Transaksi</h6>
                <h3>{{ number_format($summary['total_transaksi']) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6 class="card-title">Total Berat</h6>
                <h3>{{ number_format($summary['total_berat'], 1) }} kg</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h6 class="card-title">Total Pendapatan</h6>
                <h3>Rp {{ number_format($summary['total_harga'], 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h6 class="card-title">Total Poin</h6>
                <h3>{{ number_format($summary['total_poin']) }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Transactions Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="transaksiTable">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Tanggal</th>
                        <th>Warga</th>
                        <th>Petugas</th>
                        <th>Berat (kg)</th>
                        <th>Harga (Rp)</th>
                        <th>Poin</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksi as $item)
                    <tr>
                        <td>
                            <span class="badge bg-secondary">{{ $item->kode_transaksi }}</span>
                        </td>
                        <td>{{ $item->tanggal_transaksi->format('d/m/Y H:i') }}</td>
                        <td>{{ $item->warga->name }}</td>
                        <td>{{ $item->petugas->name }}</td>
                        <td>{{ number_format($item->total_berat, 1) }}</td>
                        <td>Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                        <td>
                            <span class="badge bg-success">{{ number_format($item->total_poin) }}</span>
                        </td>
                        <td>
                            @if($item->status == 'completed')
                                <span class="badge bg-success">Selesai</span>
                            @elseif($item->status == 'pending')
                                <span class="badge bg-warning">Pending</span>
                            @else
                                <span class="badge bg-danger">Dibatalkan</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.transaksi.show', $item->id) }}" 
                               class="btn btn-sm btn-outline-primary" 
                               title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center">Tidak ada data transaksi</td>
                    </tr>
                    @endforelse
                </tbody>
                @if($transaksi->count() > 0)
                <tfoot>
                    <tr class="table-light">
                        <td colspan="4" class="text-end"><strong>Total:</strong></td>
                        <td><strong>{{ number_format($summary['total_berat'], 1) }} kg</strong></td>
                        <td><strong>Rp {{ number_format($summary['total_harga'], 0, ',', '.') }}</strong></td>
                        <td><strong>{{ number_format($summary['total_poin']) }}</strong></td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
        
        @if($transaksi->count() > 0)
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div>
                <p class="mb-0">Menampilkan {{ $transaksi->count() }} transaksi</p>
            </div>
            <div>
                <small class="text-muted">Data periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</small>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Statistics Chart -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Rata-rata per Transaksi</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <div class="text-center p-3 border rounded">
                            <h6 class="text-muted">Rata-rata Berat</h6>
                            <h3 class="text-primary">{{ number_format($summary['avg_berat'] ?? 0, 1) }} kg</h3>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center p-3 border rounded">
                            <h6 class="text-muted">Rata-rata Harga</h6>
                            <h3 class="text-success">Rp {{ number_format($summary['avg_harga'] ?? 0, 0, ',', '.') }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Status Transaksi</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @php
                        $completedCount = $transaksi->where('status', 'completed')->count();
                        $pendingCount = $transaksi->where('status', 'pending')->count();
                        $cancelledCount = $transaksi->where('status', 'cancelled')->count();
                        $totalCount = $transaksi->count();
                    @endphp
                    <div class="col-4">
                        <div class="text-center p-3 border rounded">
                            <h6 class="text-muted">Selesai</h6>
                            <h3 class="text-success">{{ $completedCount }}</h3>
                            @if($totalCount > 0)
                            <small>{{ round(($completedCount/$totalCount)*100, 1) }}%</small>
                            @endif
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="text-center p-3 border rounded">
                            <h6 class="text-muted">Pending</h6>
                            <h3 class="text-warning">{{ $pendingCount }}</h3>
                            @if($totalCount > 0)
                            <small>{{ round(($pendingCount/$totalCount)*100, 1) }}%</small>
                            @endif
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="text-center p-3 border rounded">
                            <h6 class="text-muted">Dibatalkan</h6>
                            <h3 class="text-danger">{{ $cancelledCount }}</h3>
                            @if($totalCount > 0)
                            <small>{{ round(($cancelledCount/$totalCount)*100, 1) }}%</small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection