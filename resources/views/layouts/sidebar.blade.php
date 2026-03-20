<aside class="navbar navbar-vertical navbar-expand-lg" data-bs-theme="dark">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu" aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <h1 class="navbar-brand navbar-brand-autodark">
            <a href="{{ route('dashboard') }}" class="d-flex align-items-center text-decoration-none">
                <img src="{{ asset('images/logo-icon.png') }}" alt="SiPJLP" height="36" class="me-2">
                <div>
                    <span class="text-white fw-bold">SiPJLP</span>
                    <small class="text-muted d-block" style="font-size: 0.65rem;">Sistem Informasi PJLP</small>
                </div>
            </a>
        </h1>
        <div class="navbar-nav flex-row d-lg-none">
            <div class="nav-item dropdown">
                <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Open user menu">
                    <span class="avatar avatar-sm" style="background-image: url(https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=206bc4&color=fff)"></span>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <a href="{{ route('profile.edit') }}" class="dropdown-item">Profil</a>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item">Logout</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="collapse navbar-collapse" id="sidebar-menu">
            <ul class="navbar-nav pt-lg-3">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <i class="ti ti-home"></i>
                        </span>
                        <span class="nav-link-title">Dashboard</span>
                    </a>
                </li>

                @can('pjlp.view-all')
                <!-- Data PJLP (Admin) -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('pjlp.*') ? 'active' : '' }}" href="{{ route('pjlp.index') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <i class="ti ti-users"></i>
                        </span>
                        <span class="nav-link-title">Data PJLP</span>
                    </a>
                </li>
                @endcan

                @can('pjlp.view-unit')
                <!-- Data PJLP (Koordinator) -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('pjlp.*') ? 'active' : '' }}" href="{{ route('pjlp.index') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <i class="ti ti-users"></i>
                        </span>
                        <span class="nav-link-title">Data PJLP Unit</span>
                    </a>
                </li>
                @endcan

                <!-- Absensi -->
                @canany(['absensi.view-self', 'absensi.view-unit', 'absensi.view-all'])
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('absensi.*') || request()->routeIs('tarik-absen.*') ? 'active' : '' }}" href="#navbar-absensi" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="false">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <i class="ti ti-fingerprint"></i>
                        </span>
                        <span class="nav-link-title">Absensi</span>
                    </a>
                    <div class="dropdown-menu {{ request()->routeIs('absensi.*') || request()->routeIs('tarik-absen.*') ? 'show' : '' }}">
                        <a class="dropdown-item {{ request()->routeIs('absensi.index') ? 'active' : '' }}" href="{{ route('absensi.index') }}">
                            <i class="ti ti-list me-2"></i> Data Absensi
                        </a>
                        @can('absensi.view-all')
                        <a class="dropdown-item {{ request()->routeIs('tarik-absen.*') ? 'active' : '' }}" href="{{ route('tarik-absen.index') }}">
                            <i class="ti ti-cloud-download me-2"></i> Tarik dari Mesin
                        </a>
                        @endcan
                    </div>
                </li>
                @endcanany

                <!-- Pengajuan Cuti -->
                @canany(['cuti.create', 'cuti.view-unit', 'cuti.view-all'])
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('cuti.*') ? 'active' : '' }}" href="{{ route('cuti.index') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <i class="ti ti-plane-departure"></i>
                        </span>
                        <span class="nav-link-title">Pengajuan Cuti</span>
                        @can('cuti.approve')
                        @php
                            $pendingCuti = \App\Models\Cuti::pending()->count();
                        @endphp
                        @if($pendingCuti > 0)
                        <span class="badge badge-sm bg-red text-red-fg ms-auto">{{ $pendingCuti }}</span>
                        @endif
                        @endcan
                    </a>
                </li>
                @endcanany

                <!-- Lembar Kerja -->
                @php
                    $currentUserId = auth()->id();
                    $userPjlp = \App\Models\Pjlp::where('user_id', $currentUserId)->first();
                    $userUnit = $userPjlp?->unit;
                    $isCleaningPjlp = $userPjlp && $userUnit === \App\Enums\UnitType::CLEANING;
                    $isSecurityPjlp = $userPjlp && $userUnit === \App\Enums\UnitType::SECURITY;
                    $isAdminOrKoordinator = auth()->user()->hasAnyRole(['admin', 'koordinator', 'manajemen']);
                @endphp

                {{-- Menu Lembar Kerja (Security) - HIDDEN/DISABLED
                @if($isSecurityPjlp || $isAdminOrKoordinator)
                @canany(['lembar-kerja.create', 'lembar-kerja.view-unit', 'lembar-kerja.view-all'])
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('lembar-kerja.*') && !request()->routeIs('lembar-kerja-cs.*') ? 'active' : '' }}" href="{{ route('lembar-kerja.index') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <i class="ti ti-clipboard-list"></i>
                        </span>
                        <span class="nav-link-title">Lembar Kerja</span>
                        @can('lembar-kerja.validate')
                        @php
                            $pendingLK = \App\Models\LembarKerja::pending()->count();
                        @endphp
                        @if($pendingLK > 0)
                        <span class="badge badge-sm bg-red text-red-fg ms-auto">{{ $pendingLK }}</span>
                        @endif
                        @endcan
                    </a>
                </li>
                @endcanany
                @endif
                --}}

                <!-- Lembar Kerja CS (Cleaning Service) - untuk semua PJLP dengan unit cleaning -->
                @if($isCleaningPjlp)
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('lembar-kerja-cs.*') ? 'active' : '' }}" href="{{ route('lembar-kerja-cs.index') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <i class="ti ti-spray"></i>
                        </span>
                        <span class="nav-link-title">Cleaning Service</span>
                    </a>
                </li>
                @endif

                <!-- Lembar Kerja CS (Cleaning Service) - untuk Admin/Koordinator -->
                @if($isAdminOrKoordinator)
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('lembar-kerja-cs.*') || request()->routeIs('jadwal-kerja-cs-bulanan.*') || request()->routeIs('jadwal-shift-cs.*') ? 'active' : '' }}" href="#navbar-lkcs" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="false">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <i class="ti ti-spray"></i>
                        </span>
                        <span class="nav-link-title">Cleaning Service</span>
                        @can('lembar-kerja.validate')
                        @php
                            $pendingLKCS = \App\Models\LembarKerjaCs::where('status', 'submitted')->count();
                        @endphp
                        @if($pendingLKCS > 0)
                        <span class="badge badge-sm bg-warning text-warning-fg ms-2">{{ $pendingLKCS }}</span>
                        @endif
                        @endcan
                    </a>
                    <div class="dropdown-menu {{ request()->routeIs('lembar-kerja-cs.*') || request()->routeIs('jadwal-kerja-cs-bulanan.*') || request()->routeIs('jadwal-shift-cs.*') || request()->routeIs('master-pekerjaan-cs.*') ? 'show' : '' }}">
                        {{-- Menu untuk Koordinator: Input Data --}}
                        @can('jadwal.manage')
                        <div class="dropdown-header">Input Data</div>
                        <a class="dropdown-item {{ request()->routeIs('jadwal-shift-cs.index') || request()->routeIs('jadwal-shift-cs.rekap') ? 'active' : '' }}" href="{{ route('jadwal-shift-cs.index') }}">
                            <i class="ti ti-calendar me-2"></i> Input Jadwal Shift
                        </a>
                        <a class="dropdown-item {{ request()->routeIs('jadwal-kerja-cs-bulanan.*') ? 'active' : '' }}" href="{{ route('jadwal-kerja-cs-bulanan.index') }}">
                            <i class="ti ti-clipboard-list me-2"></i> Input Pekerjaan Harian
                        </a>
                        <div class="dropdown-divider"></div>
                        <div class="dropdown-header">Master Data</div>
                        <a class="dropdown-item {{ request()->routeIs('master-pekerjaan-cs.*') ? 'active' : '' }}" href="{{ route('master-pekerjaan-cs.index') }}">
                            <i class="ti ti-list-details me-2"></i> Master Pekerjaan
                        </a>
                        <div class="dropdown-divider"></div>
                        @endcan

                        {{-- Menu untuk Koordinator: Lembar Kerja --}}
                        <div class="dropdown-header">Lembar Kerja</div>
                        <a class="dropdown-item {{ request()->routeIs('lembar-kerja-cs.index') ? 'active' : '' }}" href="{{ route('lembar-kerja-cs.index') }}">
                            <i class="ti ti-list me-2"></i> Lihat Lembar Kerja
                        </a>
                        @can('lembar-kerja.validate')
                        <a class="dropdown-item {{ request()->routeIs('lembar-kerja-cs.validasi-bukti-index') ? 'active' : '' }}" href="{{ route('lembar-kerja-cs.validasi-bukti-index') }}">
                            <i class="ti ti-checkbox me-2"></i> Validasi Bukti
                            @php
                                $pendingBuktiCs = \App\Models\BuktiPekerjaanCs::where('is_completed', true)
                                    ->where('is_validated', false)
                                    ->where('is_rejected', false)
                                    ->count();
                            @endphp
                            @if($pendingBuktiCs > 0)
                                <span class="badge bg-warning text-warning-fg ms-auto">{{ $pendingBuktiCs }}</span>
                            @endif
                        </a>
                        @endcan
                    </div>
                </li>
                @endif

                <!-- Laporan -->
                @can('laporan.view')
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('laporan.*') ? 'active' : '' }}" href="#navbar-laporan" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="false">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <i class="ti ti-report-analytics"></i>
                        </span>
                        <span class="nav-link-title">Laporan</span>
                    </a>
                    <div class="dropdown-menu {{ request()->routeIs('laporan.*') ? 'show' : '' }}">
                        <a class="dropdown-item" href="{{ route('laporan.absensi') }}">Rekap Absensi</a>
                        <a class="dropdown-item" href="{{ route('laporan.cuti') }}">Rekap Cuti</a>
                    </div>
                </li>
                @endcan

                <!-- Master Data -->
                @can('master.manage')
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('master.*') ? 'active' : '' }}" href="#navbar-master" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="false">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <i class="ti ti-database"></i>
                        </span>
                        <span class="nav-link-title">Master Data</span>
                    </a>
                    <div class="dropdown-menu {{ request()->routeIs('master.*') ? 'show' : '' }}">
                        <a class="dropdown-item" href="{{ route('master.shift.index') }}">Shift</a>
                        <a class="dropdown-item" href="{{ route('master.jenis-cuti.index') }}">Jenis Cuti</a>
                        <a class="dropdown-item" href="{{ route('master.lokasi.index') }}">Lokasi</a>
                        <a class="dropdown-item" href="{{ route('master.area-cs.index') }}">Area CS</a>
                    </div>
                </li>
                @endcan

                <!-- Audit Log -->
                @can('audit-log.view')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('audit-log.*') ? 'active' : '' }}" href="{{ route('audit-log.index') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <i class="ti ti-history"></i>
                        </span>
                        <span class="nav-link-title">Audit Log</span>
                    </a>
                </li>
                @endcan

                <li class="nav-item mt-auto">
                    <hr class="dropdown-divider my-2">
                </li>

                <!-- User Profile -->
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="false">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <span class="avatar avatar-xs" style="background-image: url(https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=206bc4&color=fff)"></span>
                        </span>
                        <span class="nav-link-title">{{ auth()->user()->name }}</span>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                            <i class="ti ti-user me-2"></i> Profil
                        </a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="ti ti-logout me-2"></i> Logout
                            </button>
                        </form>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</aside>
