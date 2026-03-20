@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Overview</div>
                <h2 class="page-title">
                    Selamat Datang, {{ auth()->user()->name }}!
                </h2>
            </div>
            <div class="col-auto ms-auto">
                <div class="btn-list">
                    <span class="badge bg-blue-lt fs-6">
                        <i class="ti ti-building me-1"></i>
                        Unit {{ $unit ? $unit->label() : 'Semua' }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <!-- Welcome Card -->
        <div class="card bg-primary text-white mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="avatar avatar-lg bg-white-lt">
                            <i class="ti ti-calendar-event fs-1"></i>
                        </span>
                    </div>
                    <div class="col">
                        <h3 class="mb-1 text-white">{{ now()->translatedFormat('l, d F Y') }}</h3>
                        <div class="text-white-50">
                            Kelola jadwal dan pantau kinerja PJLP unit Anda dengan mudah.
                        </div>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('jadwal-kerja-cs-bulanan.index') }}" class="btn btn-light">
                            <i class="ti ti-calendar me-1"></i> Lihat Jadwal
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row row-deck row-cards mb-4">
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-primary text-white avatar">
                                    <i class="ti ti-users"></i>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">Total PJLP</div>
                                <div class="text-muted">{{ $pjlpAktif }} aktif dari {{ $totalPjlp }}</div>
                            </div>
                            <div class="col-auto">
                                <span class="h1 mb-0">{{ $totalPjlp }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-green text-white avatar">
                                    <i class="ti ti-fingerprint"></i>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">Absensi Hari Ini</div>
                                <div class="text-muted">dari {{ $pjlpAktif }} PJLP aktif</div>
                            </div>
                            <div class="col-auto">
                                <span class="h1 mb-0 text-green">{{ $absensiHariIni }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-warning text-white avatar">
                                    <i class="ti ti-calendar-off"></i>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">Cuti Menunggu</div>
                                <div class="text-muted">
                                    <a href="{{ route('cuti.index') }}?status=menunggu">Perlu persetujuan</a>
                                </div>
                            </div>
                            <div class="col-auto">
                                <span class="h1 mb-0 {{ $cutiPending > 0 ? 'text-warning' : '' }}">{{ $cutiPending }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-orange text-white avatar">
                                    <i class="ti ti-photo-check"></i>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">Bukti Pending</div>
                                <div class="text-muted">
                                    <a href="{{ route('lembar-kerja-cs.validasi-bukti-index') }}">Perlu validasi</a>
                                </div>
                            </div>
                            <div class="col-auto">
                                <span class="h1 mb-0 {{ $buktiPendingCount > 0 ? 'text-orange' : '' }}">{{ $buktiPendingCount }}</span>
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
                        <a href="{{ route('jadwal-kerja-cs-bulanan.index') }}" class="card card-link card-link-pop text-center p-3">
                            <span class="avatar avatar-lg bg-blue-lt mb-2 mx-auto">
                                <i class="ti ti-calendar fs-2"></i>
                            </span>
                            <div class="fw-medium">Input Jadwal Pekerjaan</div>
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="{{ route('jadwal-shift-cs.index') }}" class="card card-link card-link-pop text-center p-3">
                            <span class="avatar avatar-lg bg-green-lt mb-2 mx-auto">
                                <i class="ti ti-clock fs-2"></i>
                            </span>
                            <div class="fw-medium">Input Jadwal Shift</div>
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="{{ route('lembar-kerja-cs.validasi-bukti-index') }}" class="card card-link card-link-pop text-center p-3">
                            <span class="avatar avatar-lg bg-orange-lt mb-2 mx-auto">
                                <i class="ti ti-checkbox fs-2"></i>
                            </span>
                            <div class="fw-medium">Validasi Bukti</div>
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="{{ route('lembar-kerja-cs.index') }}" class="card card-link card-link-pop text-center p-3">
                            <span class="avatar avatar-lg bg-purple-lt mb-2 mx-auto">
                                <i class="ti ti-file-description fs-2"></i>
                            </span>
                            <div class="fw-medium">Lihat Lembar Kerja</div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row row-deck row-cards">
            <!-- Recent Cuti Requests -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="ti ti-calendar-off me-2 text-warning"></i>
                            Pengajuan Cuti Terbaru
                        </h3>
                        <div class="card-actions">
                            <a href="{{ route('cuti.index') }}" class="btn btn-sm btn-outline-primary">
                                Lihat Semua <i class="ti ti-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body card-body-scrollable card-body-scrollable-shadow" style="max-height: 350px;">
                        <div class="divide-y">
                            @forelse($recentCuti as $cuti)
                            <div class="row py-2">
                                <div class="col-auto">
                                    <span class="avatar bg-{{ $cuti->status->color() }}-lt">
                                        {{ strtoupper(substr($cuti->pjlp->nama, 0, 2)) }}
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="text-truncate fw-medium">
                                        {{ $cuti->pjlp->nama }}
                                    </div>
                                    <div class="text-muted small">
                                        {{ $cuti->jenisCuti->nama }} &bull;
                                        {{ $cuti->tgl_mulai->format('d/m') }} - {{ $cuti->tgl_selesai->format('d/m/Y') }}
                                    </div>
                                </div>
                                <div class="col-auto align-self-center">
                                    <span class="badge bg-{{ $cuti->status->color() }}-lt">
                                        {{ $cuti->status->label() }}
                                    </span>
                                </div>
                            </div>
                            @empty
                            <div class="empty py-4">
                                <div class="empty-icon">
                                    <i class="ti ti-mood-smile"></i>
                                </div>
                                <p class="empty-title">Tidak ada pengajuan cuti</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Bukti Pekerjaan Menunggu Validasi -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="ti ti-photo-check me-2 text-orange"></i>
                            Bukti Menunggu Validasi
                        </h3>
                        <div class="card-actions">
                            <a href="{{ route('lembar-kerja-cs.validasi-bukti-index') }}" class="btn btn-sm btn-outline-warning">
                                Validasi <i class="ti ti-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body card-body-scrollable card-body-scrollable-shadow" style="max-height: 350px;">
                        <div class="divide-y">
                            @forelse($recentBuktiPending as $bukti)
                            <div class="row py-2">
                                <div class="col-auto">
                                    <span class="avatar bg-orange-lt">
                                        <i class="ti ti-photo"></i>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="text-truncate fw-medium">
                                        {{ $bukti->pjlp->nama ?? '-' }}
                                    </div>
                                    <div class="text-muted small">
                                        {{ $bukti->jadwalBulanan->pekerjaan ?? '-' }}
                                        <br>
                                        <span class="text-primary">{{ $bukti->jadwalBulanan->area->nama ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-auto align-self-center text-end">
                                    <div class="small text-muted">
                                        {{ $bukti->dikerjakan_at ? $bukti->dikerjakan_at->diffForHumans() : '-' }}
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="empty py-4">
                                <div class="empty-icon">
                                    <i class="ti ti-mood-smile"></i>
                                </div>
                                <p class="empty-title">Semua bukti sudah divalidasi</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
