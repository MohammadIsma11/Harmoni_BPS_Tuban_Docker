<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">Monitoring Penugasan Mitra</h3>
            <p class="text-muted small">Pantau progres pekerjaan mitra dan verifikasi honorarium.</p>
        </div>
        <a href="<?php echo e(route('penugasan-mitra.create')); ?>" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
            <i class="fas fa-plus me-2"></i>Tambah Penugasan
        </a>
    </div>

    <?php if(session('success')): ?>
    <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
        <i class="fas fa-check-circle me-2"></i> <?php echo e(session('success')); ?>

    </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 text-muted small fw-bold py-3">MITRA & SOBAT ID</th>
                        <th class="text-muted small fw-bold py-3">KEGIATAN</th>
                        <th class="text-muted small fw-bold py-3 text-center">TOTAL HONOR</th>
                        <th class="text-muted small fw-bold py-3 text-center">TARGET SELESAI</th>
                        <th class="text-muted small fw-bold py-3 text-center">PROGRES</th>
                        <th class="pe-4 text-muted small fw-bold py-3 text-end">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $penugasans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-dark"><?php echo e($p->mitra->nama_lengkap); ?></div>
                            <small class="text-muted"><?php echo e($p->mitra_id); ?></small>
                        </td>
                        <td>
                            <div class="text-dark small fw-bold"><?php echo e($p->nama_kegiatan_full); ?></div>
                            <?php if($p->anggaran && $p->anggaran->team): ?>
                                <span class="badge bg-primary-subtle text-primary small rounded-pill" style="font-size: 0.6rem;"><?php echo e($p->anggaran->team->nama_tim); ?></span>
                            <?php else: ?>
                                <span class="badge bg-light text-muted small rounded-pill" style="font-size: 0.6rem;">Input Manual</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <span class="fw-bold text-dark">Rp <?php echo e(number_format($p->total_honor_tugas, 0, ',', '.')); ?></span>
                        </td>
                        <td class="text-center">
                            <span class="text-muted small"><?php echo e(Carbon\Carbon::parse($p->tgl_selesai_target)->format('d M Y')); ?></span>
                        </td>
                        <td class="text-center">
                            <?php if($p->status_tugas == 'Selesai'): ?>
                                <span class="badge bg-success rounded-pill px-3">Selesai</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark rounded-pill px-3">Progres</span>
                            <?php endif; ?>
                        </td>
                        <td class="pe-4 text-end">
                            <?php
                                $docStatusClass = ['Pending' => 'warning', 'Lengkap' => 'success', 'Revisi' => 'danger'][$p->status_dokumen] ?? 'secondary';
                            ?>
                            <div class="d-flex flex-column align-items-end">
                                <span class="badge bg-<?php echo e($docStatusClass); ?> rounded-pill px-3 mb-2"><?php echo e($p->status_dokumen); ?></span>
                                <?php if($p->status_tugas == 'Progres'): ?>
                                    <form action="<?php echo e(route('penugasan-mitra.update-status-tugas', $p->id)); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" name="status" value="Selesai" class="btn btn-outline-success btn-xs py-0 px-2 rounded-pill shadow-sm mb-2" style="font-size: 0.65rem;" onclick="return confirm('Tandai tugas ini sebagai Selesai?')">
                                            <i class="fas fa-flag-checkered me-1"></i>Selesaikan
                                        </button>
                                    </form>
                                <?php endif; ?>
                                <div class="d-flex gap-1 justify-content-end">
                                    <a href="<?php echo e(route('penugasan-mitra.spk', $p->id)); ?>" target="_blank" class="btn btn-outline-primary btn-xs py-1 px-3 rounded-pill shadow-sm" style="font-size: 0.65rem;">
                                        <i class="fas fa-print me-1"></i>SPK
                                    </a>
                                    <form action="<?php echo e(route('penugasan-mitra.destroy', $p->id)); ?>" method="POST" onsubmit="return confirm('Hapus penugasan ini? Tindakan ini tidak dapat dibatalkan.')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-outline-danger btn-xs py-1 px-2 rounded-pill shadow-sm" style="font-size: 0.65rem;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fas fa-tasks fs-1 d-block mb-3 opacity-25"></i>
                            Belum ada penugasan mitra yang dibuat.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            <?php echo e($penugasans->links()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/penugasan-mitra/index.blade.php ENDPATH**/ ?>