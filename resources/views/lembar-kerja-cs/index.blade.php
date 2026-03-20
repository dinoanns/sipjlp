@extends('layouts.app')

@section('title', 'Lembar Kerja CS')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Lembar Kerja Cleaning Service</h2>
                <div class="text-muted mt-1">Daftar lembar kerja harian CS</div>
            </div>
            <div class="col-auto ms-auto">
                @can('lembar-kerja-cs.create')
                <a href="{{ route('lembar-kerja-cs.create') }}" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                    Buat Lembar Kerja
                </a>
                @endcan
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
        @endif

        @if(session('info'))
        <div class="alert alert-info alert-dismissible" role="alert">
            {{ session('info') }}
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
        @endif

        <!-- Filter -->
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('lembar-kerja-cs.index') }}">
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label class="form-label">Tanggal Dari</label>
                            <input type="date" name="tanggal_dari" class="form-control" value="{{ request('tanggal_dari') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Tanggal Sampai</label>
                            <input type="date" name="tanggal_sampai" class="form-control" value="{{ request('tanggal_sampai') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Area</label>
                            <select name="area_id" class="form-select">
                                <option value="">Semua Area</option>
                                @foreach($areas as $area)
                                <option value="{{ $area->id }}" {{ request('area_id') == $area->id ? 'selected' : '' }}>
                                    {{ $area->nama }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Shift</label>
                            <select name="shift_id" class="form-select">
                                <option value="">Semua Shift</option>
                                @foreach($shifts as $shift)
                                <option value="{{ $shift->id }}" {{ request('shift_id') == $shift->id ? 'selected' : '' }}>
                                    {{ $shift->nama }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Menunggu Validasi</option>
                                <option value="validated" {{ request('status') == 'validated' ? 'selected' : '' }}>Tervalidasi</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">Filter</button>
                            <a href="{{ route('lembar-kerja-cs.index') }}" class="btn btn-secondary">Reset</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table -->
        <div class="card">
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Area</th>
                            <th>Shift</th>
                            <th>PJLP</th>
                            <th>Progress</th>
                            <th>Status</th>
                            <th class="w-1">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lembarKerja as $lk)
                        <tr>
                            <td>{{ $lk->tanggal->format('d/m/Y') }}</td>
                            <td>
                                <span class="badge bg-blue-lt">{{ $lk->area->kode ?? '-' }}</span>
                                {{ $lk->area->nama ?? '-' }}
                            </td>
                            <td>
                                <span class="badge text-white bg-azure">{{ $lk->shift->nama ?? '-' }}</span>
                            </td>
                            <td>{{ $lk->pjlp->nama ?? '-' }}</td>
                            <td>
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-primary" style="width: {{ $lk->completion_percentage }}%" role="progressbar">
                                    </div>
                                </div>
                                <small class="text-muted">{{ $lk->completion_percentage }}%</small>
                            </td>
                            <td>
                                <span class="badge text-white bg-{{ $lk->status_color }}">{{ $lk->status_label }}</span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('lembar-kerja-cs.show', $lk->id) }}" class="btn btn-sm btn-outline-primary" title="Lihat">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eye" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg>
                                    </a>
                                    @if($lk->canEdit())
                                    <a href="{{ route('lembar-kerja-cs.edit', $lk->id) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-edit" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" /><path d="M16 5l3 3" /></svg>
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">Tidak ada data lembar kerja</td>
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
    </div>
</div>
@endsection
