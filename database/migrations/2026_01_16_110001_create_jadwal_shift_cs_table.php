<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jadwal Shift CS per PJLP per Tanggal
     * Koordinator menginput shift untuk setiap PJLP per hari
     */
    public function up(): void
    {
        Schema::create('jadwal_shift_cs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_id')->constrained('master_area_cs')->onDelete('cascade');
            $table->foreignId('pjlp_id')->constrained('pjlp')->onDelete('cascade');
            $table->date('tanggal');
            $table->foreignId('shift_id')->nullable()->constrained('shifts')->onDelete('set null');
            $table->enum('status', ['normal', 'libur', 'libur_hari_raya', 'cuti', 'izin', 'sakit', 'alpha'])
                  ->default('normal')
                  ->comment('normal=kerja, libur=L, libur_hari_raya=R');
            $table->text('keterangan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Unique constraint: satu PJLP hanya bisa punya satu jadwal per tanggal per area
            $table->unique(['area_id', 'pjlp_id', 'tanggal']);

            // Index untuk query performance
            $table->index(['area_id', 'tanggal']);
            $table->index(['pjlp_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_shift_cs');
    }
};
