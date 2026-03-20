<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\JadwalShiftCs;
use App\Models\Pjlp;
use App\Models\Shift;
use Carbon\Carbon;
use App\Http\Requests\BulkUpdateJadwalShiftCsRequest;
use App\Http\Requests\CopyJadwalShiftCsRequest;
use App\Http\Requests\UpdateJadwalShiftCsRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JadwalShiftCsController extends Controller
{
    /**
     * Tampilan jadwal shift per PJLP per tanggal (format tabel kalender)
     */
    public function index(Request $request)
    {
        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);

        // Generate tanggal untuk bulan ini
        $startDate = Carbon::create($tahun, $bulan, 1);
        $endDate = $startDate->copy()->endOfMonth();
        $daysInMonth = $startDate->daysInMonth;

        // Ambil semua PJLP Cleaning yang aktif
        $pjlps = Pjlp::active()
            ->unit(\App\Enums\UnitType::CLEANING)
            ->orderBy('nama')
            ->get();

        // Ambil jadwal shift yang sudah ada (tanpa filter area)
        $jadwals = JadwalShiftCs::with(['shift'])
            ->byBulan($bulan, $tahun)
            ->get()
            ->groupBy(function ($item) {
                return $item->pjlp_id . '_' . $item->tanggal->format('Y-m-d');
            });

        $shifts = Shift::where('is_active', true)->get();

        // Generate array tanggal
        $dates = [];
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::create($tahun, $bulan, $day);
            $dates[] = [
                'date' => $date,
                'day' => $day,
                'dayName' => $date->translatedFormat('D'),
                'isWeekend' => $date->isWeekend(),
                'isSunday' => $date->isSunday(),
                'isToday' => $date->isToday(),
            ];
        }

        return view('jadwal-shift-cs.index', compact(
            'bulan',
            'tahun',
            'daysInMonth',
            'dates',
            'pjlps',
            'jadwals',
            'shifts'
        ));
    }

    /**
     * Update jadwal shift via AJAX
     */
    public function update(UpdateJadwalShiftCsRequest $request)
    {

        $jadwal = JadwalShiftCs::updateOrCreate(
            [
                'pjlp_id' => $request->pjlp_id,
                'tanggal' => $request->tanggal,
            ],
            [
                'shift_id' => $request->status === 'normal' ? $request->shift_id : null,
                'status' => $request->status,
                'updated_by' => auth()->id(),
            ]
        );

        // Jika baru dibuat, set created_by
        if ($jadwal->wasRecentlyCreated) {
            $jadwal->created_by = auth()->id();
            $jadwal->save();
        }

        // Refresh untuk memastikan accessor berjalan dengan data terbaru
        $jadwal->refresh();
        $jadwal->load('shift');

        AuditLog::log('Update jadwal shift CS', $jadwal, null, $jadwal->toArray());

        return response()->json([
            'success' => true,
            'jadwal' => $jadwal,
            'display_text' => $jadwal->display_text,
            'display_color' => $jadwal->display_color,
            'display_color_hex' => $jadwal->display_color_hex,
        ]);
    }

    /**
     * Bulk update jadwal (untuk copy ke beberapa tanggal)
     */
    public function bulkUpdate(BulkUpdateJadwalShiftCsRequest $request)
    {

        DB::beginTransaction();
        try {
            foreach ($request->jadwals as $data) {
                JadwalShiftCs::updateOrCreate(
                    [
                        'area_id' => $request->area_id,
                        'pjlp_id' => $data['pjlp_id'],
                        'tanggal' => $data['tanggal'],
                    ],
                    [
                        'shift_id' => $data['status'] === 'normal' ? $data['shift_id'] : null,
                        'status' => $data['status'],
                        'updated_by' => auth()->id(),
                    ]
                );
            }

            DB::commit();

            AuditLog::log('Bulk update jadwal shift CS');

            return response()->json(['success' => true, 'message' => 'Jadwal berhasil disimpan']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Copy jadwal dari tanggal tertentu ke tanggal lain
     */
    public function copyFromDate(CopyJadwalShiftCsRequest $request)
    {

        $sourceJadwals = JadwalShiftCs::byArea($request->area_id)
            ->byTanggal($request->source_date)
            ->get();

        if ($sourceJadwals->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Tidak ada jadwal di tanggal sumber'], 400);
        }

        DB::beginTransaction();
        try {
            $count = 0;
            foreach ($request->target_dates as $targetDate) {
                foreach ($sourceJadwals as $source) {
                    JadwalShiftCs::updateOrCreate(
                        [
                            'area_id' => $request->area_id,
                            'pjlp_id' => $source->pjlp_id,
                            'tanggal' => $targetDate,
                        ],
                        [
                            'shift_id' => $source->shift_id,
                            'status' => $source->status,
                            'updated_by' => auth()->id(),
                        ]
                    );
                    $count++;
                }
            }

            DB::commit();

            AuditLog::log("Copy jadwal shift CS ({$count} entri)");

            return response()->json(['success' => true, 'message' => "{$count} jadwal berhasil disalin"]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Rekapitulasi jadwal
     */
    public function rekap(Request $request)
    {
        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);
        $areaId = $request->input('area_id');

        $areas = MasterAreaCs::active()->orderBy('urutan')->get();

        if (!$areaId && $areas->isNotEmpty()) {
            $areaId = $areas->first()->id;
        }

        $rekap = DB::table('jadwal_shift_cs')
            ->join('pjlp', 'jadwal_shift_cs.pjlp_id', '=', 'pjlp.id')
            ->leftJoin('shifts', 'jadwal_shift_cs.shift_id', '=', 'shifts.id')
            ->select(
                'pjlp.id as pjlp_id',
                'pjlp.nama',
                'pjlp.nip',
                DB::raw('COUNT(CASE WHEN jadwal_shift_cs.status = "normal" THEN 1 END) as total_kerja'),
                DB::raw('COUNT(CASE WHEN jadwal_shift_cs.status = "libur" THEN 1 END) as total_libur'),
                DB::raw('COUNT(CASE WHEN jadwal_shift_cs.status = "libur_hari_raya" THEN 1 END) as total_hari_raya'),
                DB::raw('COUNT(CASE WHEN jadwal_shift_cs.status = "cuti" THEN 1 END) as total_cuti'),
                DB::raw('COUNT(CASE WHEN jadwal_shift_cs.status = "izin" THEN 1 END) as total_izin'),
                DB::raw('COUNT(CASE WHEN jadwal_shift_cs.status = "sakit" THEN 1 END) as total_sakit'),
                DB::raw('COUNT(CASE WHEN jadwal_shift_cs.status = "alpha" THEN 1 END) as total_alpha')
            )
            ->where('jadwal_shift_cs.area_id', $areaId)
            ->whereMonth('jadwal_shift_cs.tanggal', $bulan)
            ->whereYear('jadwal_shift_cs.tanggal', $tahun)
            ->groupBy('pjlp.id', 'pjlp.nama', 'pjlp.nip')
            ->orderBy('pjlp.nama')
            ->get();

        return view('jadwal-shift-cs.rekap', compact('areas', 'areaId', 'bulan', 'tahun', 'rekap'));
    }
}
