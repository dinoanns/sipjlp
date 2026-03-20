<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\JenisCuti;
use Illuminate\Http\Request;

class JenisCutiController extends Controller
{
    public function index()
    {
        $jenisCuti = JenisCuti::latest()->paginate(15);
        return view('master.jenis-cuti.index', compact('jenisCuti'));
    }

    public function create()
    {
        return view('master.jenis-cuti.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'max_hari' => 'nullable|integer|min:1',
            'keterangan' => 'nullable|string',
        ]);

        $jenisCuti = JenisCuti::create($validated);

        AuditLog::log('Menambah jenis cuti', $jenisCuti, null, $jenisCuti->toArray());

        return redirect()->route('master.jenis-cuti.index')
            ->with('success', 'Jenis cuti berhasil ditambahkan.');
    }

    public function edit(JenisCuti $jenisCuti)
    {
        return view('master.jenis-cuti.edit', compact('jenisCuti'));
    }

    public function update(Request $request, JenisCuti $jenisCuti)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'max_hari' => 'nullable|integer|min:1',
            'keterangan' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        $dataLama = $jenisCuti->toArray();
        $jenisCuti->update($validated);

        AuditLog::log('Update jenis cuti', $jenisCuti, $dataLama, $jenisCuti->fresh()->toArray());

        return redirect()->route('master.jenis-cuti.index')
            ->with('success', 'Jenis cuti berhasil diperbarui.');
    }

    public function destroy(JenisCuti $jenisCuti)
    {
        $dataLama = $jenisCuti->toArray();
        $jenisCuti->delete();

        AuditLog::log('Menghapus jenis cuti', null, $dataLama, null);

        return redirect()->route('master.jenis-cuti.index')
            ->with('success', 'Jenis cuti berhasil dihapus.');
    }
}
