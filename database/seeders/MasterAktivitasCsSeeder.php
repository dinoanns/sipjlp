<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterAktivitasCsSeeder extends Seeder
{
    /**
     * Seed master aktivitas CS berdasarkan Excel
     */
    public function run(): void
    {
        $aktivitas = [
            // Pembersihan Periodik Mingguan
            ['kode' => 'PLF', 'nama' => 'Pembersihan Plafon', 'kategori' => 'periodik', 'frekuensi' => 'mingguan', 'durasi_standar' => 60, 'perlu_foto' => true, 'urutan' => 1],
            ['kode' => 'EXF', 'nama' => 'Pembersihan Ex Fan Kecil', 'kategori' => 'periodik', 'frekuensi' => 'mingguan', 'durasi_standar' => 30, 'perlu_foto' => true, 'urutan' => 2],
            ['kode' => 'DSP', 'nama' => 'Pembersihan Dispenser', 'kategori' => 'periodik', 'frekuensi' => 'mingguan', 'durasi_standar' => 15, 'perlu_foto' => true, 'urutan' => 3],
            ['kode' => 'PTD', 'nama' => 'Pembersihan Pintu dan Dinding', 'kategori' => 'periodik', 'frekuensi' => 'mingguan', 'durasi_standar' => 45, 'perlu_foto' => true, 'urutan' => 4],
            ['kode' => 'HSD', 'nama' => 'Pembersihan Hiasan Dinding', 'kategori' => 'periodik', 'frekuensi' => 'mingguan', 'durasi_standar' => 30, 'perlu_foto' => true, 'urutan' => 5],
            ['kode' => 'TRL', 'nama' => 'Pembersihan Trolley Alkes (Stainless)', 'kategori' => 'periodik', 'frekuensi' => 'mingguan', 'durasi_standar' => 30, 'perlu_foto' => true, 'urutan' => 6],
            ['kode' => 'GLS', 'nama' => 'Glass Cleaning (Pembersihan Kaca)', 'kategori' => 'periodik', 'frekuensi' => 'mingguan', 'durasi_standar' => 45, 'perlu_foto' => true, 'urutan' => 7],
            ['kode' => 'HEP', 'nama' => 'Pembersihan Hepafilter', 'kategori' => 'periodik', 'frekuensi' => 'mingguan', 'durasi_standar' => 30, 'perlu_foto' => true, 'urutan' => 8],
            ['kode' => 'VKM', 'nama' => 'Vakum Karpet', 'kategori' => 'periodik', 'frekuensi' => 'mingguan', 'durasi_standar' => 45, 'perlu_foto' => true, 'urutan' => 9],
            ['kode' => 'BUF', 'nama' => 'Buffing Lantai', 'kategori' => 'periodik', 'frekuensi' => 'mingguan', 'durasi_standar' => 60, 'perlu_foto' => true, 'urutan' => 10],
            ['kode' => 'APR', 'nama' => 'Pembersihan APAR & Hydrant', 'kategori' => 'periodik', 'frekuensi' => 'mingguan', 'durasi_standar' => 20, 'perlu_foto' => true, 'urutan' => 11],
            ['kode' => 'TSP', 'nama' => 'Pembersihan Tempat Sampah', 'kategori' => 'periodik', 'frekuensi' => 'mingguan', 'durasi_standar' => 30, 'perlu_foto' => true, 'urutan' => 12],
            ['kode' => 'LMR', 'nama' => 'Pembersihan Permukaan Lemari > 2m', 'kategori' => 'periodik', 'frekuensi' => 'mingguan', 'durasi_standar' => 45, 'perlu_foto' => true, 'urutan' => 13],

            // Pembersihan Rutin Harian
            ['kode' => 'SPU', 'nama' => 'Menyapu Lantai', 'kategori' => 'rutin', 'frekuensi' => 'harian', 'durasi_standar' => 30, 'perlu_foto' => false, 'urutan' => 20],
            ['kode' => 'PEL', 'nama' => 'Mengepel Lantai', 'kategori' => 'rutin', 'frekuensi' => 'harian', 'durasi_standar' => 45, 'perlu_foto' => false, 'urutan' => 21],
            ['kode' => 'DIS', 'nama' => 'Disinfeksi Permukaan', 'kategori' => 'rutin', 'frekuensi' => 'harian', 'durasi_standar' => 30, 'perlu_foto' => false, 'urutan' => 22],
            ['kode' => 'TLT', 'nama' => 'Pembersihan Toilet', 'kategori' => 'rutin', 'frekuensi' => 'harian', 'durasi_standar' => 30, 'perlu_foto' => false, 'urutan' => 23],
            ['kode' => 'SMP', 'nama' => 'Pembuangan Sampah', 'kategori' => 'rutin', 'frekuensi' => 'harian', 'durasi_standar' => 15, 'perlu_foto' => false, 'urutan' => 24],

            // Pembersihan Bulanan
            ['kode' => 'ACU', 'nama' => 'Pembersihan AC Unit', 'kategori' => 'periodik', 'frekuensi' => 'bulanan', 'durasi_standar' => 60, 'perlu_foto' => true, 'urutan' => 30],
            ['kode' => 'GDN', 'nama' => 'Pembersihan Gordyn', 'kategori' => 'periodik', 'frekuensi' => 'bulanan', 'durasi_standar' => 45, 'perlu_foto' => true, 'urutan' => 31],
        ];

        foreach ($aktivitas as $item) {
            DB::table('master_aktivitas_cs')->updateOrInsert(
                ['kode' => $item['kode']],
                array_merge($item, [
                    'perlu_checklist' => true,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
