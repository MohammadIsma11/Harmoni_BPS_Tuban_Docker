<!DOCTYPE html>
<html>
<head>
    <title><?php echo e(($agenda->activity_type_id == 1) ? 'SPT' : 'Memorandum'); ?> - <?php echo e($agenda->nomor_surat_tugas); ?></title>
    <style>
        <?php echo file_get_contents(resource_path('css/pages/pdf-spt.css')); ?>

    </style>
</head>
<body>




<?php if($agenda->activity_type_id == 1): ?>
    <?php if($mode == 'perorang'): ?>
        <?php $__currentLoopData = $grupPetugas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="header">
                <img src="<?php echo e(public_path('img/logo-bps.png')); ?>" class="logo-bps">
                <div class="kop-text">
                    <span class="kop-bps">BADAN PUSAT STATISTIK</span><br>
                    <span class="kop-kab">KABUPATEN TUBAN</span>
                </div>
            </div>

            <div class="title-section">
                <span class="title-spt uppercase">SURAT PERINTAH TUGAS</span><br>
                <span class="bold uppercase">NOMOR : <?php echo e($agenda->nomor_surat_tugas); ?></span>
            </div>

            <table class="table-main">
                <tr>
                    <td class="col-label">Menimbang</td>
                    <td class="col-sep">:</td>
                    <td class="col-isi">
                        <?php if($agenda->menimbang): ?>
                            <?php echo nl2br(e($agenda->menimbang)); ?>

                        <?php else: ?>
                            bahwa untuk kelancaran pelaksanaan kegiatan <?php echo e($agenda->title); ?>, maka dipandang perlu untuk menugaskan pegawai yang namanya tersebut di bawah ini;
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td class="col-label">Mengingat</td>
                    <td class="col-sep">:</td>
                    <td class="col-isi">
                        <?php if($agenda->mengingat): ?>
                            <?php echo nl2br(e($agenda->mengingat)); ?>

                        <?php else: ?>
                            <ol class="list-poin">
                                <li>Undang-Undang Nomor 16 Tahun 1997 tentang Statistik;</li>
                                <li>Peraturan Pemerintah Nomor 51 Tahun 1999 tentang Penyelenggaraan Statistik;</li>
                                <li>Peraturan Presiden Nomor 86 Tahun 2007 tentang Badan Pusat Statistik;</li>
                            </ol>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>

            <div style="text-align: center; margin: 15px 0;" class="bold">Memberi Tugas / Perintah:</div>

            <table class="table-main">
                <tr>
                    <td class="col-label">Kepada</td>
                    <td class="col-sep">:</td>
                    <td class="col-isi bold">
                        <?php echo e($p->assignee->nama_lengkap); ?><br>
                        <span style="font-weight: normal;">NIP. <?php echo e($p->assignee->nip ?? '-'); ?> / <?php echo e($p->assignee->role); ?></span>
                    </td>
                </tr>
                <tr>
                    <td class="col-label">Untuk</td>
                    <td class="col-sep">:</td>
                    <td class="col-isi">Melaksanakan tugas <strong><?php echo e($agenda->title); ?></strong> di Kabupaten Tuban pada tanggal <strong><?php echo e(\Carbon\Carbon::parse($agenda->event_date)->translatedFormat('d F Y')); ?></strong> s.d <strong><?php echo e(\Carbon\Carbon::parse($agenda->end_date)->translatedFormat('d F Y')); ?></strong>.</td>
                </tr>
            </table>

            <div class="footer">
                <div class="ttd-wrapper">
                    Tuban, <?php echo e(now()->translatedFormat('d F Y')); ?><br>
                    Kepala BPS Kabupaten Tuban,<br>
                    <div style="height: 85px;">
                        <?php if($agenda->status_approval === 'Approved' && $agenda->approver->signature): ?>
                            <img src="<?php echo e(public_path('storage/' . $agenda->approver->signature)); ?>" class="ttd-image">
                        <?php endif; ?>
                    </div>
                    <span class="bold" style="text-decoration: underline;"><?php echo e($agenda->approver->nama_lengkap); ?></span><br>
                    NIP. <?php echo e($agenda->approver->nip); ?>

                </div>
            </div>
            <?php if(!$loop->last): ?> <div class="page-break"></div> <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php else: ?>
        <div class="header">
            <img src="<?php echo e(public_path('img/logo-bps.png')); ?>" class="logo-bps">
            <div class="kop-text">
                <span class="kop-bps">BADAN PUSAT STATISTIK</span><br>
                <span class="kop-kab">KABUPATEN TUBAN</span>
            </div>
        </div>

        <div class="title-section">
            <span class="title-spt uppercase">SURAT PERINTAH TUGAS</span><br>
            <span class="bold uppercase">NOMOR : <?php echo e($agenda->nomor_surat_tugas); ?></span>
        </div>

        <table class="table-main">
            <tr>
                <td class="col-label">Menimbang</td>
                <td class="col-sep">:</td>
                <td class="col-isi">
                    <?php if($agenda->menimbang): ?>
                        <?php echo nl2br(e($agenda->menimbang)); ?>

                    <?php else: ?>
                        bahwa untuk kelancaran pelaksanaan kegiatan <?php echo e($agenda->title); ?>, maka dipandang perlu untuk menugaskan pegawai yang namanya tersebut di bawah ini;
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td class="col-label">Mengingat</td>
                <td class="col-sep">:</td>
                <td class="col-isi">
                    <?php if($agenda->mengingat): ?>
                        <?php echo nl2br(e($agenda->mengingat)); ?>

                    <?php else: ?>
                        <ol class="list-poin">
                            <li>Undang-Undang Nomor 16 Tahun 1997 tentang Statistik;</li>
                            <li>Peraturan Pemerintah Nomor 51 Tahun 1999 tentang Penyelenggaraan Statistik;</li>
                        </ol>
                    <?php endif; ?>
                </td>
            </tr>
        </table>

        <div style="text-align: center; margin: 15px 0;" class="bold">Memberi Tugas / Perintah:</div>

        <table class="table-main">
            <tr>
                <td class="col-label">Kepada</td>
                <td class="col-sep">:</td>
                <td class="col-isi bold">(Daftar Nama Terlampir)</td>
            </tr>
            <tr>
                <td class="col-label">Untuk</td>
                <td class="col-sep">:</td>
                <td class="col-isi">Melaksanakan tugas <strong><?php echo e($agenda->title); ?></strong> di Kabupaten Tuban pada tanggal <strong><?php echo e(\Carbon\Carbon::parse($agenda->event_date)->translatedFormat('d F Y')); ?></strong> s.d <strong><?php echo e(\Carbon\Carbon::parse($agenda->end_date)->translatedFormat('d F Y')); ?></strong>.</td>
            </tr>
        </table>

        <div class="footer">
            <div class="ttd-wrapper">
                Tuban, <?php echo e(now()->translatedFormat('d F Y')); ?><br>
                Kepala BPS Kabupaten Tuban,<br>
                <div style="height: 85px;">
                    <?php if($agenda->status_approval === 'Approved' && $agenda->approver->signature): ?>
                        <img src="<?php echo e(public_path('storage/' . $agenda->approver->signature)); ?>" class="ttd-image">
                    <?php endif; ?>
                </div>
                <span class="bold" style="text-decoration: underline;"><?php echo e($agenda->approver->nama_lengkap); ?></span><br>
                NIP. <?php echo e($agenda->approver->nip); ?>

            </div>
        </div>

        <div class="page-break"></div>
        <div style="margin-left: 55%; font-size: 9pt;">
            Lampiran Surat Tugas Kepala BPS Kabupaten Tuban<br>
            Nomor : <?php echo e($agenda->nomor_surat_tugas); ?><br>
            Tanggal : <?php echo e(now()->translatedFormat('d F Y')); ?>

        </div>

        <div style="text-align: center; margin: 20px 0;">
            <span class="bold uppercase">DAFTAR PEGAWAI YANG DITUGASKAN PADA:</span><br>
            <span class="bold uppercase"><?php echo e($agenda->title); ?></span>
        </div>

        <table border="1" width="100%" style="border-collapse: collapse; font-size: 10pt;">
            <tr style="background-color: #f2f2f2;">
                <th style="padding: 8px;" width="5%">No</th>
                <th style="padding: 8px;">Nama / NIP</th>
                <th style="padding: 8px;">Jabatan</th>
            </tr>
            <?php $__currentLoopData = $grupPetugas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td align="center" style="padding: 8px;"><?php echo e($index + 1); ?></td>
                <td style="padding: 8px;"><strong><?php echo e($item->assignee->nama_lengkap); ?></strong><br>NIP. <?php echo e($item->assignee->nip ?? '-'); ?></td>
                <td style="padding: 8px;"><?php echo e($item->assignee->role); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </table>
    <?php endif; ?>




