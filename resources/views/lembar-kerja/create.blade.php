@extends('layouts.app')

@section('title', 'Buat Lembar Kerja')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Buat Lembar Kerja Baru</h3>
    </div>
    <div class="card-body">
        <div class="mb-3">
            <label class="form-label">Nama PJLP</label>
            <input type="text" class="form-control" value="{{ $pjlp->nama }}" disabled>
        </div>
        <div class="mb-3">
            <label class="form-label">Unit</label>
            <input type="text" class="form-control" value="{{ $pjlp->unit->label() }}" disabled>
        </div>
        <div class="mb-3">
            <label class="form-label">Tanggal</label>
            <input type="text" class="form-control" value="{{ now()->format('d F Y') }}" disabled>
        </div>
    </div>
    <div class="card-footer text-end">
        <a href="{{ route('lembar-kerja.index') }}" class="btn btn-secondary me-2">Batal</a>
        <form action="{{ route('lembar-kerja.store') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-primary">
                <i class="ti ti-plus me-2"></i> Buat Lembar Kerja
            </button>
        </form>
    </div>
</div>
@endsection
