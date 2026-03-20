@extends('layouts.app')

@section('title', 'Edit Area CS')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <a href="{{ route('master.area-cs.index') }}" class="text-muted text-decoration-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="5" y1="12" x2="19" y2="12" /><line x1="5" y1="12" x2="11" y2="18" /><line x1="5" y1="12" x2="11" y2="6" /></svg>
                    Kembali
                </a>
                <h2 class="page-title mt-2">
                    Edit Area: {{ $area->nama }}
                </h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards">
            <div class="col-lg-8">
                <form action="{{ route('master.area-cs.update', $area) }}" method="POST" class="card">
                    @csrf
                    @method('PUT')
                    <div class="card-header">
                        <h3 class="card-title">Data Area</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label required">Kode Area</label>
                                <input type="text" name="kode" class="form-control @error('kode') is-invalid @enderror"
                                    value="{{ old('kode', $area->kode) }}" placeholder="Contoh: LOBBY" required>
                                @error('kode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">Kode unik untuk area (huruf kapital)</small>
                            </div>
                            <div class="col-md-8 mb-3">
                                <label class="form-label required">Nama Area</label>
                                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                                    value="{{ old('nama', $area->nama) }}" placeholder="Contoh: Lobby Utama" required>
                                @error('nama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror"
                                rows="3" placeholder="Deskripsi area (opsional)">{{ old('deskripsi', $area->deskripsi) }}</textarea>
                            @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Urutan</label>
                                <input type="number" name="urutan" class="form-control @error('urutan') is-invalid @enderror"
                                    value="{{ old('urutan', $area->urutan) }}" min="0">
                                @error('urutan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">Urutan tampilan (angka lebih kecil = lebih atas)</small>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Status</label>
                                <label class="form-check form-switch mt-2">
                                    <input type="checkbox" name="is_active" class="form-check-input" value="1"
                                        {{ old('is_active', $area->is_active) ? 'checked' : '' }}>
                                    <span class="form-check-label">Aktif</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <a href="{{ route('master.area-cs.index') }}" class="btn btn-outline-secondary me-2">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2" /><circle cx="12" cy="14" r="2" /><polyline points="14 4 14 8 8 8" /></svg>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
