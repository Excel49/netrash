@extends('layouts.app')

@section('title', 'Scan QR Code')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Scan QR Code Warga</h2>
            <a href="{{ route('petugas.dashboard') }}" class="btn btn-netra-outline">
                <i class="bi bi-arrow-left me-2"></i>Kembali ke Dashboard
            </a>
        </div>
        <p class="text-muted">Arahkan kamera ke QR Code warga untuk memulai transaksi</p>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Scanner QR Code</h6>
            </div>
            <div class="card-body text-center">
                <!-- Scanner Container -->
                <div id="scanner-container" class="mb-4">
                    <div id="qr-reader" style="width: 100%; max-width: 600px; margin: 0 auto;"></div>
                </div>
                
                <!-- Petunjuk Sederhana -->
                <div class="alert alert-info">
                    <h6><i class="bi bi-info-circle me-2"></i>Petunjuk Penggunaan:</h6>
                    <ol class="mb-0 ps-3">
                        <li>Izinkan akses kamera ketika diminta</li>
                        <li>Arahkan kamera ke QR Code warga</li>
                        <li>Tunggu hingga scanner mendeteksi QR Code</li>
                        <li>Data warga akan muncul otomatis</li>
                    </ol>
                </div>
                
                <!-- Result Container -->
                <div id="scanner-result" class="mt-4 d-none">
                    <div class="card border-success">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="bi bi-check-circle me-2"></i>Data Warga Ditemukan</h6>
                        </div>
                        <div class="card-body">
                            <div id="result-content"></div>
                            <div class="mt-4 text-center">
                                <a href="#" id="btn-start-transaction" class="btn btn-netra btn-lg">
                                    <i class="bi bi-play-circle me-2"></i>Mulai Transaksi
                                </a>
                                <button type="button" id="btn-rescan" class="btn btn-outline-secondary btn-lg ms-2">
                                    <i class="bi bi-arrow-repeat me-2"></i>Scan Lagi
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Error Container -->
                <div id="scanner-error" class="alert alert-danger mt-4 d-none">
                    <h6 class="alert-heading"><i class="bi bi-exclamation-triangle me-2"></i>Error!</h6>
                    <p id="error-message"></p>
                    <button type="button" id="btn-retry" class="btn btn-outline-danger btn-sm">
                        <i class="bi bi-arrow-clockwise me-1"></i>Coba Lagi
                    </button>
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
    let html5QrCode = null;
    let scanningActive = false;
    
    // Initialize QR Scanner
    function initScanner() {
        if (scanningActive) {
            console.log("Scanner already active");
            return;
        }
        
        html5QrCode = new Html5Qrcode("qr-reader");
        scanningActive = true;
        
        const qrCodeSuccessCallback = (decodedText, decodedResult) => {
            console.log("QR Code detected:", decodedText);
            
            // Immediately stop scanning to prevent multiple scans
            if (html5QrCode && scanningActive) {
                html5QrCode.stop().then(() => {
                    scanningActive = false;
                    console.log("Scanner stopped after successful scan");
                }).catch(err => {
                    console.log("Error stopping scanner:", err);
                    scanningActive = false;
                });
            }
            
            // Show loading state
            document.getElementById('scanner-result').classList.add('d-none');
            document.getElementById('scanner-error').classList.add('d-none');
            
            // Show loading spinner
            const scannerContainer = document.getElementById('scanner-container');
            const originalContent = scannerContainer.innerHTML;
            scannerContainer.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-success" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3">Memproses QR Code...</p>
                </div>
            `;
            
            try {
                // Parse QR data
                let userId = null;
                
                // Coba parse sebagai JSON
                try {
                    const parsedData = JSON.parse(decodedText);
                    if (parsedData.user_id) {
                        userId = parsedData.user_id;
                        console.log("Parsed JSON data, user_id:", userId);
                    }
                } catch (jsonError) {
                    console.log("Not JSON format");
                    
                    // Coba format "user:123"
                    if (decodedText.startsWith('user:')) {
                        const parts = decodedText.split(':');
                        if (parts.length >= 2 && !isNaN(parts[1])) {
                            userId = parseInt(parts[1]);
                            console.log("Found user ID from user: format:", userId);
                        }
                    }
                    
                    // Coba cari angka dalam string
                    if (!userId) {
                        const matches = decodedText.match(/\d+/);
                        if (matches) {
                            userId = parseInt(matches[0]);
                            console.log("Extracted number from QR:", userId);
                        }
                    }
                }
                
                if (!userId) {
                    throw new Error("Tidak dapat menemukan ID warga dalam QR code");
                }
                
                // Fetch user data from server - PERBAIKAN: Gunakan endpoint yang benar
                fetch('/api/scan', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        qr_data: decodedText,
                        user_id: userId
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    // Restore scanner UI
                    scannerContainer.innerHTML = originalContent;
                    
                    if (data.success) {
                        // Display user data
                        showUserData(data.user);
                    } else {
                        showError(data.error || 'Terjadi kesalahan');
                        // Restart scanner after error
                        setTimeout(restartScanner, 3000);
                    }
                })
                .catch(error => {
                    console.error("Fetch Error:", error);
                    scannerContainer.innerHTML = originalContent;
                    showError('Gagal memproses data warga: ' + error.message);
                    // Restart scanner after error
                    setTimeout(restartScanner, 3000);
                });
                
            } catch (error) {
                console.error("Error parsing QR:", error);
                scannerContainer.innerHTML = originalContent;
                showError('QR Code tidak valid: ' + error.message);
                // Restart scanner after error
                setTimeout(restartScanner, 3000);
            }
        };
        
        const config = {
            fps: 5, // Kurangi FPS untuk mengurangi beban
            qrbox: { width: 250, height: 250 },
            rememberLastUsedCamera: true,
            supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA]
        };
        
        // Start scanner
        html5QrCode.start(
            { facingMode: "environment" },
            config,
            qrCodeSuccessCallback,
            (errorMessage) => {
                // Parse error, ignore it.
                console.log("QR Code parse error:", errorMessage);
            }
        ).catch(err => {
            console.error("Failed to start scanner:", err);
            showError('Tidak dapat mengakses kamera. Pastikan izin kamera telah diberikan.');
            scanningActive = false;
        });
    }
    
    function showUserData(user) {
        document.getElementById('result-content').innerHTML = `
            <div class="row">
                <div class="col-md-4 text-center">
                    <div class="mb-3">
                        <div style="width: 100px; height: 100px; background-color: #2E8B57; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; color: white; font-size: 36px; font-weight: bold;">
                            ${user.name.charAt(0)}
                        </div>
                    </div>
                </div>
                <div class="col-md-8 text-start">
                    <h4 class="text-success">${user.name}</h4>
                    <div class="row">
                        <div class="col-6">
                            <p class="mb-2"><strong>Email:</strong><br>${user.email}</p>
                            <p class="mb-2"><strong>Telepon:</strong><br>${user.phone || '-'}</p>
                        </div>
                        <div class="col-6">
                            <p class="mb-2"><strong>Total Poin:</strong><br><span class="badge bg-success">${(user.total_points || 0).toLocaleString('id-ID')} poin</span></p>
                            <p class="mb-2"><strong>Total Transaksi:</strong><br>${user.total_transactions || 0} kali</p>
                        </div>
                    </div>
                    ${user.address ? `<p class="mb-0"><strong>Alamat:</strong><br>${user.address}</p>` : ''}
                </div>
            </div>
        `;
        
        // Set button action
        const userId = user.id;
        document.getElementById('btn-start-transaction').href = `/petugas/transaksi/buat-gabungan?warga_id=${userId}`;
        
        // Show result
        document.getElementById('scanner-result').classList.remove('d-none');
    }
    
    function showError(message) {
        document.getElementById('error-message').textContent = message;
        document.getElementById('scanner-error').classList.remove('d-none');
    }
    
    function restartScanner() {
        if (html5QrCode) {
            html5QrCode.stop().then(() => {
                scanningActive = false;
                console.log("Scanner stopped for restart");
                initScanner();
            }).catch(err => {
                console.log("Error stopping scanner:", err);
                scanningActive = false;
                initScanner();
            });
        } else {
            scanningActive = false;
            initScanner();
        }
    }
    
    // Initialize scanner on page load
    initScanner();
    
    // Rescan button
    document.getElementById('btn-rescan')?.addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('scanner-result').classList.add('d-none');
        document.getElementById('scanner-error').classList.add('d-none');
        restartScanner();
    });
    
    // Retry button
    document.getElementById('btn-retry')?.addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('scanner-error').classList.add('d-none');
        restartScanner();
    });
});
</script>

<style>
#qr-reader video {
    border-radius: 10px;
    border: 2px solid #2E8B57;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

#qr-reader__scan_region {
    text-align: center;
}

#qr-reader__dashboard_section {
    margin-top: 10px;
}

#scanner-result .card {
    box-shadow: 0 4px 15px rgba(46, 139, 87, 0.2);
}
</style>
@endpush
@endsection