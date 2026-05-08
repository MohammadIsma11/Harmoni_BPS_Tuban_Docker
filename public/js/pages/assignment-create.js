/**
 * Logic for Assignment Creation Page
 */

// Global Function for Direct Interaction
window.togglePetugas = function(el, event) {
    if (event.target.tagName === 'INPUT') return;
    const cb = el.querySelector('.user-check');
    if (cb) {
        cb.checked = !cb.checked;
        $(cb).trigger('change');
    }
};

window.handleActivityTypeChange = function() {
    const val = $('#activity_type_id').val();
    const star = '<span class="required-star">*</span>';
    const $modeSurat = $('input[name="mode_surat"]:checked');
    
    // Reset required
    $('#nomor_surat_tugas, #end_date, #start_time, #notulis-select, #surat_tugas, #location, #content_surat, #report_target').prop('required', false);

    if (val == '1') { // Tugas Lapangan
        $('#spt-fields').show(); $('#memo-fields').hide(); $('#rapat-fields').hide(); 
        $('#print-mode-container').show(); $('#end-date-container').show(); $('#time-field').hide();
        $('#label-event-date').html('Tanggal Mulai' + star);
        $('#label-content-surat').html('Isi Perintah Tugas' + star);
        $('#nomor_surat_tugas, #event_date, #end_date, #content_surat, #report_target').prop('required', true);
    } else { // Rapat / DL
        $('#spt-fields').hide(); $('#memo-fields').show(); $('#print-mode-container').hide(); $('#time-field').show();
        $('#label-event-date').html('Tanggal Pelaksanaan' + star);
        $('#nomor_surat_tugas, #event_date, #start_time, #location, #content_surat').prop('required', true);
        
        if (val == '2') { // Rapat
            $('#rapat-fields').show(); $('#end-date-container').hide(); $('#notulis-select').prop('required', true);
        } else { // Dinas Luar
            $('#rapat-fields').hide(); $('#end-date-container').show(); $('#end_date').prop('required', true);
        }
    }
    
    // Trigger mode surat refresh
    if (typeof window.handleModeSuratChange === 'function') {
        window.handleModeSuratChange($modeSurat.val());
    }
    
    if (typeof window.checkAvailability === 'function') {
        window.checkAvailability();
    }
};

window.handleModeSuratChange = function(mode) {
    const type = $('#activity_type_id').val();
    if (mode === 'generate') {
        $('#section-generate').show(); $('#section-upload').hide();
        $('#content_surat, #approver_id').prop('required', true);
        if(type != '1') $('#location').prop('required', true);
        $('#surat_tugas').prop('required', false);
    } else {
        $('#section-generate').hide(); $('#section-upload').show();
        $('#content_surat, #approver_id, #location, #notulis-select').prop('required', false);
        $('#surat_tugas').prop('required', true);
    }
};

window.checkAvailability = function() {
    const start = $('#event_date').val();
    const end = $('#end_date').val() || start;
    if (start && window.checkAvailabilityRoute) {
        $.get(window.checkAvailabilityRoute, { start_date: start, end_date: end }, function(res) {
            $('.petugas-row').each(function() {
                const id = parseInt($(this).data('id'));
                $('#status_busy_' + id).toggleClass('d-none', !(res.busy_users && res.busy_users.includes(id)));
                $('#status_leave_' + id).toggleClass('d-none', !(res.leave_users && res.leave_users.includes(id)));
            });
        });
    }
};

window.initAssignmentCreate = function() {
    // 1. Listeners
    $('#activity_type_id').on('change', window.handleActivityTypeChange);
    $('input[name="mode_surat"]').on('change', function() { 
        window.handleModeSuratChange($(this).val()); 
    });

    $(document).off('change', '.user-check').on('change', '.user-check', function() {
        $(this).closest('.petugas-row').toggleClass('selected', this.checked);
        const select = $('#notulis-select');
        const currentVal = select.val();
        select.html('<option value="">-- Pilih dari petugas terpilih --</option>');
        $('.user-check:checked').each(function() {
            const id = $(this).val();
            const name = $(this).closest('.petugas-row').data('name');
            select.append(`<option value="${id}" ${id == currentVal ? 'selected' : ''}>${name}</option>`);
        });
    });

    $('#btnSelectAll').off('click').on('click', function() {
        const checkboxes = $('.user-check');
        const isAllChecked = checkboxes.length === $('.user-check:checked').length;
        checkboxes.each(function() { this.checked = !isAllChecked; $(this).trigger('change'); });
        $(this).html(!isAllChecked ? 'Batal Semua' : 'Pilih Semua');
    });

    $('#btnConfirmSubmit').off('click').on('click', function() {
        const formEl = document.getElementById('formAssignment');
        if (!formEl.checkValidity()) { formEl.reportValidity(); return; }
        if ($('.user-check:checked').length === 0) {
            Swal.fire({ title: 'Oops!', text: 'Pilih minimal satu petugas.', icon: 'warning' });
            return;
        }
        formEl.submit();
    });

    $('#event_date, #end_date').on('change', window.checkAvailability);
    
    // 2. Initial Run
    window.handleActivityTypeChange();
};

$(document).ready(function() {
    window.initAssignmentCreate();
});
