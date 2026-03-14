@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 pb-5">
    {{-- Header Section --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold text-dark mb-1">Daftar Tugas Baru</h4>
            <p class="text-muted small mb-0">Kelola penugasan pengawasan lapangan yang perlu segera Anda laporkan.</p>
        </div>
        <div class="bg-white p-2 px-3 rounded-4 shadow-sm border border-primary border-opacity-10">
            <i class="fas fa-tasks text-primary me-2"></i>
            <span class="fw-bold small text-dark">Total: {{ $tugas->count() }} Penugasan</span>
        </div>
    </div>

    {{-- Alert Status Cuti --}}
    @php
        $today = \Carbon\Carbon::now()->format('Y-m-d');
        $isCutiHariIni = \App\Models\Absensi::where('user_id', Auth::id())
                            ->where('status', 'Cuti')
                            ->where('start_date', '<=', $today)
                            ->where('end_date', '>=', $today)
                            ->exists();
    @endphp

    @if($isCutiHariIni)
        <div class="alert alert-danger border-0 shadow-sm rounded-4 p-3 mb-4 d-flex align-items-center">
            <div class="bg-danger bg-opacity-10 p-2 rounded-3 me-3 text-danger">
                <i class="fas fa-user-lock fa-lg"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-0 text-danger">Akses Pelaporan Terkunci</h6>
                <small>Anda tercatat sedang <strong>CUTI</strong> hari ini. Tombol pelaporan dinonaktifkan sementara.</small>
            </div>
        </div>
    @endif

    {{-- Tabel Tugas Modern --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr class="text-muted small text-uppercase">
                        <th class="border-0 py-3 ps-4" style="width: 180px;">Rentang Waktu</th>
                        <th class="border-0 py-3">Nama Tugas / Deskripsi</th>
                        <th class="border-0 py-3">Ditugaskan Oleh</th>
                        <th class="border-0 py-3 text-center" style="width: 240px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tugas as $t)
                        <tr class="transition-row {{ $isCutiHariIni ? 'opacity-75' : '' }}">
                            <td class="ps-4">
                                <div class="fw-bold text-dark mb-0" style="font-size: 0.85rem;">
                                    {{ date('d M', strtotime($t->event_date)) }} - {{ date('d M Y', strtotime($t->end_date)) }}
                                </div>
                                <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill mt-1" style="font-size: 0.6rem;">
                                    <i class="fas fa-hourglass-half me-1"></i> Belum Lapor
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold text-primary mb-1">{{ $t->title }}</div>
                                <div class="small text-muted text-truncate" style="max-width: 400px;" title="{{ $t->description }}">
                                    {{ $t->description ?? 'Tidak ada deskripsi tambahan.' }}
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-mini bg-info bg-opacity-10 text-info rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 25px; height: 25px; font-size: 0.6rem; font-weight: 800; border: 1px solid rgba(0,0,0,0.05);">
                                        {{ strtoupper(substr($t->creator->nama_lengkap ?? 'A', 0, 1)) }}
                                    </div>
                                    <div class="small text-dark fw-bold text-truncate" style="max-width: 150px;">
                                        {{ $t->creator->nama_lengkap ?? 'Admin' }}
                                    </div>
                                </div>
                                <div class="small text-muted mt-1" style="font-size: 0.65rem;">
                                    {{ ($t->creator && $t->creator->team) ? $t->creator->team->nama_tim : 'Kantor BPS' }}
                                </div>
                            </td>
                            <td class="text-center pe-4">
                                <div class="d-flex justify-content-center align-items-center gap-2">
                                    {{-- Tombol Lihat ST --}}
                                    @if($t->surat_tugas_path)
                                        <a href="{{ asset('storage/' . $t->surat_tugas_path) }}" class="btn btn-outline-danger btn-custom-action fw-bold shadow-xs" target="_blank">
                                            <i class="fas fa-file-pdf me-1"></i> ST
                                        </a>
                                    @else
                                        <button class="btn btn-light btn-custom-action text-muted border-0 shadow-none" disabled>
                                            <i class="fas fa-times me-1"></i> No ST
                                        </button>
                                    @endif

                                    {{-- Tombol Isi Laporan --}}
                                    @if($isCutiHariIni)
                                        <button class="btn btn-secondary btn-custom-action fw-bold shadow-none opacity-50" disabled>
                                            <i class="fas fa-lock me-1"></i> Locked
                                        </button>
                                    @else
                                        {{-- Link ini harus sesuai dengan name('task.create') di routes/web.php --}}
                                        <a href="{{ route('task.create', ['id' => $t->id]) }}" class="btn btn-primary btn-custom-action fw-bold shadow-sm">
                                            <i class="fas fa-edit me-1"></i> Lapor
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <img src="https://illustrations.popsy.co/amber/clipboard-check.svg" style="height: 140px;" class="mb-3 opacity-75">
                                <h6 class="fw-bold text-muted">Semua Beres!</h6>
                                <p class="text-muted small">Belum ada penugasan baru yang perlu dilaporkan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .btn-custom-action {
        width: 90px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem !important;
        border-radius: 8px !important;
        padding: 0 !important;
        white-space: nowrap;
    }

    .transition-row { transition: all 0.2s ease; }
    .transition-row:hover { background-color: #f8fafc !important; }
    
    .table thead th {
        font-size: 0.7rem;
        letter-spacing: 0.5px;
        font-weight: 700;
        color: #64748b;
    }
    
    .btn-primary { 
        background: linear-gradient(135deg, var(--bps-blue) 0%, #007bff 100%); 
        border: none; 
    }
    .btn-primary:hover { 
        transform: translateY(-1px); 
        box-shadow: 0 4px 12px rgba(0, 88, 168, 0.2); 
    }
</style>
@endsection