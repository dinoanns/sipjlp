<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Master Aktivitas CS - Jenis pekerjaan Cleaning Service
     * Berdasarkan Excel:
     * - Plafon, Ex fan kecil, Dispenser, Pintu dan Dinding, Hiasan Dinding
     * - Trolley alkes (stainless), Glass cleaning, Hepafilter
     * - Vakum karpet, Buffing, APAR & Hydrant, Tempat sampah
     * - Permukaan lemari dengan ketinggian > 2m
     */
    public function up(): void
    {
        Schema::create('master_aktivitas_cs', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique(); // PLF, EXF, DSP, dll
            $table->string('nama'); // Pembersihan Plafon, Pembersihan Ex Fan, dll
            $table->text('deskripsi')->nullable();
            $table->enum('kategori', ['rutin', 'periodik', 'insidentil'])->default('periodik');
            $table->enum('frekuensi', ['harian', 'mingguan', 'bulanan', 'tahunan'])->default('mingguan');
            $table->integer('durasi_standar')->nullable()->comment('Durasi standar dalam menit');
            $table->boolean('perlu_foto')->default(true)->comment('Apakah wajib upload foto bukti');
            $table->boolean('perlu_checklist')->default(true);
            $table->integer('urutan')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_aktivitas_cs');
    }
};
