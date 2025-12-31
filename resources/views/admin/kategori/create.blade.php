@extends('layouts.app')

@section('title', 'Tambah Kategori Sampah')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Tambah Kategori Sampah</h2>
            <a href="{{ route('admin.kategori.index') }}" class="btn btn-netra-outline">
                <i class="bi bi-arrow-left me-2"></i>Kembali
            </a>
        </div>
        <p class="text-muted">Tambahkan kategori sampah baru</p>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Form Tambah Kategori</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.kategori.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama_kategori') is-invalid @enderror" 
                                   name="nama_kategori" value="{{ old('nama_kategori') }}" required>
                            @error('nama_kategori')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis Sampah <span class="text-danger">*</span></label>
                            <select class="form-select @error('jenis_sampah') is-invalid @enderror" name="jenis_sampah" required>
                                <option value="">Pilih Jenis</option>
                                <option value="plastik" {{ old('jenis_sampah') == 'plastik' ? 'selected' : '' }}>Plastik</option>
                                <option value="kertas" {{ old('jenis_sampah') == 'kertas' ? 'selected' : '' }}>Kertas</option>
                                <option value="logam" {{ old('jenis_sampah') == 'logam' ? 'selected' : '' }}>Logam</option>
                                <option value="kaca" {{ old('jenis_sampah') == 'kaca' ? 'selected' : '' }}>Kaca</option>
                                <option value="organik" {{ old('jenis_sampah') == 'organik' ? 'selected' : '' }}>Organik</option>
                                <option value="lainnya" {{ old('jenis_sampah') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                            @error('jenis_sampah')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Harga per kg (Rp) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('harga_per_kg') is-invalid @enderror" 
                                   name="harga_per_kg" value="{{ old('harga_per_kg') }}" min="0" required>
                            @error('harga_per_kg')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Poin per kg <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('poin_per_kg') is-invalid @enderror" 
                                   name="poin_per_kg" value="{{ old('poin_per_kg') }}" min="0" required>
                            @error('poin_per_kg')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control @error('deskripsi') is-invalid @enderror" 
                                  name="deskripsi" rows="3">{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Warna Label (Hex)</label>
                            <div class="input-group">
                                <span class="input-group-text">#</span>
                                <input type="text" class="form-control @error('warna_label') is-invalid @enderror" 
                                       name="warna_label" value="{{ old('warna_label', '2E8B57') }}" 
                                       maxlength="6" pattern="[a-fA-F0-9]{6}">
                            </div>
                            @error('warna_label')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Contoh: 2E8B57 (hijau)</small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="status" 
                                       id="status" value="1" {{ old('status', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="status">
                                    Aktif
                                </label>
                            </div>
                            <small class="text-muted">Nonaktifkan untuk menyembunyikan kategori</small>
                        </div>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-netra btn-lg">
                            <i class="bi bi-save me-2"></i>Simpan Kategori
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection