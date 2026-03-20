<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterAreaCsSeeder extends Seeder
{
    /**
     * Seed master area CS berdasarkan sheet Excel
     */
    public function run(): void
    {
        $areas = [
            ['kode' => 'LT1', 'nama' => 'Lantai 1', 'gedung' => 'Gedung Utama', 'zona' => 'umum', 'urutan' => 1],
            ['kode' => 'LT2', 'nama' => 'Lantai 2', 'gedung' => 'Gedung Utama', 'zona' => 'semi_steril', 'urutan' => 2],
            ['kode' => 'LT3', 'nama' => 'Lantai 3', 'gedung' => 'Gedung Utama', 'zona' => 'semi_steril', 'urutan' => 3],
            ['kode' => 'LT4', 'nama' => 'Lantai 4', 'gedung' => 'Gedung Utama', 'zona' => 'umum', 'urutan' => 4],
            ['kode' => 'IGD', 'nama' => 'Instalasi Gawat Darurat', 'gedung' => 'Gedung IGD', 'zona' => 'semi_steril', 'urutan' => 5],
            ['kode' => 'OK', 'nama' => 'Kamar Operasi (OK)', 'gedung' => 'Gedung Utama', 'zona' => 'steril', 'urutan' => 6],
            ['kode' => 'UTIL', 'nama' => 'Utilitas', 'gedung' => 'Area Pendukung', 'zona' => 'umum', 'urutan' => 7],
            ['kode' => 'OUT', 'nama' => 'Outdoor / Area Luar', 'gedung' => 'Area Luar', 'zona' => 'umum', 'urutan' => 8],
        ];

        foreach ($areas as $area) {
            DB::table('master_area_cs')->updateOrInsert(
                ['kode' => $area['kode']],
                array_merge($area, [
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
