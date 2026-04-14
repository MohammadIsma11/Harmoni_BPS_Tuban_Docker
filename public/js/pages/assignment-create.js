$(document).ready(function() {
    // Inisialisasi variabel global
    window.currentAgendaDetails = {};
    window.currentLeaveUsers = [];
    window.globalConflicts = [];

    // --- 1. FUNGSI LOGIKA FORM UTAMA ---
    $('#activity_type_id').on('change', function() {
        const val = $(this).val();
        const star = '<span class="required-star">*</span>';

        // RESET SEMUA REQUIRED AWAL (PENTING AGAR TIDAK STUCK)
        $('#nomor_surat_tugas, #end_date, #start_time, #notulis-select, #surat_tugas, #location, #content_surat, #report_target').prop('required', false);

        if (val == '1') { 
            // === TUGAS LAPANGAN (SPT) ===
            $('#spt-fields').slideDown();
            $('#memo-fields').slideUp();
            $('#rapat-fields').hide(); 
            $('#print-mode-container').show();
            
            // Show: Tanggal Mulai, Tanggal Selesai, Translok. Hide: Jam.
            $('#end-date-container').show();
            $('#time-field').hide();
            $('#report_target').closest('.col-md-6').show(); // Translok muncul
            
            // Update Label
            $('#label-event-date').html('Tanggal Mulai' + star);
            $('#label-content-surat').html('Isi Perintah Tugas' + star);
            
            // Pasang Required
            $('#nomor_surat_tugas, #event_date, #end_date, #content_surat, #report_target').prop('required', true);
        } 
        else { 
            // === RAPAT (2) ATAU DINAS LUAR (3) (MEMORANDUM) ===
            $('#spt-fields').slideUp();
            $('#memo-fields').slideDown();
            $('#print-mode-container').hide();
            
            // Show: Jam, Tanggal Pelaksanaan. Hide: Translok.
            $('#time-field').show();
            $('#report_target').closest('.col-md-6').hide(); // Translok sembunyi
            
            // Update Label
            $('#label-event-date').html('Tanggal Pelaksanaan' + star);
            $('#label-content-surat').html('Keterangan / Agenda' + star);
            
            // Required Dasar Memo
            $('#nomor_surat_tugas, #event_date, #start_time, #location, #content_surat').prop('required', true);

            if (val == '2') { 
                // KHUSUS RAPAT DINAS
                $('#rapat-fields').slideDown(); // Notulis muncul
                $('#end-date-container').hide(); // Selesai sembunyi
                $('#notulis-select').prop('required', true);
            } else { 
                // KHUSUS DINAS LUAR
                $('#rapat-fields').hide(); // Notulis sembunyi
                $('#end-date-container').show(); // Selesai muncul (karena DL bisa berhari-hari)
                $('#end_date').prop('required', true);
            }
        }

        // Jalankan ulang logika mode surat (Upload vs Ketik)
        $('input[name="mode_surat"]:checked').trigger('change');
        if(typeof checkAvailability === "function") checkAvailability();
    });

    // --- 2. LOGIKA TOGGLE MODE SURAT (UPLOAD vs KETIK) ---
    $('input[name="mode_surat"]').on('change', function() {
        const mode = $(this).val();
        const type = $('#activity_type_id').val();

        if (mode === 'generate') {
            $('#section-generate').slideDown();
            $('#section-upload').slideUp();
            
            // Required Ketik
            $('#content_surat, #approver_id').prop('required', true);
            if(type != '1') $('#location').prop('required', true);
            
            $('#surat_tugas').prop('required', false);
        } else {
            $('#section-generate').slideUp();
            $('#section-upload').slideDown();
            
            // Bersihkan Required Ketik (Supaya tidak menghalangi mode upload)
            $('#content_surat, #approver_id, #location, #notulis-select, #reviewer_id, #yth, #mengingat, #menimbang').prop('required', false);
            
            $('#surat_tugas').prop('required', true);
        }
    });

    // --- 3. LOGIKA PILIH PETUGAS & CENTANG (FIXED) ---
    $(document).on('click', '.user-item', function(e) {
        if ($(e.target).is('input')) return; 
        const cb = $(this).find('.user-check');
        cb.prop('checked', !cb.prop('checked')).trigger('change');
    });

    $(document).on('change', '.user-check', function() {
        updateNotulisDropdown();
    });

    $('#btnSelectAll').on('click', function() {
        const checkboxes = $('.user-check');
        const isAllChecked = checkboxes.length === $('.user-check:checked').length;
        checkboxes.prop('checked', !isAllChecked).trigger('change');
        $(this).html(!isAllChecked ? '<i class="fas fa-times me-1"></i> Batal Semua' : '<i class="fas fa-check-double me-1"></i> Pilih Semua');
    });

    function updateNotulisDropdown() {
        const select = $('#notulis-select');
        const currentVal = select.val();
        select.html('<option value="">-- Pilih dari petugas terpilih --</option>');
        $('.user-check:checked').each(function() {
            const id = $(this).val();
            const name = $(this).closest('.petugas-row').data('name');
            select.append(`<option value="${id}" ${id == currentVal ? 'selected' : ''}>${name}</option>`);
        });
    }

    function checkAvailability() {
        const start = $('#event_date').val();
        const end = $('#end_date').val() || start;
        console.log("Checking availability for:", start, "to", end);
        if (start) {
            $.get(window.checkAvailabilityRoute, { start_date: start, end_date: end, _t: Date.now() }, function(res) {
                console.log("Availability Result:", res);
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

    // --- 4. VALIDASI & SUBMIT (ENHANCED) ---
    $('#btnConfirmSubmit').on('click', function() {
        console.log("Submit clicked. Checking conflicts...");
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

        // --- CEK KONFLIK SEBELUM SUBMIT ---
        let conflictHtml = "";
        let hasHardConflict = false;

        // 1. Cek Petugas yang Cuti
        let leaveNames = [];
        selected.each(function() {
            const id = parseInt($(this).val());
            if (window.currentLeaveUsers.includes(id)) {
                leaveNames.push($(this).closest('.petugas-row').data('name'));
                hasHardConflict = true;
            }
        });

        if (leaveNames.length > 0) {
            conflictHtml += `<div class="leave-card text-start mb-3">
                <div class="fw-bold text-danger small"><i class="fas fa-calendar-times me-1"></i> Petugas Sedang Cuti:</div>
                <ul class="mb-0 small"><li>${leaveNames.join('</li><li>')}</li></ul>
            </div>`;
        }

        // 2. Cek Petugas yang Sibuk (Bentrok Agenda Lain)
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
            console.warn("Conflicts found:", conflictHtml);
            Swal.fire({
                title: 'Konfirmasi Konflik Jadwal',
                html: `<div class="mb-3 small text-muted">Ditemukan beberapa potensi bentrokan jadwal petugas:</div><div class="conflict-summary-wrapper">${conflictHtml}</div><div class="mt-3 fw-bold small text-dark">Apakah Anda yakin tetap ingin melanjutkan penugasan ini?</div>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0058a8',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Tetap Lanjutkan',
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
            title: 'Memproses...',
            text: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });
        form.submit();
    }

    // --- 5. LOGIKA TANGGAL ---
    $('#event_date, #end_date').on('change', function() {
        $('#end_date').attr('min', $('#event_date').val());
        if($('#activity_type_id').val() == '2') {
            $('#end_date').val($('#event_date').val());
        }
        checkAvailability();
    });

    // --- INITIALIZE ---
    $('#activity_type_id').trigger('change');
    checkAvailability(); // Jalankan awal jika sudah ada tanggal (autofill)

    $('input[name="approval_type"]').on('change', function() {
        if ($(this).val() === 'multiple') {
            $('#reviewer-container').slideDown();
            $('#reviewer_id').prop('required', true);
        } else {
            $('#reviewer-container').slideUp();
            $('#reviewer_id').prop('required', false);
        }
    });
});
