<?php

namespace Database\Seeders;

use App\Models\Shift;
use Illuminate\Database\Seeder;

class ShiftSeeder extends Seeder
{
    public function run(): void
    {
        $shifts = [
            [
                'nama' => 'Pagi',
                'jam_mulai' => '07:00',
                'jam_selesai' => '15:00',
                'toleransi_terlambat' => 15,
            ],
            [
                'nama' => 'Siang',
                'jam_mulai' => '15:00',
                'jam_selesai' => '23:00',
                'toleransi_terlambat' => 15,
            ],
            [
                'nama' => 'Malam',
                'jam_mulai' => '23:00',
                'jam_selesai' => '07:00',
                'toleransi_terlambat' => 15,
            ],
        ];

        foreach ($shifts as $shift) {
            Shift::create($shift);
        }
    }
}
