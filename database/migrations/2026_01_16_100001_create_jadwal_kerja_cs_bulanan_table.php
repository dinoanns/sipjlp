<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jadwal Kerja CS Bulanan - Diinput oleh Koordinator
     * Berisi jadwal pekerjaan per area per tanggal per bulan
     * PJLP tidak bisa edit ini, hanya bisa lihat dan isi bukti
     */
    public function up(): void
    {
        Schema::create('jadwal_kerja_cs_bulanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_id')->constrained('master_area_cs')->onDelete('cascade');
            $table->date('tanggal');
            $table->string('pekerjaan', 255)->comment('Nama pekerjaan yang harus dikerjakan');
            $table->foreignId('shift_id')->constrained('shifts')->onDelete('cascade');
            $table->enum('tipe_pekerjaan', ['wajib_sipantau', 'extra_job'])
                  ->default('wajib_sipantau')
                  ->comment('wajib_sipantau=merah, extra_job=hijau');
            $table->text('keterangan')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade')
                  ->comment('Koordinator yang input');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Index untuk query performance
            $table->index(['area_id', 'tanggal']);
            $table->index(['tanggal', 'shift_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_kerja_cs_bulanan');
    }
};
