<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pjlp_id')->constrained('pjlp')->cascadeOnDelete();
            $table->date('tanggal');
            $table->time('jam_masuk')->nullable();
            $table->time('jam_pulang')->nullable();
            $table->foreignId('shift_id')->nullable()->constrained('shifts')->nullOnDelete();
            $table->enum('status', ['hadir', 'terlambat', 'alpha', 'izin', 'cuti', 'libur'])->default('hadir');
            $table->integer('menit_terlambat')->default(0);
            $table->enum('sumber_data', ['mesin', 'manual'])->default('mesin');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['pjlp_id', 'tanggal']);
        });

        // Tabel staging untuk import data dari mesin absensi
        Schema::create('absensi_raw', function (Blueprint $table) {
            $table->id();
            $table->string('mesin_id', 50)->comment('ID dari mesin absensi');
            $table->string('nip', 50)->nullable()->comment('NIP PJLP dari mesin');
            $table->datetime('tanggal_scan');
            $table->enum('tipe', ['masuk', 'pulang'])->nullable();
            $table->boolean('is_processed')->default(false);
            $table->datetime('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi_raw');
        Schema::dropIfExists('absensi');
    }
};
