@extends('layouts.app')

@section('title', 'Input Jadwal Pekerjaan CS')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    <a href="{{ route('jadwal-kerja-cs-bulanan.index', ['area_id' => $areaId, 'bulan' => \Carbon\Carbon::parse($tanggal)->month, 'tahun' => \Carbon\Carbon::parse($tanggal)->year]) }}">
                        &larr; Kembali ke Kalender
                    </a>
                </div>
                <h2 class="page-title">
                    Input Jadwal Pekerjaan CS
                </h2>
                <div class="text-muted mt-1">
                    {{ $selectedArea?->nama ?? 'Pilih Area' }} - {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}
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

        <div class="row">
            <!-- Form Input Pekerjaan Baru -->
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Tambah Pekerjaan</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('jadwal-kerja-cs-bulanan.store') }}">
                            @csrf
                            <input type="hidden" name="tanggal" value="{{ $tanggal }}">

                            <div class="mb-3">
                                <label class="form-label required">Area</label>
                                <select name="area_id" class="form-select @error('area_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Area --</option>
                                    @foreach($areas as $area)
                                        <option value="{{ $area->id }}" {{ old('area_id', $areaId) == $area->id ? 'selected' : '' }}>
                                            {{ $area->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('area_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label required">Nama Pekerjaan</label>
                                <select name="pekerjaan_id" class="form-select @error('pekerjaan_id') is-invalid @enderror @error('pekerjaan') is-invalid @enderror" id="pekerjaanSelect">
                                    <option value="">-- Pilih Pekerjaan --</option>
                                    @foreach($masterPekerjaan as $pekerjaan)
                                        <option value="{{ $pekerjaan->id }}" {{ old('pekerjaan_id') == $pekerjaan->id ? 'selected' : '' }}>
                                            {{ $pekerjaan->nama }}
                                        </option>
                                    @endforeach
                                    <option value="lainnya" {{ old('pekerjaan_id') == 'lainnya' ? 'selected' : '' }}>-- Lainnya (Ketik Manual) --</option>
                                </select>
                                <input type="text" name="pekerjaan" class="form-control mt-2 @error('pekerjaan') is-invalid @enderror"
                                       id="pekerjaanManual" placeholder="Ketik nama pekerjaan..."
                                       value="{{ old('pekerjaan') }}" style="display: {{ old('pekerjaan_id') == 'lainnya' ? 'block' : 'none' }};">
                                @error('pekerjaan_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @error('pekerjaan')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label required">Shift</label>
                                <select name="shift_id" class="form-select @error('shift_id') is-invalid @enderror" required id="shiftSelect">
                                    <option value="">-- Pilih Shift --</option>
                                    @foreach($shifts as $shift)
                                        <option value="{{ $shift->id }}" {{ old('shift_id') == $shift->id ? 'selected' : '' }}>
                                            {{ $shift->nama }} ({{ \Carbon\Carbon::parse($shift->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($shift->jam_selesai)->format('H:i') }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('shift_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Pegawai yang Ditugaskan</label>
                                <select name="pjlp_id" class="form-select @error('pjlp_id') is-invalid @enderror" id="pjlpSelect">
                                    <option value="">-- Semua Pegawai (Opsional) --</option>
                                    @foreach($pjlps as $pjlp)
                                        @php
                                            $jadwalShift = $jadwalShifts[$pjlp->id] ?? null;
                                            $shiftInfo = $jadwalShift ? ' - Shift ' . ($jadwalShift->shift?->nama ?? '-') : '';
                                        @endphp
                                        <option value="{{ $pjlp->id }}"
                                                data-shift-id="{{ $jadwalShift?->shift_id ?? '' }}"
                                                {{ old('pjlp_id') == $pjlp->id ? 'selected' : '' }}>
                                            {{ $pjlp->nama }}{{ $shiftInfo }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('pjlp_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Kosongkan jika pekerjaan ini untuk semua pegawai di shift tersebut</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Keterangan (Opsional)</label>
                                <textarea name="keterangan" class="form-control" rows="2" placeholder="Keterangan tambahan...">{{ old('keterangan') }}</textarea>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="12" y1="5" x2="12" y2="19" /><line x1="5" y1="12" x2="19" y2="12" /></svg>
                                    Tambah Pekerjaan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Copy dari tanggal lain -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Salin dari Tanggal Lain</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('jadwal-kerja-cs-bulanan.copy') }}">
                            @csrf
                            <input type="hidden" name="area_id" value="{{ $areaId }}">
                            <input type="hidden" name="tanggal_tujuan" value="{{ $tanggal }}">

                            <div class="mb-3">
                                <label class="form-label">Tanggal Sumber</label>
                                <input type="date" name="tanggal_sumber" class="form-control" required>
                                <small class="text-muted">Pilih tanggal yang jadwalnya ingin disalin ke {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}</small>
                            </div>

                            <button type="submit" class="btn btn-secondary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="8" y="8" width="12" height="12" rx="2" /><path d="M16 8v-2a2 2 0 0 0 -2 -2h-8a2 2 0 0 0 -2 2v8a2 2 0 0 0 2 2h2" /></svg>
                                Salin Jadwal
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Daftar Pekerjaan Existing -->
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            Daftar Pekerjaan - {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}
                        </h3>
                        <div class="card-actions">
                            <span class="badge bg-primary">{{ $existingJadwals->count() }} pekerjaan</span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if($existingJadwals->isEmpty())
                            <div class="empty">
                                <div class="empty-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><line x1="9" y1="10" x2="9.01" y2="10" /><line x1="15" y1="10" x2="15.01" y2="10" /><path d="M9.5 15.25a3.5 3.5 0 0 1 5 0" /></svg>
                                </div>
                                <p class="empty-title">Belum ada pekerjaan</p>
                                <p class="empty-subtitle text-muted">
                                    Tambahkan pekerjaan menggunakan form di samping.
                                </p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-vcenter card-table">
                                    <thead>
                                        <tr>
                                            <th>Pekerjaan</th>
                                            <th>Pegawai</th>
                                            <th>Shift</th>
                                            <th class="w-1">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($existingJadwals as $jadwal)
                                            <tr style="background-color: {{ $jadwal->shift_color }};">
                                                <td>
                                                    <div class="fw-bold">{{ $jadwal->nama_pekerjaan }}</div>
                                                    @if($jadwal->keterangan)
                                                        <small class="text-muted">{{ $jadwal->keterangan }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($jadwal->pjlp)
                                                        <span class="fw-medium">{{ $jadwal->pjlp->nama }}</span>
                                                    @else
                                                        <span class="text-muted">Semua Pegawai</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge" style="background-color: {{ $jadwal->shift_color }}; color: #333;">
                                                        {{ $jadwal->shift?->nama ?? '-' }} ({{ $jadwal->shift ? \Carbon\Carbon::parse($jadwal->shift->jam_mulai)->format('H:i') . ' - ' . \Carbon\Carbon::parse($jadwal->shift->jam_selesai)->format('H:i') : '' }})
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-list flex-nowrap">
                                                        <a href="{{ route('jadwal-kerja-cs-bulanan.edit', $jadwal->id) }}" class="btn btn-sm btn-icon btn-warning" title="Edit">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" /><path d="M16 5l3 3" /></svg>
                                                        </a>
                                                        <form action="{{ route('jadwal-kerja-cs-bulanan.destroy', $jadwal->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus pekerjaan ini?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-icon btn-danger" title="Hapus">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="4" y1="7" x2="20" y2="7" /><line x1="10" y1="11" x2="10" y2="17" /><line x1="14" y1="11" x2="14" y2="17" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Keterangan Warna -->
                <div class="card mt-3">
                    <div class="card-body py-2">
                        <div class="d-flex flex-wrap gap-3 align-items-center">
                            <span class="fw-bold me-2">Keterangan Warna Shift:</span>
                            <span class="badge" style="background-color: #cce5ff; color: #004085;">Shift Pagi</span>
                            <span class="badge" style="background-color: #fff3cd; color: #856404;">Shift Siang</span>
                            <span class="badge" style="background-color: #f8c8dc; color: #721c24;">Shift Malam</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<style>
    .ts-wrapper.form-select { padding: 0; border: none; }
    .ts-wrapper .ts-control { border: 1px solid #dadfe5; border-radius: 4px; min-height: 38px; }
    .ts-wrapper.focus .ts-control { border-color: #206bc4; box-shadow: 0 0 0 0.25rem rgba(32, 107, 196, 0.25); }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const pekerjaanSelect = document.getElementById('pekerjaanSelect');
    const pekerjaanManual = document.getElementById('pekerjaanManual');

    if (pekerjaanSelect) {
        // Initialize Tom Select for searchable dropdown
        const tomSelect = new TomSelect('#pekerjaanSelect', {
            create: false,
            sortField: { field: "text", direction: "asc" },
            placeholder: '-- Pilih atau Ketik Pekerjaan --',
            allowEmptyOption: true,
            onChange: function(value) {
                if (value === 'lainnya') {
                    pekerjaanManual.style.display = 'block';
                    pekerjaanManual.required = true;
                    pekerjaanManual.focus();
                } else {
                    pekerjaanManual.style.display = 'none';
                    pekerjaanManual.required = false;
                    pekerjaanManual.value = '';
                }
            }
        });

        // Set initial state if "lainnya" is selected (for old input)
        if (pekerjaanSelect.value === 'lainnya') {
            pekerjaanManual.style.display = 'block';
            pekerjaanManual.required = true;
        }
    }
});
</script>
@endpush
