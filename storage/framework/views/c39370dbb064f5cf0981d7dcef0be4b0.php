<?php
    \Carbon\Carbon::setLocale('id');
    $viewMode = request('view_mode', 'month');
    $startDate = \Carbon\Carbon::create($year, $month, 1);
    
    if($viewMode == 'week') {
        $startDate = \Carbon\Carbon::parse(request('week_start', now()->startOfWeek()->format('Y-m-d')));
        $daysToShow = 7;
    } else {
        $daysToShow = $daysInMonth;
    }
?>



<?php $__env->startSection('content'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/pages/monitoring-index.css')); ?>">

<div class="container-fluid px-4">
    <div class="card monitoring-card shadow-sm mb-4">
        <div class="card-body p-4">
            
            <div class="row align-items-center mb-4">
                <div class="col-xl-3 col-lg-12 mb-3 mb-xl-0">
                    <div class="d-flex align-items-center mb-1">
                        <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3 text-primary">
                            <i class="fas fa-calendar-alt fa-lg"></i>
                        </div>
                        <h4 class="fw-bold mb-0 text-dark">Timeline Monitoring</h4>
                    </div>
                    <p class="text-muted small mb-0">Visualisasi beban kerja personil.</p>
                </div>
                
                <div class="col-xl-9 col-lg-12">
                    <div class="d-flex flex-wrap justify-content-xl-end gap-3 align-items-center">
                        <div class="btn-group shadow-sm p-1 bg-light rounded-3">
                            <a href="<?php echo e(route('monitoring.index', ['view_mode' => 'week', 'month' => $month, 'year' => $year])); ?>" 
                               class="btn view-filter-btn <?php echo e($viewMode == 'week' ? 'active' : ''); ?>">Mingguan</a>
                            <a href="<?php echo e(route('monitoring.index', ['view_mode' => 'month', 'month' => $month, 'year' => $year])); ?>" 
                               class="btn view-filter-btn <?php echo e($viewMode == 'month' ? 'active' : ''); ?>">Bulanan</a>
                        </div>

                        
                        <div class="d-flex gap-3 px-3 border-end border-start d-none d-sm-flex">
                            <div class="legend-item"><div class="legend-color pill-tugas" style="width:10px;height:10px;border-radius:3px;display:inline-block;margin-right:5px;"></div> <small>Tugas</small></div>
                            <div class="legend-item"><div class="legend-color pill-rapat" style="width:10px;height:10px;border-radius:3px;display:inline-block;margin-right:5px;"></div> <small>Rapat</small></div>
                            <div class="legend-item"><div class="legend-color pill-dinas" style="width:10px;height:10px;border-radius:3px;display:inline-block;margin-right:5px;"></div> <small>Dinas Luar</small></div>
                            <div class="legend-item"><div class="legend-color pill-selesai" style="width:10px;height:10px;border-radius:3px;display:inline-block;margin-right:5px;"></div> <small>Selesai</small></div>
                        </div>

                        <form action="<?php echo e(route('monitoring.index')); ?>" method="GET" class="d-flex gap-2">
                            <input type="hidden" name="view_mode" value="<?php echo e($viewMode); ?>">
                            <div class="input-group input-group-sm shadow-sm">
                                <select name="month" class="form-select fw-bold border-0 bg-light">
                                    <?php $__currentLoopData = range(1, 12); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($m); ?>" <?php echo e($month == $m ? 'selected' : ''); ?>>
                                            <?php echo e(\Carbon\Carbon::create()->month($m)->translatedFormat('F')); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <select name="year" class="form-select fw-bold border-0 bg-light">
                                    <?php for($y = date('Y')-1; $y <= date('Y')+1; $y++): ?>
                                        <option value="<?php echo e($y); ?>" <?php echo e($year == $y ? 'selected' : ''); ?>><?php echo e($y); ?></option>
                                    <?php endfor; ?>
                                </select>
                                <button type="submit" class="btn btn-primary px-3"><i class="fas fa-filter"></i></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="table-responsive scrollbar-custom border shadow-sm rounded-4">
                <table class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th class="sticky-name-col py-4 text-center text-uppercase small fw-bold">Petugas Pelaksana</th>
                            <?php for($i = 0; $i < $daysToShow; $i++): ?>
                                <?php 
                                    $dt = $startDate->copy()->addDays($i);
                                    $isWeekend = $dt->isWeekend();
                                    $isToday = $dt->isToday();
                                ?>
                                <th class="text-center py-3 <?php echo e($isWeekend ? 'weekend-cell text-danger' : 'text-primary'); ?> <?php echo e($isToday ? 'today-cell' : ''); ?>" style="min-width: 55px;">
                                    <span class="d-block h6 fw-bold mb-0"><?php echo e($dt->format('d')); ?></span>
                                    <small class="fw-bold text-uppercase" style="font-size: 0.55rem;"><?php echo e($dt->translatedFormat('D')); ?></small>
                                </th>
                            <?php endfor; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="sticky-name-col px-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-init me-3 shadow-sm">
                                        <?php echo e(strtoupper(substr($user->nama_lengkap, 0, 1))); ?>

                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark lh-1 mb-1" style="font-size: 0.85rem;"><?php echo e($user->nama_lengkap); ?></div>
                                        <small class="text-muted" style="font-size: 0.7rem;"><?php echo e($user->team->nama_tim ?? 'Internal'); ?></small>
                                    </div>
                                </div>
                            </td>

                            <?php for($i = 0; $i < $daysToShow; $i++): ?>
                                <?php
                                    $dtCheck = $startDate->copy()->addDays($i);
                                    $currentDateStr = $dtCheck->format('Y-m-d');
                                    
                                    $agenda = $user->agendas->first(function($a) use ($currentDateStr) {
                                        return $currentDateStr >= $a->event_date->format('Y-m-d') && $currentDateStr <= $a->end_date->format('Y-m-d');
                                    });
                                    $isWeekend = $dtCheck->isWeekend();
                                    $isToday = $dtCheck->isToday();
                                ?>
                                <td class="day-cell <?php echo e($isWeekend ? 'weekend-cell' : ''); ?> <?php echo e($isToday ? 'today-cell' : ''); ?>">
                                    <?php if($agenda): ?>
                                        <?php
                                            // LOGIKA WARNA & ICON BARU
                                            if($agenda->status_laporan == 'Selesai') {
                                                $pillClass = 'pill-selesai';
                                                $icon = 'fa-check';
                                            } elseif($agenda->activity_type_id == 2) {
                                                $pillClass = 'pill-rapat';
                                                $icon = 'fa-users';
                                            } elseif($agenda->activity_type_id == 3) {
                                                $pillClass = 'pill-dinas'; // Kategori baru
                                                $icon = 'fa-plane-departure';
                                            } else {
                                                $pillClass = 'pill-tugas';
                                                $icon = 'fa-briefcase';
                                            }

                                            // Label Detail
                                            $tipeMap = [1 => 'Tugas', 2 => 'Rapat', 3 => 'Dinas Luar'];
                                            $labelTipe = $tipeMap[$agenda->activity_type_id] ?? 'Kegiatan';
                                            $namaTim = $agenda->creator && $agenda->creator->team ? $agenda->creator->team->nama_tim : "Umum";
                                            $asalPenugasan = $labelTipe . " dari " . $namaTim;
                                        ?>
                                        <div class="agenda-pill <?php echo e($pillClass); ?>" 
                                             onclick="showDetail('<?php echo e($agenda->title); ?>', '<?php echo e($agenda->location); ?>', '<?php echo e($user->nama_lengkap); ?>', '<?php echo e($agenda->status_laporan); ?>', '<?php echo e($asalPenugasan); ?>')">
                                            <i class="fas <?php echo e($icon); ?> shadow-sm" style="font-size: 0.7rem;"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            <?php endfor; ?>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


    <script src="<?php echo e(asset('js/pages/monitoring-index.js')); ?>"></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/monitoring/index.blade.php ENDPATH**/ ?>