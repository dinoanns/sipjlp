@extends('layouts.app')

@section('title', 'Detail Pengajuan Cuti')
@section('pretitle', 'Detail')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Informasi Pengajuan</h3>
                <div class="card-actions">
                    <span class="badge text-white bg-{{ $cuti->status->color() }} fs-5">{{ $cuti->status->label() }}</span>
                </div>
            </div>
            <div class="card-body">
                <div class="datagrid">
                    <div class="datagrid-item">
                        <div class="datagrid-title">Tanggal Permohonan</div>
                        <div class="datagrid-content">{{ $cuti->tanggal_permohonan->format('d M Y H:i') }}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Nama PJLP</div>
                        <div class="datagrid-content">{{ $cuti->pjlp->nama }}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">NIP</div>
                        <div class="datagrid-content">{{ $cuti->pjlp->nip }}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Unit</div>
                        <div class="datagrid-content">{{ $cuti->pjlp->unit->label() }}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Jenis Cuti</div>
                        <div class="datagrid-content">{{ $cuti->jenisCuti->nama }}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Nomor Telepon</div>
                        <div class="datagrid-content">{{ $cuti->no_telp }}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Tanggal Mulai</div>
                        <div class="datagrid-content">{{ $cuti->tgl_mulai->format('d M Y') }}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Tanggal Selesai</div>
                        <div class="datagrid-content">{{ $cuti->tgl_selesai->format('d M Y') }}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Jumlah Hari</div>
                        <div class="datagrid-content"><strong>{{ $cuti->jumlah_hari }} hari</strong></div>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="form-label">Alasan Cuti</label>
                    <div class="p-3 bg-light rounded">
                        {{ $cuti->alasan }}
                    </div>
                </div>

                @if($cuti->status->value !== 'menunggu')
                <hr class="my-4">
                <h4>Informasi Persetujuan</h4>
                <div class="datagrid">
                    <div class="datagrid-item">
                        <div class="datagrid-title">Diproses Oleh</div>
                        <div class="datagrid-content">{{ $cuti->approvedBy?->name ?? '-' }}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Waktu Proses</div>
                        <div class="datagrid-content">{{ $cuti->approved_at?->format('d M Y H:i') ?? '-' }}</div>
                    </div>
                </div>

                @if($cuti->alasan_penolakan)
                <div class="mt-3">
                    <label class="form-label text-danger">Alasan Penolakan</label>
                    <div class="p-3 bg-danger-lt rounded">
                        {{ $cuti->alasan_penolakan }}
                    </div>
                </div>
                @endif
                @endif
            </div>
            <div class="card-footer">
                <a href="{{ route('cuti.index') }}" class="btn btn-link">
                    <i class="ti ti-arrow-left me-2"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    @can('approve', $cuti)
    @if($cuti->status->value === 'menunggu')
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Aksi Koordinator</h3>
            </div>
            <div class="card-body">
                <!-- Approve -->
                <form action="{{ route('cuti.approve', $cuti) }}" method="POST" class="mb-3">
                    @csrf
                    <button type="submit" class="btn btn-success w-100" onclick="return confirm('Yakin ingin menyetujui cuti ini?')">
                        <i class="ti ti-check me-2"></i> Setujui Cuti
                    </button>
                </form>

                <hr>

                <!-- Reject -->
                <form action="{{ route('cuti.reject', $cuti) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label required">Alasan Penolakan</label>
                        <textarea name="alasan_penolakan" rows="3" class="form-control @error('alasan_penolakan') is-invalid @enderror"
                                  placeholder="Jelaskan alasan penolakan..." required>{{ old('alasan_penolakan') }}</textarea>
                        @error('alasan_penolakan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Yakin ingin menolak cuti ini?')">
                        <i class="ti ti-x me-2"></i> Tolak Cuti
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endif
    @endcan
</div>
@endsection
