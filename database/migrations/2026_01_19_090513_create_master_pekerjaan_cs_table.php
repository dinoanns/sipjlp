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
        Schema::create('master_pekerjaan_cs', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('kode')->nullable();
            $table->text('deskripsi')->nullable();
            $table->integer('urutan')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Add pekerjaan_id column to jadwal_kerja_cs_bulanan table
        Schema::table('jadwal_kerja_cs_bulanan', function (Blueprint $table) {
            $table->foreignId('pekerjaan_id')->nullable()->after('pekerjaan')->constrained('master_pekerjaan_cs')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwal_kerja_cs_bulanan', function (Blueprint $table) {
            $table->dropForeign(['pekerjaan_id']);
            $table->dropColumn('pekerjaan_id');
        });

        Schema::dropIfExists('master_pekerjaan_cs');
    }
};
