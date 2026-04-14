<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4 pb-5">
    
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3 mt-4">
        <div>
            <h4 class="fw-bold mb-0 text-dark">Riwayat & Notulensi</h4>
            <p class="text-muted small mb-0">Manajemen arsip dokumentasi dan hasil rapat yang telah terlaksana.</p>
        </div>
        <div class="bg-primary bg-opacity-10 p-2 px-3 rounded-4 border border-primary border-opacity-10 shadow-sm">
            <i class="fas fa-archive text-primary me-2"></i>
            <span class="fw-bold small text-primary"><?php echo e($historyMeetings->count()); ?> Agenda Tersimpan</span>
        </div>
    </div>

                
                <div class="d-flex justify-content-start mb-3">
                    <div class="btn-group p-1 bg-white rounded-4 shadow-sm border border-primary border-opacity-10">
                        <a href="<?php echo e(route('meeting.history')); ?>" 
                        class="btn btn-sm rounded-3 px-4 <?php echo e(request()->routeIs('meeting.history') ? 'btn-primary shadow-sm' : 'btn-light text-muted'); ?>">
                        <i class="fas fa-handshake me-1"></i> Rapat Dinas
                        </a>
                        <a href="<?php echo e(route('meeting.history.dinas')); ?>" 
                        class="btn btn-sm rounded-3 px-4 <?php echo e(request()->routeIs('meeting.history.dinas') ? 'btn-primary shadow-sm' : 'btn-light text-muted'); ?>">
                        <i class="fas fa-route me-1"></i> Dinas Luar
                        </a>
                    </div>
                </div>

                
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-3">
                        
                        <form action="<?php echo e(request()->routeIs('meeting.history.dinas') ? route('meeting.history.dinas') : route('meeting.history')); ?>" 
                            method="GET" class="row g-2">
                            
                            <div class="col-md-10">
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="fas fa-search text-muted"></i></span>
                                    <input type="text" name="search" class="form-control bg-light border-0" 
                                        placeholder="Cari judul <?php echo e(request()->routeIs('meeting.history.dinas') ? 'dinas luar' : 'rapat'); ?>..." 
                                        value="<?php echo e(request('search')); ?>">
                                </div>
                            </div>
                            
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100 rounded-3 fw-bold shadow-sm">
                                    <i class="fas fa-filter me-1"></i> Cari
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

    
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
    <tr class="text-muted small text-uppercase">
        <th class="border-0 py-3 ps-4" style="width: 15%;">Tanggal</th>
        
        <th class="border-0 py-3" style="width: 35%;">
            <?php echo e(request()->routeIs('meeting.history.dinas') ? 'Agenda Dinas Luar' : 'Agenda Rapat'); ?>

        </th>
        
        <th class="border-0 py-3">
            <?php echo e(request()->routeIs('meeting.history.dinas') ? 'Pelapor' : 'Notulis'); ?>

        </th>
        <th class="border-0 py-3 text-center">Aksi</th>
    </tr>
</thead>
<tbody>
    <?php $__empty_1 = true; $__currentLoopData = $historyMeetings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $meeting): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <tr>
        <td class="ps-4">
            <div class="fw-bold text-dark mb-0"><?php echo e(\Carbon\Carbon::parse($meeting->event_date)->translatedFormat('d M Y')); ?></div>
            <small class="text-muted"><i class="far fa-clock me-1"></i><?php echo e($meeting->start_time ?? '--:--'); ?> WIB</small>
        </td>
        <td>
            <div class="fw-bold text-primary mb-1"><?php echo e($meeting->title); ?></div>
            
            <small class="text-muted">
                <i class="fas fa-map-marker-alt me-1 text-danger"></i> 
                <?php
                    $locH = $meeting->location;
                    if ($meeting->activity_type_id == 2 && empty($locH)) {
                        $locH = 'Ruang Rapat';
                    }
                ?>
                <?php echo e($locH ?? '-'); ?>

            </small>
        </td>
        <td>
            <div class="d-flex align-items-center">
                <div class="avatar-sm-table <?php echo e($meeting->activity_type_id == 3 ? 'bg-success' : 'bg-info'); ?> text-white me-2 shadow-sm">
                    <?php echo e(strtoupper(substr(($meeting->activity_type_id == 3 ? ($meeting->assignee->nama_lengkap ?? 'D') : ($meeting->notulis->nama_lengkap ?? 'N')), 0, 1))); ?>

                </div>
                <div class="small fw-bold text-dark">
                    <?php echo e($meeting->activity_type_id == 3 ? ($meeting->assignee->nama_lengkap ?? '-') : ($meeting->notulis->nama_lengkap ?? '-')); ?>

                </div>
            </div>
        </td>
        <td class="text-center">
            
            <div class="d-flex justify-content-center gap-2">
                <?php if($meeting->activity_type_id == 3): ?>
                    <a href="<?php echo e(route('meeting.history.detail_dinas', $meeting->id)); ?>" class="btn btn-light btn-sm rounded-3 shadow-xs border" title="Lihat Detail Dinas">
                        <i class="fas fa-eye text-success"></i>
                    </a>
                <?php else: ?>
                    <a href="<?php echo e(route('meeting.history.detail', $meeting->id)); ?>" class="btn btn-light btn-sm rounded-3 shadow-xs border" title="Lihat Detail Rapat">
                        <i class="fas fa-eye text-primary"></i>
                    </a>
                <?php endif; ?>

                <?php 
                    $isOwner = ($meeting->activity_type_id == 3) ? ($meeting->assigned_to == Auth::id()) : ($meeting->notulis_id == Auth::id());
                ?>

                <?php if($isOwner || in_array(Auth::user()->role, ['Admin', 'Kepala'])): ?>
                    <?php if($meeting->activity_type_id == 3): ?>
                        <a href="<?php echo e(route('meeting.dinas.create', $meeting->id)); ?>" class="btn btn-light btn-sm rounded-3 shadow-xs border" title="Edit Laporan">
                            <i class="fas fa-edit text-warning"></i>
                        </a>
                    <?php else: ?>
                        <a href="<?php echo e(route('meeting.notulensi', $meeting->id)); ?>" class="btn btn-light btn-sm rounded-3 shadow-xs border" title="Edit Notulensi">
                            <i class="fas fa-edit text-warning"></i>
                        </a>
                    <?php endif; ?>
                    
                    <button type="button" class="btn btn-light btn-sm rounded-3 shadow-xs border btn-delete-history" 
                            data-id="<?php echo e($meeting->id); ?>" 
                            data-title="<?php echo e($meeting->title); ?>"
                            title="Hapus">
                        <i class="fas fa-trash text-danger"></i>
                    </button>

                    <form id="delete-form-<?php echo e($meeting->id); ?>" 
                        action="<?php echo e(route('meeting.history.destroy', $meeting->id)); ?>" 
                        method="POST" class="d-none">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                    </form>
                <?php endif; ?>
            </div>
        </td>
    </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
     <tr>
        <td colspan="4" class="text-center py-5">
            <div class="py-4">
                <i class="fas fa-folder-open fa-3x text-muted opacity-25 mb-3"></i>
                <h6 class="fw-bold text-muted">Data riwayat belum tersedia.</h6>
            </div>
        </td>
    </tr>
    <?php endif; ?>
</tbody>
            </table>
        </div>
    </div>
</div>

<link rel="stylesheet" href="<?php echo e(asset('css/pages/meeting-history.css')); ?>">


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php $__env->startPush('scripts'); ?>
    <script src="<?php echo e(asset('js/pages/meeting-history.js')); ?>"></script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/meeting/history.blade.php ENDPATH**/ ?>