@extends('layouts.app')

@section('title', 'Tambah Lokasi')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Tambah Lokasi Baru</h3>
            </div>
            <form action="{{ route('master.lokasi.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label required">Nama Lokasi</label>
                        <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                               value="{{ old('nama') }}" required>
                        @error('nama')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror"
                                  rows="3">{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-check">
                            <input type="checkbox" name="is_active" value="1" class="form-check-input" checked>
                            <span class="form-check-label">Aktif</span>
                        </label>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a href="{{ route('master.lokasi.index') }}" class="btn btn-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
