<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan kolom pjlp_id untuk menentukan pegawai yang ditugaskan
     * mengerjakan pekerjaan tersebut di shift dan area tertentu
     */
    public function up(): void
    {
        Schema::table('jadwal_kerja_cs_bulanan', function (Blueprint $table) {
            $table->foreignId('pjlp_id')->nullable()->after('shift_id')
                  ->constrained('pjlp')->onDelete('set null')
                  ->comment('PJLP yang ditugaskan mengerjakan');
        });
    }

    public function down(): void
    {
        Schema::table('jadwal_kerja_cs_bulanan', function (Blueprint $table) {
            $table->dropForeign(['pjlp_id']);
            $table->dropColumn('pjlp_id');
        });
    }
};
