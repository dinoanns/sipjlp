@extends('layouts.app')

@section('title', 'Validasi Bukti Pekerjaan CS')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Validasi Bukti Pekerjaan Cleaning Service
                </h2>
                <div class="text-muted mt-1">
                    {{ $selectedArea?->nama ?? 'Semua Area' }} - {{ \Carbon\Carbon::create($tahun, $bulan, 1)->translatedFormat('F Y') }}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                {{ session('success') }}
                <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible" role="alert">
                {{ session('error') }}
                <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
        @endif

        <!-- Filter -->
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('lembar-kerja-cs.validasi-bukti-index') }}" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Area/Lantai</label>
                        <select name="area_id" class="form-select">
                            <option value="">Semua Area</option>
                            @foreach($areas as $area)
                                <option value="{{ $area->id }}" {{ $areaId == $area->id ? 'selected' : '' }}>
                                    {{ $area->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Bulan</label>
                        <select name="bulan" class="form-select">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Tahun</label>
                        <select name="tahun" class="form-select">
                            @for($y = now()->year - 1; $y <= now()->year + 1; $y++)
                                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Menunggu Validasi</option>
                            <option value="validated" {{ $status == 'validated' ? 'selected' : '' }}>Tervalidasi</option>
                            <option value="rejected" {{ $status == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-filter me-1"></i> Filter
                        </button>
                        <a href="{{ route('lembar-kerja-cs.validasi-bukti-index') }}" class="btn btn-secondary">
                            <i class="ti ti-refresh me-1"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Statistik -->
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-warning text-white avatar">
                                    <i class="ti ti-clock"></i>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">{{ $stats['pending'] ?? 0 }}</div>
                                <div class="text-muted">Menunggu Validasi</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-success text-white avatar">
                                    <i class="ti ti-check"></i>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">{{ $stats['validated'] ?? 0 }}</div>
                                <div class="text-muted">Tervalidasi</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-danger text-white avatar">
                                    <i class="ti ti-x"></i>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">{{ $stats['rejected'] ?? 0 }}</div>
                                <div class="text-muted">Ditolak</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Bukti Pekerjaan -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Bukti Pekerjaan</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-striped">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Area</th>
                            <th>Pekerjaan</th>
                            <th>Shift</th>
                            <th>Diupload Oleh</th>
                            <th>Waktu Upload</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($buktiList as $bukti)
                            <tr>
                                <td>{{ $bukti->jadwalBulanan->tanggal->format('d/m/Y') }}</td>
                                <td>{{ $bukti->jadwalBulanan->area->nama ?? '-' }}</td>
                                <td>
                                    <div class="fw-bold">{{ $bukti->jadwalBulanan->pekerjaan }}</div>
                                </td>
                                <td>
                                    <span class="badge" style="background-color: {{ $bukti->jadwalBulanan->shift_color }}; color: #333;">
                                        {{ $bukti->jadwalBulanan->shift->nama ?? '-' }}
                                    </span>
                                </td>
                                <td>{{ $bukti->pjlp->nama ?? '-' }}</td>
                                <td>{{ $bukti->dikerjakan_at?->format('d/m/Y H:i') ?? '-' }}</td>
                                <td>
                                    @if($bukti->is_validated)
                                        <span class="badge text-white bg-success">
                                            <i class="ti ti-check me-1"></i> Tervalidasi
                                        </span>
                                    @elseif($bukti->is_rejected)
                                        <span class="badge text-white bg-danger">
                                            <i class="ti ti-x me-1"></i> Ditolak
                                        </span>
                                    @else
                                        <span class="badge text-white bg-warning">
                                            <i class="ti ti-clock me-1"></i> Menunggu
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                                onclick="viewBukti('{{ asset('storage/' . $bukti->foto_bukti) }}', '{{ $bukti->jadwalBulanan->pekerjaan }}')">
                                            <i class="ti ti-eye"></i>
                                        </button>
                                        @if(!$bukti->is_validated && !$bukti->is_rejected)
                                            <button type="button" class="btn btn-sm btn-success"
                                                    onclick="showValidasiModal({{ $bukti->id }}, 'validate')">
                                                <i class="ti ti-check"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger"
                                                    onclick="showValidasiModal({{ $bukti->id }}, 'reject')">
                                                <i class="ti ti-x"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    Tidak ada data bukti pekerjaan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($buktiList->hasPages())
                <div class="card-footer">
                    {{ $buktiList->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal View Bukti -->
<div class="modal modal-blur fade" id="viewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalTitle">Bukti Pekerjaan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="viewImage" src="" class="img-fluid rounded" style="max-height: 70vh;">
            </div>
        </div>
    </div>
</div>

<!-- Modal Validasi -->
<div class="modal modal-blur fade" id="validasiModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="validasiModalTitle">Validasi Bukti</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="validasiForm" method="POST">
                @csrf
                <input type="hidden" name="action" id="validasiAction">
                <div class="modal-body">
                    <p id="validasiMessage"></p>
                    <div class="mb-3">
                        <label class="form-label">Catatan (opsional)</label>
                        <textarea name="catatan_validator" class="form-control" rows="3" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn" id="validasiSubmitBtn">Konfirmasi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let viewModal, validasiModal;

    document.addEventListener('DOMContentLoaded', function() {
        viewModal = new bootstrap.Modal(document.getElementById('viewModal'));
        validasiModal = new bootstrap.Modal(document.getElementById('validasiModal'));
    });

    function viewBukti(imageUrl, pekerjaan) {
        document.getElementById('viewImage').src = imageUrl;
        document.getElementById('viewModalTitle').textContent = 'Bukti: ' + pekerjaan;
        viewModal.show();
    }

    function showValidasiModal(buktiId, action) {
        const form = document.getElementById('validasiForm');
        const actionInput = document.getElementById('validasiAction');
        const title = document.getElementById('validasiModalTitle');
        const message = document.getElementById('validasiMessage');
        const submitBtn = document.getElementById('validasiSubmitBtn');

        form.action = '{{ url("lembar-kerja-cs/validasi-bukti") }}/' + buktiId;
        actionInput.value = action;

        if (action === 'validate') {
            title.textContent = 'Validasi Bukti Pekerjaan';
            message.textContent = 'Apakah Anda yakin ingin memvalidasi bukti pekerjaan ini?';
            submitBtn.className = 'btn btn-success';
            submitBtn.textContent = 'Ya, Validasi';
        } else {
            title.textContent = 'Tolak Bukti Pekerjaan';
            message.textContent = 'Apakah Anda yakin ingin menolak bukti pekerjaan ini?';
            submitBtn.className = 'btn btn-danger';
            submitBtn.textContent = 'Ya, Tolak';
        }

        validasiModal.show();
    }
</script>
@endpush
