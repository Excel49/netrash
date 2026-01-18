@extends('layouts.app')

@section('title', $barang->nama_barang)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="row g-0">
                    <div class="col-md-5">
                        <img src="{{ $barang->gambar_url }}" 
                             class="img-fluid rounded-start" 
                             alt="{{ $barang->nama_barang }}"
                             style="height: 300px; object-fit: cover;">
                    </div>
                    <div class="col-md-7">
                        <div class="card-body">
                            <h1 class="card-title h3">{{ $barang->nama_barang }}</h1>
                            
                            <div class="d-flex align-items-center mb-3">
                                <span class="badge bg-secondary me-2">{{ ucfirst($barang->kategori) }}</span>
                                <span class="badge bg-netra">{{ number_format($barang->harga_poin) }} poin</span>
                            </div>
                            
                            <div class="mb-3">
                                <h5 class="text-muted">Deskripsi</h5>
                                <p>{{ $barang->deskripsi }}</p>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <h6 class="text-muted">Stok Tersedia</h6>
                                    <p class="h4 {{ $barang->stok == 0 ? 'text-danger' : 'text-success' }}">
                                        {{ $barang->stok }} pcs
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted">Tipe Penukaran</h6>
                                    <p class="h5">
                                        <i class="fas fa-{{ $barang->tipe_penukaran == 'ambil_sendiri' ? 'walking' : 'truck' }} me-1"></i>
                                        {{ $barang->tipe_penukaran == 'ambil_sendiri' ? 'Ambil Sendiri' : 'Dikirim' }}
                                    </p>
                                </div>
                            </div>
                            
                            @if($barang->lokasi_penyerahan)
                            <div class="mb-3">
                                <h6 class="text-muted">Lokasi Penyerahan</h6>
                                <p><i class="fas fa-map-marker-alt me-1"></i> {{ $barang->lokasi_penyerahan }}</p>
                            </div>
                            @endif
                            
                            <div class="d-grid gap-2">
                                @if($barang->stok > 0 && $barang->status)
                                    <a href="{{ route('warga.penukaran.create') }}?barang_id={{ $barang->id }}" 
                                       class="btn btn-netra btn-lg">
                                        <i class="fas fa-shopping-cart me-2"></i> Tukar Poin Sekarang
                                    </a>
                                @else
                                    <button class="btn btn-secondary btn-lg" disabled>
                                        <i class="fas fa-times me-2"></i> Barang Tidak Tersedia
                                    </button>
                                @endif
                                <a href="{{ route('warga.barang.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Katalog
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- Poin Info -->
            <div class="card mb-4">
                <div class="card-body text-center">
                    <h6 class="card-title text-muted mb-2">
                        <i class="fas fa-coins me-1"></i> Poin Anda
                    </h6>
                    <h2 class="text-netra mb-1">
                        {{ number_format(auth()->user()->total_points, 0, ',', '.') }}
                    </h2>
                    <small class="text-muted">pts</small>
                    
                    @if($barang->stok > 0 && $barang->status)
                        <div class="mt-4">
                            <p class="text-muted mb-1">Maksimal barang yang bisa ditukar:</p>
                            @php
                                $maxQty = min($barang->stok, floor(auth()->user()->total_points / $barang->harga_poin));
                            @endphp
                            <h4 class="text-success">{{ $maxQty }} pcs</h4>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Syarat & Ketentuan -->
            @if($barang->syarat_ketentuan)
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-file-alt me-2"></i> Syarat & Ketentuan
                    </h6>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-0">{{ $barang->syarat_ketentuan }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection