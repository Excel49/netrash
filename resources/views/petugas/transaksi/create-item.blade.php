@extends('layouts.app')

@section('title', 'Buat Transaksi Item Spesifik')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-1">Transaksi Item Spesifik</h4>
                    <p class="text-muted mb-0">Pilih item sampah yang akan ditimbang</p>
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

    <!-- Daftar Item -->
    @if($warga && $items->count() > 0)
    <div class="row">
        @foreach($items as $item)
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">{{ $item->nama_kategori }}</h5>
                    <p class="card-text text-muted small">{{ $item->deskripsi ?? 'Item sampah spesifik' }}</p>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-success fw-bold">
                                Rp {{ number_format($item->harga_per_kg, 0, ',', '.') }}/kg
                            </span>
                            <span class="text-primary fw-bold">
                                {{ $item->poin_per_kg }} poin/kg
                            </span>
                        </div>
                    </div>
                    
                    <a href="{{ route('petugas.transaksi.create.item', $item->id) }}?warga_id={{ $warga->id }}" 
                       class="btn btn-netra w-100">
                        <i class="fas fa-weight-hanging me-2"></i>Pilih Item
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @elseif($warga)
    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>
        Tidak ada item spesifik yang tersedia saat ini.
    </div>
    @endif
</div>
@endsection