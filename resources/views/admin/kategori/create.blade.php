@extends('layouts.app')

@section('title', 'Tambah Kategori Sampah - Admin')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="h3 mb-0">Tambah Kategori Sampah Baru</h1>
        <a href="{{ route('admin.kategori.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.kategori.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="nama_kategori" class="form-label">Nama Kategori *</label>
                            <input type="text" 
                                   class="form-control @error('nama_kategori') is-invalid @enderror" 
                                   id="nama_kategori" 
                                   name="nama_kategori" 
                                   value="{{ old('nama_kategori') }}" 
                                   required
                                   placeholder="Contoh: Plastik, Kertas, Logam">
                            @error('nama_kategori')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
    <label for="jenis_sampah" class="form-label">Jenis Sampah *</label>
    <select class="form-select @error('jenis_sampah') is-invalid @enderror" 
            id="jenis_sampah" 
            name="jenis_sampah" 
            required>
        <option value="">Pilih Jenis Sampah</option>
        <option value="organik" {{ old('jenis_sampah') == 'organik' ? 'selected' : '' }}>Organik</option>
        <option value="anorganik" {{ old('jenis_sampah') == 'anorganik' ? 'selected' : '' }}>Anorganik</option>
        <option value="berbahaya" {{ old('jenis_sampah') == 'berbahaya' ? 'selected' : '' }}>Bahan Berbahaya</option>
        <option value="daur_ulang" {{ old('jenis_sampah') == 'daur_ulang' ? 'selected' : '' }}>Daur Ulang</option>
        <option value="lainnya" {{ old('jenis_sampah') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
    </select>
    @error('jenis_sampah')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <small class="text-muted">Kategori utama sampah</small>
</div>
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
                        </div>
                        
                        <div class="mb-3">
                            <label for="warna_label" class="form-label">Warna Label *</label>
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
                                       value="{{ old('warna_label', '#3b82f6') }}" 
                                       placeholder="Contoh: #3b82f6" 
                                       pattern="^#[0-9A-Fa-f]{6}$" 
                                       maxlength="7"
                                       oninput="updateColorInput(this.value)">
                            </div>
                            @error('warna_label')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Gunakan format HEX (#RRGGBB)</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi (Opsional)</label>
                            <textarea class="form-control @error('deskripsi') is-invalid @enderror" 
                                      id="deskripsi" 
                                      name="deskripsi" 
                                      rows="3"
                                      placeholder="Deskripsi singkat tentang kategori sampah ini">{{ old('deskripsi') }}</textarea>
                            @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-netra">
                                <i class="fas fa-save me-1"></i> Simpan Kategori
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
    function updateColorInput(value) {
        const colorInput = document.getElementById('warna_label');
        if (value.match(/^#[0-9A-Fa-f]{6}$/)) {
            colorInput.value = value;
        }
    }
    
    // Update text input when color picker changes
    document.getElementById('warna_label').addEventListener('input', function(e) {
        const textInput = this.parentElement.nextElementSibling;
        textInput.value = e.target.value;
    });
</script>
@endpush