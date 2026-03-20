<?php

namespace App\Http\Controllers;

use App\Enums\StatusCuti;
use App\Models\AuditLog;
use App\Models\Cuti;
use App\Models\JenisCuti;
use App\Http\Requests\RejectCutiRequest;
use App\Http\Requests\StoreCutiRequest;
use Illuminate\Http\Request;

class CutiController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Cuti::with(['pjlp', 'jenisCuti', 'approvedBy']);

        // Filter berdasarkan role
        if ($user->hasRole('pjlp')) {
            $pjlp = $user->pjlp;
            if (!$pjlp) {
                return redirect()->route('dashboard')->with('error', 'Profil PJLP tidak ditemukan.');
            }
            $query->forPjlp($pjlp->id);
        } elseif ($user->hasRole('koordinator')) {
            $query->whereHas('pjlp', function ($q) use ($user) {
                $q->forKoordinator($user);
            });
        }

        // Filter status
        if ($request->filled('status')) {
            $query->status($request->status);
        }

        // Filter tanggal
        if ($request->filled('dari')) {
            $query->whereDate('tgl_mulai', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->whereDate('tgl_selesai', '<=', $request->sampai);
        }

        $cuti = $query->latest()->paginate(15);
        $jenisCutiList = JenisCuti::active()->get();

        return view('cuti.index', compact('cuti', 'jenisCutiList'));
    }

    public function create()
    {
        $user = auth()->user();
        $pjlp = $user->pjlp;

        if (!$pjlp) {
            return redirect()->route('dashboard')->with('error', 'Profil PJLP tidak ditemukan. Hubungi Administrator.');
        }

        $jenisCutiList = JenisCuti::active()->get();

        return view('cuti.create', compact('pjlp', 'jenisCutiList'));
    }

    public function store(StoreCutiRequest $request)
    {
        $user = auth()->user();
        $pjlp = $user->pjlp;

        if (!$pjlp) {
            return redirect()->route('dashboard')->with('error', 'Profil PJLP tidak ditemukan.');
        }

        $validated = $request->validated();

        $cuti = Cuti::create([
            'pjlp_id' => $pjlp->id,
            'jenis_cuti_id' => $validated['jenis_cuti_id'],
            'alasan' => $validated['alasan'],
            'no_telp' => $validated['no_telp'],
            'tgl_mulai' => $validated['tgl_mulai'],
            'tgl_selesai' => $validated['tgl_selesai'],
            'status' => StatusCuti::MENUNGGU,
        ]);

        AuditLog::log('Mengajukan cuti', $cuti, null, $cuti->toArray());

        return redirect()->route('cuti.index')
            ->with('success', 'Pengajuan cuti berhasil dikirim dan menunggu persetujuan koordinator.');
    }

    public function show(Cuti $cuti)
    {
        $user = auth()->user();

        // Check access
        if ($user->hasRole('pjlp') && $cuti->pjlp_id !== $user->pjlp?->id) {
            abort(403);
        }

        if ($user->hasRole('koordinator')) {
            $pjlp = $cuti->pjlp;
            if ($user->unit && $user->unit->value !== 'all' && $pjlp->unit->value !== $user->unit->value) {
                abort(403);
            }
        }

        $cuti->load(['pjlp', 'jenisCuti', 'approvedBy']);

        return view('cuti.show', compact('cuti'));
    }

    public function approve(Request $request, Cuti $cuti)
    {
        $this->authorize('approve', $cuti);

        if ($cuti->status !== StatusCuti::MENUNGGU) {
            return back()->with('error', 'Cuti ini sudah diproses sebelumnya.');
        }

        $dataLama = $cuti->toArray();
        $cuti->approve(auth()->user());

        AuditLog::log('Menyetujui cuti', $cuti, $dataLama, $cuti->fresh()->toArray());

        return back()->with('success', 'Cuti berhasil disetujui.');
    }

    public function reject(RejectCutiRequest $request, Cuti $cuti)
    {
        $this->authorize('approve', $cuti);

        if ($cuti->status !== StatusCuti::MENUNGGU) {
            return back()->with('error', 'Cuti ini sudah diproses sebelumnya.');
        }

        $validated = $request->validated();

        $dataLama = $cuti->toArray();
        $cuti->reject(auth()->user(), $validated['alasan_penolakan']);

        AuditLog::log('Menolak cuti', $cuti, $dataLama, $cuti->fresh()->toArray());

        return back()->with('success', 'Cuti berhasil ditolak.');
    }
}
