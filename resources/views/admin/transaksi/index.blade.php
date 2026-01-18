@extends('layouts.app')

@section('title', 'Daftar Transaksi Admin')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Daftar Transaksi</h2>
            <div>
                <a href="{{ route('admin.reports.transaksi') }}" class="btn btn-netra-outline me-2">
                    <i class="bi bi-bar-chart me-2"></i>Laporan
                </a>
            </div>
        </div>
        <p class="text-muted">Kelola semua transaksi sampah dan penukaran barang</p>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-uppercase mb-0">Total</h6>
                        <h3 class="mb-0">{{ $transaksi->total() }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-receipt fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-uppercase mb-0">Sampah</h6>
                        <h3 class="mb-0">{{ $transaksi->where('jenis_transaksi', '!=', 'penukaran')->count() }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-trash fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-uppercase mb-0">Penukaran</h6>
                        <h3 class="mb-0">{{ $transaksi->where('jenis_transaksi', 'penukaran')->count() }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-basket fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-uppercase mb-0">Pending</h6>
                        <h3 class="mb-0">{{ $transaksi->where(function($q) {
                            return $q->where('status', 'pending')->orWhere('status_penukaran', 'pending');
                        })->count() }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-clock fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter dan Search -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.transaksi.index') }}" class="row g-3">
            <div class="col-md-3">
                <label for="search" class="form-label">Cari Transaksi</label>
                <input type="text" class="form-control" id="search" name="search" 
                       value="{{ request('search') }}" placeholder="Kode transaksi atau nama warga...">
            </div>
            
            <div class="col-md-2">
                <label for="jenis_transaksi" class="form-label">Jenis Transaksi</label>
                <select class="form-control" id="jenis_transaksi" name="jenis_transaksi">
                    <option value="">Semua Jenis</option>
                    <option value="setoran" {{ request('jenis_transaksi') == 'setoran' ? 'selected' : '' }}>Setoran Sampah</option>
                    <option value="penukaran" {{ request('jenis_transaksi') == 'penukaran' ? 'selected' : '' }}>Penukaran Barang</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <label for="status" class="form-label">Status</label>
                <select class="form-control" id="status" name="status">
                    <option value="">Semua Status</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai/Disetujui</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending/Menunggu</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan/Ditolak</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <label for="date" class="form-label">Tanggal</label>
                <input type="date" class="form-control" id="date" name="date" 
                       value="{{ request('date') }}">
            </div>
            
            <div class="col-md-1 d-flex align-items-end">
                <div class="d-grid w-100">
                    <button type="submit" class="btn btn-netra">
                        <i class="bi bi-search me-2"></i>Filter
                    </button>
                </div>
            </div>
            
            <div class="col-md-1 d-flex align-items-end">
                <div class="d-grid w-100">
                    <a href="{{ route('admin.transaksi.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-clockwise me-2"></i>Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Table Transaksi -->
<div class="card">
    <div class="card-body">
        @if($transaksi->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-receipt text-muted" style="font-size: 4rem;"></i>
                <h4 class="mt-3">Belum ada transaksi</h4>
                <p class="text-muted">Transaksi akan muncul di sini</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Kode Transaksi</th>
                            <th>Jenis</th>
                            <th>Warga</th>
                            <th>Petugas/Admin</th>
                            <th>Berat (kg)</th>
                            <th>Total Poin</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transaksi as $item)
                        <tr class="{{ $item->jenis_transaksi == 'penukaran' ? 'table-info' : '' }}">
                            <td>{{ $loop->iteration + (($transaksi->currentPage() - 1) * $transaksi->perPage()) }}</td>
                            <td>
                                <strong>{{ $item->kode_transaksi }}</strong>
                                @if($item->jenis_transaksi == 'penukaran')
                                <br><small class="text-muted">Penukaran Barang</small>
                                @endif
                            </td>
                            <td>
                                @if($item->jenis_transaksi == 'penukaran')
                                <span class="badge bg-info">Penukaran</span>
                                @else
                                <span class="badge bg-success">Sampah</span>
                                @endif
                            </td>
                            <td>
                                {{ $item->warga->name ?? 'N/A' }}
                                <br><small class="text-muted">{{ $item->warga->email ?? '' }}</small>
                            </td>
                            <td>
                                @if($item->jenis_transaksi == 'penukaran' && $item->admin)
                                    <span class="badge bg-secondary">Admin: {{ $item->admin->name ?? 'N/A' }}</span>
                                @else
                                    {{ $item->petugas->name ?? 'N/A' }}
                                @endif
                            </td>
                            <td>
                                @if($item->jenis_transaksi == 'penukaran')
                                    <span class="badge bg-light text-dark">-</span>
                                @else
                                    <span class="badge bg-info">{{ number_format($item->total_berat, 1) }} kg</span>
                                @endif
                            </td>
                            <td>
                                @if($item->jenis_transaksi == 'penukaran')
                                    <span class="badge bg-danger">
                                        <i class="bi bi-dash"></i>{{ number_format(abs($item->total_poin)) }} poin
                                    </span>
                                @else
                                    <span class="badge bg-success">
                                        <i class="bi bi-plus"></i>{{ number_format($item->total_poin) }} poin
                                    </span>
                                @endif
                            </td>
                            <td>
                                {{ $item->tanggal_transaksi->format('d/m/Y') }}
                                <br><small class="text-muted">{{ $item->tanggal_transaksi->format('H:i') }}</small>
                            </td>
                            <td>
                                @if($item->jenis_transaksi == 'penukaran')
                                    <!-- Hanya tampilkan status penukaran untuk transaksi penukaran -->
                                    @if($item->status_penukaran == 'pending')
                                        <span class="badge bg-warning">Menunggu Acc</span>
                                    @elseif($item->status_penukaran == 'completed')
                                        <span class="badge bg-success">Disetujui</span>
                                    @elseif($item->status_penukaran == 'cancelled')
                                        <span class="badge bg-danger">Ditolak</span>
                                    @else
                                        <span class="badge bg-secondary">N/A</span>
                                    @endif
                                @else
                                    <!-- Untuk transaksi sampah, tampilkan status biasa -->
                                    @if($item->status == 'completed')
                                        <span class="badge bg-success">Selesai</span>
                                    @elseif($item->status == 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @elseif($item->status == 'cancelled')
                                        <span class="badge bg-danger">Dibatalkan</span>
                                    @else
                                        <span class="badge bg-secondary">N/A</span>
                                    @endif
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('admin.transaksi.show', $item->id) }}" 
                                       class="btn btn-outline-primary" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    
                                    <!-- Aksi Khusus Penukaran Pending -->
                                    @if($item->jenis_transaksi == 'penukaran' && $item->status_penukaran == 'pending')
                                        <!-- Modal untuk Approve -->
                                        <button type="button" class="btn btn-outline-success" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#approveModal{{ $item->id }}"
                                                title="Setujui">
                                            <i class="bi bi-check"></i>
                                        </button>
                                        
                                        <!-- Modal untuk Reject -->
                                        <button type="button" class="btn btn-outline-danger" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#rejectModal{{ $item->id }}"
                                                title="Tolak">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    @endif
                                    
                                    <!-- Aksi untuk Transaksi Sampah Pending -->
                                    @if($item->jenis_transaksi != 'penukaran' && $item->status == 'pending')
                                        <form action="{{ route('admin.transaksi.verify', $item->id) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-success" 
                                                    title="Verifikasi"
                                                    onclick="return confirm('Verifikasi transaksi ini?')">
                                                <i class="bi bi-check"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.transaksi.cancel', $item->id) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-danger" 
                                                    title="Batalkan"
                                                    onclick="return confirm('Batalkan transaksi ini?')">
                                                <i class="bi bi-x"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                                
                                <!-- Modals untuk Penukaran -->
                                @if($item->jenis_transaksi == 'penukaran' && $item->status_penukaran == 'pending')
                                    <!-- Approve Modal -->
                                    <div class="modal fade" id="approveModal{{ $item->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('admin.transaksi.approve', $item->id) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header bg-success text-white">
                                                        <h5 class="modal-title">Setujui Penukaran</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Setujui penukaran barang oleh <strong>{{ $item->warga->name ?? 'Warga' }}</strong>?</p>
                                                        <p><strong>Barang:</strong> {{ $item->catatan }}</p>
                                                        <p><strong>Poin:</strong> {{ number_format(abs($item->total_poin)) }} poin akan dikurangi</p>
                                                        
                                                        <div class="mb-3">
                                                            <label for="catatan_admin{{ $item->id }}" class="form-label">Catatan (Opsional)</label>
                                                            <textarea class="form-control" id="catatan_admin{{ $item->id }}" 
                                                                      name="catatan_admin" rows="2" 
                                                                      placeholder="Catatan untuk warga..."></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-success">Setujui Penukaran</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Reject Modal -->
                                    <div class="modal fade" id="rejectModal{{ $item->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('admin.transaksi.reject', $item->id) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title">Tolak Penukaran</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Tolak penukaran barang oleh <strong>{{ $item->warga->name ?? 'Warga' }}</strong>?</p>
                                                        <p><strong>Barang:</strong> {{ $item->catatan }}</p>
                                                        
                                                        <div class="mb-3">
                                                            <label for="alasan_batal{{ $item->id }}" class="form-label">Alasan Penolakan *</label>
                                                            <textarea class="form-control" id="alasan_batal{{ $item->id }}" 
                                                                      name="alasan_batal" rows="3" 
                                                                      placeholder="Berikan alasan penolakan..." required></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-danger">Tolak Penukaran</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    Menampilkan {{ $transaksi->firstItem() }} sampai {{ $transaksi->lastItem() }} 
                    dari {{ $transaksi->total() }} transaksi
                </div>
                <div>
                    @php
                        // Helper untuk menambahkan query string ke pagination link
                        $queryParams = request()->except('page');
                        $queryString = http_build_query($queryParams);
                    @endphp
                    
                    @if($transaksi->hasPages())
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center mb-0">
                            {{-- Previous Page Link --}}
                            @if($transaksi->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link">
                                        <i class="fas fa-chevron-left"></i>
                                    </span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $transaksi->previousPageUrl() . ($queryString ? '&' . $queryString : '') }}" aria-label="Previous">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                </li>
                            @endif

                            {{-- Pagination Elements --}}
                            @php
                                $current = $transaksi->currentPage();
                                $last = $transaksi->lastPage();
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
                                    <a class="page-link" href="{{ $transaksi->url(1) . ($queryString ? '&' . $queryString : '') }}">1</a>
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
                                    <a class="page-link" href="{{ $transaksi->url($i) . ($queryString ? '&' . $queryString : '') }}">{{ $i }}</a>
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
                                    <a class="page-link" href="{{ $transaksi->url($last) . ($queryString ? '&' . $queryString : '') }}">{{ $last }}</a>
                                </li>
                            @endif

                            {{-- Next Page Link --}}
                            @if($transaksi->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $transaksi->nextPageUrl() . ($queryString ? '&' . $queryString : '') }}" aria-label="Next">
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
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
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
    
    .badge {
        font-size: 0.8em;
        font-weight: 500;
    }
    
    .table-info {
        background-color: rgba(13, 202, 240, 0.1) !important;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Highlight filter yang aktif
    const filters = ['search', 'jenis_transaksi', 'status', 'date'];
    filters.forEach(filter => {
        const element = document.getElementById(filter);
        if(element && element.value) {
            element.classList.add('border-warning');
        }
    });
    
    // Auto close modal setelah submit
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.addEventListener('shown.bs.modal', function() {
            const form = this.querySelector('form');
            if(form) {
                form.addEventListener('submit', function() {
                    const modalInstance = bootstrap.Modal.getInstance(modal);
                    modalInstance.hide();
                });
            }
        });
    });
    
    // Konfirmasi aksi
    document.querySelectorAll('form').forEach(form => {
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn && !form.querySelector('textarea')) {
            submitBtn.addEventListener('click', function(e) {
                if (!confirm('Anda yakin ingin melanjutkan?')) {
                    e.preventDefault();
                }
            });
        }
    });
});
</script>
@endpush