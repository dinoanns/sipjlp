<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Konfigurasi Mesin Absensi (ZKTeco via ODBC)
    |--------------------------------------------------------------------------
    |
    | Konfigurasi koneksi ke mesin absensi menggunakan ODBC
    | Bisa menggunakan DSN atau langsung ke file MDB
    |
    */

    'odbc' => [
        // Gunakan DSN name jika sudah setup di ODBC Administrator
        'dsn' => env('MESIN_ABSEN_DSN', ''),
        'username' => env('MESIN_ABSEN_USER', ''),
        'password' => env('MESIN_ABSEN_PASS', ''),

        // Atau langsung ke file MDB (tanpa perlu setup DSN)
        // Jika mdb_path diisi, akan digunakan sebagai pengganti DSN
        'mdb_path' => env('MESIN_ABSEN_MDB_PATH', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Auto Sync Settings
    |--------------------------------------------------------------------------
    */
    'auto_sync' => [
        'enabled' => env('MESIN_ABSEN_AUTO_SYNC', true),
        'interval_seconds' => env('MESIN_ABSEN_INTERVAL', 30),
    ],
];
