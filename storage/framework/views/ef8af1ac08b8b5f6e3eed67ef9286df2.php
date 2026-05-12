<?php $__env->startSection('content'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/pages/anggota-index.css')); ?>">

<div class="container-fluid">
    <div class="card card-members">
        <div class="card-body p-0">
            
            <div class="p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h5 class="fw-bold mb-1 text-dark">Data Anggota</h5>
                    <p class="text-muted small mb-0">Total: <?php echo e($anggota->total()); ?> Personel BPS Tuban</p>
                </div>
                <div class="d-flex gap-2">
                    <form action="<?php echo e(route('manajemen.anggota')); ?>" method="GET" class="d-flex">
                        <input type="text" name="search" class="form-control form-control-sm rounded-pill px-3" 
                               placeholder="Cari..." value="<?php echo e(request('search')); ?>" style="width: 180px;">
                    </form>
                    
                    
                    <?php if(Auth::user()->role == 'Admin'): ?>
                    <a href="<?php echo e(route('manajemen.anggota.create')); ?>" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
                        <i class="fas fa-plus me-1"></i> Tambah Anggota
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            
            <div class="table-responsive">
                <table class="table table-members">
                    <thead>
                        <tr>
                            <th class="ps-4 col-nama">Nama Lengkap</th>
                            <th class="col-user">Username</th>
                            <th class="text-center col-role">Role</th>
                            <th class="text-center col-tim">Tim</th>
                            <th class="text-center col-tgl">Bergabung</th>
                            
                            <?php if(Auth::user()->role == 'Admin'): ?>
                            <th class="text-center pe-4 col-aksi">Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $anggota; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-mini me-2"><?php echo e(substr($a->nama_lengkap, 0, 1)); ?></div>
                                    <div class="text-truncate fw-bold text-dark" style="max-width: 150px;" title="<?php echo e($a->nama_lengkap); ?>">
                                        <?php echo e($a->nama_lengkap); ?>

                                    </div>
                                </div>
                            </td>
                            <td><span class="text-primary small">@ <?php echo e($a->username); ?></span></td>
                            <td class="text-center">
                                <?php if($a->role == 'Admin'): ?>
                                    <span class="role-badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-10">Admin</span>
                                <?php elseif($a->role == 'Kepala'): ?>
                                    <span class="role-badge bg-dark bg-opacity-10 text-dark border border-dark border-opacity-10">Kepala</span>
                                <?php elseif($a->role == 'Katim'): ?>
                                    <span class="role-badge bg-info bg-opacity-10 text-info border border-info border-opacity-10">Katim</span>
                                <?php elseif($a->role == 'Mitra'): ?>
                                    <span class="role-badge bg-success bg-opacity-10 text-success border border-success border-opacity-10">Mitra</span>
                                <?php else: ?>
                                    <span class="role-badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-10">Pegawai</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center small fw-medium text-muted">
                                <?php echo e($a->team->nama_tim ?? '-'); ?>

                            </td>
                            <td class="text-center text-muted small">
                                <?php echo e(\Carbon\Carbon::parse($a->created_at)->format('d/m/y')); ?>

                            </td>
                            
                            
                            <?php if(Auth::user()->role == 'Admin'): ?>
                            <td class="pe-4 text-center">
                                <div class="btn-action-group">
                                    <a href="<?php echo e(route('manajemen.anggota.edit', $a->id)); ?>" class="btn-mini btn-edit" title="Edit">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    <form action="<?php echo e(route('manajemen.anggota.destroy', $a->id)); ?>" method="POST" id="del-<?php echo e($a->id); ?>">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button type="button" onclick="confirmDelete(<?php echo e($a->id); ?>, '<?php echo e($a->nama_lengkap); ?>')" class="btn-mini btn-delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="<?php echo e(Auth::user()->role == 'Admin' ? 6 : 5); ?>" class="text-center py-5 text-muted">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="fas fa-user-slash fa-3x mb-3 opacity-20"></i>
                                    <span>Tidak ada data anggota ditemukan.</span>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            
            <div class="p-3 border-top d-flex justify-content-center">
                <?php echo e($anggota->links()); ?>

            </div>
        </div>
    </div>
</div>

<?php if(Auth::user()->role == 'Admin'): ?>
<script src="<?php echo e(asset('js/pages/anggota-index.js')); ?>"></script>
<?php endif; ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/anggota/index.blade.php ENDPATH**/ ?>