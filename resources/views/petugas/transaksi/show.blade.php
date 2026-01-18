@extends('layouts.app')

@section('title', 'Detail Transaksi')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Detail Transaksi</h2>
            <div>
                <a href="{{ route('petugas.transaksi.print', $transaksi->id) }}" 
                   class="btn btn-netra-outline me-2" target="_blank">
                    <i class="bi bi-printer me-2"></i>Print
                </a>
                <a href="{{ route('petugas.transaksi.index') }}" class="btn btn-netra-outline">
                    <i class="bi bi-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
        <p class="text-muted">Kode: {{ $transaksi->kode_transaksi }}</p>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Detail Transaksi -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Informasi Transaksi</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Kode Transaksi</th>
                                <td>{{ $transaksi->kode_transaksi }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal</th>
                                <td>
                                    @if($transaksi->tanggal_transaksi instanceof \Illuminate\Support\Carbon)
                                        {{ $transaksi->tanggal_transaksi->format('d/m/Y H:i') }}
                                    @else
                                        {{ \Carbon\Carbon::parse($transaksi->tanggal_transaksi ?? $transaksi->created_at)->format('d/m/Y H:i') }}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Petugas</th>
                                <td>{{ $transaksi->petugas->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    @if($transaksi->status == 'completed')
                                    <span class="badge bg-success">Selesai</span>
                                    @elseif($transaksi->status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                    @else
                                    <span class="badge bg-danger">Dibatalkan</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Total Berat</th>
                                <td>{{ number_format($transaksi->total_berat ?? 0, 1) }} kg</td>
                            </tr>
                            <tr>
                                <th>Total Poin</th>
                                <td>{{ number_format($transaksi->total_poin ?? 0, 0, ',', '.') }} poin</td>
                            </tr>
                            <tr>
                                <th>Catatan</th>
                                <td>{{ $transaksi->catatan ?: '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Detail Item -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Detail Sampah</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Kategori</th>
                                <th>Berat (kg)</th>
                                <th>Poin</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transaksi->detailTransaksi ?? [] as $detail)
                            <tr>
                                <td>{{ $detail->kategori->nama_kategori ?? 'N/A' }}</td>
                                <td>{{ number_format($detail->berat ?? 0, 1) }}</td>
                                <td>{{ number_format($detail->poin ?? 0, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-3">Tidak ada detail transaksi</td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="2" class="text-end">Total:</th>
                                <th>{{ number_format($transaksi->total_poin ?? 0, 0, ',', '.') }} poin</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Info Warga -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Informasi Warga</h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <h5>{{ $transaksi->warga->name ?? 'N/A' }}</h5>
                </div>
                
                <table class="table table-borderless">
                    <tr>
                        <th width="40%">Email</th>
                        <td>{{ $transaksi->warga->email ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Telepon</th>
                        <td>{{ $transaksi->warga->phone ?: '-' }}</td>
                    </tr>
                    <tr>
                        <th>Alamat</th>
                        <td>{{ $transaksi->warga->address ?: '-' }}</td>
                    </tr>
                    <tr>
                        <th>Total Poin</th>
                        <td>
                            <strong>{{ number_format($transaksi->warga->total_points ?? 0, 0, ',', '.') }}</strong>
                            <small class="text-muted d-block">Setelah transaksi ini</small>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- Aksi Cepat -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Aksi Cepat</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if(isset($transaksi->warga->id))
                    <a href="{{ route('petugas.transaksi.create') }}?warga_id={{ $transaksi->warga->id }}" 
                       class="btn btn-netra">
                        <i class="bi bi-plus-circle me-2"></i>Transaksi Baru dengan Warga Ini
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection