<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex align-items-center">
                        <a href="<?php echo e(route('manajemen.anggota')); ?>" class="btn btn-light btn-sm rounded-circle me-3">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <div>
                            <h5 class="fw-bold mb-0 text-dark">Tambah Anggota Baru</h5>
                            <small class="text-muted">Sistem Manajemen Pegawai BPS Tuban</small>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4 pt-2">
                    <form action="<?php echo e(route('manajemen.anggota.store')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label small fw-bold text-muted">NAMA LENGKAP *</label>
                                <input type="text" name="nama_lengkap" class="form-control rounded-3 bg-light border-0 <?php $__errorArgs = ['nama_lengkap'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="Contoh: Budi Santoso, S.E." required value="<?php echo e(old('nama_lengkap')); ?>">
                                <?php $__errorArgs = ['nama_lengkap'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback fw-bold"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-muted">USERNAME *</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0 text-muted">@</span>
                                    <input type="text" name="username" class="form-control rounded-3 bg-light border-0 <?php $__errorArgs = ['username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="username_bps" required value="<?php echo e(old('username')); ?>">
                                </div>
                                <?php $__errorArgs = ['username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-danger small mt-1 fw-bold"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-muted">PASSWORD *</label>
                                <input type="password" name="password" class="form-control rounded-3 bg-light border-0 <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="Minimal 6 karakter" required>
                                <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback fw-bold"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-muted">ROLE / JABATAN *</label>
                                <select name="role" class="form-select rounded-3 bg-light border-0 <?php $__errorArgs = ['role'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                    <option value="" selected disabled>Pilih Role</option>
                                    <option value="Admin" <?php echo e(old('role') == 'Admin' ? 'selected' : ''); ?>>Admin (Tim IT)</option>
                                    <option value="Kepala" <?php echo e(old('role') == 'Kepala' ? 'selected' : ''); ?>>Kepala</option>
                                    <option value="Katim" <?php echo e(old('role') == 'Katim' ? 'selected' : ''); ?>>Ketua Tim (Katim)</option>
                                    <option value="Pegawai" <?php echo e(old('role') == 'Pegawai' ? 'selected' : ''); ?>>Anggota / Pegawai</option>
                                </select>
                                <?php $__errorArgs = ['role'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback fw-bold"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label small fw-bold text-muted">PENEMPATAN TIM</label>
                                <select name="team_id" class="form-select rounded-3 bg-light border-0">
                                    <option value="">-- Tanpa Tim --</option>
                                    <?php $__currentLoopData = $teams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $team): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($team->id); ?>" <?php echo e(old('team_id') == $team->id ? 'selected' : ''); ?>>
                                            <?php echo e($team->nama_tim); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <small class="text-muted" style="font-size: 0.65rem;">Opsional untuk Admin/Kepala</small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 border-top pt-4">
                            <a href="<?php echo e(route('manajemen.anggota')); ?>" class="btn btn-light rounded-pill px-4 fw-bold text-muted">Batal</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">
                                <i class="fas fa-save me-2"></i>Simpan Anggota
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/anggota/create.blade.php ENDPATH**/ ?>