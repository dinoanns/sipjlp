<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_parkir', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pjlp_id')->constrained('pjlp')->cascadeOnDelete();
            $table->foreignId('shift_id')->constrained('shifts')->cascadeOnDelete();
            $table->date('tanggal');
            $table->enum('jenis', ['roda_4', 'roda_2']);
            $table->unsignedInteger('jumlah_kendaraan');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index(['pjlp_id', 'tanggal']);
            $table->index('tanggal');
        });

        Schema::create('laporan_parkir_foto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laporan_parkir_id')->constrained('laporan_parkir')->cascadeOnDelete();
            $table->string('path');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_parkir_foto');
        Schema::dropIfExists('laporan_parkir');
    }
};
