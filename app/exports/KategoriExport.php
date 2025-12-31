<?php

namespace App\Exports;

use App\Models\KategoriSampah;
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

class KategoriExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithColumnWidths, WithEvents
{
    public function collection()
    {
        return KategoriSampah::withCount(['detailTransaksi as total_transaksi'])
            ->withSum('detailTransaksi as total_berat', 'berat')
            ->withSum('detailTransaksi as total_poin', 'poin')
            ->withSum('detailTransaksi as total_harga', 'harga')
            ->get();
    }
    
    public function title(): string
    {
        return 'Kategori';
    }
    
    public function headings(): array
    {
        return [
            'KODE KATEGORI',
            'NAMA KATEGORI',
            'SATUAN',
            'HARGA PER KG (RP)',
            'POIN PER KG',
            'TOTAL TRANSAKSI',
            'TOTAL BERAT (KG)',
            'TOTAL POIN',
            'TOTAL HARGA (RP)',
            'RATA-RATA BERAT PER TRANSAKSI'
        ];
    }
    
    public function map($kategori): array
    {
        $avgBerat = $kategori->total_transaksi > 0 
            ? $kategori->total_berat / $kategori->total_transaksi 
            : 0;
        
        return [
            $kategori->kode_kategori,
            $kategori->nama_kategori,
            $kategori->satuan,
            number_format($kategori->harga_per_kg, 0, ',', '.'),
            number_format($kategori->poin_per_kg, 0, ',', '.'),
            number_format($kategori->total_transaksi, 0, ',', '.'),
            number_format($kategori->total_berat, 2, ',', '.'),
            number_format($kategori->total_poin, 0, ',', '.'),
            number_format($kategori->total_harga, 0, ',', '.'),
            number_format($avgBerat, 2, ',', '.')
        ];
    }
    
    public function columnWidths(): array
    {
        return [
            'A' => 15, // Kode
            'B' => 25, // Nama
            'C' => 10, // Satuan
            'D' => 18, // Harga
            'E' => 15, // Poin
            'F' => 15, // Total Transaksi
            'G' => 15, // Total Berat
            'H' => 15, // Total Poin
            'I' => 18, // Total Harga
            'J' => 25, // Rata-rata
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
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
        
        $sheet->setAutoFilter('A1:J1');
        
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
        $sheet->getStyle('D2:J' . ($sheet->getHighestRow()))->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_RIGHT,
            ],
        ]);
        
        // Style untuk header satuan (rata tengah)
        $sheet->getStyle('C2:C' . ($sheet->getHighestRow()))->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);
        
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
                $sheet->mergeCells('A1:J1');
                $sheet->setCellValue('A1', 'LAPORAN KATEGORI SAMPAH');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 16,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);
                
                // Subtitle
                $sheet->mergeCells('A2:J2');
                $sheet->setCellValue('A2', 'Statistik Berdasarkan Transaksi');
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
                $sheet->mergeCells('A' . ($lastRow + 1) . ':F' . ($lastRow + 1));
                $sheet->setCellValue('A' . ($lastRow + 1), 'TOTAL:');
                $sheet->getStyle('A' . ($lastRow + 1))->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_RIGHT,
                    ],
                ]);
                
                $totalBerat = $data->sum('total_berat');
                $totalPoin = $data->sum('total_poin');
                $totalHarga = $data->sum('total_harga');
                $totalTransaksi = $data->sum('total_transaksi');
                
                $sheet->setCellValue('G' . ($lastRow + 1), number_format($totalBerat, 2, ',', '.'));
                $sheet->setCellValue('H' . ($lastRow + 1), number_format($totalPoin, 0, ',', '.'));
                $sheet->setCellValue('I' . ($lastRow + 1), number_format($totalHarga, 0, ',', '.'));
                
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
                
                // Summary
                $sheet->mergeCells('A' . ($lastRow + 3) . ':B' . ($lastRow + 3));
                $sheet->setCellValue('A' . ($lastRow + 3), 'TOTAL KATEGORI:');
                $sheet->setCellValue('C' . ($lastRow + 3), number_format($data->count(), 0, ',', '.'));
                
                $sheet->mergeCells('A' . ($lastRow + 4) . ':B' . ($lastRow + 4));
                $sheet->setCellValue('A' . ($lastRow + 4), 'RATA-RATA BERAT PER KATEGORI:');
                $avgBeratPerKategori = $data->count() > 0 ? $totalBerat / $data->count() : 0;
                $sheet->setCellValue('C' . ($lastRow + 4), number_format($avgBeratPerKategori, 2, ',', '.') . ' kg');
                
                $sheet->mergeCells('A' . ($lastRow + 5) . ':B' . ($lastRow + 5));
                $sheet->setCellValue('A' . ($lastRow + 5), 'RATA-RATA TRANSAKSI PER KATEGORI:');
                $avgTransaksiPerKategori = $data->count() > 0 ? $totalTransaksi / $data->count() : 0;
                $sheet->setCellValue('C' . ($lastRow + 5), number_format($avgTransaksiPerKategori, 0, ',', '.'));
                
                foreach(range('A','J') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }
                
                foreach($this->columnWidths() as $column => $width) {
                    $sheet->getColumnDimension($column)->setWidth($width);
                }
            },
        ];
    }
}