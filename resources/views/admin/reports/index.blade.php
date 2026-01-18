@extends('layouts.app')

@section('title', 'Dashboard Reports')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-0">
                    <i class="fas fa-chart-line me-2"></i> Dashboard Reports
                </h2>
                <p class="text-muted mb-0">Analisis dan statistik sistem NetraTrash</p>
            </div>
            <div>
                <button class="btn btn-netra" onclick="window.print()">
                    <i class="fas fa-print me-2"></i> Print Dashboard
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Report Selection Tabs -->
<div class="card mb-4">
    <div class="card-header bg-light p-0">
        <ul class="nav nav-tabs nav-fill" id="reportTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $activeTab == 'sampah' ? 'active' : '' }}" id="sampah-tab" data-bs-toggle="tab" data-bs-target="#sampah" type="button" role="tab">
                    <i class="fas fa-trash me-2"></i> Laporan Transaksi Sampah
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $activeTab == 'penukaran' ? 'active' : '' }}" id="penukaran-tab" data-bs-toggle="tab" data-bs-target="#penukaran" type="button" role="tab">
                    <i class="fas fa-exchange-alt me-2"></i> Laporan Penukaran Barang
                </button>
            </li>
        </ul>
    </div>
</div>

<!-- Transaksi Sampah Tab -->
<div class="tab-content" id="reportTabsContent">
    <!-- Tab 1: Transaksi Sampah -->
    <div class="tab-pane fade {{ $activeTab == 'sampah' ? 'show active' : '' }}" id="sampah" role="tabpanel">
        <!-- Filter Card for Sampah -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-filter me-2"></i> Filter Laporan Transaksi Sampah
                </h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.reports.index') }}" class="row g-3">
                    <input type="hidden" name="tab" value="sampah">
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" name="start_date" class="form-control" 
                               value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Selesai</label>
                        <input type="date" name="end_date" class="form-control" 
                               value="{{ request('end_date') }}">
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
                    <div class="col-md-3">
                        <label class="form-label">Petugas</label>
                        <select name="petugas_id" class="form-select">
                            <option value="">Semua Petugas</option>
                            @foreach($petugasList as $petugas)
                            <option value="{{ $petugas->id }}" {{ request('petugas_id') == $petugas->id ? 'selected' : '' }}>
                                {{ $petugas->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-netra">
                            <i class="fas fa-filter me-2"></i> Filter Data Sampah
                        </button>
                        <a href="{{ route('admin.reports.index') }}?tab=sampah" class="btn btn-outline-secondary">
                            <i class="fas fa-redo me-2"></i> Reset Filter
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary Statistics for Sampah -->
        @if(isset($sampahSummary))
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-start border-success border-4 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Total Transaksi Sampah</h6>
                                <h3 class="mb-0">{{ number_format($sampahSummary['total_transaksi'] ?? 0) }}</h3>
                                <small class="text-muted">
                                    <i class="fas fa-calendar-alt me-1"></i> 
                                    @if(request('start_date') && request('end_date'))
                                        {{ \Carbon\Carbon::parse(request('start_date'))->format('d/m/Y') }} 
                                        - {{ \Carbon\Carbon::parse(request('end_date'))->format('d/m/Y') }}
                                    @else
                                        Semua Data
                                    @endif
                                </small>
                            </div>
                            <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                                <i class="fas fa-trash fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-start border-info border-4 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Total Berat Sampah</h6>
                                <h3 class="mb-0">{{ number_format($sampahSummary['total_berat'] ?? 0, 1) }} kg</h3>
                                <small class="text-muted">
                                    <i class="fas fa-weight-hanging me-1"></i> Berat terkumpul
                                </small>
                            </div>
                            <div class="bg-info bg-opacity-10 p-3 rounded-circle">
                                <i class="fas fa-weight fa-2x text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-start border-warning border-4 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Total Poin Diberikan</h6>
                                <h3 class="mb-0">{{ number_format($sampahSummary['total_poin'] ?? 0) }}</h3>
                                <small class="text-muted">
                                    <i class="fas fa-coins me-1"></i> Poin diberikan
                                </small>
                            </div>
                            <div class="bg-warning bg-opacity-10 p-3 rounded-circle">
                                <i class="fas fa-coins fa-2x text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-start border-primary border-4 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Warga Aktif</h6>
                                <h3 class="mb-0">{{ number_format($sampahSummary['total_warga'] ?? 0) }}</h3>
                                <small class="text-muted">
                                    <i class="fas fa-users me-1"></i> Partisipan transaksi
                                </small>
                            </div>
                            <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                                <i class="fas fa-user-check fa-2x text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transactions Table for Sampah -->
        <div class="card">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-trash me-2"></i> Daftar Transaksi Sampah
                </h5>
                <div>
                    <button type="button" class="btn btn-sm btn-netra" data-bs-toggle="modal" data-bs-target="#exportSampahModal">
                        <i class="fas fa-download me-1"></i> Export Transaksi Sampah
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 5%;">ID</th>
                                <th style="width: 15%;">Tanggal</th>
                                <th style="width: 25%;">Warga</th>
                                <th style="width: 20%;">Petugas</th>
                                <th style="width: 10%;" class="text-end">Berat (kg)</th>
                                <th style="width: 10%;" class="text-end">Poin</th>
                                <th style="width: 10%;">Status</th>
                                <th style="width: 5%;" class="text-center">Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sampahTransaksi ?? [] as $item)
                            <tr>
                                <td>
                                    <span class="badge bg-secondary">#{{ $item->id }}</span>
                                </td>
                                <td>
                                    <small class="text-muted d-block">{{ $item->created_at->format('d/m/Y') }}</small>
                                    <small class="text-muted">{{ $item->created_at->format('H:i') }}</small>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $item->warga->name ?? 'N/A' }}</div>
                                    @if($item->warga->phone ?? false)
                                    <small class="text-muted">{{ $item->warga->phone }}</small>
                                    @endif
                                </td>
                                <td>{{ $item->petugas->name ?? 'N/A' }}</td>
                                <td class="text-end fw-semibold">{{ number_format($item->total_berat, 2) }}</td>
                                <td class="text-end">
                                    <span class="badge bg-success rounded-pill px-2">
                                        {{ number_format($item->total_poin) }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'completed' => 'success',
                                            'pending' => 'warning',
                                            'cancelled' => 'danger'
                                        ];
                                        $statusIcons = [
                                            'completed' => 'check-circle',
                                            'pending' => 'clock',
                                            'cancelled' => 'times-circle'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $statusColors[$item->status] ?? 'secondary' }}">
                                        <i class="fas fa-{{ $statusIcons[$item->status] ?? 'circle' }} me-1"></i>
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.transaksi.show', $item->id) }}" 
                                       class="btn btn-sm btn-outline-netra" 
                                       title="Detail" data-bs-toggle="tooltip">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                                    <p class="text-muted mb-0">Tidak ada data transaksi sampah pada periode ini</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        @if(($sampahTransaksi ?? collect())->count() > 0)
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="4" class="text-end fw-bold">Total:</td>
                                <td class="text-end fw-bold">{{ number_format($sampahSummary['total_berat'] ?? 0, 2) }} kg</td>
                                <td class="text-end fw-bold">{{ number_format($sampahSummary['total_poin'] ?? 0) }}</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
                
                @if(($sampahTransaksi ?? collect())->count() > 0)
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-0">
                                Menampilkan {{ ($sampahTransaksi ?? collect())->count() }} dari {{ $sampahSummary['total_transaksi'] ?? 0 }} transaksi sampah
                            </p>
                        </div>
                        <div>
                            <small class="text-muted">
                                Periode: 
                                @if(request('start_date') && request('end_date'))
                                    {{ \Carbon\Carbon::parse(request('start_date'))->format('d/m/Y') }} 
                                    - {{ \Carbon\Carbon::parse(request('end_date'))->format('d/m/Y') }}
                                @else
                                    Semua Data
                                @endif
                            </small>
                        </div>
                    </div>
                    
                    <!-- Pagination Custom untuk Sampah -->
                    @php
                        $currentTab = 'sampah';
                        $currentPaginator = $sampahTransaksi ?? null;
                        
                        // PERBAIKAN: Hapus default date dari parameter
                        $paginationParams = [
                            'tab' => $currentTab,
                            'start_date' => request('start_date'), // Tidak ada default
                            'end_date' => request('end_date'),     // Tidak ada default
                            'status' => request('status'),
                            'petugas_id' => request('petugas_id'),
                        ];
                        
                        // Remove null/empty values
                        $paginationParams = array_filter($paginationParams, function($value) {
                            return $value !== null && $value !== '';
                        });
                        
                        // Build query string without page parameter
                        $queryString = http_build_query($paginationParams);
                    @endphp

                    
                    @if($currentPaginator && $currentPaginator->hasPages())
                    <div class="d-flex justify-content-center mt-3">
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center mb-0">
                                {{-- Previous Page Link --}}
                                @if($currentPaginator->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link">
                                            <i class="fas fa-chevron-left"></i>
                                        </span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $currentPaginator->previousPageUrl() . ($queryString ? '&' . $queryString : '') }}" aria-label="Previous">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                    </li>
                                @endif

                                {{-- Pagination Elements --}}
                                @php
                                    $current = $currentPaginator->currentPage();
                                    $last = $currentPaginator->lastPage();
                                    $start = max(1, $current - 2);
                                    $end = min($last, $current + 2);
                                    
                                    if($end - $start < 4) {
                                        if($start == 1) {
                                            $end = min($last, $start + 4);
                                        } else {
                                            $start = max(1, $end - 4);
                                        }
                                    }
                                @endphp

                                {{-- First Page Link --}}
                                @if($start > 1)
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $currentPaginator->url(1) . ($queryString ? '&' . $queryString : '') }}">1</a>
                                    </li>
                                    @if($start > 2)
                                        <li class="page-item disabled">
                                            <span class="page-link">...</span>
                                        </li>
                                    @endif
                                @endif

                                {{-- Page Number Links --}}
                                @for($i = $start; $i <= $end; $i++)
                                    <li class="page-item {{ ($i == $current) ? 'active' : '' }}">
                                        <a class="page-link" href="{{ $currentPaginator->url($i) . ($queryString ? '&' . $queryString : '') }}">{{ $i }}</a>
                                    </li>
                                @endfor

                                {{-- Last Page Link --}}
                                @if($end < $last)
                                    @if($end < $last - 1)
                                        <li class="page-item disabled">
                                            <span class="page-link">...</span>
                                        </li>
                                    @endif
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $currentPaginator->url($last) . ($queryString ? '&' . $queryString : '') }}">{{ $last }}</a>
                                    </li>
                                @endif

                                {{-- Next Page Link --}}
                                @if($currentPaginator->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $currentPaginator->nextPageUrl() . ($queryString ? '&' . $queryString : '') }}" aria-label="Next">
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link">
                                            <i class="fas fa-chevron-right"></i>
                                        </span>
                                    </li>
                                @endif
                            </ul>
                        </nav>
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>

    <!-- Tab 2: Penukaran Barang -->
    <div class="tab-pane fade {{ $activeTab == 'penukaran' ? 'show active' : '' }}" id="penukaran" role="tabpanel">
        <!-- Filter Card for Penukaran -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-filter me-2"></i> Filter Laporan Penukaran Barang
                </h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.reports.index') }}" class="row g-3">
                    <input type="hidden" name="tab" value="penukaran">
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" name="start_date_penukaran" class="form-control" 
                               value="{{ request('start_date_penukaran') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Selesai</label>
                        <input type="date" name="end_date_penukaran" class="form-control" 
                               value="{{ request('end_date_penukaran') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status Penukaran</label>
                        <select name="status_penukaran" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status_penukaran') == 'pending' ? 'selected' : '' }}>Menunggu Acc</option>
                            <option value="completed" {{ request('status_penukaran') == 'completed' ? 'selected' : '' }}>Disetujui</option>
                            <option value="cancelled" {{ request('status_penukaran') == 'cancelled' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Admin</label>
                        <select name="admin_id" class="form-select">
                            <option value="">Semua Admin</option>
                            @foreach($adminList ?? [] as $admin)
                            <option value="{{ $admin->id }}" {{ request('admin_id') == $admin->id ? 'selected' : '' }}>
                                {{ $admin->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-netra">
                            <i class="fas fa-filter me-2"></i> Filter Data Penukaran
                        </button>
                        <a href="{{ route('admin.reports.index') }}?tab=penukaran" class="btn btn-outline-secondary">
                            <i class="fas fa-redo me-2"></i> Reset Filter
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary Statistics for Penukaran -->
        @if(isset($penukaranSummary))
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-start border-info border-4 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Total Penukaran</h6>
                                <h3 class="mb-0">{{ number_format($penukaranSummary['total_transaksi'] ?? 0) }}</h3>
                                <small class="text-muted">
                                    <i class="fas fa-calendar-alt me-1"></i> 
                                    @if(request('start_date_penukaran') && request('end_date_penukaran'))
                                        {{ \Carbon\Carbon::parse(request('start_date_penukaran'))->format('d/m/Y') }} 
                                        - {{ \Carbon\Carbon::parse(request('end_date_penukaran'))->format('d/m/Y') }}
                                    @else
                                        Semua Data
                                    @endif
                                </small>
                            </div>
                            <div class="bg-info bg-opacity-10 p-3 rounded-circle">
                                <i class="fas fa-exchange-alt fa-2x text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-start border-danger border-4 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Total Poin Ditukar</h6>
                                <h3 class="mb-0">{{ number_format($penukaranSummary['total_poin'] ?? 0) }}</h3>
                                <small class="text-muted">
                                    <i class="fas fa-coins me-1"></i> Poin berkurang
                                </small>
                            </div>
                            <div class="bg-danger bg-opacity-10 p-3 rounded-circle">
                                <i class="fas fa-minus-circle fa-2x text-danger"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-start border-success border-4 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Penukaran Disetujui</h6>
                                <h3 class="mb-0">{{ number_format($penukaranSummary['completed'] ?? 0) }}</h3>
                                <small class="text-muted">
                                    <i class="fas fa-check-circle me-1"></i> Berhasil ditukar
                                </small>
                            </div>
                            <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                                <i class="fas fa-check fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-start border-warning border-4 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Menunggu Acc</h6>
                                <h3 class="mb-0">{{ number_format($penukaranSummary['pending'] ?? 0) }}</h3>
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i> Menunggu persetujuan
                                </small>
                            </div>
                            <div class="bg-warning bg-opacity-10 p-3 rounded-circle">
                                <i class="fas fa-hourglass-half fa-2x text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transactions Table for Penukaran -->
        <div class="card">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-exchange-alt me-2"></i> Daftar Penukaran Barang
                </h5>
                <div>
                    <button type="button" class="btn btn-sm btn-netra" data-bs-toggle="modal" data-bs-target="#exportPenukaranModal">
                        <i class="fas fa-download me-1"></i> Export Penukaran Barang
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 5%;">ID</th>
                                <th style="width: 15%;">Tanggal</th>
                                <th style="width: 25%;">Warga</th>
                                <th style="width: 20%;">Admin</th>
                                <th style="width: 20%;">Barang</th>
                                <th style="width: 10%;" class="text-end">Poin</th>
                                <th style="width: 10%;">Status</th>
                                <th style="width: 5%;" class="text-center">Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($penukaranTransaksi ?? [] as $item)
                            <tr>
                                <td>
                                    <span class="badge bg-secondary">#{{ $item->id }}</span>
                                </td>
                                <td>
                                    <small class="text-muted d-block">{{ $item->created_at->format('d/m/Y') }}</small>
                                    <small class="text-muted">{{ $item->created_at->format('H:i') }}</small>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $item->warga->name ?? 'N/A' }}</div>
                                    @if($item->warga->phone ?? false)
                                    <small class="text-muted">{{ $item->warga->phone }}</small>
                                    @endif
                                </td>
                                <td>{{ $item->admin->name ?? 'Belum diproses' }}</td>
                                <td>
                                    @php
                                        $barangInfo = $item->catatan ?? 'Penukaran Barang';
                                        // Extract barang info dari catatan
                                        if (preg_match('/Penukaran:\s*(.+?)\s*x(\d+)/', $barangInfo, $matches)) {
                                            $barangInfo = trim($matches[1]) . ' x' . $matches[2];
                                        }
                                    @endphp
                                    <small>{{ Str::limit($barangInfo, 30) }}</small>
                                </td>
                                <td class="text-end">
                                    <span class="badge bg-danger rounded-pill px-2">
                                        <i class="fas fa-minus me-1"></i>{{ number_format(abs($item->total_poin)) }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'pending' => 'warning',
                                            'completed' => 'success',
                                            'cancelled' => 'danger'
                                        ];
                                        $statusLabels = [
                                            'pending' => 'Menunggu Acc',
                                            'completed' => 'Disetujui',
                                            'cancelled' => 'Ditolak'
                                        ];
                                        $statusIcons = [
                                            'pending' => 'clock',
                                            'completed' => 'check-circle',
                                            'cancelled' => 'times-circle'
                                        ];
                                    @endphp
                                    @if(isset($item->status_penukaran))
                                    <span class="badge bg-{{ $statusColors[$item->status_penukaran] ?? 'secondary' }}">
                                        <i class="fas fa-{{ $statusIcons[$item->status_penukaran] ?? 'circle' }} me-1"></i>
                                        {{ $statusLabels[$item->status_penukaran] ?? 'N/A' }}
                                    </span>
                                    @else
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-circle me-1"></i>
                                        N/A
                                    </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.transaksi.show', $item->id) }}" 
                                       class="btn btn-sm btn-outline-netra" 
                                       title="Detail" data-bs-toggle="tooltip">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                                    <p class="text-muted mb-0">Tidak ada data penukaran barang pada periode ini</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        @if(($penukaranTransaksi ?? collect())->count() > 0)
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="5" class="text-end fw-bold">Total Poin Ditukar:</td>
                                <td class="text-end fw-bold">{{ number_format($penukaranSummary['total_poin'] ?? 0) }}</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
                
                @if(($penukaranTransaksi ?? collect())->count() > 0)
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-0">
                                Menampilkan {{ ($penukaranTransaksi ?? collect())->count() }} dari {{ $penukaranSummary['total_transaksi'] ?? 0 }} penukaran barang
                            </p>
                        </div>
                        <div>
                            <small class="text-muted">
                                Periode: 
                                @if(request('start_date_penukaran') && request('end_date_penukaran'))
                                    {{ \Carbon\Carbon::parse(request('start_date_penukaran'))->format('d/m/Y') }} 
                                    - {{ \Carbon\Carbon::parse(request('end_date_penukaran'))->format('d/m/Y') }}
                                @else
                                    Semua Data
                                @endif
                            </small>
                        </div>
                    </div>
                    
                    <!-- Pagination Custom untuk Penukaran -->
                    @php
                    $currentTab = 'penukaran';
                    $currentPaginator = $penukaranTransaksi ?? null;
                    
                    // PERBAIKAN: Hapus default date dari parameter
                    $paginationParams = [
                        'tab' => $currentTab,
                        'start_date_penukaran' => request('start_date_penukaran'), // Tidak ada default
                        'end_date_penukaran' => request('end_date_penukaran'),     // Tidak ada default
                        'status_penukaran' => request('status_penukaran'),
                        'admin_id' => request('admin_id'),
                    ];
                    
                    // Remove null/empty values
                    $paginationParams = array_filter($paginationParams, function($value) {
                        return $value !== null && $value !== '';
                    });
                    
                    // Build query string without page parameter
                    $queryString = http_build_query($paginationParams);
                @endphp
                    
                    @if($currentPaginator && $currentPaginator->hasPages())
                    <div class="d-flex justify-content-center mt-3">
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center mb-0">
                                {{-- Previous Page Link --}}
                                @if($currentPaginator->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link">
                                            <i class="fas fa-chevron-left"></i>
                                        </span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $currentPaginator->previousPageUrl() . ($queryString ? '&' . $queryString : '') }}" aria-label="Previous">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                    </li>
                                @endif

                                {{-- Pagination Elements --}}
                                @php
                                    $current = $currentPaginator->currentPage();
                                    $last = $currentPaginator->lastPage();
                                    $start = max(1, $current - 2);
                                    $end = min($last, $current + 2);
                                    
                                    if($end - $start < 4) {
                                        if($start == 1) {
                                            $end = min($last, $start + 4);
                                        } else {
                                            $start = max(1, $end - 4);
                                        }
                                    }
                                @endphp

                                {{-- First Page Link --}}
                                @if($start > 1)
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $currentPaginator->url(1) . ($queryString ? '&' . $queryString : '') }}">1</a>
                                    </li>
                                    @if($start > 2)
                                        <li class="page-item disabled">
                                            <span class="page-link">...</span>
                                        </li>
                                    @endif
                                @endif

                                {{-- Page Number Links --}}
                                @for($i = $start; $i <= $end; $i++)
                                    <li class="page-item {{ ($i == $current) ? 'active' : '' }}">
                                        <a class="page-link" href="{{ $currentPaginator->url($i) . ($queryString ? '&' . $queryString : '') }}">{{ $i }}</a>
                                    </li>
                                @endfor

                                {{-- Last Page Link --}}
                                @if($end < $last)
                                    @if($end < $last - 1)
                                        <li class="page-item disabled">
                                            <span class="page-link">...</span>
                                        </li>
                                    @endif
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $currentPaginator->url($last) . ($queryString ? '&' . $queryString : '') }}">{{ $last }}</a>
                                    </li>
                                @endif

                                {{-- Next Page Link --}}
                                @if($currentPaginator->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $currentPaginator->nextPageUrl() . ($queryString ? '&' . $queryString : '') }}" aria-label="Next">
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link">
                                            <i class="fas fa-chevron-right"></i>
                                        </span>
                                    </li>
                                @endif
                            </ul>
                        </nav>
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Export Modal for Sampah -->
<div class="modal fade" id="exportSampahModal" tabindex="-1" aria-labelledby="exportSampahModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportSampahModalLabel">
                    <i class="fas fa-download me-2"></i> Export Transaksi Sampah
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="exportSampahForm" action="{{ route('admin.reports.export') }}" method="POST" target="_blank">
                    @csrf
                    <input type="hidden" name="report" value="dashboard">
                    <input type="hidden" name="type_report" value="sampah">
                    <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                    <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                    <input type="hidden" name="status" value="{{ request('status') }}">
                    <input type="hidden" name="petugas_id" value="{{ request('petugas_id') }}">
                    
                    <div class="mb-3">
                        <label class="form-label">Format Export</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="type" id="excelSampah" value="excel" checked>
                                <label class="form-check-label d-flex align-items-center" for="excelSampah">
                                    <i class="fas fa-file-excel text-success me-2 fs-5"></i>
                                    <div>
                                        <div class="fw-bold">Excel</div>
                                        <small class="text-muted">.xlsx</small>
                                    </div>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="type" id="pdfSampah" value="pdf">
                                <label class="form-check-label d-flex align-items-center" for="pdfSampah">
                                    <i class="fas fa-file-pdf text-danger me-2 fs-5"></i>
                                    <div>
                                        <div class="fw-bold">PDF</div>
                                        <small class="text-muted">.pdf</small>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="fileNameSampah" class="form-label">Nama File</label>
                        <input type="text" class="form-control" id="fileNameSampah" 
                               name="file_name" 
                               value="Transaksi_Sampah_{{ date('Y-m-d') }}" 
                               placeholder="Masukkan nama file"
                               required>
                        <small class="text-muted">File akan didownload dengan ekstensi sesuai format</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" form="exportSampahForm" class="btn btn-netra">
                    <i class="fas fa-download me-2"></i> Download
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Export Modal for Penukaran -->
<div class="modal fade" id="exportPenukaranModal" tabindex="-1" aria-labelledby="exportPenukaranModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportPenukaranModalLabel">
                    <i class="fas fa-download me-2"></i> Export Penukaran Barang
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="exportPenukaranForm" action="{{ route('admin.reports.export') }}" method="POST" target="_blank">
                    @csrf
                    <input type="hidden" name="report" value="dashboard">
                    <input type="hidden" name="type_report" value="penukaran">
                    <input type="hidden" name="start_date" value="{{ request('start_date_penukaran') }}">
                    <input type="hidden" name="end_date" value="{{ request('end_date_penukaran') }}">
                    <input type="hidden" name="status_penukaran" value="{{ request('status_penukaran') }}">
                    <input type="hidden" name="admin_id" value="{{ request('admin_id') }}">
                    
                    <div class="mb-3">
                        <label class="form-label">Format Export</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="type" id="excelPenukaran" value="excel" checked>
                                <label class="form-check-label d-flex align-items-center" for="excelPenukaran">
                                    <i class="fas fa-file-excel text-success me-2 fs-5"></i>
                                    <div>
                                        <div class="fw-bold">Excel</div>
                                        <small class="text-muted">.xlsx</small>
                                    </div>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="type" id="pdfPenukaran" value="pdf">
                                <label class="form-check-label d-flex align-items-center" for="pdfPenukaran">
                                    <i class="fas fa-file-pdf text-danger me-2 fs-5"></i>
                                    <div>
                                        <div class="fw-bold">PDF</div>
                                        <small class="text-muted">.pdf</small>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="fileNamePenukaran" class="form-label">Nama File</label>
                        <input type="text" class="form-control" id="fileNamePenukaran" 
                               name="file_name" 
                               value="Penukaran_Barang_{{ date('Y-m-d') }}" 
                               placeholder="Masukkan nama file"
                               required>
                        <small class="text-muted">File akan didownload dengan ekstensi sesuai format</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" form="exportPenukaranForm" class="btn btn-netra">
                    <i class="fas fa-download me-2"></i> Download
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .nav-tabs .nav-link {
        border-bottom: 3px solid transparent;
        transition: all 0.3s;
        cursor: pointer;
    }
    
    .nav-tabs .nav-link.active {
        border-bottom-color: var(--bs-primary);
        color: var(--bs-primary);
        font-weight: 600;
        background-color: rgba(var(--bs-primary-rgb), 0.05);
    }
    
    .nav-tabs .nav-link:hover:not(.active) {
        background-color: rgba(0, 0, 0, 0.03);
    }
    
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
    
    .bg-opacity-10 {
        background-color: rgba(var(--bs-primary-rgb), 0.1);
    }
    
    .badge {
        font-size: 0.8em;
        font-weight: 500;
    }
    
    .table > :not(caption) > * > * {
        padding: 0.75rem 0.5rem;
    }
    
    /* Pagination Styles */
    .page-link {
        color: #2E8B57;
        border: 1px solid #dee2e6;
        margin: 0 2px;
        border-radius: 4px;
    }
    
    .page-item.active .page-link {
        background-color: #2E8B57;
        border-color: #2E8B57;
        color: white;
    }
    
    .page-link:hover {
        background-color: #f8f9fa;
        border-color: #2E8B57;
        color: #2E8B57;
    }
    
    @media print {
        .no-print {
            display: none !important;
        }
        
        .card-header, .card-footer, .modal, .btn, .nav-tabs {
            display: none !important;
        }
        
        .card {
            border: 1px solid #dee2e6 !important;
            box-shadow: none !important;
        }
        
        h2 {
            color: #000 !important;
        }
        
        .tab-pane {
            display: block !important;
            opacity: 1 !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // File name formatting
    const fileNameInputs = ['fileNameSampah', 'fileNamePenukaran'];
    fileNameInputs.forEach(id => {
        const input = document.getElementById(id);
        if (input) {
            input.addEventListener('input', function() {
                this.value = this.value.replace(/[^a-zA-Z0-9-_ ]/g, '').replace(/\s+/g, '_');
            });
        }
    });
    
    // Tab handling
    const urlParams = new URLSearchParams(window.location.search);
    const activeTab = urlParams.get('tab') || 'sampah';
    
    // Set active tab based on URL parameter
    if (activeTab === 'penukaran') {
        const penukaranTab = document.getElementById('penukaran-tab');
        const penukaranPane = document.getElementById('penukaran');
        const sampahTab = document.getElementById('sampah-tab');
        const sampahPane = document.getElementById('sampah');
        
        if (penukaranTab && penukaranPane) {
            penukaranTab.classList.add('active');
            penukaranPane.classList.add('show', 'active');
            sampahTab.classList.remove('active');
            sampahPane.classList.remove('show', 'active');
        }
    }
    
    // Handle tab click events
    const triggerTabList = document.querySelectorAll('#reportTabs button[data-bs-toggle="tab"]');
    triggerTabList.forEach(triggerEl => {
        triggerEl.addEventListener('click', function(event) {
            event.preventDefault();
            const target = this.getAttribute('data-bs-target');
            const tabId = target.replace('#', '');
            
            // Update URL dengan parameter tab
            const newUrl = new URL(window.location.href);
            newUrl.searchParams.set('tab', tabId);
            
            // Redirect ke URL yang sama dengan parameter tab
            // Ini akan memastikan controller mendapatkan parameter yang benar
            window.location.href = newUrl.toString();
        });
    });
    
    // Handle form submit untuk menjaga tab state
    document.querySelectorAll('form').forEach(form => {
        const tabInput = form.querySelector('input[name="tab"]');
        if (!tabInput) {
            // Add hidden input untuk tab jika belum ada
            const activeTabEl = document.querySelector('#reportTabs .nav-link.active');
            if (activeTabEl) {
                const tabId = activeTabEl.getAttribute('data-bs-target').replace('#', '');
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'tab';
                hiddenInput.value = tabId;
                form.appendChild(hiddenInput);
            }
        }
    });
});
</script>
@endpush