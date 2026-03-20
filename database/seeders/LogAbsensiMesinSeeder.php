<?php

namespace Database\Seeders;

use App\Models\LogAbsensiMesin;
use App\Models\Pjlp;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class LogAbsensiMesinSeeder extends Seeder
{
    /**
     * Seeder untuk testing fitur tarik absen tanpa mesin absen fisik.
     * Jalankan: php artisan db:seed --class=LogAbsensiMesinSeeder
     */
    public function run(): void
    {
        // Ambil beberapa PJLP yang ada
        $pjlps = Pjlp::limit(5)->get();

        // Generate badge numbers jika belum ada
        foreach ($pjlps as $index => $pjlp) {
            if (!$pjlp->badge_number) {
                $pjlp->update(['badge_number' => str_pad($index + 1, 3, '0', STR_PAD_LEFT)]);
            }
        }

        // Refresh data
        $pjlps = Pjlp::whereNotNull('badge_number')->limit(5)->get();

        if ($pjlps->isEmpty()) {
            $this->command->warn('Tidak ada data PJLP. Silakan tambahkan PJLP terlebih dahulu.');
            return;
        }

        $this->command->info('Generating dummy attendance data...');

        // Generate data absensi untuk 7 hari terakhir
        $startDate = Carbon::now()->subDays(7);
        $endDate = Carbon::now();

        $records = [];

        for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
            // Skip hari minggu
            if ($date->isSunday()) continue;

            foreach ($pjlps as $pjlp) {
                // Random apakah masuk atau tidak (90% masuk)
                if (rand(1, 100) > 90) continue;

                // Jam masuk (06:00 - 08:30)
                $jamMasuk = $date->copy()->setTime(rand(6, 8), rand(0, 59), rand(0, 59));

                // Jam keluar (15:00 - 18:00)
                $jamKeluar = $date->copy()->setTime(rand(15, 18), rand(0, 59), rand(0, 59));

                $records[] = [
                    'badge_number' => $pjlp->badge_number,
                    'check_time' => $jamMasuk,
                    'check_type' => 'I',
                    'pjlp_id' => $pjlp->id,
                    'is_processed' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $records[] = [
                    'badge_number' => $pjlp->badge_number,
                    'check_time' => $jamKeluar,
                    'check_type' => 'O',
                    'pjlp_id' => $pjlp->id,
                    'is_processed' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Tambah beberapa badge yang belum ter-link (untuk testing mapping)
        $unmappedBadges = ['U01', 'U02', 'U03'];
        foreach ($unmappedBadges as $badge) {
            $records[] = [
                'badge_number' => $badge,
                'check_time' => Carbon::now()->subDays(rand(1, 3))->setTime(rand(7, 8), rand(0, 59)),
                'check_type' => 'I',
                'pjlp_id' => null,
                'is_processed' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insert data
        foreach ($records as $record) {
            LogAbsensiMesin::updateOrCreate(
                [
                    'badge_number' => $record['badge_number'],
                    'check_time' => $record['check_time'],
                    'check_type' => $record['check_type'],
                ],
                $record
            );
        }

        $this->command->info('Generated ' . count($records) . ' attendance records.');
        $this->command->info('Badge yang ter-link: ' . $pjlps->pluck('badge_number')->implode(', '));
        $this->command->info('Badge belum ter-link: ' . implode(', ', $unmappedBadges));
    }
}
