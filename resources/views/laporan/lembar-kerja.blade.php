@extends('layouts.app')

@section('title', 'Laporan Lembar Kerja')

@section('actions')
<a href="{{ route('laporan.lembar-kerja.export', request()->query()) }}" class="btn btn-success">
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
            @can('lembar-kerja.view-all')
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
                    @foreach(\App\Enums\StatusLembarKerja::cases() as $status)
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
                    <div class="subheader">Total Lembar Kerja</div>
                </div>
                <div class="h1 mb-0">{{ $summary['total'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Draft</div>
                </div>
                <div class="h1 mb-0 text-secondary">{{ $summary['draft'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Submitted</div>
                </div>
                <div class="h1 mb-0 text-warning">{{ $summary['submitted'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Divalidasi</div>
                </div>
                <div class="h1 mb-0 text-success">{{ $summary['divalidasi'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Ditolak</div>
                </div>
                <div class="h1 mb-0 text-danger">{{ $summary['ditolak'] }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header">
        <h3 class="card-title">Data Lembar Kerja - {{ \Carbon\Carbon::create($tahun, $bulan)->translatedFormat('F Y') }}</h3>
    </div>
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Nama PJLP</th>
                    <th>Unit</th>
                    <th>Jumlah Kegiatan</th>
                    <th>Status</th>
                    <th>Validator</th>
                </tr>
            </thead>
            <tbody>
                @forelse($lembarKerja as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->tanggal->format('d/m/Y') }}</td>
                    <td>{{ $item->pjlp->nama }}</td>
                    <td><span class="badge">{{ $item->pjlp->unit->label() }}</span></td>
                    <td>{{ $item->details->count() }} kegiatan</td>
                    <td><span class="badge text-white bg-{{ $item->status->color() }}">{{ $item->status->label() }}</span></td>
                    <td>
                        @if($item->validasi)
                        {{ $item->validasi->validator->name ?? '-' }}
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        Tidak ada data lembar kerja untuk periode ini
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