<?php elseif($agenda->activity_type_id == 2 || $agenda->activity_type_id == 3): ?>
    <div class="header">
        <img src="<?php echo e(public_path('img/logo-bps.png')); ?>" class="logo-bps">
        <div class="kop-text">
            <span class="kop-bps">BADAN PUSAT STATISTIK</span><br>
            <span class="kop-kab">KABUPATEN TUBAN</span>
        </div>
    </div>

    <div class="title-section">
        <span class="title-spt" style="text-decoration: none;">MEMORANDUM</span><br>
        <span class="bold">NOMOR : <?php echo e($agenda->nomor_surat_tugas); ?></span>
    </div>

    <table class="table-main">
        <tr>
            <td class="col-label">Yth</td>
            <td class="col-sep">:</td>
            <td class="col-isi">
                <?php echo e($agenda->yth ?? 'Pegawai BPS Kabupaten Tuban'); ?></td>
        </tr>
        <tr>
            <td class="col-label">Hal</td>
            <td class="col-sep">:</td>
            <td class="col-isi bold"><?php echo e($agenda->title); ?></td>
        </tr>
    </table>

    <p style="margin-top: 15px;">Dengan hormat,</p>
    <p>Dalam rangka <?php echo e($agenda->title); ?> BPS Kabupaten Tuban, bersama ini mengharap kehadiran/partisipasi Saudara pada:</p>

    <table class="table-memo">
        <thead>
            <tr>
                <th width="30%">Hari/Tanggal</th>
                <th width="20%">Waktu</th>
                <th width="25%">Tempat</th>
                <th width="25%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <?php echo e(\Carbon\Carbon::parse($agenda->event_date)->translatedFormat('l')); ?><br>
                    <?php echo e(\Carbon\Carbon::parse($agenda->event_date)->translatedFormat('d-m-Y')); ?>

                    <?php if($agenda->end_date && $agenda->end_date != $agenda->event_date): ?>
                        s.d <?php echo e(\Carbon\Carbon::parse($agenda->end_date)->translatedFormat('d-m-Y')); ?>

                    <?php endif; ?>
                </td>
                <td><?php echo e($agenda->start_time ? \Carbon\Carbon::parse($agenda->start_time)->format('H.i') : '07.30'); ?> s.d Selesai</td>
                <td><?php echo e($agenda->location ?? 'BPS Kabupaten Tuban / Sesuai Undangan'); ?></td>
                <td><?php echo nl2br(e($agenda->content_surat)); ?></td>
            </tr>
        </tbody>
    </table>

    <p style="margin-top: 15px;">Demikian atas perhatiannya diucapkan terima kasih.</p>

    <div class="footer">
        <div class="ttd-wrapper">
            Tuban, <?php echo e(\Carbon\Carbon::parse($agenda->created_at)->translatedFormat('d F Y')); ?><br>
            Kepala Badan Pusat Statistik<br>
            Kabupaten Tuban,<br>
            <div style="height: 85px;">
                <?php if($agenda->status_approval === 'Approved' && $agenda->approver->signature): ?>
                    <img src="<?php echo e(public_path('storage/' . $agenda->approver->signature)); ?>" class="ttd-image">
                <?php endif; ?>
            </div>
            <span class="bold" style="text-decoration: underline;"><?php echo e($agenda->approver->nama_lengkap); ?></span><br>
            NIP. <?php echo e($agenda->approver->nip); ?>

        </div>
    </div>
<?php endif; ?>

</body>
</html><?php /**PATH /var/www/resources/views/assignment/pdf_spt.blade.php ENDPATH**/ ?>