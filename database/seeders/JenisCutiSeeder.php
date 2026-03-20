<?php

namespace Database\Seeders;

use App\Models\JenisCuti;
use Illuminate\Database\Seeder;

class JenisCutiSeeder extends Seeder
{
    public function run(): void
    {
        $jenisCuti = [
            [
                'nama' => 'Cuti Tahunan',
                'max_hari' => 12,
                'keterangan' => 'Cuti tahunan untuk pegawai',
            ],
            [
                'nama' => 'Cuti Sakit',
                'max_hari' => null,
                'keterangan' => 'Cuti karena sakit dengan surat keterangan dokter',
            ],
            [
                'nama' => 'Cuti Melahirkan',
                'max_hari' => 90,
                'keterangan' => 'Cuti melahirkan untuk pegawai wanita',
            ],
            [
                'nama' => 'Cuti Besar',
                'max_hari' => 90,
                'keterangan' => 'Cuti besar untuk pegawai yang telah bekerja minimal 6 tahun',
            ],
            [
                'nama' => 'Cuti Alasan Penting',
                'max_hari' => 14,
                'keterangan' => 'Cuti karena alasan penting seperti pernikahan, kematian keluarga, dll',
            ],
        ];

        foreach ($jenisCuti as $jenis) {
            JenisCuti::create($jenis);
        }
    }
}
