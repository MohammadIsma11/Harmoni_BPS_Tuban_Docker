@php
    \Carbon\Carbon::setLocale('id');
@endphp
@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/pages/history-index.css') }}">

<div class="container-fluid px-4 mt-3">
    {{-- Page Info --}}
    <div class="mb-4 d-flex align-items-center justify-content-between">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Riwayat Laporan Lapangan</h4>
            <p class="text-muted small mb-0">Monitor seluruh aktivitas pengawasan yang telah selesai dilaporkan.</p>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('history.index') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item active">Riwayat</li>
            </ol>
        </nav>
    </div>

    {{-- Main Wrapper --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        {{-- Header Bar --}}
        <div class="action-header-card">
            <div class="row align-items-center g-3">
                <div class="col-md-6">
                    <div class="d-flex gap-2">
                        @if(in_array(auth()->user()->role, ['Admin', 'Katim']))
                            <a href="{{ route('history.pdf_rekap') }}" class="btn-rekap btn-rekap-pdf shadow-sm">
                                <i class="fas fa-file-pdf"></i> PDF Rekap
                            </a>
                            <a href="{{ route('history.excel_rekap') }}" class="btn-rekap btn-rekap-excel shadow-sm">
                                <i class="fas fa-file-excel"></i> Excel Rekap
                            </a>
                        @endif
                    </div>
                </div>
                <div class="col-md-6 d-flex justify-content-md-end">
                    <form action="{{ route('history.index') }}" method="GET" class="search-group-custom">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Cari petugas atau kegiatan..." value="{{ request('search') }}">
                            <button class="btn" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Table Area --}}
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-history mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4" width="25%">Petugas Pelaksana</th>
                            <th width="35%">Detail Kegiatan & Lokasi</th>
                            <th width="15%" class="text-center">Tgl Pelaksanaan</th>
                            <th class="text-center pe-4" width="20%">Aksi Manajerial</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayat as $r)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-box me-3 bg-primary bg-opacity-10 text-primary fw-bold rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; font-size: 0.85rem; border: 1px solid rgba(0,88,168,0.1);">
                                        {{ strtoupper(substr($r->agenda->assignee->nama_lengkap ?? '?', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark lh-1 mb-1">{{ $r->agenda->assignee->nama_lengkap ?? 'Pegawai' }}</div>
                                        <span class="text-muted" style="font-size: 0.7rem;">
                                            <i class="fas fa-users me-1"></i> {{ $r->agenda->assignee->team->nama_tim ?? 'Lintas Tim' }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark mb-0 lh-sm">{{ $r->agenda->title }}</div>
                                <span class="location-info">
                                    <i class="fas fa-map-marker-alt text-danger me-1"></i> {{ $r->lokasi_tujuan }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="date-badge">
                                    <i class="far fa-calendar-check me-1"></i> 
                                    {{ \Carbon\Carbon::parse($r->tanggal_lapor)->translatedFormat('d M Y') }}
                                </span>
                            </td>
                            <td class="text-center pe-4">
                                <div class="d-flex justify-content-center gap-2">
                                    {{-- Detail --}}
                                    <a href="{{ route('history.detail', $r->id) }}" class="btn-action btn-view" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    {{-- Edit (Hanya jika belum divalidasi atau sesuai role) --}}
                                    <a href="{{ route('history.edit', $r->id) }}" class="btn-action btn-edit" title="Edit Laporan">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    {{-- Cetak --}}
                                    <a href="{{ route('history.export', $r->id) }}" class="btn-action btn-pdf" title="Cetak PDF">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                    
                                    {{-- Hapus: Hanya untuk Admin, Katim, atau Pemilik Laporan --}}
                                    @if(auth()->user()->role == 'Admin' || auth()->user()->role == 'Katim' || $r->assigned_to == auth()->id())
                                        <form action="{{ route('history.task_destroy', $r->id) }}" method="POST" id="form-delete-{{ $r->id }}" class="d-inline">
                                            @csrf 
                                            @method('DELETE')
                                            <button type="button" onclick="confirmDelete({{ $r->id }}, '{{ $r->title }}')" class="btn-action btn-delete" title="Hapus Laporan">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="py-4">
                                    <i class="fas fa-folder-open fa-3x text-light mb-3"></i>
                                    <p class="text-muted fw-bold mb-0">Belum ada riwayat laporan yang tersimpan.</p>
                                    <small class="text-muted">Laporan yang sudah Anda kirim akan muncul di sini.</small>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        {{-- Pagination --}}
        @if($riwayat->hasPages())
            <div class="card-footer bg-white py-3 border-top">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="small text-muted">Menampilkan {{ $riwayat->firstItem() }} - {{ $riwayat->lastItem() }} dari {{ $riwayat->total() }} data</span>
                    {{ $riwayat->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

{{-- SweetAlert Konfirmasi Hapus --}}
<script src="{{ asset('js/pages/history-index.js') }}"></script>
@endsection