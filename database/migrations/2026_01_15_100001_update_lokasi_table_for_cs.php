<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Update lokasi table untuk mendukung detail ruangan CS
     * - gedung: Gedung A, Gedung B, IGD, dll
     * - lantai: Lantai 1, Lantai 2, Basement, dll
     * - zona: Zona Bersih, Zona Semi-Steril, Zona Steril
     * - jenis_ruangan: Rawat Inap, Rawat Jalan, Administrasi, dll
     */
    public function up(): void
    {
        Schema::table('lokasi', function (Blueprint $table) {
            // gedung dan lantai sudah ada di migrasi awal
            if (!Schema::hasColumn('lokasi', 'zona')) {
                $table->enum('zona', ['umum', 'semi_steril', 'steril', 'infeksius'])->default('umum')->after('lantai');
            }
            if (!Schema::hasColumn('lokasi', 'jenis_ruangan')) {
                $table->string('jenis_ruangan')->nullable()->after('zona');
            }
        });
    }

    public function down(): void
    {
        Schema::table('lokasi', function (Blueprint $table) {
            if (Schema::hasColumn('lokasi', 'zona')) {
                $table->dropColumn('zona');
            }
            if (Schema::hasColumn('lokasi', 'jenis_ruangan')) {
                $table->dropColumn('jenis_ruangan');
            }
        });
    }
};
