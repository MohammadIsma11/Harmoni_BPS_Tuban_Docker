<?php $__env->startSection('content'); ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<link rel="stylesheet" href="<?php echo e(asset('css/pages/assignment-index.css')); ?>">

<div class="container-fluid px-4 pb-5">
    
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3 mt-4 animate-up">
        <div>
            <h4 class="fw-bold text-dark mb-1">Manajemen Penugasan</h4>
            <p class="text-muted small mb-0">Monitor rangkaian kegiatan dan distribusi petugas seluruh tim.</p>
        </div>
        <a href="<?php echo e(route('assignment.create')); ?>" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm py-2">
            <i class="fas fa-plus me-2"></i> Buat Penugasan
        </a>
    </div>

    
    <div class="card border-0 shadow-sm table-container animate-up delay-1">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Nama & Jenis Kegiatan</th>
                        <th>Petugas Terlibat</th>
                        <th class="text-center">Jadwal & Keterangan</th>
                        <th class="text-center" width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $assignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $first = $group->first();
                            $totalPetugas = $group->count();
                            $namaPetugas = $group->pluck('assignee.nama_lengkap')->implode(', ');
                            
                            $isLapangan = $first->activity_type_id == 1;
                            $eventDate = \Carbon\Carbon::parse($first->event_date);
                            $endDate = \Carbon\Carbon::parse($first->end_date);

                            // Mapping Icon dan Teks Keterangan
                            $typeData = match($first->activity_type_id) {
                                1 => ['icon' => 'fa-map-location-dot', 'color' => '#10b981', 'bg' => '#ecfdf5', 'label' => 'Tugas Lapangan'],
                                2 => ['icon' => 'fa-users-rectangle', 'color' => '#3b82f6', 'bg' => '#eff6ff', 'label' => 'Rapat'],
                                3 => ['icon' => 'fa-plane-departure', 'color' => '#f59e0b', 'bg' => '#fffbeb', 'label' => 'Dinas Luar'],
                                default => ['icon' => 'fa-tag', 'color' => '#64748b', 'bg' => '#f8fafc', 'label' => 'Kegiatan Umum'],
                            };
                        ?>
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="activity-icon-box me-3 shadow-sm" style="background-color: <?php echo e($typeData['bg']); ?>; color: <?php echo e($typeData['color']); ?>;">
                                        <i class="fas <?php echo e($typeData['icon']); ?>"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark mb-0" style="font-size: 0.95rem;"><?php echo e($first->title); ?></div>
                                        
                                        <small class="fw-bold text-uppercase" style="font-size: 0.65rem; color: <?php echo e($typeData['color']); ?>; letter-spacing: 0.5px;">
                                            <?php echo e($typeData['label']); ?>

                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="petugas-stack me-3">
                                        <?php $__currentLoopData = $group->take(4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assignee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="avatar-stack-item" title="<?php echo e($assignee->assignee->nama_lengkap); ?>">
                                                <?php echo e(strtoupper(substr($assignee->assignee->nama_lengkap ?? 'U', 0, 1))); ?>

                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php if($totalPetugas > 4): ?>
                                            <div class="avatar-stack-item bg-light text-muted" style="font-size: 0.6rem;">
                                                +<?php echo e($totalPetugas - 4); ?>

                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="lh-1">
                                        <span class="small fw-bold d-block text-dark"><?php echo e($totalPetugas); ?> Orang</span>
                                        <small class="text-muted text-truncate d-inline-block" style="max-width: 140px;" title="<?php echo e($namaPetugas); ?>">
                                            <?php echo e($namaPetugas); ?>

                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="badge-jadwal bg-light border text-dark shadow-sm">
                                    <i class="far fa-clock me-2 text-primary"></i>
                                    <?php if($isLapangan): ?>
                                        <?php echo e($eventDate->translatedFormat('d M')); ?> - <?php echo e($endDate->translatedFormat('d M Y')); ?>

                                    <?php else: ?>
                                        <?php echo e($eventDate->translatedFormat('d F Y')); ?>

                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="<?php echo e(route('assignment.edit', $first->id)); ?>" class="btn-action btn-edit shadow-sm" title="Edit Rangkaian">
                                        <i class="fas fa-pen-to-square"></i>
                                    </a>

                                    <form action="<?php echo e(route('assignment.destroy', $first->id)); ?>" method="POST" id="delete-form-<?php echo e($first->id); ?>">
                                        <?php echo csrf_field(); ?> 
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="button" class="btn-action btn-delete btn-confirm-delete shadow-sm" 
                                                data-id="<?php echo e($first->id); ?>" 
                                                data-title="<?php echo e($first->title); ?>">
                                            <i class="fas fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <img src="https://illustrations.popsy.co/blue/waiting.svg" style="width: 180px;" class="mb-3">
                                <h6 class="text-muted fw-bold">Belum ada daftar penugasan.</h6>
                                <p class="small text-muted">Klik tombol "Buat Penugasan" untuk memulai.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="<?php echo e(asset('js/pages/assignment-index.js')); ?>"></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/assignment/index.blade.php ENDPATH**/ ?>