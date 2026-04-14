<?php
    \Carbon\Carbon::setLocale('id');
?>


<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold mb-0">Daftar Laporan Pengawasan</h4>
            </div>

            <form action="<?php echo e(route('manajemen.laporan')); ?>" method="GET" class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Cari pegawai/kegiatan..." value="<?php echo e(request('search')); ?>">
                    </div>
                </div>
                <div class="col-md-2">
                    <select name="month" class="form-select">
                        <option value="">Semua Bulan</option>
                        <?php $__currentLoopData = range(1, 12); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($m); ?>" <?php echo e(request('month') == $m ? 'selected' : ''); ?>>
                                <?php echo e(\Carbon\Carbon::create()->month($m)->translatedFormat('F')); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-7 text-end">
                    <div class="btn-group">
                        <button type="submit" class="btn btn-primary px-3 rounded-start-pill">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                        <a href="<?php echo e(route('manajemen.laporan')); ?>" class="btn btn-light border px-3">
                            <i class="fas fa-sync-alt"></i>
                        </a>
                    </div>

                    <a href="<?php echo e(route('manajemen.laporan.cetak', request()->all())); ?>" class="btn btn-danger rounded-pill px-3 ms-2">
                        <i class="fas fa-print me-1"></i> Cetak Rekap (PDF)
                    </a>

                    <a href="<?php echo e(route('manajemen.laporan.excel', request()->all())); ?>" class="btn btn-success rounded-pill px-3 ms-1">
                        <i class="fas fa-file-excel me-1"></i> Excel
                    </a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Pegawai</th>
                            <th>Tujuan Pengawasan</th>
                            <th>No. Surat Tugas</th>
                            <th class="text-center">Hari & Tanggal</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $laporan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <div class="fw-bold text-dark"><?php echo e($l->assignee->nama_lengkap); ?></div>
                                <small class="text-muted"><?php echo e($l->assignee->role); ?></small>
                            </td>
                            <td>
                                <div class="fw-bold"><?php echo e($l->title); ?></div>
                                <small class="text-muted"><i class="fas fa-map-marker-alt text-danger me-1"></i><?php echo e($l->location); ?></small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border fw-normal"><?php echo e($l->nomor_surat_tugas ?? '-'); ?></span>
                            </td>
                            <td class="text-center">
                                <div class="small fw-bold"><?php echo e(\Carbon\Carbon::parse($l->event_date)->translatedFormat('l')); ?></div>
                                <div class="text-muted small"><?php echo e(\Carbon\Carbon::parse($l->event_date)->translatedFormat('d/m/Y')); ?></div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill small">
                                    <i class="fas fa-check-circle me-1"></i> Selesai
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="<?php echo e(route('riwayat.export', $l->id)); ?>" class="btn btn-sm btn-outline-danger rounded-circle" title="Cetak Dokumen Satuan">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                    <a href="<?php echo e(route('riwayat.detail', $l->id)); ?>" class="btn btn-sm btn-outline-info rounded-circle" title="Detail Laporan">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fas fa-folder-open fa-3x text-light mb-3 d-block"></i>
                                <span class="text-muted">Tidak ada laporan pengawasan yang ditemukan.</span>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="<?php echo e(asset('css/pages/manajemen-laporan-pengawasan.css')); ?>">
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/manajemen/laporan_pengawasan.blade.php ENDPATH**/ ?>