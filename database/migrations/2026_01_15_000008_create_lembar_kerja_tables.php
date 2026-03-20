<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lembar_kerja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pjlp_id')->constrained('pjlp')->cascadeOnDelete();
            $table->date('tanggal');
            $table->enum('status', ['draft', 'submitted', 'divalidasi', 'ditolak'])->default('draft');
            $table->timestamps();

            $table->unique(['pjlp_id', 'tanggal']);
        });

        Schema::create('lembar_kerja_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lembar_kerja_id')->constrained('lembar_kerja')->cascadeOnDelete();
            $table->time('jam');
            $table->text('pekerjaan');
            $table->foreignId('lokasi_id')->constrained('lokasi')->cascadeOnDelete();
            $table->text('keterangan')->nullable();
            $table->string('foto', 255)->comment('Wajib diisi - bukti pekerjaan');
            $table->timestamps();
        });

        Schema::create('lembar_kerja_validasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lembar_kerja_id')->constrained('lembar_kerja')->cascadeOnDelete();
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->datetime('validated_at')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lembar_kerja_validasi');
        Schema::dropIfExists('lembar_kerja_detail');
        Schema::dropIfExists('lembar_kerja');
    }
};
