@extends('layouts.app')

@section('title', 'Detail Lembar Kerja CS')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <a href="{{ route('lembar-kerja-cs.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M5 12l6 6" /><path d="M5 12l6 -6" /></svg>
                    Kembali
                </a>
                <h2 class="page-title">Detail Lembar Kerja CS</h2>
                <div class="text-muted mt-1">
                    {{ $lembarKerja->area->nama }} | {{ $lembarKerja->tanggal->format('d F Y') }} | Shift {{ ucfirst($lembarKerja->shift) }}
                </div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                @if($lembarKerja->canEdit())
                <a href="{{ route('lembar-kerja-cs.edit', $lembarKerja->id) }}" class="btn btn-warning">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" /><path d="M16 5l3 3" /></svg>
                    Edit
                </a>
                @endif
                <button class="btn btn-secondary" onclick="window.print()">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" /><path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" /><path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" /></svg>
                    Cetak
                </button>
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

        @if(session('warning'))
        <div class="alert alert-warning alert-dismissible" role="alert">
            {{ session('warning') }}
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Info Card -->
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">Informasi Lembar Kerja</h3>
                <div class="card-actions">
                    <span class="badge text-white bg-{{ $lembarKerja->status_color }} fs-5">{{ $lembarKerja->status_label }}</span>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <strong>PJLP:</strong><br>
                        {{ $lembarKerja->pjlp->nama ?? '-' }}<br>
                        <small class="text-muted">{{ $lembarKerja->pjlp->nip ?? '' }}</small>
                    </div>
                    <div class="col-md-3 mb-3">
                        <strong>Area:</strong><br>
                        <span class="badge bg-blue-lt">{{ $lembarKerja->area->kode }}</span>
                        {{ $lembarKerja->area->nama }}
                    </div>
                    <div class="col-md-3 mb-3">
                        <strong>Tanggal:</strong><br>
                        {{ $lembarKerja->tanggal->format('d F Y') }}<br>
                        <small class="text-muted">{{ $lembarKerja->tanggal->translatedFormat('l') }}</small>
                    </div>
                    <div class="col-md-3 mb-3">
                        <strong>Shift:</strong><br>
                        <span class="badge text-white bg-azure">{{ $lembarKerja->shift->nama ?? '-' }}</span>
                        @if($lembarKerja->shift)
                        <small class="d-block text-muted">{{ $lembarKerja->shift->jam_masuk }} - {{ $lembarKerja->shift->jam_keluar }}</small>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <strong>Progress:</strong><br>
                        <div class="progress mt-1" style="height: 20px;">
                            <div class="progress-bar bg-success" style="width: {{ $lembarKerja->completion_percentage }}%">
                                {{ $lembarKerja->completion_percentage }}%
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <strong>Dibuat:</strong><br>
                        {{ $lembarKerja->created_at->format('d/m/Y H:i') }}
                    </div>
                    @if($lembarKerja->submitted_at)
                    <div class="col-md-3 mb-3">
                        <strong>Disubmit:</strong><br>
                        {{ $lembarKerja->submitted_at->format('d/m/Y H:i') }}
                    </div>
                    @endif
                    @if($lembarKerja->validated_at)
                    <div class="col-md-3 mb-3">
                        <strong>Divalidasi:</strong><br>
                        {{ $lembarKerja->validated_at->format('d/m/Y H:i') }}<br>
                        <small class="text-muted">oleh {{ $lembarKerja->validator->name ?? '-' }}</small>
                    </div>
                    @endif
                </div>
                @if($lembarKerja->catatan_pjlp)
                <div class="row">
                    <div class="col-12">
                        <strong>Catatan PJLP:</strong><br>
                        {{ $lembarKerja->catatan_pjlp }}
                    </div>
                </div>
                @endif
                @if($lembarKerja->catatan_koordinator)
                <div class="row mt-2">
                    <div class="col-12">
                        <strong>Catatan Validasi:</strong><br>
                        <div class="alert alert-{{ $lembarKerja->isValidated() ? 'success' : 'warning' }} mb-0 mt-1">
                            {{ $lembarKerja->catatan_koordinator }}
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Detail Checklist -->
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">Detail Checklist Aktivitas</h3>
                <div class="card-actions">
                    {{ $lembarKerja->details->where('is_completed', true)->count() }} / {{ $lembarKerja->details->count() }} selesai
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Aktivitas</th>
                            <th>Kategori</th>
                            <th>Status</th>
                            <th>Waktu Selesai</th>
                            <th>Foto</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lembarKerja->details as $index => $detail)
                        <tr class="{{ $detail->is_completed ? 'table-success' : '' }}">
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $detail->aktivitas->nama ?? '-' }}</strong>
                                @if($detail->aktivitas->deskripsi)
                                <br><small class="text-muted">{{ $detail->aktivitas->deskripsi }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-azure-lt">{{ ucfirst($detail->aktivitas->kategori ?? 'umum') }}</span>
                            </td>
                            <td>
                                @if($detail->is_completed)
                                <span class="badge text-white bg-success">Selesai</span>
                                @else
                                <span class="badge text-white bg-secondary">Belum</span>
                                @endif
                            </td>
                            <td>
                                {{ $detail->waktu_selesai ? $detail->waktu_selesai->format('H:i') : '-' }}
                            </td>
                            <td>
                                @if($detail->foto_before || $detail->foto_after)
                                <div class="d-flex gap-1">
                                    @if($detail->foto_before)
                                    <a href="{{ Storage::url($detail->foto_before) }}" target="_blank">
                                        <img src="{{ Storage::url($detail->foto_before) }}" class="rounded" style="width: 40px; height: 40px; object-fit: cover;" alt="Before">
                                    </a>
                                    @endif
                                    @if($detail->foto_after)
                                    <a href="{{ Storage::url($detail->foto_after) }}" target="_blank">
                                        <img src="{{ Storage::url($detail->foto_after) }}" class="rounded" style="width: 40px; height: 40px; object-fit: cover;" alt="After">
                                    </a>
                                    @endif
                                </div>
                                @else
                                -
                                @endif
                            </td>
                            <td>{{ $detail->catatan ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">Tidak ada detail aktivitas</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Validation Form (for Koordinator/Admin) -->
        @if($lembarKerja->canValidate() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('koordinator')))
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Validasi Lembar Kerja</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('lembar-kerja-cs.validate', $lembarKerja->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Catatan Validasi</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Catatan untuk PJLP (opsional)..."></textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" name="action" value="validate" class="btn btn-success" onclick="return confirm('Validasi lembar kerja ini?')">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                            Validasi
                        </button>
                        <button type="submit" name="action" value="reject" class="btn btn-danger" onclick="return confirm('Tolak lembar kerja ini? PJLP harus mengisi ulang.')">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M18 6l-12 12" /><path d="M6 6l12 12" /></svg>
                            Tolak
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
