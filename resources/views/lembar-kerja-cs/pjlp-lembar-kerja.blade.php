@extends('layouts.app')

@section('title', 'Lembar Kerja CS')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Lembar Kerja Cleaning Service
                </h2>
                <div class="text-muted mt-1">
                    {{ $selectedArea?->nama ?? 'Pilih Area' }} - {{ \Carbon\Carbon::create($tahun, $bulan, 1)->translatedFormat('F Y') }}
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

        <!-- Filter Area dan Bulan -->
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('lembar-kerja-cs.index') }}" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Area/Lantai</label>
                        <select name="area_id" class="form-select" onchange="this.form.submit()">
                            @foreach($areas as $area)
                                <option value="{{ $area->id }}" {{ $areaId == $area->id ? 'selected' : '' }}>
                                    {{ $area->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Bulan</label>
                        <select name="bulan" class="form-select" onchange="this.form.submit()">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Tahun</label>
                        <select name="tahun" class="form-select" onchange="this.form.submit()">
                            @for($y = now()->year - 1; $y <= now()->year + 1; $y++)
                                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-search me-1"></i> Tampilkan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Keterangan -->
        <div class="card mb-3">
            <div class="card-body py-2">
                <div class="d-flex flex-wrap gap-3 align-items-center">
                    <span class="fw-bold">Warna Shift:</span>
                    <span class="badge" style="background-color: #cce5ff; color: #004085;">Shift Pagi</span>
                    <span class="badge" style="background-color: #fff3cd; color: #856404;">Shift Siang</span>
                    <span class="badge" style="background-color: #f8c8dc; color: #721c24;">Shift Malam</span>
                    <span class="mx-2">|</span>
                    <span class="fw-bold">Status:</span>
                    <span class="badge text-white bg-success"><i class="ti ti-circle-check"></i> Tervalidasi</span>
                    <span class="badge text-white bg-warning"><i class="ti ti-clock"></i> Menunggu Validasi</span>
                    <span class="badge text-white bg-danger"><i class="ti ti-x"></i> Ditolak</span>
                    <span class="badge text-white bg-secondary">Belum Upload</span>
                </div>
            </div>
        </div>

        <!-- Tabel Lembar Kerja per Hari -->
        @foreach($dataPerHari as $dateKey => $data)
            @if($data['jadwals']->count() > 0)
            <div class="card mb-3 {{ $data['isToday'] ? 'border-primary' : '' }}">
                <div class="card-header {{ $data['isToday'] ? 'bg-primary-lt' : ($data['isWeekend'] ? 'bg-light' : '') }}">
                    <h3 class="card-title">
                        <i class="ti ti-calendar me-2"></i>
                        {{ $data['tanggal']->format('d') }} {{ $data['tanggal']->translatedFormat('F Y') }}
                        <span class="text-muted">({{ $data['hari'] }})</span>
                        @if($data['isToday'])
                            <span class="badge text-white bg-primary ms-2">Hari Ini</span>
                        @endif
                    </h3>
                    <div class="card-actions">
                        <span class="badge text-white bg-blue">{{ $data['jadwals']->count() }} pekerjaan</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table table-striped">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th>Pekerjaan</th>
                                    <th style="width: 100px;">Shift</th>
                                    <th style="width: 150px;">Pegawai</th>
                                    <th style="width: 120px;">Status</th>
                                    <th style="width: 150px;">Waktu Upload</th>
                                    <th style="width: 150px;">Diupload Oleh</th>
                                    <th style="width: 120px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['jadwals'] as $index => $jadwal)
                                    @php
                                        $buktiTerakhir = $jadwal->semuaBukti->first();
                                        $sudahUpload = $buktiTerakhir != null;
                                        $bgColor = $jadwal->shift_color;
                                    @endphp
                                    <tr style="background-color: {{ $bgColor }};" id="row-{{ $jadwal->id }}">
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>
                                            <div class="fw-bold">{{ $jadwal->pekerjaan }}</div>
                                            @if($jadwal->keterangan)
                                                <small class="text-muted">{{ $jadwal->keterangan }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $shiftColor = match(strtolower($jadwal->shift?->nama ?? '')) {
                                                    'pagi' => 'success',
                                                    'siang' => 'info',
                                                    'malam' => 'primary',
                                                    default => 'secondary',
                                                };
                                            @endphp
                                            <span class="badge text-white bg-{{ $shiftColor }}">
                                                {{ $jadwal->shift?->nama ?? '-' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($jadwal->pjlp)
                                                <span class="fw-medium">{{ $jadwal->pjlp->nama }}</span>
                                            @else
                                                <span class="text-muted">Semua</span>
                                            @endif
                                        </td>
                                        <td id="status-{{ $jadwal->id }}">
                                            @if($sudahUpload)
                                                @if($buktiTerakhir->is_validated)
                                                    <span class="badge text-white bg-success">
                                                        <i class="ti ti-circle-check me-1"></i> Tervalidasi
                                                    </span>
                                                @elseif($buktiTerakhir->is_rejected)
                                                    <span class="badge text-white bg-danger">
                                                        <i class="ti ti-x me-1"></i> Ditolak
                                                    </span>
                                                @else
                                                    <span class="badge text-white bg-warning">
                                                        <i class="ti ti-clock me-1"></i> Menunggu
                                                    </span>
                                                @endif
                                            @else
                                                <span class="badge text-white bg-secondary">Belum Upload</span>
                                            @endif
                                        </td>
                                        <td id="waktu-{{ $jadwal->id }}">
                                            @if($sudahUpload)
                                                {{ $buktiTerakhir->dikerjakan_at?->format('H:i') ?? '-' }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td id="uploader-{{ $jadwal->id }}">
                                            @if($sudahUpload && $buktiTerakhir->pjlp)
                                                {{ $buktiTerakhir->pjlp->nama }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if($data['isToday'])
                                                @if($sudahUpload)
                                                    <button type="button" class="btn btn-sm btn-outline-success" onclick="viewBukti({{ $jadwal->id }}, '{{ asset('storage/' . $buktiTerakhir->foto_bukti) }}')">
                                                        <i class="ti ti-eye"></i> Lihat
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-primary" onclick="showUploadModal({{ $jadwal->id }}, '{{ $jadwal->pekerjaan }}')">
                                                        <i class="ti ti-upload"></i> Upload
                                                    </button>
                                                @endif
                                            @else
                                                @if($sudahUpload)
                                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="viewBukti({{ $jadwal->id }}, '{{ asset('storage/' . $buktiTerakhir->foto_bukti) }}')">
                                                        <i class="ti ti-eye"></i> Lihat
                                                    </button>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        @endforeach

        @if(collect($dataPerHari)->pluck('jadwals')->flatten()->count() == 0)
            <div class="card">
                <div class="card-body">
                    <div class="empty">
                        <div class="empty-icon">
                            <i class="ti ti-clipboard-list" style="font-size: 3rem;"></i>
                        </div>
                        <p class="empty-title">Belum ada jadwal pekerjaan</p>
                        <p class="empty-subtitle text-muted">
                            Koordinator belum menginput jadwal pekerjaan untuk area {{ $selectedArea?->nama ?? 'ini' }} di bulan {{ \Carbon\Carbon::create($tahun, $bulan, 1)->translatedFormat('F Y') }}.
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modal Upload Bukti -->
<div class="modal modal-blur fade" id="uploadModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Bukti Pekerjaan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="uploadForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="jadwal_id" id="uploadJadwalId">
                <div class="modal-body">
                    <p class="mb-3">
                        <strong>Pekerjaan:</strong> <span id="uploadPekerjaanNama"></span>
                    </p>

                    <div class="mb-3">
                        <label class="form-label required">Foto Bukti</label>
                        <input type="file" name="foto_bukti" class="form-control" accept="image/*" capture="environment" required>
                        <small class="text-muted">Ambil foto atau pilih dari galeri. Max 5MB.</small>
                    </div>

                    <div id="previewContainer" class="mb-3" style="display: none;">
                        <label class="form-label">Preview</label>
                        <img id="previewImage" src="" class="img-fluid rounded" style="max-height: 200px;">
                    </div>

                    <div id="uploadError" class="alert alert-danger" style="display: none;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="ti ti-upload me-1"></i> Upload Bukti
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal View Bukti -->
<div class="modal modal-blur fade" id="viewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bukti Pekerjaan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="viewImage" src="" class="img-fluid rounded" style="max-height: 70vh;">
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let uploadModal, viewModal;

    document.addEventListener('DOMContentLoaded', function() {
        uploadModal = new bootstrap.Modal(document.getElementById('uploadModal'));
        viewModal = new bootstrap.Modal(document.getElementById('viewModal'));

        // Preview image before upload
        document.querySelector('input[name="foto_bukti"]').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('previewImage').src = e.target.result;
                    document.getElementById('previewContainer').style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });

        // Handle form submit
        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitBtn = document.getElementById('submitBtn');
            const errorDiv = document.getElementById('uploadError');

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Mengupload...';
            errorDiv.style.display = 'none';

            fetch('{{ route("lembar-kerja-cs.upload-bukti-ajax") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update table row
                    const jadwalId = document.getElementById('uploadJadwalId').value;
                    document.getElementById('status-' + jadwalId).innerHTML = '<span class="badge text-white bg-success"><i class="ti ti-check me-1"></i> Sudah</span>';
                    document.getElementById('waktu-' + jadwalId).textContent = data.bukti.waktu;
                    document.getElementById('uploader-' + jadwalId).textContent = data.bukti.pjlp_nama;

                    // Update action button
                    const row = document.getElementById('row-' + jadwalId);
                    const actionCell = row.querySelector('td:last-child');
                    actionCell.innerHTML = `<button type="button" class="btn btn-sm btn-outline-success" onclick="viewBukti(${jadwalId}, '${data.bukti.foto_url}')"><i class="ti ti-eye"></i> Lihat</button>`;

                    uploadModal.hide();

                    // Show success toast
                    alert('Bukti pekerjaan berhasil diupload!');
                } else {
                    errorDiv.textContent = data.message;
                    errorDiv.style.display = 'block';
                }
            })
            .catch(error => {
                errorDiv.textContent = 'Terjadi kesalahan. Silakan coba lagi.';
                errorDiv.style.display = 'block';
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="ti ti-upload me-1"></i> Upload Bukti';
            });
        });
    });

    function showUploadModal(jadwalId, pekerjaan) {
        document.getElementById('uploadJadwalId').value = jadwalId;
        document.getElementById('uploadPekerjaanNama').textContent = pekerjaan;
        document.getElementById('uploadForm').reset();
        document.getElementById('previewContainer').style.display = 'none';
        document.getElementById('uploadError').style.display = 'none';
        uploadModal.show();
    }

    function viewBukti(jadwalId, imageUrl) {
        document.getElementById('viewImage').src = imageUrl;
        viewModal.show();
    }
</script>
@endpush

@push('styles')
<style>
    .card.border-primary {
        border-width: 2px;
    }
</style>
@endpush
