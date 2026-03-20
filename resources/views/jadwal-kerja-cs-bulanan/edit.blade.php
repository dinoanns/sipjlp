@extends('layouts.app')

@section('title', 'Edit Jadwal Pekerjaan CS')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<style>
    .ts-wrapper.form-select { padding: 0; border: none; }
    .ts-wrapper .ts-control { border: 1px solid #d9dbde; border-radius: 4px; }
    .ts-wrapper.focus .ts-control { border-color: #206bc4; box-shadow: 0 0 0 0.25rem rgba(32, 107, 196, 0.25); }
</style>
@endpush

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    <a href="{{ route('jadwal-kerja-cs-bulanan.create', ['tanggal' => $jadwalKerjaCsBulanan->tanggal->format('Y-m-d'), 'area_id' => $jadwalKerjaCsBulanan->area_id]) }}">
                        &larr; Kembali
                    </a>
                </div>
                <h2 class="page-title">
                    Edit Jadwal Pekerjaan CS
                </h2>
                <div class="text-muted mt-1">
                    {{ $jadwalKerjaCsBulanan->area?->nama ?? '-' }} - {{ $jadwalKerjaCsBulanan->tanggal->translatedFormat('l, d F Y') }}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Edit Pekerjaan</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('jadwal-kerja-cs-bulanan.update', $jadwalKerjaCsBulanan->id) }}">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label class="form-label">Area</label>
                                <input type="text" class="form-control" value="{{ $jadwalKerjaCsBulanan->area?->nama ?? '-' }}" readonly>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Tanggal</label>
                                <input type="text" class="form-control" value="{{ $jadwalKerjaCsBulanan->tanggal->translatedFormat('l, d F Y') }}" readonly>
                            </div>

                            @php
                                $isManual = !$jadwalKerjaCsBulanan->pekerjaan_id && $jadwalKerjaCsBulanan->pekerjaan;
                            @endphp
                            <div class="mb-3">
                                <label class="form-label required">Nama Pekerjaan</label>
                                <select name="pekerjaan_id" class="form-select @error('pekerjaan_id') is-invalid @enderror @error('pekerjaan') is-invalid @enderror" id="pekerjaanSelect">
                                    <option value="">-- Pilih Pekerjaan --</option>
                                    @foreach($masterPekerjaan as $pekerjaan)
                                        <option value="{{ $pekerjaan->id }}" {{ old('pekerjaan_id', $jadwalKerjaCsBulanan->pekerjaan_id) == $pekerjaan->id ? 'selected' : '' }}>
                                            {{ $pekerjaan->nama }}
                                        </option>
                                    @endforeach
                                    <option value="lainnya" {{ old('pekerjaan_id') == 'lainnya' || $isManual ? 'selected' : '' }}>-- Lainnya (Ketik Manual) --</option>
                                </select>
                                <input type="text" name="pekerjaan" class="form-control mt-2 @error('pekerjaan') is-invalid @enderror"
                                       id="pekerjaanManual" placeholder="Ketik nama pekerjaan..."
                                       value="{{ old('pekerjaan', $isManual ? $jadwalKerjaCsBulanan->pekerjaan : '') }}"
                                       style="display: {{ old('pekerjaan_id') == 'lainnya' || $isManual ? 'block' : 'none' }};">
                                @error('pekerjaan_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @error('pekerjaan')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label required">Shift</label>
                                <select name="shift_id" class="form-select @error('shift_id') is-invalid @enderror" required>
                                    @foreach($shifts as $shift)
                                        <option value="{{ $shift->id }}" {{ old('shift_id', $jadwalKerjaCsBulanan->shift_id) == $shift->id ? 'selected' : '' }}>
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
                                <select name="pjlp_id" class="form-select @error('pjlp_id') is-invalid @enderror">
                                    <option value="">-- Semua Pegawai (Opsional) --</option>
                                    @foreach($pjlps as $pjlp)
                                        <option value="{{ $pjlp->id }}" {{ old('pjlp_id', $jadwalKerjaCsBulanan->pjlp_id) == $pjlp->id ? 'selected' : '' }}>
                                            {{ $pjlp->nama }}
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
                                <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan', $jadwalKerjaCsBulanan->keterangan) }}</textarea>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2" /><circle cx="12" cy="14" r="2" /><polyline points="14 4 14 8 8 8" /></svg>
                                    Simpan Perubahan
                                </button>
                                <a href="{{ route('jadwal-kerja-cs-bulanan.create', ['tanggal' => $jadwalKerjaCsBulanan->tanggal->format('Y-m-d'), 'area_id' => $jadwalKerjaCsBulanan->area_id]) }}"
                                   class="btn btn-secondary">
                                    Batal
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const pekerjaanManual = document.getElementById('pekerjaanManual');

    // Initialize Tom Select
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
});
</script>
@endpush
