@extends('layouts.app')

@section('title', 'Tambah User')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Tambah User Baru</h3>
            </div>
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label required">Nama</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label required">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}" required>
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label required">Password</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                               required>
                        @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label required">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label required">Role</label>
                        <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="">Pilih Role</option>
                            @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                                {{ ucfirst($role->name) }}
                            </option>
                            @endforeach
                        </select>
                        @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Unit (untuk Koordinator)</label>
                        <select name="unit" class="form-select @error('unit') is-invalid @enderror">
                            <option value="">Tidak Ada</option>
                            @foreach(\App\Enums\UnitType::cases() as $unit)
                            <option value="{{ $unit->value }}" {{ old('unit') == $unit->value ? 'selected' : '' }}>
                                {{ $unit->label() }}
                            </option>
                            @endforeach
                        </select>
                        @error('unit')
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
                    <a href="{{ route('users.index') }}" class="btn btn-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
