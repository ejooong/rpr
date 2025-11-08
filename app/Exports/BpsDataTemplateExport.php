<?php

namespace App\Exports;

use App\Models\Provinsi;
use App\Models\Sektor;
use App\Models\Komoditas;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BpsDataTemplateExport implements FromArray, WithHeadings, WithStyles, WithTitle
{
    public function array(): array
    {
        // Data contoh untuk template
        return [
            [
                '2024',
                '32', // Contoh kode Jawa Barat
                'JAWA BARAT',
                '3273', // Contoh kode Kota Bandung  
                'KOTA BANDUNG',
                '3273010', // Contoh kode Kecamatan Andir
                'ANDIR',
                'A1',
                'Tanaman Pangan',
                'PADI',
                'Padi',
                '100.5',
                '500.25',
                '4.98',
                '1',
                'BPS',
                'Data contoh - hapus baris ini saat mengisi data'
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'tahun',
            'kode_provinsi', 
            'nama_provinsi',
            'kode_kabupaten',
            'nama_kabupaten',
            'kode_kecamatan', 
            'nama_kecamatan',
            'kode_sektor',
            'nama_sektor',
            'kode_komoditas',
            'nama_komoditas',
            'luas_lahan',
            'produksi',
            'produktivitas',
            'peringkat_wilayah',
            'sumber_data',
            'keterangan'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style untuk header
        $sheet->getStyle('A1:Q1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['rgb' => '2E86C1']
            ]
        ]);

        // Auto size columns
        foreach(range('A', 'Q') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Style untuk data contoh
        $sheet->getStyle('A2:Q2')->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['rgb' => 'F9E79F']
            ]
        ]);

        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Template Data BPS';
    }
}