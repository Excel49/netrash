@extends('layouts.app')

@section('title', 'Penarikan Reports')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Laporan Penarikan Poin</h2>
            <div class="d-flex gap-2">
                <!-- Export Button di sini -->
                <div class="btn-group">
                    <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="bi bi-download me-2"></i>Export
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <form action="{{ route('admin.reports.export') }}" method="POST" target="_blank">
                                @csrf
                                <input type="hidden" name="report" value="penarikan">
                                <input type="hidden" name="start_date" value="{{ $startDate }}">
                                <input type="hidden" name="end_date" value="{{ $endDate }}">
                                <input type="hidden" name="type" value="excel">
                                <button type="submit" class="dropdown-item">
                                    <i class="bi bi-file-earmark-excel me-2"></i>Excel (.xlsx)
                                </button>
                            </form>
                        </li>
                        <li>
                            <form action="{{ route('admin.reports.export') }}" method="POST" target="_blank">
                                @csrf
                                <input type="hidden" name="report" value="penarikan">
                                <input type="hidden" name="start_date" value="{{ $startDate }}">
                                <input type="hidden" name="end_date" value="{{ $endDate }}">
                                <input type="hidden" name="type" value="pdf">
                                <button type="submit" class="dropdown-item">
                                    <i class="bi bi-file-earmark-pdf me-2"></i>PDF (.pdf)
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
                <a href="{{ route('admin.reports.index') }}" class="btn btn-netra-outline">
                    <i class="bi bi-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
        <p class="text-muted">Laporan semua pengajuan penarikan poin warga</p>
    </div>
</div>

<!-- Filter Card -->
<div class="card mb-4">
    <div class="card-header bg-light">
        <h5 class="mb-0"><i class="bi bi-funnel me-2"></i>Filter Laporan</h5>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label fw-semibold">Tanggal Mulai</label>
                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Tanggal Akhir</label>
                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Status Penarikan</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-netra flex-fill">
                        <i class="bi bi-filter me-2"></i>Terapkan Filter
                    </button>
                    <a href="{{ route('admin.reports.penarikan') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-clockwise me-2"></i>Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
    <div class="card-footer bg-light">
        <small class="text-muted">
            <i class="bi bi-info-circle me-1"></i>
            Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
        </small>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-primary border-2">
            <div class="card-body text-center">
                <div class="avatar-sm mx-auto mb-3">
                    <div class="avatar-title bg-primary bg-opacity-10 text-primary rounded-circle fs-3">
                        <i class="bi bi-wallet"></i>
                    </div>
                </div>
                <h5 class="card-title text-muted">Total Penarikan</h5>
                <h2 class="text-primary mb-0">{{ number_format($summary['total']) }}</h2>
                <small class="text-muted">pengajuan</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-success border-2">
            <div class="card-body text-center">
                <div class="avatar-sm mx-auto mb-3">
                    <div class="avatar-title bg-success bg-opacity-10 text-success rounded-circle fs-3">
                        <i class="bi bi-star"></i>
                    </div>
                </div>
                <h5 class="card-title text-muted">Total Poin</h5>
                <h2 class="text-success mb-0">{{ number_format($summary['total_poin']) }}</h2>
                <small class="text-muted">poin ditarik</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-info border-2">
            <div class="card-body text-center">
                <div class="avatar-sm mx-auto mb-3">
                    <div class="avatar-title bg-info bg-opacity-10 text-info rounded-circle fs-3">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                </div>
                <h5 class="card-title text-muted">Total Nilai</h5>
                <h2 class="text-info mb-0">Rp {{ number_format($summary['total_rupiah'], 0, ',', '.') }}</h2>
                <small class="text-muted">nilai rupiah</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-warning border-2">
            <div class="card-body text-center">
                <div class="avatar-sm mx-auto mb-3">
                    <div class="avatar-title bg-warning bg-opacity-10 text-warning rounded-circle fs-3">
                        <i class="bi bi-clock-history"></i>
                    </div>
                </div>
                <h5 class="card-title text-muted">Pending</h5>
                <h2 class="text-warning mb-0">{{ number_format($summary['pending']) }}</h2>
                <small class="text-muted">menunggu persetujuan</small>
            </div>
        </div>
    </div>
</div>

