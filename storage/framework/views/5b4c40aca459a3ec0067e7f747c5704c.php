<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <div class="mb-4">
        <h3 class="fw-bold text-dark mb-1">Gatekeeper: Verifikasi Dokumen Honor</h3>
        <p class="text-muted small">Cek kelengkapan berkas fisik/unggahan sebelum honor dijadwalkan cair.</p>
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
                        <th class="ps-4 text-muted small fw-bold py-3">MITRA & KEGIATAN</th>
                        <th class="text-muted small fw-bold py-3 text-center">HONOR</th>
                        <th class="text-muted small fw-bold py-3">DOKUMEN</th>
                        <th class="text-muted small fw-bold py-3">STATUS SAAT INI</th>
                        <th class="pe-4 text-muted small fw-bold py-3 text-end">VALIDASI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $penugasans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $penugasan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-dark"><?php echo e($penugasan->mitra->nama_lengkap); ?></div>
                            <div class="small text-muted"><?php echo e($penugasan->nama_kegiatan_full); ?></div>
                            <small class="badge bg-secondary-subtle text-secondary rounded-pill mt-1">Dibuat oleh: <?php echo e($penugasan->user->nama_lengkap); ?></small>
                        </td>
                        <td class="text-center">
                            <div class="fw-bold text-primary">Rp <?php echo e(number_format($penugasan->total_honor_tugas, 0, ',', '.')); ?></div>
                            <small class="text-muted">Target: <?php echo e(Carbon\Carbon::parse($penugasan->tgl_selesai_target)->format('d/m/Y')); ?></small>
                        </td>
                        <td>
                            <?php if($penugasan->file_pendukung): ?>
                                <a href="<?php echo e(asset('storage/'.$penugasan->file_pendukung)); ?>" target="_blank" class="btn btn-outline-info btn-sm rounded-pill">
                                    <i class="fas fa-file-pdf me-1"></i> Lihat Berkas
                                </a>
                            <?php else: ?>
                                <span class="text-danger small"><i class="fas fa-times-circle me-1"></i> Tidak ada file</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                                $statusClass = [
                                    'Pending' => 'warning',
                                    'Lengkap' => 'success',
                                    'Revisi' => 'danger'
                                ][$penugasan->status_dokumen] ?? 'secondary';
                            ?>
                            <span class="badge bg-<?php echo e($statusClass); ?> rounded-pill px-3"><?php echo e($penugasan->status_dokumen); ?></span>
                        </td>
                        <td class="pe-4 text-end">
                            <form action="<?php echo e(route('honorarium.update-status', $penugasan->id)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="status" value="Lengkap">
                                <input type="hidden" name="finish_task" value="1">
                                <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold">
                                    <i class="fas fa-check-circle me-1"></i> Verifikasi & Selesai
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="fas fa-clipboard-check fs-1 d-block mb-3 opacity-25"></i>
                            Tidak ada pengajuan yang butuh verifikasi hari ini.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/honorarium/verifikasi.blade.php ENDPATH**/ ?>