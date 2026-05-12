<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Analisis Cerdas Beban Kerja (K-Means Clustering)</h1>
        <div class="d-none d-sm-inline-block">
            <span class="badge badge-primary p-2 shadow-sm"><i class="fas fa-brain mr-1"></i> Sistem AI Aktif</span>
        </div>
    </div>

    <div class="row">
        <!-- Chart Analisis -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Distribusi Beban Kerja</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="workloadChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2"><i class="fas fa-circle text-danger"></i> Tinggi</span>
                        <span class="mr-2"><i class="fas fa-circle text-success"></i> Ideal</span>
                        <span class="mr-2"><i class="fas fa-circle text-warning"></i> Rendah</span>
                    </div>
                </div>
            </div>

            <!-- Informasi Pembobotan Card -->
            <div class="card shadow mb-4 border-left-info">
                <div class="card-header py-3 bg-light">
                    <h6 class="m-0 font-weight-bold text-info"><i class="fas fa-info-circle mr-2"></i>Ketentuan Poin Beban Kerja</h6>
                </div>
                <div class="card-body py-2">
                    <div class="mb-2 d-flex justify-content-between align-items-center">
                        <span class="text-dark"><i class="fas fa-map-marker-alt text-danger mr-2"></i> Tugas Lapangan</span>
                        <span class="font-weight-bold text-danger">5 Poin</span>
                    </div>
                    <div class="mb-2 d-flex justify-content-between align-items-center">
                        <span class="text-dark"><i class="fas fa-plane-departure text-primary mr-2"></i> Dinas Luar</span>
                        <span class="font-weight-bold text-primary">3 Poin</span>
                    </div>
                    <div class="mb-0 d-flex justify-content-between align-items-center">
                        <span class="text-dark"><i class="fas fa-users text-success mr-2"></i> Rapat Dinas / Kantor</span>
                        <span class="font-weight-bold text-success">1 Poin</span>
                    </div>
                    <hr class="my-2">
                    <small class="text-muted italic">* Poin diakumulasi otomatis oleh AI setiap bulan berjalan.</small>
                </div>
            </div>
        </div>

        <!-- Insight & Rekomendasi -->
        <div class="col-xl-8 col-lg-7">
            <!-- Smart Insight Card -->
            <div class="card shadow mb-4 border-left-primary bg-light">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">SMART INSIGHT ANALYSIS</div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                Berdasarkan analisis sistem, terdapat <span class="text-danger"><?php echo e($totalOverload); ?></span> pegawai yang mengalami overload beban kerja. Disarankan untuk melakukan redistribusi tugas kepada pegawai dengan beban rendah.
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-lightbulb fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recommendation Table -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Rekomendasi Penugasan (Beban Rendah)</h6>
                    <span class="badge badge-success px-2"><?php echo e(count(array_filter($pegawaiResult, fn($p) => $p['cluster'] == 'Beban Rendah'))); ?> Tersedia</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 385px; overflow-y: auto;">
                        <table class="table table-hover mb-0" width="100%" cellspacing="0">
                            <thead class="bg-light text-dark sticky-top" style="z-index: 1;">
                                <tr>
                                    <th class="ps-3 border-0">Nama Pegawai</th>
                                    <th class="border-0">Poin</th>
                                    <th class="border-0">Ketersediaan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $hasLow = false; ?>
                                <?php $__currentLoopData = $pegawaiResult; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($p['cluster'] == 'Beban Rendah'): ?>
                                        <?php $hasLow = true; ?>
                                        <tr>
                                            <td class="ps-3 fw-bold align-middle"><?php echo e($p['nama']); ?></td>
                                            <td class="align-middle">
                                                <span class="badge badge-light text-dark border"><?php echo e(number_format($p['points'], 0)); ?> Poin</span>
                                            </td>
                                            <td class="align-middle text-success small fw-bold">
                                                <i class="fas fa-check-circle me-1"></i> Sangat Tersedia
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                <?php if(!$hasLow): ?>
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted italic">
                                            Semua pegawai sedang sibuk.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white py-2 text-center">
                    <small class="text-muted italic">Gunakan scroll untuk melihat daftar lengkap</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Full Data Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Hasil Lengkap Analisis Per Pegawai (Bulan Ini)</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm mb-0" width="100%" cellspacing="0">
                            <thead class="bg-dark text-white">
                                <tr>
                                    <th class="ps-3 py-2">Nama Pegawai</th>
                                    <th class="text-center py-2">Jumlah Kegiatan</th>
                                    <th class="text-center py-2">Poin Beban (Rincian)</th>
                                    <th class="text-center py-2">Tugas Pending</th>
                                    <th class="text-center py-2">Hasil Cluster AI</th>
                                    <th class="py-2">Analisis Indikator AI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $pegawaiResult; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="ps-3 fw-bold align-middle"><?php echo e($p['nama']); ?></td>
                                    <td class="text-center align-middle"><?php echo e($p['agendas']); ?></td>
                                    <td class="text-center align-middle">
                                        <div class="font-weight-bold"><?php echo e($p['points']); ?></div>
                                    </td>
                                    <td class="text-center align-middle"><?php echo e($p['pending']); ?></td>
                                    <td class="text-center align-middle">
                                        <span class="badge badge-<?php echo e($p['cluster'] == 'Beban Tinggi' ? 'danger' : ($p['cluster'] == 'Beban Ideal' ? 'success' : 'warning')); ?>">
                                            <?php echo e($p['cluster']); ?>

                                        </span>
                                    </td>
                                    <td class="small text-muted italic align-middle"><?php echo e($p['reason']); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    var ctx = document.getElementById("workloadChart");
    var workloadChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ["Tinggi", "Ideal", "Rendah"],
            datasets: [{
                data: [<?php echo e($counts['Beban Tinggi']); ?>, <?php echo e($counts['Beban Ideal']); ?>, <?php echo e($counts['Beban Rendah']); ?>],
                backgroundColor: ['#e74a3b', '#1cc88a', '#f6c23e'],
                hoverBackgroundColor: ['#be2617', '#17a673', '#dda20a'],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        options: {
            maintainAspectRatio: false,
            tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                caretPadding: 10,
            },
            legend: {
                display: false
            },
            cutoutPercentage: 80,
        },
    });
</script>
<?php $__env->stopPush(); ?>

<style>
    .border-left-info { border-left: 0.25rem solid #36b9cc !important; }
    .border-left-primary { border-left: 0.25rem solid #4e73df !important; }
    .italic { font-style: italic; }
    .badge-warning { background-color: #f6c23e; color: #fff; }
    .badge-danger { background-color: #e74a3b; color: #fff; }
    .badge-success { background-color: #1cc88a; color: #fff; }
    .bg-dark { background-color: #343a40 !important; }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/dashboard/kepala.blade.php ENDPATH**/ ?>