<?php
// database/migrations/2024_01_11_000000_fix_wilayah_foreign_keys.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Pastikan semua table sudah dibuat sebelum menambahkan foreign keys
        if (Schema::hasTable('provinsis') && 
            Schema::hasTable('kabupatens') && 
            Schema::hasTable('kecamatans') && 
            Schema::hasTable('desas')) {
            
            // Update poktan table
            if (Schema::hasTable('poktan')) {
                Schema::table('poktan', function (Blueprint $table) {
                    // Hapus kolom wilayah_id jika ada (dengan pengecekan aman)
                    if (Schema::hasColumn('poktan', 'wilayah_id')) {
                        // Cek dan hapus foreign key jika ada
                        $connection = Schema::getConnection();
                        $dbSchemaManager = $connection->getDoctrineSchemaManager();
                        $doctrineTable = $dbSchemaManager->listTableDetails('poktan');
                        
                        if ($doctrineTable->hasForeignKey('poktan_wilayah_id_foreign')) {
                            $table->dropForeign(['wilayah_id']);
                        }
                        
                        $table->dropColumn('wilayah_id');
                    }
                    
                    // Tambah kolom baru dengan pengecekan
                    if (!Schema::hasColumn('poktan', 'provinsi_id')) {
                        $table->foreignId('provinsi_id')->nullable()->constrained('provinsis');
                    }
                    if (!Schema::hasColumn('poktan', 'kabupaten_id')) {
                        $table->foreignId('kabupaten_id')->nullable()->constrained('kabupatens');
                    }
                    if (!Schema::hasColumn('poktan', 'kecamatan_id')) {
                        $table->foreignId('kecamatan_id')->nullable()->constrained('kecamatans');
                    }
                    if (!Schema::hasColumn('poktan', 'desa_id')) {
                        $table->foreignId('desa_id')->nullable()->constrained('desas');
                    }
                });
            }

            // Update demplot table
            if (Schema::hasTable('demplot')) {
                Schema::table('demplot', function (Blueprint $table) {
                    // Hapus kolom wilayah_id jika ada
                    if (Schema::hasColumn('demplot', 'wilayah_id')) {
                        // Cek dan hapus foreign key jika ada
                        $connection = Schema::getConnection();
                        $dbSchemaManager = $connection->getDoctrineSchemaManager();
                        $doctrineTable = $dbSchemaManager->listTableDetails('demplot');
                        
                        if ($doctrineTable->hasForeignKey('demplot_wilayah_id_foreign')) {
                            $table->dropForeign(['wilayah_id']);
                        }
                        
                        $table->dropColumn('wilayah_id');
                    }
                    
                    // Tambah kolom baru dengan pengecekan
                    if (!Schema::hasColumn('demplot', 'provinsi_id')) {
                        $table->foreignId('provinsi_id')->nullable()->constrained('provinsis');
                    }
                    if (!Schema::hasColumn('demplot', 'kabupaten_id')) {
                        $table->foreignId('kabupaten_id')->nullable()->constrained('kabupatens');
                    }
                    if (!Schema::hasColumn('demplot', 'kecamatan_id')) {
                        $table->foreignId('kecamatan_id')->nullable()->constrained('kecamatans');
                    }
                    if (!Schema::hasColumn('demplot', 'desa_id')) {
                        $table->foreignId('desa_id')->nullable()->constrained('desas');
                    }
                });
            }

            // Update petani table
            if (Schema::hasTable('petani')) {
                Schema::table('petani', function (Blueprint $table) {
                    // Tambah kolom baru dengan pengecekan (tanpa hapus wilayah_id)
                    if (!Schema::hasColumn('petani', 'provinsi_id')) {
                        $table->foreignId('provinsi_id')->nullable()->constrained('provinsis');
                    }
                    if (!Schema::hasColumn('petani', 'kabupaten_id')) {
                        $table->foreignId('kabupaten_id')->nullable()->constrained('kabupatens');
                    }
                    if (!Schema::hasColumn('petani', 'kecamatan_id')) {
                        $table->foreignId('kecamatan_id')->nullable()->constrained('kecamatans');
                    }
                    if (!Schema::hasColumn('petani', 'desa_id')) {
                        $table->foreignId('desa_id')->nullable()->constrained('desas');
                    }
                });
            }

            // Update users table
            if (Schema::hasTable('users')) {
                Schema::table('users', function (Blueprint $table) {
                    // Hapus kolom wilayah_id jika ada
                    if (Schema::hasColumn('users', 'wilayah_id')) {
                        // Cek dan hapus foreign key jika ada
                        $connection = Schema::getConnection();
                        $dbSchemaManager = $connection->getDoctrineSchemaManager();
                        $doctrineTable = $dbSchemaManager->listTableDetails('users');
                        
                        if ($doctrineTable->hasForeignKey('users_wilayah_id_foreign')) {
                            $table->dropForeign(['wilayah_id']);
                        }
                        
                        $table->dropColumn('wilayah_id');
                    }
                    
                    // Tambah kolom baru dengan pengecekan
                    if (!Schema::hasColumn('users', 'provinsi_id')) {
                        $table->foreignId('provinsi_id')->nullable()->constrained('provinsis');
                    }
                    if (!Schema::hasColumn('users', 'kabupaten_id')) {
                        $table->foreignId('kabupaten_id')->nullable()->constrained('kabupatens');
                    }
                    if (!Schema::hasColumn('users', 'kecamatan_id')) {
                        $table->foreignId('kecamatan_id')->nullable()->constrained('kecamatans');
                    }
                    if (!Schema::hasColumn('users', 'desa_id')) {
                        $table->foreignId('desa_id')->nullable()->constrained('desas');
                    }
                });
            }
        }
    }

    public function down(): void
    {
        // Rollback logic
        Schema::table('poktan', function (Blueprint $table) {
            $table->dropForeign(['provinsi_id']);
            $table->dropForeign(['kabupaten_id']);
            $table->dropForeign(['kecamatan_id']);
            $table->dropForeign(['desa_id']);
            
            $table->dropColumn(['provinsi_id', 'kabupaten_id', 'kecamatan_id', 'desa_id']);
            $table->foreignId('wilayah_id')->nullable()->constrained('wilayah');
        });

        Schema::table('demplot', function (Blueprint $table) {
            $table->dropForeign(['provinsi_id']);
            $table->dropForeign(['kabupaten_id']);
            $table->dropForeign(['kecamatan_id']);
            $table->dropForeign(['desa_id']);
            
            $table->dropColumn(['provinsi_id', 'kabupaten_id', 'kecamatan_id', 'desa_id']);
            $table->foreignId('wilayah_id')->nullable()->constrained('wilayah');
        });

        Schema::table('petani', function (Blueprint $table) {
            $table->dropForeign(['provinsi_id']);
            $table->dropForeign(['kabupaten_id']);
            $table->dropForeign(['kecamatan_id']);
            $table->dropForeign(['desa_id']);
            
            $table->dropColumn(['provinsi_id', 'kabupaten_id', 'kecamatan_id', 'desa_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['provinsi_id']);
            $table->dropForeign(['kabupaten_id']);
            $table->dropForeign(['kecamatan_id']);
            $table->dropForeign(['desa_id']);
            
            $table->dropColumn(['provinsi_id', 'kabupaten_id', 'kecamatan_id', 'desa_id']);
            $table->foreignId('wilayah_id')->nullable()->constrained('wilayah');
        });
    }
};