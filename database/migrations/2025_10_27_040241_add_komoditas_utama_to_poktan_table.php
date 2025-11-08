<?php
// database/migrations/2024_01_01_add_komoditas_utama_to_poktan_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKomoditasUtamaToPoktanTable extends Migration
{
    public function up()
    {
        Schema::table('poktan', function (Blueprint $table) {
            $table->unsignedBigInteger('komoditas_utama_id')->nullable()->after('desa_id');
            $table->foreign('komoditas_utama_id')->references('id')->on('komoditas')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('poktan', function (Blueprint $table) {
            $table->dropForeign(['komoditas_utama_id']);
            $table->dropColumn('komoditas_utama_id');
        });
    }
}