@extends('layouts.app')

@section('title', 'Detail Lembar Kerja')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Detail Pekerjaan</h3>
            </div>
            <div class="card-body">
                @if($lembarKerja->details->isEmpty())
                <div class="empty">
                    <p class="empty-title">Belum ada detail pekerjaan</p>
                </div>
                @else
                <div class="list-group list-group-flush">
                    @foreach($lembarKerja->details as $detail)
                    <div class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <a href="{{ asset('storage/lembar-kerja/' . $detail->foto) }}" target="_blank">
                                    <img src="{{ asset('storage/lembar-kerja/' . $detail->foto) }}"
                                         class="rounded" style="width: 100px; height: 100px; object-fit: cover;">
                                </a>
                            </div>
                            <div class="col">
                                <div class="d-flex justify-content-between">
                                    <strong class="text-primary">{{ $detail->jam }}</strong>
                                    <span class="badge bg-secondary">{{ $detail->lokasi->nama }}</span>
                                </div>
                                <div class="mt-1">{{ $detail->pekerjaan }}</div>
                                @if($detail->keterangan)
                                <small class="text-muted"><em>{{ $detail->keterangan }}</em></small>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Informasi</h3>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-5">Tanggal:</dt>
                    <dd class="col-7">{{ $lembarKerja->tanggal->format('d F Y') }}</dd>

                    <dt class="col-5">PJLP:</dt>
                    <dd class="col-7">{{ $lembarKerja->pjlp->nama }}</dd>

                    <dt class="col-5">Unit:</dt>
                    <dd class="col-7"><span class="badge">{{ $lembarKerja->pjlp->unit->label() }}</span></dd>

                    <dt class="col-5">Status:</dt>
                    <dd class="col-7">
                        <span class="badge bg-{{ $lembarKerja->status->color() }}">
                            {{ $lembarKerja->status->label() }}
                        </span>
                    </dd>

                    <dt class="col-5">Jumlah Kegiatan:</dt>
                    <dd class="col-7">{{ $lembarKerja->details->count() }} kegiatan</dd>

                    @if($lembarKerja->submitted_at)
                    <dt class="col-5">Disubmit:</dt>
                    <dd class="col-7">{{ $lembarKerja->submitted_at->format('d M Y H:i') }}</dd>
                    @endif
                </dl>
            </div>
        </div>

        @if($lembarKerja->validasi)
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">Validasi</h3>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-5">Validator:</dt>
                    <dd class="col-7">{{ $lembarKerja->validasi->validator->name ?? '-' }}</dd>

                    <dt class="col-5">Tanggal:</dt>
                    <dd class="col-7">{{ $lembarKerja->validasi->validated_at?->format('d M Y H:i') }}</dd>

                    @if($lembarKerja->validasi->catatan)
                    <dt class="col-5">Catatan:</dt>
                    <dd class="col-7">{{ $lembarKerja->validasi->catatan }}</dd>
                    @endif
                </dl>
            </div>
        </div>
        @endif

        @can('lembar-kerja.validate')
        @if($lembarKerja->status->value === 'submitted')
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">Aksi Validasi</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('lembar-kerja.validate', $lembarKerja) }}" method="POST" class="mb-2">
                    @csrf
                    <div class="mb-2">
                        <textarea name="catatan" class="form-control" rows="2"
                                  placeholder="Catatan validasi (opsional)"></textarea>
                    </div>
                    <button type="submit" class="btn btn-success w-100">
                        <i class="ti ti-check me-2"></i> Validasi
                    </button>
                </form>

                <hr>

                <form action="{{ route('lembar-kerja.reject', $lembarKerja) }}" method="POST">
                    @csrf
                    <div class="mb-2">
                        <textarea name="catatan" class="form-control @error('catatan') is-invalid @enderror"
                                  rows="2" placeholder="Alasan penolakan (wajib)" required></textarea>
                        @error('catatan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-danger w-100"
                            onclick="return confirm('Tolak lembar kerja ini?')">
                        <i class="ti ti-x me-2"></i> Tolak
                    </button>
                </form>
            </div>
        </div>
        @endif
        @endcan

        <div class="mt-3">
            <a href="{{ route('lembar-kerja.index') }}" class="btn btn-secondary w-100">
                <i class="ti ti-arrow-left me-2"></i> Kembali
            </a>
        </div>
    </div>
</div>
@endsection
