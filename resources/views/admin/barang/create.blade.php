@extends('layouts.app')

@section('title', 'Tambah Barang Baru')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Tambah Barang Baru</h2>
            <a href="{{ route('admin.barang.index') }}" class="btn btn-netra-outline">
                <i class="bi bi-arrow-left me-2"></i>Kembali ke Daftar Barang
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.barang.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        <!-- Nama Barang -->
                        <div class="col-md-6 mb-3">
                            <label for="nama_barang" class="form-label">Nama Barang *</label>
                            <input type="text" class="form-control @error('nama_barang') is-invalid @enderror" 
                                   id="nama_barang" name="nama_barang" 
                                   value="{{ old('nama_barang') }}" required>
                            @error('nama_barang')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Kategori -->
                        <div class="col-md-6 mb-3">
                            <label for="kategori" class="form-label">Kategori Barang</label>
                            <select class="form-control @error('kategori') is-invalid @enderror" 
                                    id="kategori" name="kategori">
                                <option value="">Pilih Kategori Barang</option>
                                <option value="elektronik" {{ old('kategori', $barang->kategori ?? '') == 'elektronik' ? 'selected' : '' }}>
                                    Elektronik
                                </option>
                                <option value="perabotan" {{ old('kategori', $barang->kategori ?? '') == 'perabotan' ? 'selected' : '' }}>
                                    Perabotan
                                </option>
                                <option value="pakaian" {{ old('kategori', $barang->kategori ?? '') == 'pakaian' ? 'selected' : '' }}>
                                    Pakaian
                                </option>
                                <option value="makanan" {{ old('kategori', $barang->kategori ?? '') == 'makanan' ? 'selected' : '' }}>
                                    Makanan/Minuman
                                </option>
                                <option value="voucher" {{ old('kategori', $barang->kategori ?? '') == 'voucher' ? 'selected' : '' }}>
                                    Voucher/Pulsa
                                </option>
                                <option value="lainnya" {{ old('kategori', $barang->kategori ?? '') == 'lainnya' ? 'selected' : '' }}>
                                    Lainnya
                                </option>
                            </select>
                            @error('kategori')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    <div class="row">
                        <!-- Harga Poin -->
                        <div class="col-md-6 mb-3">
                            <label for="harga_poin" class="form-label">Harga Poin *</label>
                            <div class="input-group">
                                <input type="number" class="form-control @error('harga_poin') is-invalid @enderror" 
                                       id="harga_poin" name="harga_poin" 
                                       value="{{ old('harga_poin') }}" 
                                       min="100" required>
                                <span class="input-group-text">poin</span>
                            </div>
                            @error('harga_poin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Minimum 100 poin</small>
                        </div>

                        <!-- Stok -->
                        <div class="col-md-6 mb-3">
                            <label for="stok" class="form-label">Stok *</label>
                            <input type="number" class="form-control @error('stok') is-invalid @enderror" 
                                   id="stok" name="stok" 
                                   value="{{ old('stok', 0) }}" 
                                   min="0" required>
                            @error('stok')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <!-- Deskripsi -->
                        <div class="col-md-12 mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <textarea class="form-control @error('deskripsi') is-invalid @enderror" 
                                      id="deskripsi" name="deskripsi" 
                                      rows="3">{{ old('deskripsi') }}</textarea>
                            @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Deskripsikan detail barang (opsional)</small>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Foto Barang -->
                        <!-- Foto Barang -->
                        <div class="col-md-6 mb-3">
                            <label for="gambar" class="form-label">Foto Barang</label>
                            <input type="file" class="form-control @error('gambar') is-invalid @enderror" 
                                id="gambar" name="gambar" accept="image/*">
                            @error('gambar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Ukuran maksimal 2MB. Format: JPG, PNG, JPEG</small>
                            
                            <!-- Preview untuk gambar yang akan diupload -->
                            <div id="preview-container" class="mt-2"></div>
                        </div>


                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" 
                                    role="switch" id="status" name="status" 
                                    value="1" {{ old('status', isset($barang) ? $barang->status : true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="status">
                                    <span class="text-success"><i class="bi bi-check-circle-fill"></i> Aktif</span> - Barang dapat ditampilkan dan ditukar
                                </label>
                            </div>
                            <small class="text-muted">Nonaktifkan untuk menyembunyikan barang dari katalog</small>
                            @error('status')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                    <!-- Konversi ke Rupiah (hanya tampilan) -->
                    <div class="alert alert-info">
                        <h6><i class="bi bi-info-circle me-2"></i> Informasi Konversi</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <small class="text-muted">Harga Poin:</small><br>
                                <strong id="hargaPoinDisplay">0 poin</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">Konversi ke Rupiah (1 poin = Rp 100):</small><br>
                                <strong id="konversiRupiah">Rp 0</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">Nilai Stok:</small><br>
                                <strong id="nilaiStok">Rp 0</strong>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4 gap-2">
                        <a href="{{ route('admin.barang.index') }}" class="btn btn-secondary">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-netra">
                            <i class="bi bi-plus-circle me-2"></i>Tambah Barang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Fungsi untuk menghitung konversi
function hitungKonversi() {
    const hargaPoin = document.getElementById('harga_poin').value || 0;
    const stok = document.getElementById('stok').value || 0;
    
    const konversiRupiah = hargaPoin * 100;
    const nilaiStok = hargaPoin * stok * 100;
    
    document.getElementById('hargaPoinDisplay').textContent = 
        new Intl.NumberFormat().format(hargaPoin) + ' poin';
    document.getElementById('konversiRupiah').textContent = 
        'Rp ' + new Intl.NumberFormat().format(konversiRupiah);
    document.getElementById('nilaiStok').textContent = 
        'Rp ' + new Intl.NumberFormat().format(nilaiStok);
}

// Event listeners untuk input
document.getElementById('harga_poin').addEventListener('input', hitungKonversi);
document.getElementById('stok').addEventListener('input', hitungKonversi);

// Hitung saat halaman dimuat
document.addEventListener('DOMContentLoaded', hitungKonversi);

// Preview image sebelum upload
document.getElementById('gambar').addEventListener('change', function(e) {
    const previewContainer = document.getElementById('preview-container');
    previewContainer.innerHTML = '';
    
    if (e.target.files && e.target.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const previewDiv = document.createElement('div');
            previewDiv.innerHTML = `
                <small>Preview:</small><br>
                <img src="${e.target.result}" class="img-thumbnail mt-1" style="max-width: 150px;">
            `;
            previewContainer.appendChild(previewDiv);
        }
        reader.readAsDataURL(e.target.files[0]);
    }
});
</script>
@endpush