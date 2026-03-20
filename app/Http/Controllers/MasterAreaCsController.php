<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMasterAreaCsRequest;
use App\Http\Requests\UpdateMasterAreaCsRequest;
use App\Models\AuditLog;
use App\Models\MasterAreaCs;
use Illuminate\Http\Request;

class MasterAreaCsController extends Controller
{
    public function index()
    {
        $areas = MasterAreaCs::ordered()->get();
        return view('master.area-cs.index', compact('areas'));
    }

    public function create()
    {
        return view('master.area-cs.create');
    }

    public function store(StoreMasterAreaCsRequest $request)
    {
        $validated = $request->validated();

        $validated['is_active'] = true;
        $validated['urutan'] = $validated['urutan'] ?? 0;

        $area = MasterAreaCs::create($validated);

        AuditLog::log('Menambah area CS', $area, null, $area->toArray());

        return redirect()->route('master.area-cs.index')
            ->with('success', 'Area berhasil ditambahkan.');
    }

    public function edit(MasterAreaCs $area_c)
    {
        return view('master.area-cs.edit', ['area' => $area_c]);
    }

    public function update(UpdateMasterAreaCsRequest $request, MasterAreaCs $area_c)
    {
        $validated = $request->validated();

        $validated['is_active'] = $request->has('is_active');

        $dataLama = $area_c->toArray();
        $area_c->update($validated);

        AuditLog::log('Update area CS', $area_c, $dataLama, $area_c->fresh()->toArray());

        return redirect()->route('master.area-cs.index')
            ->with('success', 'Area berhasil diperbarui.');
    }

    public function destroy(MasterAreaCs $area_c)
    {
        // Check if area is being used
        if ($area_c->jadwalAktivitas()->exists() || $area_c->lembarKerja()->exists()) {
            return redirect()->route('master.area-cs.index')
                ->with('error', 'Area tidak dapat dihapus karena masih digunakan.');
        }

        $dataLama = $area_c->toArray();
        $area_c->delete();

        AuditLog::log('Menghapus area CS', null, $dataLama, null);

        return redirect()->route('master.area-cs.index')
            ->with('success', 'Area berhasil dihapus.');
    }
}
