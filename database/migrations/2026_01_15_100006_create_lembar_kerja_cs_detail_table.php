<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Detail Lembar Kerja CS - Checklist aktivitas per lembar kerja
     * Setiap item aktivitas yang harus dikerjakan beserta status penyelesaiannya
     */
    public function up(): void
    {
        Schema::create('lembar_kerja_cs_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lembar_kerja_id')->constrained('lembar_kerja_cs')->onDelete('cascade');
            $table->foreignId('aktivitas_id')->constrained('master_aktivitas_cs')->onDelete('cascade');

            // Status pengerjaan
            $table->boolean('is_dikerjakan')->default(false)->comment('Apakah dikerjakan');
            $table->boolean('is_selesai')->default(false)->comment('Apakah selesai sempurna');

            // Waktu pengerjaan
            $table->time('jam_mulai')->nullable();
            $table->time('jam_selesai')->nullable();

            // Bukti dan catatan
            $table->string('foto_sebelum')->nullable();
            $table->string('foto_sesudah')->nullable();
            $table->text('catatan')->nullable()->comment('Catatan/kendala saat mengerjakan');

            // Jika tidak dikerjakan
            $table->text('alasan_tidak_dikerjakan')->nullable();

            $table->timestamps();

            // Unique: 1 aktivitas per lembar kerja
            $table->unique(['lembar_kerja_id', 'aktivitas_id'], 'detail_unik');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lembar_kerja_cs_detail');
    }
};
