<?php
// app/Http/Requests/UpdateDemplotRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDemplotRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'petani_id' => 'required|exists:petani,id',
            'komoditas_id' => 'required|exists:komoditas,id',
            'nama_lahan' => 'required|string|max:255',
            'luas_lahan' => 'required|numeric|min:0',
            'status' => 'required|in:rencana,aktif,selesai',
            'tahun' => 'required|integer|min:2000|max:2030',
            'provinsi_id' => 'required|exists:provinsis,id',
            'kabupaten_id' => 'required|exists:kabupatens,id',
            'kecamatan_id' => 'required|exists:kecamatans,id',
            'desa_id' => 'required|exists:desas,id',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'alamat' => 'required|string',
            'foto_lahan' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'keterangan' => 'nullable|string',
        ];
        // PASTIKAN TIDAK ADA 'wilayah_id' di sini!
    }
}