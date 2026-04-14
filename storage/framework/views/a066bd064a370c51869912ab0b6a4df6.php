<?php $__env->startSection('content'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/pages/meeting-presensi.css')); ?>">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card card-presensi">
                <div class="header-presensi">
                    <i class="fas fa-file-signature fa-3x mb-3"></i>
                    <h3 class="fw-bold mb-0">Daftar Hadir Digital</h3>
                    <p class="small opacity-75 mb-0">BPS Kabupaten Tuban</p>
                </div>

                <div class="card-body p-4 p-md-5">
                    <?php if(session('success')): ?>
                        <div class="text-center py-4">
                            <div class="mb-3">
                                <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
                            </div>
                            <h4 class="fw-bold">Berhasil!</h4>
                            <p class="text-muted"><?php echo e(session('success')); ?></p>
                            <p class="small text-muted mt-3">Mengalihkan ke halaman agenda dalam <span id="timer">3</span> detik...</p>
                            <a href="<?php echo e(route('meeting.index')); ?>" class="btn btn-primary rounded-pill px-5">Kembali Sekarang</a>
                        </div>
                    <?php elseif($alreadySigned): ?>
                        <div class="text-center py-4">
                            <div class="mb-3">
                                <i class="fas fa-user-check text-primary" style="font-size: 5rem;"></i>
                            </div>
                            <h4 class="fw-bold">Sudah Mengisi</h4>
                            <p class="text-muted">Kehadiran Anda pada rapat <strong><?php echo e($agenda->title); ?></strong> sudah tercatat.</p>
                            <a href="<?php echo e(route('meeting.index')); ?>" class="btn btn-outline-primary rounded-pill px-5">Kembali</a>
                        </div>
                    <?php else: ?>
                        <div class="mb-4">
                            <h5 class="fw-bold text-dark mb-1"><?php echo e($agenda->title); ?></h5>
                            <p class="text-muted small">
                                <i class="fas fa-calendar-alt me-1"></i> <?php echo e($agenda->event_date->format('d M Y')); ?> 
                                <i class="fas fa-clock ms-2 me-1"></i> <?php echo e($agenda->start_time ?? 'WIB'); ?>

                            </p>
                        </div>

                        <div class="user-info-box">
                            <div class="row small">
                                <div class="col-4 text-muted">Nama Lengkap</div>
                                <div class="col-8 fw-bold text-dark">: <?php echo e(auth()->user()->nama_lengkap); ?></div>
                                <div class="col-4 text-muted mt-2">NIP</div>
                                <div class="col-8 fw-bold text-dark mt-2">: <?php echo e(auth()->user()->nip); ?></div>
                            </div>
                        </div>

                        <form action="<?php echo e(route('meeting.presensi.store')); ?>" method="POST" id="signature-form">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="agenda_id" value="<?php echo e($agenda->id); ?>">
                            <input type="hidden" name="signature" id="signature-value">

                            <div class="d-flex justify-content-between align-items-end mb-2">
                                <label class="small fw-bold text-secondary">Goreskan Tanda Tangan:</label>
                                <button type="button" id="clear-btn" class="btn btn-link btn-sm text-danger text-decoration-none p-0">
                                    <i class="fas fa-eraser me-1"></i> Hapus & Ulangi
                                </button>
                            </div>

                            <div class="signature-wrapper">
                                <canvas id="signature-pad"></canvas>
                            </div>

                            <p class="small text-muted mb-4 text-center">
                                <i class="fas fa-info-circle me-1 text-primary"></i> 
                                Gunakan jari atau stylus untuk menandatangani layar.
                            </p>

                            <button type="submit" class="btn btn-primary btn-simpan w-100 rounded-pill shadow-lg">
                                <i class="fas fa-paper-plane me-2"></i> Kirim Kehadiran
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    window.showSuccessMessage = <?php echo json_encode(session('success') ? true : false, 15, 512) ?>;
    window.redirectRoute = <?php echo json_encode(route('meeting.index'), 15, 512) ?>;
</script>
    <script src="<?php echo e(asset('js/pages/meeting-presensi.js')); ?>"></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/meeting/presensi.blade.php ENDPATH**/ ?>