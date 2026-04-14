function confirmDelete(id, title) {
    Swal.fire({
        title: 'Hapus Laporan?',
        html: `Apakah Anda yakin ingin menghapus laporan <br><strong>"${title}"</strong>?<br><small class='text-danger'>Data yang dihapus tidak dapat dikembalikan!</small>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#cf1322',
        cancelButtonColor: '#64748b',
        confirmButtonText: '<i class="fas fa-trash-alt me-2"></i> Ya, Hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        customClass: { popup: 'rounded-4' }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('form-delete-' + id).submit();
        }
    });
}

// Expose to window for inline onclick
window.confirmDelete = confirmDelete;
