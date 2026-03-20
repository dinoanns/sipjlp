<?php

namespace App\Services;

use App\Models\LogAbsensiMesin;
use App\Models\Pjlp;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MesinAbsenService
{
    protected $odbcConnection = null;
    protected $lastError = null;

    /**
     * Connect to attendance machine via ODBC
     * Supports both DSN and direct MDB file connection
     */
    public function connect(): bool
    {
        try {
            $mdbPath = config('mesin_absen.odbc.mdb_path');
            $dsn = config('mesin_absen.odbc.dsn');
            $user = config('mesin_absen.odbc.username');
            $pass = config('mesin_absen.odbc.password');

            // Jika mdb_path diisi, gunakan connection string langsung
            if (!empty($mdbPath)) {
                if (!file_exists($mdbPath)) {
                    $this->lastError = "File database tidak ditemukan: {$mdbPath}";
                    return false;
                }
                $connectionString = "Driver={Microsoft Access Driver (*.mdb, *.accdb)};Dbq={$mdbPath};";
                $this->odbcConnection = @odbc_connect($connectionString, $user, $pass);
            } else {
                // Gunakan DSN
                if (empty($dsn)) {
                    $this->lastError = "DSN atau MDB path belum dikonfigurasi. Set MESIN_ABSEN_DSN atau MESIN_ABSEN_MDB_PATH di .env";
                    return false;
                }
                $this->odbcConnection = @odbc_connect($dsn, $user, $pass);
            }

            if (!$this->odbcConnection) {
                $this->lastError = odbc_errormsg() ?: "Gagal membuat koneksi ODBC";
                return false;
            }

            return true;
        } catch (\Exception $e) {
            $this->lastError = $e->getMessage();
            Log::error('ODBC Connection Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Disconnect from ODBC
     */
    public function disconnect(): void
    {
        if ($this->odbcConnection) {
            odbc_close($this->odbcConnection);
            $this->odbcConnection = null;
        }
    }

    /**
     * Get last error message
     */
    public function getLastError(): ?string
    {
        return $this->lastError;
    }

    /**
     * Check if ODBC extension is available
     */
    public function isOdbcAvailable(): bool
    {
        return function_exists('odbc_connect');
    }

    /**
     * Pull attendance data from machine
     */
    public function pullAttendanceData(?string $year = null): array
    {
        $year = $year ?? date('Y');
        $result = [
            'success' => false,
            'inserted' => 0,
            'skipped' => 0,
            'errors' => [],
            'message' => '',
        ];

        if (!$this->isOdbcAvailable()) {
            $result['message'] = 'Ekstensi ODBC tidak tersedia di PHP ini.';
            return $result;
        }

        if (!$this->connect()) {
            $result['message'] = 'Gagal koneksi ke mesin absen: ' . $this->lastError;
            return $result;
        }

        try {
            // JOIN ke USERINFO untuk mendapatkan Badgenumber (Ac-No)
            // USERINFO.Badgenumber = Ac-No yang diinput di mesin
            $query = "SELECT USERINFO.Badgenumber, CHECKINOUT.CHECKTIME, CHECKINOUT.CHECKTYPE
                      FROM CHECKINOUT
                      INNER JOIN USERINFO ON USERINFO.USERID = CHECKINOUT.USERID
                      WHERE CHECKINOUT.CHECKTIME LIKE '%{$year}%'
                      ORDER BY CHECKINOUT.CHECKTIME DESC";

            $odbcResult = @odbc_exec($this->odbcConnection, $query);

            if (!$odbcResult) {
                $result['message'] = 'Gagal mengeksekusi query: ' . odbc_errormsg($this->odbcConnection);
                return $result;
            }

            // Map badge number to PJLP
            $pjlpMap = Pjlp::whereNotNull('badge_number')
                ->pluck('id', 'badge_number')
                ->toArray();

            DB::beginTransaction();

            while (odbc_fetch_row($odbcResult)) {
                $badgeNumber = odbc_result($odbcResult, 'Badgenumber');
                $checkTime = odbc_result($odbcResult, 'CHECKTIME');
                $checkType = odbc_result($odbcResult, 'CHECKTYPE');

                // Skip invalid data
                if (empty($badgeNumber) || empty($checkTime)) {
                    continue;
                }

                // Check if already exists
                $exists = LogAbsensiMesin::where('badge_number', $badgeNumber)
                    ->where('check_time', $checkTime)
                    ->where('check_type', $checkType)
                    ->exists();

                if ($exists) {
                    $result['skipped']++;
                    continue;
                }

                // Insert new record
                try {
                    LogAbsensiMesin::create([
                        'badge_number' => $badgeNumber,
                        'check_time' => Carbon::parse($checkTime),
                        'check_type' => $checkType,
                        'pjlp_id' => $pjlpMap[$badgeNumber] ?? null,
                        'is_processed' => false,
                    ]);
                    $result['inserted']++;
                } catch (\Exception $e) {
                    $result['errors'][] = "Badge {$badgeNumber}: " . $e->getMessage();
                }
            }

            DB::commit();

            $result['success'] = true;
            $result['message'] = "Berhasil menarik data. {$result['inserted']} data baru, {$result['skipped']} sudah ada.";

        } catch (\Exception $e) {
            DB::rollBack();
            $result['message'] = 'Error saat menarik data: ' . $e->getMessage();
            Log::error('Pull Attendance Error: ' . $e->getMessage());
        } finally {
            $this->disconnect();
        }

        return $result;
    }

    /**
     * Pull attendance data for specific date range
     */
    public function pullByDateRange(string $startDate, string $endDate): array
    {
        $result = [
            'success' => false,
            'inserted' => 0,
            'skipped' => 0,
            'errors' => [],
            'message' => '',
        ];

        if (!$this->isOdbcAvailable()) {
            $result['message'] = 'Ekstensi ODBC tidak tersedia di PHP ini.';
            return $result;
        }

        if (!$this->connect()) {
            $result['message'] = 'Gagal koneksi ke mesin absen: ' . $this->lastError;
            return $result;
        }

        try {
            // Format tanggal untuk MS Access: #MM/DD/YYYY#
            $startFormatted = Carbon::parse($startDate)->format('m/d/Y');
            $endFormatted = Carbon::parse($endDate)->format('m/d/Y');

            // JOIN ke USERINFO untuk mendapatkan Badgenumber (Ac-No)
            $query = "SELECT USERINFO.Badgenumber, CHECKINOUT.CHECKTIME, CHECKINOUT.CHECKTYPE
                      FROM CHECKINOUT
                      INNER JOIN USERINFO ON USERINFO.USERID = CHECKINOUT.USERID
                      WHERE CHECKINOUT.CHECKTIME >= #{$startFormatted}# AND CHECKINOUT.CHECKTIME <= #{$endFormatted} 23:59:59#
                      ORDER BY CHECKINOUT.CHECKTIME DESC";

            $odbcResult = @odbc_exec($this->odbcConnection, $query);

            if (!$odbcResult) {
                $result['message'] = 'Gagal mengeksekusi query: ' . odbc_errormsg($this->odbcConnection);
                return $result;
            }

            $pjlpMap = Pjlp::whereNotNull('badge_number')
                ->pluck('id', 'badge_number')
                ->toArray();

            DB::beginTransaction();

            while (odbc_fetch_row($odbcResult)) {
                $badgeNumber = odbc_result($odbcResult, 'Badgenumber');
                $checkTime = odbc_result($odbcResult, 'CHECKTIME');
                $checkType = odbc_result($odbcResult, 'CHECKTYPE');

                if (empty($badgeNumber) || empty($checkTime)) {
                    continue;
                }

                $exists = LogAbsensiMesin::where('badge_number', $badgeNumber)
                    ->where('check_time', $checkTime)
                    ->where('check_type', $checkType)
                    ->exists();

                if ($exists) {
                    $result['skipped']++;
                    continue;
                }

                try {
                    LogAbsensiMesin::create([
                        'badge_number' => $badgeNumber,
                        'check_time' => Carbon::parse($checkTime),
                        'check_type' => $checkType,
                        'pjlp_id' => $pjlpMap[$badgeNumber] ?? null,
                        'is_processed' => false,
                    ]);
                    $result['inserted']++;
                } catch (\Exception $e) {
                    $result['errors'][] = "Badge {$badgeNumber}: " . $e->getMessage();
                }
            }

            DB::commit();

            $result['success'] = true;
            $result['message'] = "Berhasil menarik data periode {$startDate} s/d {$endDate}. {$result['inserted']} data baru, {$result['skipped']} sudah ada.";

        } catch (\Exception $e) {
            DB::rollBack();
            $result['message'] = 'Error saat menarik data: ' . $e->getMessage();
        } finally {
            $this->disconnect();
        }

        return $result;
    }

    /**
     * Test connection to attendance machine
     */
    public function testConnection(): array
    {
        $result = [
            'success' => false,
            'message' => '',
            'sample_data' => [],
        ];

        if (!$this->isOdbcAvailable()) {
            $result['message'] = 'Ekstensi ODBC tidak tersedia. Pastikan php_odbc.dll diaktifkan di php.ini';
            return $result;
        }

        if (!$this->connect()) {
            $result['message'] = 'Gagal koneksi ke mesin absen: ' . $this->lastError;
            return $result;
        }

        try {
            // Get sample data (last 5 records) - JOIN ke USERINFO untuk dapat Badgenumber (Ac-No)
            $query = "SELECT TOP 5 USERINFO.Badgenumber, CHECKINOUT.CHECKTIME, CHECKINOUT.CHECKTYPE
                      FROM CHECKINOUT
                      INNER JOIN USERINFO ON USERINFO.USERID = CHECKINOUT.USERID
                      ORDER BY CHECKINOUT.CHECKTIME DESC";

            $odbcResult = @odbc_exec($this->odbcConnection, $query);

            if (!$odbcResult) {
                $result['message'] = 'Koneksi berhasil, tapi gagal query: ' . odbc_errormsg($this->odbcConnection);
                return $result;
            }

            while (odbc_fetch_row($odbcResult)) {
                $result['sample_data'][] = [
                    'badge' => odbc_result($odbcResult, 'Badgenumber'),
                    'time' => odbc_result($odbcResult, 'CHECKTIME'),
                    'type' => odbc_result($odbcResult, 'CHECKTYPE'),
                ];
            }

            $result['success'] = true;
            $result['message'] = 'Koneksi ke mesin absen berhasil!';

        } catch (\Exception $e) {
            $result['message'] = 'Error: ' . $e->getMessage();
        } finally {
            $this->disconnect();
        }

        return $result;
    }
}
