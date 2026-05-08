<?php $__env->startSection('content'); ?>
<form action="<?php echo e(route('penugasan-mitra.store')); ?>" method="POST" enctype="multipart/form-data" id="formPenugasan">
    <?php echo csrf_field(); ?>
    <div class="row">
        
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-4 me-3 text-primary">
                        <i class="fas fa-calendar-check fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0">Detail Kegiatan & Honor</h5>
                        <p class="text-muted small mb-0">Tentukan rincian tugas dan nilai honor per satuan.</p>
                    </div>
                </div>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Nama Kegiatan <span class="text-danger">*</span></label>
                            <input type="text" name="nama_kegiatan_manual" id="nama_kegiatan_manual" class="form-control rounded-3 bg-light border-0 py-2 shadow-none" placeholder="Masukkan nama kegiatan secara manual..." required>
                            <input type="hidden" name="anggaran_id" value="">
                            <input type="hidden" name="kategori_kegiatan" value="Pendataan"> 
                        </div>
                    </div>


                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted text-uppercase">Tim Penanggung Jawab <span class="text-danger">*</span></label>
                        <select name="team_id" id="team_id" class="form-select rounded-3 bg-light border-0 py-2 shadow-none" required>
                            <option value="">-- Pilih Tim --</option>
                            <?php $__currentLoopData = $teams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($t->id); ?>"><?php echo e($t->nama_tim); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Jadwal Mulai <span class="text-danger">*</span></label>
                            <input type="date" name="tgl_mulai" class="form-control rounded-3 bg-light border-0 py-2 shadow-none" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Jadwal Selesai <span class="text-danger">*</span></label>
                            <input type="date" name="tgl_selesai_target" class="form-control rounded-3 bg-light border-0 py-2 shadow-none" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Nomor SPK (Opsional)</label>
                            <input type="text" name="no_spk" class="form-control rounded-3 bg-light border-0 py-2 shadow-none" placeholder="Contoh: 001/SPK/BPS/2026">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Nomor BAST (Opsional)</label>
                            <input type="text" name="no_bast" class="form-control rounded-3 bg-light border-0 py-2 shadow-none" placeholder="Contoh: 001/BAST/BPS/2026">
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Satuan <span class="text-danger">*</span></label>
                            <input type="text" name="satuan" class="form-control rounded-3 bg-light border-0 py-2 shadow-none" required placeholder="Contoh: BS, RT, Dokumen">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Honor Per Satuan <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0 text-muted small">Rp</span>
                                <input type="number" name="harga_satuan" id="harga_satuan" class="form-control rounded-end-3 bg-light border-0 py-2 shadow-none" required placeholder="0">
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            <input type="hidden" name="lokasi_tugas" value="Kabupaten Tuban">
                            <label class="form-label small fw-bold text-muted text-uppercase">File Pendukung (Opsional)</label>
                            <input type="file" name="file_pendukung" class="form-control rounded-3 bg-light border-0 py-2 shadow-none">
                        </div>
                    </div>

                    
                    <div id="volume-inputs-container">
                        
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="<?php echo e(route('penugasan-mitra.index')); ?>" class="btn btn-light rounded-pill px-4 fw-bold text-muted">Batal</a>
                        <button type="button" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm" id="btnSubmit">
                            <i class="fas fa-paper-plane me-2"></i>Konfirmasi & Ajukan
                        </button>
                    </div>
            </div>
            </div>
        </div>

    
        <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="form-section-title mb-0"><i class="fas fa-users me-2"></i>Pilih Mitra & Volume</div>
                </div>

                <div class="search-mitra-group mb-3 bg-light p-3 rounded-4 border border-opacity-10">
                    <div class="mb-2">
                        <label class="form-label x-small fw-bold text-muted text-uppercase mb-1">Cari Nama</label>
                        <input type="text" id="searchMitra" class="form-control form-control-sm rounded-pill px-3 border-0 shadow-sm" placeholder="Ketik nama mitra...">
                    </div>
                    <div>
                        <label class="form-label x-small fw-bold text-muted text-uppercase mb-1">Cari Domisili/Kecamatan</label>
                        <input type="text" id="filterKecamatan" class="form-control form-control-sm rounded-pill px-3 border-0 shadow-sm" placeholder="Ketik nama kecamatan...">
                    </div>
                </div>

                <style>
                    .search-mitra-group .x-small { font-size: 0.65rem; }
                    .search-mitra-group .form-control-sm, .search-mitra-group .form-select-sm { font-size: 0.75rem; }
                </style>

                <div class="mitra-selection-box border rounded-4 shadow-sm" style="max-height: 600px; overflow-y: scroll;">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light sticky-top" style="z-index: 10;">
                            <tr>
                                <th class="small fw-bold text-muted py-2 ps-3">Mitra</th>
                                <th class="small fw-bold text-muted text-center py-2" style="width: 80px;">Volume</th>
                                <th class="small fw-bold text-muted text-center py-2" style="width: 130px;">Estimasi Honor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $mitras; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="mitra-row cursor-pointer" 
                                data-name="<?php echo e(strtolower($m->nama_lengkap)); ?>"
                                data-kec="<?php echo e(strtolower($m->alamat_kec ?? 'none')); ?>"
                                data-current-honor="<?php echo e($m->total_honor_month); ?>"
                                data-max-honor="<?php echo e($m->max_honor_bulanan ?? 3200000); ?>">
                                <td class="ps-3">
                                    <div class="fw-bold text-dark small"><?php echo e($m->nama_lengkap); ?></div>
                                    <div class="d-flex align-items-center">
                                        <small class="text-muted font-monospace" style="font-size: 0.6rem;"><?php echo e($m->sobat_id); ?></small>
                                        <span class="badge <?php echo e($m->total_honor_month >= ($m->max_honor_bulanan ?? 3200000) ? 'bg-danger' : 'bg-primary bg-opacity-10 text-primary'); ?> rounded-pill ms-2" style="font-size: 0.5rem;">
                                            Rp <?php echo e(number_format($m->total_honor_month, 0, ',', '.')); ?>

                                        </span>
                                    </div>
                                </td>
                                <td class="pe-1">
                                    <input type="number" name="volumes[<?php echo e($m->sobat_id); ?>]" 
                                           class="form-control form-control-sm border-0 bg-light text-center volume-input" 
                                           placeholder="0" style="font-size: 0.75rem; border-radius: 8px;">
                                </td>
                                <td class="pe-3 text-end">
                                    <span class="small fw-bold text-primary subtotal-display" style="font-size: 0.75rem;">Rp 0</span>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </div>
</form>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/penugasan-mitra/create.blade.php ENDPATH**/ ?>