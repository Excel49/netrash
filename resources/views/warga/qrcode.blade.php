@extends('layouts.app')

@section('title', 'QR Code Saya')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0">QR Code Saya</h2>
            <a href="{{ route('warga.dashboard') }}" class="btn btn-netra-outline">
                <i class="bi bi-arrow-left me-2"></i>Kembali
            </a>
        </div>
        <p class="text-muted">Tunjukkan QR Code ini ke petugas untuk transaksi</p>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">QR Code Pribadi</h6>
            </div>
            <div class="card-body text-center">
                @if($user->qr_code)
                    <!-- Display QR Code -->
                    <div class="mb-4 p-4 border rounded bg-white d-inline-block">
                        @php
                            // Generate QR Code langsung
                            $qrData = json_encode([
                                'user_id' => $user->id,
                                'name' => $user->name,
                                'email' => $user->email,
                                'timestamp' => now()->timestamp
                            ]);
                        @endphp
                        
                        {!! QrCode::size(250)->generate($qrData) !!}
                    </div>
                    
                    <div class="mb-4">
                        <h5>{{ $user->name }}</h5>
                        <p class="text-muted mb-1">{{ $user->email }}</p>
                        <p class="mb-0">
                            <span class="badge bg-netra">{{ number_format($user->total_points, 0, ',', '.') }} poin</span>
                        </p>
                    </div>
                    
                    <!-- Download Button -->
                    <div class="d-grid gap-2 d-md-block">
                        <button onclick="downloadQRCode()" class="btn btn-netra me-2">
                            <i class="bi bi-download me-2"></i>Download QR Code
                        </button>
                        <button onclick="printQRCode()" class="btn btn-netra-outline">
                            <i class="bi bi-printer me-2"></i>Print QR Code
                        </button>
                    </div>
                @else
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        QR Code belum tersedia. Hubungi admin untuk generate QR Code.
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Instructions -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">Petunjuk Penggunaan</h6>
            </div>
            <div class="card-body">
                <ol class="mb-0">
                    <li class="mb-2">Tunjukkan QR Code ini ke petugas saat menyerahkan sampah</li>
                    <li class="mb-2">Petugas akan scan QR Code Anda</li>
                    <li class="mb-2">Data Anda akan muncul di sistem petugas</li>
                    <li class="mb-2">Transaksi akan diproses dan poin ditambahkan</li>
                    <li class="mb-2">Simpan QR Code di smartphone Anda untuk akses cepat</li>
                </ol>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function downloadQRCode() {
    // Create a temporary canvas to download QR code
    const svg = document.querySelector('.card-body svg');
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    const img = new Image();
    
    const svgData = new XMLSerializer().serializeToString(svg);
    const svgBlob = new Blob([svgData], {type: 'image/svg+xml;charset=utf-8'});
    const url = URL.createObjectURL(svgBlob);
    
    img.onload = function() {
        canvas.width = img.width;
        canvas.height = img.height;
        ctx.drawImage(img, 0, 0);
        
        const pngUrl = canvas.toDataURL('image/png');
        const downloadLink = document.createElement('a');
        downloadLink.href = pngUrl;
        downloadLink.download = 'qrcode-{{ $user->name }}.png';
        document.body.appendChild(downloadLink);
        downloadLink.click();
        document.body.removeChild(downloadLink);
        
        URL.revokeObjectURL(url);
        
        // Show success message
        Swal.fire({
            icon: 'success',
            title: 'QR Code Downloaded',
            text: 'QR Code berhasil didownload!',
            timer: 2000,
            showConfirmButton: false
        });
    };
    
    img.src = url;
}

function printQRCode() {
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Print QR Code - {{ $user->name }}</title>
            <style>
                body { 
                    font-family: Arial, sans-serif; 
                    text-align: center; 
                    padding: 20px;
                }
                .qrcode-container { 
                    margin: 20px auto; 
                    padding: 20px; 
                    border: 1px solid #ddd; 
                    display: inline-block;
                }
                .user-info { 
                    margin-top: 20px; 
                    font-size: 14px;
                }
                @media print {
                    body { padding: 0; }
                }
            </style>
        </head>
        <body>
            <h3>NetraTrash - QR Code</h3>
            <div class="qrcode-container">
                ${document.querySelector('.card-body').innerHTML}
            </div>
            <div class="user-info">
                <p><strong>Nama:</strong> {{ $user->name }}</p>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Poin:</strong> {{ number_format($user->total_points, 0, ',', '.') }}</p>
                <p><small>Cetak tanggal: ${new Date().toLocaleDateString('id-ID')}</small></p>
            </div>
            <script>
                window.onload = function() {
                    window.print();
                    setTimeout(function() {
                        window.close();
                    }, 500);
                };
            <\/script>
        </body>
        </html>
    `);
    printWindow.document.close();
}
</script>
@endpush
@endsection