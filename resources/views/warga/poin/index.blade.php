@extends('layouts.app')

@section('title', 'Poin Saya')

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="h3 mb-0">
        <i class="fas fa-coins"></i> Poin Saya
    </h1>
    <div>
        <a href="{{ route('warga.dashboard') }}" class="btn btn-secondary me-2">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
        <a href="{{ route('warga.penarikan.create') }}" class="btn btn-netra">
            <i class="fas fa-hand-holding-usd me-1"></i> Tarik Poin
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <!-- Main Points Card -->
    <div class="col-md-4">
        <div class="card bg-gradient-primary text-white mb-4">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-coins fa-3x opacity-50"></i>
                </div>
                <h6 class="card-title mb-2">Total Poin</h6>
                <h1 class="display-4 mb-3" id="totalPoints">
                    {{ number_format(auth()->user()->total_points, 0, ',', '.') }}
                </h1>
                <small class="opacity-75">pts</small>
                <div class="mt-4">
                    <div class="row">
                        <div class="col-6">
                            <small class="d-block">Nilai Rupiah</small>
                            <strong>Rp {{ number_format(auth()->user()->total_points * 100, 0, ',', '.') }}</strong>
                        </div>
                        <div class="col-6">
                            <small class="d-block">Kurs</small>
                            <strong>100 pts = Rp 10.000</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-md-8">
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-history fa-2x text-netra"></i>
                        </div>
                        <h6 class="card-title">Riwayat Poin</h6>
                        <p class="card-text small text-muted">
                            Lihat semua riwayat transaksi dan poin yang Anda dapatkan
                        </p>
                        <a href="{{ route('warga.poin.history') }}" class="btn btn-outline-netra mt-2">
                            Lihat Riwayat
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-hand-holding-usd fa-2x text-success"></i>
                        </div>
                        <h6 class="card-title">Tarik Poin</h6>
                        <p class="card-text small text-muted">
                            Tukarkan poin menjadi uang tunai dengan mudah
                        </p>
                        <a href="{{ route('warga.penarikan.create') }}" class="btn btn-outline-success mt-2">
                            Tarik Sekarang
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-tags fa-2x text-info"></i>
                        </div>
                        <h6 class="card-title">Kategori Sampah</h6>
                        <p class="card-text small text-muted">
                            Lihat harga dan kategori sampah yang bisa ditukar poin
                        </p>
                        <a href="{{ route('warga.kategori.index') }}" class="btn btn-outline-info mt-2">
                            Lihat Kategori
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-qrcode fa-2x text-warning"></i>
                        </div>
                        <h6 class="card-title">QR Code Saya</h6>
                        <p class="card-text small text-muted">
                            Tunjukkan QR code untuk transaksi penukaran sampah
                        </p>
                        <a href="{{ route('warga.qrcode.index') }}" class="btn btn-outline-warning mt-2">
                            Lihat QR Code
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Transactions -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0">
                    <i class="fas fa-exchange-alt me-2"></i> Transaksi Terakhir
                </h6>
                <a href="{{ route('warga.transaksi.index') }}" class="btn btn-sm btn-outline-netra">
                    Lihat Semua
                </a>
            </div>
            <div class="card-body">
                @php
                    $recentTransaksi = App\Models\Transaksi::where('warga_id', auth()->id())
                        ->orderBy('created_at', 'desc')
                        ->limit(5)
                        ->get();
                @endphp
                
                @if($recentTransaksi->isEmpty())
                    <div class="text-center py-3">
                        <p class="text-muted mb-0">Belum ada transaksi</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Berat</th>
                                    <th>Poin</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentTransaksi as $item)
                                <tr>
                                    <td>
                                        <small class="text-muted d-block">{{ $item->created_at->format('d/m/Y') }}</small>
                                        <small class="text-muted">{{ $item->created_at->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ number_format($item->total_berat, 1) }} kg</strong>
                                    </td>
                                    <td>
                                        <strong class="text-netra">+{{ number_format($item->total_poin) }}</strong>
                                        <small class="text-muted d-block">pts</small>
                                    </td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'pending' => 'warning',
                                                'completed' => 'success',
                                                'cancelled' => 'danger'
                                            ];
                                            $statusLabels = [
                                                'pending' => 'Menunggu',
                                                'completed' => 'Selesai',
                                                'cancelled' => 'Batal'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$item->status] ?? 'secondary' }}">
                                            {{ $statusLabels[$item->status] ?? $item->status }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Points Calculator -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="card-title mb-0">
                    <i class="fas fa-calculator me-2"></i> Kalkulator Poin
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
                                       value="100"
                                       min="1">
                                <span class="input-group-text">pts</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="rupiahValue" class="form-label">Nilai Rupiah</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" 
                                       class="form-control" 
                                       id="rupiahValue" 
                                       value="10.000"
                                       readonly>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="alert alert-info">
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted d-block">Contoh:</small>
                            <div class="d-flex justify-content-between">
                                <span>500 poin</span>
                                <span>= Rp 50.000</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>1000 poin</span>
                                <span>= Rp 100.000</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Kurs:</small>
                            <p class="mb-0">1 poin = Rp 100</p>
                            <p class="mb-0">100 poin = Rp 10.000</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const poinAmountInput = document.getElementById('poinAmount');
        const rupiahValueInput = document.getElementById('rupiahValue');
        
        function updateRupiahValue() {
            const points = parseInt(poinAmountInput.value) || 0;
            const rupiah = points * 100;
            rupiahValueInput.value = rupiah.toLocaleString('id-ID');
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
                        const currentPoints = parseInt(document.getElementById('totalPoints').textContent.replace(/\./g, ''));
                        if (data.total_points !== currentPoints) {
                            document.getElementById('totalPoints').textContent = data.formatted_points;
                            
                            // Show notification if points increased
                            if (data.total_points > currentPoints) {
                                const diff = data.total_points - currentPoints;
                                showToast(`+${diff.toLocaleString('id-ID')} poin diterima!`, 'success');
                            }
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