<?php
// database/migrations/2025_10_30_xxxxxx_create_bps_data_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bps_data', function (Blueprint $table) {
            $table->id();
            $table->year('tahun');
            $table->foreignId('provinsi_id')->constrained('provinsis');
            $table->foreignId('kabupaten_id')->nullable()->constrained('kabupatens');
            $table->foreignId('sektor_id')->constrained('sektor');
            $table->foreignId('komoditas_id')->constrained('komoditas');
            
            // Data produksi
            $table->decimal('luas_lahan', 15, 2)->nullable(); // dalam hektar
            $table->decimal('produksi', 15, 2)->nullable(); // dalam ton/ekor sesuai satuan komoditas
            $table->decimal('produktivitas', 10, 2)->nullable(); // produksi per hektar
            
            // Status komoditas unggulan
            $table->boolean('status_unggulan')->default(false);
            $table->integer('peringkat_wilayah')->nullable();
            
            // Sumber data
            $table->string('sumber_data')->default('BPS');
            $table->string('keterangan')->nullable();
            
            $table->timestamps();
            
            // Index untuk performa query
            $table->index(['tahun', 'provinsi_id']);
            $table->index(['komoditas_id', 'status_unggulan']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('bps_data');
    }
};