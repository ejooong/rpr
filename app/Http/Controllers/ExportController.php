<?php
// app/Http/Controllers/ExportController.php
namespace App\Http\Controllers;

use App\Models\Produksi;
use App\Models\Demplot;
use App\Models\Petani;
use App\Models\Poktan;
use App\Models\Komoditas;
use Illuminate\Http\Request;
use App\Exports\TrenKomoditasExport;
use Maatwebsite\Excel\Facades\Excel;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Response;
use App\Models\BpsData; // TAMBAHKAN INI
use Barryvdh\DomPDF\Facade\Pdf;
class ExportController extends Controller
{
    public function exportProduksiExcel(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));
        $bulan = $request->get('bulan');
        
        // Generate CSV sebagai alternatif Excel
        $fileName = "produksi-{$tahun}.csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $callback = function() use ($tahun, $bulan) {
            $file = fopen('php://output', 'w');
            
            // Header CSV
            fputcsv($file, ['Tanggal', 'Komoditas', 'Total Produksi (Ton)', 'Produktivitas (Ton/Ha)', 'Luas Lahan (Ha)']);
            
            // Query data
            $query = Produksi::with(['komoditas'])
                ->whereYear('tanggal_input', $tahun);

            if ($bulan) {
                $query->whereMonth('tanggal_input', $bulan);
            }

            $produksi = $query->get();
            
            // Data
            foreach ($produksi as $item) {
                fputcsv($file, [
                    $item->tanggal_input,
                    $item->komoditas->nama,
                    number_format($item->total_produksi, 2),
                    number_format($item->produktivitas, 2),
                    number_format($item->luas_lahan, 2)
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportProduksiPDF(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));
        $bulan = $request->get('bulan');
        
        $query = Produksi::with(['demplot', 'komoditas'])
            ->whereYear('tanggal_input', $tahun);

        if ($bulan) {
            $query->whereMonth('tanggal_input', $bulan);
        }

        $produksi = $query->get();
        $totalProduksi = $produksi->sum('total_produksi');

        // Generate HTML untuk PDF
        $html = $this->generateProduksiPDFHtml($produksi, $tahun, $bulan, $totalProduksi);
        
        return Response::make($html, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"produksi-{$tahun}.pdf\""
        ]);
    }

    public function exportDemplotExcel(Request $request)
    {
        $status = $request->get('status');
        
        // Generate CSV sebagai alternatif Excel
        $fileName = "demplot.csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $callback = function() use ($status) {
            $file = fopen('php://output', 'w');
            
            // Header CSV
            fputcsv($file, ['Nama Demplot', 'Komoditas', 'Lokasi', 'Status', 'Luas Lahan (Ha)', 'Tanggal Mulai']);
            
            // Query data
            $query = Demplot::with(['komoditas', 'provinsi', 'kabupaten', 'kecamatan', 'desa']);

            if ($status) {
                $query->where('status', $status);
            }

            $demplot = $query->get();
            
            // Data
            foreach ($demplot as $item) {
                $lokasi = $item->desa->nama . ', ' . $item->kecamatan->nama . ', ' . $item->kabupaten->nama;
                
                fputcsv($file, [
                    $item->nama_demplot,
                    $item->komoditas->nama,
                    $lokasi,
                    $item->status,
                    number_format($item->luas_lahan, 2),
                    $item->tanggal_mulai
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportDemplotPDF(Request $request)
    {
        $status = $request->get('status');
        
        $query = Demplot::with(['petani', 'komoditas', 'provinsi', 'kabupaten', 'kecamatan', 'desa']);

        if ($status) {
            $query->where('status', $status);
        }

        $demplot = $query->get();

        // Generate HTML untuk PDF
        $html = $this->generateDemplotPDFHtml($demplot, $status);
        
        return Response::make($html, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"demplot.pdf\""
        ]);
    }

    public function exportPetaniExcel(Request $request)
    {
        $aktif = $request->get('aktif', true);
        
        // Generate CSV sebagai alternatif Excel
        $fileName = "petani.csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $callback = function() use ($aktif) {
            $file = fopen('php://output', 'w');
            
            // Header CSV
            fputcsv($file, ['Nama Petani', 'Poktan', 'Alamat', 'Luas Lahan (Ha)', 'Status', 'No. Telepon']);
            
            // Query data
            $query = Petani::with(['poktan', 'provinsi', 'kabupaten', 'kecamatan', 'desa'])
                ->where('aktif', $aktif);

            $petani = $query->get();
            
            // Data
            foreach ($petani as $item) {
                $alamat = $item->desa->nama . ', ' . $item->kecamatan->nama . ', ' . $item->kabupaten->nama;
                $status = $item->aktif ? 'Aktif' : 'Tidak Aktif';
                
                fputcsv($file, [
                    $item->nama_petani,
                    $item->poktan->nama_poktan ?? '-',
                    $alamat,
                    number_format($item->luas_lahan_garap, 2),
                    $status,
                    $item->no_telepon ?? '-'
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPetaniPDF(Request $request)
    {
        $aktif = $request->get('aktif', true);
        
        $query = Petani::with(['poktan', 'provinsi', 'kabupaten', 'kecamatan', 'desa'])
            ->where('aktif', $aktif);

        $petani = $query->get();
        $totalPetani = $petani->count();
        $totalLahan = $petani->sum('luas_lahan_garap');

        // Generate HTML untuk PDF
        $html = $this->generatePetaniPDFHtml($petani, $aktif, $totalPetani, $totalLahan);
        
        return Response::make($html, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"petani.pdf\""
        ]);
    }

    public function exportPoktanExcel(Request $request)
    {
        // Generate CSV sebagai alternatif Excel
        $fileName = "poktan.csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // Header CSV
            fputcsv($file, ['Nama Poktan', 'Ketua', 'Alamat', 'Jumlah Anggota', 'No. Telepon', 'Tanggal Berdiri']);
            
            // Query data
            $poktan = Poktan::with(['provinsi', 'kabupaten', 'kecamatan', 'desa', 'petani'])->get();
            
            // Data
            foreach ($poktan as $item) {
                $alamat = $item->desa->nama . ', ' . $item->kecamatan->nama . ', ' . $item->kabupaten->nama;
                $jumlahAnggota = $item->petani->where('aktif', true)->count();
                
                fputcsv($file, [
                    $item->nama_poktan,
                    $item->nama_ketua,
                    $alamat,
                    $jumlahAnggota,
                    $item->no_telepon ?? '-',
                    $item->tanggal_berdiri
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPoktanPDF(Request $request)
    {
        $poktan = Poktan::with(['provinsi', 'kabupaten', 'kecamatan', 'desa', 'petani'])->get();

        // Generate HTML untuk PDF
        $html = $this->generatePoktanPDFHtml($poktan);
        
        return Response::make($html, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"poktan.pdf\""
        ]);
    }

    public function exportKomoditasUnggulan(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));
        
        // Generate CSV sebagai alternatif Excel
        $fileName = "komoditas-unggulan-{$tahun}.csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $callback = function() use ($tahun) {
            $file = fopen('php://output', 'w');
            
            // Header CSV
            fputcsv($file, ['Komoditas', 'Total Produksi (Ton)', 'Rata-rata Produktivitas (Ton/Ha)', 'Status']);
            
            // Query data
            $trendKomoditas = Produksi::join('komoditas', 'produksi.komoditas_id', '=', 'komoditas.id')
                ->where('komoditas.status_unggulan', true)
                ->whereYear('produksi.tanggal_input', $tahun)
                ->select('komoditas.nama', \DB::raw('SUM(produksi.total_produksi) as total'), \DB::raw('AVG(produksi.produktivitas) as produktivitas'))
                ->groupBy('komoditas.id', 'komoditas.nama')
                ->orderByDesc('total')
                ->get();
            
            // Data
            foreach ($trendKomoditas as $item) {
                fputcsv($file, [
                    $item->nama,
                    number_format($item->total, 2),
                    number_format($item->produktivitas, 2),
                    'Unggulan'
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportDashboardSummary(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));
        
        // Data statistik
        $stats = [
            'total_produksi' => Produksi::whereYear('tanggal_input', $tahun)->sum('total_produksi'),
            'total_petani' => Petani::where('aktif', true)->count(),
            'total_demplot' => Demplot::count(),
            'total_poktan' => Poktan::count(),
            'total_komoditas' => Komoditas::where('aktif', true)->count(),
        ];

        // Produksi per sektor
        $produksiPerSektor = Produksi::join('komoditas', 'produksi.komoditas_id', '=', 'komoditas.id')
            ->join('sektor', 'komoditas.sektor_id', '=', 'sektor.id')
            ->whereYear('produksi.tanggal_input', $tahun)
            ->select('sektor.nama as sektor', \DB::raw('SUM(produksi.total_produksi) as total'))
            ->groupBy('sektor.id', 'sektor.nama')
            ->get();

        // Generate HTML untuk PDF
        $html = $this->generateDashboardSummaryPDFHtml($stats, $produksiPerSektor, $tahun);
        
        return Response::make($html, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"dashboard-summary-{$tahun}.pdf\""
        ]);
    }

    public function exportLaporanTren(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));
        
        $trendKomoditas = Produksi::join('komoditas', 'produksi.komoditas_id', '=', 'komoditas.id')
            ->where('komoditas.status_unggulan', true)
            ->whereYear('produksi.tanggal_input', $tahun)
            ->select('komoditas.nama', \DB::raw('SUM(produksi.total_produksi) as total'))
            ->groupBy('komoditas.id', 'komoditas.nama')
            ->orderByDesc('total')
            ->get();

        // Generate HTML untuk PDF
        $html = $this->generateLaporanTrenPDFHtml($trendKomoditas, $tahun);
        
        return Response::make($html, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"laporan-tren-{$tahun}.pdf\""
        ]);
    }

    public function exportTrenKomoditasExcel(Request $request)
    {
        $tahunAwal = $request->get('tahun_awal', date('Y') - 5);
        $tahunAkhir = $request->get('tahun_akhir', date('Y'));
        $komoditasId = $request->get('komoditas_id');
        $wilayahId = $request->get('wilayah_id');
        
        return Excel::download(
            new TrenKomoditasExport($tahunAwal, $tahunAkhir, $komoditasId, $wilayahId), 
            "tren-komoditas-{$tahunAwal}-{$tahunAkhir}.xlsx"
        );
    }   

    public function exportTrenKomoditasPDF(Request $request)
    {
        $tahunAwal = $request->get('tahun_awal', date('Y') - 5);
        $tahunAkhir = $request->get('tahun_akhir', date('Y'));
        $komoditasId = $request->get('komoditas_id');
        $wilayahId = $request->get('wilayah_id');
        
        // Ambil data tren komoditas
        $trenData = $this->getTrenKomoditasData($tahunAwal, $tahunAkhir, $komoditasId, $wilayahId);
        $komoditasList = Komoditas::where('aktif', true)->get();

        // Generate HTML untuk PDF
        $html = $this->generateTrenKomoditasPDFHtml($trenData, $tahunAwal, $tahunAkhir, $komoditasList);
        
        return Response::make($html, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"tren-komoditas-{$tahunAwal}-{$tahunAkhir}.pdf\""
        ]);
    }

    /**
     * Export BPS Data to Excel - FORMAT EXCEL ASLI
     */
   /**
 * Export BPS Data to Excel - FORMAT EXCEL ASLI
 */
public function exportBpsDataExcel(Request $request)
{
    // Default ke tahun tertinggi yang ada atau dari request
    $defaultYear = BpsData::max('tahun') ?? date('Y');
    $tahun = $request->get('tahun', $defaultYear);
    $provinsiId = $request->get('provinsi_id');
    
    // Generate file Excel asli
    $fileName = "bps-data-{$tahun}.xlsx";
    
    // Query data BPS dengan filter yang sama seperti dashboard
    $query = BpsData::with([
        'provinsi:id,nama',
        'kabupaten:id,nama', 
        'kecamatan:id,nama',
        'sektor:id,nama',
        'komoditas:id,nama'
    ])->where('tahun', $tahun);

    // Filter provinsi jika dipilih
    if ($provinsiId) {
        $query->where('provinsi_id', $provinsiId);
    }

    // Tambahkan filter kabupaten dan kecamatan jika ada di request
    if ($request->has('kabupaten_id') && $request->kabupaten_id) {
        $query->where('kabupaten_id', $request->kabupaten_id);
    }

    if ($request->has('kecamatan_id') && $request->kecamatan_id) {
        $query->where('kecamatan_id', $request->kecamatan_id);
    }

    $bpsData = $query->get();
    
    // Jika tidak ada data, return CSV dengan pesan
    if ($bpsData->isEmpty()) {
        $fileName = "bps-data-{$tahun}.csv";
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $callback = function() use ($tahun) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['INFO: Tidak ada data BPS untuk tahun ' . $tahun]);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Jika ada data, generate Excel
    return Excel::download(new class($bpsData, $tahun) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithMapping, \Maatwebsite\Excel\Concerns\WithStyles, \Maatwebsite\Excel\Concerns\WithTitle {
        
        protected $bpsData;
        protected $tahun;

        public function __construct($bpsData, $tahun)
        {
            $this->bpsData = $bpsData;
            $this->tahun = $tahun;
        }

        public function collection()
        {
            return $this->bpsData;
        }

        public function title(): string
        {
            return 'Data BPS ' . $this->tahun;
        }

        public function headings(): array
        {
            return [
                'TAHUN',
                'PROVINSI', 
                'KABUPATEN/KOTA',
                'KECAMATAN',
                'SEKTOR',
                'KOMODITAS',
                'LUAS LAHAN (Ha)',
                'PRODUKSI (Ton)',
                'PRODUKTIVITAS (Ton/Ha)',
                'STATUS UNGGULAN',
                'SUMBER DATA'
            ];
        }

        public function map($bpsData): array
        {
            return [
                $bpsData->tahun,
                $bpsData->provinsi->nama ?? 'PROVINSI',
                $bpsData->kabupaten->nama ?? 'NASIONAL', 
                $bpsData->kecamatan->nama ?? 'KABUPATEN',
                $bpsData->sektor->nama ?? '-',
                $bpsData->komoditas->nama ?? '-',
                $bpsData->luas_lahan ?? 0,
                $bpsData->produksi ?? 0,
                $bpsData->produktivitas ?? 0,
                $bpsData->status_unggulan ? 'UNGULAN' : 'BIASA',
                $bpsData->sumber_data ?? 'BPS'
            ];
        }

        public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
        {
            // Style untuk header
            $sheet->getStyle('A1:K1')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'color' => ['rgb' => '1e3a8a'], // Biru NasDem
                ],
            ]);

            // Auto size columns
            foreach(range('A', 'K') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }

            // Format angka untuk kolom numerik
            $sheet->getStyle('G2:G' . ($sheet->getHighestRow()))
                  ->getNumberFormat()
                  ->setFormatCode('#,##0.00');
                  
            $sheet->getStyle('H2:H' . ($sheet->getHighestRow()))
                  ->getNumberFormat()
                  ->setFormatCode('#,##0.00');
                  
            $sheet->getStyle('I2:I' . ($sheet->getHighestRow()))
                  ->getNumberFormat()
                  ->setFormatCode('0.00');

            // Border untuk semua data
            $sheet->getStyle('A1:K' . ($sheet->getHighestRow()))
                  ->getBorders()
                  ->getAllBorders()
                  ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            return [];
        }
        
    }, $fileName);
}

/**
 * Export BPS Data to PDF - USING LARAVEL DOMPDF PACKAGE
 */
public function exportBpsDataPDF(Request $request)
{
    // Default ke tahun tertinggi yang ada atau dari request
    $defaultYear = BpsData::max('tahun') ?? date('Y');
    $tahun = $request->get('tahun', $defaultYear);
    $provinsiId = $request->get('provinsi_id');
    
    $query = BpsData::with(['provinsi', 'kabupaten', 'kecamatan', 'sektor', 'komoditas'])
        ->where('tahun', $tahun);

    if ($provinsiId) {
        $query->where('provinsi_id', $provinsiId);
    }

    // Tambahkan filter kabupaten dan kecamatan jika ada di request
    if ($request->has('kabupaten_id') && $request->kabupaten_id) {
        $query->where('kabupaten_id', $request->kabupaten_id);
    }

    if ($request->has('kecamatan_id') && $request->kecamatan_id) {
        $query->where('kecamatan_id', $request->kecamatan_id);
    }

    $bpsData = $query->get();
    
    $data = [
        'bpsData' => $bpsData,
        'tahun' => $tahun,
        'totalProduksi' => $bpsData->sum('produksi'),
        'totalLuasLahan' => $bpsData->sum('luas_lahan'),
        'provinsiFilter' => $provinsiId ? \App\Models\Provinsi::find($provinsiId) : null,
        'kabupatenFilter' => $request->kabupaten_id ? \App\Models\Kabupaten::find($request->kabupaten_id) : null,
        'kecamatanFilter' => $request->kecamatan_id ? \App\Models\Kecamatan::find($request->kecamatan_id) : null,
    ];

    $pdf = Pdf::loadView('exports.bps-data-pdf', $data)
              ->setPaper('a4', 'landscape')
              ->setOption('defaultFont', 'Arial');

    return $pdf->download("bps-data-{$tahun}.pdf");
}
    /**
     * Export BPS Komoditas Unggulan to Excel
     */
    public function exportBpsKomoditasUnggulanExcel(Request $request)
    {
        // Default ke tahun tertinggi yang ada
        $defaultYear = BpsData::max('tahun') ?? 2024;
        $tahun = $request->get('tahun', $defaultYear);
        
        // Generate CSV sebagai alternatif Excel
        $fileName = "bps-komoditas-unggulan-{$tahun}.csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $callback = function() use ($tahun) {
            $file = fopen('php://output', 'w');
            
            // Header CSV
            fputcsv($file, [
                'Komoditas', 
                'Total Produksi (Ton)', 
                'Rata-rata Produktivitas (Ton/Ha)', 
                'Jumlah Wilayah',
                'Status'
            ]);
            
            // Cek apakah ada data untuk tahun ini
            $dataExists = BpsData::where('tahun', $tahun)->exists();
            
            if (!$dataExists) {
                fputcsv($file, ["Tidak ada data BPS untuk tahun {$tahun}"]);
            } else {
                // Query data komoditas unggulan BPS
                $komoditasUnggulan = BpsData::with(['komoditas'])
                    ->where('tahun', $tahun)
                    ->where('status_unggulan', true)
                    ->select(
                        'komoditas_id',
                        \DB::raw('SUM(produksi) as total_produksi'),
                        \DB::raw('AVG(produktivitas) as rata_produktivitas'),
                        \DB::raw('COUNT(DISTINCT provinsi_id) as jumlah_provinsi')
                    )
                    ->groupBy('komoditas_id')
                    ->orderByDesc('total_produksi')
                    ->get();
                
                if ($komoditasUnggulan->isEmpty()) {
                    fputcsv($file, ["Tidak ada komoditas unggulan untuk tahun {$tahun}"]);
                } else {
                    // Data
                    foreach ($komoditasUnggulan as $item) {
                        fputcsv($file, [
                            $item->komoditas->nama ?? 'Tidak Diketahui',
                            number_format($item->total_produksi, 2),
                            number_format($item->rata_produktivitas, 2),
                            $item->jumlah_provinsi,
                            'Unggulan'
                        ]);
                    }
                }
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getTrenKomoditasData($tahunAwal, $tahunAkhir, $komoditasId = null, $wilayahId = null)
    {
        $query = Produksi::with(['komoditas', 'demplot'])
            ->whereBetween('tanggal_input', ["{$tahunAwal}-01-01", "{$tahunAkhir}-12-31"])
            ->select(
                \DB::raw('YEAR(tanggal_input) as tahun'),
                'komoditas_id',
                \DB::raw('SUM(total_produksi) as total_produksi'),
                \DB::raw('AVG(produktivitas) as rata_produktivitas')
            )
            ->groupBy('tahun', 'komoditas_id');

        if ($komoditasId) {
            $query->where('komoditas_id', $komoditasId);
        }

        // Filter wilayah jika dipilih
        if ($wilayahId) {
            $query->whereHas('demplot', function($q) use ($wilayahId) {
                $q->where('provinsi_id', $wilayahId)
                  ->orWhere('kabupaten_id', $wilayahId)
                  ->orWhere('kecamatan_id', $wilayahId)
                  ->orWhere('desa_id', $wilayahId);
            });
        }

        $results = $query->get();

        // Format data
        $trenData = [];
        foreach ($results as $result) {
            $tahun = $result->tahun;
            if (!isset($trenData[$tahun])) {
                $trenData[$tahun] = [];
            }
            $trenData[$tahun][] = $result;
        }

        return $trenData;
    }

    // Method untuk generate HTML PDF
    private function generateTrenKomoditasPDFHtml($trenData, $tahunAwal, $tahunAkhir, $komoditasList)
    {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Laporan Tren Komoditas ' . $tahunAwal . ' - ' . $tahunAkhir . '</title>
            <style>
                body { font-family: Arial, sans-serif; font-size: 12px; }
                .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
                .table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                .table th { background-color: #1e3a8a; color: white; }
                .footer { margin-top: 20px; text-align: center; font-size: 10px; color: #666; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>Laporan Tren Komoditas</h1>
                <h2>RPR NasDem</h2>
                <h3>Periode ' . $tahunAwal . ' - ' . $tahunAkhir . '</h3>
                <p>Generated on ' . date('d/m/Y H:i') . '</p>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>Tahun</th>
                        <th>Komoditas</th>
                        <th>Total Produksi (Ton)</th>
                        <th>Produktivitas (Ton/Ha)</th>
                    </tr>
                </thead>
                <tbody>';

        $totalProduksi = 0;
        foreach ($trenData as $tahun => $items) {
            foreach ($items as $item) {
                $totalProduksi += $item->total_produksi;
                $html .= '
                    <tr>
                        <td>' . $tahun . '</td>
                        <td>' . $item->komoditas->nama . '</td>
                        <td>' . number_format($item->total_produksi, 2) . '</td>
                        <td>' . number_format($item->rata_produktivitas, 2) . '</td>
                    </tr>';
            }
        }

        $html .= '
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" style="text-align: right; font-weight: bold;">Total Produksi:</td>
                        <td colspan="2" style="font-weight: bold;">' . number_format($totalProduksi, 2) . ' Ton</td>
                    </tr>
                </tfoot>
            </table>

            <div class="footer">
                <p>Dokumen ini digenerate secara otomatis oleh Sistem RPR NasDem</p>
            </div>
        </body>
        </html>';

        return $html;
    }

    // Method untuk generate HTML PDF lainnya (sederhana)
    private function generateLaporanTrenPDFHtml($trendKomoditas, $tahun)
    {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Laporan Tren ' . $tahun . '</title>
            <style>body { font-family: Arial; } .header { text-align: center; } table { width: 100%; border-collapse: collapse; } th, td { border: 1px solid #ddd; padding: 8px; } th { background-color: #1e3a8a; color: white; }</style>
        </head>
        <body>
            <div class="header">
                <h1>Laporan Tren Komoditas Unggulan</h1>
                <h2>Tahun ' . $tahun . '</h2>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Komoditas</th>
                        <th>Total Produksi (Ton)</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($trendKomoditas as $item) {
            $html .= '
                    <tr>
                        <td>' . $item->nama . '</td>
                        <td>' . number_format($item->total, 2) . '</td>
                    </tr>';
        }

        $html .= '
                </tbody>
            </table>
        </body>
        </html>';

        return $html;
    }

    /**
     * Generate HTML for Empty BPS Data PDF
     */
    private function generateEmptyBpsDataPDFHtml($tahun)
    {
        $availableYears = BpsData::distinct()->pluck('tahun')->toArray();
        $suggestion = !empty($availableYears) ? 'Tahun yang tersedia: ' . implode(', ', $availableYears) : 'Tidak ada data BPS di database';
        
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Laporan Data BPS ' . $tahun . '</title>
            <style>
                body { 
                    font-family: Arial, sans-serif; 
                    font-size: 14px; 
                    margin: 50px;
                    text-align: center;
                }
                .warning {
                    background-color: #fff3cd;
                    border: 1px solid #ffeaa7;
                    padding: 20px;
                    border-radius: 5px;
                    margin: 20px 0;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>Laporan Data Pertanian BPS</h1>
                <h2>RPR NasDem</h2>
                <h3>Tahun ' . $tahun . '</h3>
            </div>

            <div class="warning">
                <h3>⚠️ Data Tidak Ditemukan</h3>
                <p><strong>Tidak ada data BPS untuk tahun ' . $tahun . '</strong></p>
                <p>' . $suggestion . '</p>
                <p>Silakan pilih tahun lain yang tersedia.</p>
            </div>

            <div class="footer">
                <p>Generated on ' . date('d/m/Y H:i') . '</p>
            </div>
        </body>
        </html>';

        return $html;
    }

    /**
     * Generate HTML for BPS Data PDF
     */
    private function generateBpsDataPDFHtml($bpsData, $tahun, $totalProduksi, $totalLuasLahan)
    {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Laporan Data BPS ' . $tahun . '</title>
            <style>
                body { 
                    font-family: Arial, sans-serif; 
                    font-size: 12px; 
                    margin: 20px;
                }
                .header { 
                    text-align: center; 
                    margin-bottom: 20px; 
                    border-bottom: 2px solid #333; 
                    padding-bottom: 10px; 
                }
                .summary {
                    background-color: #f8f9fa;
                    padding: 15px;
                    border-radius: 5px;
                    margin-bottom: 20px;
                }
                .table { 
                    width: 100%; 
                    border-collapse: collapse; 
                    margin-top: 10px; 
                    font-size: 10px;
                }
                .table th, .table td { 
                    border: 1px solid #ddd; 
                    padding: 6px; 
                    text-align: left; 
                }
                .table th { 
                    background-color: #1e3a8a; 
                    color: white; 
                }
                .footer { 
                    margin-top: 20px; 
                    text-align: center; 
                    font-size: 10px; 
                    color: #666; 
                }
                .unggulan {
                    background-color: #d1fae5;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>Laporan Data Pertanian BPS</h1>
                <h2>RPR NasDem</h2>
                <h3>Tahun ' . $tahun . '</h3>
                <p>Generated on ' . date('d/m/Y H:i') . '</p>
            </div>

            <div class="summary">
                <h4>Ringkasan Data:</h4>
                <p><strong>Total Produksi:</strong> ' . number_format($totalProduksi, 2) . ' Ton</p>
                <p><strong>Total Luas Lahan:</strong> ' . number_format($totalLuasLahan, 2) . ' Ha</p>
                <p><strong>Rata-rata Produktivitas:</strong> ' . number_format($totalLuasLahan > 0 ? $totalProduksi / $totalLuasLahan : 0, 2) . ' Ton/Ha</p>
                <p><strong>Jumlah Data:</strong> ' . $bpsData->count() . ' records</p>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>Provinsi</th>
                        <th>Kabupaten</th>
                        <th>Kecamatan</th>
                        <th>Sektor</th>
                        <th>Komoditas</th>
                        <th>Luas Lahan</th>
                        <th>Produksi</th>
                        <th>Produktivitas</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($bpsData as $item) {
            $rowClass = $item->status_unggulan ? 'unggulan' : '';
            $html .= '
                    <tr class="' . $rowClass . '">
                        <td>' . ($item->provinsi->nama ?? '-') . '</td>
                        <td>' . ($item->kabupaten->nama ?? '-') . '</td>
                        <td>' . ($item->kecamatan->nama ?? '-') . '</td>
                        <td>' . ($item->sektor->nama ?? '-') . '</td>
                        <td>' . ($item->komoditas->nama ?? '-') . '</td>
                        <td>' . number_format($item->luas_lahan, 2) . ' Ha</td>
                        <td>' . number_format($item->produksi, 2) . ' Ton</td>
                        <td>' . number_format($item->produktivitas, 2) . ' Ton/Ha</td>
                        <td>' . ($item->status_unggulan ? '⭐ Unggulan' : 'Biasa') . '</td>
                    </tr>';
        }

        $html .= '
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" style="text-align: right; font-weight: bold;">Total:</td>
                        <td style="font-weight: bold;">' . number_format($totalLuasLahan, 2) . ' Ha</td>
                        <td style="font-weight: bold;">' . number_format($totalProduksi, 2) . ' Ton</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>

            <div class="footer">
                <p>Dokumen ini digenerate secara otomatis oleh Sistem RPR NasDem</p>
                <p>Data sumber: Badan Pusat Statistik (BPS)</p>
            </div>
        </body>
        </html>';

        return $html;
    }

    // Tambahkan method generate PDF lainnya sesuai kebutuhan...
    private function generateProduksiPDFHtml($produksi, $tahun, $bulan, $totalProduksi)
    {
        // Implementasi sederhana untuk produksi PDF
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Laporan Produksi ' . $tahun . '</title>
            <style>body { font-family: Arial; } .header { text-align: center; } table { width: 100%; border-collapse: collapse; } th, td { border: 1px solid #ddd; padding: 8px; } th { background-color: #1e3a8a; color: white; }</style>
        </head>
        <body>
            <div class="header">
                <h1>Laporan Produksi</h1>
                <h2>Tahun ' . $tahun . '</h2>
                <p>Total Produksi: ' . number_format($totalProduksi, 2) . ' Ton</p>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Komoditas</th>
                        <th>Produksi (Ton)</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($produksi as $item) {
            $html .= '
                    <tr>
                        <td>' . $item->tanggal_input . '</td>
                        <td>' . $item->komoditas->nama . '</td>
                        <td>' . number_format($item->total_produksi, 2) . '</td>
                    </tr>';
        }

        $html .= '
                </tbody>
            </table>
        </body>
        </html>';

        return $html;
    }

    // Method untuk generate PDF lainnya...
    private function generateDemplotPDFHtml($demplot, $status) { 
        // Implementasi sederhana untuk demplot PDF
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Laporan Demplot</title>
            <style>body { font-family: Arial; } .header { text-align: center; } table { width: 100%; border-collapse: collapse; } th, td { border: 1px solid #ddd; padding: 8px; } th { background-color: #1e3a8a; color: white; }</style>
        </head>
        <body>
            <div class="header">
                <h1>Laporan Demplot</h1>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Nama Demplot</th>
                        <th>Komoditas</th>
                        <th>Status</th>
                        <th>Luas Lahan</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($demplot as $item) {
            $html .= '
                    <tr>
                        <td>' . $item->nama_demplot . '</td>
                        <td>' . $item->komoditas->nama . '</td>
                        <td>' . $item->status . '</td>
                        <td>' . number_format($item->luas_lahan, 2) . ' Ha</td>
                    </tr>';
        }

        $html .= '
                </tbody>
            </table>
        </body>
        </html>';

        return $html;
    }

    private function generatePetaniPDFHtml($petani, $aktif, $totalPetani, $totalLahan) { 
        // Implementasi sederhana untuk petani PDF
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Laporan Petani</title>
            <style>body { font-family: Arial; } .header { text-align: center; } table { width: 100%; border-collapse: collapse; } th, td { border: 1px solid #ddd; padding: 8px; } th { background-color: #1e3a8a; color: white; }</style>
        </head>
        <body>
            <div class="header">
                <h1>Laporan Petani</h1>
                <p>Total Petani: ' . $totalPetani . '</p>
                <p>Total Luas Lahan: ' . number_format($totalLahan, 2) . ' Ha</p>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Nama Petani</th>
                        <th>Poktan</th>
                        <th>Status</th>
                        <th>Luas Lahan</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($petani as $item) {
            $status = $item->aktif ? 'Aktif' : 'Tidak Aktif';
            $html .= '
                    <tr>
                        <td>' . $item->nama_petani . '</td>
                        <td>' . ($item->poktan->nama_poktan ?? '-') . '</td>
                        <td>' . $status . '</td>
                        <td>' . number_format($item->luas_lahan_garap, 2) . ' Ha</td>
                    </tr>';
        }

        $html .= '
                </tbody>
            </table>
        </body>
        </html>';

        return $html;
    }

    private function generatePoktanPDFHtml($poktan) { 
        // Implementasi sederhana untuk poktan PDF
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Laporan Poktan</title>
            <style>body { font-family: Arial; } .header { text-align: center; } table { width: 100%; border-collapse: collapse; } th, td { border: 1px solid #ddd; padding: 8px; } th { background-color: #1e3a8a; color: white; }</style>
        </head>
        <body>
            <div class="header">
                <h1>Laporan Kelompok Tani</h1>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Nama Poktan</th>
                        <th>Ketua</th>
                        <th>Jumlah Anggota</th>
                        <th>Tanggal Berdiri</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($poktan as $item) {
            $jumlahAnggota = $item->petani->where('aktif', true)->count();
            $html .= '
                    <tr>
                        <td>' . $item->nama_poktan . '</td>
                        <td>' . $item->nama_ketua . '</td>
                        <td>' . $jumlahAnggota . '</td>
                        <td>' . $item->tanggal_berdiri . '</td>
                    </tr>';
        }

        $html .= '
                </tbody>
            </table>
        </body>
        </html>';

        return $html;
    }

    private function generateDashboardSummaryPDFHtml($stats, $produksiPerSektor, $tahun) { 
        // Implementasi sederhana untuk dashboard summary PDF
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Dashboard Summary ' . $tahun . '</title>
            <style>body { font-family: Arial; } .header { text-align: center; } table { width: 100%; border-collapse: collapse; } th, td { border: 1px solid #ddd; padding: 8px; } th { background-color: #1e3a8a; color: white; }</style>
        </head>
        <body>
            <div class="header">
                <h1>Dashboard Summary</h1>
                <h2>Tahun ' . $tahun . '</h2>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Statistik</th>
                        <th>Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Total Produksi</td>
                        <td>' . number_format($stats['total_produksi'], 2) . ' Ton</td>
                    </tr>
                    <tr>
                        <td>Total Petani</td>
                        <td>' . $stats['total_petani'] . '</td>
                    </tr>
                    <tr>
                        <td>Total Demplot</td>
                        <td>' . $stats['total_demplot'] . '</td>
                    </tr>
                    <tr>
                        <td>Total Poktan</td>
                        <td>' . $stats['total_poktan'] . '</td>
                    </tr>
                    <tr>
                        <td>Total Komoditas</td>
                        <td>' . $stats['total_komoditas'] . '</td>
                    </tr>
                </tbody>
            </table>
        </body>
        </html>';

        return $html;
    }
}