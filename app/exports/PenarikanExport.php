<?php

namespace App\Exports;

use App\Models\PenarikanPoin;
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

class PenarikanExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithColumnWidths, WithEvents
{
    protected $startDate;
    protected $endDate;
    protected $status;
    
    public function __construct($startDate = null, $endDate = null, $status = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->status = $status;
    }
    
    public function collection()
    {
        $query = PenarikanPoin::with(['warga', 'admin']);
        
        if ($this->startDate && $this->endDate) {
            $query->whereBetween('created_at', [
                $this->startDate . ' 00:00:00',
                $this->endDate . ' 23:59:59'
            ]);
        }
        
        if ($this->status) {
            $query->where('status', $this->status);
        }
        
        return $query->get();
    }
    
    public function title(): string
    {
        return 'Penarikan';
    }
    
    public function headings(): array
    {
        return [
            'ID PENARIKAN',
            'TANGGAL PENGAJUAN',
            'NAMA WARGA',
            'EMAIL WARGA',
            'TELEPON WARGA',
            'JUMLAH POIN',
            'NILAI RUPIAH (RP)',
            'STATUS',
            'TANGGAL APPROVAL',
            'ADMIN APPROVAL',
            'ALASAN PENARIKAN'
        ];
    }
    
    public function map($penarikan): array
    {
        return [
            $penarikan->id,
            $penarikan->tanggal_pengajuan->format('d/m/Y H:i'),
            $penarikan->warga->name,
            $penarikan->warga->email,
            $penarikan->warga->phone ?? '-',
            number_format($penarikan->jumlah_poin, 0, ',', '.'),
            number_format($penarikan->jumlah_rupiah, 0, ',', '.'),
            $this->getStatusText($penarikan->status),
            $penarikan->tanggal_approval ? $penarikan->tanggal_approval->format('d/m/Y H:i') : '-',
            $penarikan->admin ? $penarikan->admin->name : '-',
            $penarikan->alasan_penarikan ?: '-'
        ];
    }
    
    private function getStatusText($status)
    {
        $statuses = [
            'pending' => 'PENDING',
            'approved' => 'DISETUJUI',
            'completed' => 'SELESAI',
            'rejected' => 'DITOLAK'
        ];
        
        return $statuses[$status] ?? strtoupper($status);
    }
    
    public function columnWidths(): array
    {
        return [
            'A' => 12, // ID
            'B' => 20, // Tanggal
            'C' => 25, // Nama Warga
            'D' => 25, // Email
            'E' => 15, // Telepon
            'F' => 15, // Poin
            'G' => 20, // Nilai
            'H' => 12, // Status
            'I' => 20, // Tanggal Approval
            'J' => 25, // Admin
            'K' => 30, // Alasan
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:K1')->applyFromArray([
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
        
        $sheet->setAutoFilter('A1:K1');
        
        $sheet->getStyle('A2:K' . ($sheet->getHighestRow()))->applyFromArray([
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
        
        $sheet->getStyle('F2:G' . ($sheet->getHighestRow()))->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_RIGHT,
            ],
        ]);
        
        // Style untuk status
        $lastRow = $sheet->getHighestRow();
        for ($row = 2; $row <= $lastRow; $row++) {
            $status = $sheet->getCell('H' . $row)->getValue();
            if ($status == 'SELESAI') {
                $sheet->getStyle('H' . $row)->applyFromArray([
                    'font' => ['color' => ['rgb' => '27AE60']],
                ]);
            } elseif ($status == 'DISETUJUI') {
                $sheet->getStyle('H' . $row)->applyFromArray([
                    'font' => ['color' => ['rgb' => '3498DB']],
                ]);
            } elseif ($status == 'PENDING') {
                $sheet->getStyle('H' . $row)->applyFromArray([
                    'font' => ['color' => ['rgb' => 'F39C12']],
                ]);
            } elseif ($status == 'DITOLAK') {
                $sheet->getStyle('H' . $row)->applyFromArray([
                    'font' => ['color' => ['rgb' => 'E74C3C']],
                ]);
            }
        }
        
        $sheet->getRowDimension(1)->setRowHeight(25);
        
        return [];
    }
    
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->insertNewRowBefore(1, 4);
                
                // Judul
                $sheet->mergeCells('A1:K1');
                $sheet->setCellValue('A1', 'LAPORAN PENARIKAN POIN');
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
                
                $sheet->mergeCells('A2:K2');
                $sheet->setCellValue('A2', $period);
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['size' => 12],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);
                
                // Tanggal Export
                $sheet->mergeCells('A3:K3');
                $sheet->setCellValue('A3', 'Tanggal Export: ' . date('d/m/Y H:i:s'));
                $sheet->getStyle('A3')->applyFromArray([
                    'font' => ['size' => 10, 'italic' => true],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);
                
                // Header
                $sheet->fromArray($this->headings(), NULL, 'A5');
                
                // Data
                $data = $this->collection();
                $row = 6;
                foreach ($data as $item) {
                    $sheet->fromArray($this->map($item), NULL, 'A' . $row);
                    $row++;
                }
                
                // Total
                $lastRow = $sheet->getHighestRow();
                $sheet->mergeCells('A' . ($lastRow + 1) . ':E' . ($lastRow + 1));
                $sheet->setCellValue('A' . ($lastRow + 1), 'TOTAL:');
                $sheet->getStyle('A' . ($lastRow + 1))->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_RIGHT,
                    ],
                ]);
                
                $totalPoin = $data->sum('jumlah_poin');
                $totalRupiah = $data->sum('jumlah_rupiah');
                
                $sheet->setCellValue('F' . ($lastRow + 1), number_format($totalPoin, 0, ',', '.'));
                $sheet->setCellValue('G' . ($lastRow + 1), number_format($totalRupiah, 0, ',', '.'));
                
                $sheet->getStyle('A' . ($lastRow + 1) . ':K' . ($lastRow + 1))->applyFromArray([
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
                
                foreach(range('A','K') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }
                
                foreach($this->columnWidths() as $column => $width) {
                    $sheet->getColumnDimension($column)->setWidth($width);
                }
            },
        ];
    }
}