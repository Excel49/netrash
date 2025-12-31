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
            grid-template-columns: repeat(3, 1fr);
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
        .total-users { border-color: #2C3E50; }
        .total-users .value { color: #2C3E50; }
        .total-poin { border-color: #F39C12; }
        .total-poin .value { color: #F39C12; }
        .verified { border-color: #27AE60; }
        .verified .value { color: #27AE60; }
        
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
        .role-badge {
            font-weight: bold;
            font-size: 9px;
            padding: 2px 8px;
            border-radius: 10px;
            display: inline-block;
        }
        .role-admin {
            background-color: #f8d7da;
            color: #721c24;
        }
        .role-petugas {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        .role-warga {
            background-color: #d4edda;
            color: #155724;
        }
        .verified-badge {
            font-weight: bold;
            font-size: 9px;
            padding: 2px 6px;
            border-radius: 3px;
            display: inline-block;
        }
        .verified-yes {
            background-color: #d4edda;
            color: #155724;
        }
        .verified-no {
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
            Data Pengguna Sistem
            | Dicetak: {{ $exportDate }}
            @if($status)
                | Status: {{ $status == 'verified' ? 'Terverifikasi' : 'Belum Terverifikasi' }}
            @endif
        </div>
    </div>

    <div class="summary">
        <div class="summary-grid">
            <div class="summary-card total-users">
                <h3>Total Pengguna</h3>
                <div class="value">{{ number_format($summary['total']) }}</div>
            </div>
            <div class="summary-card total-poin">
                <h3>Total Poin</h3>
                <div class="value">{{ number_format($summary['total_poin']) }}</div>
            </div>
            <div class="summary-card verified">
                <h3>Terverifikasi</h3>
                <div class="value">{{ number_format($summary['verified']) }}</div>
            </div>
        </div>
        <div style="text-align: center; font-size: 9px; color: #666; margin-top: 8px;">
            Admin: {{ $summary['admin'] }} | Petugas: {{ $summary['petugas'] }} | Warga: {{ $summary['warga'] }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="30">No</th>
                <th width="60">ID</th>
                <th>Nama Lengkap</th>
                <th>Email</th>
                <th width="80">Telepon</th>
                <th width="70">Role</th>
                <th width="70" class="text-right">Total Poin</th>
                <th width="90">Tanggal Bergabung</th>
                <th width="70">Verifikasi</th>
                <th>Alamat</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $user)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ $user->id }}</td>
                <td>
                    <strong>{{ $user->name }}</strong>
                    @if($user->username)
                    <br><small style="color: #666;">@ {{ $user->username }}</small>
                    @endif
                </td>
                <td>{{ $user->email }}</td>
                <td class="text-center">{{ $user->phone ?: '-' }}</td>
                <td class="text-center">
                    @if($user->role_id == 1)
                        <span class="role-badge role-admin">ADMIN</span>
                    @elseif($user->role_id == 2)
                        <span class="role-badge role-petugas">PETUGAS</span>
                    @else
                        <span class="role-badge role-warga">WARGA</span>
                    @endif
                </td>
                <td class="text-right">{{ number_format($user->total_points) }}</td>
                <td class="text-center">
                    {{ $user->created_at->format('d/m/Y') }}<br>
                    {{ $user->created_at->format('H:i') }}
                </td>
                <td class="text-center">
                    @if($user->email_verified_at)
                        <span class="verified-badge verified-yes">YA</span>
                    @else
                        <span class="verified-badge verified-no">TIDAK</span>
                    @endif
                </td>
                <td>{{ Str::limit($user->address ?? '-', 30) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" class="text-right"><strong>TOTAL:</strong></td>
                <td class="text-right"><strong>{{ number_format($summary['total_poin']) }}</strong></td>
                <td colspan="3"></td>
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