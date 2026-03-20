@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Selamat Datang</div>
                <h2 class="page-title">
                    {{ auth()->user()->name }}
                </h2>
            </div>
            <div class="col-auto ms-auto">
                <div class="btn-list">
                    @if($pjlp)
                    <span class="badge bg-green-lt fs-6">
                        <i class="ti ti-id-badge me-1"></i>
                        {{ $pjlp->unit->label() }}
                    </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        @if(!$pjlp)
        <div class="alert alert-warning">
            <div class="d-flex">
                <div>
                    <i class="ti ti-alert-triangle icon alert-icon"></i>
                </div>
                <div>
                    <h4 class="alert-title">Profil PJLP Belum Terdaftar</h4>
                    <div class="text-muted">Akun Anda belum terhubung dengan data PJLP. Silakan hubungi Administrator.</div>
                </div>
            </div>
        </div>
        @else
        <!-- Welcome Card with Shift Info -->
        <div class="card bg-primary text-white mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        @if($pjlp->foto)
                            <span class="avatar avatar-xl" style="background-image: url({{ asset('storage/pjlp/' . $pjlp->foto) }})"></span>
                        @else
                            <span class="avatar avatar-xl bg-white-lt">
                                <i class="ti ti-user fs-1"></i>
                            </span>
                        @endif
                    </div>
                    <div class="col">
                        <h3 class="mb-1 text-white">{{ now()->translatedFormat('l, d F Y') }}</h3>
                        <div class="text-white-50">
                            NIP: {{ $pjlp->nip }} &bull; {{ $pjlp->unit->label() }}
                        </div>
                    </div>
                    <div class="col-auto text-end">
                        <div class="text-white-50 small mb-1">Shift Hari Ini</div>
                        @if($jadwalShiftHariIni)
                            @if($jadwalShiftHariIni->status === 'normal')
                                <span class="badge bg-white text-dark fs-5 py-2 px-3">
                                    <i class="ti ti-clock me-1"></i>
                                    {{ $jadwalShiftHariIni->shift->nama ?? '-' }}
                                </span>
                            @else
                                <span class="badge bg-{{ $jadwalShiftHariIni->status_color }} fs-5 py-2 px-3">
                                    {{ $jadwalShiftHariIni->status_label }}
                                </span>
                            @endif
                        @else
                            <span class="badge bg-secondary fs-5 py-2 px-3">Belum Ada Jadwal</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row row-deck row-cards mb-4">
            <div class="col-6 col-lg-4">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-green text-white avatar">
                                    <i class="ti ti-fingerprint"></i>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">Hari Masuk</div>
                                <div class="text-muted small">Bulan ini</div>
                            </div>
                            <div class="col-auto">
                                <span class="h2 mb-0 text-green">{{ $rekapAbsensiBulanIni['hari_masuk'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-4">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-blue text-white avatar">
                                    <i class="ti ti-scan"></i>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">Total Scan</div>
                                <div class="text-muted small">Bulan ini</div>
                            </div>
                            <div class="col-auto">
                                <span class="h2 mb-0 text-blue">{{ $rekapAbsensiBulanIni['total_scan'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-4">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-orange text-white avatar">
                                    <i class="ti ti-hourglass"></i>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">Cuti Pending</div>
                                <div class="text-muted small">Menunggu</div>
                            </div>
                            <div class="col-auto">
                                <span class="h2 mb-0 text-orange">{{ $cutiPending->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ti ti-bolt me-2 text-yellow"></i>
                    Aksi Cepat
                </h3>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <a href="{{ route('lembar-kerja-cs.index') }}" class="card card-link card-link-pop text-center p-3">
                            <span class="avatar avatar-lg bg-green-lt mb-2 mx-auto">
                                <i class="ti ti-clipboard-list fs-2"></i>
                            </span>
                            <div class="fw-medium">Lembar Kerja</div>
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="{{ route('cuti.create') }}" class="card card-link card-link-pop text-center p-3">
                            <span class="avatar avatar-lg bg-primary-lt mb-2 mx-auto">
                                <i class="ti ti-plane-departure fs-2"></i>
                            </span>
                            <div class="fw-medium">Ajukan Cuti</div>
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="{{ route('absensi.index') }}" class="card card-link card-link-pop text-center p-3">
                            <span class="avatar avatar-lg bg-blue-lt mb-2 mx-auto">
                                <i class="ti ti-fingerprint fs-2"></i>
                            </span>
                            <div class="fw-medium">Lihat Absensi</div>
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="{{ route('cuti.index') }}" class="card card-link card-link-pop text-center p-3">
                            <span class="avatar avatar-lg bg-purple-lt mb-2 mx-auto">
                                <i class="ti ti-list fs-2"></i>
                            </span>
                            <div class="fw-medium">Riwayat Cuti</div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row row-deck row-cards">
            <!-- Sisa Cuti Tahun Ini -->
            @if(count($sisaCuti) > 0)
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="ti ti-calendar-stats me-2 text-primary"></i>
                            Sisa Cuti Tahun {{ date('Y') }}
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @foreach($sisaCuti as $cuti)
                            <div class="col-12">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <span class="avatar bg-{{ $cuti['sisa'] > 0 ? 'success' : 'danger' }}-lt">
                                            <i class="ti ti-calendar-event"></i>
                                        </span>
                                    </div>
                                    <div class="col">
                                        <div class="fw-medium">{{ $cuti['jenis'] }}</div>
                                        <div class="text-muted small">
                                            Terpakai: {{ $cuti['terpakai'] }} dari {{ $cuti['max_hari'] }} hari
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <span class="badge bg-{{ $cuti['sisa'] > 0 ? 'success' : 'danger' }} fs-5">
                                            {{ $cuti['sisa'] }}
                                        </span>
                                    </div>
                                </div>
                                @if(!$loop->last)
                                <hr class="my-2">
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Pengajuan Cuti Menunggu -->
            <div class="col-lg-{{ count($sisaCuti) > 0 ? '6' : '12' }}">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="ti ti-hourglass me-2 text-warning"></i>
                            Pengajuan Cuti Menunggu
                        </h3>
                        <div class="card-actions">
                            <a href="{{ route('cuti.index') }}" class="btn btn-sm btn-outline-primary">
                                Lihat Semua <i class="ti ti-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body card-body-scrollable card-body-scrollable-shadow" style="max-height: 300px;">
                        @if($cutiPending->count() > 0)
                        <div class="divide-y">
                            @foreach($cutiPending as $cuti)
                            <div class="row py-2">
                                <div class="col-auto">
                                    <span class="avatar bg-warning-lt">
                                        <i class="ti ti-plane"></i>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="text-truncate fw-medium">
                                        {{ $cuti->jenisCuti->nama }}
                                    </div>
                                    <div class="text-muted small">
                                        {{ $cuti->tgl_mulai->format('d/m') }} - {{ $cuti->tgl_selesai->format('d/m/Y') }}
                                        ({{ $cuti->jumlah_hari }} hari)
                                    </div>
                                </div>
                                <div class="col-auto align-self-center">
                                    <span class="badge bg-{{ $cuti->status->color() }}-lt">
                                        {{ $cuti->status->label() }}
                                    </span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="empty py-4">
                            <div class="empty-icon">
                                <i class="ti ti-mood-smile"></i>
                            </div>
                            <p class="empty-title">Tidak ada cuti menunggu</p>
                            <p class="empty-subtitle text-muted">
                                Semua pengajuan cuti sudah diproses.
                            </p>
                            <div class="empty-action">
                                <a href="{{ route('cuti.create') }}" class="btn btn-primary">
                                    <i class="ti ti-plus me-1"></i> Ajukan Cuti Baru
                                </a>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
