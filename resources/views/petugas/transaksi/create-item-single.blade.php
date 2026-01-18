@extends('layouts.app')

@section('title', 'Transaksi Item ' . ($item->nama_kategori ?? ''))

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-1">Transaksi {{ $item->nama_kategori ?? 'Item' }}</h4>
                    <p class="text-muted mb-0">Input berat item spesifik</p>
                </div>
                <div>
                    <a href="{{ URL::previous() }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Item Info -->
    @if($item)
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="mb-0">Informasi Item</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>{{ $item->nama_kategori }}</h5>
                    @if($item->deskripsi)
                    <p class="text-muted">{{ $item->deskripsi }}</p>
                    @endif
                </div>
                <div class="col-md-3">
                    <h6>Harga per kg</h6>
                    <p class="fw-bold text-success">Rp {{ number_format($item->harga_per_kg, 0, ',', '.') }}</p>
                </div>
                <div class="col-md-3">
                    <h6>Poin per kg</h6>
                    <p class="fw-bold text-primary">{{ $item->poin_per_kg }} poin</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Form Transaksi -->
    <div class="card">
        <div class="card-header">
            <h6 class="mb-0">Input Berat</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('petugas.transaksi.store') }}" method="POST">
                @csrf
                <input type="hidden" name="type" value="item">
                <input type="hidden" name="kategori_id" value="{{ $item->id }}">
                
                <div class="mb-4">
                    <label class="form-label">Warga</label>
                    <select name="warga_id" class="form-select" required>
                        <option value="">Pilih Warga</option>
                        <!-- Anda bisa menambahkan opsi warga di sini atau gunakan input manual -->
                    </select>
                    <small class="text-muted">Jika warga tidak ada dalam daftar, gunakan fitur scan QR</small>
                </div>
                
                <div class="mb-4">
                    <label class="form-label">Berat {{ $item->nama_kategori ?? 'Item' }} (kg)</label>
                    <div class="input-group">
                        <input type="number" name="berat" class="form-control" 
                               step="0.1" min="0.1" placeholder="0.0" required
                               id="berat-input">
                        <span class="input-group-text">kg</span>
                    </div>
                    <small class="text-muted">Minimal 0.1 kg</small>
                </div>
                
                <div class="mb-4">
                    <label class="form-label">Catatan (Opsional)</label>
                    <textarea name="catatan" class="form-control" rows="2" 
                              placeholder="Contoh: Kondisi item, jenis kemasan, dll"></textarea>
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
                <div class="d-grid">
                    <button type="submit" class="btn btn-netra btn-lg">
                        <i class="fas fa-save me-2"></i>Simpan Transaksi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const beratInput = document.getElementById('berat-input');
    const beratDisplay = document.getElementById('berat-display');
    const hargaDisplay = document.getElementById('harga-display');
    const poinDisplay = document.getElementById('poin-display');
    
    const hargaPerKg = {{ $item->harga_per_kg ?? 0 }};
    const poinPerKg = {{ $item->poin_per_kg ?? 0 }};
    
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