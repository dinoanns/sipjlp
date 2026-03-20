@extends('layouts.app')

@section('title', 'Tambah Shift')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Tambah Shift Baru</h3>
            </div>
            <form action="{{ route('master.shift.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label required">Nama Shift</label>
                        <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                               value="{{ old('nama') }}" required>
                        @error('nama')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Jam Mulai</label>
                            <input type="time" name="jam_mulai" class="form-control @error('jam_mulai') is-invalid @enderror"
                                   value="{{ old('jam_mulai') }}" required>
                            @error('jam_mulai')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Jam Selesai</label>
                            <input type="time" name="jam_selesai" class="form-control @error('jam_selesai') is-invalid @enderror"
                                   value="{{ old('jam_selesai') }}" required>
                            @error('jam_selesai')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-check">
                            <input type="checkbox" name="is_active" value="1" class="form-check-input" checked>
                            <span class="form-check-label">Aktif</span>
                        </label>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a href="{{ route('master.shift.index') }}" class="btn btn-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
