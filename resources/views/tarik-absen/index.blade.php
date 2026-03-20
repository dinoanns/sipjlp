@extends('layouts.app')

@section('title', 'Tarik Absensi dari Mesin')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <i class="ti ti-fingerprint me-2"></i>
                    Tarik Absensi dari Mesin
                </h2>
                <div class="text-muted mt-1">
                    Sinkronisasi data absensi dari mesin fingerprint ke sistem
                </div>
            </div>
            <div class="col-auto ms-auto">
                <div class="btn-list">
                    <button type="button" class="btn btn-outline-secondary" onclick="testConnection()">
                        <i class="ti ti-plug me-1"></i> Test Koneksi
                    </button>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#pullModal">
                        <i class="ti ti-cloud-download me-1"></i> Tarik Data
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                <div class="d-flex">
                    <div><i class="ti ti-check icon alert-icon"></i></div>
                    <div>{{ session('success') }}</div>
                </div>
                <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible" role="alert">
                <div class="d-flex">
                    <div><i class="ti ti-alert-circle icon alert-icon"></i></div>
                    <div>{{ session('error') }}</div>
                </div>
                <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
        @endif

        <!-- Stats Cards -->
        <div class="row row-deck row-cards mb-4">
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Data Hari Ini</div>
                        </div>
                        <div class="h1 mb-0">{{ number_format($stats['total_hari_ini']) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Data Bulan Ini</div>
                        </div>
                        <div class="h1 mb-0">{{ number_format($stats['total_bulan_ini']) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Badge Belum Terhubung</div>
                        </div>
                        <div class="h1 mb-0 {{ $stats['belum_terhubung'] > 0 ? 'text-warning' : 'text-success' }}">
                            {{ $stats['belum_terhubung'] }}
                        </div>
                        @if($stats['belum_terhubung'] > 0)
                            <a href="{{ route('tarik-absen.unlinked') }}" class="small">Lihat &rarr;</a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">PJLP Tanpa Badge</div>
                        </div>
                        <div class="h1 mb-0 {{ $pjlpTanpaBadge > 0 ? 'text-danger' : 'text-success' }}">
                            {{ $pjlpTanpaBadge }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('tarik-absen.index') }}" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" value="{{ $tanggal }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Badge Number</label>
                        <input type="text" name="badge_number" class="form-control" value="{{ $badgeNumber }}" placeholder="Cari badge...">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-search me-1"></i> Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Data Table -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Log Absensi - {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Badge</th>
                            <th>Nama PJLP</th>
                            <th>Waktu</th>
                            <th>Tipe</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td><code>{{ $log->badge_number }}</code></td>
                                <td>
                                    @if($log->pjlp)
                                        <span class="fw-bold">{{ $log->pjlp->nama }}</span>
                                    @else
                                        <span class="text-warning">
                                            <i class="ti ti-alert-triangle me-1"></i>
                                            Belum terhubung
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $log->check_time->format('H:i:s') }}</td>
                                <td>
                                    @if($log->check_type === 'I')
                                        <span class="badge text-white" style="background-color: #2fb344;">Masuk</span>
                                    @else
                                        <span class="badge text-white" style="background-color: #4299e1;">Pulang</span>
                                    @endif
                                </td>
                                <td>
                                    @if($log->is_processed)
                                        <span class="badge text-white" style="background-color: #2fb344;">Diproses</span>
                                    @else
                                        <span class="badge text-white" style="background-color: #667382;">Pending</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <div class="empty">
                                        <div class="empty-icon">
                                            <i class="ti ti-database-off"></i>
                                        </div>
                                        <p class="empty-title">Tidak ada data</p>
                                        <p class="empty-subtitle text-muted">
                                            Belum ada data absensi untuk tanggal ini
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($logs->hasPages())
                <div class="card-footer">
                    {{ $logs->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Pull Modal -->
<div class="modal modal-blur fade" id="pullModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tarik Data Absensi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('tarik-absen.pull') }}" method="POST" id="pullForm">
                @csrf
                <div class="modal-body">
                    <p class="text-muted">Pilih rentang tanggal untuk menarik data dari mesin absen, atau kosongkan untuk menarik semua data tahun ini.</p>

                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" name="start_date" class="form-control" value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Tanggal Akhir</label>
                            <input type="date" name="end_date" class="form-control" value="{{ now()->format('Y-m-d') }}">
                        </div>
                    </div>

                    <div id="pullResult" class="alert d-none"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="pullBtn">
                        <i class="ti ti-cloud-download me-1"></i> Tarik Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Connection Test Modal -->
<div class="modal modal-blur fade" id="connectionModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Test Koneksi Mesin Absen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="connectionResult">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-2">Mengetes koneksi...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function testConnection() {
    const modal = new bootstrap.Modal(document.getElementById('connectionModal'));
    modal.show();

    fetch('{{ route("tarik-absen.test-connection") }}', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
    .then(response => response.json())
    .then(data => {
        let html = '';
        if (data.success) {
            html = `
                <div class="alert alert-success">
                    <i class="ti ti-check me-2"></i>${data.message}
                </div>
            `;
            if (data.sample_data && data.sample_data.length > 0) {
                html += `<p class="fw-bold mt-3">Sample Data (5 terakhir):</p>
                <table class="table table-sm">
                    <thead><tr><th>Badge</th><th>Waktu</th><th>Tipe</th></tr></thead>
                    <tbody>`;
                data.sample_data.forEach(d => {
                    html += `<tr><td><code>${d.badge}</code></td><td>${d.time}</td><td>${d.type === 'I' ? 'Masuk' : 'Pulang'}</td></tr>`;
                });
                html += '</tbody></table>';
            }
        } else {
            html = `<div class="alert alert-danger"><i class="ti ti-x me-2"></i>${data.message}</div>`;
        }
        document.getElementById('connectionResult').innerHTML = html;
    })
    .catch(error => {
        document.getElementById('connectionResult').innerHTML = `
            <div class="alert alert-danger">
                <i class="ti ti-x me-2"></i>Error: ${error.message}
            </div>`;
    });
}

document.getElementById('pullForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('pullBtn');
    const resultDiv = document.getElementById('pullResult');

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menarik data...';
    resultDiv.classList.add('d-none');

    const formData = new FormData(this);

    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
    .then(response => response.json())
    .then(data => {
        resultDiv.classList.remove('d-none', 'alert-success', 'alert-danger');
        if (data.success) {
            resultDiv.classList.add('alert-success');
            resultDiv.innerHTML = `<i class="ti ti-check me-2"></i>${data.message}`;
            setTimeout(() => location.reload(), 2000);
        } else {
            resultDiv.classList.add('alert-danger');
            resultDiv.innerHTML = `<i class="ti ti-x me-2"></i>${data.message}`;
        }
    })
    .catch(error => {
        resultDiv.classList.remove('d-none');
        resultDiv.classList.add('alert-danger');
        resultDiv.innerHTML = `<i class="ti ti-x me-2"></i>Error: ${error.message}`;
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="ti ti-cloud-download me-1"></i> Tarik Data';
    });
});
</script>
@endpush
