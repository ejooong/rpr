// database/migrations/2024_01_05_create_petani_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('petani', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poktan_id')->constrained('poktan');
            $table->string('nik', 20)->unique();
            $table->string('nama');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->date('tanggal_lahir');
            $table->string('pendidikan', 100);
            $table->text('alamat');
            $table->decimal('luas_lahan_garap', 10, 2);
            $table->enum('status_lahan', ['milik', 'sewa', 'bagi_hasil', 'lainnya']);
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('petani');
    }
};
