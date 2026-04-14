document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('#formNotulensi');
    
    // Fungsi Validasi Ukuran File (Sekarang Global 20MB)
    function validateSize(input, maxSizeMB, typeName) {
        const files = Array.from(input.files);
        const maxSize = maxSizeMB * 1024 * 1024;
        let oversized = [];

        files.forEach(file => {
            if (file.size > maxSize) oversized.push(file.name);
        });

        if (oversized.length > 0) {
            Swal.fire({
                icon: 'error',
                title: 'File ' + typeName + ' Kebesaran',
                html: `Batas maksimal adalah ${maxSizeMB}MB.<br><small class="text-danger">${oversized.join(', ')}</small>`,
                confirmButtonColor: '#0058a8'
            });
            input.value = ''; // Reset
            return false;
        }
        return true;
    }

    // Listener Notulensi Utama (Diubah ke 20MB)
    const reportFile = document.querySelector('#hasil_rapat_file');
    if (reportFile) {
        reportFile.addEventListener('change', function() {
            validateSize(this, 20, 'Notulensi');
        });
    }

    // Listener Materi (20MB)
    const materialPath = document.querySelector('#materi_path');
    if (materialPath) {
        materialPath.addEventListener('change', function() {
            validateSize(this, 20, 'Materi');
        });
    }

    // Listener Foto Dokumentasi (Diubah ke 20MB) & Preview
    const docPhoto = document.querySelector('#foto_dokumentasi');
    if (docPhoto) {
        docPhoto.addEventListener('change', function() {
            if(validateSize(this, 20, 'Foto')) {
                const previewContainer = document.querySelector('#image-preview-container');
                previewContainer.innerHTML = '';
                Array.from(this.files).forEach(file => {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const col = document.createElement('div');
                        col.className = 'col-3';
                        col.innerHTML = `<div class="preview-img-wrapper"><img src="${e.target.result}"></div>`;
                        previewContainer.appendChild(col);
                    };
                    reader.readAsDataURL(file);
                });
            }
        });
    }

    // Loading saat submit
    if (form) {
        form.addEventListener('submit', function() {
            if (this.checkValidity()) {
                Swal.fire({
                    title: 'Mengunggah Laporan...',
                    text: 'Mohon tunggu sebentar, file sedang diproses.',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => Swal.showLoading()
                });
            }
        });
    }
});
