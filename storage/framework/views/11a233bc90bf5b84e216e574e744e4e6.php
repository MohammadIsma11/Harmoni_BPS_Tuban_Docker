<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4 pb-5">
    
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
        <div class="d-flex align-items-center">
            <div class="bg-primary bg-opacity-10 p-3 rounded-4 me-3 text-primary shadow-sm border border-primary border-opacity-10">
                <i class="fas fa-desktop fa-lg"></i>
            </div>
            <div>
                <h4 class="fw-bold mb-0 text-dark">Monitoring Kehadiran</h4>
                <p class="text-muted small mb-0 text-truncate" style="max-width: 300px;">
                    <i class="fas fa-tag me-1 text-primary"></i> <?php echo e($meeting->title); ?>

                </p>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="<?php echo e(route('meeting.index')); ?>" class="btn btn-white rounded-pill px-4 fw-bold shadow-sm border text-muted">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
            <a href="<?php echo e(route('meeting.print_presensi', $meeting->id)); ?>" target="_blank" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm px-4">
                <i class="fas fa-print me-2"></i>Cetak Daftar Hadir
            </a>
        </div>
    </div>

    <div class="row g-4">
        
        <div class="col-12 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                
                <div style="height: 5px; background: linear-gradient(90deg, #0058a8, #007bff);"></div>
                
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h6 class="fw-bold text-muted mb-0 small text-uppercase" style="letter-spacing: 1px;">Progres Kehadiran</h6>
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 fw-bold"><?php echo e($stats['persen']); ?>%</span>
                    </div>

                    
                    <div class="text-center mb-4">
                        <div class="h2 fw-bold text-dark mb-1"><?php echo e($stats['hadir']); ?> / <?php echo e(count($allParticipants)); ?></div>
                        <p class="text-muted small mb-3">Peserta telah melakukan konfirmasi</p>
                        
                        <div class="progress rounded-pill shadow-sm" style="height: 12px; background-color: #f1f5f9;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated rounded-pill bg-primary" 
                                 role="progressbar" style="width: <?php echo e($stats['persen']); ?>%"></div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4 text-center">
                        <div class="col-6 border-end">
                            <div class="text-muted small mb-1">Sudah TTD</div>
                            <div class="h4 fw-bold text-success mb-0"><?php echo e($stats['hadir']); ?></div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small mb-1">Belum TTD</div>
                            <div class="h4 fw-bold text-danger mb-0"><?php echo e($stats['belum']); ?></div>
                        </div>
                    </div>

                    
                    <div class="p-3 bg-light rounded-4">
                        <div class="d-flex justify-content-between mb-2 pb-2 border-bottom border-white">
                            <span class="text-muted small">Waktu Mulai</span>
                            <span class="small fw-bold text-dark"><?php echo e($meeting->start_time ?? '--:--'); ?> WIB</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 pb-2 border-bottom border-white">
                            <span class="text-muted small">Lokasi</span>
                            <span class="small fw-bold text-dark"><?php echo e($meeting->location); ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted small">Notulis</span>
                            <span class="small fw-bold text-dark"><?php echo e($meeting->notulis->nama_lengkap ?? '-'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="col-12 col-xl-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 p-4 pb-0">
                    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
                        <h6 class="fw-bold text-dark mb-0"><i class="fas fa-list-ul me-2 text-primary"></i>Daftar Partisipan</h6>
                        
                        <ul class="nav nav-pills bg-light p-1 rounded-pill shadow-sm" id="pills-tab" role="tablist">
                            <li class="nav-item">
                                <button class="nav-link active rounded-pill px-4 py-1 fw-bold small" id="all-tab" data-bs-toggle="pill" data-bs-target="#tab-all" type="button">Semua</button>
                            </li>
                            <li class="nav-item ms-1">
                                <button class="nav-link rounded-pill px-4 py-1 fw-bold small" id="not-tab" data-bs-toggle="pill" data-bs-target="#tab-not" type="button">Belum Absen</button>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="card-body p-4">
                    <div class="tab-content" id="pills-tabContent">
                        
                        <div class="tab-pane fade show active" id="tab-all">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle border-0">
                                    <thead class="bg-light bg-opacity-50">
                                        <tr class="text-muted small text-uppercase">
                                            <th class="border-0 fw-bold py-3 ps-3">Peserta</th>
                                            <th class="border-0 fw-bold py-3 text-center">Tim / Bagian</th>
                                            <th class="border-0 fw-bold py-3 text-end pe-3">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $allParticipants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php $isHadir = in_array($p->assigned_to, $presentUserIds); ?>
                                            <tr>
                                                <td class="py-3 ps-3">
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-circle <?php echo e($isHadir ? 'bg-success text-white' : 'bg-secondary bg-opacity-10 text-muted'); ?> me-3 shadow-sm">
                                                            <?php echo e(strtoupper(substr($p->assignee->nama_lengkap, 0, 1))); ?>

                                                        </div>
                                                        <div>
                                                            <div class="fw-bold text-dark small"><?php echo e($p->assignee->nama_lengkap); ?></div>
                                                            <div class="text-muted" style="font-size: 0.65rem;">NIP. <?php echo e($p->assignee->nip ?? '-'); ?></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center small text-muted"><?php echo e($p->assignee->team->nama_tim ?? 'BPS'); ?></td>
                                                <td class="text-end pe-3">
                                                    <?php if($isHadir): ?>
                                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 border border-success border-opacity-10">
                                                            <i class="fas fa-check-circle me-1"></i> Hadir
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-light text-muted rounded-pill px-3 py-2 border">
                                                            <i class="fas fa-clock me-1 text-warning"></i> Menunggu
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        
                        <div class="tab-pane fade" id="tab-not">
                            <div class="row g-3">
                                <?php $countBelum = 0; ?>
                                <?php $__currentLoopData = $allParticipants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if(!in_array($p->assigned_to, $presentUserIds)): ?>
                                        <?php $countBelum++; ?>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="p-3 rounded-4 border bg-white shadow-xs border-start border-4 border-danger">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-danger bg-opacity-10 text-danger rounded-3 p-2 me-3">
                                                        <i class="fas fa-user-clock"></i>
                                                    </div>
                                                    <div class="text-truncate">
                                                        <div class="fw-bold text-dark small text-truncate"><?php echo e($p->assignee->nama_lengkap); ?></div>
                                                        <div class="text-muted" style="font-size: 0.65rem;">Belum Konfirmasi</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                <?php if($countBelum == 0): ?>
                                    <div class="text-center py-5">
                                        <div class="mb-3">
                                            <i class="fas fa-check-double text-success fa-4x opacity-25"></i>
                                        </div>
                                        <h5 class="fw-bold text-muted">Lengkap!</h5>
                                        <p class="text-muted small">Semua partisipan yang diundang telah hadir di lokasi.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="<?php echo e(asset('css/pages/meeting-monitoring.css')); ?>">
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/meeting/monitoring.blade.php ENDPATH**/ ?>