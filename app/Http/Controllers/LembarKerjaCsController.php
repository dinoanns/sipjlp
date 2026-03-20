<?php

namespace App\Http\Controllers;

use App\Models\LembarKerjaCs;
use App\Models\LembarKerjaCsDetail;
use App\Models\MasterAreaCs;
use App\Models\MasterAktivitasCs;
use App\Models\JadwalAktivitasCs;
use App\Models\JadwalKerjaCsBulanan;
use App\Models\PenugasanAreaCs;
use App\Models\BuktiPekerjaanCs;
use App\Models\Pjlp;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class LembarKerjaCsController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', LembarKerjaCs::class);

        $query = LembarKerjaCs::with(['area', 'pjlp', 'validator']);

        // Filter berdasarkan role
        $user = Auth::user();
        if ($user->hasRole('pjlp')) {
            $pjlp = Pjlp::where('user_id', $user->id)->first();
            if ($pjlp) {
                $query->where('pjlp_id', $pjlp->id);
            }
        }

        // Filter tanggal
        if ($request->filled('tanggal_dari')) {
            $query->where('tanggal', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->where('tanggal', '<=', $request->tanggal_sampai);
        }

        // Filter area
        if ($request->filled('area_id')) {
            $query->where('area_id', $request->area_id);
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter shift
        if ($request->filled('shift_id')) {
            $query->where('shift_id', $request->shift_id);
        }

        $lembarKerja = $query->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $areas = MasterAreaCs::active()->ordered()->get();
        $shifts = Shift::where('is_active', true)->get();

        return view('lembar-kerja-cs.index', compact('lembarKerja', 'areas', 'shifts'));
    }

    public function create(Request $request)
    {
        $this->authorize('create', LembarKerjaCs::class);

        $user = Auth::user();
        $pjlp = null;
        $areas = collect();
        $aktivitasPerArea = [];

        if ($user->hasRole('pjlp')) {
            $pjlp = Pjlp::where('user_id', $user->id)->first();

            if ($pjlp) {
                // Ambil area yang ditugaskan ke PJLP ini
                $penugasan = PenugasanAreaCs::with('area')
                    ->where('pjlp_id', $pjlp->id)
                    ->activeOnDate(now())
                    ->get();

                $areas = $penugasan->pluck('area')->unique('id');
            }
        } else {
            // Admin/Koordinator dapat melihat semua area
            $areas = MasterAreaCs::active()->ordered()->get();
        }

        // Pre-select tanggal dan area jika ada parameter
        $selectedDate = $request->get('tanggal', now()->format('Y-m-d'));
        $selectedArea = $request->get('area_id');
        $selectedShift = $request->get('shift_id');

        $shifts = Shift::where('is_active', true)->get();

        return view('lembar-kerja-cs.create', compact(
            'areas',
            'pjlp',
            'selectedDate',
            'selectedArea',
            'selectedShift',
            'shifts'
        ));
    }

    public function store(Request $request)
    {
        $this->authorize('create', LembarKerjaCs::class);

        $request->validate([
            'tanggal' => 'required|date',
            'area_id' => 'required|exists:master_area_cs,id',
            'shift_id' => 'required|exists:shifts,id',
        ]);

        $user = Auth::user();
        $pjlp = Pjlp::where('user_id', $user->id)->first();

        if (!$pjlp) {
            return back()->withErrors(['error' => 'Data PJLP tidak ditemukan']);
        }

        // Cek apakah sudah ada lembar kerja untuk tanggal, area, dan shift yang sama
        $existing = LembarKerjaCs::where('tanggal', $request->tanggal)
            ->where('area_id', $request->area_id)
            ->where('pjlp_id', $pjlp->id)
            ->where('shift_id', $request->shift_id)
            ->first();

        if ($existing) {
            return redirect()->route('lembar-kerja-cs.edit', $existing->id)
                ->with('info', 'Lembar kerja untuk tanggal, area, dan shift ini sudah ada. Anda dapat melanjutkan mengisi.');
        }

        DB::beginTransaction();
        try {
            // Buat lembar kerja
            $lembarKerja = LembarKerjaCs::create([
                'tanggal' => $request->tanggal,
                'area_id' => $request->area_id,
                'pjlp_id' => $pjlp->id,
                'shift_id' => $request->shift_id,
                'status' => LembarKerjaCs::STATUS_DRAFT,
            ]);

            // Ambil jadwal aktivitas untuk area dan hari ini
            $dayOfWeek = Carbon::parse($request->tanggal)->dayOfWeekIso; // 1=Senin, 7=Minggu
            $hari = JadwalAktivitasCs::getHariFromNumber($dayOfWeek);

            $jadwalAktivitas = JadwalAktivitasCs::with('aktivitas')
                ->where('area_id', $request->area_id)
                ->where('hari', $hari)
                ->where('shift_id', $request->shift_id)
                ->where('is_active', true)
                ->get();

            // Buat detail lembar kerja berdasarkan jadwal
            foreach ($jadwalAktivitas as $jadwal) {
                LembarKerjaCsDetail::create([
                    'lembar_kerja_id' => $lembarKerja->id,
                    'aktivitas_id' => $jadwal->aktivitas_id,
                    'is_completed' => false,
                ]);
            }

            DB::commit();

            return redirect()->route('lembar-kerja-cs.edit', $lembarKerja->id)
                ->with('success', 'Lembar kerja berhasil dibuat. Silakan isi checklist aktivitas.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal membuat lembar kerja: ' . $e->getMessage()]);
        }
    }

    public function show(LembarKerjaCs $lembarKerjaC)
    {
        $this->authorize('view', $lembarKerjaC);

        $lembarKerjaC->load(['area', 'pjlp', 'validator', 'details.aktivitas']);

        return view('lembar-kerja-cs.show', [
            'lembarKerja' => $lembarKerjaC
        ]);
    }

    public function edit(LembarKerjaCs $lembarKerjaC)
    {
        $this->authorize('update', $lembarKerjaC);

        // Cek apakah bisa diedit
        if (!$lembarKerjaC->canEdit()) {
            return redirect()->route('lembar-kerja-cs.show', $lembarKerjaC->id)
                ->with('warning', 'Lembar kerja ini tidak dapat diedit karena sudah disubmit atau divalidasi.');
        }

        $lembarKerjaC->load(['area', 'pjlp', 'details.aktivitas']);

        return view('lembar-kerja-cs.edit', [
            'lembarKerja' => $lembarKerjaC
        ]);
    }

    public function update(Request $request, LembarKerjaCs $lembarKerjaC)
    {
        $this->authorize('update', $lembarKerjaC);

        if (!$lembarKerjaC->canEdit()) {
            return back()->withErrors(['error' => 'Lembar kerja tidak dapat diedit']);
        }

        $request->validate([
            'catatan_pjlp' => 'nullable|string|max:1000',
            'details' => 'array',
            'details.*.id' => 'required|exists:lembar_kerja_cs_detail,id',
            'details.*.is_completed' => 'boolean',
            'details.*.catatan' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            // Update catatan utama
            $lembarKerjaC->catatan_pjlp = $request->catatan_pjlp;
            $lembarKerjaC->save();

            // Update detail
            if ($request->has('details')) {
                foreach ($request->details as $detailData) {
                    $detail = LembarKerjaCsDetail::find($detailData['id']);
                    if ($detail && $detail->lembar_kerja_id === $lembarKerjaC->id) {
                        $detail->is_completed = $detailData['is_completed'] ?? false;
                        $detail->catatan = $detailData['catatan'] ?? null;

                        if ($detail->is_completed && !$detail->waktu_selesai) {
                            $detail->waktu_selesai = now();
                        } elseif (!$detail->is_completed) {
                            $detail->waktu_selesai = null;
                        }

                        $detail->save();
                    }
                }
            }

            DB::commit();

            return back()->with('success', 'Lembar kerja berhasil disimpan');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menyimpan: ' . $e->getMessage()]);
        }
    }

    public function updateDetail(Request $request, LembarKerjaCsDetail $detail)
    {
        $lembarKerja = $detail->lembarKerja;

        $this->authorize('update', $lembarKerja);

        if (!$lembarKerja->canEdit()) {
            return response()->json(['error' => 'Lembar kerja tidak dapat diedit'], 403);
        }

        $request->validate([
            'is_completed' => 'boolean',
            'catatan' => 'nullable|string|max:500',
        ]);

        $detail->is_completed = $request->is_completed ?? false;
        $detail->catatan = $request->catatan;

        if ($detail->is_completed && !$detail->waktu_selesai) {
            $detail->waktu_selesai = now();
        } elseif (!$detail->is_completed) {
            $detail->waktu_selesai = null;
        }

        $detail->save();

        return response()->json([
            'success' => true,
            'detail' => $detail,
            'completion_percentage' => $lembarKerja->fresh()->completion_percentage
        ]);
    }

    public function uploadFoto(Request $request, LembarKerjaCsDetail $detail)
    {
        $lembarKerja = $detail->lembarKerja;

        $this->authorize('update', $lembarKerja);

        if (!$lembarKerja->canEdit()) {
            return response()->json(['error' => 'Lembar kerja tidak dapat diedit'], 403);
        }

        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:5120', // Max 5MB
            'tipe' => 'required|in:before,after',
        ]);

        $path = $request->file('foto')->store('lembar-kerja-cs/' . $lembarKerja->id, 'public');

        if ($request->tipe === 'before') {
            // Hapus foto lama jika ada
            if ($detail->foto_before) {
                Storage::disk('public')->delete($detail->foto_before);
            }
            $detail->foto_before = $path;
        } else {
            if ($detail->foto_after) {
                Storage::disk('public')->delete($detail->foto_after);
            }
            $detail->foto_after = $path;
        }

        $detail->save();

        return response()->json([
            'success' => true,
            'path' => Storage::url($path),
            'detail' => $detail
        ]);
    }

    public function submit(LembarKerjaCs $lembarKerjaC)
    {
        $this->authorize('update', $lembarKerjaC);

        if (!$lembarKerjaC->canSubmit()) {
            return back()->withErrors(['error' => 'Lembar kerja tidak dapat disubmit']);
        }

        // Cek apakah ada aktivitas yang belum selesai
        $incomplete = $lembarKerjaC->details()->where('is_completed', false)->count();
        if ($incomplete > 0) {
            return back()->withErrors(['error' => "Masih ada {$incomplete} aktivitas yang belum diselesaikan"]);
        }

        if ($lembarKerjaC->submit()) {
            return redirect()->route('lembar-kerja-cs.show', $lembarKerjaC->id)
                ->with('success', 'Lembar kerja berhasil disubmit untuk validasi');
        }

        return back()->withErrors(['error' => 'Gagal submit lembar kerja']);
    }

    public function validate(Request $request, LembarKerjaCs $lembarKerjaC)
    {
        $this->authorize('validate', $lembarKerjaC);

        if (!$lembarKerjaC->canValidate()) {
            return back()->withErrors(['error' => 'Lembar kerja tidak dapat divalidasi']);
        }

        $request->validate([
            'action' => 'required|in:validate,reject',
            'notes' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();

        if ($request->action === 'validate') {
            if ($lembarKerjaC->validate($user->id, $request->notes)) {
                return back()->with('success', 'Lembar kerja berhasil divalidasi');
            }
        } else {
            if ($lembarKerjaC->reject($user->id, $request->notes)) {
                return back()->with('success', 'Lembar kerja ditolak');
            }
        }

        return back()->withErrors(['error' => 'Gagal memproses validasi']);
    }

    public function destroy(LembarKerjaCs $lembarKerjaC)
    {
        $this->authorize('delete', $lembarKerjaC);

        if (!$lembarKerjaC->isDraft()) {
            return back()->withErrors(['error' => 'Hanya lembar kerja dengan status draft yang dapat dihapus']);
        }

        // Hapus foto-foto terkait
        foreach ($lembarKerjaC->details as $detail) {
            if ($detail->foto_before) {
                Storage::disk('public')->delete($detail->foto_before);
            }
            if ($detail->foto_after) {
                Storage::disk('public')->delete($detail->foto_after);
            }
        }

        $lembarKerjaC->details()->delete();
        $lembarKerjaC->delete();

        return redirect()->route('lembar-kerja-cs.index')
            ->with('success', 'Lembar kerja berhasil dihapus');
    }

    // Rekap per Pegawai
    public function rekapPegawai(Request $request)
    {
        $this->authorize('viewAny', LembarKerjaCs::class);
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        $rekap = DB::table('lembar_kerja_cs')
            ->join('pjlp', 'lembar_kerja_cs.pjlp_id', '=', 'pjlp.id')
            ->join('master_area_cs', 'lembar_kerja_cs.area_id', '=', 'master_area_cs.id')
            ->select(
                'pjlp.id as pjlp_id',
                'pjlp.nama as nama_pjlp',
                'pjlp.nip',
                DB::raw('COUNT(*) as total_lembar_kerja'),
                DB::raw('SUM(CASE WHEN lembar_kerja_cs.status = "validated" THEN 1 ELSE 0 END) as validated'),
                DB::raw('SUM(CASE WHEN lembar_kerja_cs.status = "submitted" THEN 1 ELSE 0 END) as submitted'),
                DB::raw('SUM(CASE WHEN lembar_kerja_cs.status = "draft" THEN 1 ELSE 0 END) as draft'),
                DB::raw('SUM(CASE WHEN lembar_kerja_cs.status = "rejected" THEN 1 ELSE 0 END) as rejected')
            )
            ->whereBetween('lembar_kerja_cs.tanggal', [$startDate, $endDate])
            ->groupBy('pjlp.id', 'pjlp.nama', 'pjlp.nip')
            ->orderBy('pjlp.nama')
            ->get();

        return view('lembar-kerja-cs.rekap-pegawai', compact('rekap', 'startDate', 'endDate'));
    }

    // Rekap per Area
    public function rekapArea(Request $request)
    {
        $this->authorize('viewAny', LembarKerjaCs::class);
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        $rekap = DB::table('lembar_kerja_cs')
            ->join('master_area_cs', 'lembar_kerja_cs.area_id', '=', 'master_area_cs.id')
            ->select(
                'master_area_cs.id as area_id',
                'master_area_cs.nama as nama_area',
                'master_area_cs.kode',
                DB::raw('COUNT(*) as total_lembar_kerja'),
                DB::raw('SUM(CASE WHEN lembar_kerja_cs.status = "validated" THEN 1 ELSE 0 END) as validated'),
                DB::raw('SUM(CASE WHEN lembar_kerja_cs.status = "submitted" THEN 1 ELSE 0 END) as submitted'),
                DB::raw('SUM(CASE WHEN lembar_kerja_cs.status = "draft" THEN 1 ELSE 0 END) as draft')
            )
            ->whereBetween('lembar_kerja_cs.tanggal', [$startDate, $endDate])
            ->groupBy('master_area_cs.id', 'master_area_cs.nama', 'master_area_cs.kode')
            ->orderBy('master_area_cs.urutan')
            ->get();

        return view('lembar-kerja-cs.rekap-area', compact('rekap', 'startDate', 'endDate'));
    }

    /**
     * Halaman input bukti pekerjaan oleh PJLP
     * PJLP hanya bisa upload bukti, tidak bisa edit pekerjaan
     */
    public function inputBukti(Request $request)
    {
        $tanggal = $request->get('tanggal', now()->format('Y-m-d'));
        $areaId = $request->get('area_id');
        $shiftId = $request->get('shift_id');

        $areas = MasterAreaCs::active()->ordered()->get();
        $shifts = Shift::where('is_active', true)->get();

        // Get jadwal pekerjaan dari koordinator
        $query = JadwalKerjaCsBulanan::with(['area', 'shift', 'buktiPekerjaan'])
            ->byTanggal($tanggal)
            ->active();

        if ($areaId) {
            $query->byArea($areaId);
        }

        if ($shiftId) {
            $query->byShift($shiftId);
        }

        $jadwals = $query->orderBy('area_id')
            ->orderBy('shift_id')
            ->get();

        // Cek shift yang sedang aktif
        $activeShift = $this->getActiveShift($shifts);

        return view('lembar-kerja-cs.input-bukti', compact(
            'tanggal',
            'areaId',
            'shiftId',
            'areas',
            'shifts',
            'jadwals',
            'activeShift'
        ));
    }

    /**
     * Upload bukti pekerjaan
     */
    public function uploadBukti(Request $request, JadwalKerjaCsBulanan $jadwal)
    {
        $this->authorize('upload', $jadwal);

        $request->validate([
            'foto_bukti' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'catatan' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $pjlp = Pjlp::where('user_id', $user->id)->first();

        // Validasi shift aktif
        $shift = $jadwal->shift;
        if (!$this->isShiftActive($shift)) {
            return back()->withErrors(['error' => 'Anda hanya bisa mengupload bukti saat shift berlangsung']);
        }

        // Validasi tanggal (hanya bisa upload untuk hari ini)
        if (!$jadwal->tanggal->isToday()) {
            return back()->withErrors(['error' => 'Anda hanya bisa mengupload bukti untuk jadwal hari ini']);
        }

        DB::beginTransaction();
        try {
            // Upload foto
            $path = $request->file('foto_bukti')->store('bukti-pekerjaan-cs/' . $jadwal->tanggal->format('Y-m'), 'public');

            // Simpan bukti pekerjaan
            BuktiPekerjaanCs::create([
                'jadwal_bulanan_id' => $jadwal->id,
                'pjlp_id' => $pjlp?->id,
                'foto_bukti' => $path,
                'catatan' => $request->catatan,
                'dikerjakan_at' => now(),
                'is_completed' => true,
            ]);

            DB::commit();

            return back()->with('success', 'Bukti pekerjaan berhasil diupload');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal mengupload bukti: ' . $e->getMessage()]);
        }
    }

    /**
     * Halaman daftar bukti pekerjaan untuk validasi oleh Koordinator
     */
    public function validasiBuktiIndex(Request $request)
    {
        abort_unless(auth()->user()->can('lembar-kerja-cs.validate'), 403);

        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);
        $areaId = $request->get('area_id');
        $status = $request->get('status');

        $areas = MasterAreaCs::active()->ordered()->get();
        $selectedArea = $areaId ? MasterAreaCs::find($areaId) : null;

        // Query bukti pekerjaan
        $query = BuktiPekerjaanCs::with(['jadwalBulanan.area', 'jadwalBulanan.shift', 'pjlp', 'validator'])
            ->whereHas('jadwalBulanan', function ($q) use ($bulan, $tahun, $areaId) {
                $q->whereMonth('tanggal', $bulan)
                  ->whereYear('tanggal', $tahun);

                if ($areaId) {
                    $q->where('area_id', $areaId);
                }
            });

        // Filter status
        if ($status === 'pending') {
            $query->where('is_completed', true)
                  ->where('is_validated', false)
                  ->where('is_rejected', false);
        } elseif ($status === 'validated') {
            $query->where('is_validated', true);
        } elseif ($status === 'rejected') {
            $query->where('is_rejected', true);
        }

        $buktiList = $query->orderBy('created_at', 'desc')->paginate(20);

        // Statistik
        $statsQuery = BuktiPekerjaanCs::whereHas('jadwalBulanan', function ($q) use ($bulan, $tahun, $areaId) {
            $q->whereMonth('tanggal', $bulan)
              ->whereYear('tanggal', $tahun);
            if ($areaId) {
                $q->where('area_id', $areaId);
            }
        });

        $stats = [
            'pending' => (clone $statsQuery)->where('is_completed', true)->where('is_validated', false)->where('is_rejected', false)->count(),
            'validated' => (clone $statsQuery)->where('is_validated', true)->count(),
            'rejected' => (clone $statsQuery)->where('is_rejected', true)->count(),
        ];

        return view('lembar-kerja-cs.validasi-bukti', compact(
            'buktiList',
            'areas',
            'selectedArea',
            'areaId',
            'bulan',
            'tahun',
            'status',
            'stats'
        ));
    }

    /**
     * Validasi bukti pekerjaan oleh Koordinator
     */
    public function validasiBukti(Request $request, BuktiPekerjaanCs $bukti)
    {
        $this->authorize('validate', $bukti);

        $request->validate([
            'action' => 'required|in:validate,reject',
            'catatan_validator' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();

        if ($request->action === 'validate') {
            $bukti->update([
                'is_validated' => true,
                'validated_by' => $user->id,
                'validated_at' => now(),
                'catatan_validator' => $request->catatan_validator,
            ]);
            return back()->with('success', 'Bukti pekerjaan berhasil divalidasi');
        } else {
            $bukti->update([
                'is_validated' => false,
                'is_rejected' => true,
                'validated_by' => $user->id,
                'validated_at' => now(),
                'catatan_validator' => $request->catatan_validator,
            ]);
            return back()->with('success', 'Bukti pekerjaan ditolak');
        }
    }

    /**
     * Cek shift yang sedang aktif
     */
    private function getActiveShift($shifts)
    {
        $now = now();

        foreach ($shifts as $shift) {
            if ($this->isShiftActive($shift)) {
                return $shift;
            }
        }

        return null;
    }

    /**
     * Cek apakah shift sedang aktif
     */
    private function isShiftActive(?Shift $shift): bool
    {
        // Jika shift null atau jam tidak diset, anggap selalu aktif
        if (!$shift || !$shift->jam_masuk || !$shift->jam_keluar) {
            return true;
        }

        $now = now();
        $today = now()->startOfDay();

        $shiftStart = $today->copy()->setTimeFromTimeString($shift->jam_masuk);
        $shiftEnd = $today->copy()->setTimeFromTimeString($shift->jam_keluar);

        // Handle shift malam (melewati tengah malam)
        if ($shiftEnd < $shiftStart) {
            $shiftEnd->addDay();
        }

        // Toleransi 30 menit sebelum dan sesudah
        $toleransi = 30;
        $shiftStart->subMinutes($toleransi);
        $shiftEnd->addMinutes($toleransi);

        return $now->between($shiftStart, $shiftEnd);
    }

    /**
     * Halaman Lembar Kerja CS untuk PJLP
     * Tampilan seperti Excel: dropdown area, tabel per hari dalam 1 bulan
     */
    public function lembarKerjaPjlp(Request $request)
    {
        $user = Auth::user();
        $pjlp = Pjlp::where('user_id', $user->id)->first();

        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);
        $areaId = $request->get('area_id');

        // Get semua area
        $areas = MasterAreaCs::active()->orderBy('urutan')->get();

        // Default ke area pertama jika tidak dipilih
        if (!$areaId && $areas->isNotEmpty()) {
            $areaId = $areas->first()->id;
        }

        $selectedArea = MasterAreaCs::find($areaId);

        // Generate tanggal dalam bulan
        $startDate = Carbon::create($tahun, $bulan, 1);
        $endDate = $startDate->copy()->endOfMonth();
        $daysInMonth = $startDate->daysInMonth;

        // Ambil jadwal pekerjaan untuk area dan bulan ini (dari Koordinator)
        $jadwals = JadwalKerjaCsBulanan::with(['shift', 'pjlp', 'semuaBukti.pjlp'])
            ->where('area_id', $areaId)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->active()
            ->orderBy('tanggal')
            ->orderBy('shift_id')
            ->get()
            ->groupBy(function ($item) {
                return $item->tanggal->format('Y-m-d');
            });

        // Generate data per hari
        $dataPerHari = [];
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $tanggal = Carbon::create($tahun, $bulan, $day);
            $dateKey = $tanggal->format('Y-m-d');

            $dataPerHari[$dateKey] = [
                'tanggal' => $tanggal,
                'hari' => $tanggal->translatedFormat('l'),
                'isWeekend' => $tanggal->isWeekend(),
                'isToday' => $tanggal->isToday(),
                'jadwals' => $jadwals[$dateKey] ?? collect(),
            ];
        }

        $shifts = Shift::where('is_active', true)->get();

        return view('lembar-kerja-cs.pjlp-lembar-kerja', compact(
            'areas',
            'selectedArea',
            'areaId',
            'bulan',
            'tahun',
            'dataPerHari',
            'shifts',
            'pjlp'
        ));
    }

    /**
     * Upload bukti pekerjaan via AJAX
     */
    public function uploadBuktiAjax(Request $request)
    {
        $request->validate([
            'jadwal_id' => 'required|exists:jadwal_kerja_cs_bulanan,id',
            'foto_bukti' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        $user = Auth::user();
        $pjlp = Pjlp::where('user_id', $user->id)->first();

        $jadwal = JadwalKerjaCsBulanan::findOrFail($request->jadwal_id);

        $this->authorize('upload', $jadwal);

        // Validasi tanggal (hanya bisa upload untuk hari ini)
        if (!$jadwal->tanggal->isToday()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda hanya bisa mengupload bukti untuk jadwal hari ini'
            ], 422);
        }

        // Validasi shift aktif
        $shift = $jadwal->shift;
        if ($shift && !$this->isShiftActive($shift)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda hanya bisa mengupload bukti saat shift berlangsung (' . $shift->jam_masuk . ' - ' . $shift->jam_keluar . ')'
            ], 422);
        }

        try {
            // Upload foto
            $path = $request->file('foto_bukti')->store('bukti-pekerjaan-cs/' . $jadwal->tanggal->format('Y-m'), 'public');

            // Simpan bukti pekerjaan
            $bukti = BuktiPekerjaanCs::create([
                'jadwal_bulanan_id' => $jadwal->id,
                'pjlp_id' => $pjlp?->id,
                'foto_bukti' => $path,
                'dikerjakan_at' => now(),
                'is_completed' => true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bukti pekerjaan berhasil diupload',
                'bukti' => [
                    'id' => $bukti->id,
                    'foto_url' => asset('storage/' . $path),
                    'waktu' => now()->format('H:i'),
                    'pjlp_nama' => $pjlp?->nama ?? $user->name,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupload bukti: ' . $e->getMessage()
            ], 500);
        }
    }
}
