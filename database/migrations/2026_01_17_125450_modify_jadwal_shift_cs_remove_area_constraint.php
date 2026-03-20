<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Modifikasi jadwal_shift_cs:
     * - Hapus area_id dari unique constraint
     * - Buat area_id nullable
     * - Unique constraint berdasarkan pjlp_id dan tanggal saja
     */
    public function up(): void
    {
        Schema::table('jadwal_shift_cs', function (Blueprint $table) {
            // Drop unique constraint lama
            $table->dropUnique(['area_id', 'pjlp_id', 'tanggal']);

            // Drop foreign key constraint untuk area_id
            $table->dropForeign(['area_id']);

            // Ubah area_id menjadi nullable
            $table->foreignId('area_id')->nullable()->change();

            // Tambah unique constraint baru tanpa area_id
            $table->unique(['pjlp_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::table('jadwal_shift_cs', function (Blueprint $table) {
            // Drop unique constraint baru
            $table->dropUnique(['pjlp_id', 'tanggal']);

            // Kembalikan area_id ke required
            $table->foreignId('area_id')->nullable(false)->change();

            // Tambah foreign key constraint kembali
            $table->foreign('area_id')->references('id')->on('master_area_cs')->onDelete('cascade');

            // Kembalikan unique constraint lama
            $table->unique(['area_id', 'pjlp_id', 'tanggal']);
        });
    }
};
