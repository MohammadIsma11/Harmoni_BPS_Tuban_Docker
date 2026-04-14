<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4 pb-5">
    <div class="mb-4 mt-4">
        <a href="javascript:history.back()" class="btn btn-light btn-sm rounded-3 border mb-3">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Arsip
        </a>
        <div class="d-flex align-items-center">
            <div class="bg-success bg-opacity-10 p-3 rounded-4 me-3 text-success">
                <i class="fas fa-route fa-lg"></i>
            </div>
            <div>
                <h4 class="fw-bold mb-0">Detail Laporan Dinas Luar</h4>
                <p class="text-muted small mb-0">Informasi lengkap hasil penugasan lapangan.</p>
            </div>
        </div>
    </div>

    <div class="row g-4">
        
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-body p-4">
                    <div class="mb-4">
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 mb-2">JUDUL AGENDA</span>
                        <h5 class="fw-bold text-dark"><?php echo e($meeting->title); ?></h5>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-4 h-100">
                                <small class="text-muted d-block mb-1">Lokasi Kunjungan:</small>
                                <div class="fw-bold text-dark"><i class="fas fa-map-marker-alt text-danger me-2"></i><?php echo e($meeting->location); ?></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-4 h-100">
                                <small class="text-muted d-block mb-1">Tanggal & Waktu:</small>
                                <div class="fw-bold text-dark">
                                    <i class="far fa-calendar-alt text-primary me-2"></i><?php echo e(\Carbon\Carbon::parse($meeting->event_date)->translatedFormat('d F Y')); ?>

                                </div>
                                <small class="text-muted"><?php echo e($meeting->start_time); ?> WIB</small>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-top">
                        <h6 class="fw-bold mb-3">Dokumen Hasil Dinas</h6>
                        <div class="d-flex align-items-center p-3 border rounded-4 bg-white shadow-xs">
                            <div class="bg-light p-3 rounded-3 me-3 text-success">
                                <i class="fas fa-file-pdf fa-2x"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold text-dark small mb-0">File Laporan/SPD</div>
                                <small class="text-muted" style="font-size: 0.65rem;">Klik tombol di samping untuk mengunduh</small>
                            </div>
                            <a href="<?php echo e(asset('storage/' . $meeting->notulensi_hasil)); ?>" target="_blank" class="btn btn-success btn-sm rounded-pill px-3">
                                <i class="fas fa-download me-1"></i> Unduh
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 mb-4 h-100">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3"><i class="fas fa-camera me-2 text-danger"></i>Dokumentasi Lapangan</h6>
                    
                    <?php if($meeting->dokumentasi_path): ?>
                        <?php $fotos = json_decode($meeting->dokumentasi_path); ?>
                        <div class="row g-2">
                            <?php $__currentLoopData = $fotos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $foto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-6">
                                    <div class="position-relative img-hover-container rounded-3 overflow-hidden shadow-sm" style="height: 140px;">
                                        <img src="<?php echo e(asset('storage/' . $foto)); ?>" class="w-100 h-100 object-fit-cover" alt="Dokumentasi">
                                        
                                        
                                        <div class="img-overlay d-flex align-items-center justify-content-center gap-2">
                                            <a href="<?php echo e(asset('storage/' . $foto)); ?>" target="_blank" class="btn btn-light btn-sm rounded-circle shadow-sm" title="Lihat Foto">
                                                <i class="fas fa-search-plus text-primary"></i>
                                            </a>
                                            <a href="<?php echo e(asset('storage/' . $foto)); ?>" download class="btn btn-light btn-sm rounded-circle shadow-sm" title="Download">
                                                <i class="fas fa-download text-success"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-images fa-3x text-muted opacity-25 mb-3"></i>
                            <p class="text-muted small">Tidak ada foto dokumentasi.</p>
                        </div>
                    <?php endif; ?>

                    <div class="mt-4 pt-4 border-top">
                        <h6 class="fw-bold mb-2 small text-uppercase text-muted">Pelapor:</h6>
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm-table bg-primary text-white me-3" style="width: 45px; height: 45px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                                <?php echo e(strtoupper(substr($meeting->assignee->nama_lengkap ?? 'A', 0, 1))); ?>

                            </div>
                            <div>
                                <div class="fw-bold text-dark"><?php echo e($meeting->assignee->nama_lengkap ?? 'N/A'); ?></div>
                                <div class="small text-muted"><?php echo e($meeting->assignee->role ?? 'Pegawai'); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="<?php echo e(asset('css/pages/meeting-detail-dinas.css')); ?>">
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/meeting/detail_dinas.blade.php ENDPATH**/ ?>