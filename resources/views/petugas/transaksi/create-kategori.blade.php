@extends('layouts.app')

@section('title', 'Buat Transaksi Kategori')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-1">Transaksi Sampah {{ ucfirst($kategori->jenis_sampah ?? 'Kategori') }}</h4>
                    <p class="text-muted mb-0">Input sampah {{ $kategori->nama_kategori ?? '' }}</p>
                </div>
                <div>
                    <a href="{{ route('petugas.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Warga Info -->
    @if($warga)
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h5>{{ $warga->name }}</h5>
                    <p class="mb-1"><strong>Email:</strong> {{ $warga->email }}</p>
                    <p class="mb-1"><strong>Telepon:</strong> {{ $warga->phone ?? '-' }}</p>
                    <p class="mb-1"><strong>Poin:</strong> {{ number_format($warga->total_points, 0, ',', '.') }}</p>
                </div>
                <div class="col-md-4 text-end">
                    @if($warga->qr_code)
                    <img src="{{ asset('storage/' . $warga->qr_code) }}" alt="QR Code" width="80" class="img-thumbnail">
                    @endif
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="alert alert-warning mb-4">
        <i class="fas fa-exclamation-triangle me-2"></i>
        Warga belum dipilih. Silakan scan QR Code terlebih dahulu.
    </div>
    @endif

    <!-- Kategori Info -->
    @if($kategori)
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="mb-0">Informasi Kategori</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <h6>Kategori</h6>
                    <p class="fw-bold">{{ $kategori->nama_kategori }}</p>
                </div>
                <div class="col-md-4">
                    <h6>Harga per kg</h6>
                    <p class="fw-bold text-success">Rp {{ number_format($kategori->harga_per_kg, 0, ',', '.') }}</p>
                </div>
                <div class="col-md-4">
                    <h6>Poin per kg</h6>
                    <p class="fw-bold text-primary">{{ $kategori->poin_per_kg }} poin</p>
                </div>
            </div>
            @if($kategori->deskripsi)
            <div class="mt-3">
                <h6>Deskripsi:</h6>
                <p class="text-muted">{{ $kategori->deskripsi }}</p>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Form Transaksi -->
    @if($warga && $kategori)
    <div class="card">
        <div class="card-header">
            <h6 class="mb-0">Input Transaksi</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('petugas.transaksi.store') }}" method="POST">
                @csrf
                <input type="hidden" name="warga_id" value="{{ $warga->id }}">
                <input type="hidden" name="type" value="kategori">
                <input type="hidden" name="kategori_id" value="{{ $kategori->id }}">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Berat Sampah (kg)</label>
                            <div class="input-group">
                                <input type="number" name="berat" class="form-control" 
                                       step="0.1" min="0.1" placeholder="0.0" required
                                       id="berat-input">
                                <span class="input-group-text">kg</span>
                            </div>
                            <small class="text-muted">Minimal 0.1 kg</small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Catatan (Opsional)</label>
                            <textarea name="catatan" class="form-control" rows="2" 
                                      placeholder="Contoh: Sampah basah, kemasan plastik, dll"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Perhitungan Otomatis -->
                <div class="card bg-light mb-4">
                    <div class="card-body">
                        <h6 class="mb-3">Perhitungan:</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <p class="mb-1">Berat:</p>
                                <h5 id="berat-display">0 kg</h5>
                            </div>
                            <div class="col-md-4">
                                <p class="mb-1">Total Harga:</p>
                                <h5 id="harga-display">Rp 0</h5>
                            </div>
                            <div class="col-md-4">
                                <p class="mb-1">Total Poin:</p>
                                <h5 id="poin-display">0 poin</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tombol Submit -->
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-netra btn-lg">
                        <i class="fas fa-save me-2"></i>Simpan Transaksi
                    </button>
                    <a href="{{ route('petugas.transaksi.select-type') }}?warga_id={{ $warga->id }}" 
                       class="btn btn-outline-secondary">
                        <i class="fas fa-redo me-2"></i>Pilih Jenis Lain
                    </a>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const beratInput = document.getElementById('berat-input');
    const beratDisplay = document.getElementById('berat-display');
    const hargaDisplay = document.getElementById('harga-display');
    const poinDisplay = document.getElementById('poin-display');
    
    const hargaPerKg = {{ $kategori->harga_per_kg ?? 0 }};
    const poinPerKg = {{ $kategori->poin_per_kg ?? 0 }};
    
    function calculate() {
        const berat = parseFloat(beratInput.value) || 0;
        
        // Update display
        beratDisplay.textContent = berat.toFixed(1) + ' kg';
        
        if (berat > 0) {
            const totalHarga = berat * hargaPerKg;
            const totalPoin = berat * poinPerKg;
            
            hargaDisplay.textContent = 'Rp ' + totalHarga.toLocaleString('id-ID');
            poinDisplay.textContent = totalPoin.toLocaleString('id-ID') + ' poin';
        } else {
            hargaDisplay.textContent = 'Rp 0';
            poinDisplay.textContent = '0 poin';
        }
    }
    
    // Event listener
    beratInput.addEventListener('input', calculate);
    
    // Initial calculation
    calculate();
});
</script>
@endpush
@endsection