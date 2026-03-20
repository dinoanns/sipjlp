@extends('layouts.app')

@section('title', 'Buat Lembar Kerja CS')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <a href="{{ route('lembar-kerja-cs.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M5 12l6 6" /><path d="M5 12l6 -6" /></svg>
                    Kembali
                </a>
                <h2 class="page-title">Buat Lembar Kerja CS</h2>
                <div class="text-muted mt-1">Pilih tanggal, area, dan shift untuk membuat lembar kerja baru</div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="row justify-content-center">
            <div class="col-lg-6">
                <form action="{{ route('lembar-kerja-cs.store') }}" method="POST">
                    @csrf
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Informasi Lembar Kerja</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label required">Tanggal</label>
                                <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', $selectedDate) }}" required>
                                <small class="form-hint">Pilih tanggal untuk lembar kerja</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label required">Area</label>
                                <select name="area_id" class="form-select" required>
                                    <option value="">-- Pilih Area --</option>
                                    @foreach($areas as $area)
                                    <option value="{{ $area->id }}" {{ old('area_id', $selectedArea) == $area->id ? 'selected' : '' }}>
                                        {{ $area->kode }} - {{ $area->nama }}
                                    </option>
                                    @endforeach
                                </select>
                                @if($areas->isEmpty())
                                <small class="form-hint text-danger">Tidak ada area yang ditugaskan kepada Anda. Hubungi Koordinator.</small>
                                @else
                                <small class="form-hint">Pilih area kerja Anda</small>
                                @endif
                            </div>

                            <div class="mb-3">
                                <label class="form-label required">Shift</label>
                                <select name="shift_id" class="form-select" required>
                                    <option value="">-- Pilih Shift --</option>
                                    @foreach($shifts as $shift)
                                    <option value="{{ $shift->id }}" {{ old('shift_id', $selectedShift) == $shift->id ? 'selected' : '' }}>
                                        {{ $shift->nama }} ({{ $shift->jam_masuk }} - {{ $shift->jam_keluar }})
                                    </option>
                                    @endforeach
                                </select>
                                <small class="form-hint">Pilih shift kerja Anda</small>
                            </div>

                            @if($pjlp)
                            <div class="mb-3">
                                <label class="form-label">PJLP</label>
                                <input type="text" class="form-control" value="{{ $pjlp->nama }} ({{ $pjlp->nip }})" readonly>
                            </div>
                            @endif
                        </div>
                        <div class="card-footer text-end">
                            <a href="{{ route('lembar-kerja-cs.index') }}" class="btn btn-link">Batal</a>
                            <button type="submit" class="btn btn-primary" {{ $areas->isEmpty() ? 'disabled' : '' }}>
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                                Buat Lembar Kerja
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
