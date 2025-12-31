@extends('layouts.app')

@section('title', 'Scan QR Code')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Scan QR Code Warga</h2>
            <a href="{{ route('petugas.dashboard') }}" class="btn btn-netra-outline">
                <i class="bi bi-arrow-left me-2"></i>Kembali
            </a>
        </div>
        <p class="text-muted">Scan QR Code warga untuk memulai transaksi</p>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Scanner</h6>
            </div>
            <div class="card-body">
                <div id="scanner-container">
                    <!-- QR Scanner akan dimuat di sini -->
                    <div id="qr-reader" style="width: 100%; max-width: 600px; margin: 0 auto;"></div>
                </div>
                
                <div id="scanner-result" class="mt-4 d-none">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0">Data Warga Ditemukan</h6>
                        </div>
                        <div class="card-body">
                            <div id="result-content"></div>
                            <div class="mt-3 text-center">
                                <a href="#" id="btn-start-transaction" class="btn btn-netra btn-lg">
                                    <i class="bi bi-play-circle me-2"></i>Mulai Transaksi
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div id="scanner-error" class="alert alert-danger mt-4 d-none">
                    <h6 class="alert-heading">Error!</h6>
                    <p id="error-message"></p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Manual Input -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Input Manual</h6>
            </div>
            <div class="card-body">
                <p class="text-muted">Jika QR Code rusak/tidak terbaca</p>
                <form id="manual-form" action="{{ route('petugas.scan.process') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="user_id" class="form-label">ID Warga</label>
                        <input type="text" class="form-control" id="user_id" name="user_id" 
                               placeholder="Masukkan ID warga" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Warga</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               placeholder="atau masukkan email" required>
                    </div>
                    <button type="submit" class="btn btn-netra w-100">
                        <i class="bi bi-search me-2"></i>Cari Warga
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Petunjuk -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">Petunjuk</h6>
            </div>
            <div class="card-body">
                <ol class="mb-0">
                    <li class="mb-2">Izinkan akses kamera</li>
                    <li class="mb-2">Arahkan kamera ke QR Code warga</li>
                    <li class="mb-2">Tunggu hingga terdeteksi otomatis</li>
                    <li class="mb-2">Klik "Mulai Transaksi" untuk lanjut</li>
                </ol>
            </div>
        </div>
        
        <!-- Riwayat Scan -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">Riwayat Scan Hari Ini</h6>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <div class="list-group-item text-center py-3 text-muted">
                        Belum ada scan hari ini
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<!-- QR Code Scanner Library -->
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let scannedUserId = null;
    
    // Initialize QR Scanner
    const html5QrCode = new Html5Qrcode("qr-reader");
    
    const qrCodeSuccessCallback = (decodedText, decodedResult) => {
        console.log("QR Code detected:", decodedText);
        
        try {
            // Parse QR data (dalam format JSON)
            const qrData = JSON.parse(decodedText);
            
            // Validate required fields
            if (!qrData.user_id || !qrData.name) {
                throw new Error("QR Code tidak valid");
            }
            
            // Show loading
            document.getElementById('scanner-result').classList.add('d-none');
            document.getElementById('scanner-error').classList.add('d-none');
            
            // Fetch user data from server
            fetch('/api/scan', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    qr_data: decodedText,
                    user_id: qrData.user_id
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Stop scanner
                    html5QrCode.stop().then(() => {
                        console.log("Scanner stopped");
                    }).catch(err => {
                        console.log("Error stopping scanner:", err);
                    });
                    
                    // Display user data
                    document.getElementById('result-content').innerHTML = `
                        <div class="row">
                            <div class="col-md-4 text-center">
                                <div class="mb-3">
                                    <div style="width: 80px; height: 80px; background-color: #2E8B57; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; color: white; font-size: 24px;">
                                        ${data.user.name.charAt(0)}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h5>${data.user.name}</h5>
                                <p class="mb-1"><strong>Email:</strong> ${data.user.email}</p>
                                <p class="mb-1"><strong>Poin:</strong> ${data.user.total_points.toLocaleString('id-ID')}</p>
                                <p class="mb-0"><strong>ID:</strong> ${data.user.id}</p>
                            </div>
                        </div>
                    `;
                    
                    // Set button action
                    scannedUserId = data.user.id;
                    document.getElementById('btn-start-transaction').href = `/petugas/transaksi/create?warga_id=${data.user.id}`;
                    
                    // Show result
                    document.getElementById('scanner-result').classList.remove('d-none');
                } else {
                    showError(data.error || 'Terjadi kesalahan');
                }
            })
            .catch(error => {
                console.error("Error:", error);
                showError('Gagal memproses data warga');
            });
            
        } catch (error) {
            console.error("Error parsing QR:", error);
            showError('QR Code tidak valid atau rusak');
        }
    };
    
    function showError(message) {
        document.getElementById('error-message').textContent = message;
        document.getElementById('scanner-error').classList.remove('d-none');
    }
    
    // Start scanner
    const config = {
        fps: 10,
        qrbox: { width: 250, height: 250 }
    };
    
    html5QrCode.start(
        { facingMode: "environment" },
        config,
        qrCodeSuccessCallback
    ).catch(err => {
        console.error("Failed to start scanner:", err);
        showError('Tidak dapat mengakses kamera. Pastikan izin kamera telah diberikan.');
    });
    
    // Manual form submission
    document.getElementById('manual-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch(this.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Redirect to transaction page
                window.location.href = `/petugas/transaksi/create?warga_id=${data.user.id}`;
            } else {
                showError(data.error || 'Warga tidak ditemukan');
            }
        })
        .catch(error => {
            console.error("Error:", error);
            showError('Terjadi kesalahan');
        });
    });
});
</script>

<style>
#qr-reader video {
    border-radius: 10px;
    border: 2px solid #2E8B57;
}

#qr-reader__scan_region {
    text-align: center;
}

#qr-reader__dashboard_section {
    margin-top: 10px;
}
</style>
@endpush
@endsection