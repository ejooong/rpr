<?php

namespace App\Imports;

use App\Models\BpsData;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Sektor;
use App\Models\Komoditas;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BpsDataImport implements ToCollection, WithHeadingRow
{
    private $errors = [];
    private $successCount = 0;

    public function collection(Collection $rows)
    {
        DB::beginTransaction();
        
        try {
            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2; // +2 karena heading row + base 1 index
                
                try {
                    // Validasi data wajib
                    if (empty($row['tahun']) || empty($row['nama_provinsi']) || empty($row['nama_sektor']) || empty($row['nama_komoditas'])) {
                        $this->errors[] = "Baris {$rowNumber}: Data wajib (tahun, provinsi, sektor, komoditas) harus diisi";
                        continue;
                    }

                    // Cari atau buat provinsi
                    $provinsi = Provinsi::where('nama', 'like', '%' . trim($row['nama_provinsi']) . '%')->first();
                    if (!$provinsi) {
                        $this->errors[] = "Baris {$rowNumber}: Provinsi '{$row['nama_provinsi']}' tidak ditemukan";
                        continue;
                    }

                    // Cari sektor
                    $sektor = Sektor::where('nama', 'like', '%' . trim($row['nama_sektor']) . '%')->first();
                    if (!$sektor) {
                        $this->errors[] = "Baris {$rowNumber}: Sektor '{$row['nama_sektor']}' tidak ditemukan";
                        continue;
                    }

                    // Cari komoditas
                    $komoditas = Komoditas::where('nama', 'like', '%' . trim($row['nama_komoditas']) . '%')
                        ->where('sektor_id', $sektor->id)
                        ->first();
                    if (!$komoditas) {
                        $this->errors[] = "Baris {$rowNumber}: Komoditas '{$row['nama_komoditas']}' tidak ditemukan di sektor {$sektor->nama}";
                        continue;
                    }

                    // Cari kabupaten jika ada
                    $kabupaten = null;
                    if (!empty($row['nama_kabupaten'])) {
                        $kabupaten = Kabupaten::where('nama', 'like', '%' . trim($row['nama_kabupaten']) . '%')
                            ->where('provinsi_id', $provinsi->id)
                            ->first();
                        if (!$kabupaten) {
                            $this->errors[] = "Baris {$rowNumber}: Kabupaten '{$row['nama_kabupaten']}' tidak ditemukan di provinsi {$provinsi->nama}";
                            continue;
                        }
                    }

                    // Cari kecamatan jika ada
                    $kecamatan = null;
                    if (!empty($row['nama_kecamatan']) && $kabupaten) {
                        $kecamatan = Kecamatan::where('nama', 'like', '%' . trim($row['nama_kecamatan']) . '%')
                            ->where('kabupaten_id', $kabupaten->id)
                            ->first();
                        if (!$kecamatan) {
                            $this->errors[] = "Baris {$rowNumber}: Kecamatan '{$row['nama_kecamatan']}' tidak ditemukan di kabupaten {$kabupaten->nama}";
                            continue;
                        }
                    }

                    // Hitung produktivitas jika tidak diisi
                    $produktivitas = $row['produktivitas'] ?? null;
                    if (empty($produktivitas)) {
                        $luasLahan = $row['luas_lahan'] ?? 0;
                        $produksi = $row['produksi'] ?? 0;
                        if ($luasLahan > 0 && $produksi > 0) {
                            $produktivitas = $produksi / $luasLahan;
                        }
                    }

                    // Cek duplikat data
                    $existingData = BpsData::where('tahun', $row['tahun'])
                        ->where('provinsi_id', $provinsi->id)
                        ->where('sektor_id', $sektor->id)
                        ->where('komoditas_id', $komoditas->id)
                        ->when($kabupaten, function($query) use ($kabupaten) {
                            return $query->where('kabupaten_id', $kabupaten->id);
                        })
                        ->when($kecamatan, function($query) use ($kecamatan) {
                            return $query->where('kecamatan_id', $kecamatan->id);
                        })
                        ->first();

                    if ($existingData) {
                        // Update data yang sudah ada
                        $existingData->update([
                            'luas_lahan' => $row['luas_lahan'] ?? $existingData->luas_lahan,
                            'produksi' => $row['produksi'] ?? $existingData->produksi,
                            'produktivitas' => $produktivitas ?? $existingData->produktivitas,
                            'peringkat_wilayah' => $row['peringkat_wilayah'] ?? $existingData->peringkat_wilayah,
                            'sumber_data' => $row['sumber_data'] ?? $existingData->sumber_data,
                            'keterangan' => $row['keterangan'] ?? $existingData->keterangan,
                        ]);
                    } else {
                        // Buat data baru
                        BpsData::create([
                            'tahun' => $row['tahun'],
                            'provinsi_id' => $provinsi->id,
                            'kabupaten_id' => $kabupaten->id ?? null,
                            'kecamatan_id' => $kecamatan->id ?? null,
                            'sektor_id' => $sektor->id,
                            'komoditas_id' => $komoditas->id,
                            'luas_lahan' => $row['luas_lahan'] ?? null,
                            'produksi' => $row['produksi'] ?? 0,
                            'produktivitas' => $produktivitas,
                            'peringkat_wilayah' => $row['peringkat_wilayah'] ?? null,
                            'sumber_data' => $row['sumber_data'] ?? 'BPS',
                            'keterangan' => $row['keterangan'] ?? null,
                        ]);
                    }

                    $this->successCount++;

                } catch (\Exception $e) {
                    // Sederhanakan pesan error - jangan tampilkan detail teknis
                    $errorMessage = $this->simplifyErrorMessage($e->getMessage(), $rowNumber);
                    $this->errors[] = $errorMessage;
                    Log::error("Import error at row {$rowNumber}: " . $e->getMessage());
                }
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            // Sederhanakan pesan error untuk exception umum
            $this->errors[] = "Terjadi kesalahan sistem saat import data";
            Log::error("Import transaction failed: " . $e->getMessage());
        }
    }

    /**
     * Parse nilai numerik dari berbagai format
     */
    private function parseNumericValue($value)
    {
        if (empty($value) || $value === '-' || $value === 'NULL' || $value === 'null') {
            return 0;
        }

        // Jika sudah numeric, langsung return
        if (is_numeric($value)) {
            return floatval($value);
        }

        // Handle string dengan koma (format Indonesia)
        if (is_string($value)) {
            $value = trim($value);
            
            // Hapus karakter non-numeric kecuali koma dan titik
            $cleaned = preg_replace('/[^\d.,-]/', '', $value);
            
            // Handle format Indonesia (1.000,00 -> 1000.00)
            if (preg_match('/^\d{1,3}(\.\d{3})*,\d+$/', $cleaned)) {
                $cleaned = str_replace('.', '', $cleaned);
                $cleaned = str_replace(',', '.', $cleaned);
            }
            
            // Handle format English (1,000.00 -> 1000.00)
            elseif (preg_match('/^\d{1,3}(,\d{3})*\.\d+$/', $cleaned)) {
                $cleaned = str_replace(',', '', $cleaned);
            }
            
            if (is_numeric($cleaned)) {
                return floatval($cleaned);
            }
        }

        return null;
    }

    /**
     * Sederhanakan pesan error untuk ditampilkan ke user
     */
    private function simplifyErrorMessage($errorMessage, $rowNumber)
    {
        // Deteksi jenis error dan berikan pesan yang user-friendly
        if (str_contains($errorMessage, 'Incorrect decimal value')) {
            return "Baris {$rowNumber}: Format angka tidak valid";
        }
        
        if (str_contains($errorMessage, 'Data truncated')) {
            return "Baris {$rowNumber}: Data terlalu panjang untuk kolom tertentu";
        }
        
        if (str_contains($errorMessage, 'Duplicate entry')) {
            return "Baris {$rowNumber}: Data duplikat";
        }
        
        if (str_contains($errorMessage, 'Cannot add or update a child row')) {
            return "Baris {$rowNumber}: Referensi data tidak valid";
        }
        
        if (str_contains($errorMessage, 'Out of range value')) {
            return "Baris {$rowNumber}: Nilai di luar batas yang diizinkan";
        }
        
        // Default message untuk error lainnya
        return "Baris {$rowNumber}: Format data tidak valid";
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getSuccessCount()
    {
        return $this->successCount;
    }
}