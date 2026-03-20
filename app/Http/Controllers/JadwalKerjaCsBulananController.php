<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\JadwalKerjaCsBulanan;
use App\Models\JadwalShiftCs;
use App\Models\MasterAreaCs;
use App\Models\MasterPekerjaanCs;
use App\Models\Pjlp;
use App\Models\Shift;
use Carbon\Carbon;
use App\Http\Requests\BulkCopyJadwalKerjaCsBulananRequest;
use App\Http\Requests\CopyJadwalKerjaCsBulananRequest;
use App\Http\Requests\StoreJadwalKerjaCsBulananRequest;
use App\Http\Requests\UpdateJadwalKerjaCsBulananRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JadwalKerjaCsBulananController extends Controller
{
    /**
     * Tampilkan jadwal bulanan per area (kalender style)
     */
    public function index(Request $request)
    {
        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);
        $areaId = $request->input('area_id');

        $areas = MasterAreaCs::active()->orderBy('urutan')->get();

        // Default ke area pertama jika tidak dipilih
        if (!$areaId && $areas->isNotEmpty()) {
            $areaId = $areas->first()->id;
        }

        $selectedArea = MasterAreaCs::find($areaId);

        // Generate calendar data
        $startDate = Carbon::create($tahun, $bulan, 1);
        $endDate = $startDate->copy()->endOfMonth();
        $daysInMonth = $startDate->daysInMonth;

        // Get jadwal untuk bulan ini
        $jadwals = JadwalKerjaCsBulanan::with(['shift'])
            ->byArea($areaId)
            ->byBulan($bulan, $tahun)
            ->active()
            ->orderBy('tanggal')
            ->get()
            ->groupBy(function ($item) {
                return $item->tanggal->format('Y-m-d');
            });

        $shifts = Shift::where('is_active', true)->get();

        return view('jadwal-kerja-cs-bulanan.index', compact(
            'areas',
            'selectedArea',
            'areaId',
            'bulan',
            'tahun',
            'startDate',
            'endDate',
            'daysInMonth',
            'jadwals',
            'shifts'
        ));
    }

    /**
     * Form input jadwal untuk tanggal tertentu
     */
    public function create(Request $request)
    {
        $tanggal = $request->input('tanggal', now()->format('Y-m-d'));
        $areaId = $request->input('area_id');

        $areas = MasterAreaCs::active()->orderBy('urutan')->get();
        $shifts = Shift::where('is_active', true)->get();
        $masterPekerjaan = MasterPekerjaanCs::active()->ordered()->get();

        $selectedArea = MasterAreaCs::find($areaId);

        // Get PJLP yang bertugas di area ini pada tanggal ini
        // Ambil dari jadwal_shift_cs yang sudah diinput
        $pjlps = collect();
        if ($areaId) {
            // Ambil PJLP yang punya jadwal shift di area dan tanggal ini
            $pjlpIds = JadwalShiftCs::where('area_id', $areaId)
                ->where('tanggal', $tanggal)
                ->where('status', 'normal')
                ->whereNotNull('shift_id')
                ->pluck('pjlp_id')
                ->unique();

            if ($pjlpIds->isNotEmpty()) {
                $pjlps = Pjlp::whereIn('id', $pjlpIds)
                    ->where('status', 'aktif')
                    ->orderBy('nama')
                    ->get();
            }

            // Jika tidak ada, fallback ke semua PJLP CS (Cleaning Service)
            if ($pjlps->isEmpty()) {
                $pjlps = Pjlp::where('status', 'aktif')
                    ->where('unit', 'cleaning')
                    ->orderBy('nama')
                    ->get();
            }
        }

        // Jadwal existing untuk tanggal ini
        $existingJadwals = collect([]);
        if ($areaId) {
            $existingJadwals = JadwalKerjaCsBulanan::with(['shift', 'pjlp'])
                ->byArea($areaId)
                ->byTanggal($tanggal)
                ->active()
                ->get();
        }

        // Get jadwal shift untuk tanggal ini (untuk menampilkan info shift pegawai)
        $jadwalShifts = [];
        if ($areaId) {
            $jadwalShifts = JadwalShiftCs::with(['pjlp', 'shift'])
                ->where('area_id', $areaId)
                ->where('tanggal', $tanggal)
                ->where('status', 'normal')
                ->get()
                ->keyBy('pjlp_id');
        }

        return view('jadwal-kerja-cs-bulanan.create', compact(
            'tanggal',
            'areas',
            'selectedArea',
            'areaId',
            'shifts',
            'masterPekerjaan',
            'pjlps',
            'existingJadwals',
            'jadwalShifts'
        ));
    }

    /**
     * Simpan jadwal baru
     */
    public function store(StoreJadwalKerjaCsBulananRequest $request)
    {
        $validated = $request->validated();

        // Set nama pekerjaan
        if ($request->pekerjaan_id === 'lainnya') {
            $validated['pekerjaan_id'] = null;
            // pekerjaan sudah ada dari input manual
        } else {
            // Get nama pekerjaan dari master untuk backward compatibility
            $masterPekerjaan = MasterPekerjaanCs::find($validated['pekerjaan_id']);
            $validated['pekerjaan'] = $masterPekerjaan?->nama;
        }

        $validated['created_by'] = auth()->id();
        $validated['is_active'] = true;

        $jadwal = JadwalKerjaCsBulanan::create($validated);

        AuditLog::log('Menambah jadwal kerja CS bulanan', $jadwal, null, $jadwal->toArray());

        return redirect()
            ->route('jadwal-kerja-cs-bulanan.create', [
                'tanggal' => $validated['tanggal'],
                'area_id' => $validated['area_id'],
            ])
            ->with('success', 'Pekerjaan berhasil ditambahkan.');
    }

    /**
     * Edit jadwal
     */
    public function edit(JadwalKerjaCsBulanan $jadwalKerjaCsBulanan)
    {
        $areas = MasterAreaCs::active()->orderBy('urutan')->get();
        $shifts = Shift::where('is_active', true)->get();
        $masterPekerjaan = MasterPekerjaanCs::active()->ordered()->get();

        // Get PJLP yang bertugas di area ini pada tanggal ini
        $pjlpIds = JadwalShiftCs::where('area_id', $jadwalKerjaCsBulanan->area_id)
            ->where('tanggal', $jadwalKerjaCsBulanan->tanggal)
            ->where('status', 'normal')
            ->whereNotNull('shift_id')
            ->pluck('pjlp_id')
            ->unique();

        if ($pjlpIds->isNotEmpty()) {
            $pjlps = Pjlp::whereIn('id', $pjlpIds)
                ->where('status', 'aktif')
                ->orderBy('nama')
                ->get();
        } else {
            // Fallback ke semua PJLP CS (Cleaning Service)
            $pjlps = Pjlp::where('status', 'aktif')
                ->where('unit', 'cleaning')
                ->orderBy('nama')
                ->get();
        }

        return view('jadwal-kerja-cs-bulanan.edit', compact(
            'jadwalKerjaCsBulanan',
            'areas',
            'shifts',
            'masterPekerjaan',
            'pjlps'
        ));
    }

    /**
     * Update jadwal
     */
    public function update(UpdateJadwalKerjaCsBulananRequest $request, JadwalKerjaCsBulanan $jadwalKerjaCsBulanan)
    {
        $validated = $request->validated();

        // Set nama pekerjaan
        if ($request->pekerjaan_id === 'lainnya') {
            $validated['pekerjaan_id'] = null;
            // pekerjaan sudah ada dari input manual
        } else {
            // Get nama pekerjaan dari master untuk backward compatibility
            $masterPekerjaan = MasterPekerjaanCs::find($validated['pekerjaan_id']);
            $validated['pekerjaan'] = $masterPekerjaan?->nama;
        }

        $dataLama = $jadwalKerjaCsBulanan->toArray();
        $jadwalKerjaCsBulanan->update($validated);

        AuditLog::log('Update jadwal kerja CS bulanan', $jadwalKerjaCsBulanan, $dataLama, $jadwalKerjaCsBulanan->fresh()->toArray());

        return redirect()
            ->route('jadwal-kerja-cs-bulanan.create', [
                'tanggal' => $jadwalKerjaCsBulanan->tanggal->format('Y-m-d'),
                'area_id' => $jadwalKerjaCsBulanan->area_id,
            ])
            ->with('success', 'Pekerjaan berhasil diperbarui.');
    }

    /**
     * Hapus jadwal
     */
    public function destroy(JadwalKerjaCsBulanan $jadwalKerjaCsBulanan)
    {
        $tanggal = $jadwalKerjaCsBulanan->tanggal->format('Y-m-d');
        $areaId = $jadwalKerjaCsBulanan->area_id;

        $dataLama = $jadwalKerjaCsBulanan->toArray();
        $jadwalKerjaCsBulanan->update(['is_active' => false]);

        AuditLog::log('Menghapus jadwal kerja CS bulanan', null, $dataLama, null);

        return redirect()
            ->route('jadwal-kerja-cs-bulanan.create', [
                'tanggal' => $tanggal,
                'area_id' => $areaId,
            ])
            ->with('success', 'Pekerjaan berhasil dihapus.');
    }

    /**
     * Copy jadwal dari tanggal lain
     */
    public function copy(CopyJadwalKerjaCsBulananRequest $request)
    {
        $validated = $request->validated();

        $jadwalSumber = JadwalKerjaCsBulanan::byArea($validated['area_id'])
            ->byTanggal($validated['tanggal_sumber'])
            ->active()
            ->get();

        if ($jadwalSumber->isEmpty()) {
            return back()->with('error', 'Tidak ada jadwal di tanggal sumber.');
        }

        DB::beginTransaction();
        try {
            foreach ($jadwalSumber as $jadwal) {
                JadwalKerjaCsBulanan::create([
                    'area_id' => $jadwal->area_id,
                    'tanggal' => $validated['tanggal_tujuan'],
                    'pekerjaan' => $jadwal->pekerjaan,
                    'shift_id' => $jadwal->shift_id,
                    'pjlp_id' => $jadwal->pjlp_id,
                    'tipe_pekerjaan' => $jadwal->tipe_pekerjaan,
                    'keterangan' => $jadwal->keterangan,
                    'created_by' => auth()->id(),
                    'is_active' => true,
                ]);
            }
            DB::commit();

            AuditLog::log('Copy jadwal kerja CS bulanan');

            return redirect()
                ->route('jadwal-kerja-cs-bulanan.create', [
                    'tanggal' => $validated['tanggal_tujuan'],
                    'area_id' => $validated['area_id'],
                ])
                ->with('success', 'Jadwal berhasil disalin.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyalin jadwal: ' . $e->getMessage());
        }
    }

    /**
     * Copy jadwal ke beberapa tanggal sekaligus (bulk copy)
     */
    public function bulkCopy(BulkCopyJadwalKerjaCsBulananRequest $request)
    {
        $validated = $request->validated();

        $jadwalSumber = JadwalKerjaCsBulanan::byArea($validated['area_id'])
            ->byTanggal($validated['tanggal_sumber'])
            ->active()
            ->get();

        if ($jadwalSumber->isEmpty()) {
            return back()->with('error', 'Tidak ada jadwal di tanggal sumber.');
        }

        DB::beginTransaction();
        try {
            $count = 0;
            foreach ($validated['tanggal_tujuan'] as $tglTujuan) {
                foreach ($jadwalSumber as $jadwal) {
                    // Skip jika sudah ada jadwal yang sama
                    $exists = JadwalKerjaCsBulanan::byArea($validated['area_id'])
                        ->byTanggal($tglTujuan)
                        ->where('pekerjaan', $jadwal->pekerjaan)
                        ->where('shift_id', $jadwal->shift_id)
                        ->active()
                        ->exists();

                    if (!$exists) {
                        JadwalKerjaCsBulanan::create([
                            'area_id' => $jadwal->area_id,
                            'tanggal' => $tglTujuan,
                            'pekerjaan' => $jadwal->pekerjaan,
                            'shift_id' => $jadwal->shift_id,
                            'pjlp_id' => $jadwal->pjlp_id,
                            'tipe_pekerjaan' => $jadwal->tipe_pekerjaan,
                            'keterangan' => $jadwal->keterangan,
                            'created_by' => auth()->id(),
                            'is_active' => true,
                        ]);
                        $count++;
                    }
                }
            }
            DB::commit();

            AuditLog::log("Bulk copy jadwal kerja CS bulanan ({$count} entri)");

            return back()->with('success', "{$count} jadwal berhasil disalin.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyalin jadwal: ' . $e->getMessage());
        }
    }
}
