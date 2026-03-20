@extends('layouts.app')

@section('title', 'Import Absensi')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Import Data Absensi</h3>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h4 class="alert-title">Petunjuk Import</h4>
                    <ul class="mb-0">
                        <li>Format file yang didukung: CSV, XLS, XLSX</li>
                        <li>Pastikan format kolom sesuai dengan template</li>
                        <li>Data yang sudah ada akan di-update berdasarkan tanggal dan PJLP</li>
                    </ul>
                </div>

                <form action="{{ route('absensi.import.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label required">Tanggal Absensi</label>
                        <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror"
                               value="{{ old('tanggal', now()->format('Y-m-d')) }}" required>
                        @error('tanggal')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label required">File Absensi</label>
                        <input type="file" name="file" class="form-control @error('file') is-invalid @enderror"
                               accept=".csv,.xls,.xlsx" required>
                        <small class="text-muted">Format: CSV, XLS, XLSX. Maks: 5MB</small>
                        @error('file')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('absensi.index') }}" class="btn btn-secondary">
                            <i class="ti ti-arrow-left me-2"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-upload me-2"></i> Import
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">Import via ODBC (Mesin Absensi)</h3>
            </div>
            <div class="card-body">
                <p class="text-muted">
                    Fitur import langsung dari mesin absensi via ODBC sedang dalam pengembangan.
                </p>
                <div class="alert alert-warning mb-0">
                    <i class="ti ti-alert-triangle me-2"></i>
                    Pastikan DSN "AbsensiMesin" sudah dikonfigurasi di Windows ODBC Data Sources
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
