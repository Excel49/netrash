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
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #2C3E50;
        }
        .header h2 {
            margin: 5px 0;
            font-size: 14px;
            color: #666;
        }
        .header .info {
            font-size: 10px;
            color: #777;
            margin-top: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th {
            background-color: #2C3E50;
            color: white;
            font-weight: bold;
            padding: 8px;
            text-align: center;
            border: 1px solid #ddd;
        }
        td {
            padding: 6px;
            border: 1px solid #ddd;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .summary {
            margin-top: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .summary-label {
            font-weight: bold;
            color: #2C3E50;
        }
        .summary-value {
            font-weight: bold;
            color: #27AE60;
        }
        .status-pending {
            color: #F39C12;
            font-weight: bold;
        }
        .status-completed {
            color: #27AE60;
            font-weight: bold;
        }
        .status-cancelled {
            color: #E74C3C;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 9px;
            color: #777;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <h2>Sistem NetraTrash</h2>
        <div class="info">
            @if($startDate && $endDate)
                Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
            @else
                Semua Data
            @endif
            | Tanggal Export: {{ $exportDate }}
        </div>
    </div>

    <div class="summary">
        <div class="summary-row">
            <span class="summary-label">Total Transaksi:</span>
            <span class="summary-value">{{ number_format($summary['total']) }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Total Berat:</span>
            <span class="summary-value">{{ number_format($summary['total_berat'], 2) }} kg</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Total Pendapatan:</span>
            <span class="summary-value">Rp {{ number_format($summary['total_harga'], 0, ',', '.') }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Total Poin:</span>
            <span class="summary-value">{{ number_format($summary['total_poin']) }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Status: Selesai:</span>
            <span class="summary-value">{{ $summary['completed'] }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Status: Pending:</span>
            <span>{{ $summary['pending'] }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Status: Dibatalkan:</span>
            <span>{{ $summary['cancelled'] }}</span>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Transaksi</th>
                <th>Tanggal</th>
                <th>Warga</th>
                <th>Petugas</th>
                <th class="text-right">Berat (kg)</th>
                <th class="text-right">Harga (Rp)</th>
                <th class="text-right">Poin</th>
                <th>Status</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $transaksi)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $transaksi->kode_transaksi }}</td>
                <td>{{ $transaksi->tanggal_transaksi->format('d/m/Y H:i') }}</td>
                <td>{{ $transaksi->warga->name }}</td>
                <td>{{ $transaksi->petugas->name }}</td>
                <td class="text-right">{{ number_format($transaksi->total_berat, 2) }}</td>
                <td class="text-right">{{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($transaksi->total_poin) }}</td>
                <td>
                    @if($transaksi->status == 'completed')
                        <span class="status-completed">SELESAI</span>
                    @elseif($transaksi->status == 'pending')
                        <span class="status-pending">PENDING</span>
                    @else
                        <span class="status-cancelled">DIBATALKAN</span>
                    @endif
                </td>
                <td>{{ $transaksi->catatan ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5" class="text-right">TOTAL:</th>
                <th class="text-right">{{ number_format($summary['total_berat'], 2) }} kg</th>
                <th class="text-right">Rp {{ number_format($summary['total_harga'], 0, ',', '.') }}</th>
                <th class="text-right">{{ number_format($summary['total_poin']) }}</th>
                <th colspan="2"></th>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <div>Dicetak oleh: Sistem NetraTrash</div>
        <div>Halaman 1 dari 1</div>
    </div>
</body>
</html>