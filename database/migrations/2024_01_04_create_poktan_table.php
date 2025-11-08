// database/migrations/2024_01_04_create_poktan_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('poktan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wilayah_id')->constrained('wilayah');
            $table->string('nama');
            $table->string('ketua');
            $table->text('alamat');
            $table->date('tanggal_terbentuk');
            $table->integer('jumlah_anggota')->default(0);
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('poktan');
    }
};
