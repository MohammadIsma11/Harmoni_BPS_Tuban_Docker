function showDetail(title, lokasi, pegawai, status, jenis) {
    Swal.fire({
        title: `<div class="mb-2 small text-uppercase text-muted fw-bold" style="font-size: 0.7rem; letter-spacing:1px;">Rincian Agenda</div><div class="px-3 text-primary">${title}</div>`,
        html: `
            <div class="text-start border-top pt-3 mx-2">
                <div class="mb-3 d-flex align-items-center p-2 bg-light rounded-3">
                    <div class="bg-white p-2 rounded-2 me-3 shadow-sm"><i class="fas fa-users-viewfinder text-primary"></i></div>
                    <div><small class="text-muted d-block">Asal Penugasan</small><span class="fw-bold text-dark">${jenis}</span></div>
                </div>
                <div class="mb-3 d-flex align-items-center p-2 bg-light rounded-3">
                    <div class="bg-white p-2 rounded-2 me-3 shadow-sm"><i class="fas fa-user-check text-success"></i></div>
                    <div><small class="text-muted d-block">Personil</small><span class="fw-bold text-dark">${pegawai}</span></div>
                </div>
                <div class="mb-3 d-flex align-items-center p-2 bg-light rounded-3">
                    <div class="bg-white p-2 rounded-2 me-3 shadow-sm"><i class="fas fa-map-pin text-danger"></i></div>
                    <div><small class="text-muted d-block">Lokasi/Ruang</small><span class="fw-bold text-dark">${lokasi}</span></div>
                </div>
                <div class="mb-0 d-flex align-items-center p-2 bg-light rounded-3">
                    <div class="bg-white p-2 rounded-2 me-3 shadow-sm"><i class="fas fa-info-circle text-info"></i></div>
                    <div><small class="text-muted d-block">Status</small>
                        <span class="badge ${status == 'Selesai' ? 'bg-success' : 'bg-warning text-dark'} border-0 px-3 mt-1 fw-bold shadow-sm">${status}</span>
                    </div>
                </div>
            </div>
        `,
        confirmButtonText: 'Tutup',
        confirmButtonColor: '#0058a8',
        customClass: { popup: 'rounded-4 shadow-lg border-0' }
    });
}

window.showDetail = showDetail;