<!-- Status Distribution -->
<div class="card mb-4">
    <div class="card-header bg-light">
        <h5 class="mb-0"><i class="bi bi-pie-chart me-2"></i>Distribusi Status</h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="card h-100 border-start border-warning border-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm me-3">
                                <div class="avatar-title bg-warning text-white rounded-circle">
                                    <i class="bi bi-clock"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="mb-0">{{ $summary['pending'] }}</h5>
                                <p class="text-muted mb-0">Pending</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card h-100 border-start border-info border-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm me-3">
                                <div class="avatar-title bg-info text-white rounded-circle">
                                    <i class="bi bi-check-circle"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="mb-0">{{ $summary['approved'] }}</h5>
                                <p class="text-muted mb-0">Disetujui</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card h-100 border-start border-success border-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm me-3">
                                <div class="avatar-title bg-success text-white rounded-circle">
                                    <i class="bi bi-check2-all"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="mb-0">{{ $summary['completed'] }}</h5>
                                <p class="text-muted mb-0">Selesai</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card h-100 border-start border-danger border-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm me-3">
                                <div class="avatar-title bg-danger text-white rounded-circle">
                                    <i class="bi bi-x-circle"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="mb-0">{{ $summary['rejected'] }}</h5>
                                <p class="text-muted mb-0">Ditolak</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Withdrawals Table -->
