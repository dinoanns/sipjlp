<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CutiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LembarKerjaController;
use App\Http\Controllers\LembarKerjaCsController;
use App\Http\Controllers\JadwalKerjaCsBulananController;
use App\Http\Controllers\JadwalShiftCsController;
use App\Http\Controllers\Master\JenisCutiController;
use App\Http\Controllers\Master\LokasiController;
use App\Http\Controllers\Master\ShiftController;
use App\Http\Controllers\MasterAreaCsController;
use App\Http\Controllers\MasterPekerjaanCsController;
use App\Http\Controllers\TarikAbsenController;
use App\Http\Controllers\PjlpController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Redirect root to login or dashboard
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // PJLP Management
    Route::resource('pjlp', PjlpController::class);

    // Absensi
    Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi.index');
    Route::get('/absensi/import', [AbsensiController::class, 'showImport'])->name('absensi.import');
    Route::post('/absensi/import', [AbsensiController::class, 'import'])->name('absensi.import.store');
    Route::get('/absensi/{absensi}', [AbsensiController::class, 'show'])->name('absensi.show');

    // Tarik Absensi dari Mesin
    Route::prefix('tarik-absen')->name('tarik-absen.')->middleware('can:absensi.import')->group(function () {
        Route::get('/', [TarikAbsenController::class, 'index'])->name('index');
        Route::get('/test-connection', [TarikAbsenController::class, 'testConnection'])->name('test-connection');
        Route::post('/pull', [TarikAbsenController::class, 'pull'])->name('pull');
        Route::post('/map-badge', [TarikAbsenController::class, 'mapBadge'])->name('map-badge');
        Route::get('/unlinked', [TarikAbsenController::class, 'unlinkedBadges'])->name('unlinked');
        Route::get('/summary', [TarikAbsenController::class, 'summary'])->name('summary');
    });

    // Cuti
    Route::get('/cuti', [CutiController::class, 'index'])->name('cuti.index');
    Route::get('/cuti/create', [CutiController::class, 'create'])->name('cuti.create');
    Route::post('/cuti', [CutiController::class, 'store'])->name('cuti.store');
    Route::get('/cuti/{cuti}', [CutiController::class, 'show'])->name('cuti.show');
    Route::post('/cuti/{cuti}/approve', [CutiController::class, 'approve'])->name('cuti.approve');
    Route::post('/cuti/{cuti}/reject', [CutiController::class, 'reject'])->name('cuti.reject');

    // Lembar Kerja
    Route::get('/lembar-kerja', [LembarKerjaController::class, 'index'])->name('lembar-kerja.index');
    Route::get('/lembar-kerja/create', [LembarKerjaController::class, 'create'])->name('lembar-kerja.create');
    Route::post('/lembar-kerja', [LembarKerjaController::class, 'store'])->name('lembar-kerja.store');
    Route::get('/lembar-kerja/{lembarKerja}', [LembarKerjaController::class, 'show'])->name('lembar-kerja.show');
    Route::get('/lembar-kerja/{lembarKerja}/edit', [LembarKerjaController::class, 'edit'])->name('lembar-kerja.edit');
    Route::put('/lembar-kerja/{lembarKerja}', [LembarKerjaController::class, 'update'])->name('lembar-kerja.update');
    Route::post('/lembar-kerja/{lembarKerja}/submit', [LembarKerjaController::class, 'submit'])->name('lembar-kerja.submit');
    Route::post('/lembar-kerja/{lembarKerja}/validate', [LembarKerjaController::class, 'validateLK'])->name('lembar-kerja.validate');
    Route::post('/lembar-kerja/{lembarKerja}/reject', [LembarKerjaController::class, 'reject'])->name('lembar-kerja.reject');
    Route::post('/lembar-kerja/{lembarKerja}/detail', [LembarKerjaController::class, 'addDetail'])->name('lembar-kerja.detail.store');
    Route::delete('/lembar-kerja/detail/{detail}', [LembarKerjaController::class, 'deleteDetail'])->name('lembar-kerja.detail.destroy');

    // Jadwal Shift CS (Input oleh Koordinator - Format Tabel per PJLP per Tanggal)
    Route::prefix('jadwal-shift-cs')->name('jadwal-shift-cs.')->middleware('can:jadwal-cs.manage')->group(function () {
        Route::get('/', [JadwalShiftCsController::class, 'index'])->name('index');
        Route::post('/update', [JadwalShiftCsController::class, 'update'])->name('update');
        Route::post('/bulk-update', [JadwalShiftCsController::class, 'bulkUpdate'])->name('bulk-update');
        Route::post('/copy-from-date', [JadwalShiftCsController::class, 'copyFromDate'])->name('copy-from-date');
    });

    // Jadwal Kerja CS Bulanan (Input Pekerjaan oleh Koordinator)
    Route::prefix('jadwal-kerja-cs-bulanan')->name('jadwal-kerja-cs-bulanan.')->middleware('can:jadwal-cs.manage')->group(function () {
        Route::get('/', [JadwalKerjaCsBulananController::class, 'index'])->name('index');
        Route::get('/create', [JadwalKerjaCsBulananController::class, 'create'])->name('create');
        Route::post('/', [JadwalKerjaCsBulananController::class, 'store'])->name('store');
        Route::post('/copy', [JadwalKerjaCsBulananController::class, 'copy'])->name('copy');
        Route::post('/bulk-copy', [JadwalKerjaCsBulananController::class, 'bulkCopy'])->name('bulk-copy');
        Route::get('/{jadwalKerjaCsBulanan}/edit', [JadwalKerjaCsBulananController::class, 'edit'])->name('edit');
        Route::put('/{jadwalKerjaCsBulanan}', [JadwalKerjaCsBulananController::class, 'update'])->name('update');
        Route::delete('/{jadwalKerjaCsBulanan}', [JadwalKerjaCsBulananController::class, 'destroy'])->name('destroy');
    });

    // Master Pekerjaan CS (Kelola daftar pekerjaan oleh Koordinator)
    Route::prefix('master-pekerjaan-cs')->name('master-pekerjaan-cs.')->middleware('can:cs.pekerjaan.manage')->group(function () {
        Route::get('/', [MasterPekerjaanCsController::class, 'index'])->name('index');
        Route::get('/create', [MasterPekerjaanCsController::class, 'create'])->name('create');
        Route::post('/', [MasterPekerjaanCsController::class, 'store'])->name('store');
        Route::get('/{masterPekerjaanC}/edit', [MasterPekerjaanCsController::class, 'edit'])->name('edit');
        Route::put('/{masterPekerjaanC}', [MasterPekerjaanCsController::class, 'update'])->name('update');
        Route::delete('/{masterPekerjaanC}', [MasterPekerjaanCsController::class, 'destroy'])->name('destroy');
        Route::patch('/{masterPekerjaanC}/toggle-status', [MasterPekerjaanCsController::class, 'toggleStatus'])->name('toggle-status');
    });

    // Lembar Kerja CS (Cleaning Service)
    Route::prefix('lembar-kerja-cs')->name('lembar-kerja-cs.')->middleware('can:lembar-kerja-cs.view-self')->group(function () {
        // Halaman utama lembar kerja PJLP (tampilan seperti Excel)
        Route::get('/', [LembarKerjaCsController::class, 'lembarKerjaPjlp'])->name('index');
        Route::post('/upload-bukti-ajax', [LembarKerjaCsController::class, 'uploadBuktiAjax'])->name('upload-bukti-ajax');

        // Validasi Bukti (Koordinator)
        Route::get('/validasi-bukti', [LembarKerjaCsController::class, 'validasiBuktiIndex'])->name('validasi-bukti-index');
        Route::post('/validasi-bukti/{bukti}', [LembarKerjaCsController::class, 'validasiBukti'])->name('validasi-bukti');

        // Legacy routes (untuk backward compatibility)
        Route::get('/create', [LembarKerjaCsController::class, 'create'])->name('create');
        Route::post('/', [LembarKerjaCsController::class, 'store'])->name('store');
        Route::get('/input-bukti', [LembarKerjaCsController::class, 'inputBukti'])->name('input-bukti');
        Route::post('/upload-bukti/{jadwal}', [LembarKerjaCsController::class, 'uploadBukti'])->name('upload-bukti');
        Route::get('/{lembarKerjaC}', [LembarKerjaCsController::class, 'show'])->name('show');
        Route::get('/{lembarKerjaC}/edit', [LembarKerjaCsController::class, 'edit'])->name('edit');
        Route::put('/{lembarKerjaC}', [LembarKerjaCsController::class, 'update'])->name('update');
        Route::delete('/{lembarKerjaC}', [LembarKerjaCsController::class, 'destroy'])->name('destroy');
        Route::get('/{lembarKerjaC}/submit', [LembarKerjaCsController::class, 'submit'])->name('submit');
        Route::post('/{lembarKerjaC}/validate', [LembarKerjaCsController::class, 'validate'])->name('validate');
        Route::patch('/detail/{detail}', [LembarKerjaCsController::class, 'updateDetail'])->name('detail.update');
        Route::post('/detail/{detail}/foto', [LembarKerjaCsController::class, 'uploadFoto'])->name('detail.foto');
    });

    // Laporan
    Route::prefix('laporan')->name('laporan.')->middleware('can:laporan.view')->group(function () {
        Route::get('/absensi', [LaporanController::class, 'absensi'])->name('absensi');
        Route::get('/absensi/export', [LaporanController::class, 'exportAbsensi'])->name('absensi.export');
        Route::get('/cuti', [LaporanController::class, 'cuti'])->name('cuti');
        Route::get('/cuti/export', [LaporanController::class, 'exportCuti'])->name('cuti.export');
    });

    // Master Data
    Route::prefix('master')->name('master.')->middleware('can:master.manage')->group(function () {
        Route::resource('shift', ShiftController::class);
        Route::resource('jenis-cuti', JenisCutiController::class);
        Route::resource('lokasi', LokasiController::class);
        Route::resource('area-cs', MasterAreaCsController::class);
    });

    // User Management
    Route::resource('users', UserController::class)->middleware('can:user.manage');

    // Audit Log
    Route::get('/audit-log', [AuditLogController::class, 'index'])->name('audit-log.index')->middleware('can:audit-log.view');
});

// Forgot password placeholder
Route::get('/forgot-password', function () {
    return back()->with('error', 'Fitur lupa password belum tersedia. Hubungi Administrator.');
})->name('password.request');
