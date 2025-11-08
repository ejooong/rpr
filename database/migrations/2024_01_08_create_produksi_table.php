// database/migrations/2024_01_07_create_produksi_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('produksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('demplot_id')->constrained('demplot');
            $table->foreignId('komoditas_id')->constrained('komoditas');
            $table->year('tahun');
            $table->tinyInteger('bulan')->nullable();
            $table->decimal('luas_panen', 10, 2);
            $table->decimal('total_produksi', 10, 2);
            $table->decimal('produktivitas', 10, 2)->nullable();
            $table->datetime('tanggal_input');
            $table->foreignId('petugas_id')->constrained('users');
            $table->string('sumber_data')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produksi');
    }
};
