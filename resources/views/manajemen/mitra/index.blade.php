@extends('layouts.app')

@section('content')
<div class="row mb-4 animate-up">
    <div class="col-12">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h3 class="fw-bold text-dark mb-1">Manajemen Master Mitra</h3>
                <p class="text-muted small mb-0">Kelola identitas mitra BPS Tuban dan akun Portal Mitra.</p>
            </div>
            @if(Auth::user()->role === 'Admin')
            <div class="d-flex flex-wrap gap-2">
                <form id="truncateForm" action="{{ route('manajemen.mitra.truncate') }}" method="POST">
                    @csrf
                    <button type="button" class="btn btn-outline-danger rounded-pill px-4 fw-bold shadow-sm" onclick="confirmTruncate()">
                        <i class="fas fa-trash-alt me-2"></i>Hapus Semua Mitra
                    </button>
                </form>
                <button type="button" class="btn btn-outline-success rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#importModal">
                    <i class="fas fa-file-excel me-2"></i>Import Excel
                </button>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Stats Dashboard -->
<div class="row g-3 mb-4 animate-up" style="animation-delay: 0.1s;">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
            <div class="d-flex align-items-center">
                <div class="bg-primary bg-opacity-10 p-3 rounded-4 me-3">
                    <i class="fas fa-users text-primary fs-4"></i>
                </div>
                <div>
                    <small class="text-muted d-block fw-bold">Total Mitra Terdaftar</small>
                    <h4 class="fw-bold mb-0 text-primary">{{ $mitras->total() }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
            <div class="d-flex align-items-center">
                <div class="bg-success bg-opacity-10 p-3 rounded-4 me-3">
                    <i class="fas fa-user-shield text-success fs-4"></i>
                </div>
                <div>
                    <small class="text-muted d-block fw-bold">Akun Portal Aktif</small>
                    <h4 class="fw-bold mb-0 text-success">{{ $mitras->total() }}</h4>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden animate-up" style="animation-delay: 0.2s;">
    <div class="card-header bg-white border-0 py-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <h6 class="fw-bold mb-0"><i class="fas fa-list me-2"></i>Daftar Mitra BPS Tuban</h6>
        <div class="search-box position-relative" style="min-width: 300px;">
            <i class="fas fa-search position-absolute text-muted" style="left: 15px; top: 50%; transform: translateY(-50%);"></i>
            <input type="text" class="form-control form-control-sm border-0 bg-light rounded-pill ps-5 py-2" placeholder="Cari Nama atau Sobat ID...">
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light text-muted small text-uppercase">
                <tr>
                    <th class="ps-4 py-3 sticky-col-start shadow-sm" style="width: 50px; background: #f8f9fa;">No</th>
                    <th class="py-3 sticky-col-start shadow-sm" style="min-width: 200px; left: 50px; background: #f8f9fa;">Nama Lengkap</th>
                    <th class="py-3" style="min-width: 150px;">Posisi</th>
                    <th class="py-3" style="min-width: 150px;">Status Seleksi</th>
                    <th class="py-3" style="min-width: 150px;">Posisi Daftar</th>
                    <th class="py-3" style="min-width: 250px;">Alamat Detail</th>
                    <th class="py-3" style="min-width: 120px;">Provinsi</th>
                    <th class="py-3" style="min-width: 120px;">Kabupaten</th>
                    <th class="py-3" style="min-width: 120px;">Kecamatan</th>
                    <th class="py-3" style="min-width: 120px;">Desa</th>
                    <th class="py-3" style="min-width: 220px;">Tempat, Tgl Lahir (Umur)</th>
                    <th class="py-3" style="min-width: 50px;">JK</th>
                    <th class="py-3" style="min-width: 150px;">Pendidikan</th>
                    <th class="py-3" style="min-width: 150px;">Pekerjaan</th>
                    <th class="py-3" style="min-width: 200px;">Pekerjaan Lain</th>
                    <th class="py-3" style="min-width: 130px;">No Telp</th>
                    <th class="py-3" style="min-width: 150px;">SOBAT ID</th>
                    <th class="py-3" style="min-width: 200px;">Email</th>
                    @if(Auth::user()->role === 'Admin')
                    <th class="pe-4 py-3 text-end sticky-col-end shadow-sm" style="width: 100px; background: #f8f9fa;">AKSI</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($mitras as $mitra)
                <tr>
                    <td class="ps-4 small text-muted sticky-col-start" style="background: white;">{{ ($mitras->currentPage() - 1) * $mitras->perPage() + $loop->iteration }}</td>
                    <td class="sticky-col-start" style="left: 50px; background: white;">
                        <div class="fw-bold text-dark mb-0">{{ $mitra->nama_lengkap }}</div>
                    </td>
                    <td><div class="small">{{ $mitra->posisi ?: '-' }}</div></td>
                    <td>
                        <span class="badge {{ $mitra->status_seleksi == 'Diterima' ? 'bg-success' : ($mitra->status_seleksi == 'Ditolak' ? 'bg-danger' : 'bg-warning text-dark') }} bg-opacity-10 text-{{ $mitra->status_seleksi == 'Diterima' ? 'success' : ($mitra->status_seleksi == 'Ditolak' ? 'danger' : 'warning') }} small rounded-pill fw-bold">
                            {{ $mitra->status_seleksi ?: 'Menunggu' }}
                        </span>
                    </td>
                    <td><div class="small">{{ $mitra->posisi_daftar ?: '-' }}</div></td>
                    <td><div class="small text-truncate" style="max-width: 250px;" title="{{ $mitra->alamat_detail }}">{{ $mitra->alamat_detail ?: '-' }}</div></td>
                    <td><div class="small">{{ $mitra->alamat_prov ?: '-' }}</div></td>
                    <td><div class="small">{{ $mitra->alamat_kab ?: '-' }}</div></td>
                    <td><div class="small">{{ $mitra->alamat_kec ?: '-' }}</div></td>
                    <td><div class="small">{{ $mitra->alamat_desa ?: '-' }}</div></td>
                    <td>
                        <div class="small fw-medium">
                            @if($mitra->tempat_lahir) {{ $mitra->tempat_lahir }}, @endif 
                            {{ $mitra->tgl_lahir ? $mitra->tgl_lahir->format('d-m-Y') : '-' }} 
                            @if($mitra->umur) ({{ $mitra->umur }}) @endif
                        </div>
                    </td>
                    <td class="text-center"><span class="badge bg-light text-dark border small">{{ $mitra->jenis_kelamin }}</span></td>
                    <td><div class="small">{{ $mitra->pendidikan ?: '-' }}</div></td>
                    <td><div class="small">{{ $mitra->pekerjaan ?: '-' }}</div></td>
                    <td><div class="small text-truncate" style="max-width: 150px;">{{ $mitra->deskripsi_pekerjaan_lain ?: '-' }}</div></td>
                    <td><div class="small">{{ $mitra->no_telp ?: '-' }}</div></td>
                    <td><span class="badge bg-light text-primary border font-monospace small">{{ $mitra->sobat_id }}</span></td>
                    <td><div class="small">{{ $mitra->email ?: '-' }}</div></td>
                    @if(Auth::user()->role === 'Admin')
                    <td class="pe-4 text-end sticky-col-end" style="background: white;">
                        <div class="d-inline-flex gap-1">
                            <a href="{{ route('manajemen.mitra.edit', $mitra->sobat_id) }}" class="p-1" title="Edit">
                                <i class="fas fa-edit text-warning"></i>
                            </a>
                            <form id="deleteForm{{ $mitra->sobat_id }}" action="{{ route('manajemen.mitra.destroy', $mitra->sobat_id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="border-0 bg-transparent p-1" onclick="confirmDelete('{{ $mitra->sobat_id }}', '{{ $mitra->nama_lengkap }}')">
                                    <i class="fas fa-trash text-danger"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="{{ Auth::user()->role === 'Admin' ? 19 : 18 }}" class="text-center py-5">
                        <div class="text-muted">
                            <i class="fas fa-user-slash fs-1 d-block mb-3 opacity-25"></i>
                            <h6 class="fw-bold">Belum ada data mitra</h6>
                            <p class="small">Klik tombol di atas untuk menambah atau import file Excel.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white border-0 py-3">
        {{ $mitras->links() }}
    </div>
</div>

@push('modals')
<!-- Import Modal Premium -->
<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 bg-light rounded-top-4 pb-0 pt-4 px-4">
                <div class="d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 p-3 rounded-4 me-3">
                        <i class="fas fa-file-excel text-success fs-3"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">Import Data Mitra</h5>
                        <p class="text-muted small mb-0">Sinkronisasi data masal via Excel</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="importForm" action="{{ route('manajemen.mitra.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert bg-success bg-opacity-10 border-0 rounded-4 p-3 mb-4">
                        <h6 class="fw-bold text-success mb-2"><i class="fas fa-info-circle me-2"></i>Format Kolom Excel (Mulai Baris 3):</h6>
                        <div class="row small text-dark">
                            <div class="col-md-6">
                                <ul class="mb-0 ps-3">
                                    <li><b>B (1)</b>: Nama Lengkap</li>
                                    <li><b>C (2)</b>: Posisi</li>
                                    <li><b>D (3)</b>: Status Seleksi</li>
                                    <li><b>E (4)</b>: Posisi Daftar</li>
                                    <li><b>F (5)</b>: Alamat Detail</li>
                                    <li><b>G (6)</b>: Alamat Prov</li>
                                    <li><b>H (7)</b>: Alamat Kab</li>
                                    <li><b>I (8)</b>: Alamat Kec</li>
                                    <li><b>J (9)</b>: Alamat Desa</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="mb-0 ps-3">
                                    <li><b>K (10)</b>: Tempat, Tgl Lahir (Umur)*</li>
                                    <li><b>L (11)</b>: Jenis Kelamin (L/P)</li>
                                    <li><b>M (12)</b>: Pendidikan</li>
                                    <li><b>N (13)</b>: Pekerjaan</li>
                                    <li><b>O (14)</b>: Deskripsi Pekerjaan Lain</li>
                                    <li><b>P (15)</b>: No Telp</li>
                                    <li><b>Q (16)</b>: SOBAT ID</li>
                                    <li><b>R (17)</b>: Email</li>
                                </ul>
                            </div>
                        </div>
                        <p class="mt-2 mb-0 small text-muted">*) Contoh Format K: <b>TUBAN, 17-08-1945 (78)</b></p>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">UNGGAH FILE EXCEL (.XLSX)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0 rounded-start-pill ps-3"><i class="fas fa-upload text-muted"></i></span>
                            <input type="file" name="file_excel" class="form-control bg-light border-0 rounded-end-pill py-2" required>
                        </div>
                        <small class="text-muted mt-2 d-block px-1">Sistem akan otomatis membuat akun Portal Mitra dengan password default: <b>sobat123</b></small>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-5 fw-bold shadow-sm">
                        <i class="fas fa-rocket me-2"></i>Mulai Sinkronisasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endpush

