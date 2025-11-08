<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('demplot', function (Blueprint $table) {
            if (!Schema::hasColumn('demplot', 'alamat')) {
                $table->text('alamat')->nullable()->after('nama_lahan');
            }
        });
    }

    public function down()
    {
        Schema::table('demplot', function (Blueprint $table) {
            $table->dropColumn('alamat');
        });
    }
};