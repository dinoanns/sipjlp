@extends('layouts.app')

@section('title', 'Input Bukti Pekerjaan CS')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Input Bukti Pekerjaan CS
                </h2>
                <div class="text-muted mt-1">
                    Upload bukti foto untuk pekerjaan yang sudah dikerjakan
                </div>
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

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible" role="alert">
                {{ session('error') }}
                <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
        @endif

        <!-- Filter -->
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('lembar-kerja-cs.input-bukti') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" value="{{ $tanggal }}" onchange="this.form.submit()">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Area</label>
                        <select name="area_id" class="form-select" onchange="this.form.submit()">
                            <option value="">-- Semua Area --</option>
                            @foreach($areas as $area)
                                <option value="{{ $area->id }}" {{ $areaId == $area->id ? 'selected' : '' }}>
                                    {{ $area->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Shift</label>
                        <select name="shift_id" class="form-select" onchange="this.form.submit()">
                            <option value="">-- Semua Shift --</option>
                            @foreach($shifts as $shift)
                                <option value="{{ $shift->id }}" {{ $shiftId == $shift->id ? 'selected' : '' }}>
                                    {{ $shift->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="10" cy="10" r="7" /><line x1="21" y1="21" x2="15" y2="15" /></svg>
                            Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Keterangan Warna Shift -->
        <div class="card mb-3">
            <div class="card-body py-2">
                <div class="d-flex flex-wrap gap-3 align-items-center">
                    <span class="fw-bold me-2">Keterangan Warna Shift:</span>
                    <span class="badge" style="background-color: #cce5ff; color: #004085;">Shift Pagi</span>
                    <span class="badge" style="background-color: #fff3cd; color: #856404;">Shift Siang</span>
                    <span class="badge" style="background-color: #f8c8dc; color: #721c24;">Shift Malam</span>
                </div>
            </div>
        </div>

        <!-- Waktu Shift Aktif -->
        @if($activeShift)
        <div class="alert alert-info">
            <div class="d-flex align-items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><polyline points="12 7 12 12 15 15" /></svg>
                <div>
                    <strong>Shift Aktif:</strong> {{ $activeShift->nama }} ({{ $activeShift->jam_masuk }} - {{ $activeShift->jam_keluar }})
                    <br><small class="text-muted">Anda hanya bisa mengupload bukti untuk pekerjaan shift yang sedang berlangsung.</small>
                </div>
            </div>
        </div>
        @else
        <div class="alert alert-warning">
            <div class="d-flex align-items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v2m0 4v.01" /><path d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.84 2.75" /></svg>
                <div>
                    <strong>Tidak ada shift aktif saat ini.</strong>
                    <br><small>Anda hanya bisa melihat jadwal, tidak bisa mengupload bukti di luar jam shift.</small>
                </div>
            </div>
        </div>
        @endif

        <!-- Daftar Pekerjaan -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    Jadwal Pekerjaan - {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}
                </h3>
                <div class="card-actions">
                    <span class="badge text-white bg-primary">{{ $jadwals->count() }} pekerjaan</span>
                </div>
            </div>
            <div class="card-body p-0">
                @if($jadwals->isEmpty())
                    <div class="empty">
                        <div class="empty-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><line x1="9" y1="10" x2="9.01" y2="10" /><line x1="15" y1="10" x2="15.01" y2="10" /><path d="M9.5 15.25a3.5 3.5 0 0 1 5 0" /></svg>
                        </div>
                        <p class="empty-title">Tidak ada jadwal pekerjaan</p>
                        <p class="empty-subtitle text-muted">
                            Belum ada jadwal pekerjaan untuk tanggal dan filter yang dipilih.
                        </p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th>Area</th>
                                    <th>Pekerjaan</th>
                                    <th>Shift</th>
                                    <th>Tipe</th>
                                    <th>Status</th>
                                    <th class="text-center" style="width: 150px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($jadwals as $index => $jadwal)
                                    @php
                                        $bukti = $jadwal->buktiPekerjaan ?? null;
                                        $isCompleted = $bukti && $bukti->is_completed;
                                        $canInput = $activeShift && $jadwal->shift_id == $activeShift->id && \Carbon\Carbon::parse($tanggal)->isToday();
                                    @endphp
                                    <tr style="background-color: {{ $jadwal->shift_color }};">
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>{{ $jadwal->area?->nama ?? '-' }}</td>
                                        <td>
                                            <div class="fw-bold">{{ $jadwal->pekerjaan }}</div>
                                            @if($jadwal->keterangan)
                                                <small class="text-muted">{{ $jadwal->keterangan }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge" style="background-color: {{ $jadwal->shift_color }}; color: #333;">
                                                {{ $jadwal->shift?->nama ?? '-' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge text-white bg-{{ $jadwal->tipe_color }}">
                                                {{ $jadwal->tipe_label }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($isCompleted)
                                                <span class="badge text-white bg-success">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                                                    Sudah Upload
                                                </span>
                                                <br><small class="text-muted">{{ $bukti->dikerjakan_at?->format('H:i') }}</small>
                                            @else
                                                <span class="badge text-white bg-secondary">Belum Upload</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($isCompleted)
                                                <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#viewBuktiModal{{ $jadwal->id }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="2" /><path d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7" /></svg>
                                                    Lihat
                                                </button>
                                            @elseif($canInput)
                                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadBuktiModal{{ $jadwal->id }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" /><polyline points="7 9 12 4 17 9" /><line x1="12" y1="4" x2="12" y2="16" /></svg>
                                                    Upload
                                                </button>
                                            @else
                                                <span class="text-muted" title="Hanya bisa upload saat shift berlangsung">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-muted" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="5" y="11" width="14" height="10" rx="2" /><circle cx="12" cy="16" r="1" /><path d="M8 11v-4a4 4 0 0 1 8 0v4" /></svg>
                                                </span>
                                            @endif
                                        </td>
                                    </tr>

                                    <!-- Modal Upload Bukti -->
                                    @if($canInput && !$isCompleted)
                                    <div class="modal modal-blur fade" id="uploadBuktiModal{{ $jadwal->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <form action="{{ route('lembar-kerja-cs.upload-bukti', $jadwal->id) }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Upload Bukti Pekerjaan</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label">Pekerjaan</label>
                                                            <input type="text" class="form-control" value="{{ $jadwal->pekerjaan }}" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label required">Foto Bukti</label>
                                                            <input type="file" name="foto_bukti" class="form-control" accept="image/*" required>
                                                            <small class="text-muted">Format: JPG, PNG, JPEG. Max: 5MB</small>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Catatan (Opsional)</label>
                                                            <textarea name="catatan" class="form-control" rows="2" placeholder="Catatan tambahan..."></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-primary">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" /><polyline points="7 9 12 4 17 9" /><line x1="12" y1="4" x2="12" y2="16" /></svg>
                                                            Upload Bukti
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    <!-- Modal View Bukti -->
                                    @if($isCompleted)
                                    <div class="modal modal-blur fade" id="viewBuktiModal{{ $jadwal->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Bukti Pekerjaan: {{ $jadwal->pekerjaan }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-8">
                                                            <img src="{{ asset('storage/' . $bukti->foto_bukti) }}" class="img-fluid rounded" alt="Bukti">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <dl>
                                                                <dt>Waktu Upload</dt>
                                                                <dd>{{ $bukti->dikerjakan_at?->translatedFormat('d F Y H:i:s') }}</dd>

                                                                <dt>Diupload Oleh</dt>
                                                                <dd>{{ $bukti->pjlp?->nama ?? '-' }}</dd>

                                                                @if($bukti->catatan)
                                                                <dt>Catatan</dt>
                                                                <dd>{{ $bukti->catatan }}</dd>
                                                                @endif

                                                                <dt>Status Validasi</dt>
                                                                <dd>
                                                                    @if($bukti->is_validated)
                                                                        <span class="badge text-white bg-success">Tervalidasi</span>
                                                                    @else
                                                                        <span class="badge text-white bg-warning">Belum Divalidasi</span>
                                                                    @endif
                                                                </dd>
                                                            </dl>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
