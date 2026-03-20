@extends('layouts.app')

@section('title', 'Edit Jenis Cuti')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Edit Jenis Cuti</h3>
            </div>
            <form action="{{ route('master.jenis-cuti.update', $jenisCuti) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label required">Nama Jenis Cuti</label>
                        <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                               value="{{ old('nama', $jenisCuti->nama) }}" required>
                        @error('nama')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Maksimal Hari/Tahun</label>
                        <input type="number" name="max_hari" class="form-control @error('max_hari') is-invalid @enderror"
                               value="{{ old('max_hari', $jenisCuti->max_hari) }}" min="1">
                        <small class="text-muted">Kosongkan jika tidak ada batas</small>
                        @error('max_hari')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror"
                                  rows="3">{{ old('keterangan', $jenisCuti->keterangan) }}</textarea>
                        @error('keterangan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-check">
                            <input type="checkbox" name="is_active" value="1" class="form-check-input"
                                   {{ old('is_active', $jenisCuti->is_active) ? 'checked' : '' }}>
                            <span class="form-check-label">Aktif</span>
                        </label>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a href="{{ route('master.jenis-cuti.index') }}" class="btn btn-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
