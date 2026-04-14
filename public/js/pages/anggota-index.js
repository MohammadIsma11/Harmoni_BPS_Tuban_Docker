function confirmDelete(id, name) {
    Swal.fire({
        title: 'Hapus Anggota?',
        text: name + " akan dihapus secara permanen dari sistem.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        customClass: {
            popup: 'rounded-4',
            confirmButton: 'rounded-pill px-4',
            cancelButton: 'rounded-pill px-4'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('del-' + id).submit();
        }
    });
}

// Expose to window if needed for inline onclick
window.confirmDelete = confirmDelete;
