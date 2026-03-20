<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pjlp_id')->constrained('pjlp')->cascadeOnDelete();
            $table->date('tanggal');
            $table->foreignId('shift_id')->constrained('shifts')->cascadeOnDelete();
            $table->foreignId('lokasi_id')->constrained('lokasi')->cascadeOnDelete();
            $table->boolean('is_published')->default(false);
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['pjlp_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal');
    }
};
