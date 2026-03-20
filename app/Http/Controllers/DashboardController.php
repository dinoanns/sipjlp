<?php

namespace App\Http\Controllers;

use App\Enums\StatusCuti;
use App\Enums\StatusLembarKerja;
use App\Models\Absensi;
use App\Models\BuktiPekerjaanCs;
use App\Models\Cuti;
use App\Models\JadwalShiftCs;
use App\Models\JenisCuti;
use App\Models\LembarKerja;
use App\Models\LogAbsensiMesin;
use App\Models\Pjlp;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->hasRole('pjlp')) {
            return $this->pjlpDashboard($user);
        }

        if ($user->hasRole('koordinator')) {
            return $this->koordinatorDashboard($user);
        }

        if ($user->hasRole('admin')) {
            return $this->adminDashboard();
        }

        if ($user->hasRole('manajemen')) {
            return $this->manajemenDashboard();
        }

        return view('dashboard.default');
    }

    private function pjlpDashboard($user)
    {
        $pjlp = $user->pjlp;

        if (!$pjlp) {
            return view('dashboard.pjlp', [
                'pjlp' => null,
                'jadwalShiftHariIni' => null,
                'cutiPending' => collect(),
                'rekapAbsensiBulanIni' => [],
                'sisaCuti' => [],
            ]);
        }

        $today = Carbon::today();
        $month = $today->month;
        $year = $today->year;

        // Jadwal shift hari ini (untuk Cleaning Service)
        $jadwalShiftHariIni = JadwalShiftCs::where('pjlp_id', $pjlp->id)
            ->whereDate('tanggal', $today)
            ->with('shift')
            ->first();

        $cutiPending = $pjlp->cuti()->pending()->latest()->take(5)->get();

        // Hitung dari log_absensi_mesin
        $logBulanIni = LogAbsensiMesin::where('pjlp_id', $pjlp->id)
            ->whereMonth('check_time', $month)
            ->whereYear('check_time', $year)
            ->get();

        $hariMasuk = $logBulanIni->where('check_type', 'I')
            ->groupBy(fn($item) => $item->check_time->format('Y-m-d'))
            ->count();

        $totalScan = $logBulanIni->count();

        $rekapAbsensiBulanIni = [
            'hari_masuk' => $hariMasuk,
            'total_scan' => $totalScan,
        ];

        // Hitung sisa cuti tahun ini per jenis cuti (exclude Cuti Melahirkan dan Cuti Besar)
        $jenisCutiList = JenisCuti::active()
            ->whereNotIn('nama', ['Cuti Melahirkan', 'Cuti Besar'])
            ->whereNotNull('max_hari')
            ->get();
        $sisaCuti = [];
        foreach ($jenisCutiList as $jenis) {
            $terpakai = Cuti::forPjlp($pjlp->id)
                ->where('jenis_cuti_id', $jenis->id)
                ->where('status', StatusCuti::DISETUJUI)
                ->whereYear('tgl_mulai', $year)
                ->sum('jumlah_hari');

            $sisaCuti[] = [
                'jenis' => $jenis->nama,
                'max_hari' => $jenis->max_hari,
                'terpakai' => $terpakai,
                'sisa' => $jenis->max_hari - $terpakai,
            ];
        }

        return view('dashboard.pjlp', compact(
            'pjlp',
            'jadwalShiftHariIni',
            'cutiPending',
            'rekapAbsensiBulanIni',
            'sisaCuti'
        ));
    }

    private function koordinatorDashboard($user)
    {
        $unit = $user->unit;

        $pjlpQuery = Pjlp::query()->forKoordinator($user);
        $totalPjlp = $pjlpQuery->count();
        $pjlpAktif = $pjlpQuery->active()->count();

        $today = Carbon::today();

        // Cuti pending unit
        $cutiPending = Cuti::whereHas('pjlp', function ($q) use ($user) {
            $q->forKoordinator($user);
        })->pending()->count();

        // Bukti Pekerjaan CS pending validasi
        $buktiPendingCount = BuktiPekerjaanCs::whereHas('pjlp', function ($q) use ($user) {
            $q->forKoordinator($user);
        })
        ->where('is_completed', true)
        ->where('is_validated', false)
        ->where('is_rejected', false)
        ->count();

        // Absensi hari ini unit (dari log mesin)
        $absensiHariIni = LogAbsensiMesin::whereHas('pjlp', function ($q) use ($user) {
            $q->forKoordinator($user);
        })->whereDate('check_time', $today)->count();

        // Recent cuti requests
        $recentCuti = Cuti::whereHas('pjlp', function ($q) use ($user) {
            $q->forKoordinator($user);
        })->with('pjlp', 'jenisCuti')->latest()->take(5)->get();

        // Recent bukti pekerjaan CS yang menunggu validasi
        $recentBuktiPending = BuktiPekerjaanCs::whereHas('pjlp', function ($q) use ($user) {
            $q->forKoordinator($user);
        })
        ->where('is_completed', true)
        ->where('is_validated', false)
        ->where('is_rejected', false)
        ->with(['pjlp', 'jadwalBulanan.area'])
        ->latest()
        ->take(5)
        ->get();

        return view('dashboard.koordinator', compact(
            'unit',
            'totalPjlp',
            'pjlpAktif',
            'cutiPending',
            'buktiPendingCount',
            'absensiHariIni',
            'recentCuti',
            'recentBuktiPending'
        ));
    }

    private function adminDashboard()
    {
        $totalPjlp = Pjlp::count();
        $pjlpSecurity = Pjlp::unit('security')->count();
        $pjlpCleaning = Pjlp::unit('cleaning')->count();
        $pjlpAktif = Pjlp::active()->count();

        $cutiPending = Cuti::pending()->count();
        $lembarKerjaPending = LembarKerja::pending()->count();

        $today = Carbon::today();
        $absensiHariIni = LogAbsensiMesin::whereDate('check_time', $today)->count();

        // Recent activities
        $recentCuti = Cuti::with('pjlp', 'jenisCuti')->latest()->take(5)->get();
        $recentLembarKerja = LembarKerja::with('pjlp')->latest()->take(5)->get();

        return view('dashboard.admin', compact(
            'totalPjlp',
            'pjlpSecurity',
            'pjlpCleaning',
            'pjlpAktif',
            'cutiPending',
            'lembarKerjaPending',
            'absensiHariIni',
            'recentCuti',
            'recentLembarKerja'
        ));
    }

    private function manajemenDashboard()
    {
        $totalPjlp = Pjlp::count();
        $pjlpSecurity = Pjlp::unit('security')->count();
        $pjlpCleaning = Pjlp::unit('cleaning')->count();

        $today = Carbon::today();
        $month = $today->month;
        $year = $today->year;

        // Rekap absensi bulan ini (dari log mesin)
        $logBulanIni = LogAbsensiMesin::whereMonth('check_time', $month)
            ->whereYear('check_time', $year)
            ->whereNotNull('pjlp_id')
            ->get();

        $rekapAbsensi = [
            'total_scan' => $logBulanIni->count(),
            'scan_masuk' => $logBulanIni->where('check_type', 'I')->count(),
            'scan_pulang' => $logBulanIni->where('check_type', 'O')->count(),
            'hari_aktif' => $logBulanIni->groupBy(fn($item) => $item->check_time->format('Y-m-d'))->count(),
        ];

        // Rekap cuti bulan ini
        $rekapCuti = Cuti::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        return view('dashboard.manajemen', compact(
            'totalPjlp',
            'pjlpSecurity',
            'pjlpCleaning',
            'rekapAbsensi',
            'rekapCuti'
        ));
    }
}
