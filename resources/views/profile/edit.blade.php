@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="row">
    <div class="col-lg-6">
        <form action="{{ route('profile.update') }}" method="POST">
            @csrf
            @method('PATCH')
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Informasi Profil</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label required">Nama</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $user->name) }}" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', $user->email) }}" required>
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <input type="text" class="form-control" value="{{ $user->roles->pluck('name')->implode(', ') }}" disabled>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
    <div class="col-lg-6">
        <form action="{{ route('profile.password') }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Ubah Password</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label required">Password Saat Ini</label>
                        <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
                        @error('current_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Password Baru</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                        @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-warning">Ubah Password</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
