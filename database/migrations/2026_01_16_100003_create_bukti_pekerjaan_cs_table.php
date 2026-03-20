<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bukti Pekerjaan CS - Diinput oleh PJLP
     * Berisi foto bukti dan timestamp pengerjaan
     */
    public function up(): void
    {
        Schema::create('bukti_pekerjaan_cs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_bulanan_id')->constrained('jadwal_kerja_cs_bulanan')->onDelete('cascade');
            $table->foreignId('pjlp_id')->nullable()->constrained('pjlp')->onDelete('set null');
            $table->string('foto_bukti')->comment('Path foto bukti pekerjaan');
            $table->text('catatan')->nullable()->comment('Catatan dari PJLP');
            $table->timestamp('dikerjakan_at')->comment('Waktu upload bukti (otomatis)');
            $table->boolean('is_completed')->default(true);

            // Validasi oleh Koordinator
            $table->boolean('is_validated')->default(false);
            $table->boolean('is_rejected')->default(false);
            $table->foreignId('validated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('validated_at')->nullable();
            $table->text('catatan_validator')->nullable();

            $table->timestamps();

            // Index
            $table->index(['jadwal_bulanan_id', 'is_completed']);
            $table->index(['pjlp_id', 'dikerjakan_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bukti_pekerjaan_cs');
    }
};
