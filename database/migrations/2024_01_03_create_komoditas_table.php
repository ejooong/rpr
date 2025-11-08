// database/migrations/2024_01_03_create_komoditas_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('komoditas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sektor_id')->constrained('sektor');
            $table->string('nama');
            $table->string('satuan', 50);
            $table->text('deskripsi')->nullable();
            $table->boolean('status_unggulan')->default(false);
            $table->string('ikon')->nullable();
            $table->string('warna_chart', 20)->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('komoditas');
    }
};
