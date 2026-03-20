@extends('layouts.app')

@section('title', 'Badge Belum Terhubung')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <a href="{{ route('tarik-absen.index') }}" class="text-muted text-decoration-none">
                    <i class="ti ti-arrow-left me-1"></i> Kembali
                </a>
                <h2 class="page-title mt-2">
                    Badge Belum Terhubung
                </h2>
                <div class="text-muted mt-1">
                    Hubungkan badge dari mesin absen ke data PJLP
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Daftar Badge Belum Terhubung</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>Badge Number</th>
                                    <th>Total Log</th>
                                    <th>Terakhir Scan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($badges as $badge)
                                    <tr>
                                        <td><code class="fw-bold">{{ $badge->badge_number }}</code></td>
                                        <td>{{ number_format($badge->total_logs) }} data</td>
                                        <td>{{ \Carbon\Carbon::parse($badge->last_check)->diffForHumans() }}</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary"
                                                onclick="openLinkModal('{{ $badge->badge_number }}')">
                                                <i class="ti ti-link me-1"></i> Hubungkan
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4">
                                            <div class="empty">
                                                <div class="empty-icon">
                                                    <i class="ti ti-check text-success"></i>
                                                </div>
                                                <p class="empty-title">Semua badge sudah terhubung!</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">PJLP Tanpa Badge</h3>
                    </div>
                    <div class="card-body p-0" style="max-height: 400px; overflow-y: auto;">
                        <div class="list-group list-group-flush">
                            @forelse($pjlps as $pjlp)
                                <div class="list-group-item">
                                    <div class="fw-bold">{{ $pjlp->nama }}</div>
                                    <small class="text-muted">{{ $pjlp->nip }}</small>
                                </div>
                            @empty
                                <div class="list-group-item text-center text-muted">
                                    Semua PJLP sudah memiliki badge
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Link Modal -->
<div class="modal modal-blur fade" id="linkModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hubungkan Badge ke PJLP</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Badge: <code id="modalBadgeNumber" class="fw-bold"></code></p>

                <div class="mb-3">
                    <label class="form-label">Pilih PJLP</label>
                    <select class="form-select" id="modalPjlpId">
                        <option value="">-- Pilih PJLP --</option>
                        @foreach($pjlps as $pjlp)
                            <option value="{{ $pjlp->id }}">{{ $pjlp->nama }} ({{ $pjlp->nip }})</option>
                        @endforeach
                    </select>
                </div>

                <div id="linkResult" class="alert d-none"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="linkBadge()">
                    <i class="ti ti-link me-1"></i> Hubungkan
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentBadge = '';
let linkModalInstance = null;

function openLinkModal(badge) {
    currentBadge = badge;
    document.getElementById('modalBadgeNumber').textContent = badge;
    document.getElementById('modalPjlpId').value = '';
    document.getElementById('linkResult').classList.add('d-none');

    linkModalInstance = new bootstrap.Modal(document.getElementById('linkModal'));
    linkModalInstance.show();
}

function linkBadge() {
    const pjlpId = document.getElementById('modalPjlpId').value;
    const resultDiv = document.getElementById('linkResult');

    if (!pjlpId) {
        resultDiv.classList.remove('d-none', 'alert-success');
        resultDiv.classList.add('alert-danger');
        resultDiv.textContent = 'Pilih PJLP terlebih dahulu';
        return;
    }

    fetch('{{ route("tarik-absen.map-badge") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({
            badge_number: currentBadge,
            pjlp_id: pjlpId
        })
    })
    .then(response => response.json())
    .then(data => {
        resultDiv.classList.remove('d-none', 'alert-danger', 'alert-success');
        if (data.success) {
            resultDiv.classList.add('alert-success');
            resultDiv.innerHTML = `<i class="ti ti-check me-2"></i>${data.message}`;
            setTimeout(() => location.reload(), 1500);
        } else {
            resultDiv.classList.add('alert-danger');
            resultDiv.innerHTML = `<i class="ti ti-x me-2"></i>${data.message || 'Gagal menghubungkan'}`;
        }
    })
    .catch(error => {
        resultDiv.classList.remove('d-none');
        resultDiv.classList.add('alert-danger');
        resultDiv.textContent = 'Error: ' + error.message;
    });
}
</script>
@endpush
