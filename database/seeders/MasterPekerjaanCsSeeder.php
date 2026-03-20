<?php

namespace Database\Seeders;

use App\Models\MasterPekerjaanCs;
use Illuminate\Database\Seeder;

class MasterPekerjaanCsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pekerjaan = [
            ['nama' => 'Menyapu Lantai', 'kode' => 'SAPU', 'urutan' => 1],
            ['nama' => 'Mengepel Lantai', 'kode' => 'PEL', 'urutan' => 2],
            ['nama' => 'Membersihkan Toilet', 'kode' => 'TOILET', 'urutan' => 3],
            ['nama' => 'Membersihkan Wastafel', 'kode' => 'WASTAFEL', 'urutan' => 4],
            ['nama' => 'Membersihkan Kaca/Jendela', 'kode' => 'KACA', 'urutan' => 5],
            ['nama' => 'Membersihkan Meja/Kursi', 'kode' => 'FURNITURE', 'urutan' => 6],
            ['nama' => 'Membuang Sampah', 'kode' => 'SAMPAH', 'urutan' => 7],
            ['nama' => 'Membersihkan Koridor', 'kode' => 'KORIDOR', 'urutan' => 8],
            ['nama' => 'Membersihkan Lift', 'kode' => 'LIFT', 'urutan' => 9],
            ['nama' => 'Membersihkan Tangga', 'kode' => 'TANGGA', 'urutan' => 10],
            ['nama' => 'Membersihkan AC', 'kode' => 'AC', 'urutan' => 11],
            ['nama' => 'Membersihkan Dinding', 'kode' => 'DINDING', 'urutan' => 12],
            ['nama' => 'Membersihkan Plafon', 'kode' => 'PLAFON', 'urutan' => 13],
            ['nama' => 'Polishing Lantai', 'kode' => 'POLISH', 'urutan' => 14],
            ['nama' => 'Deep Cleaning', 'kode' => 'DEEP', 'urutan' => 15],
            ['nama' => 'Desinfektan Area', 'kode' => 'DESINF', 'urutan' => 16],
            ['nama' => 'Membersihkan Area Parkir', 'kode' => 'PARKIR', 'urutan' => 17],
            ['nama' => 'Membersihkan Taman', 'kode' => 'TAMAN', 'urutan' => 18],
            ['nama' => 'Membersihkan Mushola', 'kode' => 'MUSHOLA', 'urutan' => 19],
            ['nama' => 'Membersihkan Kantin', 'kode' => 'KANTIN', 'urutan' => 20],
        ];

        foreach ($pekerjaan as $item) {
            MasterPekerjaanCs::firstOrCreate(
                ['kode' => $item['kode']],
                [
                    'nama' => $item['nama'],
                    'urutan' => $item['urutan'],
                    'is_active' => true,
                ]
            );
        }
    }
}
