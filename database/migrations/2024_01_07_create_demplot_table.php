// database/migrations/2024_01_06_create_demplot_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('demplot', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wilayah_id')->constrained('wilayah');
            $table->foreignId('petani_id')->constrained('petani');
            $table->foreignId('komoditas_id')->constrained('komoditas');
            $table->string('nama_lahan');
            $table->decimal('luas_lahan', 10, 2);
            $table->enum('status', ['rencana', 'aktif', 'selesai'])->default('rencana');
            $table->year('tahun');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->string('foto_lahan')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demplot');
    }
};
