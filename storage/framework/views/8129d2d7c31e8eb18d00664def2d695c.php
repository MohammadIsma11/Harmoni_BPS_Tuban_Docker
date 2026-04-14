<?php
    \Carbon\Carbon::setLocale('id');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="<?php echo e(public_path('css/pages/history-excel.css')); ?>">
</head>
<body>
    <table>
        <tr>
            <th colspan="12" class="title">BADAN PUSAT STATISTIK KABUPATEN TUBAN</th>
        </tr>
        <tr>
            <th colspan="12" class="title" style="font-size: 12pt;">LAPORAN KEGIATAN PENGAWASAN LAPANGAN</th>
        </tr>
        <tr>
            <th colspan="12" class="meta">Periode: <?php echo e(\Carbon\Carbon::now()->translatedFormat('F Y')); ?></th>
        </tr>
        <tr></tr>

        <thead>
            <tr>
                <th class="table-header">No</th>
                <th class="table-header">Nama Pegawai</th>
                <th class="table-header">Nomor Surat Tugas</th>
                <th class="table-header">Jenis Kegiatan</th>
                <th class="table-header">Tujuan & Lokasi</th>
                <th class="table-header">Tanggal</th>
                <th class="table-header">Hari</th>
                <th class="table-header">Aktivitas</th>
                <th class="table-header">Permasalahan</th>
                <th class="table-header">Responden</th>
                <th class="table-header">Solusi</th>
                <th class="table-header">Input Sistem</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $riwayat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr class="<?php echo e($key % 2 == 0 ? '' : 'bg-odd'); ?>">
                
                <td class="text-center"><?php echo e($key + 1); ?></td>
                <td class="cell-data"><?php echo e($l->assignee->nama_lengkap); ?></td>
                <td class="cell-data"><?php echo e($l->nomor_surat_tugas); ?></td>
                <td class="text-center"><?php echo e($l->activityType->name ?? '-'); ?></td>
                <td class="cell-data"><?php echo e($l->title); ?> - <?php echo e($l->location); ?></td>
                <td class="text-center"><?php echo e(\Carbon\Carbon::parse($l->event_date)->format('d/m/Y')); ?></td>
                <td class="text-center"><?php echo e(\Carbon\Carbon::parse($l->event_date)->translatedFormat('l')); ?></td>
                <td class="cell-data"><?php echo e($l->aktivitas); ?></td>
                <td class="cell-data"><?php echo e($l->permasalahan); ?></td>
                <td class="cell-data"><?php echo e($l->responden); ?></td>
                <td class="cell-data"><?php echo e($l->solusi_antisipasi); ?></td>
                <td class="text-center"><?php echo e(\Carbon\Carbon::parse($l->updated_at)->format('d/m/Y')); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</body>
</html><?php /**PATH /var/www/resources/views/history/excel_rekap.blade.php ENDPATH**/ ?>