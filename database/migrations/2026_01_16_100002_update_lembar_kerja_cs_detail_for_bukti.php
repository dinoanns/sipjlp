<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Update lembar_kerja_cs_detail untuk mendukung workflow baru:
     * - Link ke jadwal_kerja_cs_bulanan (bukan aktivitas_id lagi)
     * - PJLP hanya input: bukti foto + timestamp
     * - Koordinator validasi
     */
    public function up(): void
    {
        // Tambah kolom baru untuk link ke jadwal bulanan
        Schema::table('lembar_kerja_cs_detail', function (Blueprint $table) {
            // Foreign key ke jadwal bulanan (nullable dulu untuk data existing)
            $table->foreignId('jadwal_bulanan_id')->nullable()->after('aktivitas_id')
                  ->constrained('jadwal_kerja_cs_bulanan')->onDelete('cascade');

            // Timestamp pengerjaan (diisi otomatis saat upload bukti)
            $table->timestamp('dikerjakan_at')->nullable()->after('is_selesai')
                  ->comment('Waktu saat PJLP upload bukti');

            // Status selesai dikerjakan
            $table->boolean('is_completed')->default(false)->after('dikerjakan_at');

            // Bukti foto (wajib)
            $table->string('foto_bukti')->nullable()->after('foto_sesudah')
                  ->comment('Foto bukti pekerjaan - WAJIB');
        });
    }

    public function down(): void
    {
        Schema::table('lembar_kerja_cs_detail', function (Blueprint $table) {
            $table->dropForeign(['jadwal_bulanan_id']);
            $table->dropColumn(['jadwal_bulanan_id', 'dikerjakan_at', 'is_completed', 'foto_bukti']);
        });
    }
};
