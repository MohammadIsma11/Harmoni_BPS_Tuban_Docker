<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4 pb-5">
    <?php 
        $isEdit = !empty($meeting->notulensi_hasil); 
    ?>

    
    <div class="d-flex align-items-center mb-4 mt-4">
        <div class="bg-warning bg-opacity-10 p-3 rounded-4 me-3 text-warning">
            <i class="fas <?php echo e($isEdit ? 'fa-edit' : 'fa-file-signature'); ?> fa-lg"></i>
        </div>
        <div>
            <h4 class="fw-bold mb-0"><?php echo e($isEdit ? 'Edit Hasil Rapat' : 'Input Hasil Rapat (Upload File)'); ?></h4>
            <p class="text-muted small mb-0">Unggah dokumen notulensi, materi pendukung, dan foto dokumentasi.</p>
        </div>
    </div>

    <form id="formNotulensi" action="<?php echo e($isEdit ? route('meeting.notulensi.update', $meeting->id) : route('meeting.notulensi.store', $meeting->id)); ?>" 
          method="POST" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <?php if($isEdit): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>

        <div class="row">
            
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                    <div class="card-body p-5 text-center">
                        <div class="mb-4">
                            <i class="fas fa-file-invoice text-primary opacity-25" style="font-size: 5rem;"></i>
                        </div>
                        <h5 class="fw-bold text-dark">Dokumen Notulensi Rapat</h5>
                        <p class="text-muted small px-md-5">Unggah file hasil rapat yang telah disusun. Sistem menerima format PDF, Word, atau Text.</p>
                        
                        <div class="mx-auto" style="max-width: 400px;">
                            <input type="file" name="hasil_rapat_file" id="hasil_rapat_file" 
                                   class="form-control rounded-pill border-primary border-dashed p-3 <?php $__errorArgs = ['hasil_rapat_file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   accept=".pdf,.doc,.docx,.txt" <?php echo e($isEdit ? '' : 'required'); ?>>
                            <?php $__errorArgs = ['hasil_rapat_file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        
                        <?php if($isEdit && $meeting->notulensi_hasil): ?>
                            <div class="mt-4 p-3 bg-light rounded-4 d-inline-flex align-items-center border">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <span class="small fw-bold text-muted">File Tersimpan: </span>
                                <a href="<?php echo e(asset('storage/' . $meeting->notulensi_hasil)); ?>" target="_blank" class="ms-2 badge bg-primary text-decoration-none">
                                    <i class="fas fa-external-link-alt me-1"></i> Lihat Dokumen
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <div class="mt-3">
                            <small class="text-muted fw-bold">Batas ukuran file: 20MB</small>
                        </div>
                    </div>
                </div>

                
                <div class="card border-0 shadow-sm rounded-4 mb-4 border-start border-4 border-success">
                    <div class="card-body p-4 text-dark">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0 small text-uppercase" style="letter-spacing: 1px;">Status Kehadiran Peserta</h6>
                            <span class="badge bg-success rounded-pill px-3"><?php echo e(count($userSudahHadir)); ?> Hadir</span>
                        </div>
                        <div class="row g-2">
                            <?php $__currentLoopData = $semuaPeserta; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $isHadir = in_array($p->assigned_to, $userSudahHadir); ?>
                                <div class="col-md-4">
                                    <div class="p-2 border rounded-3 d-flex align-items-center <?php echo e($isHadir ? 'bg-white' : 'bg-light opacity-50'); ?>">
                                        <i class="fas <?php echo e($isHadir ? 'fa-check-circle text-success' : 'fa-clock text-muted'); ?> me-2"></i>
                                        <span class="small text-truncate fw-bold" title="<?php echo e($p->assignee->nama_lengkap); ?>"><?php echo e($p->assignee->nama_lengkap); ?></span>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="col-lg-4">
                
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4 text-dark">
                        <h6 class="fw-bold mb-3"><i class="fas fa-file-powerpoint me-2 text-warning"></i>Materi Rapat</h6>
                        <input type="file" name="materi_path" id="materi_path" class="form-control rounded-3" accept=".pdf,.pptx,.ppt">
                        
                        
                        <?php if($isEdit && $meeting->materi_path): ?>
                            <div class="mt-2 p-2 bg-light rounded-3 border">
                                <small class="d-block text-muted mb-1" style="font-size: 0.7rem;">Materi Terunggah:</small>
                                <a href="<?php echo e(asset('storage/' . $meeting->materi_path)); ?>" target="_blank" class="text-decoration-none small fw-bold">
                                    <i class="fas fa-file-download me-1"></i> Download Materi
                                </a>
                            </div>
                        <?php endif; ?>
                        <small class="text-muted d-block mt-1" style="font-size: 0.65rem;">Max: 20MB (PDF/PPTX)</small>
                    </div>
                </div>

                
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4 text-dark">
                        <h6 class="fw-bold mb-3"><i class="fas fa-images me-2 text-danger"></i>Foto Dokumentasi</h6>
                        <input type="file" name="foto_dokumentasi[]" id="foto_dokumentasi" class="form-control rounded-3" accept="image/*" multiple <?php echo e($isEdit ? '' : 'required'); ?>>
                        
                        <div id="image-preview-container" class="row g-2 mt-2">
                            
                            <?php if($isEdit && $meeting->dokumentasi_path): ?>
                                <?php $fotos = json_decode($meeting->dokumentasi_path); ?>
                                <?php if(is_array($fotos)): ?>
                                    <?php $__currentLoopData = $fotos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $foto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="col-3 existing-photo">
                                            <div class="preview-img-wrapper border-success border-opacity-25">
                                                <img src="<?php echo e(asset('storage/' . $foto)); ?>" alt="Existing photo">
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                        <small class="text-muted d-block mt-2" style="font-size: 0.65rem;">Maksimal 20MB per foto. <?php if($isEdit): ?> <span class="text-warning">Upload baru akan mengganti foto lama.</span> <?php endif; ?></small>
                    </div>
                </div>

                
                <button type="submit" class="btn btn-primary w-100 rounded-pill py-3 fw-bold shadow-lg border-0 mb-3">
                    <i class="fas <?php echo e($isEdit ? 'fa-save' : 'fa-cloud-upload-alt'); ?> me-2"></i> 
                    <?php echo e($isEdit ? 'Simpan Perubahan' : 'Upload Notulensi'); ?>

                </button>

                
                <a href="<?php echo e($isEdit ? route('meeting.history') : route('meeting.index')); ?>" 
                class="btn btn-light w-100 rounded-pill py-2 text-muted fw-bold border">
                    <i class="fas fa-times me-1"></i> Batal
                </a>
            </div>
        </div>
    </form>
</div>

<link rel="stylesheet" href="<?php echo e(asset('css/pages/meeting-notulensi.css')); ?>">


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?php echo e(asset('js/pages/meeting-notulensi.js')); ?>"></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/meeting/notulensi.blade.php ENDPATH**/ ?>