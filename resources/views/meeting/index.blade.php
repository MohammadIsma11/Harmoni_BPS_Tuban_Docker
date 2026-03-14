@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 pb-5">
    {{-- Header & Filter --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold text-dark mb-1">Kegiatan & Pertemuan</h4>
            <p class="text-muted small mb-0">Daftar agenda rapat dinas dan penugasan dinas luar Anda.</p>
        </div>
        
        {{-- Tombol Filter --}}
        <div class="d-flex gap-2">
            <div class="btn-group p-1 bg-white rounded-4 shadow-sm border border-primary border-opacity-10">
                <a href="{{ route('meeting.index') }}" 
                   class="btn btn-sm rounded-3 px-3 {{ !request('type') ? 'btn-primary shadow-sm' : 'btn-light text-muted' }}">
                   Semua
                </a>
                <a href="{{ route('meeting.index', ['type' => 2]) }}" 
                   class="btn btn-sm rounded-3 px-3 {{ request('type') == 2 ? 'btn-primary shadow-sm' : 'btn-light text-muted' }}">
                   Rapat
                </a>
                <a href="{{ route('meeting.index', ['type' => 3]) }}" 
                   class="btn btn-sm rounded-3 px-3 {{ request('type') == 3 ? 'btn-primary shadow-sm' : 'btn-light text-muted' }}">
                   Dinas Luar
                </a>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr class="text-muted small text-uppercase">
                        <th class="border-0 py-3 ps-4" style="width: 150px;">Tanggal</th>
                        <th class="border-0 py-3">Nama Kegiatan / Agenda</th>
                        <th class="border-0 py-3">Penyelenggara</th>
                        <th class="border-0 py-3 text-center" style="width: 160px;">Status Anda</th>
                        <th class="border-0 py-3 text-center" style="width: 200px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($meetings as $m)
                        @php
                            $isOverdue = \Carbon\Carbon::parse($m->event_date)->isPast() && !$m->event_date->isToday();
                            $sudahTTD = \App\Models\MeetingPresence::where('agenda_id', $m->id)
                                        ->where('user_id', Auth::id())
                                        ->exists();
                        @endphp
                        <tr class="transition-row">
                            <td class="ps-4">
                                <div class="fw-bold text-dark mb-0">{{ \Carbon\Carbon::parse($m->event_date)->translatedFormat('d M Y') }}</div>
                                <small class="text-muted"><i class="far fa-clock me-1"></i>{{ $m->start_time ?? '--:--' }} WIB</small>
                            </td>
                            <td>
                                <div class="fw-bold text-primary mb-1">{{ $m->title }}</div>
                                {{-- Label Pembeda Jenis Kegiatan --}}
                                @if($m->activity_type_id == 2)
                                    <span class="badge bg-blue-soft border-blue-soft">
                                        <i class="fas fa-handshake me-1"></i> RAPAT DINAS
                                    </span>
                                @elseif($m->activity_type_id == 3)
                                    <span class="badge bg-green-soft border-green-soft">
                                        <i class="fas fa-route me-1"></i> DINAS LUAR
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-info bg-opacity-10 text-info rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; font-size: 0.7rem; font-weight: 800;">
                                        {{ strtoupper(substr($m->creator->nama_lengkap ?? 'A', 0, 1)) }}
                                    </div>
                                    <span class="small fw-semibold text-muted">{{ $m->creator->nama_lengkap ?? 'Admin' }}</span>
                                </div>
                            </td>
                            <td class="text-center">
                                @if($sudahTTD)
                                    <span class="badge bg-success-subtle text-success rounded-pill border border-success border-opacity-25 status-badge">
                                        <i class="fas fa-check-circle me-1"></i> Hadir
                                    </span>
                                @elseif($isOverdue)
                                    <span class="badge bg-danger-subtle text-danger rounded-pill border border-danger border-opacity-25 status-badge">
                                        <i class="fas fa-times-circle me-1"></i> Terlewat
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill border border-warning border-opacity-25 status-badge">
                                        <i class="fas fa-clock me-1"></i> Belum Absen
                                    </span>
                                @endif
                            </td>
                            <td class="text-center pe-4">
                                <div class="d-flex justify-content-center align-items-center gap-2">
                                    
                                    {{-- LOGIKA UTAMA: ABSEN ATAU LAPOR --}}
                                    @if(!$sudahTTD && (!$isOverdue || $m->event_date->isToday()))
                                        @if($m->activity_type_id == 3)
                                            {{-- SEMUA PESERTA DINAS LUAR BISA LAPOR --}}
                                            <a href="{{ route('meeting.dinas.create', $m->id) }}" class="btn btn-success btn-custom-action fw-bold shadow-sm">
                                                <i class="fas fa-file-export me-1"></i> Lapor
                                            </a>
                                        @else
                                            {{-- KHUSUS RAPAT: Tombol Absen (Tanda Tangan) --}}
                                            <a href="{{ route('meeting.presensi', $m->id) }}" class="btn btn-primary btn-custom-action fw-bold shadow-sm">
                                                <i class="fas fa-signature me-1"></i> Absen
                                            </a>
                                        @endif
                                    @endif

                                    {{-- KHUSUS NOTULIS RAPAT --}}
                                    @if($m->activity_type_id == 2 && $m->notulis_id == Auth::id())
                                        <a href="{{ route('meeting.notulensi', $m->id) }}" class="btn btn-dark btn-sm btn-custom-action fw-bold">
                                            <i class="fas fa-pen-nib me-1"></i> Notulis
                                        </a>
                                    @endif

                                    {{-- TOMBOL PANTAU (ADMIN/KATIM/PENCIPTA) --}}
                                    @if($m->user_id == Auth::id() || in_array(Auth::user()->role, ['Admin', 'Katim', 'Kepala']))
                                        <a href="{{ route('meeting.monitoring', $m->id) }}" class="btn btn-light btn-sm btn-custom-action border shadow-xs">
                                            <i class="fas fa-desktop me-1 text-muted"></i> Pantau
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="opacity-50 mb-3">
                                    <i class="fas fa-calendar-times fa-4x text-muted"></i>
                                </div>
                                <h6 class="fw-bold text-muted">Data Tidak Ditemukan</h6>
                                <p class="text-muted small">Mungkin sedang tidak ada jadwal yang sesuai filter ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    /* Status Badge Styling */
    .status-badge {
        width: 110px; height: 30px; display: inline-flex; align-items: center; justify-content: center;
        font-size: 0.7rem !important; padding: 0 !important;
    }

    /* Type Badge Styling (Green & Blue) */
    .bg-green-soft { background-color: #f0fdf4; color: #15803d; }
    .border-green-soft { border: 1px solid #bcf0da; }
    
    .bg-blue-soft { background-color: #eff6ff; color: #1d4ed8; }
    .border-blue-soft { border: 1px solid #bfdbfe; }

    .badge { font-size: 0.6rem; letter-spacing: 0.5px; padding: 5px 10px; border-radius: 6px; font-weight: 700; }

    /* Button Styling */
    .btn-custom-action {
        width: 90px; height: 34px; display: inline-flex; align-items: center; justify-content: center;
        font-size: 0.75rem !important; border-radius: 8px !important; padding: 0 !important; white-space: nowrap;
    }

    .bg-success-subtle { background-color: #f0fdf4; }
    .bg-danger-subtle { background-color: #fef2f2; }
    .bg-warning-subtle { background-color: #fffbeb; }
    .text-success { color: #16a34a !important; }
    .text-danger { color: #dc2626 !important; }
    .text-warning-emphasis { color: #92400e !important; }
    
    .shadow-xs { box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
    .transition-row { transition: all 0.2s ease; }
    .transition-row:hover { background-color: #f8fafc !important; }
    
    .table thead th { font-size: 0.7rem; letter-spacing: 0.5px; font-weight: 700; color: #64748b; }
    .btn-primary { background: linear-gradient(135deg, #0058a8 0%, #007bff 100%); border: none; }
</style>
@endsection