@extends('layouts.app')

@section('content')
{{-- SweetAlert2 CDN --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    :root { --bps-blue: #0058a8; --bps-text: #1e293b; }
    .table-container { background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.02); overflow: hidden; border: 1px solid #f1f5f9; }
    
    .table thead th { 
        background: #f8fafc; color: #64748b; font-size: 0.75rem; 
        text-transform: uppercase; letter-spacing: 1px; padding: 20px 15px; border-bottom: 1px solid #f1f5f9;
    }

    .table tbody td { padding: 18px 15px; border-bottom: 1px solid #f8fafc; }
    
    .btn-action { width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center; border-radius: 12px; transition: 0.3s; border: none; text-decoration: none; }
    .btn-edit { background: #eff6ff; color: #2563eb; }
    .btn-edit:hover { background: #2563eb; color: white; transform: translateY(-2px); }
    .btn-delete { background: #fff1f2; color: #e11d48; }
    .btn-delete:hover { background: #e11d48; color: white; transform: translateY(-2px); }
    
    .petugas-stack { display: flex; align-items: center; }
    .avatar-stack-item { 
        width: 32px; height: 32px; border-radius: 10px; background: var(--bps-blue); 
        color: white; font-size: 0.75rem; display: flex; align-items: center; justify-content: center;
        font-weight: bold; border: 2px solid white; margin-left: -12px;
        box-shadow: 2px 0 5px rgba(0,0,0,0.1);
    }
    .avatar-stack-item:first-child { margin-left: 0; }
    
    .activity-icon-box {
        width: 44px; height: 44px; display: flex; align-items: center; justify-content: center;
        border-radius: 14px; flex-shrink: 0; font-size: 1.1rem;
    }

    .badge-jadwal {
        padding: 8px 16px; border-radius: 50px; font-weight: 700; font-size: 0.75rem;
        display: inline-flex; align-items: center;
    }
</style>

<div class="container-fluid px-4 pb-5">
    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3 mt-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">Manajemen Penugasan</h4>
            <p class="text-muted small mb-0">Monitor rangkaian kegiatan dan distribusi petugas seluruh tim.</p>
        </div>
        <a href="{{ route('assignment.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm py-2">
            <i class="fas fa-plus me-2"></i> Buat Penugasan
        </a>
    </div>

    {{-- Table Card --}}
    <div class="card border-0 shadow-sm table-container">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Nama & Jenis Kegiatan</th>
                        <th>Petugas Terlibat</th>
                        <th class="text-center">Jadwal & Keterangan</th>
                        <th class="text-center" width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignments as $group)
                        @php
                            $first = $group->first();
                            $totalPetugas = $group->count();
                            $namaPetugas = $group->pluck('assignee.nama_lengkap')->implode(', ');
                            
                            $isLapangan = $first->activity_type_id == 1;
                            $eventDate = \Carbon\Carbon::parse($first->event_date);
                            $endDate = \Carbon\Carbon::parse($first->end_date);

                            // Mapping Icon dan Teks Keterangan
                            $typeData = match($first->activity_type_id) {
                                1 => ['icon' => 'fa-map-location-dot', 'color' => '#10b981', 'bg' => '#ecfdf5', 'label' => 'Tugas Lapangan'],
                                2 => ['icon' => 'fa-users-rectangle', 'color' => '#3b82f6', 'bg' => '#eff6ff', 'label' => 'Rapat'],
                                3 => ['icon' => 'fa-plane-departure', 'color' => '#f59e0b', 'bg' => '#fffbeb', 'label' => 'Dinas Luar'],
                                default => ['icon' => 'fa-tag', 'color' => '#64748b', 'bg' => '#f8fafc', 'label' => 'Kegiatan Umum'],
                            };
                        @endphp
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="activity-icon-box me-3 shadow-sm" style="background-color: {{ $typeData['bg'] }}; color: {{ $typeData['color'] }};">
                                        <i class="fas {{ $typeData['icon'] }}"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark mb-0" style="font-size: 0.95rem;">{{ $first->title }}</div>
                                        {{-- Teks Jenis Kegiatan di bawah judul --}}
                                        <small class="fw-bold text-uppercase" style="font-size: 0.65rem; color: {{ $typeData['color'] }}; letter-spacing: 0.5px;">
                                            {{ $typeData['label'] }}
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="petugas-stack me-3">
                                        @foreach($group->take(4) as $assignee)
                                            <div class="avatar-stack-item" title="{{ $assignee->assignee->nama_lengkap }}">
                                                {{ strtoupper(substr($assignee->assignee->nama_lengkap ?? 'U', 0, 1)) }}
                                            </div>
                                        @endforeach
                                        @if($totalPetugas > 4)
                                            <div class="avatar-stack-item bg-light text-muted" style="font-size: 0.6rem;">
                                                +{{ $totalPetugas - 4 }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="lh-1">
                                        <span class="small fw-bold d-block text-dark">{{ $totalPetugas }} Orang</span>
                                        <small class="text-muted text-truncate d-inline-block" style="max-width: 140px;" title="{{ $namaPetugas }}">
                                            {{ $namaPetugas }}
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="badge-jadwal bg-light border text-dark shadow-sm">
                                    <i class="far fa-clock me-2 text-primary"></i>
                                    @if($isLapangan)
                                        {{ $eventDate->translatedFormat('d M') }} - {{ $endDate->translatedFormat('d M Y') }}
                                    @else
                                        {{ $eventDate->translatedFormat('d F Y') }}
                                    @endif
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('assignment.edit', $first->id) }}" class="btn-action btn-edit shadow-sm" title="Edit Rangkaian">
                                        <i class="fas fa-pen-to-square"></i>
                                    </a>

                                    <form action="{{ route('assignment.destroy', $first->id) }}" method="POST" id="delete-form-{{ $first->id }}">
                                        @csrf 
                                        @method('DELETE')
                                        <button type="button" class="btn-action btn-delete btn-confirm-delete shadow-sm" 
                                                data-id="{{ $first->id }}" 
                                                data-title="{{ $first->title }}">
                                            <i class="fas fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <img src="https://illustrations.popsy.co/blue/waiting.svg" style="width: 180px;" class="mb-3">
                                <h6 class="text-muted fw-bold">Belum ada daftar penugasan.</h6>
                                <p class="small text-muted">Klik tombol "Buat Penugasan" untuk memulai.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('.btn-confirm-delete').on('click', function() {
            const id = $(this).data('id');
            const title = $(this).data('title');
            const form = $('#delete-form-' + id);

            Swal.fire({
                title: 'Hapus Penugasan?',
                text: `Seluruh data petugas untuk "${title}" akan ikut terhapus.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                customClass: { popup: 'rounded-4' }
            }).then((result) => {
                if (result.isConfirmed) form.submit();
            });
        });

        @if(session('success'))
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: "{{ session('success') }}", timer: 2000, showConfirmButton: false, customClass: { popup: 'rounded-4' } });
        @endif
    });
</script>
@endsection