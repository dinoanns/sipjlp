@extends('layouts.app')

@section('title', 'Rekap Lembar Kerja CS per Area')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Rekap Lembar Kerja CS per Area</h2>
                <div class="text-muted mt-1">Ringkasan pengisian lembar kerja CS per area kerja</div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <!-- Filter -->
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('lembar-kerja-cs.rekap-area') }}">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Cards Summary -->
        <div class="row row-deck row-cards mb-3">
            @foreach($rekap as $row)
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">{{ $row->nama_area }}</div>
                            <div class="ms-auto">
                                <span class="badge bg-blue-lt">{{ $row->kode }}</span>
                            </div>
                        </div>
                        <div class="h1 mb-3">{{ $row->total_lembar_kerja }}</div>
                        <div class="d-flex mb-2">
                            <div>
                                <span class="badge bg-success me-1">{{ $row->validated }}</span> Validated
                            </div>
                        </div>
                        @php
                            $percentage = $row->total_lembar_kerja > 0 ? round(($row->validated / $row->total_lembar_kerja) * 100, 1) : 0;
                        @endphp
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-success" style="width: {{ $percentage }}%"></div>
                        </div>
                        <small class="text-muted">{{ $percentage }}% tervalidasi</small>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Table Detail -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Detail per Area</h3>
                <div class="card-actions">
                    <span class="text-muted">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</span>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Nama Area</th>
                            <th class="text-center">Total LK</th>
                            <th class="text-center">
                                <span class="badge bg-success">Validated</span>
                            </th>
                            <th class="text-center">
                                <span class="badge bg-warning">Submitted</span>
                            </th>
                            <th class="text-center">
                                <span class="badge bg-secondary">Draft</span>
                            </th>
                            <th class="text-center">% Validasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rekap as $index => $row)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><span class="badge bg-blue-lt">{{ $row->kode }}</span></td>
                            <td>{{ $row->nama_area }}</td>
                            <td class="text-center"><strong>{{ $row->total_lembar_kerja }}</strong></td>
                            <td class="text-center">{{ $row->validated }}</td>
                            <td class="text-center">{{ $row->submitted }}</td>
                            <td class="text-center">{{ $row->draft }}</td>
                            <td class="text-center">
                                @php
                                    $percentage = $row->total_lembar_kerja > 0 ? round(($row->validated / $row->total_lembar_kerja) * 100, 1) : 0;
                                @endphp
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-success" style="width: {{ $percentage }}%"></div>
                                </div>
                                <small>{{ $percentage }}%</small>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">Tidak ada data untuk periode ini</td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($rekap->count() > 0)
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="3">Total</th>
                            <th class="text-center">{{ $rekap->sum('total_lembar_kerja') }}</th>
                            <th class="text-center">{{ $rekap->sum('validated') }}</th>
                            <th class="text-center">{{ $rekap->sum('submitted') }}</th>
                            <th class="text-center">{{ $rekap->sum('draft') }}</th>
                            <th></th>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
