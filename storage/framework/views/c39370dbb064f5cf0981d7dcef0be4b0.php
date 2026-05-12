<?php
    \Carbon\Carbon::setLocale('id');
    $monthsList = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];

    // Daftar Tim Teknis Berdasarkan Request
    $technicalTeams = [
        'Tim Statistik Sosial' => '#ef4444',
        'Tim Statistik Distribusi dan Jasa' => '#10b981',
        'Tim Statistik Produksi' => '#f59e0b',
        'Tim Neraca Wilayah dan Analisis Statistik' => '#8b5cf6',
        'Tim Pengolahan dan Layanan Statistik' => '#ec4899',
        'Tim Pembinaan Statistik Sektoral' => '#06b6d4',
    ];
?>



<?php $__env->startPush('styles'); ?>
<style>
    :root {
        --bps-blue: #0058a8;
        --brand-red: #d90429;
        --soft-bg: #f8f9fa;
        --border-light: #edf2f7;
    }

    .matrix-page-wrapper { background-color: var(--soft-bg); min-height: 100vh; padding-bottom: 50px; }
    .breadcrumb-container i { color: #cbd5e0; }
    .breadcrumb-container span { color: #4a5568; letter-spacing: 0.5px; }

    .toolbar-card { border: none; border-radius: 12px; }
    .btn-reset-custom {
        background-color: var(--brand-red) !important; color: white !important;
        border: none; border-radius: 8px; font-weight: 600; font-size: 0.85rem; padding: 10px 24px;
    }

    .month-matrix-card { border-radius: 12px; background: #fff; }
    
    .calendar-matrix-grid { user-select: none; width: 100%; }
    .grid-row { 
        display: flex !important; 
        flex-wrap: wrap !important; 
        width: 100% !important; 
        margin: 0 !important; 
        padding: 0 !important;
    }
    .grid-row.headers span {
        width: calc(100% / 7); text-align: center;
        font-size: 0.65rem; font-weight: 800; color: #a0aec0; margin-bottom: 10px;
    }

    .cell {
        width: calc(100% / 7) !important; aspect-ratio: 1/1;
        display: flex !important; flex-direction: column; align-items: center; justify-content: center;
        font-size: 0.75rem; font-weight: 600; border-radius: 10px;
        background: #f7fafc; color: #4a5568; position: relative;
        border: 2px solid #fff !important; transition: all 0.15s;
    }

    .cell.has-events { cursor: pointer; background: #fff; border-color: var(--border-light) !important; }
    .cell.has-events:hover { background: #f0f7ff; transform: scale(1.05); z-index: 5; border-color: var(--bps-blue) !important; }
    .cell.active-today { background: var(--bps-blue) !important; color: #fff !important; }

    .event-indicators-wrap { 
        display: flex; gap: 2px; margin-top: 3px; justify-content: center; 
        position: absolute; bottom: 6px; width: 85%;
    }
    .e-pill { 
        height: 4px; flex: 1; border-radius: 2px; 
        min-width: 8px; max-width: 15px;
    }

    .inline-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }

    .legend-card { border: none; border-radius: 12px; }
    .legend-item { display: flex; align-items: center; gap: 10px; padding: 5px 12px; background: #f8fafc; border-radius: 8px; }

    .activities-summary { min-height: 100px; }
    .summary-list-scrollable { max-height: 65px; overflow-y: auto; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>

<div class="matrix-page-wrapper">
    
    <div class="breadcrumb-container mb-4">
        <div class="d-flex align-items-center text-muted small fw-bold">
            <i class="fas fa-home me-2 text-primary"></i>
            <i class="fas fa-chevron-right me-2" style="font-size: 0.6rem;"></i>
            <span>Matriks Kegiatan</span>
        </div>
    </div>

    
    <div class="row mb-4">
        <div class="col-lg-3 mb-3 mb-lg-0">
            <div class="toolbar-card bg-white shadow-sm rounded-4 p-3 h-100">
                <p class="small fw-bold text-muted mb-2">Filter Tahun</p>
                <div class="d-flex gap-2">
                    <form action="<?php echo e(route('monitoring.index')); ?>" method="GET" id="filterForm" class="flex-grow-1">
                        <select name="year" onchange="document.getElementById('filterForm').submit()" class="form-select border-0 bg-light fw-bold shadow-sm">
                            <?php for($y = date('Y')-2; $y <= date('Y')+2; $y++): ?>
                                <option value="<?php echo e($y); ?>" <?php echo e($year == $y ? 'selected' : ''); ?>><?php echo e($y); ?></option>
                            <?php endfor; ?>
                        </select>
                    </form>
                    <a href="<?php echo e(route('monitoring.index')); ?>" class="btn btn-danger btn-reset-custom shadow-sm px-3" title="Reset Filter">
                        <i class="fas fa-sync-alt"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-lg-9">
            <div class="legend-card bg-white shadow-sm rounded-4 p-3 h-100">
                <p class="small fw-bold text-muted mb-2">Info Tim Statistik</p>
                <div class="d-flex flex-wrap gap-2">
                    <div class="legend-item"><span class="inline-dot" style="background: #0058a8;"></span> <small class="fw-bold">Kepala BPS</small></div>
                    <div class="legend-item"><span class="inline-dot" style="background: #64748b;"></span> <small class="fw-bold">Subbag Umum</small></div>
                    <?php $__currentLoopData = $technicalTeams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $team => $color): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="legend-item">
                            <span class="inline-dot" style="background: <?php echo e($color); ?>;"></span>
                            <small class="fw-bold"><?php echo e($team); ?></small>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>

    
    <h4 class="fw-bold text-dark mb-4 ms-2"><i class="fas fa-calendar-alt text-primary me-2"></i>Matriks Kegiatan Tahun <?php echo e($year); ?></h4>

    <div class="matrix-grid-container px-2">
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php $__currentLoopData = $monthsList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mNum => $mName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $firstDayDate = \Carbon\Carbon::create($year, $mNum, 1);
                    $daysCount = $firstDayDate->daysInMonth;
                    $weekdayStart = $firstDayDate->dayOfWeek;
                    $currentMonthFlag = ($year == date('Y') && $mNum == date('m'));
                ?>
                <div class="col">
                    <div class="card month-matrix-card h-100 shadow-sm border-0">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                            <h6 class="fw-bold text-dark mb-0 d-flex align-items-center"><?php echo e($mName); ?>

                                <?php if($currentMonthFlag): ?> <span class="ms-2 badge bg-primary text-white border-0 small" style="font-size: 0.6rem;">Bulan Ini</span> <?php endif; ?>
                            </h6>
                        </div>
                        <div class="card-body px-4 pb-4">
                            <div class="calendar-matrix-grid mb-3">
                                <div class="grid-row headers mb-2"><span>S</span><span>M</span><span>T</span><span>W</span><span>T</span><span>F</span><span>S</span></div>
                                <div class="grid-row days">
                                    <?php for($i = 0; $i < $weekdayStart; $i++): ?> <div class="cell placeholder"></div> <?php endfor; ?>
                                    <?php for($day = 1; $day <= $daysCount; $day++): ?>
                                        <?php
                                            $hasActs = isset($matrixData[$mNum][$day]);
                                            $isTodayDate = ($year == date('Y') && $mNum == date('m') && $day == date('d'));
                                            $actsList = $hasActs ? $matrixData[$mNum][$day] : [];
                                        ?>
                                        <div class="cell <?php echo e($isTodayDate ? 'active-today' : ''); ?> <?php echo e($hasActs ? 'has-events' : ''); ?>"
                                             <?php if($hasActs): ?> onclick="openDayDetail(<?php echo e($mNum); ?>, <?php echo e($day); ?>, '<?php echo e($mName); ?>')" <?php endif; ?>>
                                            <span class="num"><?php echo e($day); ?></span>
                                            <?php if($hasActs): ?>
                                                <div class="event-indicators-wrap">
                                                    <?php $__currentLoopData = array_slice($actsList, 0, 3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $act): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <span class="e-pill" style="background-color: <?php echo e($act['color']); ?>" title="<?php echo e($act['title']); ?>"></span>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endfor; ?>
                                </div>
                            </div>

                            <div class="activities-summary border-top pt-3 mt-auto">
                                <p class="small fw-bold text-dark mb-2">Kegiatan (<?php echo e(isset($matrixData[$mNum]) ? count($matrixData[$mNum], COUNT_RECURSIVE) - count($matrixData[$mNum]) : 0); ?>)</p>
                                <?php if(isset($matrixData[$mNum])): ?>
                                    <div class="summary-list-scrollable">
                                        <?php $shown = 0; ?>
                                        <?php $__currentLoopData = $matrixData[$mNum]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dActs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php $__currentLoopData = $dActs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $act): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php if($shown < 2): ?>
                                                    <div class="summary-item d-flex align-items-center mb-1"><span class="inline-dot me-2" style="background-color: <?php echo e($act['color']); ?>"></span><span class="text-muted small text-truncate"><?php echo e($act['title']); ?></span></div>
                                                <?php endif; ?>
                                                <?php $shown++; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php if($shown > 2): ?> <small class="text-primary fw-bold">+ <?php echo e($shown - 2); ?> lainnya</small> <?php endif; ?>
                                    </div>
                                <?php else: ?> <p class="small text-muted mb-0 italic">Tidak ada kegiatan</p> <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    window.monitoringData = <?php echo json_encode($matrixData, 15, 512) ?>;

    $(document).ready(function() {
        window.openDayDetail = function(month, day, monthName) {
            const matrixData = window.monitoringData || {};
            if (!matrixData[month] || !matrixData[month][day]) return;
            const dayData = matrixData[month][day];
            
            let html = `<div class="text-start border-top pt-3 mx-2"><h4 class="fw-bold text-primary mb-4 text-center"><i class="fas fa-calendar-day me-2"></i>${day} ${monthName}</h4>`;

            Object.values(dayData).forEach((act) => {
                const personnelList = act.personnel.map(p => `<div class="py-1 border-bottom border-light"><span class="small fw-bold text-dark"><i class="far fa-user me-2 text-muted"></i>${p.name}</span></div>`).join('');
                const showLocation = act.type_name.toLowerCase().includes('rapat');

                html += `
                    <div class="mb-4 p-3 rounded-4 shadow-sm border-start border-4" style="border-color: ${act.color} !important; background: #fff; border: 1px solid #f1f5f9; border-left-width: 5px !important;">
                        <div class="d-flex align-items-center mb-3">
                            <div class="inline-dot me-2 shadow-sm" style="background-color: ${act.color}; width:8px; height:8px; border-radius:50%;"></div>
                            <h6 class="fw-bold text-dark mb-0 lh-base">${act.title}</h6>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-6"><small class="text-muted d-block mb-1" style="font-size:0.65rem;">Tipe</small><span class="badge bg-light text-primary border-0 px-2 py-1 fw-bold" style="font-size: 0.7rem;">${act.type_name}</span></div>
                            <div class="col-6"><small class="text-muted d-block mb-1" style="font-size:0.65rem;">Mewakili Tim</small><span class="small text-dark fw-bold">${act.team_name}</span></div>
                        </div>
                        ${showLocation ? `<div class="mb-3"><small class="text-muted d-block mb-1" style="font-size:0.65rem;">Lokasi / Ruang</small><span class="small text-dark fw-medium"><i class="fas fa-map-marker-alt me-1 text-danger"></i> ${act.location}</span></div>` : ''}
                        <div class="p-3 bg-light rounded-4 border-0">
                            <small class="text-muted d-block mb-2 pb-1 border-bottom fw-bold" style="font-size: 0.7rem;">Daftar Petugas (${act.personnel.length})</small>
                            <div class="overflow-auto" style="max-height: 150px;">${personnelList}</div>
                        </div>
                    </div>`;
            });

            html += `</div>`;
            Swal.fire({
                title: `<div class="mb-1 small text-uppercase text-muted fw-bold" style="font-size: 0.7rem; letter-spacing:1px;">Rincian Agenda</div>`,
                html: html,
                confirmButtonText: 'Tutup', confirmButtonColor: '#0058a8',
                width: '550px', showCloseButton: true, customClass: { popup: 'rounded-5 shadow-lg border-0' }
            });
        };
    });
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/monitoring/index.blade.php ENDPATH**/ ?>