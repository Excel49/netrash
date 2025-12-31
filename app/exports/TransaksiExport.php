<?php

namespace App\Exports;

use App\Models\Transaksi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TransaksiExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithColumnWidths, WithEvents
{
    protected $startDate;
    protected $endDate;
    protected $status;
    protected $petugas_id;
    
    public function __construct($startDate = null, $endDate = null, $status = null, $petugas_id = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->status = $status;
        $this->petugas_id = $petugas_id;
    }
    
    public function collection()
    {
        $query = Transaksi::with(['warga', 'petugas', 'detailTransaksi.kategori']);
        
        if ($this->startDate && $this->endDate) {
            $query->whereBetween('created_at', [
                $this->startDate . ' 00:00:00',
                $this->endDate . ' 23:59:59'
            ]);
        }
        
        if ($this->status) {
            $query->where('status', $this->status);
        }
        
        if ($this->petugas_id) {
            $query->where('petugas_id', $this->petugas_id);
        }
        
        return $query->orderBy('created_at', 'desc')->get();
    }
    
    public function title(): string
    {
        return 'Transaksi';
    }
    
    public function headings(): array
    {
        return [
            'KODE TRANSAKSI',
            'TANGGAL TRANSAKSI',
            'NAMA WARGA',
            'TELEPON WARGA',
            'PETUGAS',
            'TOTAL BERAT (KG)',
            'TOTAL HARGA (RP)',
            'TOTAL POIN',
            'STATUS',
            'CATATAN'
        ];
    }
    
    public function map($transaksi): array
    {
        return [
            $transaksi->kode_transaksi,
            $transaksi->tanggal_transaksi->format('d/m/Y H:i'),
            $transaksi->warga->name,
            $transaksi->warga->phone ?? '-',
            $transaksi->petugas->name,
            number_format($transaksi->total_berat, 2, ',', '.'),
            number_format($transaksi->total_harga, 0, ',', '.'),
            number_format($transaksi->total_poin, 0, ',', '.'),
            $this->getStatusText($transaksi->status),
            $transaksi->catatan ?? '-'
        ];
    }
    
    private function getStatusText($status)
    {
        $statuses = [
            'pending' => 'PENDING',
            'completed' => 'SELESAI',
            'cancelled' => 'DIBATALKAN'
        ];
        
        return $statuses[$status] ?? strtoupper($status);
    }
    
    public function columnWidths(): array
    {
        return [
            'A' => 20, // Kode Transaksi
            'B' => 18, // Tanggal
            'C' => 25, // Nama Warga
            'D' => 15, // Telepon
            'E' => 25, // Petugas
            'F' => 15, // Total Berat
            'G' => 18, // Total Harga
            'H' => 12, // Total Poin
            'I' => 12, // Status
            'J' => 30, // Catatan
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        // Style untuk header
        $sheet->getStyle('A1:J1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2C3E50'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        
        // Auto filter
        $sheet->setAutoFilter('A1:J1');
        
        // Style untuk semua cell
        $sheet->getStyle('A2:J' . ($sheet->getHighestRow()))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'DDDDDD'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        
        // Style untuk kolom angka (rata kanan)
        $sheet->getStyle('F2:H' . ($sheet->getHighestRow()))->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_RIGHT,
            ],
        ]);
        
        // Style untuk status
        $lastRow = $sheet->getHighestRow();
        for ($row = 2; $row <= $lastRow; $row++) {
            $status = $sheet->getCell('I' . $row)->getValue();
            if ($status == 'SELESAI') {
                $sheet->getStyle('I' . $row)->applyFromArray([
                    'font' => ['color' => ['rgb' => '27AE60']],
                ]);
            } elseif ($status == 'PENDING') {
                $sheet->getStyle('I' . $row)->applyFromArray([
                    'font' => ['color' => ['rgb' => 'F39C12']],
                ]);
            } elseif ($status == 'DIBATALKAN') {
                $sheet->getStyle('I' . $row)->applyFromArray([
                    'font' => ['color' => ['rgb' => 'E74C3C']],
                ]);
            }
        }
        
        // Set tinggi row untuk header
        $sheet->getRowDimension(1)->setRowHeight(25);
        
        return [];
    }
    
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Tambahkan judul dan informasi
                $sheet = $event->sheet->getDelegate();
                
                // Insert rows untuk judul
                $sheet->insertNewRowBefore(1, 4);
                
                // Judul
                $sheet->mergeCells('A1:J1');
                $sheet->setCellValue('A1', 'LAPORAN TRANSAKSI SAMPAH');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 16,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);
                
                // Periode
                $period = 'Periode: ';
                if ($this->startDate && $this->endDate) {
                    $start = \Carbon\Carbon::parse($this->startDate)->format('d/m/Y');
                    $end = \Carbon\Carbon::parse($this->endDate)->format('d/m/Y');
                    $period .= $start . ' - ' . $end;
                } else {
                    $period .= 'Semua Data';
                }
                
                $sheet->mergeCells('A2:J2');
                $sheet->setCellValue('A2', $period);
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['size' => 12],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);
                
                // Tanggal Export
                $sheet->mergeCells('A3:J3');
                $sheet->setCellValue('A3', 'Tanggal Export: ' . date('d/m/Y H:i:s'));
                $sheet->getStyle('A3')->applyFromArray([
                    'font' => ['size' => 10, 'italic' => true],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);
                
                // Pindahkan header ke row 5
                $sheet->fromArray($this->headings(), NULL, 'A5');
                
                // Data dimulai dari row 6
                $data = $this->collection();
                $row = 6;
                foreach ($data as $item) {
                    $sheet->fromArray($this->map($item), NULL, 'A' . $row);
                    $row++;
                }
                
                // Tambahkan total di akhir
                $lastRow = $sheet->getHighestRow();
                $sheet->mergeCells('A' . ($lastRow + 1) . ':E' . ($lastRow + 1));
                $sheet->setCellValue('A' . ($lastRow + 1), 'TOTAL:');
                $sheet->getStyle('A' . ($lastRow + 1))->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_RIGHT,
                    ],
                ]);
                
                // Hitung total
                $totalBerat = $data->sum('total_berat');
                $totalHarga = $data->sum('total_harga');
                $totalPoin = $data->sum('total_poin');
                
                $sheet->setCellValue('F' . ($lastRow + 1), number_format($totalBerat, 2, ',', '.'));
                $sheet->setCellValue('G' . ($lastRow + 1), number_format($totalHarga, 0, ',', '.'));
                $sheet->setCellValue('H' . ($lastRow + 1), number_format($totalPoin, 0, ',', '.'));
                
                $sheet->getStyle('A' . ($lastRow + 1) . ':J' . ($lastRow + 1))->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'ECF0F1'],
                    ],
                    'font' => ['bold' => true],
                    'borders' => [
                        'top' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);
                
                // Auto size untuk semua kolom
                foreach(range('A','J') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }
                
                // Set kembali lebar kolom
                foreach($this->columnWidths() as $column => $width) {
                    $sheet->getColumnDimension($column)->setWidth($width);
                }
            },
        ];
    }
}