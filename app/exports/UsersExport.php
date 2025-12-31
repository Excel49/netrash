<?php

namespace App\Exports;

use App\Models\User;
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

class UsersExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithColumnWidths, WithEvents
{
    protected $role_id;
    protected $status;
    
    public function __construct($role_id = null, $status = null)
    {
        $this->role_id = $role_id;
        $this->status = $status;
    }
    
    public function collection()
    {
        $query = User::with('role');
        
        if ($this->role_id) {
            $query->where('role_id', $this->role_id);
        }
        
        if ($this->status) {
            if ($this->status == 'verified') {
                $query->whereNotNull('email_verified_at');
            } elseif ($this->status == 'unverified') {
                $query->whereNull('email_verified_at');
            }
        }
        
        return $query->orderBy('created_at', 'desc')->get();
    }
    
    public function title(): string
    {
        return 'Users';
    }
    
    public function headings(): array
    {
        return [
            'ID USER',
            'NAMA LENGKAP',
            'EMAIL',
            'TELEPON',
            'ROLE',
            'TOTAL POIN',
            'TANGGAL BERGABUNG',
            'EMAIL TERVERIFIKASI',
            'ALAMAT',
            'TERAKHIR DIPERBARUI'
        ];
    }
    
    public function map($user): array
    {
        return [
            $user->id,
            $user->name,
            $user->email,
            $user->phone ?: '-',
            $this->getRoleText($user->role_id),
            number_format($user->total_points, 0, ',', '.'),
            $user->created_at->format('d/m/Y H:i'),
            $user->email_verified_at ? 'YA' : 'TIDAK',
            $user->address ?: '-',
            $user->updated_at->format('d/m/Y H:i')
        ];
    }
    
    private function getRoleText($roleId)
    {
        $roles = [
            1 => 'ADMIN',
            2 => 'PETUGAS',
            3 => 'WARGA'
        ];
        
        return $roles[$roleId] ?? 'UNKNOWN';
    }
    
    public function columnWidths(): array
    {
        return [
            'A' => 10, // ID
            'B' => 25, // Nama
            'C' => 30, // Email
            'D' => 15, // Telepon
            'E' => 12, // Role
            'F' => 15, // Poin
            'G' => 20, // Bergabung
            'H' => 18, // Verified
            'I' => 40, // Alamat
            'J' => 20, // Update
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
        
        $sheet->getStyle('F2:F' . ($sheet->getHighestRow()))->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_RIGHT,
            ],
        ]);
        
        // Style untuk role
        $lastRow = $sheet->getHighestRow();
        for ($row = 2; $row <= $lastRow; $row++) {
            $role = $sheet->getCell('E' . $row)->getValue();
            if ($role == 'ADMIN') {
                $sheet->getStyle('E' . $row)->applyFromArray([
                    'font' => ['color' => ['rgb' => 'E74C3C']],
                ]);
            } elseif ($role == 'PETUGAS') {
                $sheet->getStyle('E' . $row)->applyFromArray([
                    'font' => ['color' => ['rgb' => '27AE60']],
                ]);
            } elseif ($role == 'WARGA') {
                $sheet->getStyle('E' . $row)->applyFromArray([
                    'font' => ['color' => ['rgb' => '3498DB']],
                ]);
            }
            
            // Style untuk verified status
            $verified = $sheet->getCell('H' . $row)->getValue();
            if ($verified == 'YA') {
                $sheet->getStyle('H' . $row)->applyFromArray([
                    'font' => ['color' => ['rgb' => '27AE60']],
                ]);
            } else {
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
                $sheet->mergeCells('A1:J1');
                $sheet->setCellValue('A1', 'LAPORAN DATA PENGGUNA');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 16,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);
                
                // Filter info
                $filterInfo = 'Filter: ';
                $filters = [];
                
                if ($this->role_id) {
                    $roles = [1 => 'Admin', 2 => 'Petugas', 3 => 'Warga'];
                    $filters[] = 'Role: ' . ($roles[$this->role_id] ?? $this->role_id);
                }
                
                if ($this->status) {
                    $statusText = $this->status == 'verified' ? 'Terverifikasi' : 'Belum Terverifikasi';
                    $filters[] = 'Status Email: ' . $statusText;
                }
                
                if (empty($filters)) {
                    $filterInfo .= 'Semua Data';
                } else {
                    $filterInfo .= implode(', ', $filters);
                }
                
                $sheet->mergeCells('A2:J2');
                $sheet->setCellValue('A2', $filterInfo);
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
                
                // Total dan Summary
                $lastRow = $sheet->getHighestRow();
                
                // Total Users
                $sheet->mergeCells('A' . ($lastRow + 2) . ':B' . ($lastRow + 2));
                $sheet->setCellValue('A' . ($lastRow + 2), 'TOTAL PENGGUNA:');
                $sheet->setCellValue('C' . ($lastRow + 2), number_format($data->count(), 0, ',', '.'));
                
                // Total Poin
                $sheet->mergeCells('A' . ($lastRow + 3) . ':B' . ($lastRow + 3));
                $sheet->setCellValue('A' . ($lastRow + 3), 'TOTAL POIN:');
                $sheet->setCellValue('C' . ($lastRow + 3), number_format($data->sum('total_points'), 0, ',', '.'));
                
                // Rata-rata Poin
                $sheet->mergeCells('A' . ($lastRow + 4) . ':B' . ($lastRow + 4));
                $sheet->setCellValue('A' . ($lastRow + 4), 'RATA-RATA POIN:');
                $avgPoin = $data->count() > 0 ? $data->sum('total_points') / $data->count() : 0;
                $sheet->setCellValue('C' . ($lastRow + 4), number_format($avgPoin, 0, ',', '.'));
                
                // Style untuk summary
                $summaryRange = 'A' . ($lastRow + 2) . ':C' . ($lastRow + 4);
                $sheet->getStyle($summaryRange)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'ECF0F1'],
                    ],
                    'font' => ['bold' => true],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'DDDDDD'],
                        ],
                    ],
                ]);
                
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