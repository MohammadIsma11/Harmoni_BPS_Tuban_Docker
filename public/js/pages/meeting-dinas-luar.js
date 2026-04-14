document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('#formDinasLuar');
    
    // Fungsi Validasi Ukuran File
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
                title: 'File ' + typeName + ' Terlalu Besar',
                html: `Batas maksimal adalah ${maxSizeMB}MB.<br><small class="text-danger">${oversized.join(', ')}</small>`,
                confirmButtonColor: '#15803d'
            });
            input.value = ''; // Reset
            return false;
        }
        return true;
    }

    // Listener File Laporan (20MB)
    const reportFile = document.querySelector('#hasil_rapat_file');
    if (reportFile) {
        reportFile.addEventListener('change', function() {
            validateSize(this, 20, 'Laporan');
        });
    }

    // Listener Foto Dokumentasi (20MB) & Preview
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
                        col.className = 'col-4';
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
                    title: 'Mengirim Laporan Dinas...',
                    text: 'Harap tunggu, data dan file sedang diunggah.',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => Swal.showLoading()
                });
            }
        });
    }
});
