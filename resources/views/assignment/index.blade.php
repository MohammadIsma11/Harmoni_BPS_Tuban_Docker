@extends('layouts.app')

@section('content')
{{-- SweetAlert2 CDN --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<link rel="stylesheet" href="{{ asset('css/pages/assignment-index.css') }}">

<div class="container-fluid px-4 pb-5">
    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3 mt-4 animate-up">
        <div>
            <h4 class="fw-bold text-dark mb-1">Manajemen Penugasan</h4>
            <p class="text-muted small mb-0">Monitor rangkaian kegiatan dan distribusi petugas seluruh tim.</p>
        </div>
        <a href="{{ route('assignment.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm py-2">
            <i class="fas fa-plus me-2"></i> Buat Penugasan
        </a>
    </div>

    {{-- Table Card --}}
    <div class="card border-0 shadow-sm table-container animate-up">
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
<script src="{{ asset('js/pages/assignment-index.js') }}"></script>
@endsection