@php
    \Carbon\Carbon::setLocale('id');
@endphp

@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/pages/agenda-all.css') }}">

<div class="container-fluid px-4 pb-5">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm rounded-pill me-3 px-3 shadow-sm">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
            <div>
                <h4 class="fw-bold text-dark mb-1">Semua Agenda & Tugas</h4>
                <p class="text-muted small mb-0">Monitoring seluruh aktivitas pengawasan lapangan dan rapat dinas.</p>
            </div>
        </div>
        <div class="bg-white p-2 px-3 rounded-4 shadow-sm border border-primary border-opacity-10">
            <i class="fas fa-list-check text-primary me-2"></i>
            <span class="fw-bold small text-dark">Total: {{ $allAgendas->total() }} Data</span>
        </div>
    </div>

    {{-- FILTER BAR --}}
    <div class="card filter-card mb-4">
        <div class="card-body p-3">
            <form action="{{ route('agenda.all') }}" method="GET" class="row g-2">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-0 small" placeholder="Cari judul agenda..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="type" class="form-select bg-light border-0 small">
                        <option value="">-- Semua Tipe --</option>
                        <option value="1" {{ request('type') == '1' ? 'selected' : '' }}>Tugas Lapangan</option>
                        <option value="2" {{ request('type') == '2' ? 'selected' : '' }}>Dinas Rapat</option>
                        <option value="3" {{ request('type') == '3' ? 'selected' : '' }}>Dinas Luar</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select bg-light border-0 small">
                        <option value="">-- Semua Status --</option>
                        <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Belum Dilaporkan</option>
                        <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Sudah Selesai</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 rounded-3 fw-bold">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- TABEL DATA --}}
    <div class="table-container overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr class="text-muted small">
                        <th class="ps-4 py-3">Waktu Pelaksanaan</th>
                        <th>Kegiatan / Agenda</th>
                        <th>Tipe</th>
                        <th>Petugas Terkait</th>
                        <th class="text-center pe-4">Status Laporan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($allAgendas as $a)
                    <tr class="transition-row">
                        <td class="ps-4">
                            <div class="fw-bold text-dark mb-0">
                                @if($a->activity_type_id == 1 || $a->activity_type_id == 3) {{-- Tugas Lapangan atau Dinas Luar --}}
                                    @if($a->status_laporan == 'Selesai')
                                        {{-- Selesai: Tampilkan satu tanggal saja --}}
                                        {{ \Carbon\Carbon::parse($a->end_date)->translatedFormat('d M Y') }}
                                    @else
                                        {{-- Pending: Tampilkan rentang waktu --}}
                                        <span class="text-primary fw-bold">
                                            {{ \Carbon\Carbon::parse($a->event_date)->translatedFormat('d M') }} - 
                                            {{ \Carbon\Carbon::parse($a->end_date)->translatedFormat('d M Y') }}
                                        </span>
                                    @endif
                                @else {{-- Dinas Rapat: Hanya satu tanggal --}}
                                    {{ \Carbon\Carbon::parse($a->event_date)->translatedFormat('d M Y') }}
                                @endif
                            </div>
                            <small class="text-muted">
                                <i class="far fa-clock me-1"></i> {{ $a->start_time ?? '08:00' }} WIB
                            </small>
                        </td>
                        <td>
                            <div class="fw-bold text-dark mb-1">{{ $a->title }}</div>
                            @if($a->status_laporan == 'Selesai')
                                <small class="text-muted">
                                    <i class="fas fa-map-marker-alt text-danger me-1"></i> 
                                    @if($a->activity_type_id == 2 && empty($a->location))
                                        Ruang Rapat
                                    @else
                                        {{ $a->location ?? '-' }}
                                    @endif
                                </small>
                            @endif
                        </td>
                        <td>
                            @if($a->activity_type_id == 1)
                                <span class="badge bg-info-subtle border border-info border-opacity-25 rounded-pill px-3">Lapangan</span>
                            @elseif($a->activity_type_id == 3)
                                <span class="badge bg-warning-subtle border border-warning border-opacity-25 rounded-pill px-3 text-warning-emphasis">Dinas Luar</span>
                            @else
                                <span class="badge bg-primary-subtle border border-primary border-opacity-25 rounded-pill px-3">Rapat</span>
                            @endif
                        </td>
                        <td>
                            <div class="small fw-bold text-dark">{{ $a->assignee->nama_lengkap }}</div>
                            <small class="text-muted" style="font-size: 0.7rem;">Oleh: {{ $a->creator->nama_lengkap }}</small>
                        </td>
                        <td class="text-center pe-4">
                            @if($a->status_laporan == 'Selesai')
                                <span class="status-badge bg-success-subtle text-success border border-success border-opacity-25">
                                    <i class="fas fa-check-circle"></i> Sudah Lapor
                                </span>
                            @else
                                <span class="status-badge bg-warning-subtle text-warning-emphasis border border-warning border-opacity-25">
                                    <i class="fas fa-hourglass-half"></i> Belum Selesai
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="py-3">
                                <i class="fas fa-inbox fa-3x text-light mb-3"></i>
                                <h6 class="text-muted fw-bold">Data tidak ditemukan</h6>
                                <p class="text-muted small">Coba sesuaikan filter atau kata kunci pencarian Anda.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if($allAgendas->hasPages())
        <div class="p-4 border-top bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Menampilkan {{ $allAgendas->firstItem() }} - {{ $allAgendas->lastItem() }} dari {{ $allAgendas->total() }} agenda
                </small>
                <div>
                    {{ $allAgendas->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection