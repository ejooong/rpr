<?php

namespace App\Exports;

use App\Models\Produksi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;

class TrenKomoditasExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $tahunAwal;
    protected $tahunAkhir;
    protected $komoditasId;
    protected $wilayahId;

    public function __construct($tahunAwal, $tahunAkhir, $komoditasId = null, $wilayahId = null)
    {
        $this->tahunAwal = $tahunAwal;
        $this->tahunAkhir = $tahunAkhir;
        $this->komoditasId = $komoditasId;
        $this->wilayahId = $wilayahId;
    }

    public function collection()
    {
        // Gunakan DB raw query untuk menghindari masalah GROUP BY
        $query = "
            SELECT 
                YEAR(tanggal_input) as tahun, 
                komoditas_id,
                SUM(total_produksi) as total_produksi, 
                AVG(produktivitas) as rata_produktivitas
            FROM produksi 
            WHERE tanggal_input BETWEEN ? AND ?
        ";

        $params = ["{$this->tahunAwal}-01-01", "{$this->tahunAkhir}-12-31"];

        if ($this->komoditasId) {
            $query .= " AND komoditas_id = ?";
            $params[] = $this->komoditasId;
        }

        $query .= " GROUP BY YEAR(tanggal_input), komoditas_id";

        $results = DB::select($query, $params);

        // Convert results to collection and load komoditas relationship
        $collection = collect($results);
        
        // Load komoditas data
        $komoditasIds = $collection->pluck('komoditas_id')->unique();
        $komoditas = \App\Models\Komoditas::whereIn('id', $komoditasIds)->get()->keyBy('id');
        
        // Add komoditas relationship to each item
        $collection = $collection->map(function($item) use ($komoditas) {
            $item->komoditas = $komoditas[$item->komoditas_id] ?? null;
            return $item;
        });

        return $collection;
    }

    public function headings(): array
    {
        return [
            'Tahun',
            'Komoditas',
            'Total Produksi (Ton)',
            'Rata-rata Produktivitas (Ton/Ha)',
            'Keterangan',
        ];
    }

    public function map($produksi): array
    {
        $pertumbuhan = $this->calculateGrowth($produksi);
        
        return [
            $produksi->tahun,
            $produksi->komoditas ? $produksi->komoditas->nama : 'Unknown',
            number_format($produksi->total_produksi, 2),
            number_format($produksi->rata_produktivitas, 2),
            $pertumbuhan >= 0 ? "Naik {$pertumbuhan}%" : "Turun " . abs($pertumbuhan) . "%"
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
            
            // Style the header row
            'A1:E1' => [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'color' => ['argb' => 'FF1e3a8a']
                ],
                'font' => [
                    'color' => ['argb' => 'FFFFFFFF']
                ]
            ],
        ];
    }

    private function calculateGrowth($produksi)
    {
        try {
            // Hitung pertumbuhan dari tahun sebelumnya
            $prevYear = $produksi->tahun - 1;
            
            $prevProduksi = DB::select("
                SELECT SUM(total_produksi) as total 
                FROM produksi 
                WHERE YEAR(tanggal_input) = ? AND komoditas_id = ?
            ", [$prevYear, $produksi->komoditas_id]);

            $prevTotal = $prevProduksi[0]->total ?? 0;

            if ($prevTotal > 0) {
                $growth = (($produksi->total_produksi - $prevTotal) / $prevTotal) * 100;
                return round($growth, 2);
            }

            return 0;
        } catch (\Exception $e) {
            return 0;
        }
    }
}