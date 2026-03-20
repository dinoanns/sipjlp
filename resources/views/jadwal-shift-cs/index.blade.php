@extends('layouts.app')

@section('title', 'Jadwal Shift CS')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Jadwal Shift CS
                </h2>
                <div class="text-muted mt-1">
                    Input jadwal shift per PJLP per hari (dikelola oleh Koordinator)
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

        <!-- Filter -->
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('jadwal-shift-cs.index') }}" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Bulan</label>
                        <select name="bulan" class="form-select" onchange="this.form.submit()">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tahun</label>
                        <select name="tahun" class="form-select" onchange="this.form.submit()">
                            @for($y = now()->year - 1; $y <= now()->year + 1; $y++)
                                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="10" cy="10" r="7" /><line x1="21" y1="21" x2="15" y2="15" /></svg>
                            Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Keterangan -->
        <div class="card mb-3">
            <div class="card-body py-2">
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <span class="fw-bold me-2">Keterangan:</span>
                    <span class="badge text-white" style="background-color: #f59f00;">L = Libur</span>
                    <span class="badge text-white" style="background-color: #d63939;">R = Hari Raya</span>
                    @foreach($shifts as $shift)
                        @php
                            $shiftBgColor = match(strtolower($shift->nama)) {
                                'pagi' => '#cce5ff',
                                'siang' => '#fff3cd',
                                'malam' => '#f8c8dc',
                                default => '#667382',
                            };
                            $shiftTextColor = match(strtolower($shift->nama)) {
                                'pagi' => '#004085',
                                'siang' => '#856404',
                                'malam' => '#721c47',
                                default => '#fff',
                            };
                        @endphp
                        <span class="badge" style="background-color: {{ $shiftBgColor }}; color: {{ $shiftTextColor }};">{{ strtoupper($shift->nama) }}</span>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Tabel Jadwal -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="5" width="16" height="16" rx="2" /><line x1="16" y1="3" x2="16" y2="7" /><line x1="8" y1="3" x2="8" y2="7" /><line x1="4" y1="11" x2="20" y2="11" /><rect x="8" y="15" width="2" height="2" /></svg>
                    Jadwal Shift CS - {{ \Carbon\Carbon::create($tahun, $bulan, 1)->translatedFormat('F Y') }}
                </h3>
            </div>
            <div class="card-body p-0">
                @if($pjlps->isEmpty())
                    <div class="empty">
                        <div class="empty-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><line x1="9" y1="10" x2="9.01" y2="10" /><line x1="15" y1="10" x2="15.01" y2="10" /><path d="M9.5 15.25a3.5 3.5 0 0 1 5 0" /></svg>
                        </div>
                        <p class="empty-title">Tidak ada PJLP</p>
                        <p class="empty-subtitle text-muted">
                            Belum ada PJLP Cleaning Service yang aktif.
                        </p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-vcenter mb-0 table-sm" id="jadwalTable">
                            <thead class="sticky-top bg-white">
                                <tr>
                                    <th class="text-center" style="width: 40px; position: sticky; left: 0; background: #fff; z-index: 2;">No</th>
                                    <th style="min-width: 150px; position: sticky; left: 40px; background: #fff; z-index: 2;">Nama Pegawai</th>
                                    @foreach($dates as $dateInfo)
                                        <th class="text-center @if($dateInfo['isToday']) bg-info text-white @elseif($dateInfo['isSunday']) bg-danger text-white @elseif($dateInfo['isWeekend']) bg-warning-lt @endif"
                                            style="min-width: 90px;">
                                            {{ $dateInfo['day'] }} {{ $dateInfo['date']->translatedFormat('M') }}<br>
                                            <small>({{ $dateInfo['dayName'] }})</small>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pjlps as $index => $pjlp)
                                    <tr>
                                        <td class="text-center" style="position: sticky; left: 0; background: #fff;">{{ $index + 1 }}</td>
                                        <td style="position: sticky; left: 40px; background: #fff;">
                                            <div class="fw-bold">{{ $pjlp->nama }}</div>
                                            <small class="text-muted">{{ $pjlp->nip ?? '-' }}</small>
                                        </td>
                                        @foreach($dates as $dateInfo)
                                            @php
                                                $key = $pjlp->id . '_' . $dateInfo['date']->format('Y-m-d');
                                                $jadwal = $jadwals->get($key)?->first();
                                            @endphp
                                            <td class="text-center p-1 @if($dateInfo['isSunday']) bg-danger-lt @elseif($dateInfo['isWeekend']) bg-warning-lt @endif"
                                                data-pjlp-id="{{ $pjlp->id }}"
                                                data-tanggal="{{ $dateInfo['date']->format('Y-m-d') }}">
                                                <div class="jadwal-cell" onclick="openShiftModal({{ $pjlp->id }}, '{{ $dateInfo['date']->format('Y-m-d') }}', '{{ $pjlp->nama }}', '{{ $dateInfo['date']->translatedFormat('d M Y') }}')" style="cursor: pointer;">
                                                    @if($jadwal)
                                                        @php
                                                            $badgeTextColor = match($jadwal->display_color_hex) {
                                                                '#cce5ff' => '#004085',
                                                                '#fff3cd' => '#856404',
                                                                '#f8c8dc' => '#721c47',
                                                                default => '#fff',
                                                            };
                                                        @endphp
                                                        <span class="badge jadwal-badge" style="background-color: {{ $jadwal->display_color_hex }}; color: {{ $badgeTextColor }};" id="badge-{{ $pjlp->id }}-{{ $dateInfo['date']->format('Y-m-d') }}">
                                                            {{ $jadwal->display_text }}
                                                        </span>
                                                    @else
                                                        <span class="badge bg-light text-muted jadwal-badge" id="badge-{{ $pjlp->id }}-{{ $dateInfo['date']->format('Y-m-d') }}">-</span>
                                                    @endif
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Pilih Shift -->
<div class="modal modal-blur fade" id="shiftModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pilih Shift</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-2">
                    <strong id="modalPjlpName"></strong><br>
                    <small class="text-muted" id="modalTanggal"></small>
                </p>

                <input type="hidden" id="modalPjlpId">
                <input type="hidden" id="modalTanggalValue">

                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select class="form-select" id="modalStatus" onchange="toggleShiftSelect()">
                        <option value="normal">Kerja (Pilih Shift)</option>
                        <option value="libur">Libur (L)</option>
                        <option value="libur_hari_raya">Hari Raya (R)</option>
                        <option value="cuti">Cuti</option>
                        <option value="izin">Izin</option>
                        <option value="sakit">Sakit</option>
                        <option value="alpha">Alpha</option>
                    </select>
                </div>

                <div class="mb-3" id="shiftSelectContainer">
                    <label class="form-label">Shift</label>
                    <div class="d-grid gap-2">
                        @foreach($shifts as $shift)
                            @php
                                $shiftBgColor = match(strtolower($shift->nama)) {
                                    'pagi' => '#cce5ff',
                                    'siang' => '#fff3cd',
                                    'malam' => '#f8c8dc',
                                    default => '#667382',
                                };
                                $shiftTextColor = match(strtolower($shift->nama)) {
                                    'pagi' => '#004085',
                                    'siang' => '#856404',
                                    'malam' => '#721c47',
                                    default => '#fff',
                                };
                            @endphp
                            <button type="button" class="btn shift-btn" style="background-color: {{ $shiftBgColor }}; color: {{ $shiftTextColor }}; border-color: {{ $shiftBgColor }};" data-shift-id="{{ $shift->id }}" data-shift-name="{{ $shift->nama }}" data-shift-bg="{{ $shiftBgColor }}" data-shift-text="{{ $shiftTextColor }}">
                                {{ strtoupper($shift->nama) }}
                                <small>({{ $shift->jam_masuk }} - {{ $shift->jam_keluar }})</small>
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="mb-3" id="nonKerjaContainer" style="display: none;">
                    <button type="button" class="btn btn-secondary w-100" id="saveNonKerjaBtn">
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let currentModal = null;

    function openShiftModal(pjlpId, tanggal, pjlpName, tanggalDisplay) {
        document.getElementById('modalPjlpId').value = pjlpId;
        document.getElementById('modalTanggalValue').value = tanggal;
        document.getElementById('modalPjlpName').textContent = pjlpName;
        document.getElementById('modalTanggal').textContent = tanggalDisplay;
        document.getElementById('modalStatus').value = 'normal';
        toggleShiftSelect();

        currentModal = new bootstrap.Modal(document.getElementById('shiftModal'));
        currentModal.show();
    }

    function toggleShiftSelect() {
        const status = document.getElementById('modalStatus').value;
        const shiftContainer = document.getElementById('shiftSelectContainer');
        const nonKerjaContainer = document.getElementById('nonKerjaContainer');

        if (status === 'normal') {
            shiftContainer.style.display = 'block';
            nonKerjaContainer.style.display = 'none';
        } else {
            shiftContainer.style.display = 'none';
            nonKerjaContainer.style.display = 'block';
        }
    }

    // Handle shift button click
    document.querySelectorAll('.shift-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const shiftId = this.dataset.shiftId;
            const shiftName = this.dataset.shiftName;
            const shiftBg = this.dataset.shiftBg;
            const shiftText = this.dataset.shiftText;
            saveJadwal(shiftId, 'normal', shiftName.toUpperCase(), shiftBg, shiftText);
        });
    });

    // Handle non-kerja save
    document.getElementById('saveNonKerjaBtn').addEventListener('click', function() {
        const status = document.getElementById('modalStatus').value;
        let displayText = '';
        let bgColor = '';
        let textColor = '#fff';

        switch(status) {
            case 'libur':
                displayText = 'LIBUR';
                bgColor = '#f59f00';
                break;
            case 'libur_hari_raya':
                displayText = 'LIBUR';
                bgColor = '#d63939';
                break;
            case 'cuti':
                displayText = 'C';
                bgColor = '#4299e1';
                break;
            case 'izin':
                displayText = 'I';
                bgColor = '#667382';
                break;
            case 'sakit':
                displayText = 'S';
                bgColor = '#1d273b';
                break;
            case 'alpha':
                displayText = 'A';
                bgColor = '#d63939';
                break;
        }

        saveJadwal(null, status, displayText, bgColor, textColor);
    });

    function saveJadwal(shiftId, status, displayText, bgColor, textColor = '#fff') {
        const pjlpId = document.getElementById('modalPjlpId').value;
        const tanggal = document.getElementById('modalTanggalValue').value;

        fetch('{{ route("jadwal-shift-cs.update") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                pjlp_id: pjlpId,
                tanggal: tanggal,
                shift_id: shiftId,
                status: status
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update badge di tabel
                const badge = document.getElementById(`badge-${pjlpId}-${tanggal}`);
                if (badge) {
                    badge.className = 'badge jadwal-badge';
                    badge.style.backgroundColor = data.display_color_hex;
                    // Set text color based on shift
                    const shiftTextColors = {
                        '#cce5ff': '#004085',
                        '#fff3cd': '#856404',
                        '#f8c8dc': '#721c47'
                    };
                    badge.style.color = shiftTextColors[data.display_color_hex] || '#fff';
                    badge.textContent = data.display_text;
                }

                // Close modal
                if (currentModal) {
                    currentModal.hide();
                }
            } else {
                alert('Gagal menyimpan jadwal');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan');
        });
    }
</script>
@endpush

@push('styles')
<style>
    .jadwal-badge {
        font-size: 0.7rem;
        padding: 0.35em 0.5em;
        min-width: 50px;
        display: inline-block;
    }
    .jadwal-cell:hover {
        background-color: #f0f0f0;
        border-radius: 4px;
    }
    #jadwalTable th, #jadwalTable td {
        vertical-align: middle;
    }
    .table-responsive {
        max-height: 70vh;
        overflow: auto;
    }
    thead.sticky-top th {
        position: sticky;
        top: 0;
        z-index: 1;
    }
</style>
@endpush
