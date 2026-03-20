@extends('layouts.app')

@section('title', 'Laporan Absensi')

@section('actions')
<a href="{{ route('laporan.absensi.export', request()->query()) }}" class="btn btn-success">
    <i class="ti ti-download me-2"></i> Export PDF
</a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Filter Periode</h3>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Bulan</label>
                <select name="bulan" class="form-select">
                    @for($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                    </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Tahun</label>
                <select name="tahun" class="form-select">
                    @for($y = now()->year; $y >= now()->year - 2; $y--)
                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            @can('absensi.view-all')
            <div class="col-md-3">
                <label class="form-label">Unit</label>
                <select name="unit" class="form-select">
                    <option value="">Semua Unit</option>
                    @foreach(\App\Enums\UnitType::cases() as $unit)
                    @if($unit->value !== 'all')
                    <option value="{{ $unit->value }}" {{ request('unit') == $unit->value ? 'selected' : '' }}>
                        {{ $unit->label() }}
                    </option>
                    @endif
                    @endforeach
                </select>
            </div>
            @endcan
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header">
        <h3 class="card-title">Rekap Absensi - {{ \Carbon\Carbon::create($tahun, $bulan)->translatedFormat('F Y') }}</h3>
    </div>
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama PJLP</th>
                    <th>Unit</th>
                    <th class="text-center">Hari Masuk</th>
                    <th class="text-center">Hari Pulang</th>
                    <th class="text-center">Total Scan Masuk</th>
                    <th class="text-center">Total Scan Pulang</th>
                    <th class="text-center">Total Scan</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @forelse($rekapPerPjlp as $rekap)
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>{{ $rekap['pjlp']->nama }}</td>
                    <td>
                        <span class="badge text-white" style="background-color: {{ $rekap['pjlp']->unit->value == 'security' ? '#206bc4' : '#2fb344' }}">
                            {{ $rekap['pjlp']->unit->label() }}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="badge text-white" style="background-color: #2fb344;">{{ $rekap['hari_masuk'] }} hari</span>
                    </td>
                    <td class="text-center">
                        <span class="badge text-white" style="background-color: #d63939;">{{ $rekap['hari_pulang'] }} hari</span>
                    </td>
                    <td class="text-center">
                        <span class="badge text-white" style="background-color: #2fb344;">{{ $rekap['total_masuk'] }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge text-white" style="background-color: #d63939;">{{ $rekap['total_pulang'] }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge text-white" style="background-color: #206bc4;">{{ $rekap['total_scan'] }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">
                        Tidak ada data absensi untuk periode ini
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
