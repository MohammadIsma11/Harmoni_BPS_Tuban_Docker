function sinkronisasiForm() {
    const val = $('#activity_type_id').val(); // 1: Lapangan, 2: Rapat, 3: DL
    const mode = $('input[name="mode_surat"]:checked').val(); // upload / generate
    const star = '<span class="required-star">*</span>';

    // --- RESET AWAL (PENTING) ---
    // Matikan semua required agar tidak ada validasi "hantu" yang menghalangi submit
    $('#nomor_surat_tugas, #event_date, #end_date, #start_time, #notulis-select, #surat_tugas, #location, #content_surat, #report_target, #approver_id').prop('required', false);

    // --- LOGIKA BERDASARKAN JENIS KEGIATAN ---
    if (val == '1') { 
        // === TUGAS LAPANGAN (SPT) ===
        $('#spt-fields').show();      // Show: Menimbang, Mengingat
        $('#memo-fields').hide();     // Hide: Yth, Hal, Lokasi
        $('#rapat-fields').hide();    // Hide: Notulis
        $('#print-mode-container').show();

        // Waktu & Translok
        $('#end-date-container').show();
        $('#time-field').hide();
        $('#report_target').closest('.row').find('#translok-container').show(); // Translok Muncul
        
        // Labeling
        $('#label-nomor-dokumen').html('Nomor Surat Tugas (ST)' + star);
        $('#label-event-date').html('Tanggal Mulai' + star);
        $('#label-content-surat').html('Isi Perintah Tugas' + star);
        
        // Required Dasar Lapangan
        $('#nomor_surat_tugas, #event_date, #end_date, #report_target').prop('required', true);
    } 
    else { 
        // === RAPAT (2) ATAU DINAS LUAR (3) (MEMORANDUM) ===
        $('#spt-fields').hide();      // Hide: Menimbang, Mengingat
        $('#memo-fields').show();     // Show: Yth, Hal, Lokasi
        $('#print-mode-container').hide();

        // Waktu & Translok
        $('#time-field').show();
        $('#report_target').closest('.row').find('#translok-container').hide(); // Translok Sembunyi
        
        // Labeling
        $('#label-nomor-dokumen').html('Nomor Memo / Undangan' + star);
        $('#label-event-date').html('Tanggal Pelaksanaan' + star);
        $('#label-content-surat').html('Keterangan / Agenda' + star);
        
        // Required Dasar Memo
        $('#nomor_surat_tugas, #event_date, #start_time').prop('required', true);

        if (val == '2') { 
            // KHUSUS RAPAT
            $('#rapat-fields').show();       // Notulis muncul
            $('#end-date-container').hide(); // Tanggal selesai hide
        } else { 
            // KHUSUS DINAS LUAR
            $('#rapat-fields').hide();       // Notulis hide
            $('#end-date-container').show(); // Tanggal selesai show
            $('#end_date').prop('required', true);
        }
    }

    // --- LOGIKA BERDASARKAN MODE SURAT (KETIK VS UPLOAD) ---
    if (mode === 'generate') {
        $('#section-generate').show(); 
        $('#section-upload').hide();
        
        // Wajib diisi jika mode ketik
        $('#content_surat, #approver_id').prop('required', true);
        if (val != '1') $('#location').prop('required', true); // Lokasi wajib di Memo
        if (val == '2') $('#notulis-select').prop('required', true); // Notulis wajib di Rapat
    } 
    else {
        $('#section-generate').hide(); 
        $('#section-upload').show();
        
        // Jika mode upload, matikan semua required milik section ketik
        $('#content_surat, #approver_id, #location, #notulis-select').prop('required', false);
        
        // Wajibkan upload file jika draf path kosong (file baru)
        if (window.hasExistingFile === 'false') {
            $('#surat_tugas').prop('required', true);
        }
    }
}

function updateNotulisDropdown() {
    const select = $('#notulis-select');
    const currentVal = select.val() || window.savedNotulisId;
    select.html('<option value="">-- Pilih dari petugas terpilih --</option>');
    $('.user-check:checked').each(function() {
        const id = $(this).val();
        const name = $(this).closest('.petugas-row').data('name');
        select.append(`<option value="${id}" ${id == currentVal ? 'selected' : ''}>${name}</option>`);
    });
}

