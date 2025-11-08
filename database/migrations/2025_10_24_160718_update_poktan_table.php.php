<?php
// database/migrations/2024_01_11_update_poktan_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('poktan', function (Blueprint $table) {
            // Hapus kolom wilayah_id lama
            $table->dropForeign(['wilayah_id']);
            $table->dropColumn('wilayah_id');
            
            // Tambah kolom baru untuk hirarki wilayah
            $table->foreignId('provinsi_id')->nullable()->constrained('provinsis');
            $table->foreignId('kabupaten_id')->nullable()->constrained('kabupatens');
            $table->foreignId('kecamatan_id')->nullable()->constrained('kecamatans');
            $table->foreignId('desa_id')->nullable()->constrained('desas');
        });
    }

    public function down(): void
    {
        Schema::table('poktan', function (Blueprint $table) {
            $table->dropForeign(['provinsi_id']);
            $table->dropForeign(['kabupaten_id']);
            $table->dropForeign(['kecamatan_id']);
            $table->dropForeign(['desa_id']);
            
            $table->dropColumn(['provinsi_id', 'kabupaten_id', 'kecamatan_id', 'desa_id']);
            
            $table->foreignId('wilayah_id')->constrained('wilayah');
        });
    }
};