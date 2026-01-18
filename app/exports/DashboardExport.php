<?php

namespace App\Exports;

use App\Models\Transaksi;
use App\Models\User;
use App\Models\KategoriSampah;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class DashboardExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize, WithColumnWidths
{
    protected $startDate;
    protected $endDate;
    protected $status;
    protected $petugasId;
    protected $includeSummary;
    protected $includeTransactions;
    
    public function __construct($startDate, $endDate, $status = null, $petugasId = null, $includeSummary = true, $includeTransactions = true)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->status = $status;
        $this->petugasId = $petugasId;
        $this->includeSummary = $includeSummary;
        $this->includeTransactions = $includeTransactions;
    }
    
    public function title(): string
    {
        return 'Dashboard Report';
    }
    
    public function collection()
    {
        $query = Transaksi::with(['warga', 'petugas', 'detailTransaksi.kategori'])
            ->whereBetween('created_at', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59']);
            
        if ($this->status) {
            $query->where('status', $this->status);
        }
        
        if ($this->petugasId) {
            $query->where('petugas_id', $this->petugasId);
        }
        
        return $query->orderBy('created_at', 'desc')->get();
    }
    
    public function headings(): array
    {
        return [
            'Kode Transaksi',
            'Tanggal Transaksi',
            'Warga',
            'Telepon Warga',
            'Petugas',
            'Total Berat (kg)',
            'Total Poin',
            'Status',
            'Catatan',
            'Jenis Sampah',
            'Berat per Jenis (kg)',
            'Poin per Jenis'
        ];
    }
    
    public function map($transaksi): array
    {
        $jenisSampah = [];
        $beratPerJenis = [];
        $poinPerJenis = [];
        
        foreach ($transaksi->detailTransaksi as $detail) {
            $jenisSampah[] = $detail->kategori->nama_kategori ?? 'Tidak ada';
            $beratPerJenis[] = number_format($detail->berat, 2);
            $poinPerJenis[] = number_format($detail->total_poin);
        }
        
        return [
            $transaksi->kode_transaksi,
            $transaksi->created_at->format('d/m/Y H:i'),
            $transaksi->warga->name ?? 'N/A',
            $transaksi->warga->phone ?? '-',
            $transaksi->petugas->name ?? 'N/A',
            number_format($transaksi->total_berat, 2),
            number_format($transaksi->total_poin),
            $this->getStatusLabel($transaksi->status),
            $transaksi->catatan ?? '-',
            implode(', ', $jenisSampah),
            implode(', ', $beratPerJenis),
            implode(', ', $poinPerJenis)
        ];
    }
    
    public function columnWidths(): array
    {
        return [
            'A' => 20, // Kode Transaksi
            'B' => 18, // Tanggal
            'C' => 25, // Warga
            'D' => 15, // Telepon
            'E' => 20, // Petugas
            'F' => 15, // Berat
            'G' => 12, // Poin
            'H' => 12, // Status
            'I' => 30, // Catatan
            'J' => 25, // Jenis Sampah
            'K' => 20, // Berat per Jenis
            'L' => 15, // Poin per Jenis
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        // Header style
        $sheet->getStyle('A1:L1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2E8B57']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);
        
        // Data rows style
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle('A2:L' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'DDDDDD']
                ]
            ]
        ]);
        
        // Set column alignment
        $sheet->getStyle('F')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('G')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('K')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('L')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        
        // Auto filter
        $sheet->setAutoFilter('A1:L1');
        
        // Freeze header row
        $sheet->freezePane('A2');
        
        return [
            1 => ['font' => ['bold' => true]]
        ];
    }
    
    private function getStatusLabel($status)
    {
        $labels = [
            'completed' => 'Selesai',
            'pending' => 'Pending',
            'cancelled' => 'Dibatalkan'
        ];
        
        return $labels[$status] ?? $status;
    }
}