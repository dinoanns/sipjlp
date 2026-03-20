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
        Schema::table('pjlp', function (Blueprint $table) {
            $table->string('badge_number', 50)->nullable()->after('nip')->comment('Nomor badge untuk mesin absen');
            $table->index('badge_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pjlp', function (Blueprint $table) {
            $table->dropIndex(['badge_number']);
            $table->dropColumn('badge_number');
        });
    }
};
