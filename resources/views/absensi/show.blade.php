@extends('layouts.app')

@section('title', 'Detail Absensi')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Detail Absensi</h3>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4">Tanggal</dt>
                    <dd class="col-sm-8">{{ $absensi->tanggal->format('d F Y') }}</dd>

                    <dt class="col-sm-4">Nama PJLP</dt>
                    <dd class="col-sm-8">{{ $absensi->pjlp->nama }}</dd>

                    <dt class="col-sm-4">Unit</dt>
                    <dd class="col-sm-8"><span class="badge">{{ $absensi->pjlp->unit->label() }}</span></dd>

                    <dt class="col-sm-4">Shift</dt>
                    <dd class="col-sm-8">{{ $absensi->shift?->nama ?? '-' }}</dd>

                    <dt class="col-sm-4">Jam Masuk</dt>
                    <dd class="col-sm-8">{{ $absensi->jam_masuk ?? '-' }}</dd>

                    <dt class="col-sm-4">Jam Keluar</dt>
                    <dd class="col-sm-8">{{ $absensi->jam_keluar ?? '-' }}</dd>

                    <dt class="col-sm-4">Status</dt>
                    <dd class="col-sm-8">
                        @php
                            $statusColors = [
                                'hadir' => 'success',
                                'terlambat' => 'warning',
                                'alpha' => 'danger',
                                'izin' => 'info',
                                'cuti' => 'secondary',
                                'libur' => 'dark',
                            ];
                        @endphp
                        <span class="badge bg-{{ $statusColors[$absensi->status->value] ?? 'secondary' }}">
                            {{ $absensi->status->label() }}
                        </span>
                    </dd>

                    <dt class="col-sm-4">Keterlambatan</dt>
                    <dd class="col-sm-8">
                        @if($absensi->menit_terlambat > 0)
                        <span class="text-warning">{{ $absensi->menit_terlambat }} menit</span>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </dd>

                    @if($absensi->keterangan)
                    <dt class="col-sm-4">Keterangan</dt>
                    <dd class="col-sm-8">{{ $absensi->keterangan }}</dd>
                    @endif
                </dl>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar avatar-lg bg-primary-lt me-3">
                        <i class="ti ti-calendar-event"></i>
                    </div>
                    <div>
                        <h3 class="mb-0">{{ $absensi->tanggal->translatedFormat('l') }}</h3>
                        <div class="text-muted">{{ $absensi->tanggal->format('d F Y') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-3">
            <a href="{{ route('absensi.index') }}" class="btn btn-secondary w-100">
                <i class="ti ti-arrow-left me-2"></i> Kembali
            </a>
        </div>
    </div>
</div>
@endsection
