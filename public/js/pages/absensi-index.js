document.addEventListener('DOMContentLoaded', function() {
    // Init Tooltip
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    const modalAbsensiElement = document.getElementById('modalInputAbsensi');
    if (!modalAbsensiElement) return;

    const modalAbsensi = new bootstrap.Modal(modalAbsensiElement);
    const formAbsensi = document.getElementById('formAbsensi');
    const methodField = document.getElementById('methodField');
    const btnDelete = document.getElementById('btnDeleteAbsensi');
    const formDelete = document.getElementById('formDelete');

    // Filter Berhalangan
    $('#btnFilterCuti').on('click', function() {
        $(this).toggleClass('filter-active');
        const isFiltering = $(this).hasClass('filter-active');
        $('.pegawai-row').each(function() {
            const hasLeave = $(this).data('has-leave') === true;
            if (isFiltering) {
                $(this).toggle(hasLeave);
            } else {
                $(this).show();
            }
        });
    });

    // Edit Klik Bar
    $(document).on('click', '.btn-edit-absensi', function() {
        const data = $(this).data();
        const id = data.id;

        $('#modalTitle').html('<i class="fas fa-edit me-2 text-warning"></i>Edit Data Kehadiran');
        
        // URLs are passed from the Blade global variable window.absensiRoutes
        let updateUrl = window.absensiRoutes.update.replace(':id', id);
        formAbsensi.action = updateUrl;
        methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';

        $('#edit_user_id').val(data.userId);
        $('#edit_start_date').val(data.startDate || data.start);
        $('#edit_end_date').val(data.endDate || data.end);
        $('#edit_status').val(data.status);
        $('#edit_keterangan').val(data.ket);

        btnDelete.classList.remove('d-none');
        btnDelete.dataset.id = id;
        modalAbsensi.show();
    });

    // Delete Handle
    btnDelete.addEventListener('click', function() {
        const id = this.dataset.id;
        Swal.fire({
            title: 'Hapus Data?',
            text: "Data ini akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                let deleteUrl = window.absensiRoutes.destroy.replace(':id', id);
                formDelete.action = deleteUrl;
                formDelete.submit();
            }
        });
    });

    // Reset for New Input
    $('[data-bs-target="#modalInputAbsensi"]').on('click', function() {
        $('#modalTitle').html('<i class="fas fa-user-edit me-2 text-primary"></i>Input Manual');
        formAbsensi.action = window.absensiRoutes.store;
        methodField.innerHTML = '';
        formAbsensi.reset();
        btnDelete.classList.add('d-none');
    });
});
