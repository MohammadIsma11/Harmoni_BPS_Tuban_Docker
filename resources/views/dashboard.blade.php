@php
    \Carbon\Carbon::setLocale('id');
@endphp

@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/pages/dashboard.css') }}">

<div class="container-fluid px-4 pb-5">
    {{-- Header Dashboard --}}
    <div class="dashboard-header d-flex justify-content-between align-items-center">
        <div>
            <h2 class="fw-bold mb-1 text-white">Halo, {{ $nama }} 👋</h2>
            <p class="opacity-75 mb-0 text-uppercase small letter-spacing-1 fw-medium text-white">
                <i class="fas fa-id-badge me-2"></i>{{ $role }} &bull; {{ $tim }}
            </p>
        </div>
        <div class="text-end d-none d-md-block text-white">
            <div class="h5 fw-bold mb-0" style="color: #ffda6a;">
                {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
            </div>
            <small class="opacity-75">BPS Kabupaten Tuban</small>
        </div>
    </div>

    {{-- BARIS 1: STATISTIK UTAMA --}}
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card stat-card shadow-sm h-100 p-3 border-0 animate-up">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3"><i class="fas fa-user-friends"></i></div>
                    <div>
                        <p class="text-muted small fw-bold text-uppercase mb-1">
                            @if($role == 'Pegawai') Tim Saya @elseif($role == 'Katim') Anggota Tim @else Total Pegawai @endif
                        </p>
                        <h3 class="fw-bold mb-0 text-dark">{{ $total_pegawai }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card shadow-sm h-100 p-3 border-0 animate-up">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-info bg-opacity-10 text-info me-3"><i class="fas fa-layer-group"></i></div>
                    <div>
                        <p class="text-muted small fw-bold text-uppercase mb-1">Total Agenda</p>
                        <h3 class="fw-bold mb-0 text-dark">{{ $total_agenda }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card shadow-sm h-100 p-3 border-0 animate-up">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-success bg-opacity-10 text-success me-3"><i class="fas fa-clipboard-check"></i></div>
                    <div>
                        <p class="text-muted small fw-bold text-uppercase mb-1">Selesai Lapor</p>
                        <h3 class="fw-bold mb-0 text-dark">{{ $tugas_selesai }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- BARIS 1.5: STATISTIK HONORARIUM (KHUSUS ROLE TERKAIT) --}}
    @if(isset($total_honor_month))
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card stat-card shadow-sm h-100 p-3 border-0 animate-up" style="border-left: 5px solid #6c5ce7 !important;">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-purple bg-opacity-10 text-purple me-3" style="color: #6c5ce7;"><i class="fas fa-money-bill-wave"></i></div>
                    <div>
                        <p class="text-muted small fw-bold text-uppercase mb-1">Honor Terjadwal ({{ \Carbon\Carbon::parse(request('filter_bulan', date('Y-m')))->translatedFormat('M Y') }})</p>
                        <h4 class="fw-bold mb-0 text-dark">Rp {{ number_format($total_honor_month, 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card shadow-sm h-100 p-3 border-0 animate-up" style="border-left: 5px solid #e17055 !important;">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-orange bg-opacity-10 text-orange me-3" style="color: #e17055;"><i class="fas fa-hourglass-half"></i></div>
                    <div>
                        <p class="text-muted small fw-bold text-uppercase mb-1">Total Antrean Honor</p>
                        <h4 class="fw-bold mb-0 text-dark">Rp {{ number_format($total_antre_honor, 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card shadow-sm h-100 p-3 border-0 animate-up" style="border-left: 5px solid #00b894 !important;">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-teal bg-opacity-10 text-teal me-3" style="color: #00b894;"><i class="fas fa-user-check"></i></div>
                    <div>
                        <p class="text-muted small fw-bold text-uppercase mb-1">Mitra Aktif</p>
                        <h4 class="fw-bold mb-0 text-dark">{{ $mitra_aktif_count }} Mitra</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- BARIS 2: ANALISIS KHUSUS KEPALA (Top Tim & Tren Bulanan) --}}
    @if($role == 'Kepala' || $role == 'Admin')
    <div class="row g-4 mb-4">
        {{-- Top Tim Teraktif --}}
        <div class="col-lg-6">
            <div class="dashboard-panel animate-up">
                <h6 class="fw-bold text-dark mb-4"><i class="fas fa-trophy me-2 text-warning"></i>7 Tim Paling Aktif ({{ \Carbon\Carbon::parse(request('filter_bulan', date('Y-m')))->translatedFormat('F Y') }})</h6>
                <div class="row">
                    @forelse($top_teams as $t_team)
                        <div class="col-md-6 mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small fw-bold text-secondary text-truncate" style="max-width: 150px;">{{ $t_team->nama_tim }}</span>
                                <span class="badge bg-primary bg-opacity-10 text-primary" style="font-size: 0.65rem;">{{ $t_team->agendas_count }} Agenda</span>
                            </div>
                            <div class="progress progress-custom">
                                @php 
                                    $maxCount = $top_teams->first()->agendas_count ?? 1;
                                    $percent = ($t_team->agendas_count / ($maxCount > 0 ? $maxCount : 1)) * 100;
                                @endphp
                                <div class="progress-bar progress-bar-custom" role="progressbar" style="width: {{ $percent }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-muted small">Data belum tersedia.</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Skala Tugas Bulanan --}}
        <div class="col-lg-6">
            <div class="dashboard-panel animate-up">
                <h6 class="fw-bold text-dark mb-4"><i class="fas fa-chart-bar me-2 text-success"></i>Tren Skala Tugas Tahun {{ date('Y') }}</h6>
                <div class="row g-2">
                    @php $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des']; @endphp
                    @foreach($months as $idx => $m)
                    <div class="col-4 mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted fw-bold" style="width: 25px; font-size: 0.65rem;">{{ $m }}</span>
                            <div class="trend-bar flex-grow-1">
                                @php 
                                    $val = $monthly_stats[$idx+1] ?? 0; 
                                    $maxM = (count($monthly_stats) > 0) ? max($monthly_stats) : 1;
                                    $pctM = ($val / ($maxM ?: 1)) * 100;
                                @endphp
                                <div class="trend-fill" style="width: {{ $pctM }}%"></div>
                            </div>
                            <span class="small fw-bold text-dark" style="font-size: 0.65rem;">{{ $val }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- BARIS 3: TABEL AGENDA TERKINI --}}
    <div class="card table-container shadow-sm overflow-hidden border-0 animate-up">
        <div class="card-header bg-white border-0 px-4 py-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h5 class="fw-bold mb-0 text-dark">Agenda & Tugas Terkini</h5>
                    <small class="text-muted">Periode: {{ \Carbon\Carbon::parse(request('filter_bulan', date('Y-m')))->translatedFormat('F Y') }}</small>
                </div>

                @if($role == 'Kepala' || $role == 'Admin')
                <div class="filter-wrapper d-flex gap-2">
                    <form action="{{ route('dashboard') }}" method="GET" class="d-flex gap-2">
                        <select name="filter_tim" class="form-select form-select-sm border-light bg-light rounded-pill px-3" style="min-width: 160px;">
                            <option value="">Semua Tim</option>
                            @foreach($list_tim as $t)
                                <option value="{{ $t->id }}" {{ request('filter_tim') == $t->id ? 'selected' : '' }}>{{ $t->nama_tim }}</option>
                            @endforeach
                        </select>
                        <input type="month" name="filter_bulan" class="form-control form-select-sm border-light bg-light rounded-pill px-3" value="{{ request('filter_bulan', date('Y-m')) }}">
                        <button type="submit" class="btn btn-primary btn-sm rounded-circle shadow-sm"><i class="fas fa-search"></i></button>
                        <a href="{{ route('dashboard') }}" class="btn btn-light btn-sm rounded-circle shadow-sm"><i class="fas fa-sync-alt"></i></a>
                    </form>
                </div>
                @endif

                <a href="{{ route('agenda.all') }}" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold">
                    Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4" style="width: 35%;">Kegiatan & Tim Kerja</th>
                        <th style="width: 25%;">Petugas Pelaksana</th>
                        <th class="text-center" style="width: 20%;">Jadwal Pelaksanaan</th>
                        <th class="text-center" style="width: 20%;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($agenda_terbaru as $agenda)
                    @php
                        $eventDate = \Carbon\Carbon::parse($agenda->event_date);
                        $endDate = \Carbon\Carbon::parse($agenda->end_date);
                        $today = now()->startOfDay();
                        $isUrgent = ($agenda->status_laporan != 'Selesai') && ($eventDate->isToday() || $eventDate->isTomorrow());

                        if($agenda->status_laporan == 'Selesai') {
                            $statusLabel = 'SELESAI'; $statusClass = 'status-selesai';
                        } elseif ($today->lt($eventDate->startOfDay())) {
                            $statusLabel = 'AKAN DATANG'; $statusClass = 'status-akan-datang';
                        } else {
                            $statusLabel = 'SEDANG BERJALAN'; $statusClass = 'status-berjalan';
                        }
                    @endphp
                    <tr class="{{ $isUrgent ? 'urgent-row' : '' }}">
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                @if($isUrgent)
                                    <span class="badge bg-danger p-1 rounded-circle me-2 animate-pulse-red" style="width: 10px; height: 10px;"></span>
                                @endif
                                <div>
                                    <div class="fw-bold text-dark mb-0">{{ $agenda->title }}</div>
                                    <small class="text-muted fw-medium"><i class="fas fa-users me-1 text-primary"></i>Tugas dari : {{ $agenda->creator_team_name ?? '-' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle me-3">{{ strtoupper(substr($agenda->assignee->nama_lengkap ?? '?', 0, 1)) }}</div>
                                <div>
                                    <div class="fw-semibold text-secondary" style="font-size: 0.85rem;">{{ $agenda->assignee->nama_lengkap ?? 'Tanpa Nama' }}</div>
                                    <small class="text-muted text-uppercase" style="font-size: 0.65rem; font-weight: 700;">{{ $agenda->assignee->role ?? '' }}</small>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="badge bg-white {{ $isUrgent ? 'border-danger text-danger' : 'border-light text-dark' }} fw-bold px-3 py-2 border shadow-sm" style="font-size: 0.75rem;">
                                {{ $eventDate->translatedFormat('d M') }} - {{ $endDate->translatedFormat('d M Y') }}
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-pill-custom {{ $statusClass }} border shadow-sm">
                                <i class="fas {{ $statusLabel == 'SELESAI' ? 'fa-check-circle' : ($statusLabel == 'AKAN DATANG' ? 'fa-clock' : 'fa-spinner') }} me-1"></i>
                                {{ $statusLabel }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center py-5 text-muted">Tidak ada agenda aktif ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-white border-0 px-4 py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="small text-muted fw-medium">
                    Menampilkan <span class="text-dark fw-bold">{{ $agenda_terbaru->firstItem() ?? 0 }}</span> sampai <span class="text-dark fw-bold">{{ $agenda_terbaru->lastItem() ?? 0 }}</span> dari <span class="text-dark fw-bold">{{ $agenda_terbaru->total() }}</span> agenda
                </div>
                <div class="pagination-custom">
                    {{ $agenda_terbaru->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection