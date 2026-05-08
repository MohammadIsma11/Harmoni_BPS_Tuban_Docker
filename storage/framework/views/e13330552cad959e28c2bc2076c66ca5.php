<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold mb-1">Rekap Honor Dasar</h4>
                    <p class="text-muted small mb-0">Tahun Anggaran <?php echo e($selectedYear); ?></p>
                </div>
                <form action="<?php echo e(route('rekap-honor.index')); ?>" method="GET" class="d-flex gap-2">
                    <select name="tahun" class="form-select" onchange="this.form.submit()">
                        <?php $__currentLoopData = $availableYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($year); ?>" <?php echo e($selectedYear == $year ? 'selected' : ''); ?>>Tahun <?php echo e($year); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </form>
            </div>
        </div>
    </div>

    
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 75vh;">
                <table class="table table-bordered align-middle mb-0" id="main-table-rekap">
                    <thead>
                        <tr class="table-light">
                            <th class="stk-header stk-left-1">No</th>
                            <th class="stk-header stk-left-2">Nama Mitra</th>
                            <?php $__currentLoopData = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <th class="text-center stk-header month-col"><?php echo e($m); ?></th>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <th class="text-end stk-header total-col">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $grandTotal = 0; ?>
                        <?php $__currentLoopData = $mitras; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $rowTotal = 0; ?>
                            <tr>
                                <td class="text-center stk-left-1 bg-white"><?php echo e($index + 1); ?></td>
                                <td class="stk-left-2 bg-white">
                                    <div class="fw-bold small"><?php echo e($m->nama_lengkap); ?></div>
                                    <div class="text-muted" style="font-size: 0.65rem;"><?php echo e($m->sobat_id); ?></div>
                                </td>
                                <?php $__currentLoopData = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mNum): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php 
                                        $val = $pivotData[$m->sobat_id][$mNum] ?? 0;
                                        $rowTotal += $val;
                                        $isOverLimit = $val > 3200000;
                                    ?>
                                    <td class="text-end cell-click month-col <?php echo e($isOverLimit ? 'bg-danger bg-opacity-10 text-danger animate-pulse-soft' : ''); ?>" 
                                        data-sobat="<?php echo e($m->sobat_id); ?>" 
                                        data-month="<?php echo e($selectedYear); ?>-<?php echo e($mNum); ?>" 
                                        data-name="<?php echo e($m->nama_lengkap); ?>">
                                        <?php if($val > 0): ?>
                                            <span class="small fw-bold">Rp <?php echo e(number_format($val, 0, ',', '.')); ?></span>
                                        <?php else: ?>
                                            <span class="text-muted opacity-25">-</span>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <td class="text-end fw-bold text-primary total-col">
                                    Rp <?php echo e(number_format($rowTotal, 0, ',', '.')); ?>

                                </td>
                            </tr>
                            <?php $grandTotal += $rowTotal; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                    <tfoot class="table-dark">
                        <tr>
                            <th colspan="2" class="text-center stk-left-1 bg-dark">TOTAL</th>
                            <?php for($i=0; $i<12; $i++): ?> <th></th> <?php endfor; ?>
                            <th class="text-end total-col bg-dark">Rp <?php echo e(number_format($grandTotal, 0, ',', '.')); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    
    <div id="detail-section" class="mt-4 d-none">
        <div class="card border-0 shadow-lg rounded-4">
            <div class="card-header bg-white border-0 p-4 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">Rincian: <span id="det-name" class="text-primary"></span></h6>
                <button class="btn-close" onclick="$('#detail-section').addClass('d-none')"></button>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th class="ps-4">Kegiatan</th>
                            <th class="text-center">Tim</th>
                            <th class="text-center">Vol</th>
                            <th class="text-end">Honor</th>
                            <th class="text-end pe-4">Cair</th>
                        </tr>
                    </thead>
                    <tbody id="det-body"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    /* STICKY CONFIG */
    .table-responsive { position: relative; }
    #main-table-rekap { 
        border-collapse: separate; 
        border-spacing: 0; 
        min-width: 1400px;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    
    #main-table-rekap th, #main-table-rekap td {
        padding: 8px 10px !important; /* Tighter padding */
        font-size: 0.7rem !important;  /* Smaller font */
        white-space: nowrap;
    }

    .stk-header {
        position: sticky !important;
        top: 0;
        z-index: 10;
        background-color: #f8fafc !important;
        border-bottom: 2px solid #dee2e6 !important;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .stk-left-1 { position: sticky !important; left: 0; z-index: 20; width: 40px; }
    .stk-left-2 { position: sticky !important; left: 40px; z-index: 20; width: 220px; border-right: 2px solid #dee2e6 !important; }
    
    .month-col { width: 95px !important; min-width: 95px !important; }
    .total-col { width: 140px !important; min-width: 140px !important; background-color: #f8fafc !important; color: #0058a8 !important; }

    /* Intersection */
    thead th.stk-left-1 { z-index: 30; }
    thead th.stk-left-2 { z-index: 30; }

    .cell-click { cursor: pointer; transition: 0.1s; }
    .cell-click:hover { background-color: #f0f7ff !important; }

    /* ALERT PULSE */
    .animate-pulse-soft {
        animation: pulse-red 2s infinite;
    }
    @keyframes pulse-red {
        0% { background-color: rgba(220, 53, 69, 0.1); }
        50% { background-color: rgba(220, 53, 69, 0.2); }
        100% { background-color: rgba(220, 53, 69, 0.1); }
    }
</style>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    $('.cell-click').on('click', function() {
        const d = $(this).data();
        $('#detail-section').removeClass('d-none');
        $('#det-name').text(d.name);
        $('#det-body').html('<tr><td colspan="5" class="text-center py-4">Loading...</td></tr>');
        
        $('html, body').animate({ scrollTop: $('#detail-section').offset().top - 100 }, 300);

        $.get("<?php echo e(route('rekap-honor.detail')); ?>", { sobat_id: d.sobat, month: d.month }, function(res) {
            let h = '';
            res.forEach(item => {
                h += `<tr>
                    <td class="ps-4 small">${item.kegiatan}</td>
                    <td class="text-center small">${item.tim}</td>
                    <td class="text-center small">${parseFloat(item.volume)}</td>
                    <td class="text-end small">Rp ${new Intl.NumberFormat('id-ID').format(item.total_honor_tugas)}</td>
                    <td class="text-end pe-4 small fw-bold">Rp ${new Intl.NumberFormat('id-ID').format(item.nominal_cair_bulan_ini)}</td>
                </tr>`;
            });
            $('#det-body').html(h || '<tr><td colspan="5" class="text-center py-4">No data</td></tr>');
        });
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/rekap/honor.blade.php ENDPATH**/ ?>