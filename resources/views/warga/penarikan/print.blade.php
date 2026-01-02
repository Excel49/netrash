<!DOCTYPE html>
<html>
<head>
    <title>Receipt Penarikan Poin - #{{ $penarikan->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { color: #2E8B57; margin: 0; }
        .header p { margin: 5px 0; color: #666; }
        .content { margin: 20px 0; }
        .section { margin-bottom: 20px; }
        .section-title { font-weight: bold; border-bottom: 2px solid #2E8B57; padding-bottom: 5px; margin-bottom: 10px; }
        .row { display: flex; justify-content: space-between; margin-bottom: 8px; }
        .label { font-weight: bold; }
        .value { text-align: right; }
        .total { font-size: 1.2em; font-weight: bold; border-top: 2px solid #333; padding-top: 10px; margin-top: 20px; }
        .footer { margin-top: 50px; text-align: center; color: #666; font-size: 0.9em; }
        .signature { margin-top: 50px; display: flex; justify-content: space-between; }
        .signature-box { width: 200px; text-align: center; }
        .signature-line { border-top: 1px solid #333; margin-top: 50px; }
        .status-badge { 
            padding: 5px 10px; 
            border-radius: 20px; 
            font-weight: bold; 
            display: inline-block; 
            margin-top: 5px;
        }
        .pending { background: #ffc107; color: #000; }
        .approved { background: #17a2b8; color: #fff; }
        .completed { background: #28a745; color: #fff; }
        .rejected { background: #dc3545; color: #fff; }
    </style>
</head>
<body>
    <div class="header">
        <h1>NETRATRASH</h1>
        <p>Sistem Bank Sampah Digital</p>
        <h2>RECEIPT PENARIKAN POIN</h2>
        <p>No: #{{ str_pad($penarikan->id, 6, '0', STR_PAD_LEFT) }}</p>
    </div>

    <div class="content">
        <div class="section">
            <div class="section-title">Informasi Penarikan</div>
            <div class="row">
                <span class="label">Tanggal Pengajuan:</span>
                <span class="value">{{ $penarikan->created_at->format('d F Y, H:i') }}</span>
            </div>
            <div class="row">
                <span class="label">Status:</span>
                <span class="value">
                    <span class="status-badge {{ $penarikan->status }}">
                        {{ strtoupper($penarikan->status) }}
                    </span>
                </span>
            </div>
            @if($penarikan->tanggal_approval)
            <div class="row">
                <span class="label">Tanggal Approval:</span>
                <span class="value">{{ $penarikan->tanggal_approval->format('d F Y, H:i') }}</span>
            </div>
            @endif
        </div>

        <div class="section">
            <div class="section-title">Detail Warga</div>
            <div class="row">
                <span class="label">Nama:</span>
                <span class="value">{{ $penarikan->warga->name }}</span>
            </div>
            <div class="row">
                <span class="label">ID Warga:</span>
                <span class="value">#{{ str_pad($penarikan->warga_id, 6, '0', STR_PAD_LEFT) }}</span>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Detail Penarikan</div>
            <div class="row">
                <span class="label">Jumlah Poin:</span>
                <span class="value">{{ number_format($penarikan->jumlah_poin, 0, ',', '.') }} pts</span>
            </div>
            <div class="row">
                <span class="label">Nilai Rupiah:</span>
                <span class="value">Rp {{ number_format($penarikan->jumlah_rupiah, 0, ',', '.') }}</span>
            </div>
            <div class="row">
                <span class="label">Kurs:</span>
                <span class="value">100 poin = Rp 10.000</span>
            </div>
        </div>

        @if($penarikan->catatan_admin)
        <div class="section">
            <div class="section-title">Catatan Admin</div>
            <div class="row">
                <div style="width: 100%;">
                    {{ $penarikan->catatan_admin }}
                </div>
            </div>
        </div>
        @endif

        <div class="total row">
            <span class="label">TOTAL PENARIKAN:</span>
            <span class="value">Rp {{ number_format($penarikan->jumlah_rupiah, 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="signature">
        <div class="signature-box">
            <p>Warga</p>
            <div class="signature-line"></div>
            <p>{{ $penarikan->warga->name }}</p>
        </div>
        <div class="signature-box">
            <p>Admin</p>
            <div class="signature-line"></div>
            <p>{{ $penarikan->admin->name ?? '_______________' }}</p>
        </div>
    </div>

    <div class="footer">
        <p>*** Dokumen ini dicetak secara otomatis oleh sistem NetraTrash ***</p>
        <p>Tanggal cetak: {{ date('d F Y, H:i:s') }}</p>
    </div>
</body>
</html>