@extends('layouts.app')

@section('title', 'Daftar Barang')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Daftar Barang</h2>
            <div>
                <a href="{{ route('admin.barang.create') }}" class="btn btn-netra">
                    <i class="fas fa-plus-circle me-2"></i>Tambah Barang
                </a>
            </div>
        </div>
        <p class="text-muted">Kelola barang yang dapat ditukar dengan poin</p>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-uppercase mb-0">Total Barang</h6>
                        <h3 class="mb-0">{{ $totalBarang }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-box-seam fs-1"></i>
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
                        <h6 class="text-uppercase mb-0">Total Stok</h6>
                        <h3 class="mb-0">{{ $totalStok }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-archive fs-1"></i>
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
                        <h6 class="text-uppercase mb-0">Total Nilai Poin</h6>
                        <h3 class="mb-0">{{ number_format($totalNilaiPoin) }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-cash-coin fs-1"></i>
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
                        <h6 class="text-uppercase mb-0">Barang Aktif</h6>
                        <h3 class="mb-0">{{ $barangAktif }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-check-circle fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter dan Search -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.barang.index') }}" class="row g-3">
            <div class="col-md-5">
                <label for="search" class="form-label">Cari Barang</label>
                <input type="text" class="form-control" id="search" name="search" 
                       value="{{ request('search') }}" placeholder="Nama barang atau deskripsi...">
            </div>
            
            <div class="col-md-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-control" id="status" name="status">
                    <option value="">Semua Status</option>
                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
            
            <div class="col-md-2 d-flex align-items-end">
                <div class="d-grid w-100">
                    <button type="submit" class="btn btn-netra">
                        <i class="fas fa-search me-2"></i>Filter
                    </button>
                </div>
            </div>
            
            <div class="col-md-2 d-flex align-items-end">
                <div class="d-grid w-100">
                    <a href="{{ route('admin.barang.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-redo me-2"></i>Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Table Barang -->
<div class="card">
    <div class="card-body">
        @if($barang->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-box text-muted" style="font-size: 4rem;"></i>
                <h4 class="mt-3">Belum ada barang</h4>
                <p class="text-muted">Tambahkan barang pertama untuk memulai</p>
                <a href="{{ route('admin.barang.create') }}" class="btn btn-netra mt-2">
                    <i class="fas fa-plus-circle me-2"></i>Tambah Barang Pertama
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Gambar</th>
                            <th>Nama Barang</th>
                            <th>Harga Poin</th>
                            <th>Stok</th>
                            <th>Total Nilai</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($barang as $item)
                        <tr>
                            <td>{{ $loop->iteration + (($barang->currentPage() - 1) * $barang->perPage()) }}</td>
                        <td>
                            @if($item->gambar)
                                @php
                                    // Path lengkap untuk cek file
                                    $storagePath = 'barang/' . $item->gambar;
                                    $fullPath = storage_path('app/public/' . $storagePath);
                                    $publicPath = 'storage/' . $storagePath;
                                @endphp
                                
                                @if(file_exists($fullPath))
                                    <img src="{{ asset($publicPath) }}" 
                                        alt="{{ $item->nama_barang }}" 
                                        class="img-thumbnail" 
                                        style="width: 50px; height: 50px; object-fit: cover;"
                                        onerror="this.onerror=null; this.src='{{ asset('img/default-product.png') }}'">
                                @else
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                        style="width: 50px; height: 50px;">
                                        <small class="text-muted">No Image</small>
                                    </div>
                                @endif
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                    style="width: 50px; height: 50px;">
                                    <i class="bi bi-box text-muted"></i>
                                </div>
                            @endif
                        </td>
                            <td>
                                <strong>{{ $item->nama_barang }}</strong>
                                @if($item->deskripsi)
                                    <br><small class="text-muted">{{ Str::limit($item->deskripsi, 50) }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-success">{{ number_format($item->harga_poin) }} poin</span>
                            </td>
                            <td>
                                @if($item->stok > 10)
                                    <span class="badge bg-success">{{ $item->stok }}</span>
                                @elseif($item->stok > 0)
                                    <span class="badge bg-warning">{{ $item->stok }}</span>
                                @else
                                    <span class="badge bg-danger">Habis</span>
                                @endif
                            </td>
                            <td>
                                <small>{{ number_format($item->harga_poin * $item->stok) }} poin</small>
                            </td>
                            <td>
                                @if($item->status)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-nowrap">
                                <a href="{{ route('admin.barang.edit', $item->id) }}" 
                                class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                
                                <button type="button" 
                                        class="btn btn-sm btn-outline-danger"
                                        onclick="confirmDelete({{ $item->id }})">
                                    <i class="bi bi-trash"></i>
                                </button>
                                
                                <!-- Form delete hidden -->
                                <form id="delete-form-{{ $item->id }}" 
                                    action="{{ route('admin.barang.destroy', $item->id) }}" 
                                    method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
           <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    Menampilkan {{ $barang->firstItem() }} sampai {{ $barang->lastItem() }} 
                    dari {{ $barang->total() }} barang
                </div>
                <div>
                    @php
                        // Helper untuk menambahkan query string ke pagination link
                        $queryParams = request()->except('page');
                        $queryString = http_build_query($queryParams);
                    @endphp
                    
                    @if($barang->hasPages())
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center mb-0">
                            {{-- Previous Page Link --}}
                            @if($barang->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link">
                                        <i class="fas fa-chevron-left"></i>
                                    </span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $barang->previousPageUrl() . ($queryString ? '&' . $queryString : '') }}" aria-label="Previous">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                </li>
                            @endif

                            {{-- Pagination Elements --}}
                            @php
                                $current = $barang->currentPage();
                                $last = $barang->lastPage();
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
                                    <a class="page-link" href="{{ $barang->url(1) . ($queryString ? '&' . $queryString : '') }}">1</a>
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
                                    <a class="page-link" href="{{ $barang->url($i) . ($queryString ? '&' . $queryString : '') }}">{{ $i }}</a>
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
                                    <a class="page-link" href="{{ $barang->url($last) . ($queryString ? '&' . $queryString : '') }}">{{ $last }}</a>
                                </li>
                            @endif

                            {{-- Next Page Link --}}
                            @if($barang->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $barang->nextPageUrl() . ($queryString ? '&' . $queryString : '') }}" aria-label="Next">
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
    
    .img-thumbnail {
        transition: transform 0.2s;
    }
    
    .img-thumbnail:hover {
        transform: scale(1.1);
    }
</style>
@endpush

@push('scripts')
<script>
// Fungsi konfirmasi delete
function confirmDelete(id) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Barang akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Submit form delete
            document.getElementById('delete-form-' + id).submit();
        }
    });
}

// Quick filter
document.addEventListener('DOMContentLoaded', function() {
    const statusFilter = document.getElementById('status');
    
    // Jika ada filter, beri highlight
    if(statusFilter.value) {
        statusFilter.classList.add('border-warning');
    }
});
</script>
@endpush