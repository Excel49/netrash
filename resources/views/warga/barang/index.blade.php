@extends('layouts.app')

@section('title', 'Katalog Barang')

@section('content')
<div class="container-fluid py-4">
    <!-- Header dengan Tab -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="d-flex align-items-center">
                <div class="rounded-circle bg-netra p-3 me-3">
                    <i class="fas fa-store fa-lg text-white"></i>
                </div>
                <div>
                    <h1 class="h3 mb-1">Katalog Barang</h1>
                    <p class="text-muted mb-0">Tukar poin dengan barang-barang berkualitas</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-success bg-success-subtle">
                <div class="card-body py-2">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-success p-2 me-3">
                            <i class="fas fa-coins text-white"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">{{ number_format(auth()->user()->total_points, 0, ',', '.') }} Poin</h5>
                            <small class="text-success">
                                <i class="fas fa-wallet me-1"></i> Saldo tersedia
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-transparent border-bottom-0 p-0">
                    <ul class="nav nav-tabs nav-tabs-line" id="barangTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="katalog-tab" data-bs-toggle="tab" data-bs-target="#katalog" type="button" role="tab">
                                <i class="fas fa-store me-2"></i>Katalog Barang
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="riwayat-tab" data-bs-toggle="tab" data-bs-target="#riwayat" type="button" role="tab">
                                <i class="fas fa-history me-2"></i>Riwayat Penukaran
                                @if($totalPenukaran > 0)
                                <span class="badge bg-danger ms-1">{{ $totalPenukaran }}</span>
                                @endif
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <!-- Tab Content -->
                    <div class="tab-content" id="barangTabsContent">
                        
                        <!-- Tab 1: Katalog Barang -->
                        <div class="tab-pane fade show active" id="katalog" role="tabpanel">
                            <!-- Stats -->
                            <div class="alert alert-info mb-4">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>{{ $barang->total() }}</strong> barang tersedia untuk ditukar
                                    </div>
                                    <div class="text-muted small">
                                        Halaman {{ $barang->currentPage() }} dari {{ $barang->lastPage() }}
                                    </div>
                                </div>
                            </div>

                            <!-- Grid Barang -->
                            @if($barang->isEmpty())
                                <div class="text-center py-5">
                                    <div class="mb-4">
                                        <i class="fas fa-box-open fa-4x text-muted"></i>
                                    </div>
                                    <h4 class="text-muted mb-3">Belum ada barang tersedia</h4>
                                    <p class="text-muted mb-4">Admin belum menambahkan barang untuk ditukar</p>
                                    <a href="{{ route('warga.dashboard') }}" class="btn btn-netra">
                                        <i class="fas fa-arrow-left me-2"></i> Kembali ke Dashboard
                                    </a>
                                </div>
                            @else
                                <div class="row g-4">
                                    @foreach($barang as $item)
                                        <div class="col-xl-3 col-lg-4 col-md-6">
                                            <div class="card h-100 border-0 shadow-sm hover-shadow">
                                                <div class="position-relative overflow-hidden">
                                                    <img src="{{ asset('storage/barang/' . $item->gambar) }}" 
                                                        class="card-img-top" 
                                                        alt="{{ $item->nama_barang }}"
                                                        style="height: 200px; object-fit: cover;"
                                                        onerror="this.onerror=null; this.src='{{ asset('img/default-product.png') }}'">
                                                    
                                                    <div class="position-absolute top-0 end-0 p-2">
                                                        <span class="badge {{ $item->stok > 0 ? 'bg-success' : 'bg-danger' }} rounded-pill">
                                                            {{ $item->stok > 0 ? 'Tersedia' : 'Habis' }}
                                                        </span>
                                                    </div>
                                                    @if(!$item->status)
                                                    <div class="position-absolute top-0 start-0 w-100 bg-warning text-dark text-center py-1">
                                                        <small><i class="fas fa-exclamation-triangle me-1"></i> Nonaktif</small>
                                                    </div>
                                                    @endif
                                                </div>
                                                
                                                <div class="card-body d-flex flex-column p-3">
                                                    <h5 class="card-title mb-2">{{ $item->nama_barang }}</h5>
                                                    <p class="card-text text-muted small mb-3 flex-grow-1">
                                                        {{ Str::limit($item->deskripsi, 100) }}
                                                    </p>
                                                    
                                                    <div class="mb-3">
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            @if($item->kategori)
                                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle">
                                                                {{ $item->kategori }}
                                                            </span>
                                                            @endif
                                                            <span class="badge bg-netra bg-opacity-10 text-netra border border-netra">
                                                                <i class="fas fa-coins me-1"></i> {{ number_format($item->harga_poin) }}
                                                            </span>
                                                        </div>
                                                        
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <small class="text-muted">
                                                                <i class="fas fa-box me-1"></i> Stok: {{ $item->stok }}
                                                            </small>
                                                            <small class="text-muted">
                                                                <i class="fas fa-check-circle me-1 text-success"></i> {{ $item->status ? 'Aktif' : 'Nonaktif' }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="d-grid gap-2 mt-auto">
                                                        <a href="{{ route('warga.barang.show', $item->id) }}" 
                                                           class="btn btn-outline-netra btn-sm">
                                                            <i class="fas fa-eye me-1"></i> Detail Barang
                                                        </a>
                                                        
                                                        @if($item->stok > 0 && $item->status)
                                                            @php
                                                                $userPoin = auth()->user()->total_points;
                                                                $cukupPoin = $userPoin >= $item->harga_poin;
                                                            @endphp
                                                            
                                                            @if($cukupPoin)
                                                                <a href="{{ route('warga.penukaran.create', ['barang_id' => $item->id]) }}" 
                                                                   class="btn btn-netra btn-sm">
                                                                    <i class="fas fa-shopping-cart me-1"></i> 
                                                                    Tukar {{ $item->harga_poin }} Poin
                                                                </a>
                                                            @else
                                                                <button class="btn btn-outline-secondary btn-sm" disabled 
                                                                        data-bs-toggle="tooltip" 
                                                                        data-bs-placement="top"
                                                                        title="Poin Anda kurang {{ $item->harga_poin - $userPoin }} poin">
                                                                    <i class="fas fa-times me-1"></i> 
                                                                    Kurang {{ $item->harga_poin - $userPoin }} Poin
                                                                </button>
                                                            @endif
                                                        @else
                                                            <button class="btn btn-outline-secondary btn-sm" disabled>
                                                                <i class="fas fa-times me-1"></i> Tidak Tersedia
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Pagination -->
                                @if($barang->hasPages())
                                <div class="row mt-5">
                                    <div class="col-12">
                                        <nav aria-label="Page navigation" class="d-flex justify-content-center">
                                            <ul class="pagination pagination-sm mb-0">
                                                {{-- Previous Page Link --}}
                                                <li class="page-item {{ $barang->onFirstPage() ? 'disabled' : '' }}">
                                                    <a class="page-link" 
                                                       href="{{ $barang->previousPageUrl() }}" 
                                                       aria-label="Previous"
                                                       {{ $barang->onFirstPage() ? 'tabindex="-1" aria-disabled="true"' : '' }}>
                                                        <i class="fas fa-chevron-left"></i>
                                                    </a>
                                                </li>

                                                {{-- Pagination Elements --}}
                                                @php
                                                    $current = $barang->currentPage();
                                                    $last = $barang->lastPage();
                                                    $start = max(1, $current - 2);
                                                    $end = min($last, $current + 2);
                                                    
                                                    if ($start > 1) {
                                                        echo '<li class="page-item"><a class="page-link" href="' . $barang->url(1) . '">1</a></li>';
                                                        if ($start > 2) {
                                                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                                        }
                                                    }
                                                    
                                                    for ($page = $start; $page <= $end; $page++) {
                                                        $active = $page == $current ? 'active' : '';
                                                        echo '<li class="page-item ' . $active . '">';
                                                        echo '<a class="page-link" href="' . $barang->url($page) . '">' . $page . '</a>';
                                                        echo '</li>';
                                                    }
                                                    
                                                    if ($end < $last) {
                                                        if ($end < $last - 1) {
                                                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                                        }
                                                        echo '<li class="page-item"><a class="page-link" href="' . $barang->url($last) . '">' . $last . '</a></li>';
                                                    }
                                                @endphp

                                                {{-- Next Page Link --}}
                                                <li class="page-item {{ !$barang->hasMorePages() ? 'disabled' : '' }}">
                                                    <a class="page-link" 
                                                       href="{{ $barang->nextPageUrl() }}" 
                                                       aria-label="Next"
                                                       {{ !$barang->hasMorePages() ? 'tabindex="-1" aria-disabled="true"' : '' }}>
                                                        <i class="fas fa-chevron-right"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </nav>
                                        
                                        {{-- Page Info --}}
                                        <div class="text-center mt-2">
                                            <small class="text-muted">
                                                Menampilkan {{ $barang->firstItem() ?? 0 }} - {{ $barang->lastItem() ?? 0 }} 
                                                dari {{ $barang->total() }} barang
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            @endif
                        </div>
                        
                        <!-- Tab 2: Riwayat Penukaran -->
                        <div class="tab-pane fade" id="riwayat" role="tabpanel">
                            <!-- Header Info -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <div>
                                            <h4 class="mb-0 text-dark">
                                                <i class="fas fa-history text-primary me-2"></i>Riwayat Penukaran Barang
                                            </h4>
                                            <p class="text-muted mb-0">Daftar semua barang yang telah Anda tukar dengan poin</p>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-netra fs-6 px-3 py-2">
                                                <i class="fas fa-exchange-alt me-2"></i>{{ $totalPenukaran }} Penukaran
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Stats Cards -->
                            <div class="row mb-4">
                                <div class="col-md-4 mb-3">
                                    <div class="card border-start border-danger border-4">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-danger bg-opacity-10 rounded p-3 me-3">
                                                    <i class="fas fa-exchange-alt fa-2x text-danger"></i>
                                                </div>
                                                <div>
                                                    <h6 class="text-uppercase text-muted mb-1">Total Penukaran</h6>
                                                    <h2 class="mb-0">{{ $totalPenukaran }}</h2>
                                                    <small class="text-muted">kali menukar barang</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <div class="card border-start border-warning border-4">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-warning bg-opacity-10 rounded p-3 me-3">
                                                    <i class="fas fa-coins fa-2x text-warning"></i>
                                                </div>
                                                <div>
                                                    <h6 class="text-uppercase text-muted mb-1">Total Poin Dikeluarkan</h6>
                                                    <h2 class="mb-0 text-warning">-{{ number_format($totalPoinKeluar) }}</h2>
                                                    <small class="text-muted">poin untuk penukaran</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <div class="card border-start border-success border-4">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-success bg-opacity-10 rounded p-3 me-3">
                                                    <i class="fas fa-wallet fa-2x text-success"></i>
                                                </div>
                                                <div>
                                                    <h6 class="text-uppercase text-muted mb-1">Poin Saat Ini</h6>
                                                    <h2 class="mb-0 text-success">{{ number_format(auth()->user()->total_points) }}</h2>
                                                    <small class="text-muted">poin tersisa</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Filter Options -->
                            <div class="card mb-4">
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label">Urutkan</label>
                                            <select class="form-select" id="sortRiwayat">
                                                <option value="newest">Terbaru</option>
                                                <option value="oldest">Terlama</option>
                                                <option value="highest">Poin Tertinggi</option>
                                                <option value="lowest">Poin Terendah</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Status</label>
                                            <select class="form-select" id="filterStatus">
                                                <option value="">Semua Status</option>
                                                <option value="completed">Selesai</option>
                                                <option value="pending">Pending</option>
                                                <option value="cancelled">Dibatalkan</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 d-flex align-items-end">
                                            <button class="btn btn-netra w-100" onclick="applyRiwayatFilters()">
                                                <i class="fas fa-filter me-2"></i>Terapkan Filter
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tabel Riwayat Penukaran -->
                            @if($penukaran->isEmpty())
                                <div class="card border-dashed">
                                    <div class="card-body text-center py-5">
                                        <div class="mb-4">
                                            <i class="fas fa-basket-shopping fa-4x text-muted opacity-25"></i>
                                        </div>
                                        <h4 class="text-muted mb-3">Belum ada riwayat penukaran</h4>
                                        <p class="text-muted mb-4">Anda belum menukar poin dengan barang apapun</p>
                                        <button class="btn btn-netra" onclick="switchToKatalog()">
                                            <i class="fas fa-store me-2"></i>Mulai Tukar Barang
                                        </button>
                                    </div>
                                </div>
                            @else
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-0">
                                                    <i class="fas fa-list me-2"></i>Daftar Penukaran
                                                </h6>
                                                <small class="text-muted">
                                                    Menampilkan {{ $penukaran->firstItem() ?? 0 }}-{{ $penukaran->lastItem() ?? 0 }} dari {{ $penukaran->total() }} penukaran
                                                </small>
                                            </div>
                                            <div>
                                                <button class="btn btn-sm btn-outline-secondary me-2" onclick="exportRiwayat()">
                                                    <i class="fas fa-download me-1"></i>Export
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th class="ps-4" style="width: 5%">#</th>
                                                        <th style="width: 15%">Tanggal</th>
                                                        <th style="width: 15%">Transaksi</th>
                                                        <th style="width: 30%">Barang</th>
                                                        <th style="width: 15%" class="text-center">Poin</th>
                                                        <th style="width: 10%" class="text-center">Status</th>
                                                        <th style="width: 10%" class="text-center">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($penukaran as $index => $trx)
                                                    <tr class="{{ $trx->status == 'completed' ? 'table-success-subtle' : ($trx->status == 'pending' ? 'table-warning-subtle' : 'table-danger-subtle') }}">
                                                        <td class="ps-4">
                                                            <div class="text-muted small">{{ $loop->iteration + ($penukaran->currentPage() - 1) * $penukaran->perPage() }}</div>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex flex-column">
                                                                <span class="fw-medium">{{ $trx->created_at->format('d/m/Y') }}</span>
                                                                <small class="text-muted">{{ $trx->created_at->format('H:i') }}</small>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle">
                                                                {{ $trx->kode_transaksi }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="flex-shrink-0 me-3">
                                                                    <div class="bg-primary bg-opacity-10 rounded-circle p-2">
                                                                        <i class="fas fa-box text-primary"></i>
                                                                    </div>
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <div class="fw-medium text-truncate" style="max-width: 250px;">
                                                                        {{ Str::limit(str_replace('Penukaran: ', '', $trx->catatan), 40) }}
                                                                    </div>
                                                                    <small class="text-muted">Penukaran barang</small>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="text-center">
                                                            <div class="d-flex align-items-center justify-content-center">
                                                                <i class="fas fa-arrow-down text-danger me-2"></i>
                                                                <div>
                                                                    <span class="fw-bold text-danger">
                                                                        {{ number_format(abs($trx->total_poin)) }}
                                                                    </span>
                                                                    <div>
                                                                        <small class="text-muted">poin</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="text-center">
                                                            @if($trx->status == 'completed')
                                                            <span class="badge bg-success rounded-pill px-3 py-2">
                                                                <i class="fas fa-check me-1"></i>Selesai
                                                            </span>
                                                            @elseif($trx->status == 'pending')
                                                            <span class="badge bg-warning rounded-pill px-3 py-2">
                                                                <i class="fas fa-clock me-1"></i>Pending
                                                            </span>
                                                            @else
                                                            <span class="badge bg-danger rounded-pill px-3 py-2">
                                                                <i class="fas fa-times me-1"></i>Dibatalkan
                                                            </span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            <div class="btn-group btn-group-sm">
                                                                <a href="{{ route('warga.transaksi.show', $trx->id) }}" 
                                                                class="btn btn-outline-primary" 
                                                                data-bs-toggle="tooltip" 
                                                                title="Detail Penukaran">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                                <button class="btn btn-outline-info" 
                                                                        data-bs-toggle="tooltip" 
                                                                        title="Cetak Bukti"
                                                                        onclick="printReceipt('{{ $trx->id }}')">
                                                                    <i class="fas fa-print"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    
                                    @if($penukaran->hasPages())
                                    <div class="card-footer bg-white">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <small class="text-muted">
                                                    Menampilkan {{ $penukaran->firstItem() }} - {{ $penukaran->lastItem() }} dari {{ $penukaran->total() }} entri
                                                </small>
                                            </div>
                                            <div>
                                                {{ $penukaran->links('vendor.pagination.bootstrap-5') }}
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                
                                <!-- Summary -->
                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <div class="card border-dashed">
                                            <div class="card-body">
                                                <h6 class="mb-3">
                                                    <i class="fas fa-chart-bar me-2 text-primary"></i>Ringkasan Penukaran
                                                </h6>
                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="mb-3">
                                                            <small class="text-muted d-block">Rata-rata Poin per Penukaran</small>
                                                            <h4 class="text-netra">{{ $totalPenukaran > 0 ? number_format($totalPoinKeluar / $totalPenukaran) : 0 }}</h4>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="mb-3">
                                                            <small class="text-muted d-block">Poin Terbesar</small>
                                                            <h4 class="text-danger">-{{ $penukaran->max('total_poin') ? number_format(abs($penukaran->max('total_poin'))) : 0 }}</h4>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card border-dashed">
                                            <div class="card-body">
                                                <h6 class="mb-3">
                                                    <i class="fas fa-lightbulb me-2 text-warning"></i>Saran
                                                </h6>
                                                <div class="alert alert-warning mb-0">
                                                    <div class="d-flex">
                                                        <div class="flex-shrink-0">
                                                            <i class="fas fa-info-circle"></i>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <p class="mb-1">Anda memiliki <strong>{{ number_format(auth()->user()->total_points) }} poin</strong> tersisa.</p>
                                                            <p class="mb-0">Tukarkan poin Anda sebelum kadaluarsa untuk mendapatkan barang menarik!</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.table-success-subtle {
    background-color: rgba(25, 135, 84, 0.05);
}

.table-warning-subtle {
    background-color: rgba(255, 193, 7, 0.05);
}

.table-danger-subtle {
    background-color: rgba(220, 53, 69, 0.05);
}

.card.border-dashed {
    border: 2px dashed #dee2e6;
    background-color: #f8f9fa;
}

.bg-opacity-10 {
    background-color: rgba(var(--bs-primary-rgb), 0.1) !important;
}

.bg-primary.bg-opacity-10 {
    background-color: rgba(13, 110, 253, 0.1) !important;
}

.border-start.border-4 {
    border-left-width: 4px !important;
}

.hover-row:hover {
    background-color: #f8f9fa;
    cursor: pointer;
}

.badge.rounded-pill {
    border-radius: 50rem !important;
}

.text-truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.nav-tabs-line {
    border-bottom: 2px solid #dee2e6;
}

.nav-tabs-line .nav-link {
    border: none;
    color: #6c757d;
    padding: 0.75rem 1.5rem;
    margin-bottom: -2px;
    border-bottom: 2px solid transparent;
}

.nav-tabs-line .nav-link:hover {
    color: var(--netra-primary);
    border-bottom-color: var(--netra-primary-light);
}

.nav-tabs-line .nav-link.active {
    color: var(--netra-primary);
    background-color: transparent;
    border-bottom: 2px solid var(--netra-primary);
    font-weight: 600;
}

.hover-shadow {
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
}

.hover-shadow:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    border-color: var(--netra-primary);
}

.bg-success-subtle {
    background-color: rgba(25, 135, 84, 0.1) !important;
}

.bg-opacity-10 {
    background-color: rgba(var(--bs-secondary-rgb), 0.1);
}

.border-netra {
    border-color: var(--netra-primary) !important;
}

.card-img-top {
    border-top-left-radius: 0.375rem;
    border-top-right-radius: 0.375rem;
}

.badge.bg-netra {
    background-color: var(--netra-primary) !important;
}

.bg-netra.bg-opacity-10 {
    background-color: rgba(46, 139, 87, 0.1) !important;
}

.page-item.active .page-link {
    background-color: var(--netra-primary);
    border-color: var(--netra-primary);
}

.page-link {
    color: var(--netra-primary);
}

.page-link:hover {
    color: var(--netra-primary-dark);
}

@media (max-width: 768px) {
    .card-img-top {
        height: 180px !important;
    }
    
    .pagination {
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .nav-tabs-line .nav-link {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
function switchToKatalog() {
    var katalogTab = document.querySelector('#katalog-tab');
    if (katalogTab) {
        var tab = new bootstrap.Tab(katalogTab);
        tab.show();
    }
}

function applyRiwayatFilters() {
    const sortBy = document.getElementById('sortRiwayat').value;
    const status = document.getElementById('filterStatus').value;
    
    // Simpan ke URL
    const url = new URL(window.location);
    url.searchParams.set('tab', 'riwayat');
    url.searchParams.set('sort', sortBy);
    if (status) {
        url.searchParams.set('status', status);
    } else {
        url.searchParams.delete('status');
    }
    
    window.location.href = url.toString();
}

function exportRiwayat() {
    // Implement export function
    alert('Fitur export akan segera tersedia!');
}

function printReceipt(transactionId) {
    window.open(`/warga/transaksi/${transactionId}/print`, '_blank');
}

// Auto apply saved filters
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const savedSort = urlParams.get('sort');
    const savedStatus = urlParams.get('status');
    
    if (savedSort) {
        document.getElementById('sortRiwayat').value = savedSort;
    }
    
    if (savedStatus) {
        document.getElementById('filterStatus').value = savedStatus;
    }
    
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Add loading state to buttons
    document.querySelectorAll('a.btn-netra').forEach(button => {
        button.addEventListener('click', function(e) {
            if (!this.href.includes('penukaran')) return;
            
            const originalText = this.innerHTML;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
            this.classList.add('disabled');
            
            setTimeout(() => {
                this.innerHTML = originalText;
                this.classList.remove('disabled');
            }, 3000);
        });
    });
    
    // Switch tab function
    window.switchToKatalog = function() {
        var katalogTab = document.querySelector('#katalog-tab');
        if (katalogTab) {
            var tab = new bootstrap.Tab(katalogTab);
            tab.show();
        }
    };
    
    // Check URL for tab parameter
    const urlParams = new URLSearchParams(window.location.search);
    const tabParam = urlParams.get('tab');
    
    if (tabParam === 'riwayat') {
        var riwayatTab = document.querySelector('#riwayat-tab');
        if (riwayatTab) {
            var tab = new bootstrap.Tab(riwayatTab);
            tab.show();
        }
    }
    
    // Update URL when tab changes
    document.querySelectorAll('button[data-bs-toggle="tab"]').forEach(tab => {
        tab.addEventListener('shown.bs.tab', function (event) {
            const activeTab = event.target.getAttribute('id');
            const tabName = activeTab.replace('-tab', '');
            
            // Update URL without page reload
            const url = new URL(window.location);
            if (tabName === 'katalog') {
                url.searchParams.delete('tab');
            } else {
                url.searchParams.set('tab', tabName);
            }
            window.history.replaceState({}, '', url);
        });
    });
});
</script>
@endpush