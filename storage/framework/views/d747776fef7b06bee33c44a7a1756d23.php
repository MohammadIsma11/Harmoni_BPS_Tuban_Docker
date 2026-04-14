<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="<?php echo e(public_path('css/pages/history-pdf.css')); ?>">
</head>
<body>

    <?php \Carbon\Carbon::setLocale('id'); ?>

    <table class="kop-table">
        <tr>
            <td width="60">
                <img src="<?php echo e(public_path('img/logo-bps.png')); ?>" width="60">
            </td>
            <td class="header-text">
                <h1>BADAN PUSAT STATISTIK</h1>
                <h2>KABUPATEN TUBAN</h2>
                <p>LAPORAN SEMUA KEGIATAN PENGAWASAN LAPANGAN</p>
            </td>
            <td width="60"></td> </tr>
    </table>

    <div class="meta">
        Digenerate: <?php echo e(\Carbon\Carbon::now()->translatedFormat('d F Y')); ?> &bull; Total Laporan: <?php echo e($riwayat->count()); ?>

    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">Pegawai & Tanggal</th>
                <th width="30%">Kegiatan & Lokasi</th>
                <th width="45%">Detail Laporan</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $riwayat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td class="text-center"><?php echo e($key + 1); ?></td>
                <td>
                    <strong><?php echo e($l->assignee->nama_lengkap); ?></strong><br>
                    <span style="font-size: 7.5pt;"><?php echo e(\Carbon\Carbon::parse($l->event_date)->translatedFormat('l, d/m/Y')); ?></span>
                </td>
                <td>
                    <strong><?php echo e($l->title); ?></strong><br>
                    <small>Lokasi: <?php echo e($l->location); ?></small>
                </td>
                <td class="bg-gray">
                    <span class="label">AKTIVITAS:</span>
                    <div style="margin-bottom: 8px;"><?php echo e($l->aktivitas); ?></div>
                    
                    <span class="label">PERMASALAHAN:</span>
                    <div><?php echo e($l->permasalahan); ?></div>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <div class="footer-wrapper">
        <div class="footer-sign">
            <p>Tuban, <?php echo e(\Carbon\Carbon::now()->translatedFormat('d F Y')); ?></p>
            <br><br><br>
            <p><strong>( ________________ )</strong></p>
            <p>Kepala BPS Tuban</p>
        </div>
    </div>

</body>
</html>             <?php /**PATH /var/www/resources/views/history/pdf_rekap.blade.php ENDPATH**/ ?>