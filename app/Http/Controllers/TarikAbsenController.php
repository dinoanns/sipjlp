<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\LogAbsensiMesin;
use App\Models\Pjlp;
use App\Services\MesinAbsenService;
use Carbon\Carbon;
use App\Http\Requests\MapBadgeRequest;
use App\Http\Requests\PullAbsenRequest;
use App\Http\Requests\SummaryAbsenRequest;
use Illuminate\Http\Request;

class TarikAbsenController extends Controller
{
    protected MesinAbsenService $mesinAbsenService;

    public function __construct(MesinAbsenService $mesinAbsenService)
    {
        $this->mesinAbsenService = $mesinAbsenService;
    }

    /**
     * Display the tarik absen page
     */
    public function index(Request $request)
    {
        $tanggal = $request->get('tanggal', now()->format('Y-m-d'));
        $badgeNumber = $request->get('badge_number');

        $query = LogAbsensiMesin::with('pjlp')
            ->whereDate('check_time', $tanggal)
            ->orderBy('check_time', 'desc');

        if ($badgeNumber) {
            $query->where('badge_number', 'like', "%{$badgeNumber}%");
        }

        $logs = $query->paginate(50);

        // Stats
        $stats = [
            'total_hari_ini' => LogAbsensiMesin::whereDate('check_time', now())->count(),
            'total_bulan_ini' => LogAbsensiMesin::whereMonth('check_time', now()->month)
                ->whereYear('check_time', now()->year)
                ->count(),
            'belum_terhubung' => LogAbsensiMesin::whereNull('pjlp_id')
                ->whereDate('check_time', '>=', now()->subDays(7))
                ->distinct('badge_number')
                ->count('badge_number'),
        ];

        // PJLP tanpa badge
        $pjlpTanpaBadge = Pjlp::whereNull('badge_number')
            ->orWhere('badge_number', '')
            ->count();

        return view('tarik-absen.index', compact('logs', 'tanggal', 'badgeNumber', 'stats', 'pjlpTanpaBadge'));
    }

    /**
     * Test connection to attendance machine
     */
    public function testConnection()
    {
        $result = $this->mesinAbsenService->testConnection();
        return response()->json($result);
    }

    /**
     * Pull attendance data from machine
     */
    public function pull(PullAbsenRequest $request)
    {

        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        if ($startDate && $endDate) {
            $result = $this->mesinAbsenService->pullByDateRange($startDate, $endDate);
        } else {
            $result = $this->mesinAbsenService->pullAttendanceData();
        }

        if ($request->ajax()) {
            return response()->json($result);
        }

        if ($result['success']) {
            return redirect()->route('tarik-absen.index')
                ->with('success', $result['message']);
        }

        return redirect()->route('tarik-absen.index')
            ->with('error', $result['message']);
    }

    /**
     * Map badge number to PJLP
     */
    public function mapBadge(MapBadgeRequest $request)
    {

        $pjlp = Pjlp::findOrFail($request->pjlp_id);
        $pjlp->update(['badge_number' => $request->badge_number]);

        // Update existing logs with this badge
        LogAbsensiMesin::where('badge_number', $request->badge_number)
            ->whereNull('pjlp_id')
            ->update(['pjlp_id' => $pjlp->id]);

        $updated = LogAbsensiMesin::where('badge_number', $request->badge_number)
            ->where('pjlp_id', $pjlp->id)
            ->count();

        AuditLog::log("Map badge {$request->badge_number} ke PJLP {$pjlp->nama}", $pjlp, null, ['badge_number' => $request->badge_number]);

        return response()->json([
            'success' => true,
            'message' => "Badge {$request->badge_number} berhasil dihubungkan ke {$pjlp->nama}. {$updated} log diupdate.",
        ]);
    }

    /**
     * Get unlinked badges
     */
    public function unlinkedBadges()
    {
        $badges = LogAbsensiMesin::whereNull('pjlp_id')
            ->select('badge_number')
            ->selectRaw('COUNT(*) as total_logs')
            ->selectRaw('MAX(check_time) as last_check')
            ->groupBy('badge_number')
            ->orderBy('last_check', 'desc')
            ->limit(100)
            ->get();

        $pjlps = Pjlp::whereNull('badge_number')
            ->orWhere('badge_number', '')
            ->orderBy('nama')
            ->get(['id', 'nama', 'nip']);

        return view('tarik-absen.unlinked', compact('badges', 'pjlps'));
    }

    /**
     * Get attendance summary for a PJLP
     */
    public function summary(SummaryAbsenRequest $request)
    {

        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $pjlpId = $request->pjlp_id;

        $startDate = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $query = LogAbsensiMesin::with('pjlp')
            ->whereBetween('check_time', [$startDate, $endDate])
            ->orderBy('check_time');

        if ($pjlpId) {
            $query->where('pjlp_id', $pjlpId);
        }

        $logs = $query->get();

        // Group by date and PJLP
        $summary = [];
        foreach ($logs as $log) {
            $date = $log->check_time->format('Y-m-d');
            $badge = $log->badge_number;

            if (!isset($summary[$badge])) {
                $summary[$badge] = [
                    'pjlp' => $log->pjlp,
                    'badge' => $badge,
                    'dates' => [],
                ];
            }

            if (!isset($summary[$badge]['dates'][$date])) {
                $summary[$badge]['dates'][$date] = [
                    'in' => null,
                    'out' => null,
                ];
            }

            if ($log->check_type === 'I' && !$summary[$badge]['dates'][$date]['in']) {
                $summary[$badge]['dates'][$date]['in'] = $log->check_time->format('H:i:s');
            }
            if ($log->check_type === 'O') {
                $summary[$badge]['dates'][$date]['out'] = $log->check_time->format('H:i:s');
            }
        }

        $pjlps = Pjlp::whereNotNull('badge_number')
            ->orderBy('nama')
            ->get(['id', 'nama', 'badge_number']);

        return view('tarik-absen.summary', compact('summary', 'bulan', 'tahun', 'pjlpId', 'pjlps', 'startDate', 'endDate'));
    }
}
