@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">Manajemen Master Kegiatan</h3>
            <p class="text-muted small">Atur daftar kegiatan/survei dan lingkup budget honorarium untuk mitra.</p>
        </div>
        <a href="{{ route('manajemen.kegiatan.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
            <i class="fas fa-plus me-2"></i>Tambah Kegiatan
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success border-0 shadow-sm rounded-4 alert-dismissible fade show mb-4" role="alert">
        <i class="fas fa-check-circle me-2"></i><strong>Berhasil!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 text-muted small fw-bold py-3">NAMA KEGIATAN</th>
                        <th class="text-muted small fw-bold py-3">TIM / FUNGSI</th>
                        <th class="text-muted small fw-bold py-3 text-center">MIN HONOR</th>
                        <th class="text-muted small fw-bold py-3 text-center">MAX HONOR</th>
                        <th class="pe-4 text-muted small fw-bold py-3 text-end">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kegiatans as $kegiatan)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-dark">{{ $kegiatan->nama_kegiatan }}</div>
                            <small class="text-muted">Dibuat: {{ $kegiatan->created_at->format('d/m/Y') }}</small>
                        </td>
                        <td>
                            <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2">
                                <i class="fas fa-layer-group me-1"></i> {{ $kegiatan->team ? $kegiatan->team->nama_tim : '-' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="text-muted small">Rp</span> <span class="fw-bold text-dark">{{ number_format($kegiatan->min_honor_standard, 0, ',', '.') }}</span>
                        </td>
                        <td class="text-center">
                            <span class="text-muted small">Rp</span> <span class="fw-bold text-primary">{{ number_format($kegiatan->max_honor_standard, 0, ',', '.') }}</span>
                        </td>
                        <td class="pe-4 text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('manajemen.kegiatan.edit', $kegiatan->id) }}" class="btn btn-light btn-sm rounded-pill px-3">
                                    <i class="fas fa-edit text-warning"></i>
                                </a>
                                <form action="{{ route('manajemen.kegiatan.destroy', $kegiatan->id) }}" method="POST" onsubmit="return confirm('Hapus kegiatan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-light btn-sm rounded-pill px-3">
                                        <i class="fas fa-trash text-danger"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-folder-open fs-1 d-block mb-3 opacity-25"></i>
                                Belum ada data kegiatan.
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            {{ $kegiatans->links() }}
        </div>
    </div>
</div>
@endsection
