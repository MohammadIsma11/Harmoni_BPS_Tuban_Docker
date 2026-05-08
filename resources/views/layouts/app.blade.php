<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Harmoni | BPS Kabupaten Tuban</title>
    
    {{-- Assets --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('img/logo_harmoni.png') }}">

    {{-- Manual Assets --}}
    <link rel="stylesheet" href="{{ asset('css/layouts/app-layout.css') }}">
    
    {{-- Axios CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    @stack('styles')
</head>
<body>

<script>
    // Immediate sidebar state check to prevent layout flicker
    (function() {
        const isHidden = localStorage.getItem('sidebarHidden') === 'true';
        if (isHidden && window.innerWidth > 992) {
            document.documentElement.classList.add('sidebar-hidden');
        }
    })();

    // Global toggle function (Vanilla JS)
    window.toggleSidebar = function() {
        const root = document.documentElement;
        const sidebar = document.getElementById('sidebar');
        if (window.innerWidth > 992) {
            root.classList.toggle('sidebar-hidden');
            localStorage.setItem('sidebarHidden', root.classList.contains('sidebar-hidden'));
        } else {
            if (sidebar) sidebar.classList.toggle('active');
        }
    };
</script>

<div class="progress-bar-container">
    <div class="progress-bar-fill" id="progress-bar"></div>
</div>

<div class="sidebar shadow-sm" id="sidebar">
    <div class="logo-container d-flex justify-content-between align-items-center">
        <div class="flex-grow-1">
            <a href="{{ route('dashboard') }}">
                <img src="{{ asset('img/logo_harmoni.png') }}" alt="Logo Harmoni" class="sidebar-logo-img">
            </a>
            <h6 class="logo-text mb-0 text-primary">Harmoni <span class="text-dark" style="font-weight: 400;">BPS</span></h6>
            <div class="small text-muted fw-bold" style="font-size: 0.55rem; letter-spacing: 1.2px;">KABUPATEN TUBAN</div>
        </div>
        <button class="btn d-lg-none text-muted" onclick="document.getElementById('sidebar').classList.remove('active')">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <nav class="mt-2 text-dark">
        @php
            $role = Auth::user()->role;
            $mode = session('dashboard_mode', 'harmoni');
            
            // Mitra selalu di mode honor
            if ($role === 'Mitra') {
                $mode = 'honor';
            }
        @endphp

        {{-- SWITCH MODULE BUTTON (Hanya untuk Staff) --}}
        @if($role !== 'Mitra' && $role !== 'Admin')
            <div class="px-3 mb-3">
                <form action="{{ route('module.switch') }}" method="POST">
                    @csrf
                    @if($mode === 'harmoni')
                        <input type="hidden" name="mode" value="honor">
                        <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 shadow-sm animate-up">
                            <i class="fas fa-exchange-alt me-2"></i> <span>Modulo Honor</span>
                        </button>
                    @else
                        <input type="hidden" name="mode" value="harmoni">
                        <button type="submit" class="btn btn-outline-primary w-100 rounded-pill py-2 animate-up">
                            <i class="fas fa-home me-2"></i> <span>Kembali ke Harmoni</span>
                        </button>
                    @endif
                </form>
            </div>
            <div class="menu-divider"></div>
        @endif

        {{-- =============== DASHBOARD MODE: HARMONI =============== --}}
        @if($mode === 'harmoni')
            {{-- SECTION: UTAMA --}}
            @if($role != 'Admin')
                <div class="menu-divider mt-0">Menu Utama</div>
                <a href="{{ route('dashboard') }}" class="nav-link">
                    <i class="fas fa-th-large me-2"></i> <span>Dashboard</span>
                </a>
                <a href="{{ route('monitoring.index') }}" class="nav-link">
                    <i class="fas fa-calendar-check me-2"></i> <span>Timeline Agenda</span>
                </a>
                <a href="{{ route('tematik.index') }}" class="nav-link {{ Route::is('tematik.*') ? 'active' : '' }}">
                    <i class="fas fa-map-marked-alt me-2 text-success"></i> <span>Monitoring Tematik</span>
                </a>
            @endif

            {{-- SECTION: PENUGASAN --}}
            @if($role == 'Kepala' || $role == 'Katim')
                <div class="menu-divider">Perencanaan</div>
                <a href="{{ route('assignment.index') }}" class="nav-link">
                    <i class="fas fa-clipboard-list me-2"></i> <span>Assignment</span>
                </a>

                <a href="{{ route('assignment.approvals.index') }}" class="nav-link">
                    <i class="fas fa-file-signature me-2"></i> 
                    <span>Persetujuan SPT</span>
                    
                    @php
                        $notifApproval = \App\Models\Agenda::where('mode_surat', 'generate')
                            ->where('status_approval', 'Pending')
                            ->where(function($q) {
                                $q->where('approver_id', Auth::id())
                                ->orWhere('reviewer_id', Auth::id());
                            });

                        if ($role === 'Kepala') {
                            $notifApproval->whereNotNull('reviewed_at');
                        } elseif ($role === 'Katim') {
                            $notifApproval->whereNull('reviewed_at');
                        }

                        $countNotif = $notifApproval->distinct('title')->count('title');
                    @endphp

                    @if($countNotif > 0)
                        <span class="badge bg-danger rounded-pill badge-notif">{{ $countNotif }}</span>
                    @endif
                </a>
            @endif

            {{-- SECTION: USER MANAGEMENT --}}
            @if($role == 'Admin' || $role == 'Kepala')
                <div class="menu-divider">Pengaturan & Master</div>
                <a href="{{ route('manajemen.anggota') }}" class="nav-link">
                    <i class="fas fa-users-cog me-2"></i> <span>Manajemen User</span>
                </a>
                <a href="{{ route('manajemen.mitra.index') }}" class="nav-link">
                    <i class="fas fa-id-card me-2"></i> <span>Master Mitra</span>
                </a>
                <a href="{{ route('manajemen.kegiatan.index') }}" class="nav-link">
                    <i class="fas fa-tasks me-2"></i> <span>Master Kegiatan</span>
                </a>
            @endif



            {{-- SECTION: ABSENSI (KHUSUS SUBBAGIAN UMUM) --}}
            @if(Auth::user()->team && Auth::user()->team->nama_tim === 'Subbagian Umum')
                <div class="menu-divider">Administrasi</div>
                <a href="{{ route('absensi.index') }}" class="nav-link">
                    <i class="fas fa-user-check me-2"></i> <span>Gatekeeper Absensi</span>
                </a>
            @endif

            {{-- SECTION: PELAKSANAAN --}}
            @if($role == 'Pegawai')
                <div class="menu-divider">Pelaksanaan</div>
                
                {{-- Tugas Lapangan --}}
                <button class="nav-link collapsed" 
                        data-bs-toggle="collapse" data-bs-target="#menuLapangan">
                    <i class="fas fa-briefcase me-2"></i> 
                    <span>Tugas Lapangan</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </button>
                <div class="collapse" id="menuLapangan">
                    <ul class="submenu">
                        <li>
                            <a href="{{ route('task.index') }}" class="nav-link small">
                                <i class="fas fa-tasks me-2"></i> 
                                <span>Daftar Tugas</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('history.index') }}" class="nav-link small">
                                <i class="fas fa-history me-2"></i> Riwayat Laporan
                            </a>
                        </li>
                    </ul>
                </div>

                {{-- Agenda Kegiatan --}}
                <button class="nav-link collapsed" 
                        data-bs-toggle="collapse" data-bs-target="#menuRapat">
                    <i class="fas fa-handshake me-2"></i> 
                    <span>Kegiatan Dinas</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </button>
                <div class="collapse" id="menuRapat">
                    <ul class="submenu">
                        <li>
                            <a href="{{ route('meeting.index') }}" class="nav-link small">
                                <i class="fas fa-calendar-day me-2"></i> 
                                <span>Jadwal Kegiatan</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('meeting.history') }}" class="nav-link small">
                                <i class="fas fa-file-archive me-2"></i> Riwayat & Notulensi
                            </a>
                        </li>
                    </ul>
                </div>
            @endif

            {{-- Menu Akses Super --}}
            @if($role == 'Kepala' || $role == 'Katim' || Auth::user()->has_super_access == 1)
                <a href="{{ route('super.access.index') }}" class="nav-link">
                    <i class="fas fa-shield-alt me-2 text-danger"></i>
                    <span>Akses Super</span>
                </a>
            @endif

        {{-- =============== DASHBOARD MODE: HONOR =============== --}}
        @else
            <div class="menu-divider mt-0">Modul Honorarium</div>
            <a href="{{ route('dashboard') }}" class="nav-link">
                <i class="fas fa-tachometer-alt me-2"></i> <span>Dashboard Honor</span>
            </a>

            @if($role == 'Kepala' || $role == 'Katim')
                <div class="menu-divider">Penugasan</div>
                <a href="{{ route('penugasan-mitra.index') }}" class="nav-link">
                    <i class="fas fa-user-plus me-2"></i> <span>Manajemen Penugasan</span>
                </a>
            @endif

            @if($role == 'Kepala' || $role == 'Admin')
                <div class="menu-divider">Master Data</div>
                <a href="{{ route('manajemen.mitra.index') }}" class="nav-link">
                    <i class="fas fa-id-card me-2"></i> <span>Master Mitra</span>
                </a>
                <a href="{{ route('manajemen.kegiatan.index') }}" class="nav-link">
                    <i class="fas fa-tasks me-2"></i> <span>Master Kegiatan</span>
                </a>
            @endif

            @if(Auth::user()->team && Auth::user()->team->nama_tim === 'Subbagian Umum')
                <div class="menu-divider">Administrasi</div>
                <a href="{{ route('honorarium.verifikasi') }}" class="nav-link">
                    <i class="fas fa-file-invoice-dollar me-2"></i> <span>Verifikasi Dokumen</span>
                </a>
                <a href="{{ route('honorarium.pembayaran') }}" class="nav-link">
                    <i class="fas fa-hand-holding-usd me-2"></i> <span>Pembayaran Honor</span>
                </a>
            @endif

            {{-- Rekap Honor (Akses: Katim, Kepala, Admin, Subbagian Umum) --}}
            @php
                $isSubbagUmum = Auth::user()->team && Auth::user()->team->nama_tim === 'Subbagian Umum';
            @endphp
            @if($role == 'Kepala' || $role == 'Katim' || $role == 'Admin' || $isSubbagUmum)
                <div class="menu-divider">Laporan & Rekap</div>
                <a href="{{ route('rekap-honor.index') }}" class="nav-link {{ Route::is('rekap-honor.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-pie me-2"></i> <span>Rekap Honor Dasar</span>
                </a>
            @endif
        @endif

        {{-- SECTION: SMART ECOSYSTEM --}}
        <div class="menu-divider">Smart Ecosystem</div>
        <a href="http://localhost:8000/sso/login" class="nav-link" target="_blank">
            <i class="fas fa-ticket-alt me-2 text-primary"></i> <span>SEpintu Ticket</span>
        </a>
        <a href="http://localhost:8001" class="nav-link" target="_blank">
            <i class="fas fa-book-reader me-2 text-success"></i> <span>SEpintu KMS</span>
        </a>

        {{-- SECTION: SYSTEM --}}
        <div class="menu-divider">Sistem</div>
        <a href="{{ route('panduan.index') }}" class="nav-link">
            <i class="fas fa-book me-2"></i> <span>Panduan Pengguna</span>
        </a>

        <a href="#" class="nav-link text-danger mt-3" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fas fa-power-off me-2"></i> <span>Keluar</span>
        </a>
    </nav>
