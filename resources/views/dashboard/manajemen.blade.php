@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Overview PJLP</div>
                <h2 class="page-title">
                    Selamat Datang, {{ auth()->user()->name }}!
                </h2>
            </div>
            <div class="col-auto ms-auto">
                <div class="btn-list">
                    <span class="badge bg-purple-lt fs-6">
                        <i class="ti ti-crown me-1"></i>
                        Manajemen
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <!-- Welcome Card -->
        <div class="card bg-purple text-white mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="avatar avatar-lg bg-white-lt">
                            <i class="ti ti-chart-bar fs-1"></i>
                        </span>
                    </div>
                    <div class="col">
                        <h3 class="mb-1 text-white">{{ now()->translatedFormat('l, d F Y') }}</h3>
                        <div class="text-white-50">
                            Pantau statistik dan laporan PJLP RSUD Cipayung Jakarta Timur.
                        </div>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('laporan.absensi') }}" class="btn btn-light">
                            <i class="ti ti-report me-1"></i> Lihat Laporan
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row row-deck row-cards mb-4">
            <div class="col-sm-6 col-lg-4">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-primary text-white avatar avatar-lg">
                                    <i class="ti ti-users fs-2"></i>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">Total PJLP</div>
                                <div class="text-muted">Seluruh pegawai</div>
                            </div>
                            <div class="col-auto">
                                <span class="h1 mb-0">{{ $totalPjlp }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-4">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-blue text-white avatar avatar-lg">
                                    <i class="ti ti-shield fs-2"></i>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">Security</div>
                                <div class="text-muted">PJLP Security</div>
                            </div>
                            <div class="col-auto">
                                <span class="h1 mb-0 text-blue">{{ $pjlpSecurity }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-4">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-cyan text-white avatar avatar-lg">
                                    <i class="ti ti-spray fs-2"></i>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">Cleaning Service</div>
                                <div class="text-muted">PJLP CS</div>
                            </div>
                            <div class="col-auto">
                                <span class="h1 mb-0 text-cyan">{{ $pjlpCleaning }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row row-deck row-cards mb-4">
            <!-- Rekap Absensi -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="ti ti-fingerprint me-2 text-green"></i>
                            Rekap Absensi Bulan Ini
                        </h3>
                        <div class="card-actions">
                            <a href="{{ route('laporan.absensi') }}" class="btn btn-sm btn-outline-primary">
                                Lihat Detail <i class="ti ti-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-3">
                                <div class="card card-sm bg-success-lt">
                                    <div class="card-body text-center py-4">
                                        <div class="h1 mb-1 text-success">{{ $rekapAbsensi['hari_aktif'] ?? 0 }}</div>
                                        <div class="text-muted fw-medium">Hari Aktif</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="card card-sm bg-primary-lt">
                                    <div class="card-body text-center py-4">
                                        <div class="h1 mb-1 text-primary">{{ $rekapAbsensi['total_scan'] ?? 0 }}</div>
                                        <div class="text-muted fw-medium">Total Scan</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="card card-sm bg-green-lt">
                                    <div class="card-body text-center py-4">
                                        <div class="h1 mb-1 text-green">{{ $rekapAbsensi['scan_masuk'] ?? 0 }}</div>
                                        <div class="text-muted fw-medium">Scan Masuk</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="card card-sm bg-danger-lt">
                                    <div class="card-body text-center py-4">
                                        <div class="h1 mb-1 text-danger">{{ $rekapAbsensi['scan_pulang'] ?? 0 }}</div>
                                        <div class="text-muted fw-medium">Scan Pulang</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rekap Cuti -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="ti ti-plane me-2 text-orange"></i>
                            Rekap Cuti Bulan Ini
                        </h3>
                        <div class="card-actions">
                            <a href="{{ route('laporan.cuti') }}" class="btn btn-sm btn-outline-primary">
                                Lihat Detail <i class="ti ti-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-4">
                                <div class="card card-sm bg-warning-lt">
                                    <div class="card-body text-center py-4">
                                        <div class="h1 mb-1 text-warning">{{ $rekapCuti['menunggu'] ?? 0 }}</div>
                                        <div class="text-muted fw-medium">Menunggu</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="card card-sm bg-success-lt">
                                    <div class="card-body text-center py-4">
                                        <div class="h1 mb-1 text-success">{{ $rekapCuti['disetujui'] ?? 0 }}</div>
                                        <div class="text-muted fw-medium">Disetujui</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="card card-sm bg-danger-lt">
                                    <div class="card-body text-center py-4">
                                        <div class="h1 mb-1 text-danger">{{ $rekapCuti['ditolak'] ?? 0 }}</div>
                                        <div class="text-muted fw-medium">Ditolak</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions / Laporan -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ti ti-report-analytics me-2 text-blue"></i>
                    Menu Laporan
                </h3>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6 col-md-4">
                        <a href="{{ route('laporan.absensi') }}" class="card card-link card-link-pop text-center p-4">
                            <span class="avatar avatar-xl bg-green-lt mb-3 mx-auto">
                                <i class="ti ti-fingerprint fs-1"></i>
                            </span>
                            <div class="fw-medium fs-4">Laporan Absensi</div>
                            <div class="text-muted small">Rekap kehadiran PJLP</div>
                        </a>
                    </div>
                    <div class="col-6 col-md-4">
                        <a href="{{ route('laporan.cuti') }}" class="card card-link card-link-pop text-center p-4">
                            <span class="avatar avatar-xl bg-orange-lt mb-3 mx-auto">
                                <i class="ti ti-plane-departure fs-1"></i>
                            </span>
                            <div class="fw-medium fs-4">Laporan Cuti</div>
                            <div class="text-muted small">Rekap pengajuan cuti</div>
                        </a>
                    </div>
                    <div class="col-6 col-md-4">
                        <a href="{{ route('laporan.lembar-kerja') }}" class="card card-link card-link-pop text-center p-4">
                            <span class="avatar avatar-xl bg-blue-lt mb-3 mx-auto">
                                <i class="ti ti-clipboard-list fs-1"></i>
                            </span>
                            <div class="fw-medium fs-4">Laporan Lembar Kerja</div>
                            <div class="text-muted small">Rekap hasil kerja</div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
