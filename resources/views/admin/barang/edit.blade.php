@extends('layouts.app')

@section('title', 'Edit Barang')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Edit Barang</h2>
            <a href="{{ route('admin.barang.index') }}" class="btn btn-netra-outline">
                <i class="bi bi-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.barang.update', $barang->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <!-- Nama Barang -->
                        <div class="col-md-6 mb-3">
                            <label for="nama_barang" class="form-label">Nama Barang *</label>
                            <input type="text" class="form-control @error('nama_barang') is-invalid @enderror" 
                                   id="nama_barang" name="nama_barang" 
                                   value="{{ old('nama_barang', $barang->nama_barang) }}" required>
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
                    </div>

                    <div class="row">
                        <!-- Harga Poin -->
                        <div class="col-md-6 mb-3">
                            <label for="harga_poin" class="form-label">Harga Poin *</label>
                            <div class="input-group">
                                <input type="number" class="form-control @error('harga_poin') is-invalid @enderror" 
                                       id="harga_poin" name="harga_poin" 
                                       value="{{ old('harga_poin', $barang->harga_poin) }}" 
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
                                   value="{{ old('stok', $barang->stok) }}" 
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
                                      rows="3">{{ old('deskripsi', $barang->deskripsi) }}</textarea>
                            @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <!-- Foto Barang -->
                        <div class="col-md-6 mb-3">
                            <label for="gambar" class="form-label">Foto Barang</label>
                            <input type="file" class="form-control @error('gambar') is-invalid @enderror" 
                                id="gambar" name="gambar" accept="image/*">
                            @error('gambar') <!-- PERBAIKI: dari 'foto' ke 'gambar' -->
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Ukuran maksimal 2MB. Format: JPG, PNG, JPEG</small>
                            
                            <!-- Preview untuk gambar yang akan diupload -->
                            <div id="preview-container" class="mt-2"></div>
                            
                            <!-- Preview untuk gambar yang sudah ada (hanya di edit) -->
                        @if(isset($barang) && $barang->gambar)
                        <div class="mt-2">
                            <small>Foto saat ini:</small><br>
                            @php
                                // Path untuk cek file
                                $imagePath = 'barang/' . $barang->gambar;
                                $storagePath = storage_path('app/public/' . $imagePath);
                                $publicPath = 'storage/' . $imagePath;
                                $fileExists = file_exists($storagePath);
                            @endphp
                            
                            @if($fileExists)
                                <img src="{{ asset($publicPath) }}" 
                                    alt="{{ $barang->nama_barang }}" 
                                    class="img-thumbnail mt-1" style="max-width: 150px;">
                                <div class="text-success small mt-1">
                                    <i class="bi bi-check-circle"></i> File ditemukan
                                </div>
                            @else
                                <div class="alert alert-warning p-2 small">
                                    <i class="bi bi-exclamation-triangle"></i> 
                                    File gambar tidak ditemukan di: {{ $storagePath }}
                                </div>
                            @endif
                        </div>
                        @endif
                        </div>

                        <!-- Status -->
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

                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-danger" 
                                onclick="confirmDelete({{ $barang->id }})">
                            <i class="bi bi-trash me-2"></i>Hapus
                        </button>
                        
                        <div>
                            <a href="{{ route('admin.barang.index') }}" class="btn btn-secondary me-2">
                                Batal
                            </a>
                            <button type="submit" class="btn btn-netra">
                                <i class="bi bi-save me-2"></i>Update Barang
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Form untuk delete (hidden) -->
                <form id="delete-form-{{ $barang->id }}" 
                    action="{{ route('admin.barang.destroy', $barang->id) }}" 
                    method="POST" class="d-none">
                    @csrf
                    @method('DELETE')
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
    const hargaPoin = document.getElementById('harga_poin').value;
    const stok = document.getElementById('stok').value;
    
    if (hargaPoin && stok) {
        const konversiRupiah = hargaPoin * 100;
        const nilaiStok = hargaPoin * stok * 100;
        
        document.getElementById('hargaPoinDisplay').textContent = 
            new Intl.NumberFormat().format(hargaPoin) + ' poin';
        document.getElementById('konversiRupiah').textContent = 
            'Rp ' + new Intl.NumberFormat().format(konversiRupiah);
        document.getElementById('nilaiStok').textContent = 
            'Rp ' + new Intl.NumberFormat().format(nilaiStok);
    }
}

// Event listeners untuk input
document.getElementById('harga_poin').addEventListener('input', hitungKonversi);
document.getElementById('stok').addEventListener('input', hitungKonversi);

// Hitung saat halaman dimuat
document.addEventListener('DOMContentLoaded', hitungKonversi);

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

// Preview image sebelum upload
document.getElementById('gambar').addEventListener('change', function(e) {
    if (e.target.files && e.target.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            // Tampilkan preview jika ada
            const previewDiv = document.querySelector('.preview-image');
            if (!previewDiv) {
                const parent = document.getElementById('gambar').parentNode;
                const div = document.createElement('div');
                div.className = 'preview-image mt-2';
                parent.appendChild(div);
            }
            const previewDivEl = document.querySelector('.preview-image');
            previewDivEl.innerHTML = `
                <small>Preview:</small><br>
                <img src="${e.target.result}" class="img-thumbnail mt-1" style="max-width: 150px;">
            `;
        }
        reader.readAsDataURL(e.target.files[0]);
    }
});
</script>
@endpush