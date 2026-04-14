<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Harmoni | BPS Kabupaten Tuban</title>
    
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="<?php echo e(asset('img/logo_harmoni.png')); ?>">

    
    <link rel="stylesheet" href="<?php echo e(asset('css/layouts/app-layout.css')); ?>">
    
    
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    
    <link rel="stylesheet" href="https://unpkg.com/swup@4/dist/swup.css">
</head>
<body>

<div class="progress-bar-container">
    <div class="progress-bar-fill" id="progress-bar"></div>
</div>

<div class="sidebar shadow-sm" id="sidebar">
    <div class="logo-container">
        <a href="<?php echo e(route('dashboard')); ?>">
            <img src="<?php echo e(asset('img/logo_harmoni.png')); ?>" alt="Logo Harmoni" class="sidebar-logo-img">
        </a>
        <h6 class="logo-text mb-0 text-primary">Harmoni <span class="text-dark" style="font-weight: 400;">BPS</span></h6>
        <div class="small text-muted fw-bold" style="font-size: 0.55rem; letter-spacing: 1.2px;">KABUPATEN TUBAN</div>
    </div>
    
    <nav class="mt-2 text-dark">
        
        <?php if(Auth::user()->role != 'Admin'): ?>
            <div class="menu-divider mt-0">Menu Utama</div>
            <a href="<?php echo e(route('dashboard')); ?>" class="nav-link">
                <i class="fas fa-th-large me-2"></i> <span>Dashboard</span>
            </a>
        <?php endif; ?>

        
        <?php if(Auth::user()->role == 'Kepala' || Auth::user()->role == 'Katim'): ?>
            <div class="menu-divider">Perencanaan</div>
            <a href="<?php echo e(route('assignment.index')); ?>" class="nav-link">
                <i class="fas fa-clipboard-list me-2"></i> <span>Assignment</span>
            </a>

            <a href="<?php echo e(route('assignment.approvals.index')); ?>" class="nav-link">
                <i class="fas fa-file-signature me-2"></i> 
                <span>Persetujuan SPT</span>
                
                <?php
                    $notifApproval = \App\Models\Agenda::where('mode_surat', 'generate')
                        ->where('status_approval', 'Pending')
                        ->where(function($q) {
                            $q->where('approver_id', Auth::id())
                            ->orWhere('reviewer_id', Auth::id());
                        });

                    if (Auth::user()->role === 'Kepala') {
                        $notifApproval->whereNotNull('reviewed_at');
                    } elseif (Auth::user()->role === 'Katim') {
                        $notifApproval->whereNull('reviewed_at');
                    }

                    $countNotif = $notifApproval->distinct('title')->count('title');
                ?>

                <?php if($countNotif > 0): ?>
                    <span class="badge bg-danger rounded-pill badge-notif"><?php echo e($countNotif); ?></span>
                <?php endif; ?>
            </a>
        <?php endif; ?> 

        
        <?php if(Auth::user()->role == 'Admin' || Auth::user()->role == 'Kepala'): ?>
            <div class="menu-divider">Pengaturan</div>
            <a href="<?php echo e(route('manajemen.anggota')); ?>" class="nav-link">
                <i class="fas fa-users-cog me-2"></i> <span>Manajemen User</span>
            </a>
        <?php endif; ?>

        
        <?php if(Auth::user()->role != 'Admin'): ?>
            <div class="menu-divider">Monitoring</div>
            <a href="<?php echo e(route('monitoring.index')); ?>" class="nav-link">
                <i class="fas fa-calendar-check me-2"></i> <span>Timeline Agenda</span>
            </a>
        <?php endif; ?>

        
        <?php if(Auth::user()->team && Auth::user()->team->nama_tim === 'Subbagian Umum'): ?>
            <div class="menu-divider">Administrasi</div>
            <a href="<?php echo e(route('absensi.index')); ?>" class="nav-link">
                <i class="fas fa-user-check me-2"></i> <span>Gatekeeper Absensi</span>
            </a>
        <?php endif; ?>

        
        <?php if(Auth::user()->role == 'Pegawai'): ?>
            <div class="menu-divider">Pelaksanaan</div>
            
            
            <button class="nav-link collapsed" 
                    data-bs-toggle="collapse" data-bs-target="#menuLapangan">
                <i class="fas fa-briefcase me-2"></i> 
                <span>Tugas Lapangan</span>
                <i class="fas fa-chevron-down arrow"></i>
            </button>
            <div class="collapse" id="menuLapangan">
                <ul class="submenu">
                    <li>
                        <a href="<?php echo e(route('task.index')); ?>" class="nav-link small">
                            <i class="fas fa-tasks me-2"></i> 
                            <span>Daftar Tugas</span>
                            <?php if(isset($notifLapangan) && $notifLapangan > 0): ?>
                                <span class="badge bg-danger rounded-pill badge-notif"><?php echo e($notifLapangan); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('history.index')); ?>" class="nav-link small">
                            <i class="fas fa-history me-2"></i> Riwayat Laporan
                        </a>
                    </li>
                </ul>
            </div>

            
            <button class="nav-link collapsed" 
                    data-bs-toggle="collapse" data-bs-target="#menuRapat">
                <i class="fas fa-handshake me-2"></i> 
                <span>Kegiatan Dinas</span>
                <i class="fas fa-chevron-down arrow"></i>
            </button>
            <div class="collapse" id="menuRapat">
                <ul class="submenu">
                    <li>
                        <a href="<?php echo e(route('meeting.index')); ?>" class="nav-link small">
                            <i class="fas fa-calendar-day me-2"></i> 
                            <span>Jadwal Kegiatan</span>
                            <?php if(isset($notifKegiatan) && $notifKegiatan > 0): ?>
                                <span class="badge bg-danger rounded-pill badge-notif"><?php echo e($notifKegiatan); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('meeting.history')); ?>" class="nav-link small">
                            <i class="fas fa-file-archive me-2"></i> Riwayat & Notulensi
                        </a>
                    </li>
                </ul>
            </div>
        <?php endif; ?>

            
            <?php if(Auth::user()->role == 'Kepala' || Auth::user()->role == 'Katim' || Auth::user()->has_super_access == 1): ?>
                <a href="<?php echo e(route('super.access.index')); ?>" class="nav-link">
                    <i class="fas fa-shield-alt me-2 text-danger"></i>
                    <span>Akses Super</span>
                </a>
            <?php endif; ?>

        
        <div class="menu-divider">Sistem</div>
        <a href="<?php echo e(route('panduan.index')); ?>" class="nav-link">
            <i class="fas fa-book me-2"></i> <span>Panduan Pengguna</span>
        </a>

        <a href="#" class="nav-link text-danger mt-3" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fas fa-power-off me-2"></i> <span>Keluar</span>
        </a>
    </nav>
