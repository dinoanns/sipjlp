<?php

namespace Database\Seeders;

use App\Models\Lokasi;
use Illuminate\Database\Seeder;

class LokasiSeeder extends Seeder
{
    public function run(): void
    {
        $lokasi = [
            // Gedung Utama
            ['nama' => 'Lobby Utama', 'kode' => 'LBY-01', 'gedung' => 'Utama', 'lantai' => '1'],
            ['nama' => 'IGD', 'kode' => 'IGD-01', 'gedung' => 'Utama', 'lantai' => '1'],
            ['nama' => 'Rawat Inap A', 'kode' => 'RIA-01', 'gedung' => 'Utama', 'lantai' => '2'],
            ['nama' => 'Rawat Inap B', 'kode' => 'RIB-01', 'gedung' => 'Utama', 'lantai' => '3'],
            ['nama' => 'Poliklinik', 'kode' => 'PLK-01', 'gedung' => 'Utama', 'lantai' => '1'],
            ['nama' => 'Laboratorium', 'kode' => 'LAB-01', 'gedung' => 'Utama', 'lantai' => '1'],
            ['nama' => 'Radiologi', 'kode' => 'RAD-01', 'gedung' => 'Utama', 'lantai' => '1'],
            ['nama' => 'Farmasi', 'kode' => 'FAR-01', 'gedung' => 'Utama', 'lantai' => '1'],
            ['nama' => 'ICU', 'kode' => 'ICU-01', 'gedung' => 'Utama', 'lantai' => '2'],

            // Gedung Pendukung
            ['nama' => 'Kantor Administrasi', 'kode' => 'ADM-01', 'gedung' => 'Pendukung', 'lantai' => '1'],
            ['nama' => 'Ruang Rapat', 'kode' => 'RPT-01', 'gedung' => 'Pendukung', 'lantai' => '2'],
            ['nama' => 'Gudang Medis', 'kode' => 'GDM-01', 'gedung' => 'Pendukung', 'lantai' => '1'],

            // Area Luar
            ['nama' => 'Parkir Utama', 'kode' => 'PRK-01', 'gedung' => null, 'lantai' => null],
            ['nama' => 'Gerbang Utama', 'kode' => 'GRB-01', 'gedung' => null, 'lantai' => null],
            ['nama' => 'Taman', 'kode' => 'TMN-01', 'gedung' => null, 'lantai' => null],
            ['nama' => 'Area Loading Dock', 'kode' => 'LDK-01', 'gedung' => null, 'lantai' => null],
        ];

        foreach ($lokasi as $lok) {
            Lokasi::create($lok);
        }
    }
}
