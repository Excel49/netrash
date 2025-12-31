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
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 10px;
        }
        .summary-card {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-align: center;
        }
        .summary-card h3 {
            margin: 0;
            font-size: 14px;
            color: #666;
        }
        .summary-card .value {
            font-size: 20px;
            font-weight: bold;
            margin: 5px 0;
        }
        .summary-card.total { border-color: #2C3E50; }
        .summary-card.total .value { color: #2C3E50; }
        .summary-card.poin { border-color: #F39C12; }
        .summary-card.poin .value { color: #F39C12; }
        .summary-card.rupiah { border-color: #27AE60; }
        .summary-card.rupiah .value { color: #27AE60; }
        .summary-card.pending { border-color: #E74C3C; }
        .summary-card.pending .value { color: #E74C3C; }
        
        .status-badge {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            display: inline-block;
        }
        .status-pending {
            background-color: #F39C12;
            color: white;
        }
        .status-approved {
            background-color: #3498DB;
            color: white;
        }
        .status-completed {
            background-color: #27AE60;
            color: white;
        }
        .status-rejected {
            background-color: #E74C3C;
            color: white;
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
        <h2>Sistem NetraTrash - Laporan Penarikan Poin</h2>
        <div class="info">
            @if($startDate && $endDate)
                Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
            @else
                Semua Data
            @endif
            | Tanggal Export: {{ $exportDate }}
            @if($status)
                | Status: {{ ucfirst($status) }}
            @endif
        </div>
    </div>

    <div class="summary">
        <div class="summary-grid">
            <div class="summary-card total">
                <h3>Total Penarikan</h3>
                <div class="value">{{ number_format($summary['total']) }}</div>
            </div>
            <div class="summary-card poin">
                <h3>Total Poin</h3>
                <div class="value">{{ number_format($summary['total_poin']) }}</div>
            </div>
            <div class="summary-card rupiah">
                <h3>Total Nilai</h3>
                <div class="value">Rp {{ number_format($summary['total_rupiah'], 0, ',', '.') }}</div>
            </div>
            <div class="summary-card pending">
                <h3>Pending</h3>
                <div class="value">{{ number_format($summary['pending']) }}</div>
            </div>
        </div>
        <div style="text-align: center; font-size: 10px; color: #666;">
            Disetujui: {{ $summary['approved'] }} | Selesai: {{ $summary['completed'] }} | Ditolak: {{ $summary['rejected'] }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>ID</th>
                <th>Tanggal Pengajuan</th>
                <th>Warga</th>
                <th class="text-right">Jumlah Poin</th>
                <th class="text-right">Nilai Rupiah</th>
                <th>Status</th>
                <th>Tanggal Approval</th>
                <th>Admin Approval</th>
                <th>Alasan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $penarikan)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ $penarikan->id }}</td>
                <td>{{ $penarikan->tanggal_pengajuan->format('d/m/Y H:i') }}</td>
                <td>{{ $penarikan->warga->name }}</td>
                <td class="text-right">{{ number_format($penarikan->jumlah_poin) }}</td>
                <td class="text-right">Rp {{ number_format($penarikan->jumlah_rupiah, 0, ',', '.') }}</td>
                <td class="text-center">
                    @if($penarikan->status == 'pending')
                        <span class="status-badge status-pending">PENDING</span>
                    @elseif($penarikan->status == 'approved')
                        <span class="status-badge status-approved">DISETUJUI</span>
                    @elseif($penarikan->status == 'completed')
                        <span class="status-badge status-completed">SELESAI</span>
                    @else
                        <span class="status-badge status-rejected">DITOLAK</span>
                    @endif
                </td>
                <td class="text-center">
                    {{ $penarikan->tanggal_approval ? $penarikan->tanggal_approval->format('d/m/Y H:i') : '-' }}
                </td>
                <td>{{ $penarikan->admin ? $penarikan->admin->name : '-' }}</td>
                <td>{{ $penarikan->alasan_penarikan ?: '-' }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="text-right">TOTAL:</th>
                <th class="text-right">{{ number_format($summary['total_poin']) }}</th>
                <th class="text-right">Rp {{ number_format($summary['total_rupiah'], 0, ',', '.') }}</th>
                <th colspan="4"></th>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <div>Dicetak oleh: Sistem NetraTrash</div>
        <div>Halaman 1 dari 1</div>
    </div>
</body>
</html>