@extends('layouts.app')

@section('title', 'Laporan Cuti')

@section('actions')
<a href="{{ route('laporan.cuti.export', request()->query()) }}" class="btn btn-success">
    <i class="ti ti-download me-2"></i> Export PDF
</a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Filter</h3>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-2">
                <label class="form-label">Bulan</label>
                <select name="bulan" class="form-select">
                    @for($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                    </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Tahun</label>
                <select name="tahun" class="form-select">
                    @for($y = now()->year; $y >= now()->year - 2; $y--)
                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            @can('cuti.view-all')
            <div class="col-md-2">
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
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    @foreach(\App\Enums\StatusCuti::cases() as $status)
                    <option value="{{ $status->value }}" {{ request('status') == $status->value ? 'selected' : '' }}>
                        {{ $status->label() }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Total Pengajuan</div>
                </div>
                <div class="h1 mb-0">{{ $summary['total'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Menunggu</div>
                </div>
                <div class="h1 mb-0 text-warning">{{ $summary['menunggu'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Disetujui</div>
                </div>
                <div class="h1 mb-0 text-success">{{ $summary['disetujui'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Total Hari Cuti</div>
                </div>
                <div class="h1 mb-0 text-primary">{{ $summary['total_hari'] }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header">
        <h3 class="card-title">Data Cuti - {{ \Carbon\Carbon::create($tahun, $bulan)->translatedFormat('F Y') }}</h3>
    </div>
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal Pengajuan</th>
                    <th>Nama PJLP</th>
                    <th>Unit</th>
                    <th>Jenis Cuti</th>
                    <th>Periode</th>
                    <th>Jumlah Hari</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cuti as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->tanggal_permohonan->format('d/m/Y') }}</td>
                    <td>{{ $item->pjlp->nama }}</td>
                    <td><span class="badge">{{ $item->pjlp->unit->label() }}</span></td>
                    <td>{{ $item->jenisCuti->nama }}</td>
                    <td>{{ $item->tgl_mulai->format('d/m/Y') }} - {{ $item->tgl_selesai->format('d/m/Y') }}</td>
                    <td>{{ $item->jumlah_hari }} hari</td>
                    <td><span class="badge text-white bg-{{ $item->status->color() }}">{{ $item->status->label() }}</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">
                        Tidak ada data cuti untuk periode ini
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
