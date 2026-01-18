@extends('layouts.app')

@section('title', 'Detail Penukaran Barang')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Detail Penukaran Barang</h2>
            <a href="{{ route('warga.transaksi.index') }}" class="btn btn-netra-outline">
                <i class="bi bi-arrow-left me-2"></i>Kembali ke Transaksi
            </a>
        </div>
        <p class="text-muted">Kode Transaksi: {{ $transaksi->kode_transaksi }}</p>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <!-- Status Card -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted">Status Penukaran</h6>
                        @if($transaksi->status == 'pending')
                        <div class="alert alert-warning mb-0">
                            <h5 class="alert-heading">
                                <i class="bi bi-clock-history me-2"></i>Menunggu
                            </h5>
                            <p class="mb-0">Penukaran sedang diproses.</p>
                        </div>
                        @elseif($transaksi->status == 'completed')
                        <div class="alert alert-success mb-0">
                            <h5 class="alert-heading">
                                <i class="bi bi-check2-circle me-2"></i>Berhasil
                            </h5>
                            <p class="mb-0">Penukaran berhasil dilakukan.</p>
                        </div>
                        @else
                        <div class="alert alert-danger mb-0">
                            <h5 class="alert-heading">
                                <i class="bi bi-x-circle me-2"></i>Dibatalkan
                            </h5>
                            <p class="mb-0">Penukaran dibatalkan.</p>
                        </div>
                        @endif
                    </div>
                    <div class="col-md-6 text-end">
                        <h6 class="text-muted">Jumlah Poin</h6>
                        <h2 class="text-netra">{{ number_format(abs($transaksi->total_poin), 0, ',', '.') }} Poin</h2>
                        @if($transaksi->jenis_transaksi == 'penukaran')
                        <h6 class="text-danger">Poin Berkurang</h6>
                        @else
                        <h6 class="text-success">Poin Bertambah</h6>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Detail Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Detail Penukaran</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="30%">Tanggal Transaksi</th>
                        <td>{{ $transaksi->created_at->format('d F Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Total Berat</th>
                        <td>{{ number_format($transaksi->total_berat, 2, ',', '.') }} kg</td>
                    </tr>
                    <tr>
                        <th>Catatan</th>
                        <td>{{ $transaksi->catatan ?? 'Penukaran barang' }}</td>
                    </tr>
                    @if($transaksi->petugas)
                    <tr>
                        <th>Petugas</th>
                        <td>{{ $transaksi->petugas->name }}</td>
                    </tr>
                    @endif
                </table>
                
                <!-- Detail Barang jika ada -->
                @if($transaksi->detailTransaksi && count($transaksi->detailTransaksi) > 0)
                <div class="mt-4">
                    <h6>Detail Barang:</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Kategori</th>
                                    <th>Berat</th>
                                    <th>Poin</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transaksi->detailTransaksi as $detail)
                                <tr>
                                    <td>{{ $detail->kategori->nama_kategori ?? '-' }}</td>
                                    <td>{{ number_format($detail->berat, 2, ',', '.') }} kg</td>
                                    <td>{{ number_format($detail->poin, 0, ',', '.') }} poin</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="card mb-4">
            <div class="card-body text-center">
                @if($transaksi->status == 'completed')
                <a href="{{ route('warga.transaksi.index') }}" class="btn btn-netra-outline">
                    <i class="bi bi-list me-2"></i>Lihat Semua Transaksi
                </a>
                @if($transaksi->jenis_transaksi == 'penukaran')
                <a href="{{ route('warga.barang.index') }}" class="btn btn-netra ms-2">
                    <i class="bi bi-basket me-2"></i>Tukar Barang Lain
                </a>
                @endif
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background-color: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-marker {
    position: absolute;
    left: -30px;
    top: 0;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 0 0 2px #e9ecef;
}

.timeline-item.completed .timeline-marker {
    box-shadow: 0 0 0 2px var(--primary-color);
}

.timeline-content {
    padding-left: 20px;
}

.timeline-item.cancelled .timeline-marker {
    background-color: #dc3545 !important;
}
</style>
@endsection