<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuti', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pjlp_id')->constrained('pjlp')->cascadeOnDelete();
            $table->datetime('tanggal_permohonan')->comment('Timestamp dari server, read-only');
            $table->foreignId('jenis_cuti_id')->constrained('jenis_cuti')->cascadeOnDelete();
            $table->text('alasan');
            $table->string('no_telp', 20);
            $table->date('tgl_mulai');
            $table->date('tgl_selesai');
            $table->integer('jumlah_hari')->comment('Dihitung otomatis');
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->datetime('approved_at')->nullable();
            $table->text('alasan_penolakan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuti');
    }
};
