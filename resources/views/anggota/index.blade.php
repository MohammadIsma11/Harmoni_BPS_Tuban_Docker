@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/pages/anggota-index.css') }}">

<div class="container-fluid">
    <div class="card card-members">
        <div class="card-body p-0">
            {{-- Header --}}
            <div class="p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h5 class="fw-bold mb-1 text-dark">Data Anggota</h5>
                    <p class="text-muted small mb-0">Total: {{ $anggota->total() }} Personel BPS Tuban</p>
                </div>
                <div class="d-flex gap-2">
                    <form action="{{ route('manajemen.anggota') }}" method="GET" class="d-flex">
                        <input type="text" name="search" class="form-control form-control-sm rounded-pill px-3" 
                               placeholder="Cari..." value="{{ request('search') }}" style="width: 180px;">
                    </form>
                    
                    {{-- HANYA ADMIN YANG BISA TAMBAH --}}
                    @if(Auth::user()->role == 'Admin')
                    <a href="{{ route('manajemen.anggota.create') }}" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
                        <i class="fas fa-plus me-1"></i> Tambah Anggota
                    </a>
                    @endif
                </div>
            </div>

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table table-members">
                    <thead>
                        <tr>
                            <th class="ps-4 col-nama">Nama Lengkap</th>
                            <th class="col-user">Username</th>
                            <th class="text-center col-role">Role</th>
                            <th class="text-center col-tim">Tim</th>
                            <th class="text-center col-tgl">Bergabung</th>
                            {{-- KOLOM AKSI HANYA UNTUK ADMIN --}}
                            @if(Auth::user()->role == 'Admin')
                            <th class="text-center pe-4 col-aksi">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($anggota as $a)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-mini me-2">{{ substr($a->nama_lengkap, 0, 1) }}</div>
                                    <div class="text-truncate fw-bold text-dark" style="max-width: 150px;" title="{{ $a->nama_lengkap }}">
                                        {{ $a->nama_lengkap }}
                                    </div>
                                </div>
                            </td>
                            <td><span class="text-primary small">@ {{ $a->username }}</span></td>
                            <td class="text-center">
                                @if($a->role == 'Admin')
                                    <span class="role-badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-10">Admin</span>
                                @elseif($a->role == 'Kepala')
                                    <span class="role-badge bg-dark bg-opacity-10 text-dark border border-dark border-opacity-10">Kepala</span>
                                @elseif($a->role == 'Katim')
                                    <span class="role-badge bg-info bg-opacity-10 text-info border border-info border-opacity-10">Katim</span>
                                @elseif($a->role == 'Mitra')
                                    <span class="role-badge bg-success bg-opacity-10 text-success border border-success border-opacity-10">Mitra</span>
                                @else
                                    <span class="role-badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-10">Pegawai</span>
                                @endif
                            </td>
                            <td class="text-center small fw-medium text-muted">
                                {{ $a->team->nama_tim ?? '-' }}
                            </td>
                            <td class="text-center text-muted small">
                                {{ \Carbon\Carbon::parse($a->created_at)->format('d/m/y') }}
                            </td>
                            
                            {{-- AKSI HANYA UNTUK ADMIN --}}
                            @if(Auth::user()->role == 'Admin')
                            <td class="pe-4 text-center">
                                <div class="btn-action-group">
                                    <a href="{{ route('manajemen.anggota.edit', $a->id) }}" class="btn-mini btn-edit" title="Edit">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    <form action="{{ route('manajemen.anggota.destroy', $a->id) }}" method="POST" id="del-{{ $a->id }}">
                                        @csrf @method('DELETE')
                                        <button type="button" onclick="confirmDelete({{ $a->id }}, '{{ $a->nama_lengkap }}')" class="btn-mini btn-delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ Auth::user()->role == 'Admin' ? 6 : 5 }}" class="text-center py-5 text-muted">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="fas fa-user-slash fa-3x mb-3 opacity-20"></i>
                                    <span>Tidak ada data anggota ditemukan.</span>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer Pagination --}}
            <div class="p-3 border-top d-flex justify-content-center">
                {{ $anggota->links() }}
            </div>
        </div>
    </div>
</div>

@if(Auth::user()->role == 'Admin')
<script src="{{ asset('js/pages/anggota-index.js') }}"></script>
@endif

@endsection