<?php
// app/Http/Requests/UpdateProduksiRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProduksiRequest extends FormRequest
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

    protected function prepareForValidation()
    {
        // Hitung produktivitas sebelum validasi
        if ($this->luas_panen && $this->total_produksi) {
            $produktivitas = $this->luas_panen > 0 ? $this->total_produksi / $this->luas_panen : 0;
            $this->merge([
                'produktivitas' => round($produktivitas, 2)
            ]);
        }
    }
}