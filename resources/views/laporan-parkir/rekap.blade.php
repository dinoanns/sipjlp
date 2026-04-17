@extends('layouts.app')

@section('title', 'Rekap Laporan Parkir')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Security</div>
                <h2 class="page-title">Rekap Laporan Parkir Menginap</h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">

        {{-- Filter --}}
        <div class="card mb-3">
            <div class="card-body py-2">
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-sm-auto">
                        <label class="form-label mb-1 small">Bulan</label>
                        <select name="bulan" class="form-select form-select-sm">
                            @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create(null, $m)->translatedFormat('F') }}
                            </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-sm-auto">
                        <label class="form-label mb-1 small">Tahun</label>
                        <select name="tahun" class="form-select form-select-sm">
                            @for($y = now()->year; $y >= now()->year - 2; $y--)
                            <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-sm">
                        <label class="form-label mb-1 small">Cari Petugas</label>
                        <input type="text" name="search" class="form-control form-control-sm"
                               placeholder="Nama petugas..." value="{{ $search }}">
                    </div>
                    <div class="col-sm-auto">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="ti ti-search me-1"></i>Filter
                        </button>
                    </div>
                    <div class="col-sm-auto ms-auto">
                        @include('exports.partials.buttons', [
                            'route'  => 'export.laporan-parkir',
                            'params' => ['bulan' => $bulan, 'tahun' => $tahun, 'search' => $search],
                        ])
                    </div>
                </form>
            </div>
        </div>

        {{-- Stats --}}
        <div class="row g-3 mb-3">
            <div class="col-sm-4">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-blue text-white avatar"><i class="ti ti-car"></i></span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">{{ $totalRoda4 }}</div>
                                <div class="text-muted small">Total Kendaraan Roda 4</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-green text-white avatar"><i class="ti ti-motorbike"></i></span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">{{ $totalRoda2 }}</div>
                                <div class="text-muted small">Total Kendaraan Roda 2</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-purple text-white avatar"><i class="ti ti-parking"></i></span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">{{ $totalRoda4 + $totalRoda2 }}</div>
                                <div class="text-muted small">Total Semua Kendaraan</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel Rekap --}}
        <div class="card">
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th class="text-center">Roda 4</th>
                            <th class="text-center">Roda 2</th>
                            <th class="text-center">Total</th>
                            <th style="width:60px"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rekapHarian as $rekap)
                        <tr>
                            <td>{{ $rekap['tanggal'] }}</td>
                            <td class="text-center">
                                <span class="badge bg-blue-lt text-blue">{{ $rekap['roda_4'] }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-green-lt text-green">{{ $rekap['roda_2'] }}</span>
                            </td>
                            <td class="text-center fw-semibold">{{ $rekap['roda_4'] + $rekap['roda_2'] }}</td>
                            <td>
                                <button class="btn btn-sm btn-ghost-secondary" type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#detail-{{ $loop->index }}">
                                    <i class="ti ti-chevron-down"></i>
                                </button>
                            </td>
                        </tr>
                        <tr class="collapse" id="detail-{{ $loop->index }}">
                            <td colspan="5" class="bg-light p-0">
                                <table class="table table-sm mb-0">
                                    <thead>
                                        <tr class="table-secondary">
                                            <th>Petugas</th>
                                            <th>Shift</th>
                                            <th>Jenis</th>
                                            <th class="text-center">Jumlah</th>
                                            <th>Jam</th>
                                            <th>Catatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($rekap['laporan'] as $lap)
                                        <tr>
                                            <td>{{ $lap->pjlp->nama ?? '-' }}</td>
                                            <td>{{ $lap->shift->nama ?? '-' }}</td>
                                            <td>
                                                @if($lap->jenis === 'roda_4')
                                                <span class="badge bg-blue-lt text-blue"><i class="ti ti-car me-1"></i>Roda 4</span>
                                                @else
                                                <span class="badge bg-green-lt text-green"><i class="ti ti-motorbike me-1"></i>Roda 2</span>
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $lap->jumlah_kendaraan }}</td>
                                            <td>{{ $lap->created_at->format('H:i') }}</td>
                                            <td class="text-muted small">{{ $lap->catatan ?? '-' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="ti ti-parking-off me-1"></i>Tidak ada data laporan parkir
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($rekapHarian->count() > 0)
                    <tfoot>
                        <tr class="fw-bold table-light">
                            <td>Total</td>
                            <td class="text-center">{{ $totalRoda4 }}</td>
                            <td class="text-center">{{ $totalRoda2 }}</td>
                            <td class="text-center">{{ $totalRoda4 + $totalRoda2 }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
