<?php
// app/Http/Requests/StoreProduksiRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProduksiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $currentYear = date('Y');
        
        return [
            'demplot_id' => 'required|exists:demplot,id',
            'komoditas_id' => 'required|exists:komoditas,id',
            'tahun' => 'required|integer|min:2020|max:' . ($currentYear + 1),
            'bulan' => 'required|integer|min:1|max:12',
            'luas_panen' => 'required|numeric|min:0',
            'total_produksi' => 'required|numeric|min:0',
            'tanggal_input' => 'required|date',
            'sumber_data' => 'nullable|string|max:100'
        ];
    }

    public function messages(): array
    {
        return [
            'demplot_id.required' => 'Pilih demplot wajib diisi',
            'komoditas_id.required' => 'Pilih komoditas wajib diisi',
            'tahun.required' => 'Tahun wajib diisi',
            'bulan.required' => 'Bulan wajib diisi',
            'luas_panen.required' => 'Luas panen wajib diisi',
            'total_produksi.required' => 'Total produksi wajib diisi',
            'tanggal_input.required' => 'Tanggal input wajib diisi',
        ];
    }

    protected function prepareForValidation()
    {
        // Hitung produktivitas sebelum validasi
        $luasPanen = $this->luas_panen ? floatval($this->luas_panen) : 0;
        $totalProduksi = $this->total_produksi ? floatval($this->total_produksi) : 0;
        
        $produktivitas = 0;
        if ($luasPanen > 0 && $totalProduksi > 0) {
            $produktivitas = $totalProduksi / $luasPanen;
        }

        $this->merge([
            'produktivitas' => round($produktivitas, 2),
            'petugas_id' => auth()->id() // Pastikan ini ada
        ]);
    }
}