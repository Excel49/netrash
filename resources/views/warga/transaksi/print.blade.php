<!DOCTYPE html>
<html>
<head>
    <title>Cetak Transaksi - {{ $transaksi->kode_transaksi }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; }
        .header p { margin: 5px 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f5f5f5; }
        .total { font-weight: bold; }
        .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>STRUK TRANSAKSI BARANG</h1>
        <p>Netrash - Bank Sampah Digital</p>
    </div>
    
    <table>
        <tr>
            <th width="30%">Kode Transaksi</th>
            <td>{{ $transaksi->kode_transaksi }}</td>
        </tr>
        <tr>
            <th>Tanggal</th>
            <td>{{ $transaksi->created_at->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <th>Nama Warga</th>
            <td>{{ $transaksi->warga->name }}</td>
        </tr>
        <tr>
            <th>Petugas</th>
            <td>{{ $transaksi->petugas->name ?? 'Sistem' }}</td>
        </tr>
    </table>
    
    @if($transaksi->detailTransaksi && count($transaksi->detailTransaksi) > 0)
    <h3>Detail Sampah</h3>
    <table>
        <thead>
            <tr>
                <th>Kategori/Jenis</th>
                <th>Berat (kg)</th>
                <th>Poin</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaksi->detailTransaksi as $detail)
            <tr>
                <td>{{ $detail->kategori->nama_kategori ?? '-' }}</td>
                <td>{{ number_format($detail->berat, 2) }}</td>
                <td>{{ number_format($detail->poin, 0) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total">
                <td>TOTAL</td>
                <td>{{ number_format($transaksi->total_berat, 2) }} kg</td>
                <td>{{ number_format($transaksi->total_poin, 0) }} poin</td>
            </tr>
        </tfoot>
    </table>
    @endif
    
    <div class="footer">
        <p>Terima kasih telah berkontribusi menjaga lingkungan!</p>
        <p>Cetak pada: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
    
    <script>
        window.onload = function() {
            window.print();
            setTimeout(function() {
                window.close();
            }, 1000);
        }
    </script>
</body>
</html>