<style>
    .animate-up { animation: fadeInUp 0.5s ease-out backwards; }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .table-hover tbody tr:hover { background-color: #f1f5f9; }
    
    /* Sticky Columns Extension */
    .sticky-col-start {
        position: sticky;
        left: 0;
        z-index: 5;
        background: #fff !important;
    }
    .sticky-col-end {
        position: sticky;
        right: 0;
        z-index: 5;
        background: #fff !important;
    }
    .table-responsive {
        max-height: 700px;
        scroll-behavior: smooth;
        -webkit-overflow-scrolling: touch;
    }
    
    /* Better Scrollbar */
    .table-responsive::-webkit-scrollbar {
        height: 8px;
        width: 8px;
    }
    .table-responsive::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .table-responsive::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    thead th {
        position: sticky;
        top: 0;
        z-index: 10;
        background: #f8f9fa !important;
    }
    thead th.sticky-col-start, thead th.sticky-col-end {
        z-index: 11;
    }

    /* Fixed flickering on some browsers */
    .table {
        border-collapse: separate;
        border-spacing: 0;
    }
    .table td, .table th {
        border-bottom-width: 1px;
    }
</style>
@push('scripts')
<script>
    function confirmTruncate() {
        Swal.fire({
            title: 'Hapus Semua Mitra?',
            text: "PERINGATAN: Seluruh data mitra dan akun portal mereka akan dihapus permanen. Tindakan ini tidak dapat dibatalkan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus Semua!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('truncateForm').submit();
            }
        });
    }

    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Hapus Mitra?',
            text: "Apakah Anda yakin ingin menghapus " + name + "? Data akan hilang permanen.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('deleteForm' + id).submit();
            }
        });
    }

    // Handle Import Loading
    document.getElementById('importForm').addEventListener('submit', function() {
        // Tutup modal dulu
        const modal = bootstrap.Modal.getInstance(document.getElementById('importModal'));
        if (modal) modal.hide();

        Swal.fire({
            title: 'Sedang Memproses...',
            html: 'Mohon tunggu sebentar, sistem sedang melakukan sinkronisasi data mitra.',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    });
</script>
@endpush
@endsection
