@extends('layouts.app')

@section('title', 'Buat Transaksi Baru')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Buat Transaksi Baru</h2>
            <a href="{{ route('petugas.dashboard') }}" class="btn btn-netra-outline">
                <i class="bi bi-arrow-left me-2"></i>Kembali
            </a>
        </div>
        <p class="text-muted">Input data sampah dari warga</p>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Data Warga</h6>
            </div>
            <div class="card-body">
                @if($warga)
                <div class="row">
                    <div class="col-md-8">
                        <h5>{{ $warga->name }}</h5>
                        <p class="mb-1"><strong>Email:</strong> {{ $warga->email }}</p>
                        <p class="mb-1"><strong>Telepon:</strong> {{ $warga->phone }}</p>
                        <p class="mb-1"><strong>Alamat:</strong> {{ $warga->address }}</p>
                        <p class="mb-1"><strong>Total Poin:</strong> {{ number_format($warga->total_points, 0, ',', '.') }}</p>
                    </div>
                    <div class="col-md-4 text-end">
                        @if($warga->qr_code)
                        <img src="{{ asset('storage/' . $warga->qr_code) }}" alt="QR Code" width="100" class="img-thumbnail">
                        @endif
                    </div>
                </div>
                @else
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Warga belum dipilih. Silakan scan QR Code atau cari warga.
                </div>
                @endif
            </div>
        </div>
        
        <!-- Form Input Sampah -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">Input Sampah</h6>
            </div>
            <div class="card-body">
                <form id="transaksi-form" action="{{ route('petugas.transaksi.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="warga_id" value="{{ $warga->id ?? '' }}">
                    
                    <!-- Container untuk input kategori -->
                    <div id="kategori-container">
                        <div class="kategori-item mb-4 p-3 border rounded">
                            <div class="row">
                                <div class="col-md-5">
                                    <label class="form-label">Kategori Sampah</label>
                                    <select name="kategori[0][id]" class="form-select kategori-select" required>
                                        <option value="">Pilih Kategori</option>
                                        @foreach($kategori as $kat)
                                        <option value="{{ $kat->id }}" 
                                                data-harga="{{ $kat->harga_per_kg }}"
                                                data-poin="{{ $kat->poin_per_kg }}">
                                            {{ $kat->nama_kategori }} (Rp {{ number_format($kat->harga_per_kg, 0, ',', '.') }}/kg)
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Berat (kg)</label>
                                    <input type="number" name="kategori[0][berat]" class="form-control berat-input" 
                                           step="0.1" min="0.1" placeholder="0.0" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Hasil</label>
                                    <div class="hasil-info p-2 bg-light rounded">
                                        <small class="text-muted d-block">Rp 0</small>
                                        <small class="text-muted d-block">0 poin</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tombol Tambah Kategori -->
                    <button type="button" id="btn-tambah-kategori" class="btn btn-outline-netra mb-4">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Kategori Lain
                    </button>
                    
                    <!-- Total -->
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <h6>Total Berat</h6>
                                    <h4 id="total-berat">0 kg</h4>
                                </div>
                                <div class="col-md-4">
                                    <h6>Total Harga</h6>
                                    <h4 id="total-harga">Rp 0</h4>
                                </div>
                                <div class="col-md-4">
                                    <h6>Total Poin</h6>
                                    <h4 id="total-poin">0 poin</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Catatan -->
                    <div class="mb-4">
                        <label class="form-label">Catatan (Opsional)</label>
                        <textarea name="catatan" class="form-control" rows="3" placeholder="Catatan tambahan..."></textarea>
                    </div>
                    
                    <!-- Tombol Submit -->
                    <div class="d-grid">
                        <button type="submit" class="btn btn-netra btn-lg" id="btn-submit" {{ !$warga ? 'disabled' : '' }}>
                            <i class="bi bi-check-circle me-2"></i>Simpan Transaksi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Info Kategori -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Daftar Kategori</h6>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @foreach($kategori as $kat)
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">{{ $kat->nama_kategori }}</h6>
                            <span class="badge bg-netra">Rp {{ number_format($kat->harga_per_kg, 0, ',', '.') }}/kg</span>
                        </div>
                        <p class="mb-1 small">{{ $kat->poin_per_kg }} poin/kg</p>
                        <small class="text-muted">{{ $kat->jenis_sampah }}</small>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <!-- Petunjuk -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">Petunjuk</h6>
            </div>
            <div class="card-body">
                <ol class="mb-0">
                    <li class="mb-2">Pilih kategori sampah</li>
                    <li class="mb-2">Input berat dalam kg</li>
                    <li class="mb-2">Sistem hitung otomatis</li>
                    <li class="mb-2">Tambahkan kategori jika perlu</li>
                    <li class="mb-2">Klik simpan transaksi</li>
                </ol>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let kategoriCounter = 1;
    
    // Tombol tambah kategori
    document.getElementById('btn-tambah-kategori').addEventListener('click', function() {
        const container = document.getElementById('kategori-container');
        const newItem = document.createElement('div');
        newItem.className = 'kategori-item mb-4 p-3 border rounded';
        newItem.innerHTML = `
            <div class="row">
                <div class="col-md-5">
                    <label class="form-label">Kategori Sampah</label>
                    <select name="kategori[${kategoriCounter}][id]" class="form-select kategori-select" required>
                        <option value="">Pilih Kategori</option>
                        @foreach($kategori as $kat)
                        <option value="{{ $kat->id }}" 
                                data-harga="{{ $kat->harga_per_kg }}"
                                data-poin="{{ $kat->poin_per_kg }}">
                            {{ $kat->nama_kategori }} (Rp {{ number_format($kat->harga_per_kg, 0, ',', '.') }}/kg)
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Berat (kg)</label>
                    <input type="number" name="kategori[${kategoriCounter}][berat]" class="form-control berat-input" 
                           step="0.1" min="0.1" placeholder="0.0" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Hasil</label>
                    <div class="hasil-info p-2 bg-light rounded">
                        <small class="text-muted d-block">Rp 0</small>
                        <small class="text-muted d-block">0 poin</small>
                    </div>
                    <button type="button" class="btn btn-sm btn-danger mt-2 btn-hapus-kategori">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `;
        
        container.appendChild(newItem);
        
        // Add event listeners to new elements
        const select = newItem.querySelector('.kategori-select');
        const beratInput = newItem.querySelector('.berat-input');
        
        select.addEventListener('change', calculateItem);
        beratInput.addEventListener('input', calculateItem);
        
        // Hapus kategori
        newItem.querySelector('.btn-hapus-kategori').addEventListener('click', function() {
            newItem.remove();
            calculateTotal();
        });
        
        kategoriCounter++;
    });
    
    // Hitung per item
    function calculateItem(event) {
        const item = event.target.closest('.kategori-item');
        const select = item.querySelector('.kategori-select');
        const beratInput = item.querySelector('.berat-input');
        const hasilInfo = item.querySelector('.hasil-info');
        
        const selectedOption = select.options[select.selectedIndex];
        const hargaPerKg = parseFloat(selectedOption.dataset.harga) || 0;
        const poinPerKg = parseFloat(selectedOption.dataset.poin) || 0;
        const berat = parseFloat(beratInput.value) || 0;
        
        const harga = berat * hargaPerKg;
        const poin = berat * poinPerKg;
        
        hasilInfo.innerHTML = `
            <small class="text-success d-block">Rp ${harga.toLocaleString('id-ID')}</small>
            <small class="text-primary d-block">${poin.toLocaleString('id-ID')} poin</small>
        `;
        
        calculateTotal();
    }
    
    // Hitung total
    function calculateTotal() {
        let totalBerat = 0;
        let totalHarga = 0;
        let totalPoin = 0;
        
        document.querySelectorAll('.kategori-item').forEach(item => {
            const select = item.querySelector('.kategori-select');
            const beratInput = item.querySelector('.berat-input');
            
            const selectedOption = select.options[select.selectedIndex];
            const hargaPerKg = parseFloat(selectedOption.dataset.harga) || 0;
            const poinPerKg = parseFloat(selectedOption.dataset.poin) || 0;
            const berat = parseFloat(beratInput.value) || 0;
            
            totalBerat += berat;
            totalHarga += berat * hargaPerKg;
            totalPoin += berat * poinPerKg;
        });
        
        document.getElementById('total-berat').textContent = totalBerat.toFixed(1) + ' kg';
        document.getElementById('total-harga').textContent = 'Rp ' + totalHarga.toLocaleString('id-ID');
        document.getElementById('total-poin').textContent = totalPoin.toLocaleString('id-ID') + ' poin';
    }
    
    // Add event listeners to initial elements
    document.querySelectorAll('.kategori-select').forEach(select => {
        select.addEventListener('change', calculateItem);
    });
    
    document.querySelectorAll('.berat-input').forEach(input => {
        input.addEventListener('input', calculateItem);
    });
    
    // Form submission
    document.getElementById('transaksi-form').addEventListener('submit', function(e) {
        const btn = document.getElementById('btn-submit');
        btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Menyimpan...';
        btn.disabled = true;
    });
});
</script>
@endpush
@endsection