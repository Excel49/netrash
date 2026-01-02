@extends('layouts.app')

@section('title', 'Kategori Sampah')

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="h3 mb-0">
        <i class="fas fa-tags"></i> Kategori Sampah
    </h1>
    <div>
        <a href="{{ route('warga.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <!-- Info Card -->
    <div class="col-md-12 mb-4">
        <div class="alert alert-info">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="fas fa-info-circle fa-2x"></i>
                </div>
                <div class="flex-grow-1">
                    <h6 class="alert-heading mb-1">Informasi Kategori Sampah</h6>
                    <p class="mb-0">
                        Berikut adalah daftar kategori sampah yang dapat ditukarkan menjadi poin. 
                        Setiap kategori memiliki harga per kilogram yang berbeda-beda.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Kategori Cards -->
    @forelse($kategori as $item)
    <div class="col-md-4 mb-4">
        <div class="card h-100 shadow-sm border-{{ $item->status ? 'success' : 'secondary' }}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h5 class="card-title mb-1">
                            <i class="fas fa-tag me-2"></i>{{ $item->nama_kategori }}
                        </h5>
                        @if(!$item->status)
                            <span class="badge bg-secondary">Tidak Aktif</span>
                        @endif
                    </div>
                    <span class="badge bg-netra p-2">
                        <i class="fas fa-coins me-1"></i>{{ number_format($item->harga_per_kg / 100) }} pts/kg
                    </span>
                </div>
                
                <div class="mb-3">
                    <p class="card-text text-muted small">
                        <i class="fas fa-money-bill-wave me-1"></i>
                        <strong>Harga:</strong> Rp {{ number_format($item->harga_per_kg, 0, ',', '.') }}/kg
                    </p>
                    <p class="card-text text-muted small">
                        <i class="fas fa-exchange-alt me-1"></i>
                        <strong>Nilai Poin:</strong> 1 kg = {{ number_format($item->harga_per_kg / 100, 2) }} poin
                    </p>
                </div>

                <div class="mt-3 pt-3 border-top">
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted d-block">Minimal</small>
                            <strong>{{ $item->minimum_kg }} kg</strong>
                        </div>
                        <div class="col-6 text-end">
                            <small class="text-muted d-block">Poin per kg</small>
                            <strong>{{ number_format($item->harga_per_kg / 100) }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Contoh Perhitungan -->
                <div class="mt-3 pt-3 border-top">
                    <small class="text-muted d-block mb-2">
                        <i class="fas fa-calculator me-1"></i> Contoh:
                    </small>
                    <div class="row small">
                        <div class="col-6">
                            1 kg
                        </div>
                        <div class="col-6 text-end">
                            = {{ number_format($item->harga_per_kg / 100) }} poin
                        </div>
                        <div class="col-6">
                            5 kg
                        </div>
                        <div class="col-6 text-end">
                            = {{ number_format(5 * $item->harga_per_kg / 100) }} poin
                        </div>
                        <div class="col-6">
                            10 kg
                        </div>
                        <div class="col-6 text-end">
                            = {{ number_format(10 * $item->harga_per_kg / 100) }} poin
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-top-0">
                <button class="btn btn-outline-netra btn-sm w-100" 
                        onclick="calculatePoints('{{ $item->nama_kategori }}', {{ $item->harga_per_kg / 100 }})">
                    <i class="fas fa-calculator me-1"></i> Hitung Poin
                </button>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="text-center py-5">
            <i class="fas fa-tags fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Belum ada kategori sampah</h5>
            <p class="text-muted">Silakan hubungi admin untuk informasi lebih lanjut</p>
        </div>
    </div>
    @endforelse
</div>

<!-- Calculator Modal -->
<div class="modal fade" id="calculatorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Kalkulator Poin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="kategoriName" class="form-label">Kategori</label>
                    <input type="text" class="form-control" id="kategoriName" readonly>
                </div>
                <div class="mb-3">
                    <label for="pointPerKg" class="form-label">Poin per kg</label>
                    <input type="text" class="form-control" id="pointPerKg" readonly>
                </div>
                <div class="mb-3">
                    <label for="weight" class="form-label">Berat (kg)</label>
                    <div class="input-group">
                        <input type="number" 
                               class="form-control" 
                               id="weight" 
                               min="0.1" 
                               step="0.1"
                               value="1">
                        <span class="input-group-text">kg</span>
                    </div>
                    <div class="form-text">Masukkan berat sampah dalam kilogram</div>
                </div>
                <div class="alert alert-info">
                    <h6 class="alert-heading mb-2">Hasil Perhitungan:</h6>
                    <div class="d-flex justify-content-between">
                        <span>Total Poin:</span>
                        <strong id="totalPoints" class="text-netra">0</strong>
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                        <span>Nilai Rupiah:</span>
                        <strong id="totalRupiah" class="text-success">Rp 0</strong>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-netra" onclick="resetCalculator()">
                    <i class="fas fa-redo me-1"></i> Reset
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Summary Card -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="card-title mb-0">
                    <i class="fas fa-chart-pie me-2"></i> Ringkasan Kategori
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="text-center">
                            <h6 class="text-muted">Total Kategori</h6>
                            <h3 class="text-netra">{{ $kategori->count() }}</h3>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h6 class="text-muted">Kategori Aktif</h6>
                            <h3 class="text-success">{{ $kategori->where('status', true)->count() }}</h3>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h6 class="text-muted">Harga Tertinggi</h6>
                            <h4 class="text-success">
                                Rp {{ number_format($kategori->max('harga_per_kg') ?? 0, 0, ',', '.') }}/kg
                            </h4>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h6 class="text-muted">Harga Terendah</h6>
                            <h4 class="text-success">
                                Rp {{ number_format($kategori->min('harga_per_kg') ?? 0, 0, ',', '.') }}/kg
                            </h4>
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
    function calculatePoints(kategori, pointPerKg) {
        // Set modal data
        document.getElementById('modalTitle').textContent = 'Kalkulator Poin - ' + kategori;
        document.getElementById('kategoriName').value = kategori;
        document.getElementById('pointPerKg').value = pointPerKg.toLocaleString('id-ID');
        
        // Calculate initial values
        updateCalculation(pointPerKg);
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('calculatorModal'));
        modal.show();
    }
    
    function updateCalculation(pointPerKg) {
        const weight = parseFloat(document.getElementById('weight').value) || 0;
        const totalPoints = weight * pointPerKg;
        const totalRupiah = totalPoints * 100; // 1 poin = Rp 100
        
        document.getElementById('totalPoints').textContent = totalPoints.toLocaleString('id-ID', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        document.getElementById('totalRupiah').textContent = 'Rp ' + totalRupiah.toLocaleString('id-ID');
    }
    
    function resetCalculator() {
        document.getElementById('weight').value = 1;
        const pointPerKg = parseFloat(document.getElementById('pointPerKg').value.replace(/\./g, '')) || 0;
        updateCalculation(pointPerKg);
    }
    
    // Initialize event listeners
    document.addEventListener('DOMContentLoaded', function() {
        const weightInput = document.getElementById('weight');
        if (weightInput) {
            weightInput.addEventListener('input', function() {
                const pointPerKg = parseFloat(document.getElementById('pointPerKg').value.replace(/\./g, '')) || 0;
                updateCalculation(pointPerKg);
            });
        }
        
        // Auto-show modal if there's a calculation request
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('calculate')) {
            const kategori = urlParams.get('kategori');
            const pointPerKg = parseFloat(urlParams.get('points'));
            if (kategori && pointPerKg) {
                calculatePoints(kategori, pointPerKg);
            }
        }
    });
</script>
@endpush