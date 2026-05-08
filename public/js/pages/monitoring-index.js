/**
 * Logic for Monitoring Matrix Page
 */
window.initMonitoringMatrix = function() {
    // Data passed from Blade
    const matrixData = window.monitoringData || {};

    window.openDayDetail = function(month, day, monthName) {
        if (!matrixData[month] || !matrixData[month][day]) return;
        const dayData = matrixData[month][day];
        
        let html = `<div class="text-start border-top pt-3 mx-2">
            <h4 class="fw-bold text-primary mb-4 text-center">
                <i class="fas fa-calendar-day me-2"></i>${day} ${monthName}
            </h4>`;

        Object.values(dayData).forEach((act) => {
            const personnelList = act.personnel.map(p => `
                <div class="py-1 border-bottom border-light">
                    <span class="small fw-bold text-dark"><i class="far fa-user me-2 text-muted"></i>${p.name}</span>
                </div>
            `).join('');

            const showLocation = act.type_name.toLowerCase().includes('rapat');

            html += `
                <div class="mb-4 p-3 rounded-4 shadow-sm border-start border-4" style="border-color: ${act.color} !important; background: #fff; border: 1px solid #f1f5f9; border-left-width: 5px !important;">
                    <div class="d-flex align-items-center mb-3">
                        <div class="inline-dot me-2 shadow-sm" style="background-color: ${act.color}; width: 8px; height: 8px; border-radius: 50%;"></div>
                        <h6 class="fw-bold text-dark mb-0 lh-base">${act.title}</h6>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <small class="text-muted d-block mb-1" style="font-size: 0.65rem;">Tipe</small>
                            <span class="badge bg-light text-primary border-0 px-2 py-1 fw-bold" style="font-size: 0.7rem;">${act.type_name}</span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block mb-1" style="font-size: 0.65rem;">Mewakili Tim</small>
                            <span class="small text-dark fw-bold">${act.team_name}</span>
                        </div>
                    </div>
                    
                    ${showLocation ? `
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1" style="font-size: 0.65rem;">Lokasi / Ruang</small>
                        <span class="small text-dark fw-medium"><i class="fas fa-map-marker-alt me-1 text-danger"></i> ${act.location}</span>
                    </div>` : ''}

                    <div class="p-3 bg-light rounded-4 border-0">
                        <small class="text-muted d-block mb-2 pb-1 border-bottom fw-bold" style="font-size: 0.7rem;">Daftar Petugas (${act.personnel.length})</small>
                        <div class="overflow-auto" style="max-height: 150px;">${personnelList}</div>
                    </div>
                </div>
            `;
        });

        html += `</div>`;

        Swal.fire({
            title: `<div class="mb-1 small text-uppercase text-muted fw-bold" style="font-size: 0.7rem; letter-spacing:1px;">Rincian Agenda</div>`,
            html: html,
            confirmButtonText: 'Tutup',
            confirmButtonColor: '#0058a8',
            width: '550px',
            showCloseButton: true,
            customClass: { popup: 'rounded-5 shadow-lg border-0' }
        });
    };
};

$(document).ready(function() {
    window.initMonitoringMatrix();
});
