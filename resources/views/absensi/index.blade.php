@extends('layouts.app')

@section('content')
<style>
    :root { --bps-blue: #0058a8; }
    .timeline-container { background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); overflow: hidden; }
    .timeline-table { border-collapse: separate; border-spacing: 0; width: 100%; }
    
    .sticky-col { 
        position: sticky; left: 0; background: white; z-index: 10; 
        border-right: 2px solid #f1f5f9; min-width: 220px; padding: 12px 20px !important;
    }
    
    .timeline-table thead th { 
        background: #f8fafc; color: #64748b; font-size: 0.7rem; 
        text-transform: uppercase; letter-spacing: 1px; padding: 15px 10px; border: none;
    }

    .date-cell { min-width: 45px; text-align: center; border-left: 1px solid #f1f5f9 !important; }
    .date-number { font-size: 0.9rem; font-weight: 800; display: block; }
    .date-day { font-size: 0.6rem; opacity: 0.7; }
    
    .is-weekend { background-color: #fff1f2 !important; color: #e11d48 !important; }
    .is-today { background-color: #eff6ff !important; color: var(--bps-blue) !important; border-bottom: 3px solid var(--bps-blue) !important; }

    .leave-bar {
        height: 26px; width: 26px; border-radius: 7px; display: flex; align-items: center; 
        justify-content: center; font-size: 0.75rem; font-weight: 800; color: white;
        margin: 0 auto; cursor: pointer; transition: 0.2s;
    }

    .status-cuti { background: #ef4444 !important; }
    .status-ct1 { background: #f43f5e !important; } /* Warna khusus CT1 */
    .status-dl { background: #f59e0b !important; }
    .status-izin { background: #6366f1 !important; }
    .status-sakit { background: #10b981 !important; }

    .filter-active { background: var(--bps-blue) !important; color: white !important; }
    
    .btn-import { background: #2ecc71; color: white; border: none; font-weight: bold; border-radius: 50px; padding: 8px 20px; transition: 0.3s; }
    .btn-import:hover { background: #27ae60; color: white; transform: translateY(-2px); }
</style>

<div class="container-fluid px-4 pb-5">
    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3 mt-3">
        <div>
            <h4 class="fw-bold text-dark mb-1">Timeline Kehadiran Pegawai</h4>
            <p class="text-muted small mb-0">Sub Bagian Umum &bull; Gatekeeper Penugasan Lapangan</p>
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
                                    <div class="leave-bar status-{{ strtolower(trim($statusDate->status)) }}" 
                                         data-bs-toggle="tooltip"
                                         title="{{ $statusDate->status }}: {{ $statusDate->keterangan ?? 'Tanpa keterangan' }}">
                                        {{ strtoupper(substr($statusDate->status, 0, 1)) }}
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

{{-- MODAL IMPORT CSV (FIXED SUBMIT) --}}
<div class="modal fade" id="modalImportCSV" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold"><i class="fas fa-file-csv me-2 text-success"></i>Sinkronisasi Presensi BPS</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            {{-- PASTIKAN ACTION DAN METHOD BENAR --}}
            <form action="{{ route('absensi.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Pilih Bulan Data</label>
                        <input type="month" name="year_month" class="form-control rounded-3" value="{{ $currentMonth->format('Y-m') }}" required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-bold">File Presensi (.csv atau .xlsx)</label>
                        {{-- NAME HARUS 'file_import' SESUAI CONTROLLER --}}
                        <input type="file" name="file_import" class="form-control rounded-3" accept=".csv, .xlsx, .xls" required>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    {{-- TYPE HARUS SUBMIT --}}
                    <button type="submit" class="btn btn-success rounded-pill px-4 w-100 fw-bold shadow">
                        <i class="fas fa-sync me-2"></i>Mulai Sinkronisasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL INPUT MANUAL --}}
<div class="modal fade" id="modalInputAbsensi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold"><i class="fas fa-user-edit me-2 text-primary"></i>Input Manual</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('absensi.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Pilih Pegawai</label>
                        <select name="user_id" class="form-select rounded-3 shadow-sm" required>
                            <option value="">-- Pilih Nama --</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label small fw-bold">Mulai</label>
                            <input type="date" name="start_date" class="form-control rounded-3 shadow-sm" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label small fw-bold">Sampai</label>
                            <input type="date" name="end_date" class="form-control rounded-3 shadow-sm" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Status</label>
                        <select name="status" class="form-select rounded-3 shadow-sm" required>
                            <option value="Cuti">Cuti</option>
                            <option value="CT1">Cuti Setengah Hari</option>
                            <option value="DL">Dinas Luar (DL)</option>
                            <option value="Izin">Izin</option>
                            <option value="Sakit">Sakit</option>
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-bold">Keterangan</label>
                        <textarea name="keterangan" class="form-control rounded-3 shadow-sm" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="submit" class="btn btn-primary rounded-pill px-4 w-100 fw-bold shadow">
                        <i class="fas fa-save me-2"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        const btnFilter = document.getElementById('btnFilterCuti');
        btnFilter.addEventListener('click', function() {
            this.classList.toggle('filter-active');
            const isFiltering = this.classList.contains('filter-active');
            document.querySelectorAll('.pegawai-row').forEach(row => {
                row.style.display = isFiltering ? (row.getAttribute('data-has-leave') === 'true' ? '' : 'none') : '';
            });
        });
    });
</script>
@endsection