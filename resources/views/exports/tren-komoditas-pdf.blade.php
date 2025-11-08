<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Tren Komoditas {{ $tahunAwal }} - {{ $tahunAkhir }}</title>
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
        <h3>Periode {{ $tahunAwal }} - {{ $tahunAkhir }}</h3>
        <p>Generated on {{ date('d/m/Y H:i') }}</p>
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
        <tbody>
            @php
                $totalProduksi = 0;
            @endphp
            @foreach($trenData as $tahun => $items)
                @foreach($items as $item)
                    @php
                        $totalProduksi += $item->total_produksi;
                    @endphp
                    <tr>
                        <td>{{ $tahun }}</td>
                        <td>{{ $item->komoditas->nama }}</td>
                        <td>{{ number_format($item->total_produksi, 2) }}</td>
                        <td>{{ number_format($item->rata_produktivitas, 2) }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" style="text-align: right; font-weight: bold;">Total Produksi:</td>
                <td colspan="2" style="font-weight: bold;">{{ number_format($totalProduksi, 2) }} Ton</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Dokumen ini digenerate secara otomatis oleh Sistem RPR NasDem</p>
    </div>
</body>
</html>