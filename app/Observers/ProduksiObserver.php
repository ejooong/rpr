<?php
// app/Observers/ProduksiObserver.php

namespace App\Observers;

use App\Models\Produksi;

class ProduksiObserver
{
    public function creating(Produksi $produksi)
    {
        // Set petugas_id otomatis saat create
        if (empty($produksi->petugas_id)) {
            $produksi->petugas_id = auth()->id();
        }
    }
}