<div class="card">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-table me-2"></i>Data Penarikan Poin</h5>
        <span class="badge bg-netra">{{ $penarikan->count() }} Data</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-striped" id="penarikanTable">
                <thead class="table-light">
                    <tr>
                        <th width="80">ID</th>
                        <th width="160">Tanggal Pengajuan</th>
                        <th>Warga</th>
                        <th width="120" class="text-end">Jumlah Poin</th>
                        <th width="140" class="text-end">Nilai Rupiah</th>
                        <th width="100">Status</th>
                        <th>Admin</th>
                        <th width="80" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penarikan as $item)
                    <tr>
                        <td>
                            <span class="badge bg-secondary">#{{ $item->id }}</span>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="fw-semibold">{{ $item->tanggal_pengajuan->format('d/m/Y') }}</span>
                                <small class="text-muted">{{ $item->tanggal_pengajuan->format('H:i') }}</small>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm me-2">
                                    <div class="avatar-title bg-info text-white rounded-circle">
                                        {{ substr($item->warga->name, 0, 1) }}
                                    </div>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $item->warga->name }}</h6>
                                    <small class="text-muted">{{ $item->warga->email }}</small>
                                </div>
                            </div>
                        </td>
                        <td class="text-end">
                            <span class="badge bg-warning fs-6">{{ number_format($item->jumlah_poin) }}</span>
                        </td>
                        <td class="text-end fw-bold text-success">
                            Rp {{ number_format($item->jumlah_rupiah, 0, ',', '.') }}
                        </td>
                        <td>
                            @if($item->status == 'pending')
                                <span class="badge bg-warning py-2 px-3">Pending</span>
                            @elseif($item->status == 'approved')
                                <span class="badge bg-info py-2 px-3">Disetujui</span>
                            @elseif($item->status == 'completed')
                                <span class="badge bg-success py-2 px-3">Selesai</span>
                            @else
                                <span class="badge bg-danger py-2 px-3">Ditolak</span>
                            @endif
                        </td>
                        <td>
                            @if($item->admin)
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm me-2">
                                    <div class="avatar-title bg-primary text-white rounded-circle">
                                        {{ substr($item->admin->name, 0, 1) }}
                                    </div>
                                </div>
                                <div>
                                    <small class="fw-semibold">{{ $item->admin->name }}</small><br>
                                    <small class="text-muted">
                                        @if($item->tanggal_approval)
                                        {{ $item->tanggal_approval->format('d/m/Y') }}
                                        @endif
                                    </small>
                                </div>
                            </div>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.penarikan.show', $item->id) }}" 
                               class="btn btn-sm btn-outline-netra" 
                               title="Detail"
                               data-bs-toggle="tooltip">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="py-5">
                                <i class="bi bi-inbox fs-1 text-muted"></i>
                                <h5 class="mt-3">Tidak ada data penarikan</h5>
                                <p class="text-muted">Tidak ditemukan data penarikan poin pada periode ini</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($penarikan->count() > 0)
                <tfoot class="table-light">
                    <tr>
                        <th colspan="3" class="text-end">TOTAL:</th>
                        <th class="text-end">
                            <span class="badge bg-warning fs-6">{{ number_format($summary['total_poin']) }}</span>
                        </th>
                        <th class="text-end fw-bold text-success">
                            Rp {{ number_format($summary['total_rupiah'], 0, ',', '.') }}
                        </th>
                        <th colspan="3"></th>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
        
        @if($penarikan->count() > 0)
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div>
                <div class="alert alert-light mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    Menampilkan <strong>{{ $penarikan->count() }}</strong> data penarikan poin
                </div>
            </div>
            <div class="text-end">
                <small class="text-muted">
                    <i class="bi bi-calendar me-1"></i>
                    Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
                </small>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Quick Stats -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-calculator me-2"></i>Statistik Singkat</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <div class="border rounded p-3 text-center">
                            <h6 class="text-muted">Rata-rata Poin</h6>
                            <h3 class="text-primary">
                                @if($summary['total'] > 0)
                                {{ number_format($summary['total_poin'] / $summary['total'], 0) }}
                                @else
                                0
                                @endif
                            </h3>
                            <small class="text-muted">poin per penarikan</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-3 text-center">
                            <h6 class="text-muted">Rata-rata Nilai</h6>
                            <h3 class="text-success">
                                @if($summary['total'] > 0)
                                Rp {{ number_format($summary['total_rupiah'] / $summary['total'], 0, ',', '.') }}
                                @else
                                Rp 0
                                @endif
                            </h3>
                            <small class="text-muted">rupiah per penarikan</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-percent me-2"></i>Persentase Status</h5>
            </div>
            <div class="card-body">
                <div class="progress-stacked mb-3">
                    @if($summary['total'] > 0)
                        @php
                            $pendingPercent = ($summary['pending'] / $summary['total']) * 100;
                            $approvedPercent = ($summary['approved'] / $summary['total']) * 100;
                            $completedPercent = ($summary['completed'] / $summary['total']) * 100;
                            $rejectedPercent = ($summary['rejected'] / $summary['total']) * 100;
                        @endphp
                        <div class="progress" style="width: {{ $pendingPercent }}%" 
                             role="progressbar" aria-valuenow="{{ $pendingPercent }}" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar bg-warning" title="Pending: {{ round($pendingPercent, 1) }}%"></div>
                        </div>
                        <div class="progress" style="width: {{ $approvedPercent }}%" 
                             role="progressbar" aria-valuenow="{{ $approvedPercent }}" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar bg-info" title="Disetujui: {{ round($approvedPercent, 1) }}%"></div>
                        </div>
                        <div class="progress" style="width: {{ $completedPercent }}%" 
                             role="progressbar" aria-valuenow="{{ $completedPercent }}" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar bg-success" title="Selesai: {{ round($completedPercent, 1) }}%"></div>
                        </div>
                        <div class="progress" style="width: {{ $rejectedPercent }}%" 
                             role="progressbar" aria-valuenow="{{ $rejectedPercent }}" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar bg-danger" title="Ditolak: {{ round($rejectedPercent, 1) }}%"></div>
                        </div>
                    @else
                        <div class="progress w-100">
                            <div class="progress-bar bg-secondary" style="width: 100%">Tidak ada data</div>
                        </div>
                    @endif
                </div>
                <div class="row text-center">
                    <div class="col-3">
                        <small class="text-warning fw-semibold">{{ round($pendingPercent ?? 0, 1) }}%</small><br>
                        <small>Pending</small>
                    </div>
                    <div class="col-3">
                        <small class="text-info fw-semibold">{{ round($approvedPercent ?? 0, 1) }}%</small><br>
                        <small>Disetujui</small>
                    </div>
                    <div class="col-3">
                        <small class="text-success fw-semibold">{{ round($completedPercent ?? 0, 1) }}%</small><br>
                        <small>Selesai</small>
                    </div>
                    <div class="col-3">
                        <small class="text-danger fw-semibold">{{ round($rejectedPercent ?? 0, 1) }}%</small><br>
                        <small>Ditolak</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Enable tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
    
    // Set default dates if not set
    $('input[type="date"][name="start_date"]').on('change', function() {
        const endDate = $('input[type="date"][name="end_date"]');
        if (!endDate.val()) {
            endDate.val($(this).val());
        }
    });
});
</script>
@endsection

@push('styles')
<style>
.avatar-sm {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.avatar-title {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}
.progress-stacked {
    height: 10px;
    border-radius: 5px;
    overflow: hidden;
}
.table th {
    font-weight: 600;
    background-color: #f8f9fa;
}
.border-2 {
    border-width: 2px !important;
}
.card-header.bg-light {
    background-color: #f8f9fa !important;
}
</style>
@endpush