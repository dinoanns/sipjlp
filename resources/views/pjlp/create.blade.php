@extends('layouts.app')

@section('title', 'Tambah PJLP')
@section('pretitle', 'Data PJLP')

@section('content')
<form action="{{ route('pjlp.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Data PJLP</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label required">NIP</label>
                            <input type="text" name="nip" class="form-control @error('nip') is-invalid @enderror"
                                   value="{{ old('nip') }}" required>
                            @error('nip')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                                   value="{{ old('nama') }}" required>
                            @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Kode Badge Absensi</label>
                            <input type="text" name="badge_number" class="form-control @error('badge_number') is-invalid @enderror"
                                   value="{{ old('badge_number') }}" placeholder="Contoh: 001, 002, dst">
                            @error('badge_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Kode badge dari mesin fingerprint (Acc Log Finger)</small>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label required">Unit</label>
                            <select name="unit" class="form-select @error('unit') is-invalid @enderror" required>
                                <option value="">Pilih Unit</option>
                                <option value="security" {{ old('unit') == 'security' ? 'selected' : '' }}>Security</option>
                                <option value="cleaning" {{ old('unit') == 'cleaning' ? 'selected' : '' }}>Cleaning Service</option>
                            </select>
                            @error('unit')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Jabatan</label>
                            <input type="text" name="jabatan" class="form-control @error('jabatan') is-invalid @enderror"
                                   value="{{ old('jabatan') }}" required>
                            @error('jabatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">No. Telepon</label>
                            <input type="tel" name="no_telp" class="form-control @error('no_telp') is-invalid @enderror"
                                   value="{{ old('no_telp') }}">
                            @error('no_telp')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Tanggal Bergabung</label>
                            <input type="date" name="tanggal_bergabung" class="form-control @error('tanggal_bergabung') is-invalid @enderror"
                                   value="{{ old('tanggal_bergabung') }}" required>
                            @error('tanggal_bergabung')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" rows="2" class="form-control @error('alamat') is-invalid @enderror">{{ old('alamat') }}</textarea>
                        @error('alamat')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Foto</label>
                        <input type="file" name="foto" class="form-control @error('foto') is-invalid @enderror" accept="image/*">
                        @error('foto')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Format: JPG, PNG. Maksimal 2MB.</small>
                    </div>
                </div>
            </div>

            <!-- Akun User -->
            <div class="card mt-3">
                <div class="card-header">
                    <label class="form-check form-switch m-0">
                        <input type="checkbox" name="create_user" class="form-check-input" id="createUserCheck" value="1" {{ old('create_user') ? 'checked' : '' }}>
                        <span class="form-check-label">Buat Akun Login</span>
                    </label>
                </div>
                <div class="card-body" id="userFields" style="{{ old('create_user') ? '' : 'display: none;' }}">
                    <div class="mb-3">
                        <label class="form-label required">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}">
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Password</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                        @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Minimal 8 karakter</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <a href="{{ route('pjlp.index') }}" class="btn btn-link">Batal</a>
                        <button type="submit" class="btn btn-primary ms-auto">
                            <i class="ti ti-device-floppy me-2"></i> Simpan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
document.getElementById('createUserCheck').addEventListener('change', function() {
    document.getElementById('userFields').style.display = this.checked ? 'block' : 'none';
});
</script>
@endpush
@endsection
