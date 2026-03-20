<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Master Area CS - Area kerja Cleaning Service
     * Berdasarkan Excel: LT 1, LT 2, LT 3, LT 4, OK, IGD, UTIL, OUTDOOR
     */
    public function up(): void
    {
        Schema::create('master_area_cs', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique(); // LT1, LT2, IGD, OK, dll
            $table->string('nama'); // Lantai 1, Lantai 2, IGD, Kamar Operasi, dll
            $table->string('gedung')->nullable(); // Gedung Utama, Gedung IGD, dll
            $table->text('deskripsi')->nullable();
            $table->enum('zona', ['umum', 'semi_steril', 'steril', 'infeksius'])->default('umum');
            $table->integer('urutan')->default(0); // Untuk sorting tampilan
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_area_cs');
    }
};
