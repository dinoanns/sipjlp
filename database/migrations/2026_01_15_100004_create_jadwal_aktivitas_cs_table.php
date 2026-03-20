<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jadwal Aktivitas CS - Template jadwal pekerjaan per area per hari
     * Berdasarkan Excel: Setiap area punya jadwal aktivitas per hari dalam seminggu
     * Contoh: Area LT 1, Senin: Plafon (Siang), Ex fan (Malam), Dispenser (Malam)
     */
    public function up(): void
    {
        Schema::create('jadwal_aktivitas_cs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_id')->constrained('master_area_cs')->onDelete('cascade');
            $table->foreignId('aktivitas_id')->constrained('master_aktivitas_cs')->onDelete('cascade');
            $table->enum('hari', ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu']);
            $table->foreignId('shift_id')->constrained('shifts')->onDelete('cascade');
            $table->integer('minggu_ke')->nullable()->comment('Untuk aktivitas bulanan: minggu ke-1, ke-2, dst');
            $table->text('catatan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Unique constraint: 1 aktivitas per area per hari per shift
            $table->unique(['area_id', 'aktivitas_id', 'hari', 'shift_id', 'minggu_ke'], 'jadwal_unik');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_aktivitas_cs');
    }
};
