@extends('layouts.app')

@section('title', 'Lembar Kerja')

@section('actions')
@can('lembar-kerja.create')
<a href="{{ route('lembar-kerja.create') }}" class="btn btn-primary">
    <i class="ti ti-plus me-2"></i> Buat Lembar Kerja
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
            <div class="col-md-4">
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
            <div class="col-md-4">
                <label class="form-label">Tanggal</label>
                <input type="date" name="tanggal" class="form-control" value="{{ request('tanggal') }}">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">Filter</button>
                <a href="{{ route('lembar-kerja.index') }}" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card mt-3">
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    @canany(['lembar-kerja.view-unit', 'lembar-kerja.view-all'])
                    <th>PJLP</th>
                    <th>Unit</th>
                    @endcanany
                    <th>Jumlah Kegiatan</th>
                    <th>Status</th>
                    <th>Validasi</th>
                    <th class="w-1"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($lembarKerja as $item)
                <tr>
                    <td>{{ $item->tanggal->format('d M Y') }}</td>
                    @canany(['lembar-kerja.view-unit', 'lembar-kerja.view-all'])
                    <td>{{ $item->pjlp->nama }}</td>
                    <td><span class="badge">{{ $item->pjlp->unit->label() }}</span></td>
                    @endcanany
                    <td>{{ $item->details_count ?? $item->details->count() }} kegiatan</td>
                    <td><span class="badge bg-{{ $item->status->color() }}">{{ $item->status->label() }}</span></td>
                    <td>
                        @if($item->validasi)
                        <small class="text-muted">
                            {{ $item->validasi->validator->name ?? '-' }}<br>
                            {{ $item->validasi->validated_at?->format('d M Y H:i') }}
                        </small>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        <div class="btn-list">
                            @if($item->canBeEdited() && auth()->user()->hasRole('pjlp'))
                            <a href="{{ route('lembar-kerja.edit', $item) }}" class="btn btn-sm btn-warning">Edit</a>
                            @endif
                            <a href="{{ route('lembar-kerja.show', $item) }}" class="btn btn-sm">Detail</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        Tidak ada data lembar kerja
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($lembarKerja->hasPages())
    <div class="card-footer d-flex align-items-center">
        {{ $lembarKerja->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
