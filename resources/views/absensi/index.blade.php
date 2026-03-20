@extends('layouts.app')

@section('title', 'Data Absensi')

@section('actions')
@can('absensi.import')
<a href="{{ route('absensi.import') }}" class="btn btn-primary">
    <i class="ti ti-upload me-2"></i> Import Absensi
</a>
@endcan
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Filter</h3>
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
            @if($pjlpList->isNotEmpty())
            <div class="col-md-3">
                <label class="form-label">PJLP</label>
                <select name="pjlp_id" class="form-select">
                    <option value="">Semua PJLP</option>
                    @foreach($pjlpList as $pjlp)
                    <option value="{{ $pjlp->id }}" {{ request('pjlp_id') == $pjlp->id ? 'selected' : '' }}>
                        {{ $pjlp->nama }}
                    </option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">Filter</button>
                <a href="{{ route('absensi.index') }}" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header">
        <h3 class="card-title">Data Absensi - {{ \Carbon\Carbon::create($tahun, $bulan)->translatedFormat('F Y') }}</h3>
    </div>
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>Tanggal & Waktu</th>
                    @canany(['absensi.view-unit', 'absensi.view-all'])
                    <th>PJLP</th>
                    <th>Unit</th>
                    @endcanany
                    <th>Badge</th>
                    <th>Tipe</th>
                </tr>
            </thead>
            <tbody>
                @forelse($absensi as $item)
                <tr>
                    <td>{{ $item->check_time->translatedFormat('d M Y H:i:s') }}</td>
                    @canany(['absensi.view-unit', 'absensi.view-all'])
                    <td>{{ $item->pjlp?->nama ?? '-' }}</td>
                    <td>
                        @if($item->pjlp)
                        <span class="badge text-white" style="background-color: {{ $item->pjlp->unit->value == 'security' ? '#206bc4' : '#2fb344' }}">
                            {{ $item->pjlp->unit->label() }}
                        </span>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </td>
                    @endcanany
                    <td><code>{{ $item->badge_number }}</code></td>
                    <td>
                        @if($item->check_type == 'I' || $item->check_type == '0')
                        <span class="badge text-white" style="background-color: #2fb344;">Masuk</span>
                        @else
                        <span class="badge text-white" style="background-color: #d63939;">Pulang</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        Tidak ada data absensi untuk periode ini
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($absensi->hasPages())
    <div class="card-footer d-flex align-items-center">
        {{ $absensi->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
