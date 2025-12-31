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
        .total-penarikan { border-color: #2C3E50; }
        .total-penarikan .value { color: #2C3E50; }
        .total-poin { border-color: #F39C12; }
        .total-poin .value { color: #F39C12; }
        .total-nilai { border-color: #27AE60; }
        .total-nilai .value { color: #27AE60; }
        .total-pending { border-color: #E74C3C; }
        .total-pending .value { color: #E74C3C; }
        
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
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-approved {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }
        .status-rejected {
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
                Semua Data Penarikan
            @endif
            | Dicetak: {{ $exportDate }}
            @if($status)
                | Status: {{ ucfirst($status) }}
            @endif
        </div>
    </div>

    <div class="summary">
        <div class="summary-grid">
            <div class="summary-card total-penarikan">
                <h3>Total Penarikan</h3>
                <div class="value">{{ number_format($summary['total']) }}</div>
            </div>
            <div class="summary-card total-poin">
                <h3>Total Poin</h3>
                <div class="value">{{ number_format($summary['total_poin']) }}</div>
            </div>
            <div class="summary-card total-nilai">
                <h3>Total Nilai</h3>
                <div class="value">Rp {{ number_format($summary['total_rupiah'], 0, ',', '.') }}</div>
            </div>
            <div class="summary-card total-pending">
                <h3>Pending</h3>
                <div class="value">{{ number_format($summary['pending']) }}</div>
            </div>
        </div>
        <div style="text-align: center; font-size: 9px; color: #666; margin-top: 8px;">
            Disetujui: {{ $summary['approved'] }} | Selesai: {{ $summary['completed'] }} | Ditolak: {{ $summary['rejected'] }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="30">No</th>
                <th width="60">ID</th>
                <th width="80">Tanggal Pengajuan</th>
                <th>Warga</th>
                <th width="70" class="text-right">Jumlah Poin</th>
                <th width="90" class="text-right">Nilai Rupiah</th>
                <th width="70">Status</th>
                <th width="80">Tanggal Approval</th>
                <th>Admin Approval</th>
                <th>Alasan Penarikan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $penarikan)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ $penarikan->id }}</td>
                <td>{{ $penarikan->tanggal_pengajuan->format('d/m/Y') }}<br>{{ $penarikan->tanggal_pengajuan->format('H:i') }}</td>
                <td>
                    {{ $penarikan->warga->name }}
                    <br>
                    <small style="color: #666;">{{ $penarikan->warga->email }}</small>
                </td>
                <td class="text-right">{{ number_format($penarikan->jumlah_poin) }}</td>
                <td class="text-right">Rp {{ number_format($penarikan->jumlah_rupiah, 0, ',', '.') }}</td>
                <td class="text-center">
                    @if($penarikan->status == 'pending')
                        <span class="status status-pending">PENDING</span>
                    @elseif($penarikan->status == 'approved')
                        <span class="status status-approved">DISETUJUI</span>
                    @elseif($penarikan->status == 'completed')
                        <span class="status status-completed">SELESAI</span>
                    @else
                        <span class="status status-rejected">DITOLAK</span>
                    @endif
                </td>
                <td class="text-center">
                    @if($penarikan->tanggal_approval)
                        {{ $penarikan->tanggal_approval->format('d/m/Y') }}<br>
                        {{ $penarikan->tanggal_approval->format('H:i') }}
                    @else
                        -
                    @endif
                </td>
                <td>{{ $penarikan->admin ? $penarikan->admin->name : '-' }}</td>
                <td>{{ Str::limit($penarikan->alasan_penarikan ?? '-', 30) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-right"><strong>TOTAL:</strong></td>
                <td class="text-right"><strong>{{ number_format($summary['total_poin']) }}</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($summary['total_rupiah'], 0, ',', '.') }}</strong></td>
                <td colspan="4"></td>
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