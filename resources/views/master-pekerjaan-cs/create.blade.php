@extends('layouts.app')

@section('title', 'Tambah Pekerjaan CS')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    <a href="{{ route('master-pekerjaan-cs.index') }}">
                        &larr; Kembali
                    </a>
                </div>
                <h2 class="page-title">
                    Tambah Pekerjaan CS
                </h2>
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
                        <h3 class="card-title">Form Tambah Pekerjaan</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('master-pekerjaan-cs.store') }}">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label required">Nama Pekerjaan</label>
                                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                                       value="{{ old('nama') }}" placeholder="Contoh: Membersihkan Toilet" required>
                                @error('nama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Kode</label>
                                <input type="text" name="kode" class="form-control @error('kode') is-invalid @enderror"
                                       value="{{ old('kode') }}" placeholder="Contoh: TOILET" style="text-transform: uppercase;">
                                @error('kode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Kode unik untuk identifikasi pekerjaan (opsional)</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Deskripsi</label>
                                <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror"
                                          rows="3" placeholder="Deskripsi pekerjaan...">{{ old('deskripsi') }}</textarea>
                                @error('deskripsi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Urutan</label>
                                <input type="number" name="urutan" class="form-control @error('urutan') is-invalid @enderror"
                                       value="{{ old('urutan', $lastUrutan + 1) }}" min="0">
                                @error('urutan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Urutan tampilan di dropdown</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-check form-switch">
                                    <input type="checkbox" name="is_active" class="form-check-input" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <span class="form-check-label">Aktif</span>
                                </label>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2" /><circle cx="12" cy="14" r="2" /><polyline points="14 4 14 8 8 8" /></svg>
                                    Simpan
                                </button>
                                <a href="{{ route('master-pekerjaan-cs.index') }}" class="btn btn-secondary">
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