$(document).ready(function() {
    // 1. Trigger sinkronisasi saat ada perubahan dropdown atau radio
    $('#activity_type_id, input[name="mode_surat"]').on('change', function() {
        sinkronisasiForm();
    });

    // 2. Klik Baris Petugas (Perbaikan agar tidak double trigger)
    $(document).on('click', '.user-item', function(e) {
        if ($(e.target).is('input')) return;
        const cb = $(this).find('.user-check');
        cb.prop('checked', !cb.prop('checked')).trigger('change');
        $(this).toggleClass('selected', cb.prop('checked'));
    });

    // 3. Update Dropdown Notulis setiap kali checkbox berubah
    $(document).on('change', '.user-check', function() {
        updateNotulisDropdown();
    });

    // 4. Tombol Pilih Semua
    $('#btnSelectAll').on('click', function() {
        const checkboxes = $('.user-check');
        const isAllChecked = checkboxes.length === $('.user-check:checked').length;
        checkboxes.prop('checked', !isAllChecked).trigger('change');
        $('.user-item').toggleClass('selected', !isAllChecked);
        $(this).html(!isAllChecked ? '<i class="fas fa-times me-1"></i> Batal Semua' : '<i class="fas fa-check-double me-1"></i> Pilih Semua');
    });

    // --- LIVE SEARCH PETUGAS ---
    $('#searchPetugas').on('keyup', function() {
        const val = $(this).val().toLowerCase();
        $('#petugasList .petugas-row').each(function() {
            const name = $(this).data('name').toLowerCase();
            $(this).toggle(name.includes(val));
        });
        // Sembunyikan label grup jika semua anggotanya terfilter
        $('.user-group-label').each(function() {
            let hasVisible = $(this).nextUntil('.user-group-label', '.petugas-row:visible').length > 0;
            $(this).toggle(hasVisible);
        });
    });

    function checkAvailability() {
        const start = $('#event_date').val();
        const end = $('#end_date').val() || start;
        if (start) {
            $.get(window.checkAvailabilityRoute, { start_date: start, end_date: end, _t: Date.now() }, function(res) {
                // Update Badge di Daftar Petugas
                $('.petugas-row').each(function() {
                    const id = parseInt($(this).data('id'));
                    
                    // 1. Mark Busy (Ada Agenda Lain)
                    const isBusy = res.busy_users && res.busy_users.includes(id);
                    $('#status_busy_' + id).toggleClass('d-none', !isBusy);

                    // 2. Mark Leave (Sedang Cuti)
                    const isLeave = res.leave_users && res.leave_users.includes(id);
                    $('#status_leave_' + id).toggleClass('d-none', !isLeave);
                });

                // Simpan data konflik global untuk divalidasi saat submit
                window.globalConflicts = res.global_conflicts || [];
                window.currentLeaveUsers = res.leave_users || [];
                window.currentBusyUsers = res.busy_users || [];
                window.busyDetails = res.details || {};
            });
        }
    }

    $('#btnConfirmSubmit').on('click', function() {
        const form = document.getElementById('formAssignment');
        
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const selected = $('.user-check:checked');
        if (selected.length === 0) {
            Swal.fire({ title: 'Petugas Belum Dipilih!', text: 'Pilih minimal satu petugas di daftar kanan.', icon: 'warning', confirmButtonColor: '#0058a8' });
            return;
        }

        // --- VALIDASI TANGGAL (SELESAI >= MULAI) ---
        const startVal = $('#event_date').val();
        const endVal = $('#end_date').val();
        const activityType = $('#activity_type_id').val();

        if ((activityType == 1 || activityType == 3) && endVal < startVal) {
            Swal.fire({ 
                title: 'Kesalahan Tanggal', 
                text: 'Tanggal selesai tidak boleh kurang dari tanggal mulai.', 
                icon: 'error',
                confirmButtonColor: '#0058a8'
            });
            return;
        }

        // --- CEK KONFLIK SEBELUM SUBMIT ---
        let conflictHtml = "";

        // 1. Cek Petugas yang Cuti
        let leaveNames = [];
        selected.each(function() {
            const id = parseInt($(this).val());
            if (window.currentLeaveUsers.includes(id)) {
                leaveNames.push($(this).closest('.petugas-row').data('name'));
            }
        });

        if (leaveNames.length > 0) {
            conflictHtml += `<div class="leave-card text-start mb-3">
                <div class="fw-bold text-danger small"><i class="fas fa-calendar-times me-1"></i> Petugas Sedang Cuti:</div>
                <ul class="mb-0 small"><li>${leaveNames.join('</li><li>')}</li></ul>
            </div>`;
        }

        // 2. Cek Petugas yang Sibuk (Bentrok Agenda Lain)
        // Catatan: Di edit, kita mengabaikan bentrok dengan agenda yang sedang kita edit ini sendiri.
        // Karena kita tidak punya ID agenda di checkAvailability (tapi checkAvailability hanya membalas list user),
        // ini mungkin menunjukkan "Busy" untuk tugas ini sendiri. 
        // Namun untuk kesederhanaan, kita tampilkan saja peringatannya.
        let busyNames = [];
        selected.each(function() {
            const id = parseInt($(this).val());
            if (window.currentBusyUsers.includes(id)) {
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

        // 3. Cek Tabrakan Kegiatan Lapangan Global (Irisan)
        if (window.globalConflicts && window.globalConflicts.length > 0) {
            let activityNames = window.globalConflicts.map(c => `<b>${c.title}</b> (${c.range})`);
            conflictHtml += `<div class="bg-light p-2 border rounded text-start small mt-2">
                <div class="fw-bold text-muted mb-1 font-monospace" style="font-size: 0.65rem;">HARI BERIRISAN DENGAN:</div>
                ${activityNames.join('<br>')}
            </div>`;
        }

        if (conflictHtml !== "") {
            Swal.fire({
                title: 'Konfirmasi Perubahan Konflik',
                html: `<div class="mb-3 small text-muted">Ditemukan beberapa potensi bentrokan jadwal:</div>${conflictHtml}<div class="mt-3 fw-bold small text-dark">Apakah Anda yakin tetap ingin menyimpan perubahan ini?</div>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0058a8',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Simpan Perubahan',
                cancelButtonText: 'Batal / Cek Lagi'
            }).then((result) => {
                if (result.isConfirmed) {
                    processSubmit(form);
                }
            });
        } else {
            processSubmit(form);
        }
    });

    function processSubmit(form) {
        Swal.fire({
            title: 'Menyimpan...',
            text: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });
        form.submit();
    }

    // --- LOGIKA TANGGAL ---
    $('#event_date, #end_date').on('change', function() {
        $('#end_date').attr('min', $('#event_date').val());
        if($('#activity_type_id').val() == '2') {
            $('#end_date').val($('#event_date').val());
        }
        checkAvailability();
    });

    // Jalankan checkAvailability saat pertama kali load untuk menandai status awal
    checkAvailability();

    // Jalankan sinkronisasi saat pertama kali halaman dimuat (agar data edit ter-load)
    sinkronisasiForm();
    updateNotulisDropdown();
});
