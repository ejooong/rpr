<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Data BPS {{ $tahun }}</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            font-size: 10px; 
            margin: 15px;
        }
        .header { 
            text-align: center; 
            margin-bottom: 15px; 
            border-bottom: 2px solid #333; 
            padding-bottom: 10px; 
        }
        .summary {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px; 
            font-size: 8px;
        }
        .table th, .table td { 
            border: 1px solid #ddd; 
            padding: 4px; 
            text-align: left; 
        }
        .table th { 
            background-color: #1e3a8a; 
            color: white; 
            font-weight: bold;
        }
        .footer { 
            margin-top: 15px; 
            text-align: center; 
            font-size: 8px; 
            color: #666; 
        }
        .unggulan {
            background-color: #d1fae5;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0; font-size: 16px;">Laporan Data Pertanian BPS</h1>
        <h2 style="margin: 5px 0; font-size: 14px;">RPR NasDem</h2>
        <h3 style="margin: 5px 0; font-size: 12px;">Tahun {{ $tahun }}</h3>
        <p style="margin: 0; font-size: 9px;">Generated on {{ date('d/m/Y H:i') }}</p>
    </div>

    @if($bpsData->count() > 0)
    <div class="summary">
        <h4 style="margin: 0 0 5px 0; font-size: 10px;">Ringkasan Data:</h4>
        <p style="margin: 2px 0;"><strong>Total Produksi:</strong> {{ number_format($totalProduksi, 2) }} Ton</p>
        <p style="margin: 2px 0;"><strong>Total Luas Lahan:</strong> {{ number_format($totalLuasLahan, 2) }} Ha</p>
        <p style="margin: 2px 0;"><strong>Rata-rata Produktivitas:</strong> {{ number_format($totalLuasLahan > 0 ? $totalProduksi / $totalLuasLahan : 0, 2) }} Ton/Ha</p>
        <p style="margin: 2px 0;"><strong>Jumlah Data:</strong> {{ $bpsData->count() }} records</p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>Provinsi</th>
                <th>Kabupaten</th>
                <th>Kecamatan</th>
                <th>Sektor</th>
                <th>Komoditas</th>
                <th>Luas Lahan (Ha)</th>
                <th>Produksi (Ton)</th>
                <th>Produktivitas (Ton/Ha)</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bpsData as $index => $item)
            <tr class="{{ $item->status_unggulan ? 'unggulan' : '' }}">
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->provinsi->nama ?? '-' }}</td>
                <td>{{ $item->kabupaten->nama ?? '-' }}</td>
                <td>{{ $item->kecamatan->nama ?? '-' }}</td>
                <td>{{ $item->sektor->nama ?? '-' }}</td>
                <td>{{ $item->komoditas->nama ?? '-' }}</td>
                <td class="text-center">{{ number_format($item->luas_lahan, 2) }}</td>
                <td class="text-center">{{ number_format($item->produksi, 2) }}</td>
                <td class="text-center">{{ number_format($item->produktivitas, 2) }}</td>
                <td class="text-center">{{ $item->status_unggulan ? '⭐ Unggulan' : 'Biasa' }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" style="text-align: right; font-weight: bold;">Total:</td>
                <td class="text-center" style="font-weight: bold;">{{ number_format($totalLuasLahan, 2) }}</td>
                <td class="text-center" style="font-weight: bold;">{{ number_format($totalProduksi, 2) }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>
    @else
    <div style="text-align: center; padding: 40px; background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 5px;">
        <h3 style="color: #856404;">⚠️ Data Tidak Ditemukan</h3>
        <p><strong>Tidak ada data BPS untuk tahun {{ $tahun }}</strong></p>
        @php
            $availableYears = \App\Models\BpsData::distinct()->pluck('tahun')->toArray();
        @endphp
        @if(!empty($availableYears))
            <p>Tahun yang tersedia: {{ implode(', ', $availableYears) }}</p>
        @endif
    </div>
    @endif

    <div class="footer">
        <p>Dokumen ini digenerate secara otomatis oleh Sistem RPR NasDem</p>
        <p>Data sumber: Badan Pusat Statistik (BPS)</p>
    </div>
</body>
</html>