@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/pages/absensi-index.css') }}">

{{-- Small config script for external JS --}}
<script>
    window.absensiRoutes = {
        store: "{{ route('absensi.store') }}",
        update: "{{ route('absensi.update', ':id') }}",
        destroy: "{{ route('absensi.destroy', ':id') }}"
    };
</script>
<script src="{{ asset('js/pages/absensi-index.js') }}"></script>

<div class="container-fluid px-4 pb-5">
    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3 mt-3">
        <div>
            <h4 class="fw-bold text-dark mb-1">Timeline Kehadiran Pegawai</h4>
            <p class="text-muted small mb-0">Sub Bagian Umum &bull; Simbol: PD (Dinas), CT (Cuti), CST1 (Cuti 1/2 Hari)</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn-import shadow-sm" data-bs-toggle="modal" data-bs-target="#modalImportCSV">
                <i class="fas fa-file-excel me-2"></i> Import Presensi BPS
            </button>
            <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalInputAbsensi">
                <i class="fas fa-plus me-2"></i> Input Manual
            </button>
        </div>
    </div>

    {{-- Control Panel --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3 d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="d-flex gap-2">
                <div class="btn-group p-1 bg-light rounded-pill">
                    <a href="{{ route('absensi.index', ['view' => 'weekly', 'month' => $currentMonth->format('Y-m')]) }}" class="btn btn-sm rounded-pill px-3 {{ $view != 'monthly' ? 'bg-white shadow-sm fw-bold' : 'text-muted' }}">Mingguan</a>
                    <a href="{{ route('absensi.index', ['view' => 'monthly', 'month' => $currentMonth->format('Y-m')]) }}" class="btn btn-sm rounded-pill px-3 {{ $view == 'monthly' ? 'bg-white shadow-sm fw-bold' : 'text-muted' }}">Bulanan</a>
                </div>
                <button id="btnFilterCuti" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold">
                    <i class="fas fa-filter me-1"></i> Hanya Berhalangan
                </button>
            </div>
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('absensi.index', ['month' => $prevMonth, 'view' => $view]) }}" class="btn btn-light btn-sm rounded-circle"><i class="fas fa-chevron-left"></i></a>
                <span class="fw-bold text-dark text-uppercase small">{{ $currentMonth->translatedFormat('F Y') }}</span>
                <a href="{{ route('absensi.index', ['month' => $nextMonth, 'view' => $view]) }}" class="btn btn-light btn-sm rounded-circle"><i class="fas fa-chevron-right"></i></a>
            </div>
        </div>
    </div>

    {{-- Timeline Table --}}
    <div class="timeline-container">
        <div class="table-responsive">
            <table class="table timeline-table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="sticky-col">Nama Pegawai</th>
                        @foreach($period as $date)
                            <th class="date-cell {{ $date->isWeekend() ? 'is-weekend' : '' }} {{ $date->isToday() ? 'is-today' : '' }}">
                                <span class="date-day text-uppercase">{{ $date->translatedFormat('D') }}</span>
                                <span class="date-number">{{ $date->format('d') }}</span>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody id="timelineBody">
                    @foreach($users as $user)
                    @php 
                        $userLeaves = $allCuti->where('user_id', $user->id);
                        $hasLeaveThisPeriod = $userLeaves->count() > 0;
                    @endphp
                    <tr class="pegawai-row" data-has-leave="{{ $hasLeaveThisPeriod ? 'true' : 'false' }}">
                        <td class="sticky-col">
                            <div class="d-flex align-items-center">
                                <div class="avatar-box me-2" style="width: 32px; height: 32px; border-radius: 10px; background: var(--bps-blue); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 0.75rem;">
                                    {{ strtoupper(substr($user->nama_lengkap, 0, 1)) }}
                                </div>
                                <div class="lh-1">
                                    <span class="fw-bold text-dark d-block mb-1" style="font-size: 0.75rem;">{{ $user->nama_lengkap }}</span>
                                    <span class="badge bg-light text-muted border" style="font-size: 0.55rem;">{{ $user->team->nama_tim ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </td>
                        @foreach($period as $date)
                            @php 
                                $currentStr = $date->format('Y-m-d');
                                $statusDate = $userLeaves->filter(function($item) use ($currentStr) {
                                    $s = \Carbon\Carbon::parse($item->start_date)->format('Y-m-d');
                                    $e = \Carbon\Carbon::parse($item->end_date)->format('Y-m-d');
                                    return ($currentStr >= $s && $currentStr <= $e);
                                })->first();
                            @endphp
                            <td class="date-cell {{ $date->isWeekend() ? 'is-weekend' : '' }} {{ $date->isToday() ? 'is-today' : '' }}">
                                @if($statusDate)
                                    @php $cleanStatus = strtolower(trim($statusDate->status)); @endphp
                                    <div class="leave-bar status-{{ $cleanStatus }} btn-edit-absensi" 
                                         data-id="{{ $statusDate->id }}"
                                         data-user-id="{{ $user->id }}"
                                         data-start="{{ $statusDate->start_date }}"
                                         data-end="{{ $statusDate->end_date }}"
                                         data-status="{{ $statusDate->status }}"
                                         data-ket="{{ $statusDate->keterangan }}"
                                         data-bs-toggle="tooltip"
                                         title="{{ $statusDate->status }}: {{ $statusDate->keterangan ?? 'Tanpa keterangan' }}">
                                        {{ strtoupper($statusDate->status) }}
                                    </div>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL IMPORT --}}
