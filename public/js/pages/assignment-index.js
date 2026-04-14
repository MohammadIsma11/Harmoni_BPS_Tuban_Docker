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
});
