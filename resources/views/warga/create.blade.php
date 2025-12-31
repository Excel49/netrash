@extends('layouts.app')

@section('title', 'Ajukan Penarikan Poin')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Ajukan Penarikan Poin</h2>
            <a href="{{ route('warga.penarikan.index') }}" class="btn btn-netra-outline">
                <i class="bi bi-arrow-left me-2"></i>Kembali
            </a>
        </div>
        <p class="text-muted">Tukarkan poin Anda menjadi uang tunai</p>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Form Pengajuan Penarikan</h6>
            </div>
            <div class="card-body">
                <!-- Info Poin -->
                <div class="alert alert-info mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="alert-heading mb-1">Poin Tersedia</h6>
                            <p class="mb-0">Anda memiliki <strong>{{ number_format($user->total_points, 0, ',', '.') }} poin</strong></p>
                        </div>
                        <div class="text-end">
                            <h4 class="mb-0">Rp {{ number_format($user->total_points * 100, 0, ',', '.') }}</h4>
                            <small class="text-muted">(100 poin = Rp 10.000)</small>
                        </div>
                    </div>
                </div>
                
                <!-- Form -->
                <form action="{{ route('warga.penarikan.store') }}" method="POST">
                    @csrf
                    
                    <!-- Input Jumlah Poin -->
                    <div class="mb-4">
                        <label for="jumlah_poin" class="form-label">Jumlah Poin yang Ditarik</label>
                        <div class="input-group">
                            <input type="number" 
                                   class="form-control @error('jumlah_poin') is-invalid @enderror" 
                                   id="jumlah_poin" 
                                   name="jumlah_poin" 
                                   value="{{ old('jumlah_poin') }}"
                                   min="100" 
                                   step="100" 
                                   required>
                            <span class="input-group-text">poin</span>
                        </div>
                        @error('jumlah_poin')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Minimal 100 poin (Rp 10.000)</small>
                    </div>
                    
                    <!-- Kalkulator -->
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h6 class="mb-3">Perhitungan</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1">Jumlah Poin:</p>
                                    <h5 id="display-poin">0 poin</h5>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1">Nilai Rupiah:</p>
                                    <h5 id="display-rupiah">Rp 0</h5>
                                </div>
                            </div>
                            <div class="mt-3">
                                <small class="text-muted">* Setelah approval, dana akan ditransfer ke rekening Anda</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Alasan -->
                    <div class="mb-4">
                        <label for="alasan_penarikan" class="form-label">Alasan Penarikan</label>
                        <textarea class="form-control @error('alasan_penarikan') is-invalid @enderror" 
                                  id="alasan_penarikan" 
                                  name="alasan_penarikan" 
                                  rows="3" 
                                  placeholder="Contoh: Untuk kebutuhan sekolah anak, biaya kesehatan, dll." 
                                  required>{{ old('alasan_penarikan') }}</textarea>
                        @error('alasan_penarikan')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Berikan alasan yang jelas untuk mempercepat proses approval</small>
                    </div>
                    
                    <!-- Syarat dan Ketentuan -->
                    <div class="form-check mb-4">
                        <input class="form-check-input @error('terms') is-invalid @enderror" 
                               type="checkbox" 
                               id="terms" 
                               name="terms" 
                               required>
                        <label class="form-check-label" for="terms">
                            Saya menyetujui syarat dan ketentuan penarikan poin:
                        </label>
                        @error('terms')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <ul class="small text-muted mt-2">
                            <li>Proses approval membutuhkan waktu 1-3 hari kerja</li>
                            <li>Poin akan dipotong sementara selama proses approval</li>
                            <li>Jika penarikan ditolak, poin akan dikembalikan</li>
                            <li>Transfer dana dilakukan setelah penarikan disetujui</li>
                        </ul>
                    </div>
                    
                    <!-- Tombol Submit -->
                    <div class="d-grid">
                        <button type="submit" class="btn btn-netra btn-lg">
                            <i class="bi bi-send-check me-2"></i>Ajukan Penarikan
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Informasi Penting -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">Informasi Penting</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary bg-opacity-10 p-2 rounded me-3">
                                <i class="bi bi-clock-history text-primary"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">Waktu Proses</h6>
                                <p class="text-muted mb-0">1-3 hari kerja</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-success bg-opacity-10 p-2 rounded me-3">
                                <i class="bi bi-cash-coin text-success"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">Nilai Tukar</h6>
                                <p class="text-muted mb-0">100 poin = Rp 10.000</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const poinInput = document.getElementById('jumlah_poin');
    const displayPoin = document.getElementById('display-poin');
    const displayRupiah = document.getElementById('display-rupiah');
    
    // Format number
    function formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }
    
    // Update display
    function updateDisplay() {
        const poin = parseInt(poinInput.value) || 0;
        const rupiah = poin * 100;
        
        displayPoin.textContent = formatNumber(poin) + ' poin';
        displayRupiah.textContent = 'Rp ' + formatNumber(rupiah);
    }
    
    // Event listeners
    poinInput.addEventListener('input', updateDisplay);
    
    // Set initial value (minimal 100)
    if (!poinInput.value) {
        poinInput.value = 100;
    }
    
    // Initial update
    updateDisplay();
    
    // Form validation
    const form = document.querySelector('form');