<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4 pb-5">
    
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3 mt-4">
        <div class="d-flex align-items-center">
            <div class="bg-success bg-opacity-10 p-3 rounded-4 me-3 text-success shadow-sm border border-success border-opacity-10">
                <i class="fas fa-file-contract fa-lg"></i>
            </div>
            <div>
                <h4 class="fw-bold mb-0 text-dark">Detail Riwayat Rapat</h4>
                <p class="text-muted small mb-0 text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 1px;">Arsip Digital Notulensi</p>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="javascript:history.back()" class="btn btn-white rounded-pill px-4 fw-bold shadow-sm border text-muted">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
            <a href="<?php echo e(route('meeting.print_presensi', $meeting->id)); ?>" target="_blank" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm px-4">
                <i class="fas fa-print me-2"></i>Cetak Presensi
            </a>
        </div>
    </div>

    
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
        <div class="card-body p-4">
            <div class="row align-items-center g-4">
                <div class="col-lg-6 border-end-lg">
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-pill mb-2 fw-bold" style="font-size: 0.6rem;">NAMA KEGIATAN</span>
                    <h4 class="fw-bold text-dark mb-3"><?php echo e($meeting->title); ?></h4>
                    <div class="d-flex gap-3">
                        <div class="d-flex align-items-center p-2 rounded-3 bg-light border shadow-xs" style="flex: 1;">
                            <div class="bg-primary text-white rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 0.7rem;">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <div class="text-truncate">
                                <small class="text-muted d-block lh-1 mb-1" style="font-size: 0.6rem; font-weight: 700;">Pimpinan</small>
                                <span class="fw-bold text-dark small"><?php echo e($meeting->creator->nama_lengkap ?? 'Admin'); ?></span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center p-2 rounded-3 bg-light border shadow-xs" style="flex: 1;">
                            <div class="bg-info text-white rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 0.7rem;">
                                <i class="fas fa-pen-nib"></i>
                            </div>
                            <div class="text-truncate">
                                <small class="text-muted d-block lh-1 mb-1" style="font-size: 0.6rem; font-weight: 700;">Notulis</small>
                                <span class="fw-bold text-dark small"><?php echo e($meeting->notulis->nama_lengkap ?? '-'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                
                
                <div class="col-lg-6 ps-lg-4">
                    <h6 class="fw-bold text-dark mb-2 small text-uppercase"><i class="fas fa-paperclip me-2 text-warning"></i>Lampiran Materi</h6>
                    <?php if($meeting->materi_path): ?>
                        <div class="d-flex align-items-center p-3 rounded-4 border bg-white shadow-xs">
                            <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-2 me-3">
                                <i class="fas fa-file-powerpoint fa-lg"></i>
                            </div>
                            <div class="flex-grow-1 text-truncate">
                                <div class="fw-bold text-dark mb-0 small text-truncate">Dokumen Materi Rapat</div>
                                <div class="text-muted small mb-0" style="font-size: 0.65rem;">Klik untuk mengunduh file pendukung</div>
                            </div>
                            <a href="<?php echo e(asset('storage/' . $meeting->materi_path)); ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">
                                <i class="fas fa-download me-1"></i> Unduh
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="p-3 rounded-4 border bg-light text-center border-dashed">
                            <small class="text-muted italic small">Tidak ada file materi.</small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    
    <div class="row g-4">
        
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-header bg-white border-0 p-4 pb-0">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="fas fa-file-alt text-primary me-2"></i>Hasil Pembahasan (Risalah)
                    </h6>
                </div>
                <div class="card-body p-4 text-center d-flex flex-column align-items-center justify-content-center">
                    <?php if($meeting->notulensi_hasil): ?>
                        <div class="mb-4">
                            <i class="fas fa-file-pdf text-danger opacity-50" style="font-size: 6rem;"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">Dokumen Notulensi Tersedia</h5>
                        <p class="text-muted small px-5 mb-4">Hasil keputusan rapat telah diarsipkan dalam bentuk dokumen digital. Silakan klik tombol di bawah untuk melihat atau mengunduh.</p>
                        
                        <div class="d-flex gap-2">
                            <a href="<?php echo e(asset('storage/' . $meeting->notulensi_hasil)); ?>" target="_blank" class="btn btn-primary rounded-pill px-5 fw-bold shadow">
                                <i class="fas fa-eye me-2"></i> Lihat Dokumen
                            </a>
                            <a href="<?php echo e(asset('storage/' . $meeting->notulensi_hasil)); ?>" download class="btn btn-outline-primary rounded-pill px-4 fw-bold">
                                <i class="fas fa-download"></i>
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-file-excel fa-3x mb-3 text-muted opacity-25"></i>
                            <h6 class="text-muted fw-bold">Belum Ada Notulensi</h6>
                            <p class="small text-muted">File hasil rapat belum diunggah oleh notulis.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        
        <div class="col-lg-5">
            <div class="row g-4">
                
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 text-dark">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-3 small text-uppercase" style="letter-spacing: 1px;"><i class="fas fa-images me-2 text-danger"></i>Dokumentasi Kegiatan</h6>
                            <?php $photos = json_decode($meeting->dokumentasi_path, true) ?? []; ?>
                            <?php if(count($photos) > 0): ?>
                                <div class="row g-2">
                                    <?php $__currentLoopData = $photos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $photo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="col-4">
                                            <div class="position-relative img-container rounded-3 overflow-hidden shadow-sm border">
                                                <img src="<?php echo e(asset('storage/' . $photo)); ?>" class="img-fluid w-100" style="height: 80px; object-fit: cover;">
                                                <div class="img-overlay d-flex align-items-center justify-content-center gap-1">
                                                    <a href="<?php echo e(asset('storage/' . $photo)); ?>" target="_blank" class="btn btn-xs btn-light rounded-circle"><i class="fas fa-search-plus text-primary"></i></a>
                                                    <a href="<?php echo e(asset('storage/' . $photo)); ?>" download class="btn btn-xs btn-primary rounded-circle"><i class="fas fa-download"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php else: ?>
                                <div class="py-4 bg-light rounded-3 border border-dashed text-center">
                                    <small class="text-muted">Tidak ada foto dokumentasi.</small>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 text-dark border-start border-4 border-success">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-3 small text-uppercase" style="letter-spacing: 1px;"><i class="fas fa-users me-2 text-success"></i>Daftar Hadir Terverifikasi</h6>
                            <div class="list-group list-group-flush overflow-auto pe-1" style="max-height: 250px;">
                                <?php $__currentLoopData = $semuaPeserta; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php $isHadir = in_array($p->assigned_to, $userSudahHadir); ?>
                                    <div class="list-group-item px-0 py-2 border-0 d-flex align-items-center <?php echo e($isHadir ? '' : 'opacity-40'); ?>">
                                        <div class="avatar-circle <?php echo e($isHadir ? 'bg-success text-white' : 'bg-light text-muted border'); ?> rounded-circle me-2">
                                            <?php echo e(strtoupper(substr($p->assignee->nama_lengkap, 0, 1))); ?>

                                        </div>
                                        <div class="small flex-grow-1 text-truncate pe-1">
                                            <div class="fw-bold text-dark text-truncate" style="font-size: 0.75rem;"><?php echo e($p->assignee->nama_lengkap); ?></div>
                                            <div class="text-muted" style="font-size: 0.6rem;"><?php echo e($p->assignee->team->nama_tim ?? 'BPS Kabupaten'); ?></div>
                                        </div>
                                        <i class="fas <?php echo e($isHadir ? 'fa-check-circle text-success' : 'fa-times-circle text-muted'); ?>" style="font-size: 0.8rem;"></i>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="<?php echo e(asset('css/pages/meeting-detail-history.css')); ?>">
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/meeting/detail_history.blade.php ENDPATH**/ ?>