</div>

<div class="main-content">
    <nav class="top-navbar d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <button class="btn btn-light border-0 me-3 shadow-sm" id="global-sidebar-toggle" onclick="if(typeof toggleSidebar === 'function'){ toggleSidebar() }">
                <i class="fas fa-bars"></i>
            </button>
            <div class="d-none d-md-block text-dark">
                <h6 class="fw-bold mb-0">Harmoni BPS Tuban</h6>
                <small class="text-muted" style="font-size: 0.7rem;">
                    @if($mode === 'honor') Modul Manajemen Honorarium Mitra @else Sistem Manajemen Agenda & Rapat @endif
                </small>
            </div>
        </div>
        
        <div class="d-flex align-items-center">
            {{-- User Profile --}}
            <div class="user-profile-badge shadow-sm">
                <div class="dropdown">
                    <div class="d-flex align-items-center" data-bs-toggle="dropdown" style="cursor: pointer;">
                        <div class="avatar-box shadow-sm {{ $role === 'Mitra' ? 'bg-success text-white' : '' }}">
                            {{ strtoupper(substr(Auth::user()->nama_lengkap ?? 'U', 0, 1)) }}
                        </div>
                        <div class="text-start d-none d-sm-block text-dark pe-2">
                            <div class="fw-bold lh-1 mb-1" style="font-size: 0.8rem;">{{ Auth::user()->nama_lengkap }}</div>
                            <div class="badge {{ $role == 'Pegawai' ? 'bg-secondary' : ($role == 'Katim' ? 'bg-info' : ($role == 'Kepala' ? 'bg-dark' : ($role == 'Mitra' ? 'bg-success' : ($role == 'Admin' ? 'bg-danger' : 'bg-primary')))) }}" style="font-size: 0.55rem;">
                                {{ $role }}
                            </div>
                        </div>
                        <i class="fas fa-chevron-down text-muted" style="font-size: 0.7rem;"></i>
                    </div>

                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-4 mt-3 p-2" style="min-width: 200px;">
                        <li class="px-3 py-2 small border-bottom mb-2">
                            <span class="text-muted d-block" style="font-size: 0.6rem;">Username:</span>
                            <span class="fw-bold text-primary">@ {{ Auth::user()->username }}</span>
                        </li>
                        @if($role !== 'Mitra')
                        <li>
                            <a class="dropdown-item py-2 rounded-3 d-flex align-items-center" href="{{ route('profile.edit') }}">
                                <i class="fas fa-user-cog me-2 text-primary"></i>
                                <span class="small fw-bold">Pengaturan Profil</span>
                            </a>
                        </li>
                        @endif
                        <li><hr class="dropdown-divider opacity-50"></li>
                        <li>
                            <a class="dropdown-item py-2 rounded-3 text-danger d-flex align-items-center" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-power-off me-2"></i>
                                <span class="small fw-bold">Keluar</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Role Switcher --}}
            @if($role !== 'Mitra')
            @php $availableRoles = Auth::user()->getAvailableRoles(); @endphp
            @if(count($availableRoles) > 1)
            <div class="dropdown ms-3">
                <a href="#" class="role-switch-btn-standalone" data-bs-toggle="dropdown" aria-expanded="false" title="Ganti Peran">
                    <div class="role-icon-box shadow-sm {{ $role == 'Pegawai' ? 'bg-light text-muted' : 'bg-primary text-white' }}">
                        <i class="fas fa-user-tag"></i>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-4 mt-3 p-2" style="min-width: 200px;">
                    <li class="px-3 py-2 small border-bottom mb-2">
                        <span class="text-muted d-block" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase;">Akses Cepat Peran</span>
                    </li>
                    @foreach($availableRoles as $r)
                    <li>
                        <form action="{{ route('profile.switch-role') }}" method="POST">
                            @csrf
                            <input type="hidden" name="role" value="{{ $r['value'] }}">
                            <button type="submit" class="dropdown-item py-2 rounded-3 d-flex align-items-center {{ $role == $r['value'] ? 'active bg-primary text-white' : '' }}">
                                <div class="icon-box-mini me-2 {{ $role == $r['value'] ? 'bg-white text-primary' : 'bg-light text-primary' }}">
                                    <i class="fas {{ $r['icon'] }} font-size-sm"></i>
                                </div>
                                <span class="small fw-bold">{{ $r['label'] }}</span>
                            </button>
                        </form>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif
            @endif
    </nav>

    <div class="content-padding animate-fade-in">
        @yield('content')
    </div>
</div>

<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>

{{-- Scripts --}}
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="{{ asset('js/layouts/app-layout.js') }}"></script>
<script src="{{ asset('js/assignment.js') }}?v={{ time() }}"></script>

<script>
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: "{{ session('success') }}",
            confirmButtonColor: '#0058a8',
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: "{{ session('error') }}",
            confirmButtonColor: '#0058a8',
        });
    @endif

    @if(session('warning'))
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: "{{ session('warning') }}",
            confirmButtonColor: '#0058a8',
        });
    @endif

    @if(session('info'))
        Swal.fire({
            icon: 'info',
            title: 'Informasi',
            text: "{{ session('info') }}",
            confirmButtonColor: '#0058a8',
        });
    @endif
    @if($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Validasi Gagal',
            html: `
                <ul class="text-start small text-danger">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            `,
            confirmButtonColor: '#0058a8',
        });
    @endif
</script>
@stack('scripts')
</body>
</html>