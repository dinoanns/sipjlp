@extends('layouts.app')

@section('title', 'Edit Lembar Kerja')

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
                    <p class="empty-subtitle text-muted">
                        Tambahkan detail pekerjaan menggunakan form di samping
                    </p>
                </div>
                @else
                <div class="list-group list-group-flush">
                    @foreach($lembarKerja->details as $detail)
                    <div class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <a href="{{ asset('storage/lembar-kerja/' . $detail->foto) }}" target="_blank">
                                    <img src="{{ asset('storage/lembar-kerja/' . $detail->foto) }}"
                                         class="rounded" style="width: 80px; height: 80px; object-fit: cover;">
                                </a>
                            </div>
                            <div class="col">
                                <div class="d-flex justify-content-between">
                                    <strong>{{ $detail->jam }}</strong>
                                    <span class="badge bg-secondary">{{ $detail->lokasi->nama }}</span>
                                </div>
                                <div class="text-muted mt-1">{{ $detail->pekerjaan }}</div>
                                @if($detail->keterangan)
                                <small class="text-muted"><em>{{ $detail->keterangan }}</em></small>
                                @endif
                            </div>
                            <div class="col-auto">
                                <form action="{{ route('lembar-kerja.detail.destroy', $detail) }}" method="POST"
                                      onsubmit="return confirm('Hapus detail pekerjaan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @if($lembarKerja->details->isNotEmpty())
            <div class="card-footer text-end">
                <form action="{{ route('lembar-kerja.submit', $lembarKerja) }}" method="POST"
                      onsubmit="return confirm('Submit lembar kerja? Setelah submit, data tidak dapat diubah.')">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="ti ti-send me-2"></i> Submit Lembar Kerja
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Tambah Detail Pekerjaan</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('lembar-kerja.detail.store', $lembarKerja) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label required">Jam</label>
                        <input type="time" name="jam" class="form-control @error('jam') is-invalid @enderror"
                               value="{{ old('jam', now()->format('H:i')) }}" required>
                        @error('jam')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label required">Lokasi</label>
                        <select name="lokasi_id" class="form-select @error('lokasi_id') is-invalid @enderror" required>
                            <option value="">Pilih Lokasi</option>
                            @foreach($lokasi as $lok)
                            <option value="{{ $lok->id }}" {{ old('lokasi_id') == $lok->id ? 'selected' : '' }}>
                                {{ $lok->nama }}
                            </option>
                            @endforeach
                        </select>
                        @error('lokasi_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label required">Pekerjaan</label>
                        <textarea name="pekerjaan" class="form-control @error('pekerjaan') is-invalid @enderror"
                                  rows="3" required placeholder="Jelaskan pekerjaan yang dilakukan...">{{ old('pekerjaan') }}</textarea>
                        @error('pekerjaan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <input type="text" name="keterangan" class="form-control @error('keterangan') is-invalid @enderror"
                               value="{{ old('keterangan') }}" placeholder="Keterangan tambahan (opsional)">
                        @error('keterangan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label required">Foto Bukti</label>
                        <input type="file" name="foto" class="form-control @error('foto') is-invalid @enderror"
                               accept="image/jpeg,image/jpg,image/png" required>
                        <small class="text-muted">Format: JPG, JPEG, PNG. Maks: 2MB</small>
                        @error('foto')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="ti ti-plus me-2"></i> Tambah Pekerjaan
                    </button>
                </form>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body">
                <div class="d-flex">
                    <div class="me-3">
                        <div class="avatar avatar-md bg-primary-lt">
                            <i class="ti ti-calendar"></i>
                        </div>
                    </div>
                    <div>
                        <h4 class="mb-1">{{ $lembarKerja->tanggal->format('d F Y') }}</h4>
                        <div class="text-muted">
                            Status: <span class="badge bg-{{ $lembarKerja->status->color() }}">{{ $lembarKerja->status->label() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
