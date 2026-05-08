(function() {
    // 1. Utility: Update Estimation
    const updateEstimasiGlobal = () => {
        const hSatInput = document.getElementById('harga_satuan');
        if (!hSatInput) return;
        
        const hargaSatuan = parseFloat(hSatInput.value) || 0;
        let total = 0;
        
        document.querySelectorAll('.mitra-row').forEach(row => {
            const volInput = row.querySelector('.volume-input');
            const subtotalDisplay = row.querySelector('.subtotal-display');
            
            const vol = parseFloat(volInput.value) || 0;
            const subtotal = vol * hargaSatuan;
            
            if (subtotalDisplay) {
                subtotalDisplay.textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
            }

            // Treat as "selected" if volume > 0
            if (vol > 0) {
                total += subtotal;
                row.classList.add('bg-primary', 'bg-opacity-10');
            } else {
                row.classList.remove('bg-primary', 'bg-opacity-10');
            }
        });
    };

    // 2. Search & Filter Mitra
    const filterMitra = () => {
        const query = $('#searchMitra').val().toLowerCase();
        const kec = $('#filterKecamatan').val().toLowerCase();

        $('.mitra-row').each(function() {
            const name = $(this).data('name') ? $(this).data('name').toString().toLowerCase() : '';
            const district = $(this).data('kec') ? $(this).data('kec').toString().toLowerCase() : 'none';

            const matchName = name.includes(query);
            const matchKec = kec === "" || district.includes(kec);

            $(this).toggle(matchName && matchKec);
        });
    };

    $(document).on('input', '#searchMitra', filterMitra);
    $(document).on('change', '#filterKecamatan', filterMitra);

    // Estimation Trigger
    $(document).on('input', '.volume-input, #harga_satuan', updateEstimasiGlobal);

    // Submit Logic
    $(document).on('click', '#btnSubmit', function(e) {
        if ($('#formPenugasan').length === 0) return; 
        
        e.preventDefault();
        const form = $('#formPenugasan');
        
        // Find rows with volume > 0
        let selectedRows = [];
        $('.mitra-row').each(function() {
            const vol = parseFloat($(this).find('.volume-input').val()) || 0;
            if (vol > 0) {
                selectedRows.push($(this));
            }
        });

        if (selectedRows.length === 0) {
            Swal.fire('Oops!', 'Masukkan volume minimal untuk satu mitra.', 'warning');
            return;
        }

        let overSBML = [];
        let totalEstimasi = 0;
        const hSatEl = $('#harga_satuan');
        const hargaSatuan = hSatEl.length ? (parseFloat(hSatEl.val()) || 0) : 0;

        selectedRows.forEach(row => {
            const vol = parseFloat(row.find('.volume-input').val()) || 0;
            const currentHonor = parseFloat(row.data('current-honor')) || 0;
            const maxHonor = parseFloat(row.data('max-honor')) || 3200000;
            const newHonor = vol * hargaSatuan;
            
            if ((currentHonor + newHonor) > maxHonor) {
                overSBML.push(row.find('.fw-bold.text-dark').text());
            }
            totalEstimasi += newHonor;
        });

        const proceedToConfirm = () => {
            Swal.fire({
                title: 'Konfirmasi Penugasan',
                html: `
                    <div class="text-center">
                        <p>Konfirmasi pemberian tugas kepada <b>${selectedRows.length} mitra</b>.</p>
                        <div class="bg-light p-3 rounded-3 mb-3">
                            <small class="text-muted d-block font-weight-bold text-uppercase">Total Estimasi Honor</small>
                            <h4 class="text-primary mb-0 fw-bold">Rp ${totalEstimasi.toLocaleString('id-ID')}</h4>
                        </div>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0058a8',
                confirmButtonText: 'Ya, Kirim!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Remove existing hidden mitra_ids to avoid duplicates
                    form.find('input[name="mitra_ids[]"]').remove();
                    
                    // Add hidden inputs for each selected mitra
                    selectedRows.forEach(row => {
                        const volInput = row.find('.volume-input');
                        const mitraId = volInput.attr('name').match(/\[(.*?)\]/)[1];
                        form.append(`<input type="hidden" name="mitra_ids[]" value="${mitraId}">`);
                    });
                    
                    form.submit();
                }
            });
        };

        if (overSBML.length > 0) {
            Swal.fire({
                title: 'Peringatan SBML!',
                html: `Beberapa mitra berikut akan memiliki total honor melebihi <b>Rp 3.200.000</b> bulan ini:<br><br>
                       <ul class="text-start small text-danger">
                           ${overSBML.map(name => `<li>${name}</li>`).join('')}
                       </ul>
                       <p class="mb-0 small text-muted">Apakah Anda tetap ingin melanjutkan penugasan ini?</p>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Ya, Tetap Lanjutkan',
                cancelButtonText: 'Batal & Perbaiki'
            }).then((result) => {
                if (result.isConfirmed) {
                    proceedToConfirm();
                }
            });
        } else {
            proceedToConfirm();
        }
    });

    if (window.swup) {
        window.swup.hooks.on('content:replace', () => {
            setTimeout(updateEstimasiGlobal, 100);
        });
    }
})();
