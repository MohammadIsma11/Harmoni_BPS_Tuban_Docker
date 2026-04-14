$(document).on('click', '.btn-delete-history', function(e) {
    e.preventDefault();
    
    const id = $(this).data('id');
    const title = $(this).data('title');
    
    Swal.fire({
        title: 'Hapus Riwayat?',
        text: "Seluruh data rapat '" + title + "' akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        customClass: {
            popup: 'rounded-4'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Sedang Menghapus...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            $('#delete-form-' + id).submit();
        }
    });
});
