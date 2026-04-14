<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4 pb-5">
    
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold text-dark mb-1">Daftar Tugas Baru</h4>
            <p class="text-muted small mb-0">Kelola penugasan pengawasan lapangan yang perlu segera Anda laporkan.</p>
        </div>
        <div class="bg-white p-2 px-3 rounded-4 shadow-sm border border-primary border-opacity-10">
            <i class="fas fa-tasks text-primary me-2"></i>
            <span class="fw-bold small text-dark">Total: <?php echo e($tugas->count()); ?> Penugasan</span>
        </div>
    </div>

    
    <?php
        $today = \Carbon\Carbon::now()->format('Y-m-d');
        $isCutiHariIni = \App\Models\Absensi::where('user_id', Auth::id())
                            ->where('status', 'Cuti')
                            ->where('start_date', '<=', $today)
                            ->where('end_date', '>=', $today)
                            ->exists();
    ?>

    <?php if($isCutiHariIni): ?>
        <div class="alert alert-danger border-0 shadow-sm rounded-4 p-3 mb-4 d-flex align-items-center">
            <div class="bg-danger bg-opacity-10 p-2 rounded-3 me-3 text-danger">
                <i class="fas fa-user-lock fa-lg"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-0 text-danger">Akses Pelaporan Terkunci</h6>
                <small>Anda tercatat sedang <strong>CUTI</strong> hari ini. Tombol pelaporan dinonaktifkan sementara.</small>
            </div>
        </div>
    <?php endif; ?>

    
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr class="text-muted small text-uppercase">
                        <th class="border-0 py-3 ps-4" style="width: 180px;">Rentang Waktu</th>
                        <th class="border-0 py-3">Nama Tugas / Deskripsi</th>
                        <th class="border-0 py-3">Ditugaskan Oleh</th>
                        <th class="border-0 py-3 text-center" style="width: 240px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $tugas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $countLapor = \App\Models\AssignmentReport::where('agenda_id', $t->id)
                                            ->where('user_id', Auth::id())
                                            ->count();
                            $target = $t->report_target ?? 1;
                            $isSelesai = ($countLapor >= $target);
                        ?>
                        
                        <tr class="transition-row <?php echo e($isCutiHariIni ? 'opacity-75' : ''); ?>">
                            <td class="ps-4">
                                <div class="fw-bold text-dark mb-0" style="font-size: 0.85rem;">
                                    <?php echo e($t->event_date->format('d M')); ?> - <?php echo e($t->end_date->format('d M Y')); ?>

                                </div>
                                
                                
                                <?php if($isSelesai): ?>
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill mt-1" style="font-size: 0.6rem;">
                                        <i class="fas fa-check-circle me-1"></i> Selesai (<?php echo e($countLapor); ?>/<?php echo e($target); ?>)
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill mt-1" style="font-size: 0.6rem;">
                                        <i class="fas fa-hourglass-half me-1"></i> Progres: <?php echo e($countLapor); ?>/<?php echo e($target); ?>

                                    </span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <div class="fw-bold text-primary mb-1"><?php echo e($t->title); ?></div>
                                <div class="small text-muted text-truncate" style="max-width: 300px;" title="<?php echo e($t->description); ?>">
                                    <?php echo e($t->description ?? 'Tidak ada deskripsi tambahan.'); ?>

                                </div>
                            </td>

                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-mini bg-info bg-opacity-10 text-info rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; font-size: 0.7rem; font-weight: 800; border: 1px solid rgba(0,0,0,0.05);">
                                        <?php echo e(strtoupper(substr($t->creator->nama_lengkap ?? 'A', 0, 1))); ?>

                                    </div>
                                    <div class="small text-dark fw-bold">
                                        <?php echo e($t->creator->nama_lengkap ?? 'Admin'); ?>

                                    </div>
                                </div>
                            </td>

                            <td class="text-center pe-4">
                                <div class="d-flex justify-content-center align-items-center gap-2">
                                    
                                    <?php if($t->mode_surat === 'generate'): ?>
                                        <a href="<?php echo e(route('assignment.download-spt', $t->id)); ?>" class="btn btn-outline-success btn-custom-action fw-bold" target="_blank">
                                            <i class="fas fa-file-download me-1"></i> SPT
                                        </a>
                                    <?php elseif($t->surat_tugas_path): ?>
                                        <a href="<?php echo e(asset('storage/' . $t->surat_tugas_path)); ?>" class="btn btn-outline-danger btn-custom-action fw-bold" target="_blank">
                                            <i class="fas fa-file-pdf me-1"></i> ST
                                        </a>
                                    <?php endif; ?>

                                   
                                    <?php if($isCutiHariIni): ?>
                                        <button class="btn btn-secondary btn-custom-action fw-bold opacity-50" disabled>
                                            <i class="fas fa-lock me-1"></i> Locked
                                        </button>
                                    <?php elseif($t->mode_surat === 'generate' && $t->status_approval !== 'Approved'): ?>
                                        
                                        <button class="btn btn-light btn-custom-action fw-bold text-muted border-0 shadow-none" disabled style="cursor: not-allowed;">
                                            <i class="fas fa-clock me-1"></i> Antri
                                        </button>
                                    <?php elseif($isSelesai): ?>
                                        <button class="btn btn-light btn-custom-action fw-bold text-success border-success" disabled>
                                            <i class="fas fa-check me-1"></i> Done
                                        </button>
                                    <?php else: ?>
                                        <a href="<?php echo e(route('task.create', ['id' => $t->id])); ?>" class="btn btn-primary btn-custom-action fw-bold">
                                            <i class="fas fa-edit me-1"></i> Lapor
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <img src="https://illustrations.popsy.co/amber/clipboard-check.svg" style="height: 140px;" class="mb-3 opacity-75">
                                <h6 class="fw-bold text-muted">Semua Beres!</h6>
                                <p class="text-muted small">Belum ada penugasan baru yang perlu dilaporkan.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

    <link rel="stylesheet" href="<?php echo e(asset('css/pages/task-index.css')); ?>">
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/task/index.blade.php ENDPATH**/ ?>