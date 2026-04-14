<?php
    \Carbon\Carbon::setLocale('id');
?>



<?php $__env->startSection('content'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/pages/dashboard.css')); ?>">

<div class="container-fluid px-4 pb-5">
    
    <div class="dashboard-header d-flex justify-content-between align-items-center">
        <div>
            <h2 class="fw-bold mb-1 text-white">Halo, <?php echo e($nama); ?> 👋</h2>
            <p class="opacity-75 mb-0 text-uppercase small letter-spacing-1 fw-medium text-white">
                <i class="fas fa-id-badge me-2"></i><?php echo e($role); ?> &bull; <?php echo e($tim); ?>

            </p>
        </div>
        <div class="text-end d-none d-md-block text-white">
            <div class="h5 fw-bold mb-0" style="color: #ffda6a;">
                <?php echo e(\Carbon\Carbon::now()->translatedFormat('l, d F Y')); ?>

            </div>
            <small class="opacity-75">BPS Kabupaten Tuban</small>
        </div>
    </div>

    
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card stat-card shadow-sm h-100 p-3 border-0 animate-up">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3"><i class="fas fa-user-friends"></i></div>
                    <div>
                        <p class="text-muted small fw-bold text-uppercase mb-1">
                            <?php if($role == 'Pegawai'): ?> Tim Saya <?php elseif($role == 'Katim'): ?> Anggota Tim <?php else: ?> Total Pegawai <?php endif; ?>
                        </p>
                        <h3 class="fw-bold mb-0 text-dark"><?php echo e($total_pegawai); ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card shadow-sm h-100 p-3 border-0 animate-up">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-info bg-opacity-10 text-info me-3"><i class="fas fa-layer-group"></i></div>
                    <div>
                        <p class="text-muted small fw-bold text-uppercase mb-1">Total Agenda</p>
                        <h3 class="fw-bold mb-0 text-dark"><?php echo e($total_agenda); ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card shadow-sm h-100 p-3 border-0 animate-up">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-success bg-opacity-10 text-success me-3"><i class="fas fa-clipboard-check"></i></div>
                    <div>
                        <p class="text-muted small fw-bold text-uppercase mb-1">Selesai Lapor</p>
                        <h3 class="fw-bold mb-0 text-dark"><?php echo e($tugas_selesai); ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <?php if($role == 'Kepala' || $role == 'Admin'): ?>
    <div class="row g-4 mb-4">
        
        <div class="col-lg-6">
            <div class="dashboard-panel animate-up">
                <h6 class="fw-bold text-dark mb-4"><i class="fas fa-trophy me-2 text-warning"></i>7 Tim Paling Aktif (<?php echo e(\Carbon\Carbon::parse(request('filter_bulan', date('Y-m')))->translatedFormat('F Y')); ?>)</h6>
                <div class="row">
                    <?php $__empty_1 = true; $__currentLoopData = $top_teams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t_team): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small fw-bold text-secondary text-truncate" style="max-width: 150px;"><?php echo e($t_team->nama_tim); ?></span>
                                <span class="badge bg-primary bg-opacity-10 text-primary" style="font-size: 0.65rem;"><?php echo e($t_team->agendas_count); ?> Agenda</span>
                            </div>
                            <div class="progress progress-custom">
                                <?php 
                                    $maxCount = $top_teams->first()->agendas_count ?? 1;
                                    $percent = ($t_team->agendas_count / ($maxCount > 0 ? $maxCount : 1)) * 100;
                                ?>
                                <div class="progress-bar progress-bar-custom" role="progressbar" style="width: <?php echo e($percent); ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="col-12 text-muted small">Data belum tersedia.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        
        <div class="col-lg-6">
            <div class="dashboard-panel animate-up">
                <h6 class="fw-bold text-dark mb-4"><i class="fas fa-chart-bar me-2 text-success"></i>Tren Skala Tugas Tahun <?php echo e(date('Y')); ?></h6>
                <div class="row g-2">
                    <?php $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des']; ?>
                    <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-4 mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted fw-bold" style="width: 25px; font-size: 0.65rem;"><?php echo e($m); ?></span>
                            <div class="trend-bar flex-grow-1">
                                <?php 
                                    $val = $monthly_stats[$idx+1] ?? 0; 
                                    $maxM = (count($monthly_stats) > 0) ? max($monthly_stats) : 1;
                                    $pctM = ($val / ($maxM ?: 1)) * 100;
                                ?>
                                <div class="trend-fill" style="width: <?php echo e($pctM); ?>%"></div>
                            </div>
                            <span class="small fw-bold text-dark" style="font-size: 0.65rem;"><?php echo e($val); ?></span>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    
    <div class="card table-container shadow-sm overflow-hidden border-0 animate-up">
        <div class="card-header bg-white border-0 px-4 py-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h5 class="fw-bold mb-0 text-dark">Agenda & Tugas Terkini</h5>
                    <small class="text-muted">Periode: <?php echo e(\Carbon\Carbon::parse(request('filter_bulan', date('Y-m')))->translatedFormat('F Y')); ?></small>
                </div>

                <?php if($role == 'Kepala' || $role == 'Admin'): ?>
                <div class="filter-wrapper d-flex gap-2">
                    <form action="<?php echo e(route('dashboard')); ?>" method="GET" class="d-flex gap-2">
                        <select name="filter_tim" class="form-select form-select-sm border-light bg-light rounded-pill px-3" style="min-width: 160px;">
                            <option value="">Semua Tim</option>
                            <?php $__currentLoopData = $list_tim; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($t->id); ?>" <?php echo e(request('filter_tim') == $t->id ? 'selected' : ''); ?>><?php echo e($t->nama_tim); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <input type="month" name="filter_bulan" class="form-control form-select-sm border-light bg-light rounded-pill px-3" value="<?php echo e(request('filter_bulan', date('Y-m'))); ?>">
                        <button type="submit" class="btn btn-primary btn-sm rounded-circle shadow-sm"><i class="fas fa-search"></i></button>
                        <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-light btn-sm rounded-circle shadow-sm"><i class="fas fa-sync-alt"></i></a>
                    </form>
                </div>
                <?php endif; ?>

                <a href="<?php echo e(route('agenda.all')); ?>" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold">
                    Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4" style="width: 35%;">Kegiatan & Tim Kerja</th>
                        <th style="width: 25%;">Petugas Pelaksana</th>
                        <th class="text-center" style="width: 20%;">Jadwal Pelaksanaan</th>
                        <th class="text-center" style="width: 20%;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $agenda_terbaru; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agenda): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $eventDate = \Carbon\Carbon::parse($agenda->event_date);
                        $endDate = \Carbon\Carbon::parse($agenda->end_date);
                        $today = now()->startOfDay();
                        $isUrgent = ($agenda->status_laporan != 'Selesai') && ($eventDate->isToday() || $eventDate->isTomorrow());

                        if($agenda->status_laporan == 'Selesai') {
                            $statusLabel = 'SELESAI'; $statusClass = 'status-selesai';
                        } elseif ($today->lt($eventDate->startOfDay())) {
                            $statusLabel = 'AKAN DATANG'; $statusClass = 'status-akan-datang';
                        } else {
                            $statusLabel = 'SEDANG BERJALAN'; $statusClass = 'status-berjalan';
                        }
                    ?>
                    <tr class="<?php echo e($isUrgent ? 'urgent-row' : ''); ?>">
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <?php if($isUrgent): ?>
                                    <span class="badge bg-danger p-1 rounded-circle me-2 animate-pulse-red" style="width: 10px; height: 10px;"></span>
                                <?php endif; ?>
                                <div>
                                    <div class="fw-bold text-dark mb-0"><?php echo e($agenda->title); ?></div>
                                    <small class="text-muted fw-medium"><i class="fas fa-users me-1 text-primary"></i>Tugas dari : <?php echo e($agenda->creator_team_name ?? '-'); ?></small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle me-3"><?php echo e(strtoupper(substr($agenda->assignee->nama_lengkap ?? '?', 0, 1))); ?></div>
                                <div>
                                    <div class="fw-semibold text-secondary" style="font-size: 0.85rem;"><?php echo e($agenda->assignee->nama_lengkap ?? 'Tanpa Nama'); ?></div>
                                    <small class="text-muted text-uppercase" style="font-size: 0.65rem; font-weight: 700;"><?php echo e($agenda->assignee->role ?? ''); ?></small>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="badge bg-white <?php echo e($isUrgent ? 'border-danger text-danger' : 'border-light text-dark'); ?> fw-bold px-3 py-2 border shadow-sm" style="font-size: 0.75rem;">
                                <?php echo e($eventDate->translatedFormat('d M')); ?> - <?php echo e($endDate->translatedFormat('d M Y')); ?>

                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-pill-custom <?php echo e($statusClass); ?> border shadow-sm">
                                <i class="fas <?php echo e($statusLabel == 'SELESAI' ? 'fa-check-circle' : ($statusLabel == 'AKAN DATANG' ? 'fa-clock' : 'fa-spinner')); ?> me-1"></i>
                                <?php echo e($statusLabel); ?>

                            </span>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="4" class="text-center py-5 text-muted">Tidak ada agenda aktif ditemukan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-white border-0 px-4 py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="small text-muted fw-medium">
                    Menampilkan <span class="text-dark fw-bold"><?php echo e($agenda_terbaru->firstItem() ?? 0); ?></span> sampai <span class="text-dark fw-bold"><?php echo e($agenda_terbaru->lastItem() ?? 0); ?></span> dari <span class="text-dark fw-bold"><?php echo e($agenda_terbaru->total()); ?></span> agenda
                </div>
                <div class="pagination-custom">
                    <?php echo e($agenda_terbaru->appends(request()->query())->links('pagination::bootstrap-5')); ?>

                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/dashboard.blade.php ENDPATH**/ ?>