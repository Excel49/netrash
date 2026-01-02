@extends('layouts.app')

@section('title', 'Ajukan Penarikan Poin')

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="h3 mb-0">
        <i class="fas fa-hand-holding-usd"></i> Ajukan Penarikan Poin
    </h1>
    <a href="{{ route('warga.penarikan.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> Kembali
    </a>
</div>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-netra text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-money-bill-wave me-2"></i> Form Penarikan Poin
                </h5>
            </div>
            <div class="card-body">
                <!-- Poin Info Card -->
                <div class="alert alert-info mb-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="alert-heading mb-1">
                                <i class="fas fa-info-circle me-2"></i> Informasi Poin
                            </h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Poin Saat Ini:</strong></p>
                                    <h3 class="text-netra mb-0">{{ number_format($user->total_points, 0, ',', '.') }} pts</h3>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Nilai Tukar:</strong></p>
                                    <h4 class="text-success mb-0">100 poin = Rp 10.000</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <form action="{{ route('warga.penarikan.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="jumlah_poin" class="form-label fw-bold">
                            <i class="fas fa-coins me-1"></i> Jumlah Poin yang Ditarik
                        </label>
                        <div class="input-group">
                            <input type="number" 
                                   class="form-control @error('jumlah_poin') is-invalid @enderror" 
                                   id="jumlah_poin" 
                                   name="jumlah_poin" 
                                   value="{{ old('jumlah_poin') }}"
                                   min="100"
                                   step="10"
                                   placeholder="Minimal 100 poin"
                                   required>
                            <span class="input-group-text">poin</span>
                        </div>
                        <div class="form-text">
                            Minimal penarikan: 100 poin (Rp 10.000)
                        </div>
                        @error('jumlah_poin')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Rupiah Calculation -->
                    <div class="alert alert-secondary mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Estimasi Nilai Rupiah:</strong></p>
                                <h4 class="text-success mb-0" id="rupiahValue">Rp 0</h4>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Sisa Poin Setelah Penarikan:</strong></p>
                                <h4 class="text-primary mb-0" id="remainingPoints">0 pts</h4>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Poin Buttons -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            <i class="fas fa-bolt me-1"></i> Penarikan Cepat
                        </label>
                        <div class="d-flex flex-wrap gap-2">
                            @php
                                $quickPoints = [100, 500, 1000, 5000, 10000];
                            @endphp
                            @foreach($quickPoints as $points)
                                @if($points <= $user->total_points)
                                    <button type="button" 
                                            class="btn btn-outline-primary quick-point" 
                                            data-points="{{ $points }}">
                                        {{ number_format($points) }} pts
                                    </button>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <!-- Terms & Conditions -->
                    <div class="alert alert-warning">
                        <h6 class="alert-heading">
                            <i class="fas fa-exclamation-triangle me-2"></i> Syarat dan Ketentuan:
                        </h6>
                        <ul class="mb-0 small">
                            <li>Minimal penarikan: 100 poin (Rp 10.000)</li>
                            <li>Penarikan akan diproses maksimal 1x24 jam setelah approval admin</li>
                            <li>Poin akan dikembalikan jika penarikan ditolak</li>
                            <li>Hanya bisa mengajukan satu penarikan dalam satu waktu</li>
                            <li>Pastikan data rekening Anda sudah benar di profil</li>
                        </ul>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                        <a href="{{ route('warga.penarikan.index') }}" class="btn btn-outline-secondary me-md-2">
                            <i class="fas fa-times me-1"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-netra">
                            <i class="fas fa-paper-plane me-1"></i> Ajukan Penarikan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Info Panel -->
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="card-title mb-0">
                    <i class="fas fa-question-circle me-2"></i> Cara Penarikan
                </h6>
            </div>
            <div class="card-body">
                <ol class="mb-0 small">
                    <li class="mb-2">Masukkan jumlah poin yang ingin ditarik</li>
                    <li class="mb-2">Submit pengajuan</li>
                    <li class="mb-2">Tunggu approval dari admin (1-2 jam kerja)</li>
                    <li class="mb-2">Dana akan ditransfer setelah approval</li>
                    <li class="mb-2">Cek status di halaman penarikan</li>
                </ol>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-light">
                <h6 class="card-title mb-0">
                    <i class="fas fa-history me-2"></i> Penarikan Terakhir
                </h6>
            </div>
            <div class="card-body">
                @php
                    $lastWithdrawal = App\Models\PenarikanPoin::where('warga_id', auth()->id())
                        ->orderBy('created_at', 'desc')
                        ->first();
                @endphp
                @if($lastWithdrawal)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Status:</span>
                        <span class="badge bg-{{ $lastWithdrawal->status == 'completed' ? 'success' : ($lastWithdrawal->status == 'pending' ? 'warning' : ($lastWithdrawal->status == 'rejected' ? 'danger' : 'info')) }}">
                            {{ ucfirst($lastWithdrawal->status) }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Jumlah:</span>
                        <strong>{{ number_format($lastWithdrawal->jumlah_poin) }} pts</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Nilai:</span>
                        <strong class="text-success">Rp {{ number_format($lastWithdrawal->jumlah_rupiah) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Tanggal:</span>
                        <small>{{ $lastWithdrawal->created_at->format('d/m/Y H:i') }}</small>
                    </div>
                @else
                    <p class="text-muted mb-0 text-center">
                        <i class="fas fa-info-circle me-1"></i> Belum ada penarikan
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const poinInput = document.getElementById('jumlah_poin');
        const rupiahValue = document.getElementById('rupiahValue');
        const remainingPoints = document.getElementById('remainingPoints');
        const userPoints = {{ $user->total_points }};
        
        // Quick point buttons
        document.querySelectorAll('.quick-point').forEach(button => {
            button.addEventListener('click', function() {
                const points = parseInt(this.dataset.points);
                poinInput.value = points;
                calculateValues();
            });
        });
        
        // Calculate values on input change
        poinInput.addEventListener('input', calculateValues);
        
        // Initial calculation
        calculateValues();
        
        function calculateValues() {
            const points = parseInt(poinInput.value) || 0;
            
            // Calculate rupiah (100 poin = Rp 10,000)
            const rupiah = points * 100;
            
            // Format rupiah
            rupiahValue.textContent = 'Rp ' + rupiah.toLocaleString('id-ID');
            
            // Calculate remaining points
            const remaining = userPoints - points;
            remainingPoints.textContent = remaining.toLocaleString('id-ID') + ' pts';
            
            // Highlight if insufficient points
            if (remaining < 0) {
                remainingPoints.classList.remove('text-primary');
                remainingPoints.classList.add('text-danger');
                rupiahValue.classList.remove('text-success');
                rupiahValue.classList.add('text-danger');
            } else {
                remainingPoints.classList.remove('text-danger');
                remainingPoints.classList.add('text-primary');
                rupiahValue.classList.remove('text-danger');
                rupiahValue.classList.add('text-success');
            }
        }
        
        // Form validation
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            const points = parseInt(poinInput.value) || 0;
            
            if (points < 100) {
                e.preventDefault();
                alert('Minimal penarikan adalah 100 poin');
                poinInput.focus();
                return false;
            }
            
            if (points > userPoints) {
                e.preventDefault();
                alert('Poin tidak mencukupi. Poin Anda: ' + userPoints.toLocaleString('id-ID'));
                poinInput.focus();
                return false;
            }
            
            // Show loading
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.innerHTML = '<span class="loading-spinner"></span> Mengirim...';
                submitBtn.disabled = true;
            }
        });
    });
</script>
@endpush