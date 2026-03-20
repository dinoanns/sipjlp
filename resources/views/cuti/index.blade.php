@extends('layouts.app')

@section('title', 'Pengajuan Cuti')

@section('actions')
@can('cuti.create')
<a href="{{ route('cuti.create') }}" class="btn btn-primary">
    <i class="ti ti-plus me-2"></i> Ajukan Cuti
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
            <div class="col-md-3">
                <label class="form-label">Dari Tanggal</label>
                <input type="date" name="dari" class="form-control" value="{{ request('dari') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Sampai Tanggal</label>
                <input type="date" name="sampai" class="form-control" value="{{ request('sampai') }}">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">Filter</button>
                <a href="{{ route('cuti.index') }}" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card mt-3">
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>Tanggal Pengajuan</th>
                    @canany(['cuti.view-unit', 'cuti.view-all'])
                    <th>PJLP</th>
                    <th>Unit</th>
                    @endcanany
                    <th>Jenis Cuti</th>
                    <th>Periode</th>
                    <th>Jumlah Hari</th>
                    <th>Status</th>
                    <th class="w-1"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($cuti as $item)
                <tr>
                    <td>{{ $item->tanggal_permohonan->format('d M Y H:i') }}</td>
                    @canany(['cuti.view-unit', 'cuti.view-all'])
                    <td>{{ $item->pjlp->nama }}</td>
                    <td><span class="badge">{{ $item->pjlp->unit->label() }}</span></td>
                    @endcanany
                    <td>{{ $item->jenisCuti->nama }}</td>
                    <td>{{ $item->tgl_mulai->format('d M Y') }} - {{ $item->tgl_selesai->format('d M Y') }}</td>
                    <td>{{ $item->jumlah_hari }} hari</td>
                    <td><span class="badge text-white bg-{{ $item->status->color() }}">{{ $item->status->label() }}</span></td>
                    <td>
                        <a href="{{ route('cuti.show', $item) }}" class="btn btn-sm">Detail</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">
                        Tidak ada data pengajuan cuti
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($cuti->hasPages())
    <div class="card-footer d-flex align-items-center">
        {{ $cuti->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
