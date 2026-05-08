@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/pages/assignment-create.css') }}">

<div class="container-fluid px-4 pb-5">
    <div class="mb-4 mt-3">
        <a href="{{ route('assignment.index') }}" class="btn btn-light btn-sm rounded-pill px-3 fw-bold text-muted shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
        </a>
    </div>

    <form id="formAssignment" action="{{ route('assignment.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row g-4">
        {{-- SISI KIRI: DETAIL KEGIATAN --}}
<div class="col-lg-7">
    <div class="card card-assignment shadow-sm mb-4 border-0 rounded-4">
        <div class="card-body p-4">
            {{-- Header Card --}}
            <div class="d-flex align-items-center mb-4">
                <div class="bg-primary bg-opacity-10 p-3 rounded-4 me-3 text-primary">
                    <i class="fas fa-calendar-plus fa-lg"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0">Plotting Penugasan</h4>
                    <p class="text-muted small mb-0">Manajemen plotting kegiatan personil BPS Tuban.</p>
                </div>
            </div>

            {{-- 1. INFORMASI UTAMA --}}
            <div class="form-section-title"><i class="fas fa-info-circle"></i>1. Informasi Utama</div>
            
            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="small fw-bold mb-2">Asal Tim Penugasan <span class="text-danger">*</span></label>
                    <select name="team_id" class="form-select border-primary border-opacity-25 shadow-sm" required>
                        <option value="">-- Pilih Tim Penanggung Jawab --</option>
                        @foreach($teams as $team)
                            <option value="{{ $team->id }}">{{ $team->nama_tim }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6 mb-3">
                    <label class="small fw-bold mb-2">Jenis Kegiatan<span class="required-star">*</span></label>
                    <select name="activity_type_id" id="activity_type_id" class="form-select border-primary border-opacity-25" required onchange="handleActivityTypeChange()">
                        <option value="1">Tugas Lapangan</option>
                        <option value="2">Rapat Dinas</option>
                        <option value="3">Dinas Luar</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="small fw-bold mb-2">Nama Kegiatan<span class="required-star">*</span></label>
                    <input type="text" name="title" class="form-control" placeholder="Input nama kegiatan..." required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="small fw-bold mb-2">Nomor Surat Tugas<span class="required-star">*</span></label>
                    <input type="text" name="nomor_surat_tugas" id="nomor_surat_tugas" class="form-control" placeholder="Contoh: B-123/BPS/35230/..." required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6 mb-3">
                    <label class="small fw-bold mb-2">Target Laporan / Translok<span class="required-star">*</span></label>
                    <input type="number" name="report_target" id="report_target" class="form-control" value="1" min="1" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="small fw-bold mb-2">Deskripsi (Opsional)</label>
                    <textarea name="description" class="form-control" rows="1"></textarea>
                </div>
            </div>

            {{-- 2. WAKTU & DOKUMEN --}}
            <div class="form-section-title"><i class="fas fa-clock"></i>2. Waktu & Dokumen</div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="small fw-bold mb-2" id="label-event-date">Tanggal Mulai<span class="required-star">*</span></label>
                    <input type="date" name="event_date" id="event_date" class="form-control" required>
                </div>
                <div class="col-md-6" id="end-date-container">
                    <label class="small fw-bold mb-2">Tanggal Selesai<span class="required-star">*</span></label>
                    <input type="date" name="end_date" id="end_date" class="form-control" required>
                </div>
                <div class="col-md-6" id="time-field" style="display: none;">
                    <label class="small fw-bold mb-2">Jam Kegiatan<span class="required-star">*</span></label>
                    <input type="time" name="start_time" id="start_time" class="form-control">
                </div>
            </div>

            {{-- --- KOTAK NOTULIS (KHUSUS RAPAT) --- --}}
            <div id="rapat-fields" style="display: none;" class="mb-4">
                <div class="p-3 border border-warning border-opacity-25 rounded-4 bg-warning bg-opacity-10">
                    <label class="small fw-bold mb-2 text-dark"><i class="fas fa-pen-nib me-1"></i> Pilih Notulis Rapat<span class="required-star">*</span></label>
                    <select name="notulis_id" id="notulis-select" class="form-select border-warning">
                        <option value="">-- Pilih dari petugas terpilih --</option>
                        {{-- Akan diisi otomatis via JS saat petugas dipilih di sisi kanan --}}
                    </select>
                    <small class="text-muted" style="font-size: 0.65rem;">* Notulis wajib dipilih dari daftar petugas yang ditugaskan.</small>
                </div>
            </div>

            {{-- 3. DOKUMEN & PERSETUJUAN --}}
            <div class="form-section-title"><i class="fas fa-file-signature"></i>3. Dokumen & Persetujuan</div>
            <div class="mb-4 p-3 border rounded-4 bg-light">
                <label class="small fw-bold mb-3 d-block">Metode Dokumen Tugas<span class="required-star">*</span></label>
                <div class="d-flex gap-4 mb-3">
                    <div class="form-check custom-radio">
                        <input class="form-check-input" type="radio" name="mode_surat" id="modeUpload" value="upload" checked>
                        <label class="form-check-label fw-bold small" for="modeUpload">Upload PDF</label>
                    </div>
                    <div class="form-check custom-radio">
                        <input class="form-check-input" type="radio" name="mode_surat" id="modeGenerate" value="generate">
                        <label class="form-check-label fw-bold small" for="modeGenerate">Ketik Surat</label>
                    </div>
                </div>

                {{-- Section Upload --}}
                <div id="section-upload">
                    <div class="mb-0">
                        <label class="small fw-bold mb-2" id="label-upload">File PDF Surat Tugas<span class="required-star">*</span></label>
                        <input type="file" name="surat_tugas" id="surat_tugas" class="form-control" accept="application/pdf">
                    </div>
                </div>

                        <div id="section-generate" style="display: none;">
    {{-- 1. PILIHAN MODE CETAK (Hanya Muncul di SPT Lapangan) --}}
    <div class="mb-3 p-2 border rounded-3 bg-white shadow-sm" id="print-mode-container">
        <label class="small fw-bold mb-2 d-block text-primary"><i class="fas fa-print me-1"></i> Mode Output Surat</label>
        <div class="d-flex gap-3">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="print_mode" id="modePerorang" value="perorang" checked>
                <label class="form-check-label small fw-bold" for="modePerorang">Per Orang</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="print_mode" id="modeKolektif" value="kolektif">
                <label class="form-check-label small fw-bold" for="modeKolektif">Kolektif (Lampiran)</label>
            </div>
        </div>
    </div>

    {{-- 2. INPUT KHUSUS SPT (Hanya Muncul jika Tugas Lapangan) --}}
    <div id="spt-fields">
        <div class="mb-3">
            <label class="small fw-bold mb-1">Menimbang</label>
            <textarea name="menimbang" class="form-control" rows="2" placeholder="Kosongkan jika ingin format default BPS..."></textarea>
        </div>
        <div class="mb-3">
            <label class="small fw-bold mb-1">Mengingat</label>
            <textarea name="mengingat" class="form-control" rows="2" placeholder="Kosongkan jika ingin format default BPS..."></textarea>
        </div>
    </div>

    {{-- 3. INPUT KHUSUS MEMO (Hanya Muncul jika Rapat/Dinas Luar) --}}
    <div id="memo-fields" style="display: none;">
        <div class="mb-3">
            <label class="small fw-bold mb-1">Yth (Kepada)</label>
            <input type="text" name="yth" class="form-control" value="Pegawai BPS Kabupaten Tuban" placeholder="Contoh: Pegawai BPS Kabupaten Tuban">
        </div>
        <div class="mb-3">
            <label class="small fw-bold mb-1">Lokasi Kegiatan <span class="required-star">*</span></label>
            <input type="text" name="location" id="location" class="form-control" placeholder="Contoh: Ruang Rapat BPS Tuban / Cafe Bestie">
        </div>
    </div>

    {{-- 4. ISI PERINTAH / KETERANGAN --}}
    <div class="mb-3">
        <label class="small fw-bold mb-2" id="label-content-surat">Isi Perintah / Keterangan Tambahan<span class="required-star">*</span></label>
        <textarea name="content_surat" id="content_surat" class="form-control" rows="3" placeholder="Tulis rincian tugas atau agenda rapat di sini..."></textarea>
    </div>

    {{-- 5. PENGATURAN PERSETUJUAN --}}
    <div class="p-3 bg-white border rounded-4 shadow-sm">
        <label class="small fw-bold mb-3 d-block text-primary"><i class="fas fa-shield-alt me-1"></i> Pengaturan Persetujuan</label>
        <div class="d-flex gap-3 mb-3">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="approval_type" id="appSingle" value="single" checked>
                <label class="form-check-label small fw-bold" for="appSingle">Single (Kepala)</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="approval_type" id="appMultiple" value="multiple">
                <label class="form-check-label small fw-bold" for="appMultiple">Multiple (Katim & Kepala)</label>
            </div>
        </div>
        
        <div id="reviewer-container" style="display: none;" class="mb-3">
            <label class="small fw-bold mb-2">Pilih Ketua Tim (Reviewer)</label>
            <select name="reviewer_id" id="reviewer_id" class="form-select">
                <option value="">-- Pilih Katim --</option>
                @foreach($katims as $k) <option value="{{ $k->id }}">{{ $k->nama_lengkap }}</option> @endforeach
            </select>
        </div>

        <div>
            <label class="small fw-bold mb-2">Pilih Kepala BPS (Penandatangan)</label>
            <select name="approver_id" id="approver_id" class="form-select">
                <option value="">-- Pilih Kepala --</option>
                @foreach($kepalas as $k) <option value="{{ $k->id }}">{{ $k->nama_lengkap }}</option> @endforeach
            </select>
        </div>
    </div>
</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SISI KANAN: DAFTAR PETUGAS --}}
        <div class="col-lg-5">
            <div class="card card-assignment shadow-sm h-100 border-0 rounded-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="form-section-title mb-0" style="flex: 1;"><i class="fas fa-users"></i>3. Daftar Petugas</div>
                        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill fw-bold" id="btnSelectAll">Pilih Semua</button>
                    </div>
                    
                    <div class="user-selection-container shadow-sm mb-4 border rounded-3 overflow-hidden">
                        <div class="user-selection-box" style="max-height: 600px; overflow-y: auto;">
                            
                            {{-- AKUN KHUSUS (ketua.tim) --}}
                            @if($akunKhusus)
                                <div class="user-group-label bg-warning bg-opacity-10 text-dark fw-bold px-3 py-2 border-bottom">
                                    <i class="fas fa-star me-2 text-warning"></i>Akun Khusus
                                </div>
                                <div class="user-item petugas-row" onclick="togglePetugas(this, event)" data-id="{{ $akunKhusus->id }}" data-name="{{ $akunKhusus->nama_lengkap }}">
                                    <input type="checkbox" name="assigned_to[]" value="{{ $akunKhusus->id }}" class="user-check d-none">
                                    <div class="custom-chk"></div>
                                    <span class="user-name small fw-bold text-primary">{{ $akunKhusus->nama_lengkap }} ({{ $akunKhusus->username }})</span>
                                    <span class="status-badge is-busy-text d-none" id="status_busy_{{ $akunKhusus->id }}">Ada Agenda</span>
                                    <span class="status-badge is-leave-text d-none" id="status_leave_{{ $akunKhusus->id }}">Sedang Cuti</span>
                                </div>
                            @endif

                            @php $groups = ['Kepala BPS' => $kepalas, 'Ketua Tim' => $katims, 'Staf' => $pegawais]; @endphp
                            @foreach($groups as $label => $users)
                                <div class="user-group-label px-3 py-2 bg-light border-bottom border-top small fw-bold text-muted">{{ $label }}</div>
                                @foreach($users as $u)
                                    <div class="user-item petugas-row" onclick="togglePetugas(this, event)" data-id="{{ $u->id }}" data-name="{{ $u->nama_lengkap }}">
                                        <input type="checkbox" name="assigned_to[]" value="{{ $u->id }}" class="user-check d-none">
                                        <div class="custom-chk"></div>
                                        <span class="user-name small fw-bold text-dark">{{ $u->nama_lengkap }}</span>
                                        <span class="status-badge is-busy-text d-none" id="status_busy_{{ $u->id }}">Ada Agenda</span>
                                        <span class="status-badge is-leave-text d-none" id="status_leave_{{ $u->id }}">Sedang Cuti</span>
                                    </div>
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100 rounded-pill py-3 fw-bold shadow-lg" id="btnConfirmSubmit">Konfirmasi Penugasan</button>
                </div>
            </div>
        </div>
    </div>
