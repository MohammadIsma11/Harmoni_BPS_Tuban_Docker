<?php
    \Carbon\Carbon::setLocale('id');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daftar Hadir - <?php echo e($meeting->title); ?></title>
    <style>
        <?php echo file_get_contents(resource_path('css/pages/pdf-presensi.css')); ?>

    </style>
</head>
<body>
    <div class="header">
        <h3>BADAN PUSAT STATISTIK KABUPATEN TUBAN</h3>
        <h2>DAFTAR HADIR PESERTA <?php echo e($meeting->activity_type_id == 2 ? 'RAPAT' : 'KEGIATAN'); ?></h2>
    </div>

    <table class="info-table">
        <tr>
            <td width="20%">Nama Kegiatan</td>
            <td width="2%">:</td>
            <td width="78%" class="fw-bold"><?php echo e($meeting->title); ?></td>
        </tr>
        <tr>
            <td>Hari / Tanggal</td>
            <td>:</td>
            <td><?php echo e(\Carbon\Carbon::parse($meeting->event_date)->translatedFormat('l, d F Y')); ?></td>
        </tr>
        <tr>
            <td>Pimpinan / Penanggung Jawab</td>
            <td>:</td>
            <td><?php echo e($meeting->pimpinan_rapat ?? ($meeting->creator->nama_lengkap ?? 'Admin')); ?></td>
        </tr>
    </table>

    <table class="daftar-hadir">
        <thead>
            <tr>
                <th width="7%">No</th>
                <th width="40%">Nama Lengkap / NIP</th>
                <th width="23%">Jabatan / Tim</th>
                <th width="30%">Tanda Tangan</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $peserta; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $presensiBarisIni = $dataPresensi->get($p->id);
            ?>
            <tr>
                <td align="center"><?php echo e($index + 1); ?></td>
                <td>
                    <div class="fw-bold" style="font-size: 9pt;"><?php echo e($p->assignee->nama_lengkap); ?></div>
                    <div style="font-size: 8pt; color: #333;">NIP. <?php echo e($p->assignee->nip ?? '-'); ?></div>
                </td>
                <td align="center" style="font-size: 9pt;">
                    <?php echo e($p->assignee->team->nama_tim ?? 'Staf'); ?>

                </td>
                <td align="center" style="height: 60px;">
                    <?php if($presensiBarisIni && !empty($presensiBarisIni->signature_base64)): ?>
                        <?php
                            $data = trim($presensiBarisIni->signature_base64);
                            if (strpos($data, ',') !== false) {
                                $data = explode(',', $data)[1];
                            }
                            $data = str_replace(["\r", "\n", ' '], '', $data);
                            $cleanImage = 'data:image/png;base64,' . $data;
                        ?>
                        <img src="<?php echo $cleanImage; ?>" class="signature-img-row">
                    <?php else: ?>
                        <span style="color: #bbb; font-size: 7pt; font-style: italic;">(Tidak Hadir)</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <table class="footer-table">
        <tr>
            
            <td>
                <p>Mengetahui,</p>
                <p style="margin-bottom: 0;">Kepala BPS Kabupaten Tuban</p>
                
                <div class="signature-space">
                    
                    <?php if($kepala && $kepala->ttd_image): ?>
                        <img src="<?php echo e(public_path('storage/' . $kepala->ttd_image)); ?>" class="signature-pejabat">
                    <?php endif; ?>
                </div>
                
                <span class="underline"><?php echo e($kepala->nama_lengkap ?? 'NAMA KEPALA BPS, M.Si'); ?></span>
                <div style="font-size: 9pt; margin-top: 5px;">
                    NIP. <?php echo e($kepala->nip ?? '19700000 000000 0 000'); ?>

                </div>
            </td>

            
            <td>
                <p>Tuban, <?php echo e(\Carbon\Carbon::parse($meeting->event_date)->translatedFormat('d F Y')); ?></p>
                <p style="margin-bottom: 0;">Pembuat Daftar,</p>
                
                <div class="signature-space">
                    <?php if($meeting->creator && $meeting->creator->ttd_image): ?>
                        <img src="<?php echo e(public_path('storage/' . $meeting->creator->ttd_image)); ?>" class="signature-pejabat">
                    <?php endif; ?>
                </div>
                
                <span class="underline"><?php echo e($meeting->creator->nama_lengkap); ?></span>
                <div style="font-size: 9pt; margin-top: 5px;">
                    NIP. <?php echo e($meeting->creator->nip ?? '-'); ?>

                </div>
            </td>
        </tr>
    </table>
</body>
</html><?php /**PATH /var/www/resources/views/meeting/pdf_presensi.blade.php ENDPATH**/ ?>