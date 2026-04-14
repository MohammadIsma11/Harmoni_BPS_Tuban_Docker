<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4 pb-5">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3 mt-4">
        <div>
            <h4 class="fw-bold mb-0 text-dark"><i class="fas fa-shield-alt text-danger me-2"></i>Akses Super: Monitoring Arsip</h4>
            <p class="text-muted small mb-0">Pusat data seluruh riwayat kegiatan organisasi dalam satu pintu.</p>
        </div>

        
        <?php if(request('type') == 1): ?>
        <div class="d-flex gap-2">
            <a href="<?php echo e(route('history.pdf_rekap', request()->all())); ?>" class="btn btn-outline-danger rounded-pill px-3 fw-bold shadow-sm">
                <i class="fas fa-file-pdf me-1"></i> Rekap PDF
            </a>
            <a href="<?php echo e(route('history.excel_rekap', request()->all())); ?>" class="btn btn-outline-success rounded-pill px-3 fw-bold shadow-sm">
                <i class="fas fa-file-excel me-1"></i> Rekap Excel
            </a>
        </div>
        <?php endif; ?>
    </div>

    
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form action="<?php echo e(route('super.access.index')); ?>" method="GET" class="row g-2">
                <div class="col-md-7">
                    <input type="text" name="search" class="form-control bg-light border-0" placeholder="Cari judul kegiatan..." value="<?php echo e(request('search')); ?>">
                </div>
                <div class="col-md-3">
                    <select name="type" class="form-select bg-light border-0">
                        <option value="">Semua Tipe Kegiatan</option>
                        <option value="1" <?php echo e(request('type') == 1 ? 'selected' : ''); ?>>Tugas Lapangan</option>
                        <option value="2" <?php echo e(request('type') == 2 ? 'selected' : ''); ?>>Rapat Dinas</option>
                        <option value="3" <?php echo e(request('type') == 3 ? 'selected' : ''); ?>>Dinas Luar</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-danger w-100 rounded-3 fw-bold shadow-sm">Filter</button>
                </div>
            </form>
        </div>
    </div>

    
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-dark text-white">
                    <tr class="small text-uppercase">
                        <th class="py-3 ps-4" style="width: 15%;">Tanggal</th>
                        <th style="width: 35%;">Kegiatan</th>
                        <th>Tipe</th>
                        <th>Penanggung Jawab / Pelapor</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $allActivities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $act): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-dark"><?php echo e(\Carbon\Carbon::parse($act->event_date)->translatedFormat('d M Y')); ?></div>
                            <small class="text-muted">
                                <i class="far fa-clock me-1"></i>
                                <?php if(!in_array($act->activity_type_id, [2, 3])): ?>
                                    <?php echo e(\Carbon\Carbon::parse($act->updated_at)->format('H:i')); ?> WIB
                                <?php else: ?>
                                    <?php echo e($act->start_time); ?> WIB
                                <?php endif; ?>
                            </small>
                        </td>
                        <td>
                            <div class="fw-bold text-dark mb-1"><?php echo e($act->title); ?></div>
                            <small class="text-muted">
                                <i class="fas fa-map-marker-alt me-1 text-danger"></i> 
                                <?php if($act->activity_type_id == 2): ?>
                                    Ruang Rapat
                                <?php else: ?>
                                    <?php echo e($act->location ?? 'Lokasi Luar Kantor'); ?>

                                <?php endif; ?>
                            </small>
                        </td>
                        <td>
                            <?php if($act->activity_type_id == 2): ?>
                                <span class="badge bg-primary bg-opacity-10 text-primary px-3 rounded-pill">Rapat</span>
                            <?php elseif($act->activity_type_id == 3): ?>
                                <span class="badge bg-success bg-opacity-10 text-success px-3 rounded-pill">Dinas Luar</span>
                            <?php else: ?>
                                <span class="badge bg-info bg-opacity-10 text-info px-3 rounded-pill">Lapangan</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm bg-light rounded-circle me-2 d-flex align-items-center justify-content-center fw-bold text-primary border" style="width: 30px; height: 30px; font-size: 0.7rem;">
                                    <?php echo e(strtoupper(substr($act->assignee->nama_lengkap ?? $act->notulis->nama_lengkap ?? 'U', 0, 1))); ?>

                                </div>
                                <div>
                                    <div class="small fw-bold text-dark"><?php echo e($act->assignee->nama_lengkap ?? $act->notulis->nama_lengkap ?? '-'); ?></div>
                                    <small class="text-muted" style="font-size: 0.65rem;">Oleh: <?php echo e($act->creator->nama_lengkap ?? 'System'); ?></small>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            <?php if($act->activity_type_id == 2): ?>
                                <a href="<?php echo e(route('meeting.history.detail', $act->id)); ?>?from=super" class="btn btn-sm btn-white border rounded-pill px-3 shadow-xs">
                                    <i class="fas fa-eye me-1 text-primary"></i> Detail
                                </a>
                            <?php elseif($act->activity_type_id == 3): ?>
                                <a href="<?php echo e(route('meeting.history.detail_dinas', $act->id)); ?>?from=super" class="btn btn-sm btn-white border rounded-pill px-3 shadow-xs">
                                    <i class="fas fa-eye me-1 text-success"></i> Detail
                                </a>
                            <?php else: ?>
                                <a href="<?php echo e(route('history.detail', $act->id)); ?>?from=super" class="btn btn-sm btn-white border rounded-pill px-3 shadow-xs">
                                    <i class="fas fa-eye me-1 text-info"></i> Detail
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <i class="fas fa-folder-open fa-3x text-muted opacity-25 mb-3"></i>
                            <h6 class="fw-bold text-muted">Tidak ada data kegiatan ditemukan.</h6>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($allActivities->hasPages()): ?>
        <div class="card-footer bg-white border-0 p-3">
            <?php echo e($allActivities->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>

<link rel="stylesheet" href="<?php echo e(asset('css/pages/super-access-index.css')); ?>">
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/super_access/index.blade.php ENDPATH**/ ?>