@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 pb-5">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3 mt-4">
        <div>
            <h4 class="fw-bold mb-0 text-dark"><i class="fas fa-shield-alt text-danger me-2"></i>Akses Super: Monitoring Arsip</h4>
            <p class="text-muted small mb-0">Pusat data seluruh riwayat kegiatan organisasi dalam satu pintu.</p>
        </div>

        {{-- TOMBOL REKAP KHUSUS LAPANGAN --}}
        @if(request('type') == 1)
        <div class="d-flex gap-2">
            <a href="{{ route('history.pdf_rekap', request()->all()) }}" class="btn btn-outline-danger rounded-pill px-3 fw-bold shadow-sm">
                <i class="fas fa-file-pdf me-1"></i> Rekap PDF
            </a>
            <a href="{{ route('history.excel_rekap', request()->all()) }}" class="btn btn-outline-success rounded-pill px-3 fw-bold shadow-sm">
                <i class="fas fa-file-excel me-1"></i> Rekap Excel
            </a>
        </div>
        @endif
    </div>

    {{-- Filter Card --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form action="{{ route('super.access.index') }}" method="GET" class="row g-2">
                <div class="col-md-7">
                    <input type="text" name="search" class="form-control bg-light border-0" placeholder="Cari judul kegiatan..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="type" class="form-select bg-light border-0">
                        <option value="">Semua Tipe Kegiatan</option>
                        <option value="1" {{ request('type') == 1 ? 'selected' : '' }}>Tugas Lapangan</option>
                        <option value="2" {{ request('type') == 2 ? 'selected' : '' }}>Rapat Dinas</option>
                        <option value="3" {{ request('type') == 3 ? 'selected' : '' }}>Dinas Luar</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-danger w-100 rounded-3 fw-bold shadow-sm">Filter</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel Utama --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-dark text-white">
                    <tr class="small text-uppercase">
                        <th class="py-3 ps-4" style="width: 15%;">Tanggal</th>
                        <th style="width: 35%;">Kegiatan</th>
                        <th>Tipe</th>
                        <th>Penanggung Jawab / Pelapor</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($allActivities as $act)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-dark">{{ \Carbon\Carbon::parse($act->event_date)->translatedFormat('d M Y') }}</div>
                            <small class="text-muted">
                                <i class="far fa-clock me-1"></i>
                                @if(!in_array($act->activity_type_id, [2, 3]))
                                    {{ \Carbon\Carbon::parse($act->updated_at)->format('H:i') }} WIB
                                @else
                                    {{ $act->start_time }} WIB
                                @endif
                            </small>
                        </td>
                        <td>
                            <div class="fw-bold text-dark mb-1">{{ $act->title }}</div>
                            <small class="text-muted">
                                <i class="fas fa-map-marker-alt me-1 text-danger"></i> 
                                @if($act->activity_type_id == 2)
                                    Ruang Rapat
                                @else
                                    {{ $act->location ?? 'Lokasi Luar Kantor' }}
                                @endif
                            </small>
                        </td>
                        <td>
                            @if($act->activity_type_id == 2)
                                <span class="badge bg-primary bg-opacity-10 text-primary px-3 rounded-pill">Rapat</span>
                            @elseif($act->activity_type_id == 3)
                                <span class="badge bg-success bg-opacity-10 text-success px-3 rounded-pill">Dinas Luar</span>
                            @else
                                <span class="badge bg-info bg-opacity-10 text-info px-3 rounded-pill">Lapangan</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm bg-light rounded-circle me-2 d-flex align-items-center justify-content-center fw-bold text-primary border" style="width: 30px; height: 30px; font-size: 0.7rem;">
                                    {{ strtoupper(substr($act->assignee->nama_lengkap ?? $act->notulis->nama_lengkap ?? 'U', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="small fw-bold text-dark">{{ $act->assignee->nama_lengkap ?? $act->notulis->nama_lengkap ?? '-' }}</div>
                                    <small class="text-muted" style="font-size: 0.65rem;">Oleh: {{ $act->creator->nama_lengkap ?? 'System' }}</small>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            @if($act->activity_type_id == 2)
                                <a href="{{ route('meeting.history.detail', $act->id) }}?from=super" class="btn btn-sm btn-white border rounded-pill px-3 shadow-xs">
                                    <i class="fas fa-eye me-1 text-primary"></i> Detail
                                </a>
                            @elseif($act->activity_type_id == 3)
                                <a href="{{ route('meeting.history.detail_dinas', $act->id) }}?from=super" class="btn btn-sm btn-white border rounded-pill px-3 shadow-xs">
                                    <i class="fas fa-eye me-1 text-success"></i> Detail
                                </a>
                            @else
                                <a href="{{ route('history.detail', $act->id) }}?from=super" class="btn btn-sm btn-white border rounded-pill px-3 shadow-xs">
                                    <i class="fas fa-eye me-1 text-info"></i> Detail
                                </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <i class="fas fa-folder-open fa-3x text-muted opacity-25 mb-3"></i>
                            <h6 class="fw-bold text-muted">Tidak ada data kegiatan ditemukan.</h6>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($allActivities->hasPages())
        <div class="card-footer bg-white border-0 p-3">
            {{ $allActivities->links() }}
        </div>
        @endif
    </div>
</div>

<style>
    .shadow-xs { box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
    .btn-white:hover { background-color: #f8fafc; border-color: #cbd5e1; }
</style>
@endsection