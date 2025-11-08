<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('petani', function (Blueprint $table) {
            $table->string('pendidikan')->nullable()->change();
            $table->string('no_hp')->nullable()->change();
            $table->decimal('luas_lahan_garap', 8, 2)->default(0)->change();
            $table->string('status_lahan')->nullable()->change();
            $table->string('latitude')->nullable()->change();
            $table->string('longitude')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('petani', function (Blueprint $table) {
            $table->string('pendidikan')->nullable(false)->change();
            $table->string('no_hp')->nullable(false)->change();
            $table->decimal('luas_lahan_garap', 8, 2)->default(null)->change();
            $table->string('status_lahan')->nullable(false)->change();
            $table->string('latitude')->nullable(false)->change();
            $table->string('longitude')->nullable(false)->change();
        });
    }
};