@extends('layouts.app')

@section('title', 'Penukaran Barang')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-netra text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-shopping-cart me-2"></i> Penukaran Barang
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Barang Info -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <img src="{{ $barang->gambar_url }}" 
                                 alt="{{ $barang->nama_barang }}" 
                                 class="img-fluid rounded">
                        </div>
                        <div class="col-md-8">
                            <h4>{{ $barang->nama_barang }}</h4>
                            <p class="text-muted">{{ $barang->deskripsi }}</p>
                            <div class="d-flex justify-content-between">
                                <div>
                                    <span class="badge bg-secondary">{{ $barang->kategori ?? 'Umum' }}</span>
                                    <span class="badge bg-netra ms-2">{{ number_format($barang->harga_poin) }} poin</span>
                                </div>
                                <div>
                                    <small class="text-muted">
                                        <i class="fas fa-box me-1"></i> Stok: {{ $barang->stok }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Form Penukaran -->
                    <form action="{{ route('warga.penukaran.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="barang_id" value="{{ $barang->id }}">
                        
                        <div class="mb-3">
                            <label class="form-label">Jumlah</label>
                            <input type="number" 
                                   name="jumlah" 
                                   class="form-control" 
                                   value="1" 
                                   min="1" 
                                   max="{{ $barang->stok }}"
                                   required>
                            <div class="form-text">Maksimal: {{ $barang->stok }} item</div>
                        </div>
                        
                        <!-- Summary -->
                        <div class="alert alert-info">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Poin Anda:</span>
                                <strong>{{ number_format(auth()->user()->total_points) }} pts</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Harga per item:</span>
                                <strong>{{ number_format($barang->harga_poin) }} pts</strong>
                            </div>
                            <div class="d-flex justify-content-between fw-bold">
                                <span>Total poin yang akan dikurangi:</span>
                                <strong id="totalPoin">{{ number_format($barang->harga_poin) }} pts</strong>
                            </div>
                            <div class="d-flex justify-content-between mt-2">
                                <span>Sisa poin:</span>
                                <strong id="sisaPoin">{{ number_format(auth()->user()->total_points - $barang->harga_poin) }} pts</strong>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-netra">
                                <i class="fas fa-check me-2"></i> Konfirmasi Penukaran
                            </button>
                            <a href="{{ route('warga.barang.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const jumlahInput = document.querySelector('input[name="jumlah"]');
    const hargaPoin = {{ $barang->harga_poin }};
    const poinUser = {{ auth()->user()->total_points }};
    
    function updateSummary() {
        const jumlah = parseInt(jumlahInput.value) || 1;
        const totalPoin = hargaPoin * jumlah;
        const sisaPoin = poinUser - totalPoin;
        
        document.getElementById('totalPoin').textContent = 
            totalPoin.toLocaleString() + ' pts';
        document.getElementById('sisaPoin').textContent = 
            sisaPoin.toLocaleString() + ' pts';
        
        // Validasi
        const submitBtn = document.querySelector('button[type="submit"]');
        if (jumlah > {{ $barang->stok }} || totalPoin > poinUser) {
            submitBtn.disabled = true;
            submitBtn.classList.remove('btn-netra');
            submitBtn.classList.add('btn-secondary');
        } else {
            submitBtn.disabled = false;
            submitBtn.classList.remove('btn-secondary');
            submitBtn.classList.add('btn-netra');
        }
    }
    
    jumlahInput.addEventListener('input', updateSummary);
    updateSummary(); // Initial call
});
</script>
@endpush
@endsection