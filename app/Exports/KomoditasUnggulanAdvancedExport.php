<?php

namespace App\Exports;

use App\Models\BpsData;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KomoditasUnggulanAdvancedExport implements FromCollection, WithHeadings, WithMapping, WithStyles
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
                END as produktivitas,
                COUNT(DISTINCT id) as jumlah_data
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

        // Hitung kontribusi per kecamatan
        $kecamatanTotals = [];
        foreach ($data as $item) {
            $kecamatanId = $item->kecamatan_id;
            if (!isset($kecamatanTotals[$kecamatanId])) {
                $kecamatanTotals[$kecamatanId] = 0;
            }
            $kecamatanTotals[$kecamatanId] += $item->total_produksi;
        }

        // Format data
        $result = collect();
        foreach ($data as $item) {
            $kontribusi = isset($kecamatanTotals[$item->kecamatan_id]) && $kecamatanTotals[$item->kecamatan_id] > 0 
                ? ($item->total_produksi / $kecamatanTotals[$item->kecamatan_id]) * 100 
                : 0;

            $result->push([
                'kecamatan' => $item->kecamatan->nama ?? 'Tidak Diketahui',
                'kabupaten' => $item->kecamatan->kabupaten->nama ?? 'Tidak Diketahui',
                'provinsi' => $item->kecamatan->kabupaten->provinsi->nama ?? 'Tidak Diketahui',
                'komoditas' => $item->komoditas->nama ?? 'Tidak Diketahui',
                'sektor' => $item->komoditas->sektor->nama ?? '-',
                'luas_lahan' => $item->total_luas_lahan,
                'produksi' => $item->total_produksi,
                'produktivitas' => round($item->produktivitas, 2),
                'kontribusi' => round($kontribusi, 2),
                'jumlah_data' => $item->jumlah_data
            ]);
        }

        return $result->sortBy('kecamatan');
    }

    public function headings(): array
    {
        return [
            'Kecamatan',
            'Kabupaten',
            'Provinsi',
            'Komoditas',
            'Sektor',
            'Luas Lahan (Ha)',
            'Produksi (Ton)',
            'Produktivitas (Ton/Ha)',
            'Kontribusi (%)',
            'Jumlah Data'
        ];
    }

    public function map($row): array
    {
        return [
            $row['kecamatan'],
            $row['kabupaten'],
            $row['provinsi'],
            $row['komoditas'],
            $row['sektor'],
            number_format($row['luas_lahan'], 2),
            number_format($row['produksi'], 2),
            $row['produktivitas'],
            $row['kontribusi'],
            $row['jumlah_data']
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
            
            // Style the header row
            'A1:J1' => [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'color' => ['argb' => 'FFE6E6FA']
                ]
            ],
        ];
    }
}