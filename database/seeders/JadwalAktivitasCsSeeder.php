<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JadwalAktivitasCsSeeder extends Seeder
{
    /**
     * Seed jadwal aktivitas CS berdasarkan Excel JADWAL CS PERIODIK
     * Mapping dari Excel: Area + Hari + Aktivitas + Shift
     * Hari: enum string (senin, selasa, rabu, dll)
     */
    public function run(): void
    {
        // Get areas
        $areas = DB::table('master_area_cs')->pluck('id', 'kode')->toArray();

        // Get aktivitas
        $aktivitas = DB::table('master_aktivitas_cs')->pluck('id', 'kode')->toArray();

        // Get shifts
        $shifts = DB::table('shifts')->pluck('id', 'nama')->toArray();

        // Cari shift ID berdasarkan nama (case insensitive)
        $getShiftId = function ($shiftName) use ($shifts) {
            foreach ($shifts as $nama => $id) {
                if (stripos($nama, $shiftName) !== false) {
                    return $id;
                }
            }
            // Fallback: return first shift
            return reset($shifts) ?: 1;
        };

        // Jadwal per area berdasarkan Excel
        $jadwalData = [
            // LANTAI 1
            'LT1' => [
                'senin' => [
                    ['aktivitas' => 'PLF', 'shift' => 'siang'],
                    ['aktivitas' => 'EXF', 'shift' => 'malam'],
                    ['aktivitas' => 'DSP', 'shift' => 'malam'],
                ],
                'selasa' => [
                    ['aktivitas' => 'TRL', 'shift' => 'pagi'],
                    ['aktivitas' => 'PTD', 'shift' => 'siang'],
                    ['aktivitas' => 'HSD', 'shift' => 'malam'],
                ],
                'rabu' => [
                    ['aktivitas' => 'DSP', 'shift' => 'pagi'],
                    ['aktivitas' => 'VKM', 'shift' => 'siang'],
                    ['aktivitas' => 'HEP', 'shift' => 'siang'],
                    ['aktivitas' => 'LMR', 'shift' => 'malam'],
                ],
                'kamis' => [
                    ['aktivitas' => 'APR', 'shift' => 'pagi'],
                    ['aktivitas' => 'BUF', 'shift' => 'siang'],
                ],
                'jumat' => [
                    ['aktivitas' => 'GLS', 'shift' => 'pagi'],
                    ['aktivitas' => 'TSP', 'shift' => 'siang'],
                ],
            ],

            // IGD
            'IGD' => [
                'senin' => [
                    ['aktivitas' => 'PLF', 'shift' => 'pagi'],
                    ['aktivitas' => 'DSP', 'shift' => 'siang'],
                    ['aktivitas' => 'EXF', 'shift' => 'siang'],
                ],
                'selasa' => [
                    ['aktivitas' => 'PTD', 'shift' => 'pagi'],
                    ['aktivitas' => 'HSD', 'shift' => 'siang'],
                ],
                'rabu' => [
                    ['aktivitas' => 'HEP', 'shift' => 'pagi'],
                    ['aktivitas' => 'TRL', 'shift' => 'pagi'],
                    ['aktivitas' => 'GLS', 'shift' => 'siang'],
                ],
                'kamis' => [
                    ['aktivitas' => 'APR', 'shift' => 'pagi'],
                    ['aktivitas' => 'LMR', 'shift' => 'siang'],
                ],
                'jumat' => [
                    ['aktivitas' => 'BUF', 'shift' => 'pagi'],
                    ['aktivitas' => 'TSP', 'shift' => 'siang'],
                ],
            ],

            // LANTAI 2
            'LT2' => [
                'senin' => [
                    ['aktivitas' => 'PLF', 'shift' => 'pagi'],
                    ['aktivitas' => 'EXF', 'shift' => 'siang'],
                ],
                'selasa' => [
                    ['aktivitas' => 'TRL', 'shift' => 'pagi'],
                    ['aktivitas' => 'PTD', 'shift' => 'siang'],
                    ['aktivitas' => 'HSD', 'shift' => 'malam'],
                ],
                'rabu' => [
                    ['aktivitas' => 'GLS', 'shift' => 'pagi'],
                ],
                'kamis' => [
                    ['aktivitas' => 'TSP', 'shift' => 'pagi'],
                    ['aktivitas' => 'APR', 'shift' => 'siang'],
                    ['aktivitas' => 'BUF', 'shift' => 'malam'],
                ],
                'jumat' => [
                    ['aktivitas' => 'LMR', 'shift' => 'pagi'],
                    ['aktivitas' => 'VKM', 'shift' => 'siang'],
                ],
            ],

            // OK (Kamar Operasi)
            'OK' => [
                'senin' => [
                    ['aktivitas' => 'PLF', 'shift' => 'pagi'],
                    ['aktivitas' => 'EXF', 'shift' => 'siang'],
                ],
                'selasa' => [
                    ['aktivitas' => 'PTD', 'shift' => 'pagi'],
                    ['aktivitas' => 'HSD', 'shift' => 'siang'],
                ],
                'rabu' => [
                    ['aktivitas' => 'TRL', 'shift' => 'pagi'],
                    ['aktivitas' => 'LMR', 'shift' => 'siang'],
                ],
                'kamis' => [
                    ['aktivitas' => 'APR', 'shift' => 'pagi'],
                    ['aktivitas' => 'HEP', 'shift' => 'siang'],
                ],
                'jumat' => [
                    ['aktivitas' => 'GLS', 'shift' => 'pagi'],
                    ['aktivitas' => 'BUF', 'shift' => 'siang'],
                ],
            ],

            // LANTAI 3
            'LT3' => [
                'senin' => [
                    ['aktivitas' => 'PLF', 'shift' => 'pagi'],
                    ['aktivitas' => 'EXF', 'shift' => 'siang'],
                ],
                'selasa' => [
                    ['aktivitas' => 'TRL', 'shift' => 'pagi'],
                    ['aktivitas' => 'PTD', 'shift' => 'siang'],
                ],
                'rabu' => [
                    ['aktivitas' => 'GLS', 'shift' => 'pagi'],
                ],
                'kamis' => [
                    ['aktivitas' => 'TSP', 'shift' => 'pagi'],
                    ['aktivitas' => 'LMR', 'shift' => 'siang'],
                ],
                'jumat' => [
                    ['aktivitas' => 'BUF', 'shift' => 'pagi'],
                    ['aktivitas' => 'APR', 'shift' => 'siang'],
                ],
            ],

            // LANTAI 4
            'LT4' => [
                'senin' => [
                    ['aktivitas' => 'PLF', 'shift' => 'pagi'],
                    ['aktivitas' => 'DSP', 'shift' => 'siang'],
                ],
                'selasa' => [
                    ['aktivitas' => 'EXF', 'shift' => 'pagi'],
                    ['aktivitas' => 'PTD', 'shift' => 'siang'],
                ],
                'rabu' => [
                    ['aktivitas' => 'TRL', 'shift' => 'pagi'],
                    ['aktivitas' => 'HSD', 'shift' => 'siang'],
                ],
                'kamis' => [
                    ['aktivitas' => 'GLS', 'shift' => 'pagi'],
                    ['aktivitas' => 'LMR', 'shift' => 'siang'],
                ],
                'jumat' => [
                    ['aktivitas' => 'BUF', 'shift' => 'pagi'],
                    ['aktivitas' => 'APR', 'shift' => 'siang'],
                ],
            ],

            // UTILITAS
            'UTIL' => [
                'senin' => [
                    ['aktivitas' => 'SPU', 'shift' => 'pagi'],
                    ['aktivitas' => 'PEL', 'shift' => 'siang'],
                ],
                'rabu' => [
                    ['aktivitas' => 'SPU', 'shift' => 'pagi'],
                    ['aktivitas' => 'PEL', 'shift' => 'siang'],
                ],
                'jumat' => [
                    ['aktivitas' => 'SPU', 'shift' => 'pagi'],
                    ['aktivitas' => 'PEL', 'shift' => 'siang'],
                    ['aktivitas' => 'TSP', 'shift' => 'siang'],
                ],
            ],

            // OUTDOOR
            'OUT' => [
                'senin' => [
                    ['aktivitas' => 'SPU', 'shift' => 'pagi'],
                    ['aktivitas' => 'SMP', 'shift' => 'pagi'],
                ],
                'selasa' => [
                    ['aktivitas' => 'SPU', 'shift' => 'pagi'],
                    ['aktivitas' => 'SMP', 'shift' => 'pagi'],
                ],
                'rabu' => [
                    ['aktivitas' => 'SPU', 'shift' => 'pagi'],
                    ['aktivitas' => 'SMP', 'shift' => 'pagi'],
                ],
                'kamis' => [
                    ['aktivitas' => 'SPU', 'shift' => 'pagi'],
                    ['aktivitas' => 'SMP', 'shift' => 'pagi'],
                ],
                'jumat' => [
                    ['aktivitas' => 'SPU', 'shift' => 'pagi'],
                    ['aktivitas' => 'SMP', 'shift' => 'pagi'],
                ],
                'sabtu' => [
                    ['aktivitas' => 'SPU', 'shift' => 'pagi'],
                    ['aktivitas' => 'SMP', 'shift' => 'pagi'],
                ],
            ],
        ];

        foreach ($jadwalData as $areaKode => $hariData) {
            if (!isset($areas[$areaKode])) continue;

            foreach ($hariData as $hari => $aktivitasList) {
                foreach ($aktivitasList as $item) {
                    if (!isset($aktivitas[$item['aktivitas']])) continue;

                    $shiftId = $getShiftId($item['shift']);

                    DB::table('jadwal_aktivitas_cs')->updateOrInsert(
                        [
                            'area_id' => $areas[$areaKode],
                            'aktivitas_id' => $aktivitas[$item['aktivitas']],
                            'hari' => $hari,
                            'shift_id' => $shiftId,
                        ],
                        [
                            'minggu_ke' => null,
                            'is_active' => true,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                }
            }
        }
    }
}
