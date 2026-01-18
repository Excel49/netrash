@extends('layouts.app')
@section('title', 'Pilih Jenis Transaksi')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-trash-alt me-2"></i> Pilih Jenis Sampah</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- OPSI 1: KATEGORI UTAMA -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="fas fa-layer-group me-2"></i> Kategori Utama</h5>
                            </div>
                            <div class="card-body text-center">
                                <div class="mb-4">
                                    <i class="fas fa-boxes fa-4x text-success mb-3"></i>
                                    <p class="mb-0">Transaksi berdasarkan kategori utama</p>
                                </div>
                                <div class="d-grid gap-2">
                                    @foreach(['organik', 'anorganik', 'b3', 'campuran'] as $jenis)
                                        <a href="{{ route('petugas.transaksi.create', ['type' => 'kategori', 'jenis' => $jenis]) }}" 
                                           class="btn btn-outline-success btn-lg">
                                            <i class="fas fa-arrow-right me-2"></i>
                                            {{ ucfirst($jenis) }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- OPSI 2: ITEM SPESIFIK -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0"><i class="fas fa-tags me-2"></i> Item Spesifik</h5>
                            </div>
                            <div class="card-body text-center">
                                <div class="mb-4">
                                    <i class="fas fa-box-open fa-4x text-info mb-3"></i>
                                    <p class="mb-0">Transaksi berdasarkan item spesifik</p>
                                </div>
                                <div class="d-grid">
                                    <a href="{{ route('petugas.transaksi.create', ['type' => 'item']) }}" 
                                       class="btn btn-info btn-lg">
                                        <i class="fas fa-plus-circle me-2"></i>
                                        Pilih Item Spesifik
                                    </a>
                                </div>
                                
                                <!-- Daftar item spesifik populer -->
                                @php
                                    $populerItems = App\Models\KategoriSampah::unlocked()
                                        ->where('status', true)
                                        ->limit(5)
                                        ->get();
                                @endphp
                                @if($populerItems->count() > 0)
                                    <hr>
                                    <h6 class="mt-3">Item Populer:</h6>
                                    <div class="list-group mt-2">
                                        @foreach($populerItems as $item)
                                            <a href="{{ route('petugas.transaksi.create.item', $item->id) }}" 
                                               class="list-group-item list-group-item-action">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span>{{ $item->nama_kategori }}</span>
                                                    <small class="text-success">{{ $item->poin_formatted }}</small>
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection