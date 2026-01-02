<?php

namespace App\Exports;

use App\Models\Transaksi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class PetugasStatsExport implements FromCollection, WithHeadings, WithTitle
{
    protected $petugasId;
    
    public function __construct($petugasId)
    {
        $this->petugasId = $petugasId;
    }
    
    public function collection()
    {
        $stats = [
            [
                'Metric' => 'Transaksi Hari Ini',
                'Value' => Transaksi::where('petugas_id', $this->petugasId)
                    ->whereDate('created_at', today())
                    ->count()
            ],
            [
                'Metric' => 'Transaksi Bulan Ini',
                'Value' => Transaksi::where('petugas_id', $this->petugasId)
                    ->whereMonth('created_at', now()->month)
                    ->count()
            ],
            [
                'Metric' => 'Berat Sampah Hari Ini (kg)',
                'Value' => Transaksi::where('petugas_id', $this->petugasId)
                    ->whereDate('created_at', today())
                    ->sum('total_berat') ?? 0
            ],
            [
                'Metric' => 'Berat Sampah Bulan Ini (kg)',
                'Value' => Transaksi::where('petugas_id', $this->petugasId)
                    ->whereMonth('created_at', now()->month)
                    ->sum('total_berat') ?? 0
            ],
            [
                'Metric' => 'Poin Diberikan Hari Ini',
                'Value' => Transaksi::where('petugas_id', $this->petugasId)
                    ->whereDate('created_at', today())
                    ->sum('total_poin') ?? 0
            ],
            [
                'Metric' => 'Poin Diberikan Bulan Ini',
                'Value' => Transaksi::where('petugas_id', $this->petugasId)
                    ->whereMonth('created_at', now()->month)
                    ->sum('total_poin') ?? 0
            ],
        ];
        
        return collect($stats);
    }
    
    public function headings(): array
    {
        return [
            'Metrik',
            'Nilai'
        ];
    }
    
    public function title(): string
    {
        return 'Statistik Petugas';
    }
}