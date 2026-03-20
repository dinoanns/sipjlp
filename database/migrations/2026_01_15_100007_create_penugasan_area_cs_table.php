<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Penugasan Area CS - Assignment PJLP ke Area tertentu
     * Menentukan PJLP mana yang bertanggung jawab untuk area mana
     */
    public function up(): void
    {
        Schema::create('penugasan_area_cs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pjlp_id')->constrained('pjlp')->onDelete('cascade');
            $table->foreignId('area_id')->constrained('master_area_cs')->onDelete('cascade');
            $table->foreignId('shift_id')->constrained('shifts')->onDelete('cascade');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('catatan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Index untuk query yang sering
            $table->index(['pjlp_id', 'tanggal_mulai', 'tanggal_selesai']);
            $table->index(['area_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penugasan_area_cs');
    }
};
