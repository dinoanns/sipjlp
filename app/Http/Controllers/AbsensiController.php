<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\AuditLog;
use App\Models\LogAbsensiMesin;
use App\Models\Pjlp;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Baca langsung dari log_absensi_mesin
        $query = LogAbsensiMesin::with('pjlp');

        if ($user->hasRole('pjlp')) {
            $pjlp = $user->pjlp;
            if (!$pjlp) {
                return redirect()->route('dashboard')->with('error', 'Profil PJLP tidak ditemukan.');
            }
            $query->where('pjlp_id', $pjlp->id);
        } elseif ($user->hasRole('koordinator')) {
            $query->whereHas('pjlp', function ($q) use ($user) {
                $q->forKoordinator($user);
            });
        }

        // Filter bulan & tahun
        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);
        $query->whereMonth('check_time', $bulan)
              ->whereYear('check_time', $tahun);

        // Filter PJLP
        if ($request->filled('pjlp_id') && !$user->hasRole('pjlp')) {
            $query->where('pjlp_id', $request->pjlp_id);
        }

        $absensi = $query->orderBy('check_time', 'desc')->paginate(31);

        // Get PJLP list for filter
        $pjlpList = collect();
        if (!$user->hasRole('pjlp')) {
            $pjlpQuery = Pjlp::active();
            if ($user->hasRole('koordinator')) {
                $pjlpQuery->forKoordinator($user);
            }
            $pjlpList = $pjlpQuery->get();
        }

        return view('absensi.index', compact('absensi', 'pjlpList', 'bulan', 'tahun'));
    }

    public function show(Absensi $absensi)
    {
        $user = auth()->user();

        if ($user->hasRole('pjlp') && $absensi->pjlp_id !== $user->pjlp?->id) {
            abort(403);
        }

        $absensi->load(['pjlp', 'shift']);

        return view('absensi.show', compact('absensi'));
    }

    public function showImport()
    {
        $this->authorize('import', Absensi::class);
        return view('absensi.import');
    }

    public function import(Request $request)
    {
        $this->authorize('import', Absensi::class);

        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls',
            'tanggal' => 'required|date',
        ]);

        // TODO: Implement actual import logic
        // This would read from the uploaded file or ODBC connection

        AuditLog::log('Import data absensi');

        return back()->with('success', 'Data absensi berhasil diimport.');
    }
}
