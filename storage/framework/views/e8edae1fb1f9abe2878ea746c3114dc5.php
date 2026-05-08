<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4 pb-5">
    
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-black text-dark mb-1">REKAPITULASI HONOR MITRA</h2>
            <p class="text-muted small">Monitoring akumulasi honorarium dan beban kerja mitra secara real-time.</p>
        </div>
        <form action="<?php echo e(route('dashboard')); ?>" method="GET" class="d-flex gap-2">
            <input type="month" name="filter_bulan" class="form-control rounded-pill border-0 shadow-sm px-4" value="<?php echo e($filter_bulan); ?>" onchange="this.form.submit()">
        </form>
    </div>

    
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3">
                        <i class="fas fa-calendar-alt text-primary"></i>
                    </div>
                    <span class="small fw-bold text-muted text-uppercase">Periode Laporan</span>
                </div>
                <h4 class="fw-bold mb-1"><?php echo e(\Carbon\Carbon::parse($filter_bulan)->translatedFormat('F Y')); ?></h4>
                <p class="text-muted extra-small mb-0">Bulan aktif laporan</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-success bg-opacity-10 p-2 rounded-3 me-3">
                        <i class="fas fa-trophy text-success"></i>
                    </div>
                    <span class="small fw-bold text-muted text-uppercase">Realisasi Honor <?php echo e(\Carbon\Carbon::parse($filter_bulan)->translatedFormat('F')); ?></span>
                </div>
                <h4 class="fw-bold text-success mb-1">Rp <?php echo e(number_format($topHonorMonth['total_honor'] ?? 0, 0, ',', '.')); ?></h4>
                <p class="text-muted small mb-0"><?php echo e($topHonorMonth['mitra']->nama_lengkap ?? '-'); ?></p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-info bg-opacity-10 p-2 rounded-3 me-3">
                        <i class="fas fa-chart-line text-info"></i>
                    </div>
                    <span class="small fw-bold text-muted text-uppercase">Realisasi Honor Tahun Ini</span>
                </div>
                <h4 class="fw-bold text-info mb-1">Rp <?php echo e(number_format($topHonorYear->total_honor ?? 0, 0, ',', '.')); ?></h4>
                <p class="text-muted small mb-0"><?php echo e($topHonorYear->mitra->nama_lengkap ?? '-'); ?></p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-warning bg-opacity-10 p-2 rounded-3 me-3">
                        <i class="fas fa-tasks text-warning"></i>
                    </div>
                    <span class="small fw-bold text-muted text-uppercase">Jumlah Survei</span>
                </div>
                <h4 class="fw-bold text-warning mb-1"><?php echo e(number_format($totalSurveyCount, 0, ',', '.')); ?> Dok</h4>
                <p class="text-muted small mb-0">Total beban dokumen tahun ini</p>
            </div>
        </div>
    </div>

    
    <div class="text-center mb-4">
        <h3 class="fw-black text-dark mb-1">REALISASI HONOR BULANAN (LUNAS)</h3>
        <p class="text-muted">Daftar mitra yang honorariumnya telah cair pada periode ini</p>
    </div>
    
    <div class="row overflow-auto flex-nowrap pb-4 scrollbar-hidden">
        <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $mName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="col-md-4" style="min-width: 350px;">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="bg-primary-dark text-white p-3 text-center">
                    <h5 class="mb-0 fw-bold"><?php echo e($mName); ?> <?php echo e(\Carbon\Carbon::parse($filter_bulan)->format('Y')); ?></h5>
                </div>
                <div class="p-3">
                    <div class="input-group input-group-sm mb-3">
                        <span class="input-group-text border-0 bg-light"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" class="form-control border-0 bg-light" placeholder="Cari...">
                    </div>
                    <div style="max-height: 300px; overflow-y: auto;">
                        <table class="table table-hover table-sm align-middle">
                            <thead class="bg-light sticky-top">
                                <tr>
                                    <th class="small py-2">NAMA</th>
                                    <th class="small py-2 text-center">BULAN</th>
                                    <th class="small py-2 text-end">HONOR</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                                <?php
                                    $monthMap = ['Januari'=>1, 'Februari'=>2, 'Maret'=>3, 'April'=>4, 'Mei'=>5, 'Juni'=>6, 'Juli'=>7, 'Agustus'=>8, 'September'=>9, 'Oktober'=>10, 'November'=>11, 'Desember'=>12];
                                    $currentFilterMonth = \Carbon\Carbon::parse($filter_bulan)->month;
                                ?>

                                <?php if($monthMap[$mName] == $currentFilterMonth): ?>
                                    <?php $__empty_1 = true; $__currentLoopData = $monthlyHonorDetails->where('realized_honor', '>', 0)->sortByDesc('realized_honor')->take(10); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td class="small fw-bold text-dark"><?php echo e($detail->nama_lengkap); ?></td>
                                        <td class="small text-center text-muted"><?php echo e($mName); ?></td>
                                        <td class="small text-end fw-bold text-primary">Rp <?php echo e(number_format($detail->realized_honor, 0, ',', '.')); ?></td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr><td colspan="3" class="text-center py-4 text-muted small">Data tidak tersedia</td></tr>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <tr><td colspan="3" class="text-center py-4 text-muted small">Data tidak tersedia</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <div class="text-center my-5">
        <h3 class="fw-black text-dark mb-1">REKAPITULASI HONOR BERDASARKAN PENUGASAN</h3>
    </div>
    <div class="card border-0 shadow-sm rounded-4 p-4">
        <div class="d-flex justify-content-end mb-3">
            <div class="input-group input-group-sm w-25">
                <span class="input-group-text bg-light border-0"><i class="fas fa-search"></i></span>
                <input type="text" class="form-control bg-light border-0" placeholder="Cari...">
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="bg-primary-dark text-white text-uppercase small">
                    <tr>
                        <th class="py-3 ps-3">Bulan</th>
                        <th class="py-3">Pendataan</th>
                        <th class="py-3">Pemeriksaan Pendataan</th>
                        <th class="py-3">Pengolahan Data</th>
                        <th class="py-3 pe-3 text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $crosstabData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="fw-bold text-muted ps-3 py-3"><?php echo e($row['bulan']); ?></td>
                        <td class="text-dark">Rp <?php echo e(number_format($row['Pendataan'], 2, ',', '.')); ?></td>
                        <td class="text-dark">Rp <?php echo e(number_format($row['Pemeriksaan'], 2, ',', '.')); ?></td>
                        <td class="text-dark">Rp <?php echo e(number_format($row['Pengolahan'], 2, ',', '.')); ?></td>
                        <td class="fw-black text-primary text-end pe-3">Rp <?php echo e(number_format($row['total'], 2, ',', '.')); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>

    
    <div class="text-center my-5">
        <h3 class="fw-black text-dark mb-1">REKAP PRODUKTIVITAS TIM</h3>
        <p class="text-muted small">Peringkat tim berdasarkan jumlah penugasan mitra di bulan <?php echo e(\Carbon\Carbon::parse($filter_bulan)->translatedFormat('F Y')); ?></p>
    </div>
    <div class="row g-4">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="bg-light text-uppercase small">
                            <tr>
                                <th class="py-3 ps-3">Peringkat</th>
                                <th class="py-3">Nama Tim</th>
                                <th class="py-3 text-center">Jumlah Kegiatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $teamRecap; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $team): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="ps-3 fw-bold text-muted">#<?php echo e($index + 1); ?></td>
                                <td>
                                    <div class="fw-bold text-dark"><?php echo e($team->nama_tim); ?></div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-4 py-2">
                                        <?php echo e(number_format($team->total_activities, 0, ',', '.')); ?> Kegiatan
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="fas fa-info-circle me-2"></i> Belum ada aktivitas penugasan bulan ini.
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    
    <div class="text-center my-5">
        <h3 class="fw-black text-dark mb-1">MONITORING KAPASITAS & BEBAN RENCANA MITRA</h3>
        <p class="text-muted small">Total beban penugasan (Rencana + Realisasi) untuk menjaga plafon honor bulanan <?php echo e(\Carbon\Carbon::parse($filter_bulan)->translatedFormat('F Y')); ?></p>
    </div>
    <div class="card border-0 shadow-sm rounded-4 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex gap-2">
                <span class="badge bg-danger rounded-pill px-3 py-2 small">MAX: ≥ Rp 3,2jt</span>
                <span class="badge bg-warning text-dark rounded-pill px-3 py-2 small">SIAGA: ≥ Rp 2,8jt</span>
                <span class="badge bg-success rounded-pill px-3 py-2 small">TERSEDIA: < Rp 2,8jt</span>
            </div>
            <div class="input-group input-group-sm w-25">
                <span class="input-group-text bg-light border-0"><i class="fas fa-search"></i></span>
                <input type="text" id="searchKapasitas" class="form-control bg-light border-0" placeholder="Cari nama mitra...">
            </div>
        </div>
        <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
            <table class="table table-hover align-middle">
                <thead class="bg-primary-dark text-white text-uppercase small sticky-top">
                    <tr>
                        <th class="py-3 ps-3">Peringkat</th>
                        <th class="py-3">Nama Mitra</th>
                        <th class="py-3">Sobat ID</th>
                        <th class="py-3 text-end">Total Honor</th>
                        <th class="py-3 pe-3 text-center">Status Kapasitas</th>
                    </tr>
                </thead>
                <tbody id="tableKapasitas">
                    <?php $__currentLoopData = $monthlyHonorDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $mitra): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $honor = $mitra->accumulated_honor;
                        $limit = $mitra->max_honor_bulanan;
                        
                        if ($honor >= $limit) {
                            $status = 'MAX'; $bg = 'danger'; $icon = 'exclamation-triangle';
                        } elseif ($honor >= 2800000) {
                            $status = 'SIAGA'; $bg = 'warning'; $icon = 'clock';
                        } else {
                            $status = 'TERSEDIA'; $bg = 'success'; $icon = 'check-circle';
                        }
                    ?>
                    <tr class="mitra-row-kapasitas" data-name="<?php echo e(strtolower($mitra->nama_lengkap)); ?>">
                        <td class="ps-3 py-3 fw-bold text-muted">#<?php echo e($index + 1); ?></td>
                        <td>
                            <div class="fw-bold text-dark"><?php echo e($mitra->nama_lengkap); ?></div>
                            <small class="text-muted"><?php echo e($mitra->posisi); ?></small>
                        </td>
                        <td class="text-muted font-monospace small"><?php echo e($mitra->sobat_id); ?></td>
                        <td class="text-end fw-bold <?php echo e($bg == 'danger' ? 'text-danger' : ($bg == 'warning' ? 'text-warning' : 'text-primary')); ?>">
                            Rp <?php echo e(number_format($honor, 0, ',', '.')); ?>

                        </td>
                        <td class="text-center pe-3">
                            <span class="badge bg-<?php echo e($bg); ?> rounded-pill px-3 py-2 w-100" style="max-width: 120px;">
                                <i class="fas fa-<?php echo e($icon); ?> me-1"></i> <?php echo e($status); ?>

                            </span>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>

    
    <div class="text-center my-5">
        <h3 class="fw-black text-dark mb-1">DATA RANKING BEBAN KERJA (VOLUME) SETAHUN</h3>
    </div>
    <div class="card border-0 shadow-sm rounded-4 p-4">
        <div class="d-flex justify-content-end mb-3">
            <div class="input-group input-group-sm w-25">
                <span class="input-group-text bg-light border-0"><i class="fas fa-search"></i></span>
                <input type="text" class="form-control bg-light border-0" placeholder="Cari...">
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="bg-primary-dark text-white text-uppercase small">
                    <tr>
                        <th class="py-3 ps-3">Ranking</th>
                        <th class="py-3">Sobat ID</th>
                        <th class="py-3">Nama</th>
                        <th class="py-3 pe-3 text-center">Jumlah Survei</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $mitraRankings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $rank): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $roman = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X'];
                    ?>
                    <tr>
                        <td class="ps-3 py-3 fw-bold"><?php echo e($roman[$index] ?? ($index + 1)); ?></td>
                        <td class="text-muted font-monospace"><?php echo e($rank->mitra_id); ?></td>
                        <td class="fw-bold text-dark"><?php echo e($rank->mitra->nama_lengkap); ?></td>
                        <td class="text-center pe-3"><span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3"><?php echo e(number_format($rank->total_survey, 0, ',', '.')); ?></span></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<style>
    .fw-black { font-weight: 900; }
    .bg-primary-dark { background-color: #1a4d6e; }
    .extra-small { font-size: 0.65rem; }
    .scrollbar-hidden::-webkit-scrollbar { display: none; }
    .scrollbar-hidden { -ms-overflow-style: none; scrollbar-width: none; }
    .table-sm th, .table-sm td { font-size: 0.75rem; }
    thead th { letter-spacing: 0.5px; }
</style>
<?php $__env->startPush('scripts'); ?>
<script>
    document.getElementById('searchKapasitas')?.addEventListener('input', function() {
        const query = this.value.toLowerCase();
        document.querySelectorAll('.mitra-row-kapasitas').forEach(row => {
            const name = row.dataset.name;
            row.style.display = name.includes(query) ? 'table-row' : 'none';
        });
    });
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/dashboard-honor.blade.php ENDPATH**/ ?>