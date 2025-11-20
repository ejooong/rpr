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

class KomoditasUnggulanAdvancedExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithEvents
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

        // Hitung kontribusi per kecamatan dan group by kecamatan
        $kecamatanTotals = [];
        foreach ($data as $item) {
            $kecamatanId = $item->kecamatan_id;
            if (!isset($kecamatanTotals[$kecamatanId])) {
                $kecamatanTotals[$kecamatanId] = 0;
            }
            $kecamatanTotals[$kecamatanId] += $item->total_produksi;
        }

        // Group data by kecamatan untuk struktur yang lebih baik
        $groupedData = [];
        foreach ($data as $item) {
            $kecamatanId = $item->kecamatan_id;
            $kontribusi = isset($kecamatanTotals[$kecamatanId]) && $kecamatanTotals[$kecamatanId] > 0 
                ? ($item->total_produksi / $kecamatanTotals[$kecamatanId]) * 100 
                : 0;

            if (!isset($groupedData[$kecamatanId])) {
                $groupedData[$kecamatanId] = [
                    'kecamatan' => $item->kecamatan->nama ?? 'Tidak Diketahui',
                    'kabupaten' => $item->kecamatan->kabupaten->nama ?? 'Tidak Diketahui',
                    'provinsi' => $item->kecamatan->kabupaten->provinsi->nama ?? 'Tidak Diketahui',
                    'data' => []
                ];
            }

            $groupedData[$kecamatanId]['data'][] = [
                'komoditas' => $item->komoditas->nama ?? 'Tidak Diketahui',
                'sektor' => $item->komoditas->sektor->nama ?? '-',
                'luas_lahan' => $item->total_luas_lahan,
                'produksi' => $item->total_produksi,
                'produktivitas' => round($item->produktivitas, 2),
                'kontribusi' => round($kontribusi, 2),
                'jumlah_data' => $item->jumlah_data
            ];
        }

        // Simpan grouped data untuk digunakan di mapping
        $this->groupedData = $groupedData;

        // Flatten data untuk collection
        $flattenedData = collect();
        foreach ($groupedData as $kecamatanData) {
            foreach ($kecamatanData['data'] as $item) {
                $flattenedData->push([
                    'kecamatan' => $kecamatanData['kecamatan'],
                    'kabupaten' => $kecamatanData['kabupaten'],
                    'provinsi' => $kecamatanData['provinsi'],
                    ...$item
                ]);
            }
        }

        return $flattenedData->sortBy('kecamatan');
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
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center']
            ],
            
            // Style the header row
            'A1:J1' => [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'color' => ['argb' => 'FF2E86AB'] // Biru tua
                ],
                'font' => [
                    'color' => ['argb' => 'FFFFFFFF'], // Putih
                    'bold' => true
                ]
            ],

            // Style untuk angka (rata kanan)
            'F:J' => [
                'alignment' => ['horizontal' => 'right']
            ],

            // Style untuk header kecamatan
            'A:A' => [
                'font' => ['bold' => true]
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Tambahkan border ke seluruh tabel
                $lastRow = $sheet->getHighestRow();
                $lastColumn = $sheet->getHighestColumn();
                
                $styleArray = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ];
                
                $sheet->getStyle('A1:' . $lastColumn . $lastRow)->applyFromArray($styleArray);
                
                // Auto size columns
                foreach (range('A', $lastColumn) as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }
                
                // Tambahkan judul laporan
                $sheet->insertNewRowBefore(1, 3);
                
                $title = "LAPORAN ANALISIS KOMODITAS UNGGULAN PER KECAMATAN";
                $subtitle = "Tahun: " . $this->tahun;
                
                if ($this->kabupatenId) {
                    $kabupaten = \App\Models\Kabupaten::find($this->kabupatenId);
                    if ($kabupaten) {
                        $subtitle .= " | Kabupaten: " . $kabupaten->nama;
                    }
                }
                
                if ($this->kecamatanId) {
                    $kecamatan = \App\Models\Kecamatan::find($this->kecamatanId);
                    if ($kecamatan) {
                        $subtitle .= " | Kecamatan: " . $kecamatan->nama;
                    }
                }
                
                if ($this->sektorId) {
                    $sektor = \App\Models\Sektor::find($this->sektorId);
                    if ($sektor) {
                        $subtitle .= " | Sektor: " . $sektor->nama;
                    }
                }
                
                $sheet->setCellValue('A1', $title);
                $sheet->setCellValue('A2', $subtitle);
                $sheet->setCellValue('A3', 'Tanggal Export: ' . now()->format('d/m/Y H:i:s'));
                
                // Style untuk judul
                $sheet->mergeCells('A1:J1');
                $sheet->mergeCells('A2:J2');
                $sheet->mergeCells('A3:J3');
                
                $sheet->getStyle('A1:A3')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14
                    ],
                    'alignment' => [
                        'horizontal' => 'center',
                        'vertical' => 'center'
                    ]
                ]);
                
                $sheet->getStyle('A1')->getFont()->setSize(16);
                $sheet->getStyle('A2')->getFont()->setSize(12);
                $sheet->getStyle('A3')->getFont()->setSize(10)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF666666'));
                
                // Warna background untuk judul
                $sheet->getStyle('A1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FF1E3A8A'); // Biru NasDem
                
                $sheet->getStyle('A1')->getFont()->getColor()->setARGB('FFFFFFFF'); // Putih
                
                // Pindah header ke row 4
                $sheet->fromArray($this->headings(), null, 'A4');
                
                // Isi data mulai dari row 5
                $row = 5;
                $currentKecamatan = '';
                
                foreach ($this->groupedData as $kecamatanData) {
                    foreach ($kecamatanData['data'] as $item) {
                        if ($currentKecamatan !== $kecamatanData['kecamatan']) {
                            $currentKecamatan = $kecamatanData['kecamatan'];
                            // Beri warna background untuk baris pertama setiap kecamatan
                            $sheet->getStyle("A{$row}:J{$row}")->getFill()
                                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                                ->getStartColor()->setARGB('FFF0F8FF'); // Biru muda
                        }
                        
                        $sheet->setCellValue("A{$row}", $kecamatanData['kecamatan']);
                        $sheet->setCellValue("B{$row}", $kecamatanData['kabupaten']);
                        $sheet->setCellValue("C{$row}", $kecamatanData['provinsi']);
                        $sheet->setCellValue("D{$row}", $item['komoditas']);
                        $sheet->setCellValue("E{$row}", $item['sektor']);
                        $sheet->setCellValue("F{$row}", number_format($item['luas_lahan'], 2));
                        $sheet->setCellValue("G{$row}", number_format($item['produksi'], 2));
                        $sheet->setCellValue("H{$row}", $item['produktivitas']);
                        $sheet->setCellValue("I{$row}", $item['kontribusi']);
                        $sheet->setCellValue("J{$row}", $item['jumlah_data']);
                        
                        $row++;
                    }
                }
                
                // Format angka dengan separator
                $sheet->getStyle("F5:G{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle("H5:H{$row}")->getNumberFormat()->setFormatCode('0.00');
                $sheet->getStyle("I5:I{$row}")->getNumberFormat()->setFormatCode('0.00');
            },
        ];
    }
}