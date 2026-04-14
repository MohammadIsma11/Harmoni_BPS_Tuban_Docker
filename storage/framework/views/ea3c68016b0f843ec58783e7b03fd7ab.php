<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4 pb-5">
    
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold text-dark mb-1">Kegiatan & Pertemuan</h4>
            <p class="text-muted small mb-0">Daftar agenda rapat dinas dan penugasan dinas luar Anda.</p>
        </div>
        
        
        <div class="d-flex gap-2">
            <div class="btn-group p-1 bg-white rounded-4 shadow-sm border border-primary border-opacity-10">
                <a href="<?php echo e(route('meeting.index')); ?>" 
                   class="btn btn-sm rounded-3 px-3 <?php echo e(!request('type') ? 'btn-primary shadow-sm' : 'btn-light text-muted'); ?>">
                   Semua
                </a>
                <a href="<?php echo e(route('meeting.index', ['type' => 2])); ?>" 
                   class="btn btn-sm rounded-3 px-3 <?php echo e(request('type') == 2 ? 'btn-primary shadow-sm' : 'btn-light text-muted'); ?>">
                   Rapat
                </a>
                <a href="<?php echo e(route('meeting.index', ['type' => 3])); ?>" 
                   class="btn btn-sm rounded-3 px-3 <?php echo e(request('type') == 3 ? 'btn-primary shadow-sm' : 'btn-light text-muted'); ?>">
                   Dinas Luar
                </a>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr class="text-muted small text-uppercase">
                        <th class="border-0 py-3 ps-4" style="width: 150px;">Tanggal</th>
                        <th class="border-0 py-3">Nama Kegiatan / Agenda</th>
                        <th class="border-0 py-3">Penyelenggara</th>
                        <th class="border-0 py-3 text-center" style="width: 160px;">Status Anda</th>
                        <th class="border-0 py-3 text-center" style="width: 200px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $meetings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $isOverdue = \Carbon\Carbon::parse($m->event_date)->isPast() && !$m->event_date->isToday();
                            $sudahTTD = \App\Models\MeetingPresence::where('agenda_id', $m->id)
                                        ->where('user_id', Auth::id())
                                        ->exists();
                        ?>
                        <tr class="transition-row">
                            <td class="ps-4">
                                <div class="fw-bold text-dark mb-0"><?php echo e(\Carbon\Carbon::parse($m->event_date)->translatedFormat('d M Y')); ?></div>
                                <small class="text-muted"><i class="far fa-clock me-1"></i><?php echo e($m->start_time ?? '--:--'); ?> WIB</small>
                            </td>
                            <td>
                                <div class="fw-bold text-primary mb-1"><?php echo e($m->title); ?></div>
                                
                                <?php if($m->activity_type_id == 2): ?>
                                    <span class="badge bg-blue-soft border-blue-soft">
                                        <i class="fas fa-handshake me-1"></i> RAPAT DINAS
                                    </span>
                                <?php elseif($m->activity_type_id == 3): ?>
                                    <span class="badge bg-green-soft border-green-soft">
                                        <i class="fas fa-route me-1"></i> DINAS LUAR
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-info bg-opacity-10 text-info rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; font-size: 0.7rem; font-weight: 800;">
                                        <?php echo e(strtoupper(substr($m->creator->nama_lengkap ?? 'A', 0, 1))); ?>

                                    </div>
                                    <span class="small fw-semibold text-muted"><?php echo e($m->creator->nama_lengkap ?? 'Admin'); ?></span>
                                </div>
                            </td>
                            <td class="text-center">
                                <?php if($sudahTTD): ?>
                                    <span class="badge bg-success-subtle text-success rounded-pill border border-success border-opacity-25 status-badge">
                                        <i class="fas fa-check-circle me-1"></i> Hadir
                                    </span>
                                <?php elseif($isOverdue): ?>
                                    <span class="badge bg-danger-subtle text-danger rounded-pill border border-danger border-opacity-25 status-badge">
                                        <i class="fas fa-times-circle me-1"></i> Terlewat
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill border border-warning border-opacity-25 status-badge">
                                        <i class="fas fa-clock me-1"></i> Belum Absen
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center pe-4">
                                <div class="d-flex justify-content-center align-items-center gap-2">
                                    
                                    
                                    <?php if(!$sudahTTD && (!$isOverdue || $m->event_date->isToday())): ?>
                                        <?php if($m->activity_type_id == 3): ?>
                                            
                                            <a href="<?php echo e(route('meeting.dinas.create', $m->id)); ?>" class="btn btn-success btn-custom-action fw-bold shadow-sm">
                                                <i class="fas fa-file-export me-1"></i> Lapor
                                            </a>
                                        <?php else: ?>
                                            
                                            <a href="<?php echo e(route('meeting.presensi', $m->id)); ?>" class="btn btn-primary btn-custom-action fw-bold shadow-sm">
                                                <i class="fas fa-signature me-1"></i> Absen
                                            </a>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    
                                    <?php if($m->activity_type_id == 2 && $m->notulis_id == Auth::id()): ?>
                                        <a href="<?php echo e(route('meeting.notulensi', $m->id)); ?>" class="btn btn-dark btn-sm btn-custom-action fw-bold">
                                            <i class="fas fa-pen-nib me-1"></i> Notulis
                                        </a>
                                    <?php endif; ?>

                                    
                                    <?php if($m->user_id == Auth::id() || in_array(Auth::user()->role, ['Admin', 'Katim', 'Kepala'])): ?>
                                        <a href="<?php echo e(route('meeting.monitoring', $m->id)); ?>" class="btn btn-light btn-sm btn-custom-action border shadow-xs">
                                            <i class="fas fa-desktop me-1 text-muted"></i> Pantau
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="opacity-50 mb-3">
                                    <i class="fas fa-calendar-times fa-4x text-muted"></i>
                                </div>
                                <h6 class="fw-bold text-muted">Data Tidak Ditemukan</h6>
                                <p class="text-muted small">Mungkin sedang tidak ada jadwal yang sesuai filter ini.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<link rel="stylesheet" href="<?php echo e(asset('css/pages/meeting-index.css')); ?>">
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/meeting/index.blade.php ENDPATH**/ ?>