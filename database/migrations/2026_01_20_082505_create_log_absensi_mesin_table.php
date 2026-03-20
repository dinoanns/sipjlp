<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('log_absensi_mesin', function (Blueprint $table) {
            $table->id();
            $table->string('badge_number', 50)->comment('Nomor badge/ID dari mesin absen');
            $table->dateTime('check_time')->comment('Waktu check in/out dari mesin');
            $table->enum('check_type', ['I', 'O'])->comment('I=In, O=Out');
            $table->foreignId('pjlp_id')->nullable()->constrained('pjlp')->nullOnDelete()->comment('Relasi ke PJLP (jika teridentifikasi)');
            $table->boolean('is_processed')->default(false)->comment('Sudah diproses ke tabel absensi');
            $table->timestamps();

            // Index untuk pencarian
            $table->index(['badge_number', 'check_time']);
            $table->index(['check_time', 'check_type']);
            $table->unique(['badge_number', 'check_time', 'check_type'], 'unique_log_absensi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_absensi_mesin');
    }
};