</form>
</div>

@push('scripts')
<script>
    window.checkAvailabilityRoute = "{{ route('assignment.check-availability') }}";

    // Global Functions
    window.togglePetugas = function(el, event) {
        if (event.target.tagName === 'INPUT') return;
        const cb = el.querySelector('.user-check');
        if (cb) { cb.checked = !cb.checked; $(cb).trigger('change'); }
    };

    window.handleActivityTypeChange = function() {
        const val = $('#activity_type_id').val();
        const star = '<span class="required-star">*</span>';
        const $modeSurat = $('input[name="mode_surat"]:checked');
        $('#nomor_surat_tugas, #end_date, #start_time, #notulis-select, #surat_tugas, #location, #content_surat, #report_target').prop('required', false);

        if (val == '1') { 
            $('#spt-fields').show(); $('#memo-fields').hide(); $('#rapat-fields').hide(); $('#print-mode-container').show(); $('#end-date-container').show(); $('#time-field').hide();
            $('#label-event-date').html('Tanggal Mulai' + star); $('#label-content-surat').html('Isi Perintah Tugas' + star);
            $('#nomor_surat_tugas, #event_date, #end_date, #content_surat, #report_target').prop('required', true);
        } else {
            $('#spt-fields').hide(); $('#memo-fields').show(); $('#print-mode-container').hide(); $('#time-field').show();
            $('#label-event-date').html('Tanggal Pelaksanaan' + star); $('#nomor_surat_tugas, #event_date, #start_time, #location, #content_surat').prop('required', true);
            if (val == '2') { $('#rapat-fields').show(); $('#end-date-container').hide(); $('#notulis-select').prop('required', true); }
            else { $('#rapat-fields').hide(); $('#end-date-container').show(); $('#end_date').prop('required', true); }
        }
        window.handleModeSuratChange($modeSurat.val());
        window.checkAvailability();
    };

    window.handleModeSuratChange = function(mode) {
        const type = $('#activity_type_id').val();
        if (mode === 'generate') {
            $('#section-generate').show(); $('#section-upload').hide(); $('#content_surat, #approver_id').prop('required', true);
            if(type != '1') $('#location').prop('required', true); $('#surat_tugas').prop('required', false);
        } else {
            $('#section-generate').hide(); $('#section-upload').show(); $('#content_surat, #approver_id, #location, #notulis-select').prop('required', false); $('#surat_tugas').prop('required', true);
        }
    };

    window.checkAvailability = function() {
        const start = $('#event_date').val(); const end = $('#end_date').val() || start;
        if (start && window.checkAvailabilityRoute) {
            $.get(window.checkAvailabilityRoute, { start_date: start, end_date: end }, function(res) {
                $('.petugas-row').each(function() {
                    const id = parseInt($(this).data('id'));
                    $('#status_busy_' + id).toggleClass('d-none', !(res.busy_users && res.busy_users.includes(id)));
                    $('#status_leave_' + id).toggleClass('d-none', !(res.leave_users && res.leave_users.includes(id)));
                });
                
                // Simpan data konflik untuk validasi submit
                window.globalConflicts = res.global_conflicts || [];
                window.currentLeaveUsers = res.leave_users || [];
                window.currentBusyUsers = res.busy_users || [];
                window.busyDetails = res.details || {};
            });
        }
    };

    $(document).ready(function() {
        $('#activity_type_id').on('change', window.handleActivityTypeChange);
        $('input[name="mode_surat"]').on('change', function() { window.handleModeSuratChange($(this).val()); });
        $(document).off('change', '.user-check').on('change', '.user-check', function() {
            $(this).closest('.petugas-row').toggleClass('selected', this.checked);
            const select = $('#notulis-select'); const currentVal = select.val();
            select.html('<option value="">-- Pilih dari petugas terpilih --</option>');
            $('.user-check:checked').each(function() {
                const id = $(this).val(); const name = $(this).closest('.petugas-row').data('name');
                select.append(`<option value="${id}" ${id == currentVal ? 'selected' : ''}>${name}</option>`);
            });
        });

        $('#btnSelectAll').on('click', function() {
            const checkboxes = $('.user-check'); const isAllChecked = checkboxes.length === $('.user-check:checked').length;
            checkboxes.each(function() { this.checked = !isAllChecked; $(this).trigger('change'); });
            $(this).html(!isAllChecked ? 'Batal Semua' : 'Pilih Semua');
        });

        $('#btnConfirmSubmit').on('click', function() {
            const formEl = document.getElementById('formAssignment');
            if (!formEl.checkValidity()) { formEl.reportValidity(); return; }
            
            const selected = $('.user-check:checked');
            if (selected.length === 0) { 
                Swal.fire({ title: 'Oops!', text: 'Pilih minimal satu petugas.', icon: 'warning' }); 
                return; 
            }

            // --- CEK KONFLIK SEBELUM SUBMIT ---
            let conflictHtml = "";

            // 1. Cek Petugas yang Cuti
            let leaveNames = [];
            selected.each(function() {
                const id = parseInt($(this).val());
                if (window.currentLeaveUsers && window.currentLeaveUsers.includes(id)) {
                    leaveNames.push($(this).closest('.petugas-row').data('name'));
                }
            });
            if (leaveNames.length > 0) {
                conflictHtml += `<div class="leave-card text-start mb-3">
                    <div class="fw-bold text-danger small"><i class="fas fa-calendar-times me-1"></i> Petugas Sedang Cuti:</div>
                    <ul class="mb-0 small"><li>${leaveNames.join('</li><li>')}</li></ul>
                </div>`;
            }

            // 2. Cek Petugas yang Sibuk
            let busyNames = [];
            selected.each(function() {
                const id = parseInt($(this).val());
                if (window.currentBusyUsers && window.currentBusyUsers.includes(id)) {
                    const detail = window.busyDetails[id] ? ` (${window.busyDetails[id].title})` : '';
                    busyNames.push($(this).closest('.petugas-row').data('name') + detail);
                }
            });
            if (busyNames.length > 0) {
                conflictHtml += `<div class="conflict-card text-start mb-3">
                    <div class="fw-bold text-warning small"><i class="fas fa-exclamation-triangle me-1"></i> Bentrok Agenda Lain:</div>
                    <ul class="mb-0 small"><li>${busyNames.join('</li><li>')}</li></ul>
                </div>`;
            }

            // 3. Cek Irisan Global
            if (window.globalConflicts && window.globalConflicts.length > 0) {
                let activityNames = window.globalConflicts.map(c => `<b>${c.title}</b> (${c.range})`);
                conflictHtml += `<div class="bg-light p-3 border-start border-primary border-5 rounded-4 text-start small mt-2 shadow-sm">
                    <div class="fw-bold text-primary mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px;"><i class="fas fa-layer-group me-1"></i> KEGIATAN BERIRISAN:</div>
                    <div class="text-muted">${activityNames.join('<br>')}</div>
                </div>`;
            }

            if (conflictHtml !== "") {
                Swal.fire({
                    title: 'Konfirmasi Penugasan',
                    html: `<div class="mb-3 small text-muted">Ditemukan beberapa potensi bentrokan jadwal:</div>${conflictHtml}<div class="mt-3 fw-bold small text-dark">Apakah Anda yakin tetap ingin melanjutkan?</div>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#0058a8',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Tetap Kirim',
                    cancelButtonText: 'Batal / Cek Lagi'
                }).then((result) => {
                    if (result.isConfirmed) {
                        formEl.submit();
                    }
                });
            } else {
                formEl.submit();
            }
        });

        $('#event_date, #end_date').on('change', window.checkAvailability);
        window.handleActivityTypeChange();
    });
</script>
@endpush
@endsection