</div>

<div class="main-content">
    <nav class="top-navbar d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <button class="btn btn-outline-secondary d-lg-none me-3 shadow-sm" id="btn-toggle">
                <i class="fas fa-bars"></i>
            </button>
            <div class="d-none d-md-block text-dark">
                <h6 class="fw-bold mb-0">Harmoni BPS Tuban</h6>
                <small class="text-muted" style="font-size: 0.7rem;">Sistem Manajemen Agenda & Rapat</small>
            </div>
        </div>
        
        <div class="d-flex align-items-center">
            
            <div class="dropdown me-3">
                <a href="#" class="nav-link position-relative notification-bell" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-bell text-muted fs-5"></i>
                    <?php $unreadCount = Auth::user()->unreadNotifications->count(); ?>
                    <?php if($unreadCount > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.55rem; padding: 0.25em 0.5em;">
                            <?php echo e($unreadCount > 9 ? '9+' : $unreadCount); ?>

                        </span>
                    <?php endif; ?>
                </a>
                <div class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-4 mt-2 p-0 overflow-hidden" aria-labelledby="notificationDropdown" style="width: 320px; max-height: 400px;">
                    <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold">Notifikasi</h6>
                        <a href="<?php echo e(route('notifications.readAll')); ?>" class="small text-decoration-none">Tandai sudah baca</a>
                    </div>
                    <div class="notification-list overflow-auto" style="max-height: 300px;">
                        <?php $__empty_1 = true; $__currentLoopData = Auth::user()->notifications()->take(10)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <a href="<?php echo e(route('notifications.markAsRead', $notification->id)); ?>" class="dropdown-item p-3 border-bottom d-flex align-items-start <?php echo e($notification->read_at ? 'opacity-75' : 'bg-light-primary'); ?>">
                                <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3">
                                    <i class="fas fa-clipboard-list text-primary"></i>
                                </div>
                                <div class="flex-grow-1 overflow-hidden">
                                    <div class="small fw-bold text-dark text-truncate"><?php echo e($notification->data['title'] ?? 'Penugasan Baru'); ?></div>
                                    <div class="small text-muted text-wrap" style="font-size: 0.75rem;"><?php echo e($notification->data['message'] ?? ''); ?></div>
                                    <div class="small text-muted mt-1" style="font-size: 0.65rem;"><?php echo e($notification->created_at->diffForHumans()); ?></div>
                                </div>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="p-4 text-center text-muted">
                                <i class="fas fa-bell-slash d-block mb-2 fs-4"></i>
                                <span class="small">Belum ada notifikasi</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <a href="<?php echo e(route('notifications.index')); ?>" class="dropdown-item py-2 bg-light text-center small text-primary fw-bold">Lihat Semua Notifikasi</a>
                </div>
            </div>

            
            <div class="dropdown">
            <div class="user-profile-badge shadow-sm" data-bs-toggle="dropdown">
                <div class="avatar-box shadow-sm">
                    <?php echo e(strtoupper(substr(Auth::user()->nama_lengkap ?? 'U', 0, 1))); ?>

                </div>
                <div class="text-start d-none d-sm-block text-dark pe-2">
                    <div class="fw-bold lh-1 mb-1" style="font-size: 0.8rem;"><?php echo e(Auth::user()->nama_lengkap); ?></div>
                    <div class="badge <?php echo e(Auth::user()->role == 'Pegawai' ? 'bg-secondary' : (Auth::user()->role == 'Katim' ? 'bg-info' : (Auth::user()->role == 'Kepala' ? 'bg-dark' : 'bg-primary'))); ?>" style="font-size: 0.55rem;">
                        <?php echo e(Auth::user()->role); ?>

                    </div>
                </div>
                <i class="fas fa-chevron-down text-muted" style="font-size: 0.7rem;"></i>
            </div>
            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-4 mt-2 p-2">
                <li class="px-3 py-2 small border-bottom mb-1">
                    <span class="text-muted d-block" style="font-size: 0.6rem;">Username:</span>
                    <span class="fw-bold text-primary">@ <?php echo e(Auth::user()->username); ?></span>
                </li>
                <li>
                    <a class="dropdown-item py-2 rounded-3" href="<?php echo e(route('profile.edit')); ?>">
                        <i class="fas fa-user-cog me-2 text-primary"></i>Pengaturan Profil
                    </a>
                </li>
                <li><hr class="dropdown-divider opacity-50"></li>
                <li>
                    <a class="dropdown-item py-2 rounded-3 text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-power-off me-2"></i>Keluar
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" class="d-none"><?php echo csrf_field(); ?></form>

    <div class="content-padding">
        <div id="swup" class="transition-fade">
            <?php echo $__env->yieldContent('content'); ?>
        </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://unpkg.com/swup@4/dist/Swup.umd.js"></script>
    <script src="<?php echo e(asset('js/layouts/app-layout.js')); ?>"></script>
    <script>
        window.flashMessages = {
            success: "<?php echo e(session('success')); ?>",
            error: "<?php echo e(session('error')); ?>"
        };
    </script>
<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html><?php /**PATH /var/www/resources/views/layouts/app.blade.php ENDPATH**/ ?>