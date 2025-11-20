<?php

namespace App\Exports;

use App\Models\BpsData;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RankingKomoditasAdvancedExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithEvents
{
    protected $tahun;
    protected $kabupatenId;
    protected $kecamatanId;
    protected $sektorId;
    protected $groupedData = [];

    public function __construct($tahun, $kabupatenId = null, $kecamatanId = null, $sektorId = null)
    {
        $this->tahun = $tahun;
        $this->kabupatenId = $kabupatenId;
        $this->kecamatanId = $kecamatanId;
        $this->sektorId = $sektorId;
    }

    public function collection()
    {
        $query = BpsData::with(['komoditas.sektor', 'kecamatan.kabupaten.provinsi'])
            ->where('tahun', $this->tahun)
            ->selectRaw('
                kecamatan_id,
                komoditas_id,
                SUM(luas_lahan) as total_luas_lahan,
                SUM(produksi) as total_produksi,
                CASE 
                    WHEN SUM(luas_lahan) > 0 THEN SUM(produksi) / SUM(luas_lahan)
                    ELSE 0 
                END as produktivitas
            ')
            ->groupBy('kecamatan_id', 'komoditas_id')
            ->having('total_produksi', '>', 0);

        if ($this->kabupatenId) {
            $query->where('kabupaten_id', $this->kabupatenId);
        }

        if ($this->kecamatanId) {
            $query->where('kecamatan_id', $this->kecamatanId);
        }

        if ($this->sektorId) {
            $query->whereHas('komoditas', function($q) {
                $q->where('sektor_id', $this->sektorId);
            });
        }

        $data = $query->get();

        // Group data by kecamatan dan urutkan berdasarkan produktivitas
        $groupedData = [];
        foreach ($data as $item) {
            $kecamatanId = $item->kecamatan_id;
            
            if (!isset($groupedData[$kecamatanId])) {
                $groupedData[$kecamatanId] = [
                    'kecamatan' => $item->kecamatan->nama ?? 'Tidak Diketahui',
                    'kabupaten' => $item->kecamatan->kabupaten->nama ?? 'Tidak Diketahui',
                    'data' => []
                ];
            }

            $groupedData[$kecamatanId]['data'][] = [
                'komoditas' => $item->komoditas->nama ?? 'Tidak Diketahui',
                'sektor' => $item->komoditas->sektor->nama ?? '-',
                'luas_lahan' => $item->total_luas_lahan,
                'produksi' => $item->total_produksi,
                'produktivitas' => round($item->produktivitas, 2)
            ];
        }

        // Urutkan data per kecamatan berdasarkan produktivitas
        foreach ($groupedData as &$kecamatanData) {
            usort($kecamatanData['data'], function($a, $b) {
                return $b['produktivitas'] <=> $a['produktivitas'];
            });
        }

        $this->groupedData = $groupedData;

        // Flatten data untuk collection
        $flattenedData = collect();
        foreach ($groupedData as $kecamatanData) {
            $rank = 1;
            foreach ($kecamatanData['data'] as $item) {
                $flattenedData->push([
                    'kecamatan' => $kecamatanData['kecamatan'],
                    'kabupaten' => $kecamatanData['kabupaten'],
                    'rank' => $rank++,
                    ...$item
                ]);
            }
        }

        return $flattenedData;
    }

    public function headings(): array
    {
        return [
            'Kecamatan',
            'Kabupaten',
            'Rank',
            'Komoditas',
            'Sektor',
            'Luas Lahan (Ha)',
            'Produksi (Ton)',
            'Produktivitas (Ton/Ha)'
        ];
    }

    public function map($row): array
    {
        return [
            $row['kecamatan'],
            $row['kabupaten'],
            $row['rank'],
            $row['komoditas'],
            $row['sektor'],
            number_format($row['luas_lahan'], 2),
            number_format($row['produksi'], 2),
            $row['produktivitas']
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'A1:H1' => [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'color' => ['argb' => 'FFE6E6FA']
                ]
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Implementasi styling tambahan untuk ranking komoditas
                // ... (serupa dengan KomoditasUnggulanAdvancedExport)
            },
        ];
    }
}