<div class="modal fade" id="modalImportCSV" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold"><i class="fas fa-file-csv me-2 text-success"></i>Sinkronisasi Presensi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('absensi.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Pilih Bulan Data</label>
                        <input type="month" name="year_month" class="form-control rounded-3" value="{{ $currentMonth->format('Y-m') }}" required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-bold">File Presensi (.csv atau .xlsx)</label>
                        <input type="file" name="file_import" class="form-control rounded-3" accept=".csv, .xlsx, .xls" required>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="submit" class="btn btn-success rounded-pill px-4 w-100 fw-bold shadow">
                        <i class="fas fa-sync me-2"></i>Mulai Sinkronisasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL INPUT/EDIT --}}
<div class="modal fade" id="modalInputAbsensi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold" id="modalTitle"><i class="fas fa-user-edit me-2 text-primary"></i>Input Manual</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formAbsensi" action="{{ route('absensi.store') }}" method="POST">
                @csrf
                <div id="methodField"></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Pilih Pegawai</label>
                        <select name="user_id" id="edit_user_id" class="form-select rounded-3 shadow-sm" required>
                            <option value="">-- Pilih Nama --</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label small fw-bold">Mulai</label>
                            <input type="date" name="start_date" id="edit_start_date" class="form-control rounded-3 shadow-sm" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label small fw-bold">Sampai</label>
                            <input type="date" name="end_date" id="edit_end_date" class="form-control rounded-3 shadow-sm" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Status</label>
                        <select name="status" id="edit_status" class="form-select rounded-3 shadow-sm" required>
                            <option value="CT">CT (Cuti Full)</option>
                            <option value="CST1">CST1 (Cuti Setengah Hari)</option>
                            <option value="PD">PD (Perjalanan Dinas)</option>
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-bold">Keterangan</label>
                        <textarea name="keterangan" id="edit_keterangan" class="form-control rounded-3 shadow-sm" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 d-flex flex-column gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill px-4 w-100 fw-bold shadow">
                        <i class="fas fa-save me-2"></i>Simpan Data
                    </button>
                    <button type="button" id="btnDeleteAbsensi" class="btn btn-link text-danger text-decoration-none fw-bold w-100 d-none">
                        <i class="fas fa-trash-alt me-1"></i> Hapus Data Ini
                    </button>
                </div>
            </form>
            <form id="formDelete" action="" method="POST" class="d-none">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection