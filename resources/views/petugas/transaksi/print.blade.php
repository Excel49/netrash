<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Transaksi - {{ $transaksi->kode_transaksi }}</title>
    <style>
        @media print {
            @page {
                size: 80mm auto;
                margin: 0;
            }
            body {
                font-family: 'Courier New', monospace;
                font-size: 12px;
                width: 80mm;
                margin: 0;
                padding: 5mm;
            }
            .no-print {
                display: none !important;
            }
        }
        
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            max-width: 80mm;
            margin: 0 auto;
            padding: 10px;
            background: white;
        }
        
        .header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        
        .header h2 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
        }
        
        .header p {
            margin: 0;
            font-size: 10px;
        }
        
        .info {
            margin-bottom: 10px;
        }
        
        .info table {
            width: 100%;
        }
        
        .info td {
            padding: 2px 0;
        }
        
        .items {
            margin: 10px 0;
        }
        
        .items table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .items th {
            border-bottom: 1px dashed #000;
            padding: 5px 0;
            text-align: left;
        }
        
        .items td {
            padding: 3px 0;
            border-bottom: 1px dashed #ddd;
        }
        
        .total {
            margin-top: 10px;
            border-top: 2px solid #000;
            padding-top: 10px;
        }
        
        .total table {
            width: 100%;
        }
        
        .total td {
            padding: 3px 0;
        }
        
        .total .amount {
            text-align: right;
            font-weight: bold;
        }
        
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px dashed #000;
            font-size: 10px;
        }
        
        .barcode {
            text-align: center;
            margin: 10px 0;
        }
        
        .print-btn {
            text-align: center;
            margin-top: 20px;
        }
        
        .print-btn button {
            padding: 10px 20px;
            background: #2E8B57;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .bold {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h2>NETRATRASH</h2>
        <p>Bank Sampah Digital</p>
        <p>Jl. Lingkungan Sejahtera No. 123</p>
        <p>Telp: 0812-3456-7890</p>
    </div>
    
    <!-- Transaction Info -->
    <div class="info">
        <table>
            <tr>
                <td>Kode Transaksi</td>
                <td class="text-right bold">{{ $transaksi->kode_transaksi }}</td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td class="text-right">{{ $transaksi->tanggal_transaksi->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td>Warga</td>
                <td class="text-right">{{ $transaksi->warga->name }}</td>
            </tr>
            <tr>
                <td>Petugas</td>
                <td class="text-right">{{ $transaksi->petugas->name }}</td>
            </tr>
        </table>
    </div>
    
    <!-- Items -->
    <div class="items">
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="text-right">Berat</th>
                    <th class="text-right">Poin</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaksi->detailTransaksi as $detail)
                <tr>
                    <td>{{ $detail->kategori->nama_kategori }}</td>
                    <td class="text-right">{{ number_format($detail->berat, 1) }} kg</td>
                    <td class="text-right">{{ number_format($detail->poin, 0) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <!-- Total -->
    <div class="total">
        <table>
            <tr>
                <td>Total Berat</td>
                <td class="text-right amount">{{ number_format($transaksi->total_berat, 1) }} kg</td>
            </tr>
            <tr>
                <td>Total Harga</td>
                <td class="text-right amount">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Poin</td>
                <td class="text-right amount">{{ number_format($transaksi->total_poin, 0, ',', '.') }} poin</td>
            </tr>
            <tr>
                <td colspan="2" style="border-top: 1px dashed #000; padding-top: 5px;">
                    <strong>Catatan:</strong> {{ $transaksi->catatan ?: '-' }}
                </td>
            </tr>
        </table>
    </div>
    
    <!-- Footer -->
    <div class="footer">
        <p>** Struk ini sebagai bukti transaksi **</p>
        <p>Terima kasih telah menjaga lingkungan</p>
        <p>Sampahmu, Poinmu, Lingkungan Sehat!</p>
        <div class="barcode">
            <div style="font-family: 'Libre Barcode 39'; font-size: 24px;">
                *{{ $transaksi->kode_transaksi }}*
            </div>
        </div>
        <p>{{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
    
    <!-- Print Button -->
    <div class="print-btn no-print">
        <button onclick="window.print()">
            <i class="bi bi-printer"></i> Print Struk
        </button>
        <p style="margin-top: 10px; font-size: 10px; color: #666;">
            *Pastikan printer thermal sudah terhubung
        </p>
    </div>
    
    <script>
        // Auto print on load
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 1000);
        };
        
        // After print, go back
        window.onafterprint = function() {
            setTimeout(function() {
                window.history.back();
            }, 500);
        };
    </script>
</body>
</html>