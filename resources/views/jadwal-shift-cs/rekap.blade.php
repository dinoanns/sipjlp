@extends('layouts.app')

@section('title', 'Rekapitulasi Jadwal CS')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    <a href="{{ route('jadwal-shift-cs.index', ['area_id' => $areaId, 'bulan' => $bulan, 'tahun' => $tahun]) }}">
                        &larr; Kembali ke Jadwal
                    </a>
                </div>
                <h2 class="page-title">
                    Rekapitulasi Jadwal CS
                </h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <!-- Filter -->
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('jadwal-shift-cs.rekap') }}" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Area/Lantai</label>
                        <select name="area_id" class="form-select" onchange="this.form.submit()">
                            @foreach($areas as $area)
                                <option value="{{ $area->id }}" {{ $areaId == $area->id ? 'selected' : '' }}>
                                    {{ $area->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Bulan</label>
                        <select name="bulan" class="form-select" onchange="this.form.submit()">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Tahun</label>
                        <select name="tahun" class="form-select" onchange="this.form.submit()">
                            @for($y = now()->year - 1; $y <= now()->year + 1; $y++)
                                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabel Rekap -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    Rekap {{ \Carbon\Carbon::create($tahun, $bulan, 1)->translatedFormat('F Y') }}
                </h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIP</th>
                            <th>Nama Pegawai</th>
                            <th class="text-center">Kerja</th>
                            <th class="text-center">Libur (L)</th>
                            <th class="text-center">Hari Raya (R)</th>
                            <th class="text-center">Cuti</th>
                            <th class="text-center">Izin</th>
                            <th class="text-center">Sakit</th>
                            <th class="text-center">Alpha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rekap as $index => $row)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $row->nip ?? '-' }}</td>
                                <td>{{ $row->nama }}</td>
                                <td class="text-center"><span class="badge bg-success">{{ $row->total_kerja }}</span></td>
                                <td class="text-center"><span class="badge bg-warning text-dark">{{ $row->total_libur }}</span></td>
                                <td class="text-center"><span class="badge bg-danger">{{ $row->total_hari_raya }}</span></td>
                                <td class="text-center"><span class="badge bg-info">{{ $row->total_cuti }}</span></td>
                                <td class="text-center"><span class="badge bg-secondary">{{ $row->total_izin }}</span></td>
                                <td class="text-center"><span class="badge bg-dark">{{ $row->total_sakit }}</span></td>
                                <td class="text-center"><span class="badge bg-danger">{{ $row->total_alpha }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted">Tidak ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
