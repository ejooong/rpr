<?php

namespace App\Exports;

use App\Models\BpsData;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RankingKomoditasAdvancedExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $tahun;
    protected $kabupatenId;
    protected $kecamatanId;
    protected $sektorId;

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

        // Format data dan beri ranking per kecamatan
        $groupedData = collect();
        foreach ($data as $item) {
            $kecamatanId = $item->kecamatan_id;
            
            $groupedData->push([
                'kecamatan' => $item->kecamatan->nama ?? 'Tidak Diketahui',
                'kabupaten' => $item->kecamatan->kabupaten->nama ?? 'Tidak Diketahui',
                'komoditas' => $item->komoditas->nama ?? 'Tidak Diketahui',
                'sektor' => $item->komoditas->sektor->nama ?? '-',
                'luas_lahan' => $item->total_luas_lahan,
                'produksi' => $item->total_produksi,
                'produktivitas' => round($item->produktivitas, 2)
            ]);
        }

        return $groupedData->sortByDesc('produktivitas');
    }

    public function headings(): array
    {
        return [
            'Kecamatan',
            'Kabupaten',
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
            'A1:G1' => [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'color' => ['argb' => 'FFE6E6FA']
                ]
            ],
        ];
    }
}