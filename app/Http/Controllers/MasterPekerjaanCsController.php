<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\MasterPekerjaanCs;
use App\Http\Requests\StoreMasterPekerjaanCsRequest;
use App\Http\Requests\UpdateMasterPekerjaanCsRequest;
use Illuminate\Http\Request;

class MasterPekerjaanCsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pekerjaan = MasterPekerjaanCs::orderBy('urutan')->get();

        return view('master-pekerjaan-cs.index', compact('pekerjaan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $lastUrutan = MasterPekerjaanCs::max('urutan') ?? 0;

        return view('master-pekerjaan-cs.create', compact('lastUrutan'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMasterPekerjaanCsRequest $request)
    {
        $validated = $request->validated();

        $validated['is_active'] = $request->has('is_active');
        $validated['urutan'] = $validated['urutan'] ?? (MasterPekerjaanCs::max('urutan') + 1);

        $pekerjaan = MasterPekerjaanCs::create($validated);

        AuditLog::log('Menambah master pekerjaan CS', $pekerjaan, null, $pekerjaan->toArray());

        return redirect()
            ->route('master-pekerjaan-cs.index')
            ->with('success', 'Pekerjaan berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MasterPekerjaanCs $masterPekerjaanC)
    {
        return view('master-pekerjaan-cs.edit', ['pekerjaan' => $masterPekerjaanC]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMasterPekerjaanCsRequest $request, MasterPekerjaanCs $masterPekerjaanC)
    {
        $validated = $request->validated();

        $validated['is_active'] = $request->has('is_active');

        $dataLama = $masterPekerjaanC->toArray();
        $masterPekerjaanC->update($validated);

        AuditLog::log('Update master pekerjaan CS', $masterPekerjaanC, $dataLama, $masterPekerjaanC->fresh()->toArray());

        return redirect()
            ->route('master-pekerjaan-cs.index')
            ->with('success', 'Pekerjaan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MasterPekerjaanCs $masterPekerjaanC)
    {
        // Check if pekerjaan is being used
        if ($masterPekerjaanC->jadwalKerja()->exists()) {
            return back()->with('error', 'Pekerjaan tidak dapat dihapus karena sedang digunakan.');
        }

        $dataLama = $masterPekerjaanC->toArray();
        $masterPekerjaanC->delete();

        AuditLog::log('Menghapus master pekerjaan CS', null, $dataLama, null);

        return redirect()
            ->route('master-pekerjaan-cs.index')
            ->with('success', 'Pekerjaan berhasil dihapus.');
    }

    /**
     * Toggle status aktif
     */
    public function toggleStatus(MasterPekerjaanCs $masterPekerjaanC)
    {
        $dataLama = $masterPekerjaanC->toArray();
        $masterPekerjaanC->update([
            'is_active' => !$masterPekerjaanC->is_active
        ]);

        AuditLog::log('Toggle status pekerjaan CS', $masterPekerjaanC, $dataLama, $masterPekerjaanC->fresh()->toArray());

        $status = $masterPekerjaanC->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Pekerjaan berhasil {$status}.");
    }
}
