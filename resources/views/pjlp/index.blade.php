@extends('layouts.app')

@section('title', 'Data PJLP')

@section('actions')
@can('pjlp.create')
<a href="{{ route('pjlp.create') }}" class="btn btn-primary">
    <i class="ti ti-plus me-2"></i> Tambah PJLP
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
                <label class="form-label">Unit</label>
                <select name="unit" class="form-select">
                    <option value="">Semua Unit</option>
                    <option value="security" {{ request('unit') == 'security' ? 'selected' : '' }}>Security</option>
                    <option value="cleaning" {{ request('unit') == 'cleaning' ? 'selected' : '' }}>Cleaning Service</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    @foreach(\App\Enums\StatusPjlp::cases() as $status)
                    <option value="{{ $status->value }}" {{ request('status') == $status->value ? 'selected' : '' }}>
                        {{ $status->label() }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Cari</label>
                <input type="text" name="search" class="form-control" placeholder="Nama atau NIP..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">Filter</button>
                <a href="{{ route('pjlp.index') }}" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card mt-3">
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>NIP</th>
                    <th>Nama</th>
                    <th>Unit</th>
                    <th>Jabatan</th>
                    <th>Status</th>
                    <th>Tanggal Bergabung</th>
                    <th class="w-1"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($pjlp as $item)
                <tr>
                    <td>{{ $item->nip }}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            @if($item->foto)
                            <span class="avatar avatar-sm me-2" style="background-image: url({{ $item->foto_url }})"></span>
                            @else
                            <span class="avatar avatar-sm me-2" style="background-image: url(https://ui-avatars.com/api/?name={{ urlencode($item->nama) }}&background=206bc4&color=fff)"></span>
                            @endif
                            {{ $item->nama }}
                        </div>
                    </td>
                    <td><span class="badge">{{ $item->unit->label() }}</span></td>
                    <td>{{ $item->jabatan }}</td>
                    <td><span class="badge text-white bg-{{ $item->status->color() }}">{{ $item->status->label() }}</span></td>
                    <td>{{ $item->tanggal_bergabung->format('d M Y') }}</td>
                    <td>
                        <div class="btn-list flex-nowrap">
                            <a href="{{ route('pjlp.show', $item) }}" class="btn btn-sm">Detail</a>
                            @can('pjlp.edit')
                            <a href="{{ route('pjlp.edit', $item) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        Tidak ada data PJLP
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($pjlp->hasPages())
    <div class="card-footer d-flex align-items-center">
        {{ $pjlp->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
