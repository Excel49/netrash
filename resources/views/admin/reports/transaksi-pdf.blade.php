<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        @page {
            margin: 20px;
            size: A4 landscape;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #2C3E50;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            color: #2C3E50;
            font-weight: bold;
        }
        .header .subtitle {
            margin: 5px 0;
            font-size: 14px;
            color: #666;
        }
        .header .info {
            font-size: 10px;
            color: #777;
            margin-top: 8px;
        }
        .summary {
            margin: 15px 0;
            padding: 10px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
        }
        .summary-card {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-align: center;
            background-color: white;
        }
        .summary-card h3 {
            margin: 0 0 5px 0;
            font-size: 11px;
            color: #666;
            font-weight: bold;
        }
        .summary-card .value {
            font-size: 16px;
            font-weight: bold;
            margin: 0;
        }
        .total-transaksi { border-color: #2C3E50; }
        .total-transaksi .value { color: #2C3E50; }
        .total-berat { border-color: #27AE60; }
        .total-berat .value { color: #27AE60; }
        .total-pendapatan { border-color: #E74C3C; }
        .total-pendapatan .value { color: #E74C3C; }
        .total-poin { border-color: #F39C12; }
        .total-poin .value { color: #F39C12; }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th {
            background-color: #2C3E50;
            color: white;
            font-weight: bold;
            padding: 8px 5px;
            text-align: center;
            border: 1px solid #dee2e6;
            font-size: 10px;
        }
        td {
            padding: 6px 5px;
            border: 1px solid #dee2e6;
            vertical-align: top;
            font-size: 9px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .status {
            font-weight: bold;
            font-size: 9px;
            padding: 2px 6px;
            border-radius: 3px;
            display: inline-block;
        }
        .status-selesai {
            background-color: #d4edda;
            color: #155724;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-dibatalkan {
            background-color: #f8d7da;
            color: #721c24;
        }
        tfoot td {
            font-weight: bold;
            background-color: #f8f9fa;
            border-top: 2px solid #dee2e6;
        }
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #dee2e6;
            font-size: 8px;
            color: #777;
            text-align: center;
        }
        .page-number {
            position: fixed;
            bottom: 20px;
            right: 20px;
            font-size: 9px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <div class="subtitle">Sistem Manajemen Sampah NetraTrash</div>
        <div class="info">
            @if($startDate && $endDate)
                Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
            @else
                Semua Data Transaksi
            @endif
            | Dicetak: {{ $exportDate }}
            @if($status)
                | Status: {{ ucfirst($status) }}
            @endif
        </div>
    </div>

    <div class="summary">
        <div class="summary-grid">
            <div class="summary-card total-transaksi">
                <h3>Total Transaksi</h3>
                <div class="value">{{ number_format($summary['total']) }}</div>
            </div>
            <div class="summary-card total-berat">
                <h3>Total Berat</h3>
                <div class="value">{{ number_format($summary['total_berat'], 2) }} kg</div>
            </div>
            <div class="summary-card total-pendapatan">
                <h3>Total Pendapatan</h3>
                <div class="value">Rp {{ number_format($summary['total_harga'], 0, ',', '.') }}</div>
            </div>
            <div class="summary-card total-poin">
                <h3>Total Poin</h3>
                <div class="value">{{ number_format($summary['total_poin']) }}</div>
            </div>
        </div>
        <div style="text-align: center; font-size: 9px; color: #666; margin-top: 8px;">
            Selesai: {{ $summary['completed'] }} | Pending: {{ $summary['pending'] }} | Dibatalkan: {{ $summary['cancelled'] }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="30">No</th>
                <th width="100">Kode Transaksi</th>
                <th width="80">Tanggal</th>
                <th>Warga</th>
                <th>Petugas</th>
                <th width="60" class="text-right">Berat (kg)</th>
                <th width="80" class="text-right">Harga (Rp)</th>
                <th width="60" class="text-right">Poin</th>
                <th width="70">Status</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $transaksi)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $transaksi->kode_transaksi }}</td>
                <td>{{ $transaksi->tanggal_transaksi->format('d/m/Y') }}<br>{{ $transaksi->tanggal_transaksi->format('H:i') }}</td>
                <td>{{ $transaksi->warga->name }}<br><small style="color: #666;">{{ $transaksi->warga->phone ?? '-' }}</small></td>
                <td>{{ $transaksi->petugas->name }}</td>
                <td class="text-right">{{ number_format($transaksi->total_berat, 2) }}</td>
                <td class="text-right">{{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($transaksi->total_poin) }}</td>
                <td class="text-center">
                    @if($transaksi->status == 'completed')
                        <span class="status status-selesai">SELESAI</span>
                    @elseif($transaksi->status == 'pending')
                        <span class="status status-pending">PENDING</span>
                    @else
                        <span class="status status-dibatalkan">DIBATALKAN</span>
                    @endif
                </td>
                <td>{{ Str::limit($transaksi->catatan ?? '-', 30) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="text-right"><strong>TOTAL:</strong></td>
                <td class="text-right"><strong>{{ number_format($summary['total_berat'], 2) }} kg</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($summary['total_harga'], 0, ',', '.') }}</strong></td>
                <td class="text-right"><strong>{{ number_format($summary['total_poin']) }}</strong></td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <div>Laporan ini dibuat secara otomatis oleh Sistem NetraTrash</div>
        <div>Alamat: Jl. Contoh No. 123, Kota Bandung | Telp: (022) 123456</div>
    </div>
    
    <div class="page-number">
        Halaman 1
    </div>
</body>
</html>