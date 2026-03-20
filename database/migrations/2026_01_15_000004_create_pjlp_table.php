<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pjlp', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nip', 50)->unique();
            $table->string('nama', 255);
            $table->enum('unit', ['security', 'cleaning']);
            $table->string('jabatan', 100);
            $table->string('no_telp', 20)->nullable();
            $table->text('alamat')->nullable();
            $table->date('tanggal_bergabung');
            $table->enum('status', ['aktif', 'nonaktif', 'cuti'])->default('aktif');
            $table->string('foto', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pjlp');
    }
};
