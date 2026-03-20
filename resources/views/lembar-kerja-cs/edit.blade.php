@extends('layouts.app')

@section('title', 'Edit Lembar Kerja CS')

@push('styles')
<style>
    .checklist-item {
        transition: background-color 0.2s;
    }
    .checklist-item:hover {
        background-color: rgba(var(--tblr-primary-rgb), 0.05);
    }
    .checklist-item.completed {
        background-color: rgba(var(--tblr-success-rgb), 0.1);
    }
    .form-check-input:checked {
        background-color: var(--tblr-success);
        border-color: var(--tblr-success);
    }
    .aktivitas-nama {
        font-weight: 500;
    }
    .aktivitas-kategori {
        font-size: 0.75rem;
    }
    .foto-preview {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 4px;
        cursor: pointer;
    }
    .foto-placeholder {
        width: 80px;
        height: 80px;
        border: 2px dashed #ccc;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        background: #f8f9fa;
    }
    .foto-placeholder:hover {
        border-color: var(--tblr-primary);
        background: rgba(var(--tblr-primary-rgb), 0.05);
    }
</style>
@endpush

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <a href="{{ route('lembar-kerja-cs.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M5 12l6 6" /><path d="M5 12l6 -6" /></svg>
                    Kembali
                </a>
                <h2 class="page-title">Isi Lembar Kerja CS</h2>
                <div class="text-muted mt-1">
                    {{ $lembarKerja->area->nama }} | {{ $lembarKerja->tanggal->format('d F Y') }} | {{ $lembarKerja->shift->nama ?? '-' }}
                </div>
            </div>
            <div class="col-auto ms-auto">
                <div class="d-flex align-items-center">
                    <span class="me-3">Progress:</span>
                    <div class="progress" style="width: 150px; height: 20px;">
                        <div class="progress-bar bg-success" id="progressBar" style="width: {{ $lembarKerja->completion_percentage }}%">
                            <span id="progressText">{{ $lembarKerja->completion_percentage }}%</span>
                        </div>
                    </div>
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

        @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if($lembarKerja->isRejected())
        <div class="alert alert-warning">
            <h4 class="alert-title">Lembar Kerja Ditolak</h4>
            <div class="text-muted">
                {{ $lembarKerja->catatan_koordinator ?? 'Tidak ada catatan dari validator' }}
            </div>
            <div class="mt-2">
                <small>Ditolak oleh: {{ $lembarKerja->validator->name ?? '-' }} pada {{ $lembarKerja->validated_at?->format('d/m/Y H:i') }}</small>
            </div>
        </div>
        @endif

        <form action="{{ route('lembar-kerja-cs.update', $lembarKerja->id) }}" method="POST" id="formLembarKerja">
            @csrf
            @method('PUT')

            <!-- Info Card -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>PJLP:</strong><br>
                            {{ $lembarKerja->pjlp->nama ?? '-' }}
                        </div>
                        <div class="col-md-3">
                            <strong>Area:</strong><br>
                            <span class="badge bg-blue-lt">{{ $lembarKerja->area->kode }}</span>
                            {{ $lembarKerja->area->nama }}
                        </div>
                        <div class="col-md-3">
                            <strong>Tanggal:</strong><br>
                            {{ $lembarKerja->tanggal->format('d F Y') }}
                        </div>
                        <div class="col-md-3">
                            <strong>Shift:</strong><br>
                            <span class="badge bg-azure">{{ $lembarKerja->shift->nama ?? '-' }}</span>
                            @if($lembarKerja->shift)
                            <small class="d-block text-muted">{{ $lembarKerja->shift->jam_masuk }} - {{ $lembarKerja->shift->jam_keluar }}</small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Checklist Card -->
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Checklist Aktivitas</h3>
                    <div class="card-actions">
                        <span class="text-muted" id="completedCount">
                            {{ $lembarKerja->details->where('is_completed', true)->count() }} / {{ $lembarKerja->details->count() }} selesai
                        </span>
                    </div>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($lembarKerja->details as $index => $detail)
                    <div class="list-group-item checklist-item {{ $detail->is_completed ? 'completed' : '' }}" id="item-{{ $detail->id }}">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <label class="form-check form-check-single m-0">
                                    <input type="hidden" name="details[{{ $index }}][id]" value="{{ $detail->id }}">
                                    <input type="checkbox"
                                           class="form-check-input checklist-checkbox"
                                           name="details[{{ $index }}][is_completed]"
                                           value="1"
                                           data-detail-id="{{ $detail->id }}"
                                           {{ $detail->is_completed ? 'checked' : '' }}>
                                </label>
                            </div>
                            <div class="col">
                                <div class="aktivitas-nama {{ $detail->is_completed ? 'text-decoration-line-through text-muted' : '' }}">
                                    {{ $detail->aktivitas->nama ?? 'Aktivitas tidak ditemukan' }}
                                </div>
                                @if($detail->aktivitas)
                                <div class="aktivitas-kategori text-muted">
                                    <span class="badge bg-azure-lt">{{ ucfirst($detail->aktivitas->kategori ?? 'umum') }}</span>
                                    @if($detail->aktivitas->frekuensi)
                                    <span class="ms-1">{{ $detail->aktivitas->frekuensi }}</span>
                                    @endif
                                </div>
                                @endif
                            </div>
                            <div class="col-auto">
                                @if($detail->waktu_selesai)
                                <small class="text-muted">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-clock" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" /><path d="M12 7v5l3 3" /></svg>
                                    {{ $detail->waktu_selesai->format('H:i') }}
                                </small>
                                @endif
                            </div>
                            <div class="col-auto">
                                <!-- Foto Before -->
                                <div class="d-inline-block me-2">
                                    @if($detail->foto_before)
                                    <img src="{{ Storage::url($detail->foto_before) }}"
                                         class="foto-preview"
                                         alt="Before"
                                         title="Foto Before"
                                         data-bs-toggle="modal"
                                         data-bs-target="#modalFoto"
                                         data-src="{{ Storage::url($detail->foto_before) }}">
                                    @else
                                    <label class="foto-placeholder" title="Upload Foto Before">
                                        <input type="file"
                                               class="d-none foto-input"
                                               accept="image/*"
                                               data-detail-id="{{ $detail->id }}"
                                               data-tipe="before">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon text-muted" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 8h.01" /><path d="M3 6a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v12a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3v-12z" /><path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l5 5" /><path d="M14 14l1 -1c.928 -.893 2.072 -.893 3 0l3 3" /></svg>
                                    </label>
                                    @endif
                                    <small class="d-block text-center text-muted" style="font-size: 10px;">Before</small>
                                </div>
                                <!-- Foto After -->
                                <div class="d-inline-block">
                                    @if($detail->foto_after)
                                    <img src="{{ Storage::url($detail->foto_after) }}"
                                         class="foto-preview"
                                         alt="After"
                                         title="Foto After"
                                         data-bs-toggle="modal"
                                         data-bs-target="#modalFoto"
                                         data-src="{{ Storage::url($detail->foto_after) }}">
                                    @else
                                    <label class="foto-placeholder" title="Upload Foto After">
                                        <input type="file"
                                               class="d-none foto-input"
                                               accept="image/*"
                                               data-detail-id="{{ $detail->id }}"
                                               data-tipe="after">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon text-muted" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 8h.01" /><path d="M3 6a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v12a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3v-12z" /><path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l5 5" /><path d="M14 14l1 -1c.928 -.893 2.072 -.893 3 0l3 3" /></svg>
                                    </label>
                                    @endif
                                    <small class="d-block text-center text-muted" style="font-size: 10px;">After</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <input type="text"
                                       class="form-control form-control-sm"
                                       name="details[{{ $index }}][catatan]"
                                       placeholder="Catatan..."
                                       value="{{ $detail->catatan }}">
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="list-group-item text-center text-muted">
                        Tidak ada aktivitas terjadwal untuk hari dan shift ini
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Catatan Umum -->
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Catatan Umum</h3>
                </div>
                <div class="card-body">
                    <textarea name="catatan_pjlp" class="form-control" rows="3" placeholder="Catatan tambahan untuk lembar kerja ini...">{{ $lembarKerja->catatan_pjlp }}</textarea>
                </div>
            </div>

            <!-- Actions -->
            <div class="card">
                <div class="card-footer d-flex justify-content-between">
                    <div>
                        <button type="submit" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2" /><path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M14 4l0 4l-6 0l0 -4" /></svg>
                            Simpan Draft
                        </button>
                    </div>
                    <div>
                        @if($lembarKerja->canSubmit() && $lembarKerja->details->count() > 0)
                        <a href="{{ route('lembar-kerja-cs.submit', $lembarKerja->id) }}"
                           class="btn btn-success"
                           onclick="return confirm('Apakah Anda yakin ingin submit lembar kerja ini untuk validasi? Pastikan semua aktivitas sudah selesai.')">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                            Submit untuk Validasi
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Foto -->
<div class="modal fade" id="modalFoto" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body p-0">
                <img src="" id="modalFotoImg" class="w-100">
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle checkbox change with auto-save
    document.querySelectorAll('.checklist-checkbox').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const detailId = this.dataset.detailId;
            const isCompleted = this.checked;
            const item = document.getElementById('item-' + detailId);
            const namaEl = item.querySelector('.aktivitas-nama');

            // Visual feedback
            if (isCompleted) {
                item.classList.add('completed');
                namaEl.classList.add('text-decoration-line-through', 'text-muted');
            } else {
                item.classList.remove('completed');
                namaEl.classList.remove('text-decoration-line-through', 'text-muted');
            }

            // Auto-save via AJAX
            fetch(`/lembar-kerja-cs/detail/${detailId}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ is_completed: isCompleted })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateProgress(data.completion_percentage);
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });

    // Handle foto upload
    document.querySelectorAll('.foto-input').forEach(function(input) {
        input.addEventListener('change', function() {
            const detailId = this.dataset.detailId;
            const tipe = this.dataset.tipe;
            const file = this.files[0];

            if (!file) return;

            const formData = new FormData();
            formData.append('foto', file);
            formData.append('tipe', tipe);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            // Show loading
            const placeholder = this.parentElement;
            placeholder.innerHTML = '<div class="spinner-border spinner-border-sm" role="status"></div>';

            fetch(`/lembar-kerja-cs/detail/${detailId}/foto`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Replace placeholder with image
                    const img = document.createElement('img');
                    img.src = data.path;
                    img.className = 'foto-preview';
                    img.setAttribute('data-bs-toggle', 'modal');
                    img.setAttribute('data-bs-target', '#modalFoto');
                    img.setAttribute('data-src', data.path);
                    placeholder.innerHTML = '';
                    placeholder.appendChild(img);
                    placeholder.classList.remove('foto-placeholder');
                } else {
                    alert('Gagal upload foto');
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal upload foto');
                location.reload();
            });
        });
    });

    // Handle foto preview modal
    document.querySelectorAll('.foto-preview').forEach(function(img) {
        img.addEventListener('click', function() {
            document.getElementById('modalFotoImg').src = this.dataset.src;
        });
    });

    function updateProgress(percentage) {
        const progressBar = document.getElementById('progressBar');
        const progressText = document.getElementById('progressText');
        progressBar.style.width = percentage + '%';
        progressText.textContent = percentage + '%';

        // Update completed count
        const checkboxes = document.querySelectorAll('.checklist-checkbox');
        const completed = document.querySelectorAll('.checklist-checkbox:checked').length;
        document.getElementById('completedCount').textContent = `${completed} / ${checkboxes.length} selesai`;
    }
});
</script>
@endpush
