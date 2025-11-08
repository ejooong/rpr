<?php
// database/migrations/2025_10_30_xxxxxx_add_kecamatan_id_to_bps_data_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bps_data', function (Blueprint $table) {
            // Hapus dulu constraint foreign key yang ada (jika perlu)
            // $table->dropForeign(['kabupaten_id']);
            
            // Tambah kecamatan_id
            $table->foreignId('kecamatan_id')
                  ->nullable()
                  ->after('kabupaten_id')
                  ->constrained('kecamatans')
                  ->onDelete('cascade');
                  
            // Optional: Tambah index untuk performa
            $table->index(['tahun', 'provinsi_id', 'kabupaten_id', 'kecamatan_id']);
            $table->index(['komoditas_id', 'status_unggulan', 'tahun']);
        });
    }

    public function down()
    {
        Schema::table('bps_data', function (Blueprint $table) {
            $table->dropForeign(['kecamatan_id']);
            $table->dropColumn('kecamatan_id');
            $table->dropIndex(['tahun', 'provinsi_id', 'kabupaten_id', 'kecamatan_id']);
            $table->dropIndex(['komoditas_id', 'status_unggulan', 'tahun']);
        });
    }
};