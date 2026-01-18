@extends('layouts.app')

@section('title', 'Poin Saya')

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="h3 mb-0">
        <i class="fas fa-coins"></i> Kelola Poin
    </h1>
    <div>
        <a href="{{ route('warga.penukaran.history') }}" class="btn btn-info me-2">
            <i class="fas fa-history me-1"></i> Riwayat Penukaran
        </a>
        <a href="{{ route('warga.barang.index') }}" class="btn btn-netra me-2">
            <i class="fas fa-shopping-cart me-1"></i> Tukar Barang
        </a>
        <a href="{{ route('warga.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <!-- Stats Cards -->
        <div class="row">
            <div class="col-md-3 mb-3">
                <div class="card bg-gradient-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase mb-0">Poin Saat Ini</h6>
                                <h1 class="mb-0 display-5">{{ number_format($totalPoin) }}</h1>
                                <small class="opacity-75">siap ditukar</small>
                            </div>
                            <div>
                                <i class="fas fa-wallet fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card bg-gradient-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase mb-0">Total Masuk</h6>
                                <h1 class="mb-0 display-5">+{{ number_format($totalPoinMasuk) }}</h1>
                                <small class="opacity-75">dari sampah</small>
                            </div>
                            <div>
                                <i class="fas fa-arrow-down fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card bg-gradient-danger text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase mb-0">Total Keluar</h6>
                                <h1 class="mb-0 display-5">-{{ number_format($totalPoinKeluar) }}</h1>
                                <small class="opacity-75">untuk barang</small>
                            </div>
                            <div>
                                <i class="fas fa-arrow-up fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card bg-gradient-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase mb-0">Total Penukaran</h6>
                                <h1 class="mb-0 display-5">{{ $totalPenukaran }}</h1>
                                <small class="opacity-75">kali menukar</small>
                            </div>
                            <div>
                                <i class="fas fa-exchange-alt fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-bolt me-2"></i>Aksi Cepat
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <a href="{{ route('warga.barang.index') }}" class="btn btn-outline-success w-100 text-start h-100">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-success text-white rounded p-3 me-3">
                                            <i class="fas fa-shopping-cart fa-2x"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">Tukar Barang</h6>
                                            <p class="text-muted mb-0">Tukar poin dengan barang</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            
                            <div class="col-md-3 mb-3">
                                <a href="{{ route('warga.transaksi.index') }}" class="btn btn-outline-primary w-100 text-start h-100">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary text-white rounded p-3 me-3">
                                            <i class="fas fa-receipt fa-2x"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">Riwayat Sampah</h6>
                                            <p class="text-muted mb-0">Lihat transaksi setoran</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            
                            <div class="col-md-3 mb-3">
                                <a href="{{ route('warga.penukaran.history') }}" class="btn btn-outline-info w-100 text-start h-100">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-info text-white rounded p-3 me-3">
                                            <i class="fas fa-history fa-2x"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">Riwayat Penukaran</h6>
                                            <p class="text-muted mb-0">Lihat barang ditukar</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            
                            <div class="col-md-3 mb-3">
                                <a href="{{ route('warga.kategori.index') }}" class="btn btn-outline-warning w-100 text-start h-100">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-warning text-white rounded p-3 me-3">
                                            <i class="fas fa-tags fa-2x"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">Kategori Sampah</h6>
                                            <p class="text-muted mb-0">Harga & kategori</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="fas fa-arrow-down text-success me-2"></i>Transaksi Terakhir
                        </h6>
                        <a href="{{ route('warga.transaksi.index') }}" class="btn btn-sm btn-outline-primary">
                            Lihat Semua
                        </a>
                    </div>
                    <div class="card-body">
                        @if($recentTransaksi->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentTransaksi as $trx)
                            <div class="list-group-item border-0 px-0 py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1 text-truncate" style="max-width: 200px;">
                                            {{ $trx->kode_transaksi }}
                                        </h6>
                                        <small class="text-muted">
                                            {{ $trx->created_at->format('d/m/Y H:i') }}
                                            @if($trx->petugas)
                                            • {{ $trx->petugas->name }}
                                            @endif
                                        </small>
                                    </div>
                                    <div class="text-end">
                                        <span class="text-success fw-bold">
                                            +{{ number_format($trx->total_poin, 0, ',', '.') }}
                                        </span>
                                        <div>
                                            <small class="text-muted">
                                                {{ number_format($trx->total_berat, 1) }} kg
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-receipt fa-3x mb-3"></i>
                            <p>Belum ada transaksi</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="fas fa-arrow-up text-danger me-2"></i>Penukaran Terakhir
                        </h6>
                        <a href="{{ route('warga.penukaran.history') }}" class="btn btn-sm btn-outline-info">
                            Lihat Semua
                        </a>
                    </div>
                    <div class="card-body">
                        @if($recentPenukaran->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentPenukaran as $trx)
                            <div class="list-group-item border-0 px-0 py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1 text-truncate" style="max-width: 200px;">
                                            {{ $trx->kode_transaksi }}
                                        </h6>
                                        <small class="text-muted">
                                            {{ $trx->created_at->format('d/m/Y H:i') }}
                                        </small>
                                        <div>
                                            <small class="text-truncate d-block" style="max-width: 200px;">
                                                {{ Str::limit($trx->catatan, 35) }}
                                            </small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <span class="text-danger fw-bold">
                                            -{{ number_format(abs($trx->total_poin), 0, ',', '.') }}
                                        </span>
                                        <div>
                                            <span class="badge bg-info">Barang</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-basket-shopping fa-3x mb-3"></i>
                            <p>Belum ada penukaran</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Features -->
        <div class="row mt-4">
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-qrcode fa-2x text-netra"></i>
                        </div>
                        <h6 class="card-title">QR Code Saya</h6>
                        <p class="card-text small text-muted">
                            Tunjukkan QR code untuk transaksi penukaran sampah
                        </p>
                        <a href="{{ route('warga.qrcode.index') }}" class="btn btn-outline-netra mt-2">
                            Lihat QR Code
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-chart-line fa-2x text-primary"></i>
                        </div>
                        <h6 class="card-title">Statistik Lengkap</h6>
                        <p class="card-text small text-muted">
                            Lihat grafik perkembangan poin dan transaksi Anda
                        </p>
                        <a href="{{ route('warga.poin.history') }}" class="btn btn-outline-primary mt-2">
                            Lihat Statistik
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-calculator fa-2x text-success"></i>
                        </div>
                        <h6 class="card-title">Kalkulator</h6>
                        <p class="card-text small text-muted">
                            Hitung perkiraan poin dari sampah Anda
                        </p>
                        <a href="{{ route('warga.kategori.calculator') }}" class="btn btn-outline-success mt-2">
                            Hitung Poin
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Points Calculator -->
        <div class="card mt-4">
            <div class="card-header bg-light">
                <h6 class="card-title mb-0">
                    <i class="fas fa-calculator me-2"></i> Kalkulator Konversi Poin
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="poinAmount" class="form-label">Jumlah Poin</label>
                            <div class="input-group">
                                <input type="number" 
                                       class="form-control" 
                                       id="poinAmount" 
                                       value="{{ $totalPoin }}"
                                       min="1"
                                       max="{{ $totalPoin }}">
                                <span class="input-group-text">pts</span>
                            </div>
                            <small class="text-muted">Maksimal: {{ number_format($totalPoin) }} poin</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="rupiahValue" class="form-label">Nilai Konversi</label>
                            <div class="input-group">
                                <span class="input-group-text">≈ Rp</span>
                                <input type="text" 
                                       class="form-control" 
                                       id="rupiahValue" 
                                       readonly>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Barang yang bisa ditukar -->
                <div id="availableItems" class="mt-3">
                    <h6 class="mb-3">Barang yang dapat ditukar:</h6>
                    <div class="row" id="itemsList">
                        <!-- Items will be loaded here -->
                    </div>
                </div>
                
                <div class="alert alert-info mt-3">
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted d-block">Contoh Konversi:</small>
                            <div class="d-flex justify-content-between">
                                <span>500 poin</span>
                                <span>≈ Rp 50.000</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>1000 poin</span>
                                <span>≈ Rp 100.000</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>{{ number_format($totalPoin) }} poin</span>
                                <span>≈ Rp {{ number_format($totalPoin * 100, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Catatan:</small>
                            <p class="mb-0">Poin hanya untuk insentif partisipasi</p>
                            <p class="mb-0">1 poin ≈ Rp 100 (nilai konversi)</p>
                            <p class="mb-0">Poin tidak dapat dikembalikan ke uang</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card {
        transition: transform 0.2s;
    }
    .card:hover {
        transform: translateY(-2px);
    }
    .progress {
        height: 8px;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const poinAmountInput = document.getElementById('poinAmount');
        const rupiahValueInput = document.getElementById('rupiahValue');
        const itemsList = document.getElementById('itemsList');
        
        // Update konversi poin ke rupiah
        function updateRupiahValue() {
            const points = parseInt(poinAmountInput.value) || 0;
            const rupiah = points * 100;
            rupiahValueInput.value = rupiah.toLocaleString('id-ID');
            
            // Load barang yang bisa ditukar
            loadAvailableItems(points);
        }
        
        // Load barang yang bisa ditukar berdasarkan poin
        function loadAvailableItems(points) {
            fetch(`/api/barang/available?points=${points}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.items.length > 0) {
                        itemsList.innerHTML = '';
                        data.items.forEach(item => {
                            const itemHtml = `
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body text-center">
                                            <h6 class="card-title">${item.nama_barang}</h6>
                                            <p class="text-muted small mb-2">${item.harga_poin} poin</p>
                                            <p class="small mb-2">Stok: ${item.stok}</p>
                                            <a href="/warga/penukaran/create?barang_id=${item.id}" 
                                               class="btn btn-sm btn-success">
                                                Tukar Sekarang
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            `;
                            itemsList.innerHTML += itemHtml;
                        });
                        document.getElementById('availableItems').style.display = 'block';
                    } else {
                        itemsList.innerHTML = `
                            <div class="col-12 text-center py-3">
                                <p class="text-muted">Tidak ada barang yang dapat ditukar dengan ${points} poin</p>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error loading items:', error);
                    itemsList.innerHTML = `
                        <div class="col-12 text-center py-3">
                            <p class="text-muted">Gagal memuat data barang</p>
                        </div>
                    `;
                });
        }
        
        poinAmountInput.addEventListener('input', updateRupiahValue);
        
        // Initial calculation
        updateRupiahValue();
        
        // Auto-refresh points every 30 seconds
        setInterval(function() {
            fetch('/api/poin/balance')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const currentPoints = parseInt('{{ $totalPoin }}');
                        if (data.total_points !== currentPoints) {
                            // Show notification if points changed
                            const diff = data.total_points - currentPoints;
                            if (diff > 0) {
                                showToast(`+${diff.toLocaleString('id-ID')} poin diterima!`, 'success');
                            } else if (diff < 0) {
                                showToast(`${Math.abs(diff).toLocaleString('id-ID')} poin digunakan!`, 'warning');
                            }
                            
                            // Refresh page after 2 seconds to show updated data
                            setTimeout(() => {
                                location.reload();
                            }, 2000);
                        }
                    }
                })
                .catch(error => console.error('Error fetching points:', error));
        }, 30000);
    });
    
    function showToast(message, type = 'success') {
        const toastHtml = `
            <div class="toast align-items-center text-bg-${type} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;
        
        const toastContainer = document.getElementById('toast-container') || createToastContainer();
        toastContainer.insertAdjacentHTML('beforeend', toastHtml);
        
        const toastElement = toastContainer.lastElementChild;
        const toast = new bootstrap.Toast(toastElement);
        toast.show();
        
        toastElement.addEventListener('hidden.bs.toast', function() {
            this.remove();
        });
    }
    
    function createToastContainer() {
        const container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        container.style.zIndex = '1060';
        document.body.appendChild(container);
        return container;
    }
</script>
@endpush