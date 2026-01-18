@extends('layouts.app')

@section('title', 'Tambah Item Spesifik Sampah - Admin')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="h3 mb-0">Tambah Item Spesifik Sampah</h1>
        <a href="{{ route('admin.kategori.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-netra text-white">
                    <h5 class="mb-0"><i class="fas fa-box-open me-2"></i> Form Tambah Item Spesifik</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Info:</strong> Halaman ini untuk menambahkan <strong>Item Spesifik</strong> seperti Botol Plastik, Besi, Kardus, dll. 
                        <br>Kategori utama (Organik, Anorganik, B3, Campuran) sudah ditetapkan dan tidak dapat diubah.
                    </div>
                    
                    <form action="{{ route('admin.kategori.store') }}" method="POST" id="createForm">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nama_kategori" class="form-label">Nama Item Spesifik *</label>
                                    <input type="text" 
                                           class="form-control @error('nama_kategori') is-invalid @enderror" 
                                           id="nama_kategori" 
                                           name="nama_kategori" 
                                           value="{{ old('nama_kategori') }}" 
                                           required
                                           placeholder="Contoh: Botol Plastik, Besi Tua, Kardus">
                                    @error('nama_kategori')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Masukkan nama item spesifik yang akan dikumpulkan</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="jenis_sampah" class="form-label">Kategori Utama *</label>
                                    <select class="form-select @error('jenis_sampah') is-invalid @enderror" 
                                            id="jenis_sampah" 
                                            name="jenis_sampah" 
                                            required>
                                        <option value="">Pilih Kategori Utama</option>
                                        <option value="organik" {{ old('jenis_sampah') == 'organik' ? 'selected' : '' }}>
                                            <i class="fas fa-leaf text-success me-1"></i> Organik
                                        </option>
                                        <option value="anorganik" {{ old('jenis_sampah') == 'anorganik' ? 'selected' : '' }}>
                                            <i class="fas fa-plastic text-info me-1"></i> Anorganik
                                        </option>
                                        <option value="b3" {{ old('jenis_sampah') == 'b3' ? 'selected' : '' }}>
                                            <i class="fas fa-radiation text-danger me-1"></i> B3 (Bahan Berbahaya)
                                        </option>
                                        <option value="campuran" {{ old('jenis_sampah') == 'campuran' ? 'selected' : '' }}>
                                            <i class="fas fa-layer-group text-warning me-1"></i> Campuran
                                        </option>
                                    </select>
                                    @error('jenis_sampah')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Pilih kategori utama untuk item ini</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                                                   
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="poin_per_kg" class="form-label">Poin per Kg *</label>
                                    <input type="number" 
                                           step="0.1" 
                                           min="0" 
                                           class="form-control @error('poin_per_kg') is-invalid @enderror" 
                                           id="poin_per_kg" 
                                           name="poin_per_kg" 
                                           value="{{ old('poin_per_kg', 0) }}" 
                                           required>
                                    @error('poin_per_kg')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Poin yang didapat per kilogram sampah</small>
                                    <div class="mt-1">
                                        <span id="poin_formatted" class="badge bg-success">0 pts</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="warna_label" class="form-label">Warna Label</label>
                                    <div class="input-group">
                                        <input type="color" 
                                               class="form-control form-control-color @error('warna_label') is-invalid @enderror" 
                                               id="warna_label" 
                                               name="warna_label" 
                                               value="{{ old('warna_label', '#3b82f6') }}" 
                                               title="Pilih warna untuk label" 
                                               style="width: 70px;">
                                        <input type="text" 
                                               class="form-control @error('warna_label') is-invalid @enderror" 
                                               id="warna_label_text"
                                               value="{{ old('warna_label', '#3b82f6') }}" 
                                               placeholder="Contoh: #3b82f6" 
                                               pattern="^#[0-9A-Fa-f]{6}$" 
                                               maxlength="7">
                                    </div>
                                    @error('warna_label')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Warna untuk label/item (format HEX: #RRGGBB)</small>
                                </div>
                            </div>
                            

                        </div>
                        
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi Item (Opsional)</label>
                            <textarea class="form-control @error('deskripsi') is-invalid @enderror" 
                                      id="deskripsi" 
                                      name="deskripsi" 
                                      rows="3"
                                      placeholder="Deskripsi singkat tentang item ini, contoh: botol plastik bekas minuman, kardus bekas kemasan, dll.">{{ old('deskripsi') }}</textarea>
                            @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Maksimal 500 karakter</small>
                            <div class="mt-1">
                                <span id="char_count" class="badge bg-secondary">0/500</span>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="reset" class="btn btn-outline-secondary me-2">
                                <i class="fas fa-redo me-1"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-netra">
                                <i class="fas fa-save me-1"></i> Simpan Item
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .form-check-input:checked {
        background-color: var(--netra-primary);
        border-color: var(--netra-primary);
    }
    
    .form-control-color {
        height: 38px;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Format harga real-time
        const hargaInput = document.getElementById('harga_per_kg');
        const hargaFormatted = document.getElementById('harga_formatted');
        
        const poinInput = document.getElementById('poin_per_kg');
        const poinFormatted = document.getElementById('poin_formatted');
        
        // Character counter for description
        const descTextarea = document.getElementById('deskripsi');
        const charCount = document.getElementById('char_count');
        
        // Color picker sync
        const colorPicker = document.getElementById('warna_label');
        const colorText = document.getElementById('warna_label_text');
        
        // Format harga
        function formatHarga() {
            const value = hargaInput.value ? parseInt(hargaInput.value) : 0;
            hargaFormatted.textContent = 'Rp ' + value.toLocaleString('id-ID');
        }
        
        // Format poin
        function formatPoin() {
            const value = poinInput.value ? parseFloat(poinInput.value) : 0;
            poinFormatted.textContent = value.toFixed(1) + ' pts';
        }
        
        // Update character count
        function updateCharCount() {
            const count = descTextarea.value.length;
            charCount.textContent = count + '/500';
            
            if (count > 500) {
                charCount.classList.remove('bg-secondary');
                charCount.classList.add('bg-danger');
            } else {
                charCount.classList.remove('bg-danger');
                charCount.classList.add('bg-secondary');
            }
        }
        
        // Color picker sync
        function updateColorInput(value) {
            if (value.match(/^#[0-9A-Fa-f]{6}$/)) {
                colorPicker.value = value;
            }
        }
        
        // Update text input when color picker changes
        function updateColorText(value) {
            colorText.value = value;
        }
        
        // Event listeners
        hargaInput.addEventListener('input', formatHarga);
        poinInput.addEventListener('input', formatPoin);
        descTextarea.addEventListener('input', updateCharCount);
        colorText.addEventListener('input', function(e) {
            updateColorInput(e.target.value);
        });
        colorPicker.addEventListener('input', function(e) {
            updateColorText(e.target.value);
        });
        
        // Initial formatting
        formatHarga();
        formatPoin();
        updateCharCount();
        
        // Form validation
        document.getElementById('createForm').addEventListener('submit', function(e) {
            let isValid = true;
            
            // Validate nama_kategori
            const namaInput = document.getElementById('nama_kategori');
            if (!namaInput.value.trim()) {
                isValid = false;
                namaInput.classList.add('is-invalid');
            } else {
                namaInput.classList.remove('is-invalid');
            }
            
            // Validate harga_per_kg
            if (!hargaInput.value || parseFloat(hargaInput.value) <= 0) {
                isValid = false;
                hargaInput.classList.add('is-invalid');
            } else {
                hargaInput.classList.remove('is-invalid');
            }
            
            // Validate poin_per_kg
            if (!poinInput.value || parseFloat(poinInput.value) < 0) {
                isValid = false;
                poinInput.classList.add('is-invalid');
            } else {
                poinInput.classList.remove('is-invalid');
            }
            
            // Validate deskripsi length
            if (descTextarea.value.length > 500) {
                isValid = false;
                descTextarea.classList.add('is-invalid');
                charCount.classList.remove('bg-secondary');
                charCount.classList.add('bg-danger');
            } else {
                descTextarea.classList.remove('is-invalid');
            }
            
            if (!isValid) {
                e.preventDefault();
                // Scroll to first error
                const firstError = document.querySelector('.is-invalid');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstError.focus();
                }
            }
        });
    });
</script>
@endpush